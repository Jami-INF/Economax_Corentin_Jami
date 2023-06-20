<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Marchand;
use App\Entity\PromoCode;
use App\Enum\TypeReducEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PromoCodeType extends AbstractType
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
            ->add('description', null, [
                'label' => 'Description de l\'annonce',
            ])
            ->add('typeReduc', EnumType::class, [
                'class' => TypeReducEnum::class,
                'choice_label' => 'getLabel',
                'label' => 'Type de réduction',
            ])
            ->add('value', null, [
                    'label' => 'Valeur de la réduction',
                ]
            )
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
                'required' => true,
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
            'data_class' => PromoCode::class,
        ]);
    }
}
