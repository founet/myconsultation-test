<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Unavailability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $start;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $end;

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): Unavailability
    {
        $this->start = $start;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}