<?php

namespace App\Entity\Tenant;

use App\Repository\BranchHealthInsuranceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * BranchHealthInsurance - Relación entre sucursal y seguro de salud
 * (Migrado desde Legacy RelSucursalPrevision)
 * HL7: Organization relationship with financial arrangements
 */
#[ORM\Entity(repositoryClass: BranchHealthInsuranceRepository::class)]
#[ORM\Table(name: 'branch_health_insurance')]
class BranchHealthInsurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $branchId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $healthInsuranceId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdSucursal()
    {
        return $this->branchId ? (object)['id' => $this->branchId] : null;
    }

    public function getHealthInsuranceId(): ?int
    {
        return $this->healthInsuranceId;
    }

    public function setHealthInsuranceId(?int $healthInsuranceId): self
    {
        $this->healthInsuranceId = $healthInsuranceId;
        return $this;
    }

    public function getIdPrevision()
    {
        return $this->healthInsuranceId ? (object)['id' => $this->healthInsuranceId] : null;
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
        return (object)['id' => $this->isActive ? 1 : 0];
    }
}
