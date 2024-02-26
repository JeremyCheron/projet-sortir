<?php

namespace App\Services;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PlaceService
{

    public function __construct(private EntityManagerInterface $em, private FormFactoryInterface $formFactory,
                                private PlaceRepository $placeRepository)
    {
    }

    public function getAllPlaces()
    {
        return $this->placeRepository->findAll();
    }

    public function createPlace(Request $request)
    {
        $place = new Place();
        $form = $this->formFactory->create(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($place);
            $this->em->flush();
            return true;
        }

        return $form;

    }

    public function updatePlace(Request $request, Place $place)
    {
        $form = $this->formFactory->create(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return true;
        }

        return $form;
    }

    public function deletePlace(Request $request, Place $place, $token)
    {
        if ($token === $request->request->get('_token')) {
            $this->em->remove($place);
            $this->em->flush();
            return true;
        }

        return false;
    }

}