<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use App\Services\PlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/place')]
class PlaceController extends AbstractController
{

    public function __construct(private PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    #[Route('/', name: 'app_place_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('place/index.html.twig', [
            'places' => $this->placeService->getAllPlaces(),
        ]);
    }

    #[Route('/new', name: 'app_place_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $formOrSuccess = $this->placeService->createPlace($request);

        if ($formOrSuccess === true) {
            return $this->redirectToRoute('app_place_index');
        }

        return $this->render('place/new.html.twig', [
            'form' => $formOrSuccess->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_place_show', methods: ['GET'])]
    public function show(Place $place): Response
    {
        return $this->render('place/show.html.twig', [
            'place' => $place,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_place_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        $formOrSuccess = $this->placeService->updatePlace($request, $place);

        if ($formOrSuccess === true) {
            return $this->redirectToRoute('app_place_index');
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $formOrSuccess->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_place_delete', methods: ['POST'])]
    public function delete(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');

        if ($this->placeService->deletePlace($request, $place, $token)) {
            return $this->redirectToRoute('app_place_index');
        }

        throw $this->createAccessDeniedException('Invalide CSRD token.');

    }
}
