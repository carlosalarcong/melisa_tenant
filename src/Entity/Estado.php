<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'estado')]
class Estado
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 45, nullable: false)]
    private ?string $nombreEstado = null;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private ?bool $activo = true;

    #[ORM\OneToMany(mappedBy: 'estado', targetEntity: Pais::class)]
    private Collection $paises;

    #[ORM\OneToMany(mappedBy: 'estado', targetEntity: Region::class)]
    private Collection $regiones;

    #[ORM\OneToMany(mappedBy: 'estado', targetEntity: Religion::class)]
    private Collection $religiones;

    #[ORM\OneToMany(mappedBy: 'estado', targetEntity: Sexo::class)]
    private Collection $sexos;

    public function __construct()
    {
        $this->paises = new ArrayCollection();
        $this->regiones = new ArrayCollection();
        $this->religiones = new ArrayCollection();
        $this->sexos = new ArrayCollection();
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

    public function getNombreEstado(): ?string
    {
        return $this->nombreEstado;
    }

    public function setNombreEstado(string $nombreEstado): static
    {
        $this->nombreEstado = $nombreEstado;
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
     * @return Collection<int, Pais>
     */
    public function getPaises(): Collection
    {
        return $this->paises;
    }

    public function addPais(Pais $pais): static
    {
        if (!$this->paises->contains($pais)) {
            $this->paises->add($pais);
            $pais->setEstado($this);
        }

        return $this;
    }

    public function removePais(Pais $pais): static
    {
        if ($this->paises->removeElement($pais)) {
            if ($pais->getEstado() === $this) {
                $pais->setEstado(null);
            }
        }

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
            $region->setEstado($this);
        }

        return $this;
    }

    public function removeRegion(Region $region): static
    {
        if ($this->regiones->removeElement($region)) {
            if ($region->getEstado() === $this) {
                $region->setEstado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Religion>
     */
    public function getReligiones(): Collection
    {
        return $this->religiones;
    }

    public function addReligion(Religion $religion): static
    {
        if (!$this->religiones->contains($religion)) {
            $this->religiones->add($religion);
            $religion->setEstado($this);
        }

        return $this;
    }

    public function removeReligion(Religion $religion): static
    {
        if ($this->religiones->removeElement($religion)) {
            if ($religion->getEstado() === $this) {
                $religion->setEstado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sexo>
     */
    public function getSexos(): Collection
    {
        return $this->sexos;
    }

    public function addSexo(Sexo $sexo): static
    {
        if (!$this->sexos->contains($sexo)) {
            $this->sexos->add($sexo);
            $sexo->setEstado($this);
        }

        return $this;
    }

    public function removeSexo(Sexo $sexo): static
    {
        if ($this->sexos->removeElement($sexo)) {
            if ($sexo->getEstado() === $this) {
                $sexo->setEstado(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombreEstado ?? 'Estado sin nombre';
    }
}
