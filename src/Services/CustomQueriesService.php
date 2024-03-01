<?php

namespace App\Services;

use App\DTO\EventFilterDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CustomQueriesService
{

    public function __construct(private EntityManagerInterface $em)
    {
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

    public function searchEventWithCriterias(EventFilterDTO $filter, User $user = null)
    {

        $qb = $this->em->createQueryBuilder();

        $qb->select('e', 's', 'u', 'a', 'c')
            ->from('App\Entity\Event', 'e')
            ->leftJoin('e.status', 's')
            ->leftJoin('e.eventPlanner', 'u')
            ->leftJoin('e.attendants', 'a')
            ->leftJoin('e.campus', 'c');

        if (!empty($filter->name)) {
            $qb->andWhere('e.name LIKE :name')
                ->setParameter('name', '%' . $filter->name . '%');
        }

        if (!empty($filter->startDateMin)) {
            $qb->andWhere('e.startDate >= :start_date')
                ->setParameter('start_date', $filter->startDateMin);
        }

        if (!empty($filter->startDateMax)) {
            $qb->andWhere('e.startDate <= :start_date')
                ->setParameter('start_date', $filter->startDateMax);
        }

        if (!empty($filter->campus)) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $filter->campus);
        }

        if (!empty($filter->planner) && $filter->planner === true) {
            $qb->andWhere('e.eventPlanner = :me')
                ->setParameter('me', $user);
        }

        if (!empty($filter->attendant) && $filter->attendant === true) {
            $qb->andWhere(':user MEMBER OF e.attendants')
                ->setParameter('user', $user);
        }

        if (!empty($filter->pastEvents) && $filter->pastEvents === true) {
            $qb->andWhere('s.name = :status_finished')
                ->setParameter('status_finished', 'finished');
        }

        if (!empty($filter->statusName)) {
            $qb->andWhere('s.name != :status_archived')
                ->setParameter('status_archived', 'archived');
        }

        return $qb->getQuery()
                ->getResult();

    }

}