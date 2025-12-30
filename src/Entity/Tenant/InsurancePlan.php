<?php

namespace App\Entity\Tenant;

use App\Repository\InsurancePlanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InsurancePlan - Plan de previsión/seguro de salud
 * (Migrado desde Legacy Prevision)
 * HL7: Financial Class / Insurance
 */
#[ORM\Entity(repositoryClass: InsurancePlanRepository::class)]
#[ORM\Table(name: 'insurance_plan')]
class InsurancePlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $abbreviatedName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $planCode = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $interfaceCode = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $copaymentAmount = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isDefault = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stateId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $organizationId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $insuranceTypeId = null;

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
    public function getNombrePrevision(): string
    {
        return $this->name;
    }

    public function getAbbreviatedName(): ?string
    {
        return $this->abbreviatedName;
    }

    public function setAbbreviatedName(?string $abbreviatedName): self
    {
        $this->abbreviatedName = $abbreviatedName;
        return $this;
    }

    // Alias Legacy
    public function getNombreAbreviado(): ?string
    {
        return $this->abbreviatedName;
    }

    public function getPlanCode(): ?int
    {
        return $this->planCode;
    }

    public function setPlanCode(?int $planCode): self
    {
        $this->planCode = $planCode;
        return $this;
    }

    // Alias Legacy
    public function getCodigoPrevision(): ?int
    {
        return $this->planCode;
    }

    public function getInterfaceCode(): ?string
    {
        return $this->interfaceCode;
    }

    public function setInterfaceCode(?string $interfaceCode): self
    {
        $this->interfaceCode = $interfaceCode;
        return $this;
    }

    // Alias Legacy
    public function getCodigoInterfaz(): ?string
    {
        return $this->interfaceCode;
    }

    public function getCopaymentAmount(): ?int
    {
        return $this->copaymentAmount;
    }

    public function setCopaymentAmount(?int $copaymentAmount): self
    {
        $this->copaymentAmount = $copaymentAmount;
        return $this;
    }

    // Alias Legacy
    public function getCopago(): ?int
    {
        return $this->copaymentAmount;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    // Alias Legacy
    public function getIcono(): ?string
    {
        return $this->icon;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    // Alias Legacy
    public function getValorDefault(): int
    {
        return $this->isDefault ? 1 : 0;
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

    public function getIdEstado()
    {
        return $this->stateId ? (object)['id' => $this->stateId] : null;
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

    public function getInsuranceTypeId(): ?int
    {
        return $this->insuranceTypeId;
    }

    public function setInsuranceTypeId(?int $insuranceTypeId): self
    {
        $this->insuranceTypeId = $insuranceTypeId;
        return $this;
    }

    public function getIdTipoPrevision()
    {
        return $this->insuranceTypeId ? (object)['id' => $this->insuranceTypeId] : null;
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
