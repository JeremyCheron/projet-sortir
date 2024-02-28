<?php

namespace App\Controller;

use App\DTO\EventFilterDTO;
use App\DTO\FilteredEventDTO;
use App\Form\SearchType;
use App\Services\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/filter', name: 'filter_')]
class FilterController extends AbstractController
{
    #[Route('/events', name: 'events', methods: ['POST'])]
    public function filterEvents(Request $request, EventService $eventService ): JsonResponse
    {

        $eventFilterDTO = new EventFilterDTO();
        $eventFilterDTO->name = $request->request->get('name');
        $eventFilterDTO->startDateMin = $request->request->get('startDateMin');
        $eventFilterDTO->startDateMax = $request->request->get('startDateMax');
        $eventFilterDTO->campus = $request->request->get('campus');
        $eventFilterDTO->planner = $request->request->get('planner');
        $eventFilterDTO->attendant = $request->request->get('attendant');
        $eventFilterDTO->pastEvents = $request->request->get('pastEvents');

        $allEvents = $eventService->getAllEvents();
        $filteredEvents = [];
        foreach ($allEvents as $event) {

            if (
                ($eventFilterDTO->name === null || $event->getName() === $eventFilterDTO->name) &&
                ($eventFilterDTO->startDateMin === null || $event->getStartDate() > $eventFilterDTO->startDateMin) &&
                ($eventFilterDTO->startDateMax === null || $event->getStartDate() < $eventFilterDTO->startDateMin) &&
                ($eventFilterDTO->campus === null || $event->getCampus() === $eventFilterDTO->campus)
            ) {
                $filteredEvents[] = $event;
            }

        }

        return new JsonResponse($filteredEvents);

    }

    #[Route('/form', name: 'form', methods: ['GET', 'POST'])]
    public function form(FormFactoryInterface $formFactory)
    {
        $form = $formFactory->create(SearchType::class);

        return $this->render('/form/search.html.twig', [
            'form' => $form
        ]);
    }

}
