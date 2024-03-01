<?php

namespace App\Services;

use App\DTO\EventFilterDTO;
use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class EventService
{
    public function __construct(private EntityManagerInterface $em,
                                private EventRepository $eventRepository,
                                private FormFactoryInterface $formFactory,
                                private EventStatusService $eventStatusService,
                                private CustomQueriesService $queriesService)
    {
    }

    public function create(Request $request,User $user)
    {
        $event = new Event();
        $form = $this->formFactory->create(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event->setStatus($this->eventStatusService->getStatusByName('created'));
            $event->setCampus($user->getCampus());
            $event->setEventPlanner($user);

        if ($form->getClickedButton() === $form->get('saveAndPublish'))
        {
            $event->setStatus($this->eventStatusService->getStatusByName('open'));
        }
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

    public function updateEvent(Request $request, Event $event)
    {
        $form = $this->formFactory->create(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getClickedButton() === $form->get('saveAndPublish'))
            {
                $event->setStatus($this->eventStatusService->getStatusByName('open'));
            }
            $this->em->flush();
            return true;
        }

        return $form;

    }

    public function subscribe(Event $event, User $user)
    {
        $dateTime = new DateTime();
        if ($event->getAttendants()->count() < $event->getMaxRegistrations() && $dateTime < $event->getRegistrationDeadline())
        {
            $event->addAttendant($user);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function unsubscribe(Event $event, User $user)
    {
        if ($event->getAttendants()->contains($user))
        {
            $event->removeAttendant($user);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function cancelEvent (Event $event )
    {
        $canceledEvent = $this->eventStatusService->getStatusByName('canceled');
        $event->setStatus($canceledEvent);
        $this->em->flush();
    }

    public function openEvent (Event $event )
    {
        $openedEvent = $this->eventStatusService->getStatusByName('open');
        $event->setStatus($openedEvent);
        $this->em->flush();
    }

    public function archiveEvents()
    {
        // Date actuelle moins 1 mois
        $date = new DateTime();
        $date = $date->modify("-1 month");

        //Event commencé au plus tard il y a un mois
        $filter = new EventFilterDTO();
        $filter->startDateMax = $date;
        $filter->statusName = 'archived';

        $events = $this->queriesService->searchEventWithCriterias($filter);

        $archivedStatus = $this->eventStatusService->getStatusByName('archived');

        foreach ($events as $event) {
            $event->setStatus($archivedStatus);
        }

        $this->em->flush();

    }

}