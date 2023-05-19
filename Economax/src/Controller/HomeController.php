<?php

namespace App\Controller;

use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(protected DealRepository $dealRepository)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $deals = $this->dealRepository->findAll();

        return $this->render('home/index.html.twig', [
            'deals' => $deals,
        ]);
    }
}
