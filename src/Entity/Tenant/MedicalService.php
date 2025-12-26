<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalService - Healthcare service provided
 * 
 * Represents a specific medical service (Servicio) offered within a department.
 * Maps to HL7 FHIR: HealthcareService
 * 
 * Hierarchy: Organization → Branch → Department → MedicalService
 * 
 * A member can be assigned to multiple services, but only ONE can be active at a time.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_service')]
class MedicalService
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * HL7 ServiceDeliveryLocationRoleType
     * Examples: HOSP, PHARM, CLINIC, etc.
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $hl7ServiceType = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'services')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Department $department;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: false)]
    private State $state;

    /**
     * @var Collection<int, MemberService>
     */
    #[ORM\OneToMany(targetEntity: MemberService::class, mappedBy: 'service')]
    private Collection $memberServices;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->memberServices = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getHl7ServiceType(): ?string
    {
        return $this->hl7ServiceType;
    }

    public function setHl7ServiceType(?string $hl7ServiceType): self
    {
        $this->hl7ServiceType = $hl7ServiceType;
        return $this;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;
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

    /**
     * @return Collection<int, MemberService>
     */
    public function getMemberServices(): Collection
    {
        return $this->memberServices;
    }

    public function addMemberService(MemberService $memberService): self
    {
        if (!$this->memberServices->contains($memberService)) {
            $this->memberServices->add($memberService);
            $memberService->setService($this);
        }
        return $this;
    }

    public function removeMemberService(MemberService $memberService): self
    {
        if ($this->memberServices->removeElement($memberService)) {
            if ($memberService->getService() === $this) {
                $memberService->setService(null);
            }
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
