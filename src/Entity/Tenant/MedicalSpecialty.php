<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalSpecialty - Medical specialization
 * 
 * Represents a medical specialty (Especialidad Médica) that professionals can have.
 * Maps to HL7 FHIR: PractitionerRole.specialty
 * 
 * Examples: Cardiology, Pediatrics, Surgery, etc.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_specialty')]
class MedicalSpecialty
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $code = null;

    /**
     * HL7 SNOMED-CT code for the specialty
     * Example: 394802001 for General Medicine
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $snomedCode = null;

    /**
     * HL7 v2 Practitioner Specialty code
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $hl7SpecialtyCode = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', nullable: false)]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: false)]
    private State $state;

    /**
     * @var Collection<int, MemberSpecialty>
     */
    #[ORM\OneToMany(targetEntity: MemberSpecialty::class, mappedBy: 'specialty')]
    private Collection $memberSpecialties;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->memberSpecialties = new ArrayCollection();
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

    public function getSnomedCode(): ?string
    {
        return $this->snomedCode;
    }

    public function setSnomedCode(?string $snomedCode): self
    {
        $this->snomedCode = $snomedCode;
        return $this;
    }

    public function getHl7SpecialtyCode(): ?string
    {
        return $this->hl7SpecialtyCode;
    }

    public function setHl7SpecialtyCode(?string $hl7SpecialtyCode): self
    {
        $this->hl7SpecialtyCode = $hl7SpecialtyCode;
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

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;
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
     * @return Collection<int, MemberSpecialty>
     */
    public function getMemberSpecialties(): Collection
    {
        return $this->memberSpecialties;
    }

    public function addMemberSpecialty(MemberSpecialty $memberSpecialty): self
    {
        if (!$this->memberSpecialties->contains($memberSpecialty)) {
            $this->memberSpecialties->add($memberSpecialty);
            $memberSpecialty->setSpecialty($this);
        }
        return $this;
    }

    public function removeMemberSpecialty(MemberSpecialty $memberSpecialty): self
    {
        if ($this->memberSpecialties->removeElement($memberSpecialty)) {
            if ($memberSpecialty->getSpecialty() === $this) {
                $memberSpecialty->setSpecialty(null);
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
