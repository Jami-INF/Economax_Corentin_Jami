<?php

namespace App\Controller;

use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends AbstractController
{

    public function __construct(
        protected GroupRepository $groupRepository,
        protected DealRepository $dealRepository,
    )
    {
    }

    #[Route('/group', name: 'app_group')]
    public function index(): Response
    {
        $allGroups = $this->groupRepository->findAll();

        return $this->render('group/index.html.twig', [
            'groups' => $allGroups,
        ]);
    }

    #[Route('/group/create', name: 'app_group_create')]
    public function create(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->groupRepository->save($group);

            return $this->redirectToRoute('app_group');
        }
        return $this->render('group/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/group/{id}', name: 'app_group_show')]
    public function show(?Group $group): Response
    {
        $deal = $this->dealRepository->findBy(['groups' => $group]);

        return $this->render('group/show.html.twig', [
            'deals' => $deal,
        ]);
    }

    #[Route('/group/delete/{id}', name: 'app_group_delete')]
    public function delete(int $id): Response
    {
        $group = $this->groupRepository->find($id);
        if($group->getDeals()->count() > 0) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer un groupe qui contient des deals');
            return $this->redirectToRoute('app_group');
        }
        $this->groupRepository->remove($group);

        return $this->redirectToRoute('app_group');
    }
}
