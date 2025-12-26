<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberSpecialty - Relationship between Member and Medical Specialty
 * 
 * Represents the assignment of a medical specialty to a professional member.
 * Maps to HL7 FHIR: PractitionerRole.specialty
 * 
 * A professional can have multiple specialties.
 * Specialties can be blocked after a certain date (for regulatory compliance).
 */
#[ORM\Entity]
#[ORM\Table(name: 'member_specialty')]
#[ORM\UniqueConstraint(name: 'unique_member_specialty', columns: ['member_id', 'specialty_id'])]
class MemberSpecialty
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Member $member;

    #[ORM\ManyToOne(targetEntity: MedicalSpecialty::class, inversedBy: 'memberSpecialties')]
    #[ORM\JoinColumn(name: 'specialty_id', referencedColumnName: 'id', nullable: false)]
    private MedicalSpecialty $specialty;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: false)]
    private State $state;

    /**
     * Date from which this specialty cannot be edited
     * Used for regulatory compliance and audit purposes
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $blockDate = null;

    /**
     * Professional registration number for this specialty
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $registrationNumber = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $assignedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $certificationDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $expirationDate = null;

    public function __construct()
    {
        $this->assignedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): self
    {
        $this->member = $member;
        return $this;
    }

    public function getSpecialty(): MedicalSpecialty
    {
        return $this->specialty;
    }

    public function setSpecialty(?MedicalSpecialty $specialty): self
    {
        $this->specialty = $specialty;
        return $this;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function setState(State $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getBlockDate(): ?\DateTimeInterface
    {
        return $this->blockDate;
    }

    public function setBlockDate(?\DateTimeInterface $blockDate): self
    {
        $this->blockDate = $blockDate;
        return $this;
    }

    /**
     * Check if this specialty is currently blocked from editing
     */
    public function isBlocked(): bool
    {
        if ($this->blockDate === null) {
            return false;
        }
        return $this->blockDate <= new \DateTime();
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(?string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;
        return $this;
    }

    public function getAssignedAt(): ?\DateTimeInterface
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(?\DateTimeInterface $assignedAt): self
    {
        $this->assignedAt = $assignedAt;
        return $this;
    }

    public function getCertificationDate(): ?\DateTimeInterface
    {
        return $this->certificationDate;
    }

    public function setCertificationDate(?\DateTimeInterface $certificationDate): self
    {
        $this->certificationDate = $certificationDate;
        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->member->getUsername(), $this->specialty->getName());
    }
}
