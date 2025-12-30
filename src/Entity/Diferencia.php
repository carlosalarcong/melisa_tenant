<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diferencia
 *
 * @ORM\Table(name="diferencia")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DiferenciaRepository")
 */
class Diferencia
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_SOLICITUD", type="datetime", nullable=false)
     */
    private $fechaSolicitud;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_AUTORIZACION", type="datetime", nullable=true)
     */
    private $fechaAutorizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="TOTAL_CUENTA", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $totalCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="TOTAL_DESCUENTO", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $totalDescuento;

    /**
     * @var string
     *
     * @ORM\Column(name="TOTAL_CUENTA_CON_DESCUENTO", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $totalCuentaConDescuento;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAnulacion;

    /**
     * @var \Paciente
     *
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPaciente;

    /**
     * @var \EstadoDiferencia
     *
     * @ORM\ManyToOne(targetEntity="EstadoDiferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_DIFERENCIA", referencedColumnName="ID")
     * })
     */
    private $idEstadoDiferencia;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SOLICITUD", referencedColumnName="ID")
     * })
     */
    private $idUsuarioSolicitud;

    /**
     * @var \MotivoDiferencia
     *
     * @ORM\ManyToOne(targetEntity="MotivoDiferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MOTIVO_DIFERENCIA", referencedColumnName="ID")
     * })
     */
    private $idMotivoDiferencia;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_AUTORIZACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAutorizacion;

    /**
     * @var \PagoCuenta
     *
     * @ORM\ManyToOne(targetEntity="PagoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAGO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idPagoCuenta;

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
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return Diferencia
     */
    public function setFechaAnulacion($fechaAnulacion = null)
    {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAnulacion()
    {
        return $this->fechaAnulacion;
    }

    /**
     * Set fechaSolicitud.
     *
     * @param \DateTime $fechaSolicitud
     *
     * @return Diferencia
     */
    public function setFechaSolicitud($fechaSolicitud)
    {
        $this->fechaSolicitud = $fechaSolicitud;

        return $this;
    }

    /**
     * Get fechaSolicitud.
     *
     * @return \DateTime
     */
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }

    /**
     * Set fechaAutorizacion.
     *
     * @param \DateTime|null $fechaAutorizacion
     *
     * @return Diferencia
     */
    public function setFechaAutorizacion($fechaAutorizacion = null)
    {
        $this->fechaAutorizacion = $fechaAutorizacion;

        return $this;
    }

    /**
     * Get fechaAutorizacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAutorizacion()
    {
        return $this->fechaAutorizacion;
    }

    /**
     * Set totalCuenta.
     *
     * @param string $totalCuenta
     *
     * @return Diferencia
     */
    public function setTotalCuenta($totalCuenta)
    {
        $this->totalCuenta = $totalCuenta;

        return $this;
    }

    /**
     * Get totalCuenta.
     *
     * @return string
     */
    public function getTotalCuenta()
    {
        return $this->totalCuenta;
    }

    /**
     * Set totalDescuento.
     *
     * @param string $totalDescuento
     *
     * @return Diferencia
     */
    public function setTotalDescuento($totalDescuento)
    {
        $this->totalDescuento = $totalDescuento;

        return $this;
    }

    /**
     * Get totalDescuento.
     *
     * @return string
     */
    public function getTotalDescuento()
    {
        return $this->totalDescuento;
    }

    /**
     * Set totalCuentaConDescuento.
     *
     * @param string $totalCuentaConDescuento
     *
     * @return Diferencia
     */
    public function setTotalCuentaConDescuento($totalCuentaConDescuento)
    {
        $this->totalCuentaConDescuento = $totalCuentaConDescuento;

        return $this;
    }

    /**
     * Get totalCuentaConDescuento.
     *
     * @return string
     */
    public function getTotalCuentaConDescuento()
    {
        return $this->totalCuentaConDescuento;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Paciente|null $idPaciente
     *
     * @return Diferencia
     */
    public function setIdPaciente(\App\Entity\Paciente $idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Paciente|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return Diferencia
     */
    public function setIdUsuarioAnulacion(\App\Entity\UsuariosRebsol $idUsuarioAnulacion = null)
    {
        $this->idUsuarioAnulacion = $idUsuarioAnulacion;

        return $this;
    }

    /**
     * Get idUsuarioAnulacion.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAnulacion()
    {
        return $this->idUsuarioAnulacion;
    }

    /**
     * Set idUsuarioSolicitud.
     *
     * @param \App\Entity\UsuariosRebsol $idUsuarioSolicitud
     *
     * @return Diferencia
     */
    public function setIdUsuarioSolicitud(\App\Entity\UsuariosRebsol $idUsuarioSolicitud)
    {
        $this->idUsuarioSolicitud = $idUsuarioSolicitud;

        return $this;
    }

    /**
     * Get idUsuarioSolicitud.
     *
     * @return \App\Entity\UsuariosRebsol
     */
    public function getIdUsuarioSolicitud()
    {
        return $this->idUsuarioSolicitud;
    }

    /**
     * Set idUsuarioAutorizacion.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioAutorizacion
     *
     * @return Diferencia
     */
    public function setIdUsuarioAutorizacion(\App\Entity\UsuariosRebsol $idUsuarioAutorizacion = null)
    {
        $this->idUsuarioAutorizacion = $idUsuarioAutorizacion;

        return $this;
    }

    /**
     * Get idUsuarioAutorizacion.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAutorizacion()
    {
        return $this->idUsuarioAutorizacion;
    }

    /**
     * Set idEstadoDiferencia.
     *
     * @param \App\Entity\EstadoDiferencia $idEstadoDiferencia
     *
     * @return Diferencia
     */
    public function setIdEstadoDiferencia(\App\Entity\EstadoDiferencia $idEstadoDiferencia)
    {
        $this->idEstadoDiferencia = $idEstadoDiferencia;

        return $this;
    }

    /**
     * Get idEstadoDiferencia.
     *
     * @return \App\Entity\EstadoDiferencia
     */
    public function getIdEstadoDiferencia()
    {
        return $this->idEstadoDiferencia;
    }

    /**
     * Set idMotivoDiferencia.
     *
     * @param \App\Entity\MotivoDiferencia $idMotivoDiferencia
     *
     * @return Diferencia
     */
    public function setIdMotivoDiferencia(\App\Entity\MotivoDiferencia $idMotivoDiferencia)
    {
        $this->idMotivoDiferencia = $idMotivoDiferencia;

        return $this;
    }

    /**
     * Get idMotivoDiferencia.
     *
     * @return \App\Entity\MotivoDiferencia
     */
    public function getIdMotivoDiferencia()
    {
        return $this->idMotivoDiferencia;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \App\Entity\PagoCuenta|null $idPagoCuenta
     *
     * @return Diferencia
     */
    public function setIdPagoCuenta(\App\Entity\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \App\Entity\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

}
