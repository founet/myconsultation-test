<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function findConflicts(\DateTimeInterface $start, \DateTimeInterface $end, int $addressId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.address = :address')
            ->andWhere('a.start < :end AND a.end > :start')
            ->setParameter('address', $addressId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

}
