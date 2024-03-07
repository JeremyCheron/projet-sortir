<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Services\GroupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/group')]
class GroupController extends AbstractController
{

    public function __construct(private readonly GroupService $groupService)
    {
    }

    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $this->groupService->getMyGroups($this->getUser()),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->setOwner($this->getUser());
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Group $group): Response
    {

        if ($group->getOwner() !== $this->getUser()) {
            $this->addFlash("error","Ce groupe ne vous appartient pas");
            return $this->redirectToRoute('app_group_index', [
            ]);
        }

        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
