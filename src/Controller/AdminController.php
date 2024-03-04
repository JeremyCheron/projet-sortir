<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin' , name: 'admin_')]
class AdminController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    #[Route('/manageUsers', name: 'manage_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function manageUsers(): Response
    {
        $users= $this->userService->findAll();
        return $this->render('admin/manage_users.html.twig', [
            'users'=>$users
        ]);
    }

    #[Route('/deleteUser/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user): Response
    {
        $this->userService->deleteUser($user);
        return $this->redirectToRoute('admin_manage_users');
    }

    #[Route('/switchActive/{id}', name: 'switch_active')]
    #[IsGranted('ROLE_ADMIN')]
    public function switchActive(User $user): Response
    {
        $this->userService->switchActive($user);
        return $this->redirectToRoute('admin_manage_users');
    }
}
