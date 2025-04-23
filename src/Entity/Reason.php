<?php

namespace App\Entity;

use App\Repository\ReasonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReasonRepository::class)]
class Reason
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $durationMinutes;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Reason
    {
        $this->name = $name;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): Reason
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }
}
