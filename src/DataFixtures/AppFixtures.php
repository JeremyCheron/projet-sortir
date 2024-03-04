<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\Place;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\EventStatusRepository;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppFixtures extends Fixture
{

    public function __construct(private EventStatusRepository $eventStatusRepository,
                                private CampusRepository $campusRepository,
                                private UserRepository $userRepository,
                                private PlaceRepository $placeRepository,
                                private TranslatorInterface $translator,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $postcode = $faker->postcode;
            $postcodeDigits = preg_replace('/[^0-9]/', '', $postcode);
            $postcodeInt = (int)$postcodeDigits;

            $city = new City();
            $city->setName($faker->city);
            $city->setZipCode($postcodeInt);
            $manager->persist($city);

            $place = new Place();
            $place->setName($faker->company);
            $place->setStreet($faker->streetName);
            $place->setLatitude($faker->latitude);
            $place->setLongitude($faker->longitude);

            $place->setCity($city);
            $manager->persist($place);
        }
        $manager->flush();

        $statuses = [$this->translator->trans('created'),
            $this->translator->trans('open'),
            $this->translator->trans('closed'),
            $this->translator->trans('ongoing'),
            $this->translator->trans('finished'),
            $this->translator->trans('archived'),
            $this->translator->trans('canceled')];

        foreach ($statuses as $statusToAdd) {
            $eventStatus = new EventStatus();
            $eventStatus->setName($statusToAdd);

            $manager->persist($eventStatus);
        }

        $manager->flush();

        for ($i=0; $i <10; $i++) {
            $user = new User();
            $campus = new Campus();
            $campus->setName($faker->city);
            $manager->persist($campus);

            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setlastname($faker->lastName);
            $user->setNickname($faker->userName);
            $user->setPassword('toto');
            $user->setPhoneNumber($faker->phoneNumber);
            $user->setActive(true);
            $user->setCampus($campus);
            $manager->persist($user);

        }

        $manager->flush();

        $dbStatuses=$this->eventStatusRepository->findAll();
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

            $randomStatus= $dbStatuses[array_rand($dbStatuses)];
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
