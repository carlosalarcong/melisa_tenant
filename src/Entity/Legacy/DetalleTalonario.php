<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleTalonario
 *
 * @ORM\Table(name="detalle_talonario")
 * @ORM\Entity(repositoryClass="App\Repository\DetalleTalonarioRepository")
 */
class DetalleTalonario
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
     * @var int
     *
     * @ORM\Column(name="NUMERO_DOCUMENTO", type="integer", nullable=false)
     */
    private $numeroDocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $monto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_DETALLE_BOLETA", type="datetime", nullable=false)
     */
    private $fechaDetalleBoleta;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_DESANULACION", type="datetime", nullable=true)
     */
    private $fechaDesanulacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MOTIVO_ANULACION", type="text", length=0, nullable=true)
     */
    private $motivoAnulacion;

    /**
     * @var \Talonario
     *
     * @ORM\ManyToOne(targetEntity="Talonario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TALONARIO", referencedColumnName="ID")
     * })
     */
    private $idTalonario;

    /**
     * @var \Facturacion
     *
     * @ORM\ManyToOne(targetEntity="Facturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FACTURACION", referencedColumnName="ID")
     * })
     */
    private $idFacturacion;

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
     * @var \EstadoDetalleTalonario
     *
     * @ORM\ManyToOne(targetEntity="EstadoDetalleTalonario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_DETALLE_TALONARIO", referencedColumnName="ID")
     * })
     */
    private $idEstadoDetalleTalonario;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_DETALLE_BOLETA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioDetalleBoleta;

    /**
     * @var \Caja
     *
     * @ORM\ManyToOne(targetEntity="Caja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CAJA", referencedColumnName="ID")
     * })
     */
    private $idCaja;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_DESANULACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioDesanulacion;

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
     * Set numeroDocumento.
     *
     * @param int $numeroDocumento
     *
     * @return DetalleTalonario
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento.
     *
     * @return int
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set monto.
     *
     * @param string|null $monto
     *
     * @return DetalleTalonario
     */
    public function setMonto($monto = null)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto.
     *
     * @return string|null
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set fechaDetalleBoleta.
     *
     * @param \DateTime $fechaDetalleBoleta
     *
     * @return DetalleTalonario
     */
    public function setFechaDetalleBoleta($fechaDetalleBoleta)
    {
        $this->fechaDetalleBoleta = $fechaDetalleBoleta;

        return $this;
    }

    /**
     * Get fechaDetalleBoleta.
     *
     * @return \DateTime
     */
    public function getFechaDetalleBoleta()
    {
        return $this->fechaDetalleBoleta;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return DetalleTalonario
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
     * Set fechaDesanulacion.
     *
     * @param \DateTime|null $fechaDesanulacion
     *
     * @return DetalleTalonario
     */
    public function setFechaDesanulacion($fechaDesanulacion = null)
    {
        $this->fechaDesanulacion = $fechaDesanulacion;

        return $this;
    }

    /**
     * Get fechaDesanulacion.
     *
     * @return \DateTime|null
     */
    public function getFechaDesanulacion()
    {
        return $this->fechaDesanulacion;
    }

    /**
     * Set motivoAnulacion.
     *
     * @param string|null $motivoAnulacion
     *
     * @return DetalleTalonario
     */
    public function setMotivoAnulacion($motivoAnulacion = null)
    {
        $this->motivoAnulacion = $motivoAnulacion;

        return $this;
    }

    /**
     * Get motivoAnulacion.
     *
     * @return string|null
     */
    public function getMotivoAnulacion()
    {
        return $this->motivoAnulacion;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente|null $idPaciente
     *
     * @return DetalleTalonario
     */
    public function setIdPaciente(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente $idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idCaja.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja|null $idCaja
     *
     * @return DetalleTalonario
     */
    public function setIdCaja(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja $idCaja = null)
    {
        $this->idCaja = $idCaja;

        return $this;
    }

    /**
     * Get idCaja.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja|null
     */
    public function getIdCaja()
    {
        return $this->idCaja;
    }

    /**
     * Set idEstadoDetalleTalonario.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoDetalleTalonario $idEstadoDetalleTalonario
     *
     * @return DetalleTalonario
     */
    public function setIdEstadoDetalleTalonario(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoDetalleTalonario $idEstadoDetalleTalonario)
    {
        $this->idEstadoDetalleTalonario = $idEstadoDetalleTalonario;

        return $this;
    }

    /**
     * Get idEstadoDetalleTalonario.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoDetalleTalonario
     */
    public function getIdEstadoDetalleTalonario()
    {
        return $this->idEstadoDetalleTalonario;
    }

    /**
     * Set idTalonario.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Talonario $idTalonario
     *
     * @return DetalleTalonario
     */
    public function setIdTalonario(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Talonario $idTalonario)
    {
        $this->idTalonario = $idTalonario;

        return $this;
    }

    /**
     * Get idTalonario.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Talonario
     */
    public function getIdTalonario()
    {
        return $this->idTalonario;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return DetalleTalonario
     */
    public function setIdUsuarioAnulacion(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuarioAnulacion = null)
    {
        $this->idUsuarioAnulacion = $idUsuarioAnulacion;

        return $this;
    }

    /**
     * Get idUsuarioAnulacion.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null
     */
    public function getIdUsuarioAnulacion()
    {
        return $this->idUsuarioAnulacion;
    }

    /**
     * Set idUsuarioDesanulacion.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null $idUsuarioDesanulacion
     *
     * @return DetalleTalonario
     */
    public function setIdUsuarioDesanulacion(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuarioDesanulacion = null)
    {
        $this->idUsuarioDesanulacion = $idUsuarioDesanulacion;

        return $this;
    }

    /**
     * Get idUsuarioDesanulacion.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null
     */
    public function getIdUsuarioDesanulacion()
    {
        return $this->idUsuarioDesanulacion;
    }

    /**
     * Set idUsuarioDetalleBoleta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuarioDetalleBoleta
     *
     * @return DetalleTalonario
     */
    public function setIdUsuarioDetalleBoleta(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuarioDetalleBoleta)
    {
        $this->idUsuarioDetalleBoleta = $idUsuarioDetalleBoleta;

        return $this;
    }

    /**
     * Get idUsuarioDetalleBoleta.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol
     */
    public function getIdUsuarioDetalleBoleta()
    {
        return $this->idUsuarioDetalleBoleta;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PagoCuenta|null $idPagoCuenta
     *
     * @return DetalleTalonario
     */
    public function setIdPagoCuenta(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }


    /**
     * Set idFacturacion.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Facturacion|null $idFacturacion
     *
     * @return DetalleTalonario
     */
    public function setIdFacturacion(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Facturacion $idFacturacion = null)
    {
        $this->idFacturacion = $idFacturacion;

        return $this;
    }

    /**
     * Get idFacturacion.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Facturacion|null
     */
    public function getIdFacturacion()
    {
        return $this->idFacturacion;
    }
}
