<?php

namespace App\Repository;

use App\Entity\StandParticipation;
use App\Entity\CampaignStand;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StandParticipation>
 */
class StandParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StandParticipation::class);
    }

    /**
     * Find participation by campaign stand and participant
     */
    public function findByStandAndParticipant(CampaignStand $stand, Participant $participant): ?StandParticipation
    {
        return $this->createQueryBuilder('sp')
            ->where('sp.campaignStand = :stand')
            ->andWhere('sp.participant = :participant')
            ->setParameter('stand', $stand)
            ->setParameter('participant', $participant)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all participations for a campaign stand
     */
    public function findByCampaignStand(CampaignStand $stand): array
    {
        return $this->createQueryBuilder('sp')
            ->leftJoin('sp.participant', 'p')
            ->where('sp.campaignStand = :stand')
            ->setParameter('stand', $stand)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all participations for a participant
     */
    public function findByParticipant(Participant $participant): array
    {
        return $this->createQueryBuilder('sp')
            ->leftJoin('sp.campaignStand', 'cs')
            ->where('sp.participant = :participant')
            ->setParameter('participant', $participant)
            ->orderBy('cs.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count participations by status for a campaign stand
     */
    public function countByStatusForStand(CampaignStand $stand, string $status): int
    {
        return $this->createQueryBuilder('sp')
            ->select('COUNT(sp.id)')
            ->where('sp.campaignStand = :stand')
            ->andWhere('sp.status = :status')
            ->setParameter('stand', $stand)
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get participation statistics for a campaign stand
     */
    public function getParticipationStats(CampaignStand $stand): array
    {
        return [
            'attending' => $this->countByStatusForStand($stand, StandParticipation::STATUS_ATTENDING),
            'maybe' => $this->countByStatusForStand($stand, StandParticipation::STATUS_MAYBE),
            'not_attending' => $this->countByStatusForStand($stand, StandParticipation::STATUS_NOT_ATTENDING),
        ];
    }

    /**
     * Find participations by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('sp')
            ->leftJoin('sp.campaignStand', 'cs')
            ->leftJoin('sp.participant', 'p')
            ->where('sp.status = :status')
            ->setParameter('status', $status)
            ->orderBy('cs.startTime', 'ASC')
            ->addOrderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
