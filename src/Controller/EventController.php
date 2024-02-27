<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Services\CityService;
use App\Services\EventService;
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

    #[Route('/add', name: 'add')]
    public function add(Request $request, EventService $eventService ): Response
    {
        $newEvent = new Event();
        $cities = $this->cityService->getAllCities();
        $newEventForm =$this->createForm(EventType::class,$newEvent);
        $newEventForm->handleRequest($request);

        if($newEventForm->isSubmitted() && $newEventForm->isValid()){
            $eventService->create($newEvent);


            $this->addFlash('success',"Event created successfully");
            //TODO : modifier route (renvoie sur le detail de l'event)
            return $this->redirectToRoute('event_details');
        }

        return $this->render('event/addEvent.html.twig', [
            'newEventForm'=>$newEventForm->createView(),
            'cities'=>$cities
        ]);
    }

    #[Route('/1', name: 'details')]

    public function showDetails():Response{
        $event= $this->eventService->getEventById(1);
        return $this->render('event/details.html.twig', [
            'event'=>$event,
        ]);

    }

    #[Route('/allEvents', name: 'list')]
    public function list(){
        $events=$this->eventService->getAllEvents();

        return $this->render('main/home.html.twig', [
            'events'=>$events,
        ]);
    }
}
