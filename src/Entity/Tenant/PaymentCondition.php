<?php

namespace App\Entity\Tenant;

use App\Repository\PaymentConditionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentCondition - Condición de pago (Payment condition/terms)
 * (Migrado desde Legacy CondicionPago)
 */
#[ORM\Entity(repositoryClass: PaymentConditionRepository::class)]
#[ORM\Table(name: 'payment_condition')]
class PaymentCondition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 10)]
    private string $interfaceCode;

    #[ORM\Column(type: 'integer')]
    private int $maxTerm = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isOnDay = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stateId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $organizationId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

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

    // Alias para compatibilidad Legacy
    public function getNombre(): string
    {
        return $this->name;
    }

    public function getInterfaceCode(): string
    {
        return $this->interfaceCode;
    }

    public function setInterfaceCode(string $interfaceCode): self
    {
        $this->interfaceCode = $interfaceCode;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getCodigoInterfaz(): string
    {
        return $this->interfaceCode;
    }

    public function getMaxTerm(): int
    {
        return $this->maxTerm;
    }

    public function setMaxTerm(int $maxTerm): self
    {
        $this->maxTerm = $maxTerm;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getPlazoMaximo(): int
    {
        return $this->maxTerm;
    }

    public function isOnDay(): bool
    {
        return $this->isOnDay;
    }

    public function setIsOnDay(bool $isOnDay): self
    {
        $this->isOnDay = $isOnDay;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getEsAlDia(): bool
    {
        return $this->isOnDay;
    }

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function setStateId(?int $stateId): self
    {
        $this->stateId = $stateId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdEstado()
    {
        return $this->stateId ? (object)['id' => $this->stateId] : null;
    }

    public function getOrganizationId(): ?int
    {
        return $this->organizationId;
    }

    public function setOrganizationId(?int $organizationId): self
    {
        $this->organizationId = $organizationId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdEmpresa()
    {
        return $this->organizationId ? (object)['id' => $this->organizationId] : null;
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
}
