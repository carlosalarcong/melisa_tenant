<?php

namespace App\Entity\Tenant;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\PaisRepository::class)]
#[ORM\Table(name: 'pais')]
class Pais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "nombre_pais", type: "string", length: 255, nullable: true)]
    private ?string $nombrePais = null;

    #[ORM\Column(name: "nombre_gentilicio", type: "string", length: 255, nullable: false)]
    private ?string $nombreGentilicio = null;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private ?bool $activo = true;

    #[ORM\Column(name: "id_estado", type: "boolean", options: ["default" => true])]
    private bool|null|Estado $estado = true;

    public function __construct()
    {
        $this->regiones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getNombrePais(): ?string
    {
        return $this->nombrePais;
    }

    public function setNombrePais(?string $nombrePais): static
    {
        $this->nombrePais = $nombrePais;
        return $this;
    }

    public function getNombreGentilicio(): ?string
    {
        return $this->nombreGentilicio;
    }

    public function setNombreGentilicio(?string $nombreGentilicio): static
    {
        $this->nombreGentilicio = $nombreGentilicio;
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
        return $this->nombrePais ?? 'País sin nombre';
    }
}
