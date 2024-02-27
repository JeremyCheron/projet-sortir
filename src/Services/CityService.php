<?php

namespace App\Services;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CityService
{

    public function __construct(private EntityManagerInterface $em,
                                private FormFactoryInterface $formFactory,
                                private CityRepository $cityRepository)
    {
    }

    public function getAllCities()
    {
        return $this->cityRepository->findAll();
    }

    public function createCity(Request $request)
    {
        $city = new City();
        $form = $this->formFactory->create(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($city);
            $this->em->flush();
            return true;
        }

        return $form;

    }

    public function updateCity(Request $request, City $city) {

        $form = $this->formFactory->create(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return true;
        }

        return $form;

    }

    public function deleteCity(Request $request, City $city, $token)
    {
        if ($token === $request->request->get('_token')) {
            $this->em->remove($city);
            $this->em->flush();
            return true;
        }
        return false;
    }

}