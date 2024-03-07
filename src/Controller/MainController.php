<?php

namespace App\Controller;

use App\DTO\EventFilterDTO;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\EventRepository;
use App\Services\CustomQueriesService;
use App\Services\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/', name: 'main_')]
class MainController extends AbstractController
{

    public function __construct(private FormFactoryInterface $formFactory)
    {
    }

    #[Route('', name: 'home')]
    public function home(EventService $eventService,
                         Security $security,
                         CustomQueriesService $queriesService,
                        Request $request): Response
    {
        if (!$security->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }

        $eventService->archiveEvents();

        $filter = new EventFilterDTO();
        $form = $this->createForm(SearchType::class, $filter);

        $form->handleRequest($request);

        $user = $this->getUser();
        if ($user instanceof User)
        {
            $events = $queriesService->searchEventWithCriterias($filter, $user);
        }

        return $this->render('main/home.html.twig', [
            'events' => $events,
            'form' => $form
        ]);
    }

    #[Route('/search', name: 'search', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function search()
    {
    }

}
