<?php

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participation>
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function findParticipationsForPassengerByStatuses(User $passenger, array $rideStatuses, array $participationStatuses):array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :passenger')
            ->andWhere('p.status IN (:participation_statuses)')
            ->join('p.ride', 'r')
            ->andWhere('r.status IN (:ride_statuses)')
            ->setParameter('passenger', $passenger)
            ->setParameter('ride_statuses', $rideStatuses)
            ->setParameter('participation_statuses', $participationStatuses)
            ->getQuery()
            ->getResult();
    }
}
