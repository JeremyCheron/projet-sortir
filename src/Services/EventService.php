<?php

namespace App\Services;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventService
{
    public function __construct(private EntityManagerInterface $em, private EventRepository $eventRepository)
    {
    }

    public function create(Event $newEvent): Event
    {
        $this->em->persist($newEvent);
        $this->em->flush();
        return $newEvent;
    }

    public function getAllEvents()
    {
        return $this->eventRepository->findAll();
    }

    public function getEventById(int $id)
    {
        return $this->eventRepository->find($id);
    }
}