<?php

namespace App\Entity\Tenant;

use App\Repository\TreatmentTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * TreatmentType - Tipo de tratamiento
 * (Migrado desde Legacy TipoTratamiento)
 * HL7: Treatment Type
 */
#[ORM\Entity(repositoryClass: TreatmentTypeRepository::class)]
#[ORM\Table(name: 'treatment_type')]
class TreatmentType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $isImed = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $organizationId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    // Alias Legacy
    public function getCodigo(): int
    {
        return $this->code;
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

    public function isImed(): bool
    {
        return $this->isImed;
    }

    public function setIsImed(bool $isImed): self
    {
        $this->isImed = $isImed;
        return $this;
    }

    // Alias Legacy
    public function getEsImed(): bool
    {
        return $this->isImed;
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

    public function getOrganizationId(): ?int
    {
        return $this->organizationId;
    }

    public function setOrganizationId(?int $organizationId): self
    {
        $this->organizationId = $organizationId;
        return $this;
    }

    public function getIdEmpresa()
    {
        return $this->organizationId ? (object)['id' => $this->organizationId] : null;
    }
}
