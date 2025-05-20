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
            $status = $participation->getStatus();
            $rideStatus = $ride->getStatus();
            $price = $participation->getCreditsUsed();

            // Si la participation est confirmée et le trajet actif ou terminé → débit et commission
            if ($status === 'confirmed' && in_array($rideStatus, ['active', 'completed'])) {
                $debit = new CreditTransaction();
                $debit->setUser($user);
                $debit->setAmount(-($price - 2));
                $debit->setReason('Booking a trip');
                $debit->setRide($ride);
                $debit->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($debit);

                $commission = new CreditTransaction();
                $commission->setUser($user);
                $commission->setAmount(-2);
                $commission->setReason('Commission');
                $commission->setRide($ride);
                $commission->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($commission);
            }

            // Si participation confirmée mais trajet annulé → remboursement
            if ($status === 'confirmed' && $rideStatus === 'canceled') {
                $refund = new CreditTransaction();
                $refund->setUser($user);
                $refund->setAmount(($price - 2));
                $refund->setReason('Refund');
                $refund->setRide($ride);
                $refund->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($refund);

                $refundCommission = new CreditTransaction();
                $refundCommission->setUser($user);
                $refundCommission->setAmount(2);
                $refundCommission->setReason('Refund Commission');
                $refundCommission->setRide($ride);
                $refundCommission->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($refundCommission);
            }
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
