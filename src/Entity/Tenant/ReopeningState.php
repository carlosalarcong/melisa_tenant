<?php

namespace App\Entity\Tenant;

use App\Repository\ReopeningStateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * ReopeningState - Estado de reapertura de caja
 * (Migrado desde Legacy EstadoReapertura)
 */
#[ORM\Entity(repositoryClass: ReopeningStateRepository::class)]
#[ORM\Table(name: 'reopening_state')]
class ReopeningState
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $name = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // Alias Legacy
    public function getNombre(): ?string
    {
        return $this->name;
    }
}
