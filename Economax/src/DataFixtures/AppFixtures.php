<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\Group;
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
        $user->setPassword($faker->password());
        $user->setUsername($faker->userName());
        $manager->persist($user);


        $groups = Array();
        for ($i = 0; $i < 10; $i++) {
            $groups[$i] = new Group();
            $groups[$i]->setName($faker->word());
            $manager->persist($groups[$i]);
        }

        $advert = Array();
        for ($i = 0; $i < 10; $i++) {
            $advert[$i] = new Advert();
            $advert[$i]->setPrice($faker->randomFloat(2, 0, 100));
            $advert[$i]->setUsualPrice($faker->randomFloat(2, 0, 100));
            $advert[$i]->setShipping($faker->randomFloat(2, 0, 100));
            $advert[$i]->setGroups($groups[rand(0, 9)]);
            $advert[$i]->setUser($user);
            $this->extracted($advert[$i], $faker, $manager);

        }

        $promoCode = Array();
        for ($i = 0; $i < 10; $i++) {
            $promoCode[$i] = new PromoCode();
            $promoCode[$i]->setTypeReduc(TypeReducEnum::AMOUNT);
            $promoCode[$i]->setGroups($groups[rand(0, 9)]);
            $promoCode[$i]->setUser($user);
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
        $advert->setTitle($faker->sentence(3, true));
        $advert->setDescription($faker->sentence(10, true));
        $advert->setTemperature($faker->randomFloat(2, 0, 100));
        $advert->setLink($faker->url());
        $advert->setPromoCode($faker->word());
        $advert->setIsExpired(false);

        $manager->persist($advert);
    }
}
