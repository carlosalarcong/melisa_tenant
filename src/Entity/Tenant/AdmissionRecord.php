<?php

namespace App\Entity\Tenant;

use App\Repository\Tenant\AdmissionRecordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdmissionRecordRepository::class)]
#[ORM\Table(name: 'admission_record')]
#[ORM\HasLifecycleCallbacks]
class AdmissionRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\Column(length: 30)]
    private string $admissionType = 'hospitalaria';

    #[ORM\Column(length: 30)]
    private string $status = 'draft';

    #[ORM\Column(nullable: true)]
    private ?int $payerId = null;

    #[ORM\Column(nullable: true)]
    private ?int $agreementId = null;

    #[ORM\Column(nullable: true)]
    private ?int $serviceId = null;

    #[ORM\Column(nullable: true)]
    private ?int $bedId = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $triage = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $consultationReason = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): self
    {
        $this->person = $person;
        return $this;
    }

    public function getAdmissionType(): string
    {
        return $this->admissionType;
    }

    public function setAdmissionType(string $admissionType): self
    {
        $this->admissionType = $admissionType;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPayerId(): ?int
    {
        return $this->payerId;
    }

    public function setPayerId(?int $payerId): self
    {
        $this->payerId = $payerId;
        return $this;
    }

    public function getAgreementId(): ?int
    {
        return $this->agreementId;
    }

    public function setAgreementId(?int $agreementId): self
    {
        $this->agreementId = $agreementId;
        return $this;
    }

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function setServiceId(?int $serviceId): self
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getBedId(): ?int
    {
        return $this->bedId;
    }

    public function setBedId(?int $bedId): self
    {
        $this->bedId = $bedId;
        return $this;
    }

    public function getTriage(): ?string
    {
        return $this->triage;
    }

    public function setTriage(?string $triage): self
    {
        $this->triage = $triage;
        return $this;
    }

    public function getConsultationReason(): ?string
    {
        return $this->consultationReason;
    }

    public function setConsultationReason(?string $consultationReason): self
    {
        $this->consultationReason = $consultationReason;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

