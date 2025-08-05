<?php

namespace App\Controller;

use App\Repository\CampaignStandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/finished-stands', name: 'app_finished_stands_')]
final class FinishedStandsController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(CampaignStandRepository $campaignStandRepository): Response
    {
        $finishedStands = $campaignStandRepository->findFinished();

        return $this->render('finished_stands/index.html.twig', [
            'finished_stands' => $finishedStands,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, CampaignStandRepository $campaignStandRepository): Response
    {
        $stand = $campaignStandRepository->find($id);

        if (!$stand) {
            throw $this->createNotFoundException('Stand nicht gefunden.');
        }

        if (!$stand->isFinished()) {
            $this->addFlash('warning', 'Dieser Stand ist noch nicht beendet.');
            return $this->redirectToRoute('app_campaign_stand_show', ['id' => $id]);
        }

        return $this->render('finished_stands/show.html.twig', [
            'stand' => $stand,
        ]);
    }
}
