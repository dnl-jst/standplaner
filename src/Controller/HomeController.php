<?php

namespace App\Controller;

use App\Repository\CampaignStandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CampaignStandRepository $campaignStandRepository): Response
    {
        // Alle Stände nach Startzeit sortiert abrufen
        $stands = $campaignStandRepository->findBy([], ['startTime' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'stands' => $stands,
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(): Response
    {
        return $this->render('home/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
