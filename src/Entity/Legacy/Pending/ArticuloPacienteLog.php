<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticuloPaciente
 *
 * @ORM\Table(name="articulo_paciente_log")
 * @ORM\Entity
 */
class ArticuloPacienteLog
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
     * @ORM\Column(name="ID_ARTICULO_PACIENTE", type="integer", nullable=false)
     */
    private $idArticuloPaciente;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_SOLICITUD", type="datetime", nullable=true)
     */
    private $fechaSolicitud;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_AGENDA", type="datetime", nullable=true)
     */
    private $fechaAgenda;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_REALIZACION", type="datetime", nullable=true)
     */
    private $fechaRealizacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var float|null
     *
     * @ORM\Column(name="CANTIDAD", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PRECIO_COBRADO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $precioCobrado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GLOSA_SOLICITUD", type="string", length=100, nullable=true)
     */
    private $glosaSolicitud;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PROCESADO_CUENTAS", type="integer", nullable=true)
     */
    private $procesadoCuentas;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PORCENTAJE_DESCUENTO", type="integer", nullable=true)
     */
    private $porcentajeDescuento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_DESCUENTO", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $totalDescuento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO_NC", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montoNc;

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
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO_SOLICITUD", referencedColumnName="ID")
     * })
     */
    private $idServicioSolicitud;

    /**
     * @var \Articulo
     *
     * @ORM\ManyToOne(targetEntity="Articulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ARTICULO", referencedColumnName="ID")
     * })
     */
    private $idArticulo;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PREVISION", referencedColumnName="ID")
     * })
     */
    private $idPrevision;

    /**
     * @var \AccionClinicaPaciente
     *
     * @ORM\ManyToOne(targetEntity="AccionClinicaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idAccionClinicaPaciente;

    /**
     * @var \RchReceta
     *
     * @ORM\ManyToOne(targetEntity="RchReceta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_RECETA", referencedColumnName="ID")
     * })
     */
    private $idRchReceta;

    /**
     * @var \RecienNacido
     *
     * @ORM\ManyToOne(targetEntity="RecienNacido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RECIEN_NACIDO", referencedColumnName="ID")
     * })
     */
    private $idRecienNacido;

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
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO_REALIZACION", referencedColumnName="ID")
     * })
     */
    private $idServicioRealizacion;

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
     * @var \Bodega
     *
     * @ORM\ManyToOne(targetEntity="Bodega")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BODEGA", referencedColumnName="ID")
     * })
     */
    private $idBodega;

    /**
     * @var \RelUsuarioServicio
     *
     * @ORM\ManyToOne(targetEntity="RelUsuarioServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SERVICIO_REALIZADOR", referencedColumnName="ID")
     * })
     */
    private $idUsuarioServicioRealizador;

    /**
     * @var \TipoCargaArticuloPaciente
     *
     * @ORM\ManyToOne(targetEntity="TipoCargaArticuloPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_CARGA_ARTICULO_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idTipoCargaArticuloPaciente;

    /**
     * @var \RelUsuarioServicio
     *
     * @ORM\ManyToOne(targetEntity="RelUsuarioServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SERVICIO_SOLICITANTE", referencedColumnName="ID")
     * })
     */
    private $idUsuarioServicioSolicitante;

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
     * @var \RchIndicacionPlanificacion
     *
     * @ORM\ManyToOne(targetEntity="RchIndicacionPlanificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_INDICACION_PLANIFICACION", referencedColumnName="ID")
     * })
     */
    private $idRchIndicacionPlanificacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_PAQUETE", type="boolean", nullable=false)
     */
    private $esPaquete = '0';

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
     * @var \EstadoAccionClinica
     *
     * @ORM\ManyToOne(targetEntity="EstadoAccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idEstadoAccionClinica;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_COMENTARIO", type="datetime", nullable=true)
     */
    private $fechaComentario;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_COMENTARIO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioComentario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COMENTARIO", type="string", length=100, nullable=true)
     */
    private $comentario;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getIdArticuloPaciente()
    {
        return $this->idArticuloPaciente;
    }

    /**
     * @param int $idArticuloPaciente
     */
    public function setIdArticuloPaciente($idArticuloPaciente)
    {
        $this->idArticuloPaciente = $idArticuloPaciente;
    }

    public function setFechaSolicitud($fechaSolicitud = null)
    {
        $this->fechaSolicitud = $fechaSolicitud;

        return $this;
    }

    /**
     * Get fechaSolicitud.
     *
     * @return \DateTime|null
     */
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }

    /**
     * Set fechaAgenda.
     *
     * @param \DateTime|null $fechaAgenda
     *
     * @return ArticuloPaciente
     */
    public function setFechaAgenda($fechaAgenda = null)
    {
        $this->fechaAgenda = $fechaAgenda;

        return $this;
    }

    /**
     * Get fechaAgenda.
     *
     * @return \DateTime|null
     */
    public function getFechaAgenda()
    {
        return $this->fechaAgenda;
    }

    /**
     * Set fechaRealizacion.
     *
     * @param \DateTime|null $fechaRealizacion
     *
     * @return ArticuloPaciente
     */
    public function setFechaRealizacion($fechaRealizacion = null)
    {
        $this->fechaRealizacion = $fechaRealizacion;

        return $this;
    }

    /**
     * Get fechaRealizacion.
     *
     * @return \DateTime|null
     */
    public function getFechaRealizacion()
    {
        return $this->fechaRealizacion;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return ArticuloPaciente
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
     * Set cantidad.
     *
     * @param float|null $cantidad
     *
     * @return ArticuloPaciente
     */
    public function setCantidad($cantidad = null)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float|null
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precioCobrado.
     *
     * @param string|null $precioCobrado
     *
     * @return ArticuloPaciente
     */
    public function setPrecioCobrado($precioCobrado = null)
    {
        $this->precioCobrado = $precioCobrado;

        return $this;
    }

    /**
     * Get precioCobrado.
     *
     * @return string|null
     */
    public function getPrecioCobrado()
    {
        return $this->precioCobrado;
    }

    /**
     * Set glosaSolicitud.
     *
     * @param string|null $glosaSolicitud
     *
     * @return ArticuloPaciente
     */
    public function setGlosaSolicitud($glosaSolicitud = null)
    {
        $this->glosaSolicitud = $glosaSolicitud;

        return $this;
    }

    /**
     * Get glosaSolicitud.
     *
     * @return string|null
     */
    public function getGlosaSolicitud()
    {
        return $this->glosaSolicitud;
    }

    /**
     * Set procesadoCuentas.
     *
     * @param int|null $procesadoCuentas
     *
     * @return ArticuloPaciente
     */
    public function setProcesadoCuentas($procesadoCuentas = null)
    {
        $this->procesadoCuentas = $procesadoCuentas;

        return $this;
    }

    /**
     * Get procesadoCuentas.
     *
     * @return int|null
     */
    public function getProcesadoCuentas()
    {
        return $this->procesadoCuentas;
    }

    /**
     * Set porcentajeDescuento.
     *
     * @param int|null $porcentajeDescuento
     *
     * @return ArticuloPaciente
     */
    public function setPorcentajeDescuento($porcentajeDescuento = null)
    {
        $this->porcentajeDescuento = $porcentajeDescuento;

        return $this;
    }

    /**
     * Get porcentajeDescuento.
     *
     * @return int|null
     */
    public function getPorcentajeDescuento()
    {
        return $this->porcentajeDescuento;
    }

    /**
     * Set totalDescuento.
     *
     * @param string|null $totalDescuento
     *
     * @return ArticuloPaciente
     */
    public function setTotalDescuento($totalDescuento = null)
    {
        $this->totalDescuento = $totalDescuento;

        return $this;
    }

    /**
     * Get totalDescuento.
     *
     * @return string|null
     */
    public function getTotalDescuento()
    {
        return $this->totalDescuento;
    }

    /**
     * Set montoNc.
     *
     * @param string|null $montoNc
     *
     * @return ArticuloPaciente
     */
    public function setMontoNc($montoNc = null)
    {
        $this->montoNc = $montoNc;

        return $this;
    }

    /**
     * Get montoNc.
     *
     * @return string|null
     */
    public function getMontoNc()
    {
        return $this->montoNc;
    }

    /**
     * Set idUsuarioServicioSolicitante.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RelUsuarioServicio|null $idUsuarioServicioSolicitante
     *
     * @return ArticuloPaciente
     */
    public function setIdUsuarioServicioSolicitante(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RelUsuarioServicio $idUsuarioServicioSolicitante = null)
    {
        $this->idUsuarioServicioSolicitante = $idUsuarioServicioSolicitante;

        return $this;
    }

    /**
     * Get idUsuarioServicioSolicitante.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RelUsuarioServicio|null
     */
    public function getIdUsuarioServicioSolicitante()
    {
        return $this->idUsuarioServicioSolicitante;
    }

    /**
     * Set idUsuarioServicioRealizador.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RelUsuarioServicio|null $idUsuarioServicioRealizador
     *
     * @return ArticuloPaciente
     */
    public function setIdUsuarioServicioRealizador(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RelUsuarioServicio $idUsuarioServicioRealizador = null)
    {
        $this->idUsuarioServicioRealizador = $idUsuarioServicioRealizador;

        return $this;
    }

    /**
     * Get idUsuarioServicioRealizador.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RelUsuarioServicio|null
     */
    public function getIdUsuarioServicioRealizador()
    {
        return $this->idUsuarioServicioRealizador;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return ArticuloPaciente
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
     * Set idServicioSolicitud.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Servicio|null $idServicioSolicitud
     *
     * @return ArticuloPaciente
     */
    public function setIdServicioSolicitud(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Servicio $idServicioSolicitud = null)
    {
        $this->idServicioSolicitud = $idServicioSolicitud;

        return $this;
    }

    /**
     * Get idServicioSolicitud.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Servicio|null
     */
    public function getIdServicioSolicitud()
    {
        return $this->idServicioSolicitud;
    }

    /**
     * Set idServicioRealizacion.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Servicio|null $idServicioRealizacion
     *
     * @return ArticuloPaciente
     */
    public function setIdServicioRealizacion(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Servicio $idServicioRealizacion = null)
    {
        $this->idServicioRealizacion = $idServicioRealizacion;

        return $this;
    }

    /**
     * Get idServicioRealizacion.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Servicio|null
     */
    public function getIdServicioRealizacion()
    {
        return $this->idServicioRealizacion;
    }

    /**
     * Set idArticulo.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Articulo|null $idArticulo
     *
     * @return ArticuloPaciente
     */
    public function setIdArticulo(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Articulo $idArticulo = null)
    {
        $this->idArticulo = $idArticulo;

        return $this;
    }

    /**
     * Get idArticulo.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Articulo|null
     */
    public function getIdArticulo()
    {
        return $this->idArticulo;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado|null $idEstado
     *
     * @return ArticuloPaciente
     */
    public function setIdEstado(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idEstadoPago.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoPago|null $idEstadoPago
     *
     * @return ArticuloPaciente
     */
    public function setIdEstadoPago(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoPago $idEstadoPago = null)
    {
        $this->idEstadoPago = $idEstadoPago;

        return $this;
    }

    /**
     * Get idEstadoPago.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoPago|null
     */
    public function getIdEstadoPago()
    {
        return $this->idEstadoPago;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente|null $idPaciente
     *
     * @return ArticuloPaciente
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
     * Set idPagoCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PagoCuenta|null $idPagoCuenta
     *
     * @return ArticuloPaciente
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
     * Set idPrevision.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision|null $idPrevision
     *
     * @return ArticuloPaciente
     */
    public function setIdPrevision(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idPrevision = null)
    {
        $this->idPrevision = $idPrevision;

        return $this;
    }

    /**
     * Get idPrevision.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision|null
     */
    public function getIdPrevision()
    {
        return $this->idPrevision;
    }

    /**
     * Set idAccionClinicaPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AccionClinicaPaciente|null $idAccionClinicaPaciente
     *
     * @return ArticuloPaciente
     */
    public function setIdAccionClinicaPaciente(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AccionClinicaPaciente $idAccionClinicaPaciente = null)
    {
        $this->idAccionClinicaPaciente = $idAccionClinicaPaciente;

        return $this;
    }

    /**
     * Get idAccionClinicaPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AccionClinicaPaciente|null
     */
    public function getIdAccionClinicaPaciente()
    {
        return $this->idAccionClinicaPaciente;
    }

    /**
     * Set idRchReceta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RchReceta|null $idRchReceta
     *
     * @return ArticuloPaciente
     */
    public function setIdRchReceta(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RchReceta $idRchReceta = null)
    {
        $this->idRchReceta = $idRchReceta;

        return $this;
    }

    /**
     * Get idRchReceta.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RchReceta|null
     */
    public function getIdRchReceta()
    {
        return $this->idRchReceta;
    }

    /**
     * Set idTipoCargaArticuloPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TipoCargaArticuloPaciente $idTipoCargaArticuloPaciente
     *
     * @return ArticuloPaciente
     */
    public function setIdTipoCargaArticuloPaciente(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TipoCargaArticuloPaciente $idTipoCargaArticuloPaciente)
    {
        $this->idTipoCargaArticuloPaciente = $idTipoCargaArticuloPaciente;

        return $this;
    }

    /**
     * Get idTipoCargaArticuloPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TipoCargaArticuloPaciente
     */
    public function getIdTipoCargaArticuloPaciente()
    {
        return $this->idTipoCargaArticuloPaciente;
    }

    /**
     * Set idBodega.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Bodega|null $idBodega
     *
     * @return ArticuloPaciente
     */
    public function setIdBodega(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Bodega $idBodega = null)
    {
        $this->idBodega = $idBodega;

        return $this;
    }

    /**
     * Get idBodega.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Bodega|null
     */
    public function getIdBodega()
    {
        return $this->idBodega;
    }

    /**
     * Set idRecienNacido.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RecienNacido|null $idRecienNacido
     *
     * @return ArticuloPaciente
     */
    public function setIdRecienNacido(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RecienNacido $idRecienNacido = null)
    {
        $this->idRecienNacido = $idRecienNacido;

        return $this;
    }

    /**
     * Get idRecienNacido.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\RecienNacido|null
     */
    public function getIdRecienNacido()
    {
        return $this->idRecienNacido;
    }

    /**
     * @return \RchIndicacionPlanificacion
     */
    public function getIdRchIndicacionPlanificacion()
    {
        return $this->idRchIndicacionPlanificacion;
    }

    /**
     * @param \RchIndicacionPlanificacion $idRchIndicacionPlanificacion
     */
    public function setIdRchIndicacionPlanificacion($idRchIndicacionPlanificacion)
    {
        $this->idRchIndicacionPlanificacion = $idRchIndicacionPlanificacion;
    }

    /**
     * @return bool|string
     */
    public function getEsPaquete()
    {
        return $this->esPaquete;
    }

    /**
     * @param bool|string $esPaquete
     */
    public function setEsPaquete($esPaquete)
    {
        $this->esPaquete = $esPaquete;
    }

    /**
     * @return \UsuariosRebsol
     */
    public function getIdUsuarioSolicitud()
    {
        return $this->idUsuarioSolicitud;
    }

    /**
     * @param \UsuariosRebsol $idUsuarioSolicitud
     */
    public function setIdUsuarioSolicitud($idUsuarioSolicitud)
    {
        $this->idUsuarioSolicitud = $idUsuarioSolicitud;
    }

    /**
     * @return \EstadoAccionClinica
     */
    public function getIdEstadoAccionClinica()
    {
        return $this->idEstadoAccionClinica;
    }

    /**
     * @param \EstadoAccionClinica $idEstadoAccionClinica
     */
    public function setIdEstadoAccionClinica($idEstadoAccionClinica)
    {
        $this->idEstadoAccionClinica = $idEstadoAccionClinica;
    }

    /**
     * @return \DateTime|null
     */
    public function getFechaComentario()
    {
        return $this->fechaComentario;
    }

    /**
     * @param \DateTime|null $fechaComentario
     */
    public function setFechaComentario($fechaComentario)
    {
        $this->fechaComentario = $fechaComentario;
    }

    /**
     * @return \UsuariosRebsol
     */
    public function getIdUsuarioComentario()
    {
        return $this->idUsuarioComentario;
    }

    /**
     * @param \UsuariosRebsol $idUsuarioComentario
     */
    public function setIdUsuarioComentario($idUsuarioComentario)
    {
        $this->idUsuarioComentario = $idUsuarioComentario;
    }

    /**
     * @return string|null
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * @param string|null $comentario
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }

}
