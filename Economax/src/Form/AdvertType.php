<?php

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Group;
use App\Entity\Marchand;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('link', null, [
                'label' => 'Lien de l\'annonce',
            ])
            ->add('promoCode', null, [
                'label' => 'Code promo',
            ])
            ->add('title', null, [
                'label' => 'Titre de l\'annonce',
            ])
            ->add('description')
            ->add('price', NumberType::class, [
                'label' => 'Prix de l\'annonce',
                'html5' => true,
            ])
            ->add('usualPrice', null, [
                'label' => 'Prix habituel',
                'html5' => true,
            ])
            ->add('shipping', null, [
                'label' => 'Frais de port',
                'html5' => true,
            ])
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
                'label' => 'Groupe de l\'annonce',
                'attr' => [
                    'class' => 'form-control d-flex flex-wrap justify-content-between',
                ],
            ])
            ->add('marchand', EntityType::class, [
                'class' => Marchand::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
                'label' => 'Marchand',
                'required' => false,
                'attr' => [
                    'class' => 'form-control d-flex flex-wrap justify-content-between',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Image',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
