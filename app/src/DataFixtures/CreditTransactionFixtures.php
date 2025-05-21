<?php

namespace App\DataFixtures;

use App\Entity\CreditTransaction;
use App\Entity\Participation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CreditTransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $participations = $manager->getRepository(Participation::class)->findAll();

        foreach ($participations as $participation) {
            $ride = $participation->getRide();
            $user = $participation->getUser();
            $driver = $ride->getDriver();
            $price = $participation->getCreditsUsed();

            // Si la participation est confirmée et le trajet actif ou terminé → débit et commission
            
                $debit = new CreditTransaction();
                $debit->setUser($user);
                $debit->setAmount(-($price - 2));
                $debit->setReason('Booking a trip');
                $debit->setRide($ride);
                $debit->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($debit);

                $credit = new CreditTransaction();
                $credit->setUser($driver);
                $credit->setAmount($price - 2);
                $credit->setReason('Driver payment');
                $credit->setRide($ride);
                $credit->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($credit);

                $commission = new CreditTransaction();
                $commission->setUser($user);
                $commission->setAmount(-2);
                $commission->setReason('Commission');
                $commission->setRide($ride);
                $commission->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($commission);
            
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
