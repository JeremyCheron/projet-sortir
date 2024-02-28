<?php

namespace App\DataFixtures;

use App\Entity\EventStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    private array $status = ['created', 'open', 'closed', 'ongoing', 'finished', 'archived', 'canceled'];
    public function load(ObjectManager $manager): void
    {
        foreach ($this->status as $statusToAdd) {
            $eventStatus = new EventStatus();
            $eventStatus->setName($statusToAdd);

            $manager->persist($eventStatus);
        }


        $manager->flush();
    }
}
