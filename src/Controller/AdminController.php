<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    #[Route('/manageUsers', name: 'admin_manage_users')]
    public function manageUsers(): Response
    {
        $users= $this->userService->findAll();
        return $this->render('admin/manage_users.html.twig', [
            'users'=>$users
        ]);
    }

    #[Route('/deleteUser/{id}', name: 'admin_delete_user')]
    public function deleteUser(User $user): Response
    {
        $this->userService->deleteUser($user);
        return $this->redirectToRoute('admin_manage_users');
    }

    #[Route('/switchActive/{id}', name: 'admin_switch_active')]
    public function switchActive(User $user): Response
    {
        $this->userService->switchActive($user);
        return $this->redirectToRoute('admin_manage_users');
    }
}
