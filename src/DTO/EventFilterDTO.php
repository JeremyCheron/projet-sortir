<?php

namespace App\DTO;

use App\Entity\Campus;
use DateTimeInterface;
use phpDocumentor\Reflection\Types\Boolean;

class EventFilterDTO
{
    public ?string $name;
    public ?DateTimeInterface $startDateMin;
    public ?DateTimeInterface $startDateMax;
    public ?Campus $campus;
    public ?bool $planner;
    public ?bool $attendant;
    public ?bool $pastEvents;

    public ?string $statusName;


}