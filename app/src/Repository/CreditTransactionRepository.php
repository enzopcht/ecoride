<?php

namespace App\Repository;

use App\Entity\CreditTransaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreditTransaction>
 */
class CreditTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditTransaction::class);
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
}
