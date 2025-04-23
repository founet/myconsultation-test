<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class AvailabilitySlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private Address $address;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $weekdays = null; // e.g. ["monday", "wednesday"]

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $endTime;

    #[ORM\OneToMany(mappedBy: 'availabilitySlot', targetEntity: SlotReason::class)]
    private Collection $slotReasons;

    public function __construct()
    {
        $this->slotReasons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): AvailabilitySlot
    {
        $this->id = $id;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): AvailabilitySlot
    {
        $this->address = $address;
        return $this;
    }

    public function getWeekdays(): ?array
    {
        return $this->weekdays;
    }

    public function setWeekdays(?array $weekdays): AvailabilitySlot
    {
        $this->weekdays = $weekdays;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): AvailabilitySlot
    {
        $this->date = $date;
        return $this;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): AvailabilitySlot
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): AvailabilitySlot
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getSlotReasons(): Collection
    {
        return $this->slotReasons;
    }

    public function setSlotReasons(Collection $slotReasons): AvailabilitySlot
    {
        $this->slotReasons = $slotReasons;
        return $this;
    }

}
