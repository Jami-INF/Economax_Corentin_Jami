<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private Security $security;

    public function __construct(
        protected UserRepository $userRepository,
        protected DealRepository $dealRepository,
        Security $security

    )
    {
        $this->security = $security;

    }
    #[Route('/user/{id}/preview', name: 'app_user_preview')]
    public function preview(?User $user): Response
    {
        // Stats
        $dealWithMostVote = $this->dealRepository->findMostVotedDealByUser($user);
        $mostVote = $dealWithMostVote->getSumTemperatures();

        $dealsHot = $this->dealRepository->findNumberOfDealsBecommingHotByUser($user);
        $nbDealHot = 0;
        foreach ($dealsHot as $deal) {
            $nbDealHot += 1;
        }
        $nbDeal = $user->getDeals()->count();
        $percentDealHot = $nbDealHot / $nbDeal * 100;

        $dealsVote = $this->dealRepository->findDealsPostedByUserInLastYear($user);
        $vote = 0;
        foreach ($dealsVote as $deal) {
            $vote += $deal->getSumTemperatures();
        }
        $averageVote = $vote / count($dealsVote);

        // Badges
        $nbVote = $this->dealRepository->findNumberOfVoteByUser($user)["nbVotes"];
        $nbComment = $user->getComment()->count();
        $nbDeal = $user->getDeals()->count();

        return $this->render('user/preview.html.twig', [
            'user' => $user,
            'vote' => $mostVote,
            'percentDealHot' => $percentDealHot,
            'averageVote' => $averageVote,
            'nbVote' => $nbVote,
            'nbComment' => $nbComment,
            'nbDeal' => $nbDeal,
        ]);
    }

    #[Route('/user/{id}/deals', name: 'app_user_deals')]
    public function deals(?User $user): Response
    {
        // find all deals posted by user
        $deals = $user->getDeals();

        return $this->render('user/deals.html.twig', [
            'deals' => $deals,
        ]);
    }

    #[Route('/user/{id}/deals/saved', name: 'app_user_deals_saved')]
    public function dealsSaved(?User $user): Response
    {
        $dealFavorite = $user->getFavorites();
        return $this->render('user/deals_saved.html.twig', [
            'deals' => $dealFavorite
        ]);
    }

    #[Route('/user/{id}/alerts', name: 'app_user_alerts')]
    public function alerts(): Response
    {
        return $this->render('user/alerts.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/{id}/settings', name: 'app_user_settings')]
    public function settings(?User $user, Request $request): Response
    {
        //TODO: button to delete account and render anonymous user
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($user);
        }

        return $this->render('user/setting.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
    /**
     * AnonymizeUser
     * #Route('/user/{id}/anonymize', name: 'app_user_anonymize')
     */
    #[Route('/user/{id}/anonymize', name: 'app_user_anonymize')]
    public function anonymizeUser(?User $userConnected): Response
    {
        $userConnected->setUsername("Anonymous");
        $userConnected->setEmail("anonymedUser@" . $userConnected->getId());
        $userConnected->setPassword("anonymedUser" . $userConnected->getId());
        $userConnected->setImageName("anonymous.png");
        $this->userRepository->save($userConnected);
        return $this->redirectToRoute('app_logout');
    }



}
