<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PagoCuenta
 *
 * @ORM\Table(name="pago_cuenta")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PagoCuentaRepository")
 */
class PagoCuenta
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
     * @ORM\Column(name="FECHA_PAGO", type="datetime", nullable=false)
     */
    private $fechaPago;

    /**
     * @var int|null
     *
     * @ORM\Column(name="NUMERO_DOCUMENTO", type="bigint", nullable=true)
     */
    private $numeroDocumento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="IMPUESTO", type="integer", nullable=true)
     */
    private $impuesto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $monto;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_ESTADO_DOCUMENTO", type="integer", nullable=true)
     */
    private $idEstadoDocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CUOTA", type="string", length=11, nullable=true)
     */
    private $cuota;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_A_PAGO", type="datetime", nullable=true)
     */
    private $fechaAPago;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MOTIVO_ANULACION", type="text", length=0, nullable=true)
     */
    private $motivoAnulacion;

    /**
     * @var \EstadoPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_PAGO", referencedColumnName="ID")
     * })
     */
    private $idEstadoPago;

    /**
     * @var \CuentaPaciente
     *
     * @ORM\ManyToOne(targetEntity="CuentaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CUENTA_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idCuentaPaciente;

    /**
     * @var \PagoWeb
     *
     * @ORM\ManyToOne(targetEntity="PagoWeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAGO_WEB", referencedColumnName="ID")
     * })
     */
    private $idPagoWeb;

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
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresa;

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
     * @var \MotivoDiferencia
     *
     * @ORM\ManyToOne(targetEntity="MotivoDiferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MOTIVO_DIFERENCIA", referencedColumnName="ID")
     * })
     */
    private $idMotivoDiferencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO_DIFERENCIA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montoDiferencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PRECIO_DIFERENCIA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $precioDiferencia;

    /**
     * @var string|null
     *
     *
     * @ORM\Column(name="OBSERVACION_REGULARIZACION", type="text", length=0, nullable=true)
     */
    private $observacionRegularizacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_REGULARIZACION", type="datetime", nullable=true)
     */
    private $fechaRegularizacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_COBRANZA", type="boolean", nullable=false)
     */
    private $esCobranza = '0';

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
     * Set fechaPago.
     *
     * @param \DateTime $fechaPago
     *
     * @return PagoCuenta
     */
    public function setFechaPago($fechaPago)
    {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago.
     *
     * @return \DateTime
     */
    public function getFechaPago()
    {
        return $this->fechaPago;
    }

    /**
     * Set numeroDocumento.
     *
     * @param int|null $numeroDocumento
     *
     * @return PagoCuenta
     */
    public function setNumeroDocumento($numeroDocumento = null)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento.
     *
     * @return int|null
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set impuesto.
     *
     * @param int|null $impuesto
     *
     * @return PagoCuenta
     */
    public function setImpuesto($impuesto = null)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto.
     *
     * @return int|null
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set monto.
     *
     * @param string|null $monto
     *
     * @return PagoCuenta
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
     * Set idEstadoDocumento.
     *
     * @param int|null $idEstadoDocumento
     *
     * @return PagoCuenta
     */
    public function setIdEstadoDocumento($idEstadoDocumento = null)
    {
        $this->idEstadoDocumento = $idEstadoDocumento;

        return $this;
    }

    /**
     * Get idEstadoDocumento.
     *
     * @return int|null
     */
    public function getIdEstadoDocumento()
    {
        return $this->idEstadoDocumento;
    }

    /**
     * Set cuota.
     *
     * @param string|null $cuota
     *
     * @return PagoCuenta
     */
    public function setCuota($cuota = null)
    {
        $this->cuota = $cuota;

        return $this;
    }

    /**
     * Get cuota.
     *
     * @return string|null
     */
    public function getCuota()
    {
        return $this->cuota;
    }

    /**
     * Set fechaAPago.
     *
     * @param \DateTime|null $fechaAPago
     *
     * @return PagoCuenta
     */
    public function setFechaAPago($fechaAPago = null)
    {
        $this->fechaAPago = $fechaAPago;

        return $this;
    }

    /**
     * Get fechaAPago.
     *
     * @return \DateTime|null
     */
    public function getFechaAPago()
    {
        return $this->fechaAPago;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return PagoCuenta
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
     * Set motivoAnulacion.
     *
     * @param string|null $motivoAnulacion
     *
     * @return PagoCuenta
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
     * @param \App\Entity\Paciente|null $idPaciente
     *
     * @return PagoCuenta
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
     * Set idSubEmpresa.
     *
     * @param \App\Entity\SubEmpresa|null $idSubEmpresa
     *
     * @return PagoCuenta
     */
    public function setIdSubEmpresa(\App\Entity\SubEmpresa $idSubEmpresa = null)
    {
        $this->idSubEmpresa = $idSubEmpresa;

        return $this;
    }

    /**
     * Get idSubEmpresa.
     *
     * @return \App\Entity\SubEmpresa|null
     */
    public function getIdSubEmpresa()
    {
        return $this->idSubEmpresa;
    }

    /**
     * Set idEstadoPago.
     *
     * @param \App\Entity\EstadoPago|null $idEstadoPago
     *
     * @return PagoCuenta
     */
    public function setIdEstadoPago(\App\Entity\EstadoPago $idEstadoPago = null)
    {
        $this->idEstadoPago = $idEstadoPago;

        return $this;
    }

    /**
     * Get idEstadoPago.
     *
     * @return \App\Entity\EstadoPago|null
     */
    public function getIdEstadoPago()
    {
        return $this->idEstadoPago;
    }

    /**
     * Set idCuentaPaciente.
     *
     * @param \App\Entity\CuentaPaciente|null $idCuentaPaciente
     *
     * @return PagoCuenta
     */
    public function setIdCuentaPaciente(\App\Entity\CuentaPaciente $idCuentaPaciente = null)
    {
        $this->idCuentaPaciente = $idCuentaPaciente;

        return $this;
    }

    /**
     * Get idCuentaPaciente.
     *
     * @return \App\Entity\CuentaPaciente|null
     */
    public function getIdCuentaPaciente()
    {
        return $this->idCuentaPaciente;
    }

    /**
     * Set idCaja.
     *
     * @param \App\Entity\Caja|null $idCaja
     *
     * @return PagoCuenta
     */
    public function setIdCaja(\App\Entity\Caja $idCaja = null)
    {
        $this->idCaja = $idCaja;

        return $this;
    }

    /**
     * Get idCaja.
     *
     * @return \App\Entity\Caja|null
     */
    public function getIdCaja()
    {
        return $this->idCaja;
    }

    /**
     * Set idUsuario.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuario
     *
     * @return PagoCuenta
     */
    public function setIdUsuario(\App\Entity\UsuariosRebsol $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return PagoCuenta
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
     * Set idPagoWeb.
     *
     * @param \App\Entity\PagoWeb|null $idPagoWeb
     *
     * @return PagoCuenta
     */
    public function setIdPagoWeb(\App\Entity\PagoWeb $idPagoWeb = null)
    {
        $this->idPagoWeb = $idPagoWeb;

        return $this;
    }

    /**
     * Get idPagoWeb.
     *
     * @return \App\Entity\PagoWeb|null
     */
    public function getIdPagoWeb()
    {
        return $this->idPagoWeb;
    }
    /**
     * Set idMotivoDiferencia.
     *
     * @param \App\Entity\MotivoDiferencia|null idMotivoDiferencia
     *
     * @return PagoCuenta
     */
    public function setIdMotivoDiferencia(\App\Entity\MotivoDiferencia $idMotivoDiferencia = null)
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
     * Set montoDiferencia.
     *
     * @param string|null $montoDiferencia
     *
     * @return PagoCuenta
     */
    public function setMontoDiferencia($montoDiferencia = null)
    {
        $this->montoDiferencia = $montoDiferencia;

        return $this;
    }

    /**
     * Get montoDiferencia.
     *
     * @return string|null
     */
    public function getMontoDiferencia()
    {
        return $this->montoDiferencia;
    }

    /**
     * Set precioDiferencia.
     *
     * @param string|null $precioDiferencia
     *
     * @return PagoCuenta
     */
    public function setPrecioDiferencia($precioDiferencia = null)
    {
        $this->precioDiferencia = $precioDiferencia;

        return $this;
    }

    /**
     * Get precioDiferencia.
     *
     * @return string|null
     */
    public function getPrecioDiferencia()
    {
        return $this->precioDiferencia;
    }

    /**
     * Set observacionRegularizacion
     *
     * @param string $observacionRegularizacion
     * @return PagoCuenta
     */
    public function setObservacionRegularizacion($observacionRegularizacion = null)
    {
        $this->observacionRegularizacion = $observacionRegularizacion;

        return $this;
    }

    /**
     * Get observacionRegularizacion
     *
     * @return string
     */
    public function getObservacionRegularizacion()
    {
        return $this->observacionRegularizacion;
    }

    public function setFechaRegularizacion($fechaRegularizacion)
    {
        $this->fechaRegularizacion = $fechaRegularizacion;

        return $this;
    }

    /**
     * Get fechaRegularizacion.
     *
     * @return \DateTime
     */
    public function getFechaRegularizacion()
    {
        return $this->fechaRegularizacion;
    }

    /**
     * @return bool
     */
    public function isEsCobranza()
    {
        return $this->esCobranza;
    }

    /**
     * @param bool $esCobranza
     */
    public function setEsCobranza($esCobranza)
    {
        $this->esCobranza = $esCobranza;
    }

}
