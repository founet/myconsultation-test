<?php

namespace App\Repository;

use App\Entity\AvailabilitySlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AvailabilitySlot>
 */
class AvailabilitySlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvailabilitySlot::class);
    }

    public function findAvailableSlotsFor(int $addressId, int $reasonId, \DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('slot')
            ->join('slot.slotReasons', 'sr')
            ->where('slot.address = :address')
            ->andWhere('sr.reason = :reason')
            ->andWhere('(slot.date = :date OR slot.date IS NULL)')
            ->setParameter('address', $addressId)
            ->setParameter('reason', $reasonId)
            ->setParameter('date', $date->format('Y-m-d'));

        return $qb->getQuery()->getResult();
    }


}
