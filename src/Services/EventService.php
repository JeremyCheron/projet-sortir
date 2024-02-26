<?php

namespace App\Services;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;

class EventService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }
        public function create(Event $newEvent): Event
        {

        $this->em->persist($newEvent);
        $this->em->flush();
        return $newEvent;
    }


}