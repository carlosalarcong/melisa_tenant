<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;
use Hakam\MultiTenancyBundle\Model\TenantEntityInterface;

/**
 * Role entity - User roles in the system
 * 
 * Migrated from: RolProfesional (melisa_prod)
 * Table: role
 */
#[ORM\Entity]
#[ORM\Table(name: 'role')]
class Role implements TenantEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $webDescription = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $overbookingLimit = null;

    /**
     * Indicates if this role is for clinical professionals (doctors, nurses, etc.)
     * false = Administrative user
     * true = Clinical professional
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isClinicalProfessional = false;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: true)]
    private ?State $state = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
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

    public function getWebDescription(): ?string
    {
        return $this->webDescription;
    }

    public function setWebDescription(?string $webDescription): self
    {
        $this->webDescription = $webDescription;
        return $this;
    }

    public function getOverbookingLimit(): ?int
    {
        return $this->overbookingLimit;
    }

    public function setOverbookingLimit(?int $overbookingLimit): self
    {
        $this->overbookingLimit = $overbookingLimit;
        return $this;
    }

    public function getIsClinicalProfessional(): bool
    {
        return $this->isClinicalProfessional;
    }

    public function setIsClinicalProfessional(bool $isClinicalProfessional): self
    {
        $this->isClinicalProfessional = $isClinicalProfessional;
        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Check if role is active
     */
    public function isActive(): bool
    {
        return $this->state && $this->state->isActive();
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
