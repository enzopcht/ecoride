<?php

namespace App\Repository;

use App\Entity\CreditTransaction;
use App\Entity\User;
use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<CreditTransaction>
 */
class CreditTransactionRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, CreditTransaction::class);
        $this->em = $em;
    }

    public function calculateUserBalance(User $user): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.amount) as balance')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $qb;
    }

    public function calculateEcoRideRevenue(): float
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->select('SUM(t.amount) as total')
            ->where('t.reason = :commission OR t.reason = :commissionRefund')
            ->setParameter('commission', 'Commission')
            ->setParameter('commissionRefund', 'Refund Commission')
            ->getQuery()
            ->getSingleScalarResult();
    }



    /**
     * Create and persist a credit transaction for a user and ride.
     */
    public function createTransaction(User $user, Ride $ride, int $amount, string $reason): CreditTransaction
    {
        $transaction = new CreditTransaction();
        $transaction->setUser($user);
        $transaction->setRide($ride);
        $transaction->setAmount($amount);
        $transaction->setReason($reason);
        $transaction->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($transaction);

        return $transaction;
    }

    public function countCreditsGroupedByDay(int $range = 7): array
    {
        $since = (new \DateTimeImmutable())->modify("-$range days");

        $credits = $this->createQueryBuilder('t')
            ->select('t.created_at, t.amount, t.reason')
            ->where('t.created_at >= :since')
            ->andWhere('t.reason = :commission OR t.reason = :commissionRefund')
            ->setParameter('since', $since)
            ->setParameter('commission', 'Commission')
            ->setParameter('commissionRefund', 'Refund Commission')
            ->getQuery()
            ->getResult();

        $grouped = [];
        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'd MMM');
        foreach ($credits as $credit) {
            $date = $credit['created_at'];
            $day = $formatter->format($date);
            if (!isset($grouped[$day])) {
                $grouped[$day] = 0;
            }
            $grouped[$day] += $credit['amount'];
        }

        $result = [];
        $today = new \DateTimeImmutable();
        for ($i = $range - 1; $i >= 0; $i--) {
            $day = $formatter->format($today->modify("-$i days"));
            $result[] = [
                'date' => $day,
                'amount' => $grouped[$day] ?? 0,
            ];
        }

        return $result;
    }
}
