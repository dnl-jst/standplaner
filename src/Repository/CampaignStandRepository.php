<?php

namespace App\Repository;

use App\Entity\CampaignStand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampaignStand>
 */
class CampaignStandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampaignStand::class);
    }

    /**
     * Find all campaign stands ordered by start time
     */
    public function findAllOrderedByStartTime(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming campaign stands
     */
    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.startTime > :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('c.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find campaign stands by district
     */
    public function findByDistrict(string $district): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.district = :district')
            ->setParameter('district', $district)
            ->orderBy('c.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
