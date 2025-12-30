<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modulo
 *
 * @ORM\Table(name="modulo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ModuloRepository")
 */
class Modulo
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE", type="string", length=100, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ABREVIADO", type="string", length=100, nullable=true)
     */
    private $nombreAbreviado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IMAGEN", type="string", length=100, nullable=true)
     */
    private $imagen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RUTA", type="string", length=100, nullable=true)
     */
    private $ruta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="BASE_PERFIL", type="string", length=100, nullable=true)
     */
    private $basePerfil;

    /**
     * @var int
     *
     * @ORM\Column(name="ORDEN", type="integer", nullable=false)
     */
    private $orden;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRIPCION", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Modulo
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
     * @param string|null $nombre
     *
     * @return Modulo
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set nombreAbreviado.
     *
     * @param string|null $nombreAbreviado
     *
     * @return Modulo
     */
    public function setNombreAbreviado($nombreAbreviado = null)
    {
        $this->nombreAbreviado = $nombreAbreviado;

        return $this;
    }

    /**
     * Get nombreAbreviado.
     *
     * @return string|null
     */
    public function getNombreAbreviado()
    {
        return $this->nombreAbreviado;
    }

    /**
     * Set imagen.
     *
     * @param string|null $imagen
     *
     * @return Modulo
     */
    public function setImagen($imagen = null)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen.
     *
     * @return string|null
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set ruta.
     *
     * @param string|null $ruta
     *
     * @return Modulo
     */
    public function setRuta($ruta = null)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta.
     *
     * @return string|null
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set basePerfil.
     *
     * @param string|null $basePerfil
     *
     * @return Modulo
     */
    public function setBasePerfil($basePerfil = null)
    {
        $this->basePerfil = $basePerfil;

        return $this;
    }

    /**
     * Get basePerfil.
     *
     * @return string|null
     */
    public function getBasePerfil()
    {
        return $this->basePerfil;
    }

    /**
     * Set orden.
     *
     * @param int $orden
     *
     * @return Modulo
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return Modulo
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Modulo
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
