<?php

namespace App\Services;



use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $em,
                                private UserPasswordHasherInterface $passwordHasher,
                                private UserRepository $userRepository,
                                private FormFactoryInterface $formFactory
                                )
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


    public function modifyPassword(Request $request, User $user): true|\Symfony\Component\Form\FormInterface
    {
      $passwordForm = $this->formFactory->create(ChangePasswordType::class, $user);
      $passwordForm->handleRequest($request);

      if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {

                $password = $passwordForm->getData()->getPassword();
                $this->hashNewPassword($user, $password);

      return true;
      }
      return $passwordForm;
    }

}

