<?php
namespace App\Service;

use App\Entity\Reason;
use App\Repository\AvailabilitySlotRepository;
use App\Repository\AppointmentRepository;
use App\Repository\ReasonRepository;
use App\Repository\UnavailabilityRepository;
use DateInterval;
use DateTimeImmutable;

class AvailabilityService
{
    public function __construct(
        private AvailabilitySlotRepository $slotRepo,
        private AppointmentRepository $appointmentRepo,
        private UnavailabilityRepository $unavailabilityRepo,
        private ReasonRepository $reasonRepo
    ) {}

    public function getReasons(int $reasonId): array
    {
        return $this->reasonRepo->findId($reasonId);
    }
    public function getNextAvailableSlot(int $addressId, int $reasonId): ?DateTimeImmutable
    {
        $now = new DateTimeImmutable();
        $reason = $this->getReason($reasonId);
        if (!$reason) return null;

        $duration = new DateInterval('PT' . $reason->getDurationMinutes() . 'M');

        for ($i = 0; $i < 30; $i++) {
            $date = $now->modify("+$i days");
            $slots = $this->getMatchingSlots($addressId, $reasonId, $date);

            foreach ($slots as $slot) {
                $availableTime = $this->findAvailableTimeInSlot($slot, $date, $duration, $addressId);
                if ($availableTime !== null) {
                    return $availableTime;
                }
            }
        }

        return null;
    }

    private function getReason(int $reasonId): ?Reason
    {
        return $this->slotRepo->getEntityManager()
            ->getRepository(Reason::class)
            ->find($reasonId);
    }

    private function getMatchingSlots(int $addressId, int $reasonId, \DateTimeImmutable $date): array
    {
        $slots = $this->slotRepo->findAvailableSlotsFor($addressId, $reasonId, $date);
        $dayName = strtolower($date->format('l'));

        return array_filter($slots, function ($slot) use ($dayName) {
            return $slot->getDate() !== null || in_array($dayName, $slot->getWeekdays() ?? []);
        });
    }

    private function findAvailableTimeInSlot($slot, \DateTimeImmutable $date, DateInterval $duration, int $addressId): ?DateTimeImmutable
    {
        $slotStart = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $slot->getStartTime()->format('H:i'));
        $slotEnd = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $slot->getEndTime()->format('H:i'));

        $current = $slotStart;
        while ($current->add($duration) <= $slotEnd) {
            if ($this->isSlotFree($current, $current->add($duration), $addressId)) {
                return $current;
            }
            $current = $current->add(new DateInterval('PT1M'));
        }

        return null;
    }

    private function isSlotFree(DateTimeImmutable $start, DateTimeImmutable $end, int $addressId): bool
    {
        $appointments = $this->appointmentRepo->findConflicts($start, $end, $addressId);
        $unavailabilities = $this->unavailabilityRepo->findConflicts($start, $end);

        return empty($appointments) && empty($unavailabilities);
    }
}
