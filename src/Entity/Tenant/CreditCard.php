<?php

namespace App\Entity\Tenant;

use App\Repository\CreditCardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CreditCard - Tarjeta de crédito
 * (Migrado desde Legacy TarjetaCredito)
 */
#[ORM\Entity(repositoryClass: CreditCardRepository::class)]
#[ORM\Table(name: 'credit_card')]
class CreditCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $abbreviation = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $creditCardTypeId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stateId = null;

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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getAbreviacion(): ?string
    {
        return $this->abbreviation;
    }

    public function getCreditCardTypeId(): ?int
    {
        return $this->creditCardTypeId;
    }

    public function setCreditCardTypeId(?int $creditCardTypeId): self
    {
        $this->creditCardTypeId = $creditCardTypeId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdTarjetaCreditoTipo()
    {
        return $this->creditCardTypeId ? (object)['id' => $this->creditCardTypeId] : null;
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
