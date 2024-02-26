<?php

namespace App\DataFixtures;

use App\Entity\EventStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    private array $status = ['créée', 'ouverte', 'cloturée', 'en cours', 'passée', 'archivée', 'annulée'];
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
