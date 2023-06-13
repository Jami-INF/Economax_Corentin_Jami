<?php

namespace App\Form;

use App\Entity\Alert;
use App\Enum\TemperatureEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyWord', null, [
                'label' => 'Mot clé',
            ])
            ->add('temperature', EnumType::class, [
                'class' => TemperatureEnum::class,
                'choice_label' => 'getLabel',
                'label' => 'Température',
            ])
            ->add('isNotify', null, [
                'label' => 'Recevoir une notification par email une fois par jour',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
