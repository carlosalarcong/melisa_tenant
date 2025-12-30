<?php

namespace App\Entity\Tenant;

use App\Repository\CashRegisterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CashRegister - Caja de recaudación
 * (Migrado desde Legacy Caja)
 */
#[ORM\Entity(repositoryClass: CashRegisterRepository::class)]
#[ORM\Table(name: 'cash_register')]
class CashRegister
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id')]
    private ?Member $member = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $openingDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $closingDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $reopenDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reopenStatusId = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $initialAmount = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;
        return $this;
    }

    public function getOpeningDate(): ?\DateTimeInterface
    {
        return $this->openingDate;
    }

    public function setOpeningDate(?\DateTimeInterface $openingDate): self
    {
        $this->openingDate = $openingDate;
        return $this;
    }

    public function getClosingDate(): ?\DateTimeInterface
    {
        return $this->closingDate;
    }

    public function setClosingDate(?\DateTimeInterface $closingDate): self
    {
        $this->closingDate = $closingDate;
        return $this;
    }

    public function getReopenDate(): ?\DateTimeInterface
    {
        return $this->reopenDate;
    }

    public function setReopenDate(?\DateTimeInterface $reopenDate): self
    {
        $this->reopenDate = $reopenDate;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getFechaReapertura(): ?\DateTimeInterface
    {
        return $this->reopenDate;
    }

    public function getReopenStatusId(): ?int
    {
        return $this->reopenStatusId;
    }

    public function setReopenStatusId(?int $reopenStatusId): self
    {
        $this->reopenStatusId = $reopenStatusId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdEstadoReapertura()
    {
        return $this->reopenStatusId ? (object)['id' => $this->reopenStatusId] : null;
    }

    public function getInitialAmount(): ?string
    {
        return $this->initialAmount;
    }

    public function setInitialAmount(?string $initialAmount): self
    {
        $this->initialAmount = $initialAmount;
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
}
