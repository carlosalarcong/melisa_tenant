<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sexo')]
class Sexo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private ?string $nombre = null;

    #[ORM\Column(type: "string", length: 10, nullable: false)]
    private ?string $codigo = null;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private ?bool $activo = true;

    #[ORM\ManyToOne(targetEntity: Estado::class, inversedBy: 'sexos')]
    #[ORM\JoinColumn(name: 'id_estado', referencedColumnName: 'id')]
    private ?Estado $estado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): static
    {
        $this->codigo = $codigo;
        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): static
    {
        $this->activo = $activo;
        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? 'Sexo sin nombre';
    }
}
