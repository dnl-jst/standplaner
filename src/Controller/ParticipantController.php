<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\CampaignStandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParticipantController extends AbstractController
{
    #[Route('/teilnehmer', name: 'app_participants')]
    public function index(
        ParticipantRepository $participantRepository,
        CampaignStandRepository $campaignStandRepository
    ): Response {
        // Alle Teilnehmer mit ihren Teilnahmen abrufen (mit JOIN für bessere Performance)
        $participants = $participantRepository->createQueryBuilder('p')
            ->leftJoin('p.participations', 'part')
            ->leftJoin('part.campaignStand', 'cs')
            ->addSelect('part', 'cs')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();

        // Alle Stände nach Startzeit sortiert für die Tabellenköpfe
        $stands = $campaignStandRepository->findBy([], ['startTime' => 'ASC']);

        // Statistiken berechnen
        $totalParticipants = count($participants);
        $totalParticipations = 0;
        $attendingCount = 0;
        $maybeCount = 0;
        $notAttendingCount = 0;

        foreach ($participants as $participant) {
            foreach ($participant->getParticipations() as $participation) {
                $totalParticipations++;
                switch ($participation->getStatus()) {
                    case 'attending':
                        $attendingCount++;
                        break;
                    case 'maybe':
                        $maybeCount++;
                        break;
                    case 'not_attending':
                        $notAttendingCount++;
                        break;
                }
            }
        }

        return $this->render('participant/index.html.twig', [
            'participants' => $participants,
            'stands' => $stands,
            'stats' => [
                'totalParticipants' => $totalParticipants,
                'totalParticipations' => $totalParticipations,
                'attendingCount' => $attendingCount,
                'maybeCount' => $maybeCount,
                'notAttendingCount' => $notAttendingCount,
            ]
        ]);
    }
}
