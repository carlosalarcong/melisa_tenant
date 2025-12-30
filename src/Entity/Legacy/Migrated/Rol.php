<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rol
 *
 * @ORM\Table(name="rol")
 * @ORM\Entity
 */
class Rol
{
    const MEDICO = 1;
    const ADMINISTRATIVO = 2;
    const ENFERMERA = 3;
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=20, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="ABREVIACION", type="string", length=20, nullable=false)
     */
    private $abreviacion;

    /**
     * @var string
     *
     * @ORM\Column(name="COLOR", type="string", length=45, nullable=false)
     */
    private $color;

    /**
     * @var int
     *
     * @ORM\Column(name="PROF_CLINICO", type="integer", nullable=false)
     */
    private $profClinico;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Rol
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Rol
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set abreviacion.
     *
     * @param string $abreviacion
     *
     * @return Rol
     */
    public function setAbreviacion($abreviacion)
    {
        $this->abreviacion = $abreviacion;

        return $this;
    }

    /**
     * Get abreviacion.
     *
     * @return string
     */
    public function getAbreviacion()
    {
        return $this->abreviacion;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Rol
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set profClinico.
     *
     * @param int $profClinico
     *
     * @return Rol
     */
    public function setProfClinico($profClinico)
    {
        $this->profClinico = $profClinico;

        return $this;
    }

    /**
     * Get profClinico.
     *
     * @return int
     */
    public function getProfClinico()
    {
        return $this->profClinico;
    }
}
