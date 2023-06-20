<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\Group;
use App\Entity\Marchand;
use App\Entity\PromoCode;
use App\Entity\User;
use App\Enum\TypeReducEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setEmail($faker->email());
        $user->setPassword('$2y$13$QlLPUgg3Fup.ZS99MZO.LOuwojCSzfKkBIl52iCGJXs6q0BPlPfPe');//user
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setDescription($faker->text());
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail($faker->email());
        $admin->setPassword('$2y$13$9u6uguMYgKYerqzu1TKR/.KDjd5bIYkPRD6cISIZ.sui5SkNM/FYG');//admin
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);


        $categoriesFake = ['Électronique', 'Mode', 'Alimentation', 'Maison', 'Sport', 'Jouets', 'Jeux vidéo', 'Bricolage', 'Beauté', 'Auto', 'Livres', 'Musique', 'Bébé', 'Animalerie', 'Bureau', 'Informatique', 'Jardin', 'Bijoux', 'Chaussures', 'Montres', 'Bagages', 'Santé'];

        $groups = Array();
        for ($i = 0; $i < count($categoriesFake); $i++) {
            $groups[$i] = new Group();
            $groups[$i]->setName($categoriesFake[$i]);
            $manager->persist($groups[$i]);
        }

        $marchand = Array();
        for ($i = 0; $i < 12; $i++) {
            $marchand[$i] = new Marchand();
            $marchand[$i]->setName($faker->company());
            $marchand[$i]->setDescription($faker->text());
            $manager->persist($marchand[$i]);
        }


        $advert = Array();
        for ($i = 0; $i < 10; $i++) {
            $advert[$i] = new Advert();
            $advert[$i]->setPrice($faker->randomFloat(1, 10, 200));
            $advert[$i]->setUsualPrice($faker->randomFloat(1, 200, 1000));
            $advert[$i]->setShipping($faker->randomFloat(1, 0, 60));
            $advert[$i]->setGroups($groups[rand(0, count($categoriesFake)-1)]);
            $advert[$i]->setMarchand($marchand[rand(0, count($marchand)-1)]);
            $advert[$i]->setUser($user);
            $this->extracted($advert[$i], $faker, $manager);

        }

        $promoCode = Array();
        for ($i = 0; $i < 10; $i++) {
            $promoCode[$i] = new PromoCode();
            $promoCode[$i]->setTypeReduc(TypeReducEnum::AMOUNT);
            $promoCode[$i]->setGroups($groups[rand(0, count($categoriesFake)-1)]);
            $promoCode[$i]->setMarchand($marchand[rand(0, count($marchand)-1)]);
            $promoCode[$i]->setUser($user);
            $promoCode[$i]->setValue($faker->randomFloat(2, 0, 200));
            $this->extracted($promoCode[$i], $faker, $manager);
        }



        $manager->flush();
    }

    /**
     * @param $advert
     * @param \Faker\Generator $faker
     * @param ObjectManager $manager
     * @return void
     */
    public function extracted($advert, \Faker\Generator $faker, ObjectManager $manager): void
    {
        $advert->setTitle($faker->sentence(10, true));
        $advert->setDescription($faker->sentence(30, true));
        $advert->setLink($faker->url());
        $advert->setPromoCode($faker->word());

        $manager->persist($advert);
    }
}
