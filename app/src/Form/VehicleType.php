<?php

namespace App\Form;

use App\Entity\CarBrand;
use App\Entity\CarModel;
use App\Form\Model\VehicleData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleType extends AbstractType
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
                'attr' => [
                    'class' => 'form-control',
                    'max' => (new \DateTime())->format('Y-m-d')
                ],
            ])
            ->add('brand', EntityType::class, [
                'class' => CarBrand::class,
                'choice_label' => 'label',
                'label' => 'Marque',
                'placeholder' => 'Sélectionnez une marque',
                'attr' => ['class' => 'form-control', 'id' => 'brand-select'],
            ])
            ->add('model', EntityType::class, [
                'class' => CarModel::class,
                'choice_label' => 'label',
                'label' => 'Modèle',
                'placeholder' => 'Sélectionnez un modèle',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'Couleur',
                'choices' => [
                    'Blanc' => 'Blanc',
                    'Noir' => 'Noir',
                    'Gris' => 'Gris',
                    'Bleu' => 'Bleu',
                    'Rouge' => 'Rouge',
                    'Vert' => 'Vert',
                    'Jaune' => 'Jaune',
                    'Orange' => 'Orange',
                    'Marron' => 'Marron',
                    'Violet' => 'Violet',
                    'Rose' => 'Rose',
                    'Beige' => 'Beige',
                    'Autre' => 'Autre',
                ],
                'placeholder' => 'Sélectionnez une couleur',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VehicleData::class,
        ]);
    }
}
