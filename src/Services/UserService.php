<?php

namespace App\Services;



use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $em,
                                private UserPasswordHasherInterface $passwordHasher,
                                private UserRepository $userRepository)
    {
    }

    public function hashNewPassword(User $user, string $password): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $this->em->flush();

    }

    public function findAll()
    {
        return $this->userRepository->findAll();
    }

    public function deleteUser(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();

    }
    public function switchActive(User $user): void
    {
        $user->setActive(!$user->isActive());
        $this->em->flush();
    }

}