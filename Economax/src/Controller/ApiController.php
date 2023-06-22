<?php

namespace App\Controller;

use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

class ApiController extends AbstractController
{
    public function __construct(
        protected DealRepository $dealRepository,
        protected UserRepository $userRepository
    )
    {
    }
    #[Route('/api/deal', name: 'app_api')]
    public function listDeal(SerializerInterface $serializer,): JsonResponse
    {
        $deals = $this->dealRepository->findAllDealsFromWeek();
        $jsonDeals = $serializer->serialize($deals, 'json', ['groups' => 'deal:read']);

        return new JsonResponse($jsonDeals, Response::HTTP_OK, [], true);
    }

    #[Route('/api/deal/saved', name: 'app_api_saved')]
    public function listDealSaved(AuthorizationCheckerInterface $authChecker,SerializerInterface $serializer,): JsonResponse|Response
    {
        if (!$authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $deals = $this->userRepository->find($this->getUser()->getId())->getFavorites();
        $jsonDeals = $serializer->serialize($deals, 'json', ['groups' => 'deal:read']);

        return new JsonResponse($jsonDeals, Response::HTTP_OK, [], true);
    }
}
