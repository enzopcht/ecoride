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
        $start = (clone $date)->setTime(0, 0);
        $end = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->andWhere('r.departure_city = :departure')
            ->andWhere('r.arrival_city = :arrival')
            ->andWhere('r.departure_time BETWEEN :start AND :end')
            ->setParameter('departure', $departure)
            ->setParameter('arrival', $arrival)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
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
            ->setParameter('departure', $departure)
            ->setParameter('arrival', $arrival)
            ->setParameter('date', $date)
            ->orderBy('r.departure_time', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
