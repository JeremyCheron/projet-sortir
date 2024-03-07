<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{

    public function __construct(private readonly EntityManagerInterface $em,
                                private readonly GroupRepository $groupRepository)
    {
    }

    public function getMyGroups($user)
    {
        return $this->groupRepository->findBy(['owner' => $user]);
    }
}