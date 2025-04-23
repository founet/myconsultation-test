<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $start;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $end;

    #[ORM\ManyToOne]
    private Address $address;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): Appointment
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): \DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): Appointment
    {
        $this->end = $end;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): Appointment
    {
        $this->address = $address;
        return $this;
    }



}