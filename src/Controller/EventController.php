<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventStatus;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\EventStatusRepository;
use App\Services\CampusService;
use App\Services\CityService;
use App\Services\CustomQueriesService;
use App\Services\EventService;
use Composer\XdebugHandler\Status;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    public function __construct(private EventService $eventService,
                                private CityService $cityService,
                                private  TranslatorInterface $translator)
    {
    }
    #[Route('', name: 'list')]
    #[IsGranted('ROLE_USER')]
    public function list(): RedirectResponse
    {

       return $this->redirectToRoute('main_home');

    }

    #[Route('/add', name: 'add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request): Response
    {
        $user = $this->getUser();
        if($user instanceof User)
        {
        $formOrSuccess = $this->eventService->create($request, $user);
        $cities = $this->cityService->getAllCities();
        }
        if ($formOrSuccess instanceof Event) {
            $this->addFlash('success',
            $this->translator->trans(
                'flashmessage.addevent'
            ));
            return $this->redirectToRoute('event_details', [
                'id' => $formOrSuccess->getId()
            ]);
        }

        return $this->render('event/addEvent.html.twig', [
            'form' => $formOrSuccess->createView(),
            'cities' => $cities
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Event $event): Response
    {
        $formOrSuccess = $this->eventService->updateEvent($request, $event);
        $cities = $this->cityService->getAllCities();


        if ($formOrSuccess instanceof Event) {
            $this->addFlash('success',
                $this->translator->trans(
                    'flashmessage.updateevent'
                ));
            return $this->redirectToRoute('event_details', [
                'id' => $formOrSuccess->getId()
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $formOrSuccess->createView(),
            'cities' => $cities
        ]);
    }

    #[Route('/{id}/subscribe', name:'subscribe', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function subscribe(Event $event):Response
    {
        $activeUser = $this->getUser();
        if($activeUser instanceof User)
        {
            $this->eventService->subscribe($event, $activeUser);
            $this->addFlash('success',

                $this->translator->trans(
                    'flashmessage.sub',
                    ['%event%' => $event->getName()]
                ));
        }

        return $this->redirectToRoute('event_details', [
            'id' => $event->getId()
        ]);
    }

    #[Route('/{id}/unsubscribe', name:'unsubscribe', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function unsubscribe(Event $event):Response
    {
        $activeUser = $this->getUser();
        if($activeUser instanceof User)
        {
            $this->eventService->unsubscribe($event, $activeUser);
            $this->addFlash('success','Vous êtes bien désinscrit à l\'évènement ' . $event->getName());
        }
        return $this->redirectToRoute('event_details', [
            'id' => $event->getId()
        ]);
    }

    #[Route('/{id}', name: 'details', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showDetails(Event $event, CustomQueriesService $queriesService):Response{
        return $this->render('event/show.html.twig', [
            'event' => $queriesService->getOneEvent($event),
        ]);

    }

    #[Route('/{id}/publish', name: 'publish')]
    #[IsGranted('ROLE_USER')]
    public function publish(Event $event, CustomQueriesService $queriesService): Response
    {
        if ($event->getEventPlanner() !== $this->getUser())
        {
            $this->addFlash('error', 'Ce n\'est pas votre évènement !');
            $this->redirectToRoute('event_list');
        }
        $this->eventService->openEvent($event);
        $this->addFlash('success',
            $this->translator->trans(
                'flashmessage.publish'
            ));
        return $this->render('event/show.html.twig', [
            'event' => $queriesService->getOneEvent($event)
        ]);
    }

    #[Route('/{id}/cancel', name: 'cancel')]
    #[IsGranted('ROLE_ADMIN')]
    public function cancel(Event $event, CustomQueriesService $queriesService): Response
    {
        $this->eventService->cancelEvent($event);
        $this->addFlash('success',
            $this->translator->trans(
                'flashmessage.cancel'
            ));
        return $this->render('event/details.html.twig', [
            'event' => $queriesService->getOneEvent($event)
        ]);
    }



}
