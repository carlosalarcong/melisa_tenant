<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UbicacionCaja
 *
 * @ORM\Table(name="ubicacion_caja")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\UbicacionCajaRepository")
 */
class UbicacionCaja
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=50, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRIPCION", type="text", length=0, nullable=true)
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
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;



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
     * @return UbicacionCaja
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
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return UbicacionCaja
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
     * Set idSucursal.
     *
     * @param \App\Entity\Sucursal|null $idSucursal
     *
     * @return UbicacionCaja
     */
    public function setIdSucursal(\App\Entity\Sucursal $idSucursal = null)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \App\Entity\Sucursal|null
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Estado|null $idEstado
     *
     * @return UbicacionCaja
     */
    public function setIdEstado(\App\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
