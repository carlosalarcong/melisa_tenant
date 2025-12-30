<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccionClinicaPaciente
 *
 * @ORM\Table(name="accion_clinica_paciente")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\AccionClinicaPacienteRepository")
 */
class AccionClinicaPaciente
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
     * @ORM\Column(name="FECHA_VALIDADO", type="datetime", nullable=true)
     */
    private $fechaValidado;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_INFORMADO", type="datetime", nullable=true)
     */
    private $fechaInformado;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_LLAMADO", type="datetime", nullable=true)
     */
    private $fechaLlamado;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CANTIDAD", type="integer", nullable=true)
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
     * @var bool|null
     *
     * @ORM\Column(name="ES_GES", type="boolean", nullable=true, options={"comment"="No debería aceptar nulo, por eso el valor default = 0"})
     */
    private $esGES = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TIENE_RECARGO", type="boolean", nullable=true)
     */
    private $tieneRecargo = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_RECHAZO", type="datetime", nullable=true)
     */
    private $fechaRechazo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_NIVEL_FONASA", type="integer", nullable=true)
     */
    private $montoNivelFonasa;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_AFECTO", type="integer", nullable=true)
     */
    private $montoAfecto;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_EXENTO", type="integer", nullable=true)
     */
    private $montoExento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="IVA", type="integer", nullable=true)
     */
    private $iva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_AFECTO_SIN_IVA", type="integer", nullable=true)
     */
    private $montoAfectoSinIva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_IVA", type="integer", nullable=true)
     */
    private $montoIva;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_BOLETA", type="string", length=100, nullable=true)
     */
    private $numeroBoleta;

    /**
     * @var \PaqueteArticulo
     *
     * @ORM\ManyToOne(targetEntity="PaqueteArticulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_ARTICULO", referencedColumnName="ID")
     * })
     */
    private $idPaqueteArticulo;

    /**
     * @var \PaquetePrestacion
     *
     * @ORM\ManyToOne(targetEntity="PaquetePrestacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idPaquetePrestacion;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_LLAMADO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioLlamado;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_INFORMADO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioInformado;

    /**
     * @var \PabAgenda
     *
     * @ORM\ManyToOne(targetEntity="PabAgenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAB_AGENDA", referencedColumnName="ID")
     * })
     */
    private $idPabAgenda;

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
     *   @ORM\JoinColumn(name="ID_USUARIO_VALIDADO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioValidado;

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
     * @var \NivelFonasa
     *
     * @ORM\ManyToOne(targetEntity="NivelFonasa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_NIVEL_FONASA", referencedColumnName="ID")
     * })
     */
    private $idNivelFonasa;

    /**
     * @var \AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idAccionClinica;

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
     * @var \EstadoAccionClinica
     *
     * @ORM\ManyToOne(targetEntity="EstadoAccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idEstadoAccionClinica;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_RECHAZO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioRechazo;

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
     * @var \RelUsuarioServicio
     *
     * @ORM\ManyToOne(targetEntity="RelUsuarioServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SERVICIO_SOLICITANTE", referencedColumnName="ID")
     * })
     */
    private $idUsuarioServicioSolicitante;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROFESIONAL_REALIZADOR", referencedColumnName="ID")
     * })
     */
    private $idProfesionalRealizador;

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
     * @ORM\Column(name="PRECIO_DIFERENCIA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $precioDiferencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RCH_FUNDAMENTOS", type="string", length=255, nullable=true)
     */
    private $rchFundamentos;

    /**
     * @var \Rol
     *
     * @ORM\ManyToOne(targetEntity="Rol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ROL", referencedColumnName="ID")
     * })
     */
    private $idRol;

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
     * @var bool|null
     *
     * @ORM\Column(name="ES_INDICACIONES_CUIDADOS", type="boolean", nullable=true, options={"comment"="No debería aceptar nulo, por eso el valor default = 0"})
     */
    private $esIndicacionesCuidados = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="CANTIDAD_PROCEDIMIENTOS", type="integer", nullable=true)
     */
    private $cantidadProcedimientos;

    /**
     * @var \EstadoAccionClinica
     *
     * @ORM\ManyToOne(targetEntity="EstadoAccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_ACCION_CLINICA_PROCEDIMIENTO", referencedColumnName="ID")
     * })
     */
    private $idEstadoAccionClinicaProcedimiento;

    /**
     * @var \ExamenPacienteFcDetalle
     *
     * @ORM\ManyToOne(targetEntity="ExamenPacienteFcDetalle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EXAMEN_PACIENTE_DETALLE", referencedColumnName="ID")
     * })
     */
    private $idExamenPacienteDetalle;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CARGA_DIA_CAMA", type="datetime", nullable=true)
     */
    private $fechaCargaDiaCama;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CANTIDAD_ACCION_CLINICA_DIA_CAMA_PAQUETE", type="integer", nullable=true)
     */
    private $cantidadAccionClinicaDiaCamaPaquete;

    /**
     * @var int|null
     *
     * @ORM\Column(name="COUNT_ACCION_CLINICA_DIA_CAMA_PAQUETE", type="integer", nullable=true)
     */
    private $countAccionClinicaDiaCamaPaquete;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_DIA_CAMA_PAQUETE_ENFERMERIA", type="boolean", nullable=true, options={"default":"0"})
     */
    private $esDiaCamaPaqueteEnfermeria = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_DIA_CAMA_PAQUETE_FACTURABLE", type="boolean", nullable=true, options={"default":"0"})
     */
    private $esDiaCamaPaqueteFacturable = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_DIA_CAMA_PAQUETE_ADMISION", type="boolean", nullable=true, options={"default":"0"})
     */
    private $esDiaCamaPaqueteAdmision = '0';

    /**
     * @var \AccionClinicaPaciente
     *
     * @ORM\ManyToOne(targetEntity="AccionClinicaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA_PACIENTE_PAQUETE", referencedColumnName="ID")
     * })
     */
    private $idAccionClinicaPacientePaquete;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_PAQUETE_ADMISION", type="boolean", nullable=true, options={"default":"0"})
     */
    private $esPaqueteAdmision = '0';

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
     * @var \DetalleTratamiento
     *
     * @ORM\ManyToOne(targetEntity="DetalleTratamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_DETALLE_TRATAMIENTO", referencedColumnName="ID")
     * })
     */
    private $idDetalleTratamiento;

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
     * Set fechaSolicitud.
     *
     * @param \DateTime|null $fechaSolicitud
     *
     * @return AccionClinicaPaciente
     */
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
     * @return AccionClinicaPaciente
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
     * @return AccionClinicaPaciente
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
     * Set fechaValidado.
     *
     * @param \DateTime|null $fechaValidado
     *
     * @return AccionClinicaPaciente
     */
    public function setFechaValidado($fechaValidado = null)
    {
        $this->fechaValidado = $fechaValidado;

        return $this;
    }

    /**
     * Get fechaValidado.
     *
     * @return \DateTime|null
     */
    public function getFechaValidado()
    {
        return $this->fechaValidado;
    }

    /**
     * Set fechaInformado.
     *
     * @param \DateTime|null $fechaInformado
     *
     * @return AccionClinicaPaciente
     */
    public function setFechaInformado($fechaInformado = null)
    {
        $this->fechaInformado = $fechaInformado;

        return $this;
    }

    /**
     * Get fechaInformado.
     *
     * @return \DateTime|null
     */
    public function getFechaInformado()
    {
        return $this->fechaInformado;
    }

    /**
     * Set fechaLlamado.
     *
     * @param \DateTime|null $fechaLlamado
     *
     * @return AccionClinicaPaciente
     */
    public function setFechaLlamado($fechaLlamado = null)
    {
        $this->fechaLlamado = $fechaLlamado;

        return $this;
    }

    /**
     * Get fechaLlamado.
     *
     * @return \DateTime|null
     */
    public function getFechaLlamado()
    {
        return $this->fechaLlamado;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return AccionClinicaPaciente
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
     * Set fechaRechazo.
     *
     * @param \DateTime|null $fechaRechazo
     *
     * @return AccionClinicaPaciente
     */
    public function setFechaRechazo($fechaRechazo = null)
    {
        $this->fechaRechazo = $fechaRechazo;

        return $this;
    }

    /**
     * Get fechaRechazo.
     *
     * @return \DateTime|null
     */
    public function getFechaRechazo()
    {
        return $this->fechaRechazo;
    }

    /**
     * Set cantidad.
     *
     * @param int|null $cantidad
     *
     * @return AccionClinicaPaciente
     */
    public function setCantidad($cantidad = null)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return int|null
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
     * @return AccionClinicaPaciente
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
     * @return AccionClinicaPaciente
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
     * @return AccionClinicaPaciente
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
     * @return AccionClinicaPaciente
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
     * @return AccionClinicaPaciente
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
     * @return AccionClinicaPaciente
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
     * Set esGES.
     *
     * @param bool|null $esGES
     *
     * @return AccionClinicaPaciente
     */
    public function setEsGES($esGES = null)
    {
        $this->esGES = $esGES;

        return $this;
    }

    /**
     * Get esGES.
     *
     * @return bool|null
     */
    public function getEsGES()
    {
        return $this->esGES;
    }

    /**
     * Set tieneRecargo.
     *
     * @param bool|null $tieneRecargo
     *
     * @return AccionClinicaPaciente
     */
    public function setTieneRecargo($tieneRecargo = null)
    {
        $this->tieneRecargo = $tieneRecargo;

        return $this;
    }

    /**
     * Get tieneRecargo.
     *
     * @return bool|null
     */
    public function getTieneRecargo()
    {
        return $this->tieneRecargo;
    }

    /**
     * Set montoNivelFonasa.
     *
     * @param int|null $montoNivelFonasa
     *
     * @return AccionClinicaPaciente
     */
    public function setMontoNivelFonasa($montoNivelFonasa = null)
    {
        $this->montoNivelFonasa = $montoNivelFonasa;

        return $this;
    }

    /**
     * Get montoNivelFonasa.
     *
     * @return int|null
     */
    public function getMontoNivelFonasa()
    {
        return $this->montoNivelFonasa;
    }

    /**
     * Set montoAfecto.
     *
     * @param int|null $montoAfecto
     *
     * @return AccionClinicaPaciente
     */
    public function setMontoAfecto($montoAfecto = null)
    {
        $this->montoAfecto = $montoAfecto;

        return $this;
    }

    /**
     * Get montoAfecto.
     *
     * @return int|null
     */
    public function getMontoAfecto()
    {
        return $this->montoAfecto;
    }

    /**
     * Set montoExento.
     *
     * @param int|null $montoExento
     *
     * @return AccionClinicaPaciente
     */
    public function setMontoExento($montoExento = null)
    {
        $this->montoExento = $montoExento;

        return $this;
    }

    /**
     * Get montoExento.
     *
     * @return int|null
     */
    public function getMontoExento()
    {
        return $this->montoExento;
    }

    /**
     * Set iva.
     *
     * @param int|null $iva
     *
     * @return AccionClinicaPaciente
     */
    public function setIva($iva = null)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return int|null
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set montoAfectoSinIva.
     *
     * @param int|null $montoAfectoSinIva
     *
     * @return AccionClinicaPaciente
     */
    public function setMontoAfectoSinIva($montoAfectoSinIva = null)
    {
        $this->montoAfectoSinIva = $montoAfectoSinIva;

        return $this;
    }

    /**
     * Get montoAfectoSinIva.
     *
     * @return int|null
     */
    public function getMontoAfectoSinIva()
    {
        return $this->montoAfectoSinIva;
    }

    /**
     * Set montoIva.
     *
     * @param int|null $montoIva
     *
     * @return AccionClinicaPaciente
     */
    public function setMontoIva($montoIva = null)
    {
        $this->montoIva = $montoIva;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumeroBoleta()
    {
        return $this->numeroBoleta;
    }

    /**
     * @param string|null $numeroBoleta
     */
    public function setNumeroBoleta($numeroBoleta)
    {
        $this->numeroBoleta = $numeroBoleta;
    }

    /**
     * Get montoIva.
     *
     * @return int|null
     */
    public function getMontoIva()
    {
        return $this->montoIva;
    }

    /**
     * Set idUsuarioServicioSolicitante.
     *
     * @param \App\Entity\RelUsuarioServicio|null $idUsuarioServicioSolicitante
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioServicioSolicitante(\App\Entity\RelUsuarioServicio $idUsuarioServicioSolicitante = null)
    {
        $this->idUsuarioServicioSolicitante = $idUsuarioServicioSolicitante;

        return $this;
    }

    /**
     * Get idUsuarioServicioSolicitante.
     *
     * @return \App\Entity\RelUsuarioServicio|null
     */
    public function getIdUsuarioServicioSolicitante()
    {
        return $this->idUsuarioServicioSolicitante;
    }

    /**
     * Set idUsuarioServicioRealizador.
     *
     * @param \App\Entity\RelUsuarioServicio|null $idUsuarioServicioRealizador
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioServicioRealizador(\App\Entity\RelUsuarioServicio $idUsuarioServicioRealizador = null)
    {
        $this->idUsuarioServicioRealizador = $idUsuarioServicioRealizador;

        return $this;
    }

    /**
     * Get idUsuarioServicioRealizador.
     *
     * @return \App\Entity\RelUsuarioServicio|null
     */
    public function getIdUsuarioServicioRealizador()
    {
        return $this->idUsuarioServicioRealizador;
    }

    /**
     * Set idUsuarioValidado.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioValidado
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioValidado(\App\Entity\UsuariosRebsol $idUsuarioValidado = null)
    {
        $this->idUsuarioValidado = $idUsuarioValidado;

        return $this;
    }

    /**
     * Get idUsuarioValidado.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioValidado()
    {
        return $this->idUsuarioValidado;
    }

    /**
     * Set idUsuarioInformado.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioInformado
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioInformado(\App\Entity\UsuariosRebsol $idUsuarioInformado = null)
    {
        $this->idUsuarioInformado = $idUsuarioInformado;

        return $this;
    }

    /**
     * Get idUsuarioInformado.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioInformado()
    {
        return $this->idUsuarioInformado;
    }

    /**
     * Set idUsuarioLlamado.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioLlamado
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioLlamado(\App\Entity\UsuariosRebsol $idUsuarioLlamado = null)
    {
        $this->idUsuarioLlamado = $idUsuarioLlamado;

        return $this;
    }

    /**
     * Get idUsuarioLlamado.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioLlamado()
    {
        return $this->idUsuarioLlamado;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return AccionClinicaPaciente
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
     * Set idUsuarioRechazo.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioRechazo
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioRechazo(\App\Entity\UsuariosRebsol $idUsuarioRechazo = null)
    {
        $this->idUsuarioRechazo = $idUsuarioRechazo;

        return $this;
    }

    /**
     * Get idUsuarioRechazo.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioRechazo()
    {
        return $this->idUsuarioRechazo;
    }

    /**
     * Set idProfesionalRealizador.
     *
     * @param \App\Entity\UsuariosRebsol|null $idProfesionalRealizador
     *
     * @return AccionClinicaPaciente
     */
    public function setIdProfesionalRealizador(\App\Entity\UsuariosRebsol $idProfesionalRealizador = null)
    {
        $this->idProfesionalRealizador = $idProfesionalRealizador;

        return $this;
    }

    /**
     * Get idProfesionalRealizador.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdProfesionalRealizador()
    {
        return $this->idProfesionalRealizador;
    }

    /**
     * Set idServicioSolicitud.
     *
     * @param \App\Entity\Servicio|null $idServicioSolicitud
     *
     * @return AccionClinicaPaciente
     */
    public function setIdServicioSolicitud(\App\Entity\Servicio $idServicioSolicitud = null)
    {
        $this->idServicioSolicitud = $idServicioSolicitud;

        return $this;
    }

    /**
     * Get idServicioSolicitud.
     *
     * @return \App\Entity\Servicio|null
     */
    public function getIdServicioSolicitud()
    {
        return $this->idServicioSolicitud;
    }

    /**
     * Set idServicioRealizacion.
     *
     * @param \App\Entity\Servicio|null $idServicioRealizacion
     *
     * @return AccionClinicaPaciente
     */
    public function setIdServicioRealizacion(\App\Entity\Servicio $idServicioRealizacion = null)
    {
        $this->idServicioRealizacion = $idServicioRealizacion;

        return $this;
    }

    /**
     * Get idServicioRealizacion.
     *
     * @return \App\Entity\Servicio|null
     */
    public function getIdServicioRealizacion()
    {
        return $this->idServicioRealizacion;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \App\Entity\AccionClinica $idAccionClinica
     *
     * @return AccionClinicaPaciente
     */
    public function setIdAccionClinica(\App\Entity\AccionClinica $idAccionClinica)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \App\Entity\AccionClinica
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * Set idEstadoAccionClinica.
     *
     * @param \App\Entity\EstadoAccionClinica|null $idEstadoAccionClinica
     *
     * @return AccionClinicaPaciente
     */
    public function setIdEstadoAccionClinica(\App\Entity\EstadoAccionClinica $idEstadoAccionClinica = null)
    {
        $this->idEstadoAccionClinica = $idEstadoAccionClinica;

        return $this;
    }

    /**
     * Get idEstadoAccionClinica.
     *
     * @return \App\Entity\EstadoAccionClinica|null
     */
    public function getIdEstadoAccionClinica()
    {
        return $this->idEstadoAccionClinica;
    }

    /**
     * Set idEstadoPago.
     *
     * @param \App\Entity\EstadoPago|null $idEstadoPago
     *
     * @return AccionClinicaPaciente
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
     * Set idPaciente.
     *
     * @param \App\Entity\Paciente $idPaciente
     *
     * @return AccionClinicaPaciente
     */
    public function setIdPaciente(\App\Entity\Paciente $idPaciente)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Paciente
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \App\Entity\PagoCuenta|null $idPagoCuenta
     *
     * @return AccionClinicaPaciente
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

    /**
     * Set idPrevision.
     *
     * @param \App\Entity\Prevision|null $idPrevision
     *
     * @return AccionClinicaPaciente
     */
    public function setIdPrevision(\App\Entity\Prevision $idPrevision = null)
    {
        $this->idPrevision = $idPrevision;

        return $this;
    }

    /**
     * Get idPrevision.
     *
     * @return \App\Entity\Prevision|null
     */
    public function getIdPrevision()
    {
        return $this->idPrevision;
    }

    /**
     * Set idPaqueteArticulo.
     *
     * @param \App\Entity\PaqueteArticulo|null $idPaqueteArticulo
     *
     * @return AccionClinicaPaciente
     */
    public function setIdPaqueteArticulo(\App\Entity\PaqueteArticulo $idPaqueteArticulo = null)
    {
        $this->idPaqueteArticulo = $idPaqueteArticulo;

        return $this;
    }

    /**
     * Get idPaqueteArticulo.
     *
     * @return \App\Entity\PaqueteArticulo|null
     */
    public function getIdPaqueteArticulo()
    {
        return $this->idPaqueteArticulo;
    }

    /**
     * @return \PaquetePrestacion
     */
    public function getIdPaquetePrestacion()
    {
        return $this->idPaquetePrestacion;
    }

    /**
     * @param \PaquetePrestacion $idPaquetePrestacion
     */
    public function setIdPaquetePrestacion($idPaquetePrestacion)
    {
        $this->idPaquetePrestacion = $idPaquetePrestacion;
    }

    /**
     * Set idBodega.
     *
     * @param \App\Entity\Bodega|null $idBodega
     *
     * @return AccionClinicaPaciente
     */
    public function setIdBodega(\App\Entity\Bodega $idBodega = null)
    {
        $this->idBodega = $idBodega;

        return $this;
    }

    /**
     * Get idBodega.
     *
     * @return \App\Entity\Bodega|null
     */
    public function getIdBodega()
    {
        return $this->idBodega;
    }

    /**
     * Set idPabAgenda.
     *
     * @param \App\Entity\PabAgenda|null $idPabAgenda
     *
     * @return AccionClinicaPaciente
     */
    public function setIdPabAgenda(\App\Entity\PabAgenda $idPabAgenda = null)
    {
        $this->idPabAgenda = $idPabAgenda;

        return $this;
    }

    /**
     * Get idPabAgenda.
     *
     * @return \App\Entity\PabAgenda|null
     */
    public function getIdPabAgenda()
    {
        return $this->idPabAgenda;
    }

    /**
     * Set idRecienNacido.
     *
     * @param \App\Entity\RecienNacido|null $idRecienNacido
     *
     * @return AccionClinicaPaciente
     */
    public function setIdRecienNacido(\App\Entity\RecienNacido $idRecienNacido = null)
    {
        $this->idRecienNacido = $idRecienNacido;

        return $this;
    }

    /**
     * Get idRecienNacido.
     *
     * @return \App\Entity\RecienNacido|null
     */
    public function getIdRecienNacido()
    {
        return $this->idRecienNacido;
    }

    /**
     * Set idNivelFonasa.
     *
     * @param \App\Entity\NivelFonasa|null $idNivelFonasa
     *
     * @return AccionClinicaPaciente
     */
    public function setIdNivelFonasa(\App\Entity\NivelFonasa $idNivelFonasa = null)
    {
        $this->idNivelFonasa = $idNivelFonasa;

        return $this;
    }

    /**
     * Get idNivelFonasa.
     *
     * @return \App\Entity\NivelFonasa|null
     */
    public function getIdNivelFonasa()
    {
        return $this->idNivelFonasa;
    }

    /**
     * Set idMotivoDiferencia.
     *
     * @param \App\Entity\MotivoDiferencia|null idMotivoDiferencia
     *
     * @return AccionClinicaPaciente
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
     * Set precioDiferencia.
     *
     * @param string|null $precioDiferencia
     *
     * @return AccionClinicaPaciente
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
     * Set rchFundamentos.
     *
     * @param string|null $rchFundamentos
     *
     * @return AccionClinicaPaciente
     */
    public function setRchFundamentos($rchFundamentos = null)
    {
        $this->rchFundamentos = $rchFundamentos;

        return $this;
    }

    /**
     * Get rchFundamentos.
     *
     * @return string|null
     */
    public function getRchFundamentos()
    {
        return $this->rchFundamentos;
    }

    /**
     * Set idRol.
     *
     * @param \App\Entity\Rol|null $idRol
     *
     * @return AccionClinicaPaciente
     */
    public function setIdRol(\App\Entity\Rol $idRol = null)
    {
        $this->idRol = $idRol;

        return $this;
    }

    /**
     * Get idRol.
     *
     * @return \App\Entity\Rol|null
     */
    public function getIdRol()
    {
        return $this->idRol;
    }

    /**
     * Set idUsuarioSolicitud.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioSolicitud
     *
     * @return AccionClinicaPaciente
     */
    public function setIdUsuarioSolicitud(\App\Entity\UsuariosRebsol $idUsuarioSolicitud = null)
    {
        $this->idUsuarioSolicitud = $idUsuarioSolicitud;

        return $this;
    }

    /**
     * Get idUsuarioSolicitud.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioSolicitud()
    {
        return $this->idUsuarioSolicitud;
    }

    /**
     * Set esIndicacionesCuidados.
     *
     * @param bool|null $esIndicacionesCuidados
     *
     * @return AccionClinicaPaciente
     */
    public function setEsIndicacionesCuidados($esIndicacionesCuidados = null)
    {
        $this->esIndicacionesCuidados = $esIndicacionesCuidados;

        return $this;
    }

    /**
     * Get esIndicacionesCuidados.
     *
     * @return bool|null
     */
    public function getEsIndicacionesCuidados()
    {
        return $this->esIndicacionesCuidados;
    }

    /**
     * @return int|null
     */
    public function getCantidadProcedimientos()
    {
        return $this->cantidadProcedimientos;
    }

    /**
     * @param int|null $cantidadProcedimientos
     */
    public function setCantidadProcedimientos($cantidadProcedimientos)
    {
        $this->cantidadProcedimientos = $cantidadProcedimientos;
    }

    /**
     * @return \EstadoAccionClinica
     */
    public function getIdEstadoAccionClinicaProcedimiento()
    {
        return $this->idEstadoAccionClinicaProcedimiento;
    }

    /**
     * @param \EstadoAccionClinica $idEstadoAccionClinicaProcedimiento
     */
    public function setIdEstadoAccionClinicaProcedimiento($idEstadoAccionClinicaProcedimiento)
    {
        $this->idEstadoAccionClinicaProcedimiento = $idEstadoAccionClinicaProcedimiento;
    }

    /**
     * @return \ExamenPacienteFcDetalle
     */
    public function getIdExamenPacienteDetalle()
    {
        return $this->idExamenPacienteDetalle;
    }

    /**
     * @param \ExamenPacienteFcDetalle $idExamenPacienteDetalle
     */
    public function setIdExamenPacienteDetalle($idExamenPacienteDetalle = null)
    {
        $this->idExamenPacienteDetalle = $idExamenPacienteDetalle;
    }

    /**
     * @return \DateTime|null
     */
    public function getFechaCargaDiaCama()
    {
        return $this->fechaCargaDiaCama;
    }

    /**
     * @param \DateTime|null $fechaCargaDiaCama
     */
    public function setFechaCargaDiaCama($fechaCargaDiaCama = NULL)
    {
        $this->fechaCargaDiaCama = $fechaCargaDiaCama;
    }

    /**
     * @return int|null
     */
    public function getCantidadAccionClinicaDiaCamaPaquete()
    {
        return $this->cantidadAccionClinicaDiaCamaPaquete;
    }

    /**
     * @param int|null $cantidadAccionClinicaDiaCamaPaquete
     */
    public function setCantidadAccionClinicaDiaCamaPaquete($cantidadAccionClinicaDiaCamaPaquete = NULL)
    {
        $this->cantidadAccionClinicaDiaCamaPaquete = $cantidadAccionClinicaDiaCamaPaquete;
    }

    /**
     * @return int|null
     */
    public function getCountAccionClinicaDiaCamaPaquete()
    {
        return $this->countAccionClinicaDiaCamaPaquete;
    }

    /**
     * @param int|null $countAccionClinicaDiaCamaPaquete
     */
    public function setCountAccionClinicaDiaCamaPaquete($countAccionClinicaDiaCamaPaquete = NULL)
    {
        $this->countAccionClinicaDiaCamaPaquete = $countAccionClinicaDiaCamaPaquete;
    }

    /**
     * @return bool|null
     */
    public function getEsDiaCamaPaqueteEnfermeria()
    {
        return $this->esDiaCamaPaqueteEnfermeria;
    }

    /**
     * @param bool|null $esDiaCamaPaqueteEnfermeria
     */
    public function setEsDiaCamaPaqueteEnfermeria($esDiaCamaPaqueteEnfermeria)
    {
        $this->esDiaCamaPaqueteEnfermeria = $esDiaCamaPaqueteEnfermeria;
    }

    /**
     * @return bool|null
     */
    public function getEsDiaCamaPaqueteFacturable()
    {
        return $this->esDiaCamaPaqueteFacturable;
    }

    /**
     * @param bool|null $esDiaCamaPaqueteFacturable
     */
    public function setEsDiaCamaPaqueteFacturable($esDiaCamaPaqueteFacturable)
    {
        $this->esDiaCamaPaqueteFacturable = $esDiaCamaPaqueteFacturable;
    }

    /**
     * @return bool|null
     */
    public function getEsDiaCamaPaqueteAdmision()
    {
        return $this->esDiaCamaPaqueteAdmision;
    }

    /**
     * @param bool|null $esDiaCamaPaqueteAdmision
     */
    public function setEsDiaCamaPaqueteAdmision($esDiaCamaPaqueteAdmision)
    {
        $this->esDiaCamaPaqueteAdmision = $esDiaCamaPaqueteAdmision;
    }

    /**
     * @return \AccionClinicaPaciente
     */
    public function getIdAccionClinicaPacientePaquete()
    {
        return $this->idAccionClinicaPacientePaquete;
    }

    /**
     * @param \AccionClinicaPaciente $idAccionClinicaPacientePaquete
     */
    public function setIdAccionClinicaPacientePaquete($idAccionClinicaPacientePaquete)
    {
        $this->idAccionClinicaPacientePaquete = $idAccionClinicaPacientePaquete;
    }

    /**
     * @return bool|null
     */
    public function getEsPaqueteAdmision()
    {
        return $this->esPaqueteAdmision;
    }

    /**
     * @param bool|null $esPaqueteAdmision
     */
    public function setEsPaqueteAdmision($esPaqueteAdmision)
    {
        $this->esPaqueteAdmision = $esPaqueteAdmision;
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
     * @return \DetalleTratamiento
     */
    public function getIdDetalleTratamiento()
    {
        return $this->idDetalleTratamiento;
    }

    /**
     * @param \DetalleTratamiento $idDetalleTratamiento
     */
    public function setIdDetalleTratamiento($idDetalleTratamiento)
    {
        $this->idDetalleTratamiento = $idDetalleTratamiento;
    }

}
