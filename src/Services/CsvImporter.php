<?php

namespace App\Services;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CsvImporter
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHasher)
    {
    }


    public function importUserCsv($csvFilePath)
    {
       $csv = array_map('str_getcsv', file($csvFilePath));

        $headers = array_shift($csv);
        $data = [];

        foreach ($csv as $row) {
            $rowData = array_combine($headers, $row);
            $data[] = $rowData;
        }

        $campusRepository = $this->em->getRepository(Campus::class);
        $campuses = $campusRepository->findAll();

        foreach ($data as $userData) {
            $user = new User();
            $user->setNickname($userData['pseudo']);
            $user->setLastname($userData['lastname']);
            $user->setFirstname($userData['firstname']);
            $user->setEmail($userData['email']);
            $user->setPhoneNumber($userData['telephone']);

            $user->setActive(true);

            $randomCampus = $campuses[array_rand($campuses)];
            $user->setCampus($randomCampus);

            $password = $userData['pseudo'];
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
        }
        $this->em->flush();
    }
}