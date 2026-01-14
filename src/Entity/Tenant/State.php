<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;
use Hakam\MultiTenancyBundle\Model\TenantEntityInterface;

/**
 * State (Estado) - Generic state entity for various entities
 * 
 * Used to represent states like: ACTIVE, INACTIVE, BLOCKED, etc.
 * This is a reference table with predefined values.
 */
#[ORM\Entity]
#[ORM\Table(name: 'state')]
class State implements TenantEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 45)]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Check if state is active
     */
    public function isActive(): bool
    {
        return strtoupper($this->name) === 'ACTIVO' || 
               strtoupper($this->name) === 'ACTIVE';
    }

    /**
     * Check if state is inactive
     */
    public function isInactive(): bool
    {
        return strtoupper($this->name) === 'INACTIVO' || 
               strtoupper($this->name) === 'INACTIVE';
    }

    /**
     * Check if state is blocked
     */
    public function isBlocked(): bool
    {
        return strtoupper($this->name) === 'BLOQUEADO' || 
               strtoupper($this->name) === 'BLOCKED';
    }
}
