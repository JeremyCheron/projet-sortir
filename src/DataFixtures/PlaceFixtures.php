<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PlaceFixtures extends Fixture
{
    public function __construct(private readonly EntityManagerInterface $em)
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
    }
}
