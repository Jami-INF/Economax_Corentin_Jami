<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\User;
use App\Form\AlertType;
use App\Form\UserType;
use App\Repository\AlertRepository;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected DealRepository $dealRepository,
        protected AlertRepository $alertRepository,
    )
    {
    }
    #[Route('/user/{id}/preview', name: 'app_user_preview')]
    public function preview(?User $user, ManagerRegistry $registry): Response
    {
        $em = $registry->getManager();
        $em->getFilters()->disable('soft_deleteable');
        // Stats
        if($user->getDeals()->count() > 0){
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
        } else {
            $mostVote = 0;
            $percentDealHot = 0;
            $averageVote = 0;
        }

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
    public function alerts(?User $user, Request $request): Response
    {
        $user->setIsNotify(false);
        $this->userRepository->save($user);
        $alerts = $user->getAlerts();
        $allDeals = $this->dealRepository->findAll();
        $deals = [];
        foreach ($alerts as $alert) {
            foreach ($allDeals as $deal) {
                if(preg_match("/{$alert->getKeyWord()}/i", $deal->getTitle()) ) {
                    $alertTemp = $alert->getTemperatureValue();
                    $dealTemp = $deal->getSumTemperatures();
                    if($alertTemp <= $dealTemp) {
                        $deals[] = $deal;
                    }
                }
            }
        }

        return $this->render('user/alerts.html.twig', [
            'deals' => $deals,
        ]);
    }
    #[Route('/user/{id}/alerts/setting', name: 'app_user_alerts_setting')]
    public function alertsSetting(?User $user, Request $request): Response
    {
        $alert = new Alert();
        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $alert->setUser($user);
            $this->alertRepository->save($alert);

            return $this->redirectToRoute('app_user_alerts', [
                'id' => $user->getId(),
            ]);
        }

        $alerts = $user->getAlerts();

        return $this->render('user/alerts-setting.html.twig', [
            'alerts' => $alerts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/alerts/remove/{alert}', name: 'app_user_alerts_remove')]
    public function removeAlert(?User $user, ?Alert $alert): Response
    {
        $this->alertRepository->remove($alert);

        return $this->redirectToRoute('app_user_alerts', [
            'id' => $user->getId(),
        ]);
    }

    #[Route('/user/{id}/settings', name: 'app_user_settings')]
    public function settings(?User $user, Request $request): Response
    {
        //TODO: button to delete account and render anonymous user
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();
            if($avatar){
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$avatar->guessExtension();
                try {
                    $avatar->move(
                        $this->getParameter('avatar_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $this->redirectToRoute('app_user_settings', [
                        'id' => $user->getId(),
                    ]);
                }
                $user->setAvatar($newFilename);
            }

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
        $userConnected->setAvatar(null);
        $this->userRepository->save($userConnected);
        return $this->redirectToRoute('app_logout');
    }



}
