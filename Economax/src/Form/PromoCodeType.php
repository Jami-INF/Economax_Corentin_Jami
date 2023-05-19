<?php

namespace App\Form;

use App\Entity\PromoCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('temperature')
            ->add('link')
            ->add('promoCode')
            ->add('title')
            ->add('description')
            ->add('isExpired')
            ->add('typeReduc')
            ->add('groups', null, [
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
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
