<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use App\Services\CustomQueriesService;
use App\Services\PlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/place')]
class PlaceController extends AbstractController
{

    public function __construct(private PlaceService $placeService,
                                private CustomQueriesService $queriesService,
                                private PlaceRepository $placeRepository )
    {
    }

    #[Route('/ajax/{id}', name: 'app_get_place_details')]
    public function getPlaceDetails($id): JsonResponse
    {
        $place = $this->placeRepository->find($id);

        // Vérifier si le lieu existe
        if (!$place) {
            // Retourner une réponse JSON avec un code d'erreur approprié si le lieu n'est pas trouvé
            return new JsonResponse(['error' => 'Place not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Construire un tableau de données avec les détails du lieu
        $placeDetails = [
            'name' => $place->getName(),
            'street' => $place->getStreet(),
            'zipcode' => $place->getCity()->getZipCode(),
            'latitude' => $place->getLatitude(),
            'longitude' => $place->getLongitude()
        ];

        // Retourner une réponse JSON avec les détails du lieu
        return new JsonResponse($placeDetails);
    }

    #[Route('/ByCity/{id}', name: 'app_place_by_city')]
    public function placesByCity(Request $request, $id): JsonResponse
    {
        $places = $this->placeRepository->findBy(['city' => $id]);
        $placesData = [];
            foreach ($places as $place) {
                $placesData[$place->getId()] = [
                    'name' => $place->getName(),
                    'street' =>$place->getStreet(),
                    'zipcode' => $place->getCity()->getZipCode(),
                    'latitude' => $place->getLatitude(),
                    'longitude' => $place->getLongitude()];
            }
        return new JsonResponse($placesData);
    }

    #[Route('/', name: 'app_place_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('place/index.html.twig', [
            'places' => $this->queriesService->getAllPlaces(),
        ]);
    }

    #[Route('/new', name: 'app_place_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
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
    #[IsGranted('ROLE_USER')]
    public function show(Place $place, CustomQueriesService $queriesService): Response
    {
        return $this->render('place/show.html.twig', [
            'place' => $queriesService->getPlace($place),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_place_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Place $place): Response
    {
        $token = $request->request->get('_token');

        if ($this->placeService->deletePlace($request, $place, $token)) {
            return $this->redirectToRoute('app_place_index');
        }

        throw $this->createAccessDeniedException('Invalide CSRF token.');

    }
}
