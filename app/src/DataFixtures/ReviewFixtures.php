<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Participation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $participations = $manager->getRepository(Participation::class)->findAll();

        foreach ($participations as $participation) {
            $ride = $participation->getRide();

            if ($ride->getStatus() !== 'completed' || $participation->getStatus() !== 'confirmed') {
                continue;
            }

            $review = new Review();
            $review->setAuthor($participation->getUser());
            $review->setTarget($ride->getDriver());
            $review->setRide($ride);
            $review->setRating($faker->numberBetween(1, 5));
            $review->setComment($faker->sentence(10));
            $review->setValidated($faker->boolean(90));
            $review->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($review);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            \App\DataFixtures\ParticipationFixtures::class,
        ];
    }
}