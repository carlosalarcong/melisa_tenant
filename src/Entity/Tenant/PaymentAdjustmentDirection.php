<?php

namespace App\Entity\Tenant;

use App\Repository\PaymentAdjustmentDirectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentAdjustmentDirection - Tipo de sentido de diferencia (incremento/descuento)
 * (Migrado desde Legacy TipoSentidoDiferencia)
 */
#[ORM\Entity(repositoryClass: PaymentAdjustmentDirectionRepository::class)]
#[ORM\Table(name: 'payment_adjustment_direction')]
class PaymentAdjustmentDirection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getNombre(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getIdEstado()
    {
        return $this->isActive ? (object)['id' => 1] : (object)['id' => 2];
    }
}
