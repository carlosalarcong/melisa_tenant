<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pais')]
class Pais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $nombrePais = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $nombreGentilicio = null;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private ?bool $activo = true;

    #[ORM\OneToMany(mappedBy: 'pais', targetEntity: Region::class)]
    private Collection $regiones;

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

    /**
     * @return Collection<int, Region>
     */
    public function getRegiones(): Collection
    {
        return $this->regiones;
    }

    public function addRegion(Region $region): static
    {
        if (!$this->regiones->contains($region)) {
            $this->regiones->add($region);
            $region->setPais($this);
        }

        return $this;
    }

    public function removeRegion(Region $region): static
    {
        if ($this->regiones->removeElement($region)) {
            // set the owning side to null (unless already changed)
            if ($region->getPais() === $this) {
                $region->setPais(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombrePais ?? 'País sin nombre';
    }
}
