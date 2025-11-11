<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hakam\MultiTenancyBundle\Services\TenantDbConfigurationInterface;
use Hakam\MultiTenancyBundle\Enum\DatabaseStatusEnum;
use Hakam\MultiTenancyBundle\Enum\DriverTypeEnum;

/**
 * Entity para tenant_db que implementa la interface requerida por el bundle
 */
#[ORM\Entity]
#[ORM\Table(name: 'tenant_db')]
class TenantDb implements TenantDbConfigurationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: 'database_name')]
    private ?string $databaseName = null;

    #[ORM\Column(length: 50, name: 'database_status')]
    private ?string $databaseStatus = 'DATABASE_MIGRATED';

    // Métodos de la interface TenantDbConfigurationInterface
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifierValue(): mixed
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

    public function getDatabaseStatus(): DatabaseStatusEnum
    {
        return DatabaseStatusEnum::from($this->databaseStatus ?? 'DATABASE_MIGRATED');
    }

    public function setDatabaseStatus(DatabaseStatusEnum $databaseStatus): self
    {
        $this->databaseStatus = $databaseStatus->value;
        return $this;
    }

    // Métodos adicionales requeridos por la interface
    
    public function getDbName(): string
    {
        return $this->databaseName ?? '';
    }

    public function getDbUsername(): string
    {
        return 'melisa';
    }

    public function getDbPassword(): string
    {
        return 'melisamelisa';
    }

    public function getDbHost(): string
    {
        return 'localhost';
    }

    public function getDbPort(): int
    {
        return 3306;
    }

    public function getDsnUrl(): string
    {
        // Construir DSN en formato: mysql://user:pass@host:port/dbname
        return sprintf(
            'mysql://%s:%s@%s:%d/%s',
            $this->getDbUsername(),
            $this->getDbPassword(),
            $this->getDbHost(),
            $this->getDbPort(),
            $this->getDbName()
        );
    }

    public function getDriverType(): DriverTypeEnum
    {
        return DriverTypeEnum::MYSQL;
    }
}
