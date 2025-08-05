<?php

namespace App\Controller;

use App\Repository\CampaignStandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(CampaignStandRepository $campaignStandRepository): Response
    {
        // Alle Stände abrufen
        $stands = $campaignStandRepository->findBy([], ['startTime' => 'ASC']);

        // Statistiken für die Stände berechnen
        $occupiedStands = 0;      // Stände mit mindestens 3 Teilnehmer*innen
        $understaffedStands = 0;  // Stände mit unter 3 Teilnehmer*innen

        foreach ($stands as $stand) {
            $attendingCount = 0;
            foreach ($stand->getParticipations() as $participation) {
                if ($participation->getStatus() === 'attending') {
                    $attendingCount++;
                }
            }

            if ($attendingCount >= 3) {
                $occupiedStands++;
            } else {
                $understaffedStands++;
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
            'stats' => [
                'totalStands' => count($stands),
                'occupiedStands' => $occupiedStands,
                'understaffedStands' => $understaffedStands,
            ],
        ]);
    }
}
