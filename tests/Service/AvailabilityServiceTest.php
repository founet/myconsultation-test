<?php

namespace App\Tests\Service;

use App\Entity\Reason;
use App\Entity\AvailabilitySlot;
use App\Service\AvailabilityService;
use App\Repository\AvailabilitySlotRepository;
use App\Repository\AppointmentRepository;
use App\Repository\UnavailabilityRepository;
use App\Repository\ReasonRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AvailabilityServiceTest extends TestCase
{
    private function createReason(): Reason
    {
        $reason = new Reason();
        $ref = new \ReflectionClass($reason);
        $prop = $ref->getProperty('durationMinutes');
        $prop->setAccessible(true);
        $prop->setValue($reason, 30);
        return $reason;
    }

    private function createMockSlot(): AvailabilitySlot
    {
        $slot = $this->createMock(AvailabilitySlot::class);
        $slot->method('getStartTime')->willReturn(new \DateTime('08:00'));
        $slot->method('getEndTime')->willReturn(new \DateTime('12:00'));
        $slot->method('getDate')->willReturn(null);
        $slot->method('getWeekdays')->willReturn(['wednesday']);
        return $slot;
    }

    public function testReturnsFirstAvailableSlot()
    {
        $reason = $this->createReason();
        $slot = $this->createMockSlot();

        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('findAvailableSlotsFor')->willReturn([$slot]);

        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $appointmentRepo->method('findConflicts')->willReturn([]);

        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $unavailabilityRepo->method('findConflicts')->willReturn([]);

        $reasonRepo = $this->createMock(ReasonRepository::class);
        $reasonRepo->method('find')->willReturn($reason);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo, $reasonRepo);

        $result = $service->getNextAvailableSlot(1, 1);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals('08:00', $result->format('H:i'));
    }

    public function testReturnsNullIfReasonNotFound()
    {
        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $reasonRepo = $this->createMock(ReasonRepository::class);
        $reasonRepo->method('find')->willReturn(null);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo, $reasonRepo);

        $this->assertNull($service->getNextAvailableSlot(1, 999));
    }

    public function testReturnsNullIfAllSlotsConflictWithAppointments()
    {
        $reason = $this->createReason();
        $slot = $this->createMockSlot();

        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('findAvailableSlotsFor')->willReturn([$slot]);

        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $appointmentRepo->method('findConflicts')->willReturn(['conflict']);

        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $unavailabilityRepo->method('findConflicts')->willReturn([]);

        $reasonRepo = $this->createMock(ReasonRepository::class);
        $reasonRepo->method('find')->willReturn($reason);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo, $reasonRepo);

        $this->assertNull($service->getNextAvailableSlot(1, 1));
    }

    public function testReturnsNullIfAllSlotsConflictWithUnavailability()
    {
        $reason = $this->createReason();
        $slot = $this->createMockSlot();

        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('findAvailableSlotsFor')->willReturn([$slot]);

        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $appointmentRepo->method('findConflicts')->willReturn([]);

        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $unavailabilityRepo->method('findConflicts')->willReturn(['conflict']);

        $reasonRepo = $this->createMock(ReasonRepository::class);
        $reasonRepo->method('find')->willReturn($reason);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo, $reasonRepo);

        $this->assertNull($service->getNextAvailableSlot(1, 1));
    }
}
