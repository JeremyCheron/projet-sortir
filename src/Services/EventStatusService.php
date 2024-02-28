<?php

namespace App\Services;

use App\Repository\EventStatusRepository;

class EventStatusService
{

    public function __construct(private EventStatusRepository $eventStatusRepository)
    {
    }

    public function getStatusById(int $id)
    {
        return $this->eventStatusRepository->find($id);
    }

}