<?php

namespace App\DataFixtures;

use App\Entity\Campus;
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
    }
}
