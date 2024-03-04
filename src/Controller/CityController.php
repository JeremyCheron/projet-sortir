<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Services\CityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/city')]
class CityController extends AbstractController
{

    public function __construct(private CityService $cityService)
    {
    }

    #[Route('/', name: 'app_city_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $this->cityService->getAllCities(),
        ]);
    }

    #[Route('/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $formOrSuccess = $this->cityService->createCity($request);

        if ($formOrSuccess === true) {
            return $this->redirectToRoute('app_city_index');
        }


        return $this->render('city/new.html.twig', [
            'form' => $formOrSuccess->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, City $city): Response
    {
        $formOrSuccess = $this->cityService->updateCity($request, $city);

        if ($formOrSuccess === true) {
            return $this->redirectToRoute('app_city_index');
        }

        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $formOrSuccess->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_city_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, City $city): Response
    {
        $token = $request->request->get('_token');

        if ($this->cityService->deleteCity($request, $city, $token)) {
            return $this->redirectToRoute('app_city_index');
        }

        throw $this->createAccessDeniedException('Invalid CSRF token.');

    }
}
