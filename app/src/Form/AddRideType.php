<?php

namespace App\Form;

use App\Entity\Ride;
use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;

class AddRideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureAddress', TextType::class, [
                'label' => 'Adresse de départ',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('arrivalAddress', TextType::class, [
                'label' => 'Adresse d\'arrivée',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])

            ->add('departurePostCode', HiddenType::class, [
                'label' => 'Code postal de départ',
            ])
            ->add('departureCity', HiddenType::class, [
                'label' => 'Ville de départ',
            ])

            ->add('arrivalPostCode', HiddenType::class, [
                'label' => 'Code postal d\'arrivé',
            ])
            ->add('arrivalCity', HiddenType::class, [
                'label' => 'Ville d\'arrivée',
            ])

            ->add('departureTime', DateTimeType::class, [
                'label' => 'Date et heure de départ',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                    'class' => 'form-control'
                ],
                'html5' => true,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => new \DateTimeImmutable(),
                        'message' => 'La date et l\'heure de départ doivent être dans le futur.',
                    ])
                ]
            ])
            ->add('arrivalTime', DateTimeType::class, [
                'label' => 'Date et heure d\'arrivée',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'class' => 'form-control'
                ],
                'html5' => true,
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 2,
                        'message' => 'Le prix doit être au minimum de {{ compared_value }} jetons, la commission d\'EcoRide.'
                    ])
                ],
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('seatsAvailable', IntegerType::class, [
                'label' => 'Places disponibles',
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 8,
                        'notInRangeMessage' => 'Le nombre de places doit être compris entre {{ min }} et {{ max }}.',
                    ])
                ]
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'label' => 'Sélectionnez votre voiture pour ce trajet',
                'choice_label' => function ($vehicle) {
                    return sprintf('%s - %s (%s) | %s', $vehicle->getCarModel()->getBrand()->getLabel(), $vehicle->getCarModel()->getLabel(), $vehicle->getPlate(), $vehicle->getCarModel()->getEnergy() == 'thermal' ? 'Thermique' : 'Electrique');
                },
                'query_builder' => function (VehicleRepository $vr) use ($options) {
                    return $vr->createQueryBuilder('v')
                        ->where('v.owner = :user')
                        ->setParameter('user', $options['user']);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ride::class,
            'user' => null,
        ]);
    }
}
