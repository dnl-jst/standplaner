<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\StandParticipation;
use App\Repository\CampaignStandRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParticipationController extends AbstractController
{
    #[Route('/anmeldung', name: 'app_participation')]
    public function index(CampaignStandRepository $campaignStandRepository): Response
    {
        // Alle Stände nach Startzeit sortiert abrufen
        $stands = $campaignStandRepository->findBy([], ['startTime' => 'ASC']);

        return $this->render('participation/index.html.twig', [
            'stands' => $stands,
        ]);
    }

    #[Route('/anmeldung/speichern', name: 'app_participation_save', methods: ['POST'])]
    public function save(
        Request $request,
        CampaignStandRepository $campaignStandRepository,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $name = trim($request->request->get('participant_name', ''));
        $participations = $request->request->all('participations');

        // Validierung
        if (empty($name)) {
            $this->addFlash('error', 'Bitte gib deinen Namen ein.');
            return $this->redirectToRoute('app_participation');
        }

        // Prüfen ob Teilnehmer bereits existiert, sonst erstellen
        $participant = $participantRepository->findOneBy(['name' => $name]);
        if (!$participant) {
            $participant = new Participant();
            $participant->setName($name);
            $entityManager->persist($participant);
        }

        // Bestehende Teilnahmen des Teilnehmers löschen
        $existingParticipations = $entityManager->getRepository(StandParticipation::class)
            ->findBy(['participant' => $participant]);
        
        foreach ($existingParticipations as $existing) {
            $entityManager->remove($existing);
        }

        // Neue Teilnahmen erstellen
        $savedCount = 0;
        foreach ($participations as $standId => $status) {
            if (in_array($status, ['attending', 'maybe', 'not_attending'])) {
                $stand = $campaignStandRepository->find($standId);
                if ($stand) {
                    $participation = new StandParticipation();
                    $participation->setParticipant($participant);
                    $participation->setStatus($status);
                    $stand->addParticipation($participation);
                    $entityManager->persist($participation);
                    $savedCount++;
                }
            }
        }

        $entityManager->flush();

        if ($savedCount > 0) {
            $this->addFlash('success', sprintf(
                'Danke %s! Deine Anmeldung für %d Stände wurde gespeichert.',
                $name,
                $savedCount
            ));
        } else {
            $this->addFlash('info', 'Keine Teilnahmen ausgewählt.');
        }

        return $this->redirectToRoute('app_home');
    }
}
