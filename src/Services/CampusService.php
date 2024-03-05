<?php

namespace App\Services;

use App\Repository\CampusRepository;

class CampusService
{

    public function __construct(private readonly CampusRepository $campusRepository)
    {
    }

    public function getCampuses()
    {
        return $this->campusRepository->findAll();
    }

    public function getCampusById(int $id)
    {
        return $this->campusRepository->find($id);
    }

}