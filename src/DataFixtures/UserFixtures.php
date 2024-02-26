<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        // $product = new Product();
        // $manager->persist($product);
        for ($i=0; $i <10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setlastname($faker->lastName);
            $user->setPassword('toto');
            $user->setPhoneNumber($faker->phoneNumber);
            $user->setActive(true);

            $manager->persist($user);

        }

        $manager->flush();
    }
}
