<?php

namespace App\Entity\Tenant;

use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentMethod - Formas de pago disponibles en el sistema
 * (Migrado desde Legacy FormaPago)
 */
#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
#[ORM\Table(name: 'payment_method')]
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean')]
    private bool $requiresBank = false;

    #[ORM\Column(type: 'boolean')]
    private bool $requiresDocument = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $paymentTypeId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
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

    public function getRequiresBank(): bool
    {
        return $this->requiresBank;
    }

    public function setRequiresBank(bool $requiresBank): self
    {
        $this->requiresBank = $requiresBank;
        return $this;
    }

    public function getRequiresDocument(): bool
    {
        return $this->requiresDocument;
    }

    public function setRequiresDocument(bool $requiresDocument): self
    {
        $this->requiresDocument = $requiresDocument;
        return $this;
    }

    public function getPaymentTypeId(): ?int
    {
        return $this->paymentTypeId;
    }

    public function setPaymentTypeId(?int $paymentTypeId): self
    {
        $this->paymentTypeId = $paymentTypeId;
        return $this;
    }
}
