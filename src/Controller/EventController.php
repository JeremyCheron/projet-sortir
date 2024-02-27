<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Services\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(Request $request, EventService $eventService ): Response
    {
        $newEvent = new Event();
        $newEventForm =$this->createForm(EventType::class,$newEvent);
        $newEventForm->handleRequest($request);

        if($newEventForm->isSubmitted() && $newEventForm->isValid()){
            $eventService->create($newEvent);

            $this->addFlash('success',"Event created successfully");
            //TODO : modifier route (renvoie sur le detail de l'event)
            return $this->redirectToRoute('event_details');
        }

        return $this->render('event/addEvent.html.twig', [
            'newEventForm'=>$newEventForm->createView()
        ]);
    }

    #[Route('/2', name: 'details')]

    public function showDetails(EventRepository $ep):Response{
        $event= $ep->find(2);
        $attendants= $this->getUser();
        return $this->render('event/details.html.twig', [
            'event'=>$event,
            'attendants'=>$attendants
        ]);

    }
}
