<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Repository\CampusRepository;
use App\Repository\EventStatusRepository;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime;

class EventFixtures extends Fixture
{
    public function __construct(private EventStatusRepository $eventStatusRepository,
                                private CampusRepository $campusRepository,
                                private PlaceRepository $placeRepository,
                                private UserRepository $userRepository,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
       $faker = Factory::create();
       $statuses=$this->eventStatusRepository->findAll();
       $campuses=$this->campusRepository->findAll();
       $users=$this->userRepository->findAll();
       $places=$this->placeRepository->findAll();

       for ($i = 0; $i<30; $i++){
           $event = new Event();
           $event->setName($faker->name);
           $event->setStartDate($faker->dateTime);
           $event->setDuration($faker->numberBetween(30,2880));
           $event->setRegistrationDeadline($faker->dateTime);
           $event->setMaxRegistrations($faker->numberBetween(5,20));
           $event->setDescription($faker->name);

           $randomStatus= $statuses[array_rand($statuses)];
           $event->setStatus($randomStatus);

           $randomCampus= $campuses[array_rand($campuses)];
           $event->setCampus($randomCampus);

           $randomPlace= $places[array_rand($places)];
           $event->setPlace($randomPlace);

           $randomUser= $users[array_rand($users)];
           $event->setEventPlanner($randomUser);

           $manager->persist($event);
       }

        $manager->flush();
    }
}
