<?php

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sexo')]
class Gender
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 10, nullable: false)]
    private ?string $code = null;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private ?bool $isActive = true;

    #[ORM\Column(name: "id_estado", type: "boolean", options: ["default" => true])]
    private bool|null|Estado $status = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getStatus(): ?Estado
    {
        return $this->status;
    }

    public function setStatus(?Estado $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Gender without name';
    }
}
