<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Services\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    #[Route('/newEvent', name: 'createEvent')]
    public function createEvent(Request $request, EventService $eventService ): Response
    {
        $newEvent = new Event();
        $newEventForm =$this->createForm(EventType::class,$newEvent);
        $newEventForm->handleRequest($request);

        if($newEventForm->isSubmitted() && $newEventForm->isValid()){
            $eventService->create($newEvent);

            $this->addFlash('success',"Event created successfully");
            //TODO : modifier route (renvoie sur le detail de l'event)
            return $this->redirectToRoute('main_home');
        }


        return $this->render('event/newEvent.html.twig', [
            'newEventForm'=>$newEventForm->createView()
        ]);
    }
}
