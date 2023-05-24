<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
        Security $security
    )
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $deals = $this->dealRepository->findAll();

        return $this->render('deal/index.html.twig', [
            'deals' => $deals,
        ]);
    }

    #[Route('/deal', name: 'app_deal')]
    public function modal(): Response
    {
        return $this->render('deal/modal.html.twig');
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
            $deal->setTemperature(0);
            $deal->setUser($this->security->getUser());
            if($type == 'deal'){
                $this->advertRepository->save($deal);
            } else {
                $this->promoCodeRepository->save($deal);
            }

            return $this->redirectToRoute('app_deal');
        }

        return $this->render('deal/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/deal/info/{id}', name: 'app_deal_info')]
    public function info(?Deal $deal): Response
    {
        return $this->render('deal/info.html.twig', [
            'deal' => $deal,
        ]);
    }
}
