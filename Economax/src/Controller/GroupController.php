<?php

namespace App\Controller;

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
        protected GroupRepository $groupRepository
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

    #[Route('/group/delete/{id}', name: 'app_group_delete')]
    public function delete(int $id): Response
    {
        $group = $this->groupRepository->find($id);
        $this->groupRepository->remove($group);

        return $this->redirectToRoute('app_group');
    }
}
