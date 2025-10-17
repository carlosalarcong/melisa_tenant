<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'region')]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $codigoRegion = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $nombreRegion = null;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $addressStateHl7 = null;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private ?bool $activo = true;

    #[ORM\ManyToOne(targetEntity: Pais::class, inversedBy: 'regiones')]
    #[ORM\JoinColumn(name: 'id_pais', referencedColumnName: 'id')]
    private ?Pais $pais = null;

    #[ORM\ManyToOne(targetEntity: Estado::class, inversedBy: 'regiones')]
    #[ORM\JoinColumn(name: 'id_estado', referencedColumnName: 'id')]
    private ?Estado $estado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getCodigoRegion(): ?int
    {
        return $this->codigoRegion;
    }

    public function setCodigoRegion(?int $codigoRegion): static
    {
        $this->codigoRegion = $codigoRegion;
        return $this;
    }

    public function getNombreRegion(): ?string
    {
        return $this->nombreRegion;
    }

    public function setNombreRegion(?string $nombreRegion): static
    {
        $this->nombreRegion = $nombreRegion;
        return $this;
    }

    public function getAddressStateHl7(): ?string
    {
        return $this->addressStateHl7;
    }

    public function setAddressStateHl7(?string $addressStateHl7): static
    {
        $this->addressStateHl7 = $addressStateHl7;
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

    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    public function setPais(?Pais $pais): static
    {
        $this->pais = $pais;
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
        return $this->nombreRegion ?? 'Región sin nombre';
    }
}
