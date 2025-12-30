<?php

namespace App\Entity\Tenant;

use App\Repository\FreeChargeReasonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * FreeChargeReason - Motivo de gratuidad (atención gratuita)
 * (Migrado desde Legacy MotivoGratuidad)
 */
#[ORM\Entity(repositoryClass: FreeChargeReasonRepository::class)]
#[ORM\Table(name: 'free_charge_reason')]
class FreeChargeReason
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stateId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $branchId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $typeId = null;

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

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function setStateId(?int $stateId): self
    {
        $this->stateId = $stateId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdEstado()
    {
        return $this->stateId ? (object)['id' => $this->stateId] : null;
    }

    public function getBranchId(): ?int
    {
        return $this->branchId;
    }

    public function setBranchId(?int $branchId): self
    {
        $this->branchId = $branchId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdSucursal()
    {
        return $this->branchId ? (object)['id' => $this->branchId] : null;
    }

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(?int $typeId): self
    {
        $this->typeId = $typeId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdTipoGratuidad()
    {
        return $this->typeId ? (object)['id' => $this->typeId] : null;
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
}
