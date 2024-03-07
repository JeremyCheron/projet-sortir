<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RegistrationAdminFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Services\RegistrationService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private readonly RegistrationService    $registrationService,
                                private readonly UserRepository         $userRepository,
                                private readonly EntityManagerInterface $em,
                                private readonly UserService $userService)
    {
    }

    #[Route('/register', name: 'app_register')]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route('/register/admin', name: 'app_register_admin')]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route('/first', name: 'app_register_confirm')]
    public function confirmRegistration(Request $request): Response
    {

        $token = $request->query->get('token');

        // Find User by Token
        $user = $this->userRepository->findOneBy(['registrationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Invalid Token.');
        }

        // Form password
        $passwordForm = $this->createForm(ChangePasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $passwordForm->getData()->getPassword();
            $this->userService->hashNewPassword($user, $password);


            // Delete token
            $user->setRegistrationToken(null);
            $this->em->flush();

            return $this->redirectToRoute('user_modify_profile');
        }

        return $this->render('user/changepassword.html.twig', ['user' => $user,'passwordForm' => $passwordForm->createView()]);

    }

}

