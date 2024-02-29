<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class CustomQueriesService
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getAllEventsWithStatusAndOwner()
    {

        $qb = $this->em->createQueryBuilder();

        return $qb->select('e', 's', 'u', 'a')
            ->from('App\Entity\Event', 'e')
            ->leftJoin('e.status', 's')
            ->leftJoin('e.eventPlanner', 'u')
            ->leftJoin('e.attendants', 'a')
            ->getQuery()
            ->getResult();

    }

    public function getOneEvent($id) {

        $qb = $this->em->createQueryBuilder();

        return $qb->select('e', 's', 'u', 'a', 'c', 'p', 'city')
            ->from('App\Entity\Event', 'e')
            ->leftJoin('e.status', 's')
            ->leftJoin('e.eventPlanner', 'u')
            ->leftJoin('e.attendants', 'a')
            ->leftJoin('e.campus', 'c')
            ->leftJoin('e.place', 'p')
            ->leftJoin('p.city', 'city')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getAllPlaces()
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('p', 'c')
            ->from('App\Entity\Place', 'p')
            ->leftJoin('p.city', 'c')
            ->getQuery()
            ->getResult();
    }

    public function getPlace($id)
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('p', 'c')
            ->from('App\Entity\Place', 'p')
            ->leftJoin('p.city', 'c')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}