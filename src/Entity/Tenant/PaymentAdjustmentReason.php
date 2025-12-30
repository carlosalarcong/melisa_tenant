<?php

namespace App\Entity\Tenant;

use App\Repository\PaymentAdjustmentReasonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentAdjustmentReason - Motivo de diferencia
 * (Migrado desde Legacy MotivoDiferencia)
 */
#[ORM\Entity(repositoryClass: PaymentAdjustmentReasonRepository::class)]
#[ORM\Table(name: 'payment_adjustment_reason')]
class PaymentAdjustmentReason
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
    private ?int $organizationId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $paymentAdjustmentDirectionId = null;

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

    public function getPaymentAdjustmentDirectionId(): ?int
    {
        return $this->paymentAdjustmentDirectionId;
    }

    public function setPaymentAdjustmentDirectionId(?int $paymentAdjustmentDirectionId): self
    {
        $this->paymentAdjustmentDirectionId = $paymentAdjustmentDirectionId;
        return $this;
    }

    public function getIdTipoSentidoDiferencia()
    {
        return $this->paymentAdjustmentDirectionId ? (object)['id' => $this->paymentAdjustmentDirectionId] : null;
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
