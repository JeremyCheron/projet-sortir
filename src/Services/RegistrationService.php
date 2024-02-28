<?php

namespace App\Services;

use App\Entity\User;
use App\Form\RegistrationAdminFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(private EntityManagerInterface $em,
                                private FormFactoryInterface $formFactory,
                                private UserRepository $userRepository,
                                private UserPasswordHasherInterface $passwordHasher)
    {
    }


    public function registerAsAdmin(Request $request): true|\Symfony\Component\Form\FormInterface
    {
        $user = new User();
        $form = $this->formFactory->create(RegistrationAdminFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setActive(true);
            $nickname = $user->getFirstname();
            $nickname = $nickname . $user->getLastname();

            //le password est le prenomnom (hashé dans la base de données)

            $hashedPassword = $this->passwordHasher->hashPassword($user, $nickname);
            $user->setPassword($hashedPassword);
            $nickname = $nickname . rand(100, 999);

            //le pseudo unique et le prenomnom suivi d'un nombre à 3 chiffres aléatoire

            $user->setNickname($nickname);

            $this->em->persist($user);
            $this->em->flush();
            return true;
        }

        return $form;

    }


}