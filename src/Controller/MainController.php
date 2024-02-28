<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Services\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'main_')]
class MainController extends AbstractController
{
    #[Route('', name: 'home')]
    public function home(EventService $eventService): Response
    {
        return $this->render('main/home.html.twig', [
            'events'=>$eventService->getAllEvents()
        ]);
    }
}
