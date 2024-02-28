<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationAdminFormType;
use App\Form\RegistrationFormType;
use App\Services\RegistrationService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private RegistrationService $registrationService)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setActive(true);
//            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('main_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    //todo : route accessible uniquement pour les ROLE_ADMIN
    #[Route('/register/admin', name: 'app_register_admin')]
    public function registerAdmin(Request $request): Response
    {
            $formOrSuccess = $this->registrationService->registerAsAdmin($request);
            if($formOrSuccess === true)
            {
                return $this->redirectToRoute('main_home');
            }

        return $this->render('registration/register_admin.html.twig', [
            'registrationForm' => $formOrSuccess->createView(),
        ]);
    }

}

