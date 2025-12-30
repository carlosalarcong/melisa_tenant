<?php

namespace App\Entity\Tenant;

use App\Repository\ReferralSourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * ReferralSource - Origen de referencia del paciente
 * (Migrado desde Legacy Origen)
 * HL7: PV1-10 Hospital Service / Referral Source
 */
#[ORM\Entity(repositoryClass: ReferralSourceRepository::class)]
#[ORM\Table(name: 'referral_source')]
class ReferralSource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $code;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $branchId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $referralSourceTypeId = null;

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

    // Alias Legacy
    public function getNombre(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    // Alias Legacy
    public function getCodigo(): string
    {
        return $this->code;
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

    public function getReferralSourceTypeId(): ?int
    {
        return $this->referralSourceTypeId;
    }

    public function setReferralSourceTypeId(?int $referralSourceTypeId): self
    {
        $this->referralSourceTypeId = $referralSourceTypeId;
        return $this;
    }

    public function getIdTipoOrigen()
    {
        return $this->referralSourceTypeId ? (object)['id' => $this->referralSourceTypeId] : null;
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
