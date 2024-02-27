<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
