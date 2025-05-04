<?php

namespace App\Repository;

use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ride>
 */
class RideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ride::class);
    }

    //    /**
    //     * @return Ride[] Returns an array of Ride objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ride
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findRidesBySearchData(string $departure, string $arrival, \DateTime $date): array
    {
        $now = new \DateTime();
        $today = (clone $now)->setTime(0, 0);

        // Si la date demandée est dans le passé, retourne une liste vide
        if ($date < $today) {
            return [];
        }

        $start = (clone $date)->setTime(0, 0);
        $end = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->andWhere('r.departure_city = :departure')
            ->andWhere('r.arrival_city = :arrival')
            ->andWhere('r.departure_time BETWEEN :start AND :end')
            ->andWhere('r.seats_available > 0')
            ->andWhere('r.status = :status')
            ->setParameter('departure', $departure)
            ->setParameter('arrival', $arrival)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status', 'pending')
            ->orderBy('r.departure_time', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNextRideAfterDate(string $departure, string $arrival, \DateTime $date): ?Ride
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.departure_city = :departure')
            ->andWhere('r.arrival_city = :arrival')
            ->andWhere('r.departure_time > :date')
            ->andWhere('r.seats_available > 0')
            ->andWhere('r.status = :status')
            ->setParameter('departure', $departure)
            ->setParameter('arrival', $arrival)
            ->setParameter('date', $date)
            ->setParameter('status', 'pending')
            ->orderBy('r.departure_time', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
