<?php

namespace App\Form;

use App\Entity\Group;
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
            ->add('link')
            ->add('promoCode')
            ->add('title')
            ->add('description')
            ->add('typeReduc', EnumType::class, [
                'class' => TypeReducEnum::class,
                'choice_label' => 'getLabel',
            ])
            ->add('value', null,
                [
                    'attr' => [
                        'placeholder' => 'Valeur de la réduction',
                    ],
                    'label' => 'Valeur de la réduction',
                ]
            )
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
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
