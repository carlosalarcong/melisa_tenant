<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberService - Relationship between Member and Service
 * 
 * Represents the assignment of a member to a medical service.
 * Maps to HL7 FHIR: PractitionerRole (with location and service)
 * 
 * IMPORTANT RULES:
 * - A member can be assigned to MULTIPLE services
 * - Only ONE service can be ACTIVE at a time
 * - Active service determines the member's working context
 */
#[ORM\Entity]
#[ORM\Table(name: 'member_service')]
#[ORM\UniqueConstraint(name: 'unique_member_service', columns: ['member_id', 'service_id'])]
class MemberService
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Member $member;

    #[ORM\ManyToOne(targetEntity: MedicalService::class, inversedBy: 'memberServices')]
    #[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'id', nullable: false)]
    private MedicalService $service;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: false)]
    private State $state;

    /**
     * Indicates if this is the currently active service for the member
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isActive = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $assignedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $activatedAt = null;

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

    public function getService(): MedicalService
    {
        return $this->service;
    }

    public function setService(?MedicalService $service): self
    {
        $this->service = $service;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        if ($isActive) {
            $this->activatedAt = new \DateTime();
        }
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

    public function getActivatedAt(): ?\DateTimeInterface
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?\DateTimeInterface $activatedAt): self
    {
        $this->activatedAt = $activatedAt;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->member->getUsername(), $this->service->getName());
    }
}
