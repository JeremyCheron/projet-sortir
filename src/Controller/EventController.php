<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventStatus;
use App\Form\EventType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\EventStatusRepository;
use App\Services\CityService;
use App\Services\EventService;
use Composer\XdebugHandler\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    public function __construct(private EventService $eventService, private CityService $cityService)
    {
    }
    #[Route('', name: 'list')]
    public function list(EventRepository $eventRepository){
//        $events=$this->eventService->getAllEvents();
        $events = $eventRepository->findAll();
        return $this->render('event/list.html.twig', [
            'events'=>$events,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EventService $eventService ): Response
    {
        $newEvent = new Event();
        $cities = $this->cityService->getAllCities();
//        TODO:créer serviceStatus
//        $newEvent->setStatus();
        $newEventForm =$this->createForm(EventType::class,$newEvent);
        $newEventForm->handleRequest($request);

        if($newEventForm->isSubmitted() && $newEventForm->isValid()){
            $eventService->create($newEvent);


            $this->addFlash('success',"Event created successfully");
            return $this->redirectToRoute('event_details', ['id'=>$newEvent->getId()]);
        }

        return $this->render('event/addEvent.html.twig', [
            'newEventForm'=>$newEventForm->createView(),
            'cities'=>$cities
        ]);
    }


    #[Route('/{id}', name: 'details', methods:['GET'])]
    public function showDetails(Event $event):Response{
        return $this->render('event/details.html.twig', [
            'event'=>$event,
        ]);

    }


}
