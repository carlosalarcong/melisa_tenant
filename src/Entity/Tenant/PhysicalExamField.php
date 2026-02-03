<?php

namespace App\Entity\Tenant;

use App\Repository\Tenant\PhysicalExamFieldRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PhysicalExamField
 * 
 * Campos configurables para examen físico con múltiples agrupaciones
 */
#[ORM\Entity(repositoryClass: PhysicalExamFieldRepository::class)]
#[ORM\Table(name: 'physical_exam_field')]
class PhysicalExamField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    #[Assert\Length(max: 45)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'sort_order', type: 'integer')]
    private int $sortOrder = 0;

    #[ORM\Column(name: 'range_min', type: 'integer', nullable: true)]
    private ?int $rangeMin = null;

    #[ORM\Column(name: 'range_max', type: 'integer', nullable: true)]
    private ?int $rangeMax = null;

    #[ORM\Column(name: 'age_min', type: 'integer', nullable: true)]
    private ?int $ageMin = null;

    #[ORM\Column(name: 'age_max', type: 'integer', nullable: true)]
    private ?int $ageMax = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    #[Assert\Length(max: 45)]
    private ?string $unit = null;

    #[ORM\Column(name: 'is_weight', type: 'boolean')]
    private bool $isWeight = false;

    #[ORM\Column(name: 'is_height', type: 'boolean')]
    private bool $isHeight = false;

    #[ORM\Column(name: 'is_bmi', type: 'boolean')]
    private bool $isBmi = false;

    #[ORM\Column(name: 'is_temperature', type: 'boolean')]
    private bool $isTemperature = false;

    #[ORM\Column(name: 'is_systolic', type: 'boolean')]
    private bool $isSystolic = false;

    #[ORM\Column(name: 'is_diastolic', type: 'boolean')]
    private bool $isDiastolic = false;

    #[ORM\Column(name: 'is_saturation', type: 'boolean')]
    private bool $isSaturation = false;

    #[ORM\Column(name: 'is_respiratory_rate', type: 'boolean')]
    private bool $isRespiratoryRate = false;

    #[ORM\Column(name: 'is_pce', type: 'boolean')]
    private bool $isPce = false;

    #[ORM\Column(name: 'field_type', type: 'string', length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $fieldType = null;

    #[ORM\Column(name: 'exam_type', type: 'string', length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $examType = null;

    #[ORM\ManyToOne(targetEntity: PhysicalExamGrouping::class)]
    #[ORM\JoinColumn(name: 'grouping1_id', nullable: true)]
    private ?PhysicalExamGrouping $grouping1 = null;

    #[ORM\ManyToOne(targetEntity: PhysicalExamGrouping::class)]
    #[ORM\JoinColumn(name: 'grouping2_id', nullable: true)]
    private ?PhysicalExamGrouping $grouping2 = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'id_estado', type: 'integer')]
    private int $idEstado = 1;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
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

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getRangeMin(): ?int
    {
        return $this->rangeMin;
    }

    public function setRangeMin(?int $rangeMin): self
    {
        $this->rangeMin = $rangeMin;
        return $this;
    }

    public function getRangeMax(): ?int
    {
        return $this->rangeMax;
    }

    public function setRangeMax(?int $rangeMax): self
    {
        $this->rangeMax = $rangeMax;
        return $this;
    }

    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(?int $ageMin): self
    {
        $this->ageMin = $ageMin;
        return $this;
    }

    public function getAgeMax(): ?int
    {
        return $this->ageMax;
    }

    public function setAgeMax(?int $ageMax): self
    {
        $this->ageMax = $ageMax;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function isWeight(): bool
    {
        return $this->isWeight;
    }

    public function setIsWeight(bool $isWeight): self
    {
        $this->isWeight = $isWeight;
        return $this;
    }

    public function isHeight(): bool
    {
        return $this->isHeight;
    }

    public function setIsHeight(bool $isHeight): self
    {
        $this->isHeight = $isHeight;
        return $this;
    }

    public function isBmi(): bool
    {
        return $this->isBmi;
    }

    public function setIsBmi(bool $isBmi): self
    {
        $this->isBmi = $isBmi;
        return $this;
    }

    public function isTemperature(): bool
    {
        return $this->isTemperature;
    }

    public function setIsTemperature(bool $isTemperature): self
    {
        $this->isTemperature = $isTemperature;
        return $this;
    }

    public function isSystolic(): bool
    {
        return $this->isSystolic;
    }

    public function setIsSystolic(bool $isSystolic): self
    {
        $this->isSystolic = $isSystolic;
        return $this;
    }

    public function isDiastolic(): bool
    {
        return $this->isDiastolic;
    }

    public function setIsDiastolic(bool $isDiastolic): self
    {
        $this->isDiastolic = $isDiastolic;
        return $this;
    }

    public function isSaturation(): bool
    {
        return $this->isSaturation;
    }

    public function setIsSaturation(bool $isSaturation): self
    {
        $this->isSaturation = $isSaturation;
        return $this;
    }

    public function isRespiratoryRate(): bool
    {
        return $this->isRespiratoryRate;
    }

    public function setIsRespiratoryRate(bool $isRespiratoryRate): self
    {
        $this->isRespiratoryRate = $isRespiratoryRate;
        return $this;
    }

    public function isPce(): bool
    {
        return $this->isPce;
    }

    public function setIsPce(bool $isPce): self
    {
        $this->isPce = $isPce;
        return $this;
    }

    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    public function setFieldType(?string $fieldType): self
    {
        $this->fieldType = $fieldType;
        return $this;
    }

    public function getExamType(): ?string
    {
        return $this->examType;
    }

    public function setExamType(?string $examType): self
    {
        $this->examType = $examType;
        return $this;
    }

    public function getGrouping1(): ?PhysicalExamGrouping
    {
        return $this->grouping1;
    }

    public function setGrouping1(?PhysicalExamGrouping $grouping1): self
    {
        $this->grouping1 = $grouping1;
        return $this;
    }

    public function getGrouping2(): ?PhysicalExamGrouping
    {
        return $this->grouping2;
    }

    public function setGrouping2(?PhysicalExamGrouping $grouping2): self
    {
        $this->grouping2 = $grouping2;
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

    public function getIdEstado(): int
    {
        return $this->idEstado;
    }

    public function setIdEstado(int $idEstado): self
    {
        $this->idEstado = $idEstado;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
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
        return $this->name ?? 'Campo sin nombre';
    }
}
