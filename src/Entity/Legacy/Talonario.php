<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Talonario
 *
 * @ORM\Table(name="talonario")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TalonarioRepository")
 */
class Talonario
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
     * @var int|null
     *
     * @ORM\Column(name="NUMERO_PILA", type="integer", nullable=true)
     */
    private $numeroPila;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ESTADO_PILA", type="datetime", nullable=true)
     */
    private $fechaEstadoPila;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ENTREGA", type="datetime", nullable=true)
     */
    private $fechaEntrega;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_INICIO", type="string", length=12, nullable=true)
     */
    private $numeroInicio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_TERMINO", type="string", length=12, nullable=true)
     */
    private $numeroTermino;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_ACTUAL", type="string", length=12, nullable=true)
     */
    private $numeroActual;

    /**
     * @var \UbicacionCaja
     *
     * @ORM\ManyToOne(targetEntity="UbicacionCaja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UBICACION_CAJA", referencedColumnName="ID")
     * })
     */
    private $idUbicacionCaja;

    /**
     * @var \EstadoPila
     *
     * @ORM\ManyToOne(targetEntity="EstadoPila")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_PILA", referencedColumnName="ID")
     * })
     */
    private $idEstadoPila;

    /**
     * @var \RelEmpresaTipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="RelEmpresaTipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REL_EMPRESA_TIPO_DOCUMENTO", referencedColumnName="ID")
     * })
     */
    private $idRelEmpresaTipoDocumento;

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
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresa;



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
     * Set numeroPila.
     *
     * @param int|null $numeroPila
     *
     * @return Talonario
     */
    public function setNumeroPila($numeroPila = null)
    {
        $this->numeroPila = $numeroPila;

        return $this;
    }

    /**
     * Get numeroPila.
     *
     * @return int|null
     */
    public function getNumeroPila()
    {
        return $this->numeroPila;
    }

    /**
     * Set fechaEstadoPila.
     *
     * @param \DateTime|null $fechaEstadoPila
     *
     * @return Talonario
     */
    public function setFechaEstadoPila($fechaEstadoPila = null)
    {
        $this->fechaEstadoPila = $fechaEstadoPila;

        return $this;
    }

    /**
     * Get fechaEstadoPila.
     *
     * @return \DateTime|null
     */
    public function getFechaEstadoPila()
    {
        return $this->fechaEstadoPila;
    }

    /**
     * Set fechaEntrega.
     *
     * @param \DateTime|null $fechaEntrega
     *
     * @return Talonario
     */
    public function setFechaEntrega($fechaEntrega = null)
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega.
     *
     * @return \DateTime|null
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set numeroInicio.
     *
     * @param string|null $numeroInicio
     *
     * @return Talonario
     */
    public function setNumeroInicio($numeroInicio = null)
    {
        $this->numeroInicio = $numeroInicio;

        return $this;
    }

    /**
     * Get numeroInicio.
     *
     * @return string|null
     */
    public function getNumeroInicio()
    {
        return $this->numeroInicio;
    }

    /**
     * Set numeroTermino.
     *
     * @param string|null $numeroTermino
     *
     * @return Talonario
     */
    public function setNumeroTermino($numeroTermino = null)
    {
        $this->numeroTermino = $numeroTermino;

        return $this;
    }

    /**
     * Get numeroTermino.
     *
     * @return string|null
     */
    public function getNumeroTermino()
    {
        return $this->numeroTermino;
    }

    /**
     * Set numeroActual.
     *
     * @param string|null $numeroActual
     *
     * @return Talonario
     */
    public function setNumeroActual($numeroActual = null)
    {
        $this->numeroActual = $numeroActual;

        return $this;
    }

    /**
     * Get numeroActual.
     *
     * @return string|null
     */
    public function getNumeroActual()
    {
        return $this->numeroActual;
    }

    /**
     * Set idRelEmpresaTipoDocumento.
     *
     * @param \App\Entity\Legacy\Legacy\RelEmpresaTipoDocumento|null $idRelEmpresaTipoDocumento
     *
     * @return Talonario
     */
    public function setIdRelEmpresaTipoDocumento(\App\Entity\Legacy\Legacy\RelEmpresaTipoDocumento $idRelEmpresaTipoDocumento = null)
    {
        $this->idRelEmpresaTipoDocumento = $idRelEmpresaTipoDocumento;

        return $this;
    }

    /**
     * Get idRelEmpresaTipoDocumento.
     *
     * @return \App\Entity\Legacy\Legacy\RelEmpresaTipoDocumento|null
     */
    public function getIdRelEmpresaTipoDocumento()
    {
        return $this->idRelEmpresaTipoDocumento;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Legacy\Legacy\Estado|null $idEstado
     *
     * @return Talonario
     */
    public function setIdEstado(\App\Entity\Legacy\Legacy\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Legacy\Legacy\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idSucursal.
     *
     * @param \App\Entity\Legacy\Legacy\Sucursal|null $idSucursal
     *
     * @return Talonario
     */
    public function setIdSucursal(\App\Entity\Legacy\Legacy\Sucursal $idSucursal = null)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \App\Entity\Legacy\Legacy\Sucursal|null
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idEstadoPila.
     *
     * @param \App\Entity\Legacy\Legacy\EstadoPila|null $idEstadoPila
     *
     * @return Talonario
     */
    public function setIdEstadoPila(\App\Entity\Legacy\Legacy\EstadoPila $idEstadoPila = null)
    {
        $this->idEstadoPila = $idEstadoPila;

        return $this;
    }

    /**
     * Get idEstadoPila.
     *
     * @return \App\Entity\Legacy\Legacy\EstadoPila|null
     */
    public function getIdEstadoPila()
    {
        return $this->idEstadoPila;
    }

    /**
     * Set idSubEmpresa.
     *
     * @param \App\Entity\Legacy\Legacy\SubEmpresa|null $idSubEmpresa
     *
     * @return Talonario
     */
    public function setIdSubEmpresa(\App\Entity\Legacy\Legacy\SubEmpresa $idSubEmpresa = null)
    {
        $this->idSubEmpresa = $idSubEmpresa;

        return $this;
    }

    /**
     * Get idSubEmpresa.
     *
     * @return \App\Entity\Legacy\Legacy\SubEmpresa|null
     */
    public function getIdSubEmpresa()
    {
        return $this->idSubEmpresa;
    }

    /**
     * Set idUbicacionCaja.
     *
     * @param \App\Entity\Legacy\Legacy\UbicacionCaja|null $idUbicacionCaja
     *
     * @return Talonario
     */
    public function setIdUbicacionCaja(\App\Entity\Legacy\Legacy\UbicacionCaja $idUbicacionCaja = null)
    {
        $this->idUbicacionCaja = $idUbicacionCaja;

        return $this;
    }

    /**
     * Get idUbicacionCaja.
     *
     * @return \App\Entity\Legacy\Legacy\UbicacionCaja|null
     */
    public function getIdUbicacionCaja()
    {
        return $this->idUbicacionCaja;
    }
}
