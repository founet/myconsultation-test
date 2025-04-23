<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SlotReason
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private AvailabilitySlot $availabilitySlot;

    #[ORM\ManyToOne]
    private Reason $reason;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvailabilitySlot(): AvailabilitySlot
    {
        return $this->availabilitySlot;
    }

    public function setAvailabilitySlot(AvailabilitySlot $availabilitySlot): SlotReason
    {
        $this->availabilitySlot = $availabilitySlot;
        return $this;
    }

    public function getReason(): Reason
    {
        return $this->reason;
    }

    public function setReason(Reason $reason): SlotReason
    {
        $this->reason = $reason;
        return $this;
    }


}
