<?php

namespace App\Services;

use App\Repository\CampusRepository;

class CampusService
{

    public function __construct(private CampusRepository $campusRepository)
    {
    }

    public function getCampusById(int $id)
    {
        return $this->campusRepository->find($id);
    }

}