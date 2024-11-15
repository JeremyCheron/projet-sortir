<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/campus')]
class CampusController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('/', name: 'app_campus_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(CampusRepository $campusRepository): Response
    {
        return $this->render('campus/index.html.twig', [
            'campuses' => $campusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_campus_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success',
            $this->translator->trans(
                'flashmessage.addcampus'
            ));

            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campus_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Campus $campus): Response
    {
        return $this->render('campus/show.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campus_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success',
            $this->translator->trans(
                'flashmessage.editcampus'
            ));


            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campus_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campus);
            $entityManager->flush();
            $this->addFlash('success',
            $this->translator->trans(
                'flashmessage.deletecampus'
            ));
        }

        return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
    }
}
