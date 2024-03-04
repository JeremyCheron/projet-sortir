<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/details/{id}', name: 'details')]
    public function user(User $profil): Response
    {
        $activeUser = $this->getUser();
        if($activeUser instanceof User)
        {
            if($activeUser->getId() == $profil->getId())
            {
                return $this->render('user/user.html.twig', ['user'=>$activeUser]);
            }
        }
        return $this->render('user/user.html.twig', ['user'=>$profil]);
    }

    #[Route('/modify', name: 'modify_profile')]
    public function modify(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

            $userForm = $this->createForm(UserType::class, $user);
            $userForm->handleRequest($request);

            if($user instanceof User)
            {

                if ($userForm->isSubmitted() && $userForm->isValid())
                {

                    $entityManager->flush();

                    //on affiche un message à l'utilisateur pour lui indiquer que l'entité a été ajouté
                    $this->addFlash('success', 'Your profile has been modified !');

                    //puis on redirige vers la page des idées
                    return $this->redirectToRoute('user_details',['id'=>$user->getId()]);
                }


        }
        return $this->render('user/modify.html.twig', ['user' => $user, 'userForm' => $userForm->createView()]);
    }


    #[Route('/modify/password', name: 'change_password')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserService $userService): Response
    {
        $user = $this->getUser();

        $formOrSuccess = $userService->modifyPassword($request, $user);
        if ($formOrSuccess===true)
        {
            return $this->redirectToRoute('user_modify_profile');
        }

        return $this->render('user/changepassword.html.twig', ['user' => $user,'passwordForm' => $formOrSuccess->createView()]);
    }

}