<?php

namespace App\Form;

use App\Form\Model\BecomeDriverData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BecomeDriverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plate', TextType::class, [
                'label' => 'Plaque d\'immatriculation',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('firstRegistrationDate', DateType::class, [
                'label' => 'Date de première immatriculation',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('brand', ChoiceType::class, [
                'label' => 'Marque',
                'placeholder' => 'Sélectionnez une marque',
                'choices' => [], 
                'attr' => ['class' => 'form-control', 'id' => 'brand-select'],
            ])
            ->add('model', ChoiceType::class, [
                'label' => 'Modèle',
                'placeholder' => 'Sélectionnez un modèle',
                'choices' => [],
                'attr' => ['class' => 'form-control', 'id' => 'model-select'],
            ])
            ->add('color',  TextType::class, [
                'label' => 'Couleur',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('seats', IntegerType::class, [
                'label' => 'Nombre de places disponibles (Renseignez le nombre de places possiblement réservable dans le véhicule)',
                'attr' => ['class' => 'form-control', 'min' => 1, 'max' => 4],
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
