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
        // Nur aktive Stände anzeigen (zukünftige + laufende)
        $stands = $campaignStandRepository->findActive();
        return $this->render('home/index.html.twig', [
            'stands' => $stands,
        ]);
    }
}
