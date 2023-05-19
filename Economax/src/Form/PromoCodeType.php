<?php

namespace App\Form;

use App\Entity\PromoCode;
use App\Enum\TypeReducEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('link')
            ->add('promoCode')
            ->add('title')
            ->add('description')
            ->add('isExpired')
            ->add('typeReduc', EnumType::class, [
                'class' => TypeReducEnum::class,
                'choice_label' => 'getLabel',
            ])
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
