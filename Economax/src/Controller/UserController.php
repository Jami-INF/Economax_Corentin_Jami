<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(
        protected UserRepository $userRepository,
    )
    {
    }
    #[Route('/user/{id}/preview', name: 'app_user_preview')]
    public function preview(?User $user): Response
    {
        //TODO : render stats about user
        //TODO : render user badges
        return $this->render('user/preview.html.twig', [
            'user' => $user,
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
    public function dealsSaved(): Response
    {
        return $this->render('user/deals_saved.html.twig', [
            'controller_name' => 'UserController',
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
        if($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($user);
        }

        return $this->render('user/setting.html.twig', [
            'form' => $form,
        ]);
    }
}
