<?php

namespace App\Entity\Tenant;

use App\Repository\AccountPaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccountPayment - Pago de cuenta por paciente
 * (Migrado desde Legacy PagoCuenta)
 */
#[ORM\Entity(repositoryClass: AccountPaymentRepository::class)]
#[ORM\Table(name: 'account_payment')]
class AccountPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $patientId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cashierId = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $totalAmount = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $paymentStatusId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatientId(): ?int
    {
        return $this->patientId;
    }

    public function setPatientId(?int $patientId): self
    {
        $this->patientId = $patientId;
        return $this;
    }

    public function getCashierId(): ?int
    {
        return $this->cashierId;
    }

    public function setCashierId(?int $cashierId): self
    {
        $this->cashierId = $cashierId;
        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getPaymentStatusId(): ?int
    {
        return $this->paymentStatusId;
    }

    public function setPaymentStatusId(?int $paymentStatusId): self
    {
        $this->paymentStatusId = $paymentStatusId;
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

    // Alias para compatibilidad Legacy
    public function getIdEstadoPago()
    {
        return $this->paymentStatusId ? (object)['id' => $this->paymentStatusId] : null;
    }
}
