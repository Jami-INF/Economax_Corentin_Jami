<?php

namespace App\Controller;

use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(
        protected DealRepository $dealRepository
    )
    {
        $this->dealRepository = $dealRepository;
    }
    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search'); // $_POST['q']
        $deals = $this->dealRepository->findAllBySearch($search);
        return $this->render('search/index.html.twig', [
            'deals' => $deals,
        ]);
    }
}
