<?php

namespace App\Tests\Service;

use App\Entity\Reason;
use App\Entity\AvailabilitySlot;
use App\Service\AvailabilityService;
use App\Repository\AvailabilitySlotRepository;
use App\Repository\AppointmentRepository;
use App\Repository\UnavailabilityRepository;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AvailabilityServiceTest extends TestCase
{
    public function testReturnsFirstAvailableSlot()
    {
        $mockSlot = $this->createMock(AvailabilitySlot::class);
        $mockSlot->method('getStartTime')->willReturn(new \DateTime('08:00'));
        $mockSlot->method('getEndTime')->willReturn(new \DateTime('12:00'));
        $mockSlot->method('getDate')->willReturn(null);
        $mockSlot->method('getWeekdays')->willReturn(['wednesday']);

        $reason = new Reason();
        $reflection = new \ReflectionClass($reason);
        $prop = $reflection->getProperty('durationMinutes');
        $prop->setAccessible(true);
        $prop->setValue($reason, 30);

        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('getEntityManager')->willReturn(
            new class($reason) {
                private $reason;
                public function __construct($reason) {
                    $this->reason = $reason;
                }
                public function getRepository($class) {
                    return new class($this->reason) {
                        private $reason;
                        public function __construct($reason) { $this->reason = $reason; }
                        public function find($id) { return $this->reason; }
                    };
                }
            }
        );

        $slotRepo->method('findAvailableSlotsFor')->willReturn([$mockSlot]);

        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $appointmentRepo->method('findConflicts')->willReturn([]);

        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $unavailabilityRepo->method('findConflicts')->willReturn([]);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo);

        $slot = $service->getNextAvailableSlot(1, 1);

        $this->assertInstanceOf(DateTimeImmutable::class, $slot);
        $this->assertEquals('08:00', $slot->format('H:i'));
    }

    public function testReturnsNullIfReasonNotFound()
    {
        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('getEntityManager')->willReturn(
            new class {
                public function getRepository($class) {
                    return new class {
                        public function find($id) { return null; } // simulate motif not found
                    };
                }
            }
        );

        $service = new AvailabilityService($slotRepo, $this->createMock(AppointmentRepository::class), $this->createMock(UnavailabilityRepository::class));

        $this->assertNull($service->getNextAvailableSlot(1, 999));
    }

    public function testReturnsNullIfAllSlotsConflictWithAppointments()
    {
        $mockSlot = $this->createConfiguredMock(AvailabilitySlot::class, [
            'getStartTime' => new \DateTime('08:00'),
            'getEndTime' => new \DateTime('09:00'),
            'getDate' => null,
            'getWeekdays' => ['wednesday'],
        ]);

        $reason = new Reason();
        $prop = (new \ReflectionClass($reason))->getProperty('durationMinutes');
        $prop->setAccessible(true);
        $prop->setValue($reason, 30);

        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('getEntityManager')->willReturn(
            new class($reason) {
                private $reason;
                public function __construct($reason) { $this->reason = $reason; }
                public function getRepository($class) {
                    return new class($this->reason) {
                        private $reason;
                        public function __construct($reason) { $this->reason = $reason; }
                        public function find($id) { return $this->reason; }
                    };
                }
            }
        );
        $slotRepo->method('findAvailableSlotsFor')->willReturn([$mockSlot]);

        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $appointmentRepo->method('findConflicts')->willReturn(['dummy_conflict']);

        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $unavailabilityRepo->method('findConflicts')->willReturn([]);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo);

        $this->assertNull($service->getNextAvailableSlot(1, 1));
    }

    public function testReturnsNullIfAllSlotsConflictWithUnavailability()
    {
        $mockSlot = $this->createConfiguredMock(AvailabilitySlot::class, [
            'getStartTime' => new \DateTime('08:00'),
            'getEndTime' => new \DateTime('09:00'),
            'getDate' => null,
            'getWeekdays' => ['wednesday'],
        ]);

        $reason = new Reason();
        $prop = (new \ReflectionClass($reason))->getProperty('durationMinutes');
        $prop->setAccessible(true);
        $prop->setValue($reason, 30);

        $slotRepo = $this->createMock(AvailabilitySlotRepository::class);
        $slotRepo->method('getEntityManager')->willReturn(
            new class($reason) {
                private $reason;
                public function __construct($reason) { $this->reason = $reason; }
                public function getRepository($class) {
                    return new class($this->reason) {
                        private $reason;
                        public function __construct($reason) { $this->reason = $reason; }
                        public function find($id) { return $this->reason; }
                    };
                }
            }
        );
        $slotRepo->method('findAvailableSlotsFor')->willReturn([$mockSlot]);

        $appointmentRepo = $this->createMock(AppointmentRepository::class);
        $appointmentRepo->method('findConflicts')->willReturn([]);

        $unavailabilityRepo = $this->createMock(UnavailabilityRepository::class);
        $unavailabilityRepo->method('findConflicts')->willReturn(['conflict']);

        $service = new AvailabilityService($slotRepo, $appointmentRepo, $unavailabilityRepo);

        $this->assertNull($service->getNextAvailableSlot(1, 1));
    }

}
