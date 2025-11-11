<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Esta es una entity STUB requerida por hakam/multi-tenancy-bundle
 * 
 * NO SE USA en nuestra aplicación. Nuestra lógica de gestión de tenants está en:
 * - TenantResolver: Lee la tabla 'tenant' de melisa_central
 * - TenantContext: Mantiene el tenant activo
 * 
 * Solo existe para satisfacer la dependencia del bundle.
 */
#[ORM\Entity]
class TenantDb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $databaseName = null;

    #[ORM\Column(length: 50)]
    private ?string $databaseStatus = 'migrated';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatabaseName(): ?string
    {
        return $this->databaseName;
    }

    public function setDatabaseName(string $databaseName): self
    {
        $this->databaseName = $databaseName;
        return $this;
    }

    public function getDatabaseStatus(): ?string
    {
        return $this->databaseStatus;
    }

    public function setDatabaseStatus(string $databaseStatus): self
    {
        $this->databaseStatus = $databaseStatus;
        return $this;
    }
}
