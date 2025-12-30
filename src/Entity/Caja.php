<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Caja
 *
 * @ORM\Table(name="caja")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\CajaRepository")
 */
class Caja
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
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_APERTURA", type="datetime", nullable=false)
     */
    private $fechaApertura;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CIERRE", type="datetime", nullable=true)
     */
    private $fechaCierre;

    /**
     * @var int
     *
     * @ORM\Column(name="MONTO_INICIAL", type="integer", nullable=false)
     */
    private $montoInicial;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TURNO", type="string", length=20, nullable=true)
     */
    private $turno;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_ESPERADO", type="integer", nullable=true)
     */
    private $montoEsperado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_REAL", type="integer", nullable=true)
     */
    private $montoReal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_REAPERTURA", type="datetime", nullable=true)
     */
    private $fechaReapertura;

    /**
     * @var int|null
     *
     * @ORM\Column(name="SUPERAVIT", type="integer", nullable=true)
     */
    private $superavit;

    /**
     * @var int|null
     *
     * @ORM\Column(name="DEFICIT", type="integer", nullable=true)
     */
    private $deficit;

    /**
     * @var \EstadoReapertura
     *
     * @ORM\ManyToOne(targetEntity="EstadoReapertura")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_REAPERTURA", referencedColumnName="ID")
     * })
     */
    private $idEstadoReapertura;

    /**
     * @var \RelUbicacionCajero
     *
     * @ORM\ManyToOne(targetEntity="RelUbicacionCajero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UBICACION_CAJERO", referencedColumnName="ID")
     * })
     */
    private $idUbicacionCajero;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_REAPERTURA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioReapertura;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="ID")
     * })
     */
    private $idUsuario;



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
     * Set fechaApertura.
     *
     * @param \DateTime $fechaApertura
     *
     * @return Caja
     */
    public function setFechaApertura($fechaApertura)
    {
        $this->fechaApertura = $fechaApertura;

        return $this;
    }

    /**
     * Get fechaApertura.
     *
     * @return \DateTime
     */
    public function getFechaApertura()
    {
        return $this->fechaApertura;
    }

    /**
     * Set fechaCierre.
     *
     * @param \DateTime|null $fechaCierre
     *
     * @return Caja
     */
    public function setFechaCierre($fechaCierre = null)
    {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre.
     *
     * @return \DateTime|null
     */
    public function getFechaCierre()
    {
        return $this->fechaCierre;
    }

    /**
     * Set montoInicial.
     *
     * @param int $montoInicial
     *
     * @return Caja
     */
    public function setMontoInicial($montoInicial)
    {
        $this->montoInicial = $montoInicial;

        return $this;
    }

    /**
     * Get montoInicial.
     *
     * @return int
     */
    public function getMontoInicial()
    {
        return $this->montoInicial;
    }

    /**
     * Set turno.
     *
     * @param string|null $turno
     *
     * @return Caja
     */
    public function setTurno($turno = null)
    {
        $this->turno = $turno;

        return $this;
    }

    /**
     * Get turno.
     *
     * @return string|null
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * Set montoEsperado.
     *
     * @param int|null $montoEsperado
     *
     * @return Caja
     */
    public function setMontoEsperado($montoEsperado = null)
    {
        $this->montoEsperado = $montoEsperado;

        return $this;
    }

    /**
     * Get montoEsperado.
     *
     * @return int|null
     */
    public function getMontoEsperado()
    {
        return $this->montoEsperado;
    }

    /**
     * Set montoReal.
     *
     * @param int|null $montoReal
     *
     * @return Caja
     */
    public function setMontoReal($montoReal = null)
    {
        $this->montoReal = $montoReal;

        return $this;
    }

    /**
     * Get montoReal.
     *
     * @return int|null
     */
    public function getMontoReal()
    {
        return $this->montoReal;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return Caja
     */
    public function setObservacion($observacion = null)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion.
     *
     * @return string|null
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set fechaReapertura.
     *
     * @param \DateTime|null $fechaReapertura
     *
     * @return Caja
     */
    public function setFechaReapertura($fechaReapertura = null)
    {
        $this->fechaReapertura = $fechaReapertura;

        return $this;
    }

    /**
     * Get fechaReapertura.
     *
     * @return \DateTime|null
     */
    public function getFechaReapertura()
    {
        return $this->fechaReapertura;
    }

    /**
     * Set superavit.
     *
     * @param int|null $superavit
     *
     * @return Caja
     */
    public function setSuperavit($superavit = null)
    {
        $this->superavit = $superavit;

        return $this;
    }

    /**
     * Get superavit.
     *
     * @return int|null
     */
    public function getSuperavit()
    {
        return $this->superavit;
    }

    /**
     * Set deficit.
     *
     * @param int|null $deficit
     *
     * @return Caja
     */
    public function setDeficit($deficit = null)
    {
        $this->deficit = $deficit;

        return $this;
    }

    /**
     * Get deficit.
     *
     * @return int|null
     */
    public function getDeficit()
    {
        return $this->deficit;
    }

    /**
     * Set idSucursal.
     *
     * @param \App\Entity\Sucursal $idSucursal
     *
     * @return Caja
     */
    public function setIdSucursal(\App\Entity\Sucursal $idSucursal)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \App\Entity\Sucursal
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idEstadoReapertura.
     *
     * @param \App\Entity\EstadoReapertura|null $idEstadoReapertura
     *
     * @return Caja
     */
    public function setIdEstadoReapertura(\App\Entity\EstadoReapertura $idEstadoReapertura = null)
    {
        $this->idEstadoReapertura = $idEstadoReapertura;

        return $this;
    }

    /**
     * Get idEstadoReapertura.
     *
     * @return \App\Entity\EstadoReapertura|null
     */
    public function getIdEstadoReapertura()
    {
        return $this->idEstadoReapertura;
    }

    /**
     * Set idUsuario.
     *
     * @param \App\Entity\UsuariosRebsol $idUsuario
     *
     * @return Caja
     */
    public function setIdUsuario(\App\Entity\UsuariosRebsol $idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return \App\Entity\UsuariosRebsol
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idUsuarioReapertura.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioReapertura
     *
     * @return Caja
     */
    public function setIdUsuarioReapertura(\App\Entity\UsuariosRebsol $idUsuarioReapertura = null)
    {
        $this->idUsuarioReapertura = $idUsuarioReapertura;

        return $this;
    }

    /**
     * Get idUsuarioReapertura.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioReapertura()
    {
        return $this->idUsuarioReapertura;
    }

    /**
     * Set idUbicacionCajero.
     *
     * @param \App\Entity\RelUbicacionCajero $idUbicacionCajero
     *
     * @return Caja
     */
    public function setIdUbicacionCajero(\App\Entity\RelUbicacionCajero $idUbicacionCajero)
    {
        $this->idUbicacionCajero = $idUbicacionCajero;

        return $this;
    }

    /**
     * Get idUbicacionCajero.
     *
     * @return \App\Entity\RelUbicacionCajero
     */
    public function getIdUbicacionCajero()
    {
        return $this->idUbicacionCajero;
    }
}
