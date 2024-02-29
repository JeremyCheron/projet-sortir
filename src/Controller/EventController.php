<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\EventStatusRepository;
use App\Services\CampusService;
use App\Services\CityService;
use App\Services\EventService;
use Composer\XdebugHandler\Status;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    public function __construct(private EventService $eventService,
                                private CityService $cityService)
    {
    }
    #[Route('', name: 'list')]
    public function list(){
       return $this->redirectToRoute('main_home');
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $user = $this->getUser();
        if($user instanceof User)
        {
        $formOrSuccess = $this->eventService->create($request, $user);
        $cities = $this->cityService->getAllCities();
        }
        if ($formOrSuccess === true) {
            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/addEvent.html.twig', [
            'form' => $formOrSuccess->createView(),
            'cities'=>$cities
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event): Response
    {
        $formOrSuccess = $this->eventService->updateEvent($request, $event);
        $cities = $this->cityService->getAllCities();


        if ($formOrSuccess === true) {
            return $this->redirectToRoute('main_home');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $formOrSuccess->createView(),
            'cities'=>$cities
        ]);
    }

    #[Route('/{id}/subscribe', name:'subscribe', methods: ['GET'])]
    public function subscribe (Event $event):Response
    {
        $activeUser = $this->getUser();
        if($activeUser instanceof User)
        {
        $this->eventService->subscribe($event, $activeUser);
        }

        return $this->redirectToRoute('event_details', [
            'id' => $event->getId()
        ]);
    }

    #[Route('/{id}/unsubscribe', name:'unsubscribe', methods: ['GET'])]
    public function unsubscribe(Event $event):Response
    {
        $activeUser = $this->getUser();
        if($activeUser instanceof User)
        {
            $this->eventService->unsubscribe($event, $activeUser);
        }
        return $this->redirectToRoute('event_details', [
            'id' => $event->getId()
        ]);
    }

    #[Route('/{id}', name: 'details', methods:['GET'])]
    public function showDetails(Event $event):Response{
        return $this->render('event/details.html.twig', [
            'event'=>$event,
        ]);

    }






}
