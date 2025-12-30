<?php

namespace App\Entity\Tenant;

use App\Repository\BankRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bank - Banco (Bank institution)
 * (Migrado desde Legacy Banco)
 */
#[ORM\Entity(repositoryClass: BankRepository::class)]
#[ORM\Table(name: 'bank')]
class Bank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $taxId = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $accountNumber = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stateId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $organizationId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getRut(): ?string
    {
        return $this->taxId;
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

    public function getAccountNumber(): ?int
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?int $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getCuentaCorriente(): ?int
    {
        return $this->accountNumber;
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

    public function getOrganizationId(): ?int
    {
        return $this->organizationId;
    }

    public function setOrganizationId(?int $organizationId): self
    {
        $this->organizationId = $organizationId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdEmpresa()
    {
        return $this->organizationId ? (object)['id' => $this->organizationId] : null;
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
