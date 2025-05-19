<?php

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\Ride;
use App\Entity\User;
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

    public function findRidesBySearchData(string $departure, string $arrival, \DateTime $date): array
    {
        $now = new \DateTime();
        $today = (clone $now)->setTime(0, 0);

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

    public function countRidesGroupedByDay(int $range = 7): array
    {
        $since = (new \DateTimeImmutable())->modify("-$range days");

        $rides = $this->createQueryBuilder('r')
            ->select('r.departure_time')
            ->where('r.departure_time >= :since')
            ->setParameter('since', $since)
            ->getQuery()
            ->getResult();

        $grouped = [];
        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'd MMM');
        foreach ($rides as $ride) {
            $date = $ride['departure_time'];
            $day = $formatter->format($date);
            if (!isset($grouped[$day])) {
                $grouped[$day] = 0;
            }
            $grouped[$day]++;
        }

        $result = [];
        $today = new \DateTimeImmutable();
        for ($i = $range - 1; $i >= 0; $i--) {
            $day = $formatter->format($today->modify("-$i days"));
            $result[] = [
                'date' => $day,
                'count' => $grouped[$day] ?? 0,
            ];
        }

        return $result;
    }
}
