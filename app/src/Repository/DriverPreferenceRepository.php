<?php 

namespace App\Repository;

use App\Document\DriverPreference;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class DriverPreferenceRepository extends DocumentRepository
{
    public function findOneByUserId(int $userId): ?DriverPreference
    {
        return $this->createQueryBuilder()
            ->field('userId')->equals($userId)
            ->getQuery()
            ->getSingleResult();
    }
}