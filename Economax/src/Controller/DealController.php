<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Deal;
use App\Entity\Temperature;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\DealRepository;
use App\Repository\TemperatureRepository;
use App\Repository\UserRepository;
use App\Service\ReportDealMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Advert;
use App\Entity\PromoCode;
use App\Form\AdvertType;
use App\Form\PromoCodeType;
use App\Repository\AdvertRepository;
use App\Repository\PromoCodeRepository;
use Symfony\Component\HttpFoundation\Request;

class DealController extends AbstractController
{
    private Security $security;

    public function __construct(
        protected AdvertRepository $advertRepository,
        protected PromoCodeRepository $promoCodeRepository,
        protected DealRepository $dealRepository,
        protected CommentRepository $commentRepository,
        protected TemperatureRepository $temperatureRepository,
        protected UserRepository $userRepository,
        protected ReportDealMailer $reportDealMailer,
        Security $security
    )
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $deals = $this->dealRepository->findAllByComment();
        $dealsHot = $this->dealRepository->findAllHotToday();

        return $this->render('deal/index.html.twig', [
            'deals' => $deals,
            'dealsHot' => $dealsHot,
        ]);
    }

    #[Route('/hot', name: 'app_deal_hot')]
    public function hot(): Response
    {
        $deals = $this->dealRepository->findAllHot();
        $dealsHot = $this->dealRepository->findAllHotToday();


        return $this->render('deal/index.html.twig', [
            'deals' => $deals,
            'dealsHot' => $dealsHot
        ]);
    }

    #[Route('/deal/list/{type}', name: 'app_deal_list')]
    public function list(String $type): Response
    {
        if($type == 'deal'){
            $deals = $this->advertRepository->findAll();
        } else {
            $deals = $this->promoCodeRepository->findAll();
        }

        return $this->render('deal/list.html.twig', [
            'deals' => $deals,
            'type' => $type
        ]);
    }

    #[Route('/deal/create/{type}', name: 'app_deal_create')]
    public function create(String $type, Request $request): Response
    {

        if($type == 'deal'){
            $deal = new Advert();
            $form = $this->createForm(AdvertType::class, $deal);
        } else {
            $deal = new PromoCode();
            $form = $this->createForm(PromoCodeType::class, $deal);
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            if($user == null){
                return $this->redirectToRoute('app_login');
            }
            $deal->setUser($this->security->getUser());
            if($type == 'deal'){
                $this->advertRepository->save($deal);
            } else {
                $this->promoCodeRepository->save($deal);
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('deal/form.html.twig', [
            'form' => $form,
            'type' => $type
        ]);
    }

    #[Route('/deal/info/{id}', name: 'app_deal_info')]
    public function info(Request $request,?Deal $deal): Response
    {
        $comments = new Comment();

        $form = $this->createForm(CommentType::class, $comments);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            if($user == null){
                return $this->redirectToRoute('app_login');
            }
            $comments->setUser($this->security->getUser());
            $comments->setDeal($deal);
            $this->commentRepository->save($comments);

            return $this->redirectToRoute('app_deal_info', ['id' => $deal->getId()]);
        }

        // all comments by date DESC
        $allComment = $this->commentRepository->findBy(['deal' => $deal], ['createdAt' => 'DESC']);

        return $this->render('deal/info.html.twig', [
            'deal' => $deal,
            'comments' => $allComment,
            'form' => $form
        ]);
    }

    #[Route('/deal/edit/{id}/temperature/increase', name: 'increase')]
    public function addTemperature(Deal $deal): JsonResponse
    {
        //vérifie que l'utilisateur n'a pas déjà voté pour ce deal
        $temperature = $this->temperatureRepository->findOneBy(['deal' => $deal, 'user' => $this->security->getUser()]);
        if($temperature == null){
            $temperature = new Temperature();
            $temperature->setDeal($deal);
            $temperature->setUser($this->security->getUser());
            $temperature->setValue(1);
            $this->temperatureRepository->save($temperature);
        }
        return new JsonResponse(['temperature' => $deal->getSumTemperatures()]);
    }



    #[Route('/deal/edit/{id}/temperature/decrease', name: 'decrease')]
    public function removeTemperature(Deal $deal): JsonResponse
    {
        //vérifie que l'utilisateur n'a pas déjà voté pour ce deal
        $temperature = $this->temperatureRepository->findOneBy(['deal' => $deal, 'user' => $this->security->getUser()]);
        if($temperature == null){
            $temperature = new Temperature();
            $temperature->setDeal($deal);
            $temperature->setUser($this->security->getUser());
            $temperature->setValue(-1);
            $this->temperatureRepository->save($temperature);
        }
        return new JsonResponse(['temperature' => $deal->getSumTemperatures()]);
    }

    #[Route('/deal/edit/{id}/favorite/add', name: 'addfavorite')]
    public function addFavorite(Deal $deal): Response
    {
        $user = $this->security->getUser();
        if($user == null){
            return new Response('Veuillez vous connecter pour ajouter un favoris');
        }
        $user->addFavorite($deal);
        $this->userRepository->save($user);
        return new Response('Deal ajouté à vos favoris');
    }

    #[Route('/deal/edit/{id}/favorite/remove', name: 'removefavorite')]
    public function removeFavorite(Deal $deal): Response
    {
        $user = $this->security->getUser();
        if($user == null){
            return new Response('Veuillez vous connecter pour ajouter un favoris');
        }
        $user->removeFavorite($deal);
        $this->userRepository->save($user);
        return new Response('Deal supprimé de vos favoris');
    }



    #[Route('/deal/delete/{id}', name: 'app_deal_delete')]
    public function delete(?Deal $deal): Response
    {
        $this->dealRepository->remove($deal);
        return $this->redirectToRoute('app_home');
    }

    #[Route('/deal/report/{id}', name: 'app_deal_report')]
    public function report(?Deal $deal): Response
    {
        $this->reportDealMailer->sendReport($deal);

        return $this->redirectToRoute('app_home');
    }

}
