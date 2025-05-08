<?php

namespace App\Form;

use App\Form\Model\BecomeDriverData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BecomeDriverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicle', VehicleType::class, [
                'label' => false,
            ])
            ->add('musicAllowed', CheckboxType::class, [
                'label' => 'Musique acceptée à bord',
                'required' => false,
            ])
            ->add('smokingAllowed', CheckboxType::class, [
                'label' => 'On peut fumer à bord',
                'required' => false,
            ])
            ->add('animalsAllowed', CheckboxType::class, [
                'label' => 'Animaux acceptés à bord',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BecomeDriverData::class,
        ]);
    }
}
