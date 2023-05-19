<?php

namespace App\Controller;

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
        Security $security
    )
    {
        $this->security = $security;
    }


    #[Route('/deal', name: 'app_deal')]
    public function index(): Response
    {
        return $this->render('deal/index.html.twig');
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
}
