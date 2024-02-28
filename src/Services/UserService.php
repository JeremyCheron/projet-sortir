<?php

namespace App\Services;



use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $em,
                                private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function hashNewPassword(User $user, string $password): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $this->em->flush();

    }
}