<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Services\CampusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{

    public function __construct(private readonly CampusService $campusService)
    {
    }

    #[Route('/', name: 'list')]
    public function index(): Response
    {
        return $this->render('campus/index.html.twig', [
            'campuses' => $this->campusService->getCampuses(),
        ]);
    }

    #[Route('/show', name: 'show')]
    public function show(Campus $campus)
    {
        return $this->render('campus/show.html.twig', [
            'campus' => $this->campusService->getCampusById($campus->getId())
        ]);
    }

    #[Route('/edit', name: 'edit')]
    public function edit(Campus $campus)
    {

    }

    #[Route('/delete', name: 'delete')]
    public function delete(Campus $campus)
    {

    }

}
