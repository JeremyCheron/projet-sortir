<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('', name: 'my_profile')]
    public function user(EntityManagerInterface $entityManager): Response
    {

        $r=$entityManager->getRepository(User::class);

        //le faire dans un service
        $user=$r->find(1);
        return $this->render('user/user.html.twig',['user'=>$user]);
    }

    #[Route('/modify', name: 'modify_profile')]
    public function modify(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $entityManager->flush();

            //on affiche un message à l'utilisateur pour lui indiquer que l'entité a été ajouté
            $this->addFlash('success','Your profile has been modified !');

            //puis on redirige vers la page des idées
            return $this->redirectToRoute('user_my_profile');
        }

        return $this->render('user/modify.html.twig',['user'=>$user, 'userForm'=>$userForm->createView()]);
    }
}
