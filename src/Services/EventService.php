<?php

namespace App\Services;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class EventService
{
    public function __construct(private EntityManagerInterface $em,
                                private EventRepository $eventRepository,
                                private FormFactoryInterface $formFactory,
                                private EventStatusService $eventStatusService)
    {
    }

    public function create(Request $request,User $user)
    {
        $event = new Event();
        $form = $this->formFactory->create(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setStatus($this->eventStatusService->getStatusById(1));
            $event->setCampus($user->getCampus());
            $event->setEventPlanner($user);
            $this->em->persist($event);
            $this->em->flush();

            return true;

        }

        return $form;

    }

    public function getAllEvents()
    {
        return $this->eventRepository->findAll();
    }

    public function getEventById(int $id)
    {
        return $this->eventRepository->find($id);
    }

    public function updateEvent(Request $request, Event $event, User $user)
    {
        $form = $this->formFactory->create(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setStatus($this->eventStatusService->getStatusById(1));
            $this->em->flush();
            return true;
        }

        return $form;

    }
}