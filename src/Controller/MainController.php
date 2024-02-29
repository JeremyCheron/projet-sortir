<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\EventRepository;
use App\Services\CustomQueriesService;
use App\Services\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'main_')]
class MainController extends AbstractController
{

    public function __construct(private FormFactoryInterface $formFactory)
    {
    }

    #[Route('', name: 'home')]
    public function home(EventService $eventService, Security $security, CustomQueriesService $queriesService): Response
    {
        if (!$security->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }

        $form = $this->formFactory->create(SearchType::class);

        return $this->render('main/home.html.twig', [
            'events'=>$queriesService->getAllEventsWithStatusAndOwner(),
            'form' => $form
        ]);
    }
}
