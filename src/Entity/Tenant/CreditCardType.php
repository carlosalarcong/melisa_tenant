<?php

namespace App\Entity\Tenant;

use App\Repository\CreditCardTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CreditCardType - Tipo de tarjeta de crédito
 * (Migrado desde Legacy TarjetaCreditoTipo)
 */
#[ORM\Entity(repositoryClass: CreditCardTypeRepository::class)]
#[ORM\Table(name: 'credit_card_type')]
class CreditCardType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getNombre(): ?string
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
