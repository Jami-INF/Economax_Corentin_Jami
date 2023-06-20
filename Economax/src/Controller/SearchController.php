<?php

namespace App\Controller;

use App\Repository\DealRepository;
use App\Repository\GroupRepository;
use App\Repository\MarchandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(
        protected DealRepository $dealRepository,
        protected MarchandRepository $marchandRepository,
        protected GroupRepository $groupRepository
    )
    {
        $this->dealRepository = $dealRepository;
        $this->marchandRepository = $marchandRepository;
        $this->groupRepository = $groupRepository;
    }
    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search'); // $_POST['q']
        $deals = $this->dealRepository->findAllBySearch($search);
        $marchands = $this->marchandRepository->findAllBySearch($search);
        $groups = $this->groupRepository->findAllBySearch($search);
        return $this->render('search/index.html.twig', [
            'deals' => $deals,
            'marchands' => $marchands,
            'groups' => $groups,
        ]);
    }
}
