<?php

namespace App\Controller;

use App\Entity\CampaignStand;
use App\Form\CampaignStandType;
use App\Repository\CampaignStandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/staende')]
#[IsGranted('ROLE_USER')]
final class CampaignStandController extends AbstractController
{
    #[Route('/', name: 'app_campaign_stand_index', methods: ['GET'])]
    public function index(CampaignStandRepository $campaignStandRepository): Response
    {
        $stands = $campaignStandRepository->findBy([], ['startTime' => 'ASC']);

        // Statistiken für jeden Stand berechnen
        $standsWithStats = [];
        foreach ($stands as $stand) {
            $attendingCount = 0;
            $maybeCount = 0;
            $notAttendingCount = 0;

            foreach ($stand->getParticipations() as $participation) {
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

            $standsWithStats[] = [
                'stand' => $stand,
                'stats' => [
                    'attending' => $attendingCount,
                    'maybe' => $maybeCount,
                    'notAttending' => $notAttendingCount,
                    'total' => $attendingCount + $maybeCount + $notAttendingCount,
                    'isOccupied' => $attendingCount >= 3,
                ]
            ];
        }

        return $this->render('campaign_stand/index.html.twig', [
            'stands_with_stats' => $standsWithStats,
        ]);
    }

    #[Route('/neu', name: 'app_campaign_stand_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaignStand = new CampaignStand();
        $form = $this->createForm(CampaignStandType::class, $campaignStand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campaignStand);
            $entityManager->flush();

            $this->addFlash('success', 'Der Wahlkampfstand wurde erfolgreich erstellt.');

            return $this->redirectToRoute('app_campaign_stand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campaign_stand/new.html.twig', [
            'campaign_stand' => $campaignStand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campaign_stand_show', methods: ['GET'])]
    public function show(CampaignStand $campaignStand): Response
    {
        // Statistiken berechnen
        $attendingCount = 0;
        $maybeCount = 0;
        $notAttendingCount = 0;
        $participants = [];

        foreach ($campaignStand->getParticipations() as $participation) {
            $participants[] = [
                'participant' => $participation->getParticipant(),
                'status' => $participation->getStatus(),
                'registeredAt' => $participation->getCreatedAt(),
            ];

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

        return $this->render('campaign_stand/show.html.twig', [
            'campaign_stand' => $campaignStand,
            'participants' => $participants,
            'stats' => [
                'attending' => $attendingCount,
                'maybe' => $maybeCount,
                'notAttending' => $notAttendingCount,
                'total' => $attendingCount + $maybeCount + $notAttendingCount,
                'isOccupied' => $attendingCount >= 3,
            ]
        ]);
    }

    #[Route('/{id}/bearbeiten', name: 'app_campaign_stand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CampaignStand $campaignStand, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampaignStandType::class, $campaignStand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Der Wahlkampfstand wurde erfolgreich aktualisiert.');

            return $this->redirectToRoute('app_campaign_stand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campaign_stand/edit.html.twig', [
            'campaign_stand' => $campaignStand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campaign_stand_delete', methods: ['POST'])]
    public function delete(Request $request, CampaignStand $campaignStand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campaignStand->getId(), $request->request->get('_token'))) {
            // Prüfen, ob bereits Anmeldungen vorhanden sind
            if ($campaignStand->getParticipations()->count() > 0) {
                $this->addFlash('error', 'Der Stand kann nicht gelöscht werden, da bereits Anmeldungen vorhanden sind.');
            } elseif (!$campaignStand->isFuture()) {
                $this->addFlash('error', 'Der Stand kann nicht gelöscht werden, da er bereits begonnen hat oder beendet ist.');
            } else {
                $entityManager->remove($campaignStand);
                $entityManager->flush();
                $this->addFlash('success', 'Der Wahlkampfstand wurde erfolgreich gelöscht.');
            }
        }

        return $this->redirectToRoute('app_campaign_stand_index', [], Response::HTTP_SEE_OTHER);
    }
}
