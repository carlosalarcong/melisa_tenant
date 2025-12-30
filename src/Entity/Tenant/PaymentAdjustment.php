<?php

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentAdjustment - Ajustes o diferencias en pagos (equivalente a Diferencia legacy)
 */
#[ORM\Entity()]
#[ORM\Table(name: 'payment_adjustment')]
class PaymentAdjustment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $cancellationDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $requestDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $authorizationDate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $totalAmount = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $discountAmount = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $totalWithDiscount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->requestDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCancellationDate(): ?\DateTimeImmutable
    {
        return $this->cancellationDate;
    }

    public function setCancellationDate(?\DateTimeImmutable $cancellationDate): static
    {
        $this->cancellationDate = $cancellationDate;
        return $this;
    }

    public function getRequestDate(): ?\DateTimeImmutable
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeImmutable $requestDate): static
    {
        $this->requestDate = $requestDate;
        return $this;
    }

    public function getAuthorizationDate(): ?\DateTimeImmutable
    {
        return $this->authorizationDate;
    }

    public function setAuthorizationDate(?\DateTimeImmutable $authorizationDate): static
    {
        $this->authorizationDate = $authorizationDate;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getDiscountAmount(): ?string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(string $discountAmount): static
    {
        $this->discountAmount = $discountAmount;
        return $this;
    }

    public function getTotalWithDiscount(): ?string
    {
        return $this->totalWithDiscount;
    }

    public function setTotalWithDiscount(string $totalWithDiscount): static
    {
        $this->totalWithDiscount = $totalWithDiscount;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
