<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;
use Hakam\MultiTenancyBundle\Model\TenantEntityInterface;

/**
 * License - Organization license information
 * 
 * Stores the number of user licenses allocated to each organization/tenant.
 * Used for license quota validation when creating or activating users.
 */
#[ORM\Entity]
#[ORM\Table(name: 'license')]
class License implements TenantEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', nullable: false)]
    private Organization $organization;

    #[ORM\Column(name: 'quantity', type: 'integer')]
    private int $quantity;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return sprintf('License #%d: %d licenses for %s', 
            $this->id, 
            $this->quantity, 
            $this->organization->getName() ?? 'N/A'
        );
    }
}
