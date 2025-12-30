<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;
/**
 * ReservaAtencion
 *
 * @ORM\Table(name="reserva_atencion", indexes={@ORM\Index(name="IDX_RESERVAATENCION_APELLIDOPATERNO", columns={"APELLIDO_PATERNO"}), @ORM\Index(name="IDX_RESERVAATENCION_IDENTIFICACIONEXTRANJERO", columns={"IDENTIFICACION_EXTRANJERO"}),  @ORM\Index(name="IDX_RESERVAATENCION_APELLIDOMATERNO", columns={"APELLIDO_MATERNO"}), @ORM\Index(name="IDX_RESERVAATENCION_NOMBRES", columns={"NOMBRES"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ReservaAtencionRepository")
 */
class ReservaAtencion
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
     * @var string|null
     *
     * @ORM\Column(name="APELLIDO_PATERNO", type="string", length=45, nullable=true)
     */
    private $apellidoPaterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="APELLIDO_MATERNO", type="string", length=45, nullable=true)
     */
    private $apellidoMaterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRES", type="string", length=60, nullable=true)
     */
    private $nombres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_FIJO", type="string", length=20, nullable=true)
     */
    private $telefonoFijo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_MOVIL", type="string", length=20, nullable=true)
     */
    private $telefonoMovil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_CONTACTO", type="string", length=20, nullable=true)
     */
    private $telefonoContacto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIRECCION", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RESTO_DIRECCION", type="string", length=255, nullable=true)
     */
    private $restoDireccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_NACIMIENTO", type="datetime", nullable=true)
     */
    private $fechaNacimiento;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ENVIO_CORREO_CONFIRMACION", type="datetime", nullable=true)
     */
    private $fechaEnvioCorreoConfirmacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CORREO_ELECTRONICO", type="string", length=80, nullable=true)
     */
    private $correoElectronico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CORREO_CONFIRMACION", type="string", length=80, nullable=true)
     */
    private $correoConfirmacion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CONFIRMADO", type="integer", nullable=true)
     */
    private $confirmado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RUT_PACIENTE", type="integer", nullable=true)
     */
    private $rutPaciente;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_PLAN", type="integer", nullable=true)
     */
    private $idPlan;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_REGISTRO", type="datetime", nullable=true)
     */
    private $fechaRegistro;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_RESERVA", type="datetime", nullable=true)
     */
    private $fechaReserva;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CONFIRMACION", type="datetime", nullable=true)
     */
    private $fechaConfirmacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ATENCION", type="datetime", nullable=true)
     */
    private $fechaAtencion;

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
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_RESERVA", type="text", length=0, nullable=true)
     */
    private $observacionReserva;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PERSONA_CONFIRMA", type="string", length=255, nullable=true)
     */
    private $personaConfirma;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_CONFIRMA", type="text", length=0, nullable=true)
     */
    private $observacionConfirma;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ASISTENCIA", type="smallint", nullable=true)
     */
    private $asistencia;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="PRIMERA_VEZ", type="boolean", nullable=true)
     */
    private $primeraVez;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_OTRO_MEDICO", type="string", length=100, nullable=true)
     */
    private $nombreOtroMedico;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_MODIFICA", type="datetime", nullable=true)
     */
    private $fechaModifica;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="RECEPCIONADO", type="boolean", nullable=true)
     */
    private $recepcionado;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="EN_ESPERA", type="boolean", nullable=true)
     */
    private $enEspera;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_RECEPCION", type="datetime", nullable=true)
     */
    private $fechaRecepcion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_EN_USO", type="datetime", nullable=true)
     */
    private $fechaEnUso;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ES_GES", type="integer", nullable=true)
     */
    private $esGes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PACIENTE_NUEVO", type="integer", nullable=true)
     */
    private $pacienteNuevo;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="SOBRECUPO", type="boolean", nullable=true)
     */
    private $sobrecupo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIGITO_VERIFICADOR_PACIENTE", type="string", length=1, nullable=true)
     */
    private $digitoVerificadorPaciente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IDENTIFICACION_EXTRANJERO", type="string", length=100, nullable=true)
     */
    private $identificacionExtranjero;

    /**
     * @var string|null
     *
     * @ORM\Column(name="URL_MASTER_ZOOM", type="text", length=0, nullable=true)
     */
    private $urlMasterZoom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MEETING_ID", type="string", length=100, nullable=true)
     */
    private $meetingId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FOLIO", type="string", length=100, nullable=true)
     */
    private $folio;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_FUNCIONARIO_ANULA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioFuncionarioAnula;

    /**
     * @var \SubEspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="SubEspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUBESPECIALIDAD_MEDICA", referencedColumnName="ID")
     * })
     */
    private $idSubespecialidadMedica;

    /**
     * @var \EstadoReserva
     *
     * @ORM\ManyToOne(targetEntity="EstadoReserva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_RESERVA", referencedColumnName="ID")
     * })
     */
    private $idEstadoReserva;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_FUNCIONARIO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioFuncionario;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_FUNCIONARIO_RECEPCIONA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioFuncionarioRecepciona;

    /**
     * @var \TipoPrestacionAgenda
     *
     * @ORM\ManyToOne(targetEntity="TipoPrestacionAgenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PRESTACION_AGENDA", referencedColumnName="ID")
     * })
     */
    private $idTipoPrestacionAgenda;

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
     * @var \TipoAgendamientoReserva
     *
     * @ORM\ManyToOne(targetEntity="TipoAgendamientoReserva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_AGENDAMIENTO", referencedColumnName="ID")
     * })
     */
    private $idTipoAgendamiento;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SOLICITA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioSolicita;

    /**
     * @var \TipoIdentificacionExtranjero
     *
     * @ORM\ManyToOne(targetEntity="TipoIdentificacionExtranjero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_IDENTIFICACION_EXTRANJERO", referencedColumnName="ID")
     * })
     */
    private $idTipoIdentificacionExtranjero;

    /**
     * @var \Sexo
     *
     * @ORM\ManyToOne(targetEntity="Sexo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SEXO", referencedColumnName="ID")
     * })
     */
    private $idSexo;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_FUNCIONARIO_CONFIRMA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioFuncionarioConfirma;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UNIDAD", referencedColumnName="ID")
     * })
     */
    private $idUnidad;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ENVIA_CORREO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioEnviaCorreo;

    /**
     * @var \TipoAnulacion
     *
     * @ORM\ManyToOne(targetEntity="TipoAnulacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idTipoAnulacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_PROFESIONAL", referencedColumnName="ID")
     * })
     */
    private $idUsuarioProfesional;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONVENIO", referencedColumnName="ID")
     * })
     */
    private $idConvenio;

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
     * @var \UsuarioExterno
     *
     * @ORM\ManyToOne(targetEntity="UsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_EXTERNO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioExterno;

    /**
     * @var \HorarioConsulta
     *
     * @ORM\ManyToOne(targetEntity="HorarioConsulta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_HORARIO_CONSULTA", referencedColumnName="ID")
     * })
     */
    private $idHorarioConsulta;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_EN_USO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioEnUso;

    /**
     * @var \Cargo
     *
     * @ORM\ManyToOne(targetEntity="Cargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CARGO_AGENDAMIENTO", referencedColumnName="ID")
     * })
     */
    private $idCargoAgendamiento;

    /**
     * @var \Comuna
     *
     * @ORM\ManyToOne(targetEntity="Comuna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_COMUNA", referencedColumnName="ID")
     * })
     */
    private $idComuna;

    /**
     * @var \EspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="EspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESPECIALIDAD_MEDICA", referencedColumnName="ID")
     * })
     */
    private $idEspecialidadMedica;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_MODIFICA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioModifica;

    /**
     * @var \Pais
     *
     * @ORM\ManyToOne(targetEntity="Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAIS", referencedColumnName="ID")
     * })
     */
    private $idPais;

    /**
     * @var \EmpresaSolicitante
     *
     * @ORM\ManyToOne(targetEntity="EmpresaSolicitante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA_SOLICITANTE", referencedColumnName="ID")
     * })
     */
    private $idEmpresaSolicitante;

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
     * Set apellidoPaterno.
     *
     * @param string|null $apellidoPaterno
     *
     * @return ReservaAtencion
     */
    public function setApellidoPaterno($apellidoPaterno = null)
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    /**
     * Get apellidoPaterno.
     *
     * @return string|null
     */
    public function getApellidoPaterno()
    {
        return $this->apellidoPaterno;
    }

    /**
     * Set apellidoMaterno.
     *
     * @param string|null $apellidoMaterno
     *
     * @return ReservaAtencion
     */
    public function setApellidoMaterno($apellidoMaterno = null)
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    /**
     * Get apellidoMaterno.
     *
     * @return string|null
     */
    public function getApellidoMaterno()
    {
        return $this->apellidoMaterno;
    }

    /**
     * Set nombres.
     *
     * @param string|null $nombres
     *
     * @return ReservaAtencion
     */
    public function setNombres($nombres = null)
    {
        $this->nombres = $nombres;

        return $this;
    }

    /**
     * Get nombres.
     *
     * @return string|null
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set telefonoFijo.
     *
     * @param string|null $telefonoFijo
     *
     * @return ReservaAtencion
     */
    public function setTelefonoFijo($telefonoFijo = null)
    {
        $this->telefonoFijo = $telefonoFijo;

        return $this;
    }

    /**
     * Get telefonoFijo.
     *
     * @return string|null
     */
    public function getTelefonoFijo()
    {
        return $this->telefonoFijo;
    }

    /**
     * Set telefonoMovil.
     *
     * @param string|null $telefonoMovil
     *
     * @return ReservaAtencion
     */
    public function setTelefonoMovil($telefonoMovil = null)
    {
        $this->telefonoMovil = $telefonoMovil;

        return $this;
    }

    /**
     * Get telefonoMovil.
     *
     * @return string|null
     */
    public function getTelefonoMovil()
    {
        return $this->telefonoMovil;
    }

    /**
     * Set telefonoContacto.
     *
     * @param string|null $telefonoContacto
     *
     * @return ReservaAtencion
     */
    public function setTelefonoContacto($telefonoContacto = null)
    {
        $this->telefonoContacto = $telefonoContacto;

        return $this;
    }

    /**
     * Get telefonoContacto.
     *
     * @return string|null
     */
    public function getTelefonoContacto()
    {
        return $this->telefonoContacto;
    }

    /**
     * Set direccion.
     *
     * @param string|null $direccion
     *
     * @return ReservaAtencion
     */
    public function setDireccion($direccion = null)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string|null
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set restoDireccion.
     *
     * @param string|null $restoDireccion
     *
     * @return ReservaAtencion
     */
    public function setRestoDireccion($restoDireccion = null)
    {
        $this->restoDireccion = $restoDireccion;

        return $this;
    }

    /**
     * Get restoDireccion.
     *
     * @return string|null
     */
    public function getRestoDireccion()
    {
        return $this->restoDireccion;
    }

    /**
     * Set numero.
     *
     * @param string|null $numero
     *
     * @return ReservaAtencion
     */
    public function setNumero($numero = null)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string|null
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set fechaNacimiento.
     *
     * @param \DateTime|null $fechaNacimiento
     *
     * @return ReservaAtencion
     */
    public function setFechaNacimiento($fechaNacimiento = null)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento.
     *
     * @return \DateTime|null
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set fechaEnvioCorreoConfirmacion.
     *
     * @param \DateTime|null $fechaEnvioCorreoConfirmacion
     *
     * @return ReservaAtencion
     */
    public function setFechaEnvioCorreoConfirmacion($fechaEnvioCorreoConfirmacion = null)
    {
        $this->fechaEnvioCorreoConfirmacion = $fechaEnvioCorreoConfirmacion;

        return $this;
    }

    /**
     * Get fechaEnvioCorreoConfirmacion.
     *
     * @return \DateTime|null
     */
    public function getFechaEnvioCorreoConfirmacion()
    {
        return $this->fechaEnvioCorreoConfirmacion;
    }

    /**
     * Set correoElectronico.
     *
     * @param string|null $correoElectronico
     *
     * @return ReservaAtencion
     */
    public function setCorreoElectronico($correoElectronico = null)
    {
        $this->correoElectronico = $correoElectronico;

        return $this;
    }

    /**
     * Get correoElectronico.
     *
     * @return string|null
     */
    public function getCorreoElectronico()
    {
        return $this->correoElectronico;
    }

    /**
     * Set correoConfirmacion.
     *
     * @param string|null $correoConfirmacion
     *
     * @return ReservaAtencion
     */
    public function setCorreoConfirmacion($correoConfirmacion = null)
    {
        $this->correoConfirmacion = $correoConfirmacion;

        return $this;
    }

    /**
     * Get correoConfirmacion.
     *
     * @return string|null
     */
    public function getCorreoConfirmacion()
    {
        return $this->correoConfirmacion;
    }

    /**
     * Set confirmado.
     *
     * @param int|null $confirmado
     *
     * @return ReservaAtencion
     */
    public function setConfirmado($confirmado = null)
    {
        $this->confirmado = $confirmado;

        return $this;
    }

    /**
     * Get confirmado.
     *
     * @return int|null
     */
    public function getConfirmado()
    {
        return $this->confirmado;
    }

    /**
     * Set rutPaciente.
     *
     * @param int|null $rutPaciente
     *
     * @return ReservaAtencion
     */
    public function setRutPaciente($rutPaciente = null)
    {
        $this->rutPaciente = $rutPaciente;

        return $this;
    }

    /**
     * Get rutPaciente.
     *
     * @return int|null
     */
    public function getRutPaciente()
    {
        return $this->rutPaciente;
    }

    /**
     * Set idPlan.
     *
     * @param int|null $idPlan
     *
     * @return ReservaAtencion
     */
    public function setIdPlan($idPlan = null)
    {
        $this->idPlan = $idPlan;

        return $this;
    }

    /**
     * Get idPlan.
     *
     * @return int|null
     */
    public function getIdPlan()
    {
        return $this->idPlan;
    }

    /**
     * Set fechaRegistro.
     *
     * @param \DateTime|null $fechaRegistro
     *
     * @return ReservaAtencion
     */
    public function setFechaRegistro($fechaRegistro = null)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro.
     *
     * @return \DateTime|null
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set fechaReserva.
     *
     * @param \DateTime|null $fechaReserva
     *
     * @return ReservaAtencion
     */
    public function setFechaReserva($fechaReserva = null)
    {
        $this->fechaReserva = $fechaReserva;

        return $this;
    }

    /**
     * Get fechaReserva.
     *
     * @return \DateTime|null
     */
    public function getFechaReserva()
    {
        return $this->fechaReserva;
    }

    /**
     * Set fechaConfirmacion.
     *
     * @param \DateTime|null $fechaConfirmacion
     *
     * @return ReservaAtencion
     */
    public function setFechaConfirmacion($fechaConfirmacion = null)
    {
        $this->fechaConfirmacion = $fechaConfirmacion;

        return $this;
    }

    /**
     * Get fechaConfirmacion.
     *
     * @return \DateTime|null
     */
    public function getFechaConfirmacion()
    {
        return $this->fechaConfirmacion;
    }

    /**
     * Set fechaAtencion.
     *
     * @param \DateTime|null $fechaAtencion
     *
     * @return ReservaAtencion
     */
    public function setFechaAtencion($fechaAtencion = null)
    {
        $this->fechaAtencion = $fechaAtencion;

        return $this;
    }

    /**
     * Get fechaAtencion.
     *
     * @return \DateTime|null
     */
    public function getFechaAtencion()
    {
        return $this->fechaAtencion;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return ReservaAtencion
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
     * @return ReservaAtencion
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
     * Set observacionReserva.
     *
     * @param string|null $observacionReserva
     *
     * @return ReservaAtencion
     */
    public function setObservacionReserva($observacionReserva = null)
    {
        $this->observacionReserva = $observacionReserva;

        return $this;
    }

    /**
     * Get observacionReserva.
     *
     * @return string|null
     */
    public function getObservacionReserva()
    {
        return $this->observacionReserva;
    }

    /**
     * Set personaConfirma.
     *
     * @param string|null $personaConfirma
     *
     * @return ReservaAtencion
     */
    public function setPersonaConfirma($personaConfirma = null)
    {
        $this->personaConfirma = $personaConfirma;

        return $this;
    }

    /**
     * Get personaConfirma.
     *
     * @return string|null
     */
    public function getPersonaConfirma()
    {
        return $this->personaConfirma;
    }

    /**
     * Set observacionConfirma.
     *
     * @param string|null $observacionConfirma
     *
     * @return ReservaAtencion
     */
    public function setObservacionConfirma($observacionConfirma = null)
    {
        $this->observacionConfirma = $observacionConfirma;

        return $this;
    }

    /**
     * Get observacionConfirma.
     *
     * @return string|null
     */
    public function getObservacionConfirma()
    {
        return $this->observacionConfirma;
    }

    /**
     * Set asistencia.
     *
     * @param int|null $asistencia
     *
     * @return ReservaAtencion
     */
    public function setAsistencia($asistencia = null)
    {
        $this->asistencia = $asistencia;

        return $this;
    }

    /**
     * Get asistencia.
     *
     * @return int|null
     */
    public function getAsistencia()
    {
        return $this->asistencia;
    }

    /**
     * Set primeraVez.
     *
     * @param bool|null $primeraVez
     *
     * @return ReservaAtencion
     */
    public function setPrimeraVez($primeraVez = null)
    {
        $this->primeraVez = $primeraVez;

        return $this;
    }

    /**
     * Get primeraVez.
     *
     * @return bool|null
     */
    public function getPrimeraVez()
    {
        return $this->primeraVez;
    }

    /**
     * Set nombreOtroMedico.
     *
     * @param string|null $nombreOtroMedico
     *
     * @return ReservaAtencion
     */
    public function setNombreOtroMedico($nombreOtroMedico = null)
    {
        $this->nombreOtroMedico = $nombreOtroMedico;

        return $this;
    }

    /**
     * Get nombreOtroMedico.
     *
     * @return string|null
     */
    public function getNombreOtroMedico()
    {
        return $this->nombreOtroMedico;
    }

    /**
     * Set fechaModifica.
     *
     * @param \DateTime|null $fechaModifica
     *
     * @return ReservaAtencion
     */
    public function setFechaModifica($fechaModifica = null)
    {
        $this->fechaModifica = $fechaModifica;

        return $this;
    }

    /**
     * Get fechaModifica.
     *
     * @return \DateTime|null
     */
    public function getFechaModifica()
    {
        return $this->fechaModifica;
    }

    /**
     * Set recepcionado.
     *
     * @param bool|null $recepcionado
     *
     * @return ReservaAtencion
     */
    public function setRecepcionado($recepcionado = null)
    {
        $this->recepcionado = $recepcionado;

        return $this;
    }

    /**
     * Get recepcionado.
     *
     * @return bool|null
     */
    public function getRecepcionado()
    {
        return $this->recepcionado;
    }

    /**
     * Set enEspera.
     *
     * @param bool|null $enEspera
     *
     * @return ReservaAtencion
     */
    public function setEnEspera($enEspera = null)
    {
        $this->enEspera = $enEspera;

        return $this;
    }

    /**
     * Get enEspera.
     *
     * @return bool|null
     */
    public function getEnEspera()
    {
        return $this->enEspera;
    }

    /**
     * Set fechaRecepcion.
     *
     * @param \DateTime|null $fechaRecepcion
     *
     * @return ReservaAtencion
     */
    public function setFechaRecepcion($fechaRecepcion = null)
    {
        $this->fechaRecepcion = $fechaRecepcion;

        return $this;
    }

    /**
     * Get fechaRecepcion.
     *
     * @return \DateTime|null
     */
    public function getFechaRecepcion()
    {
        return $this->fechaRecepcion;
    }

    /**
     * Set fechaEnUso.
     *
     * @param \DateTime|null $fechaEnUso
     *
     * @return ReservaAtencion
     */
    public function setFechaEnUso($fechaEnUso = null)
    {
        $this->fechaEnUso = $fechaEnUso;

        return $this;
    }

    /**
     * Get fechaEnUso.
     *
     * @return \DateTime|null
     */
    public function getFechaEnUso()
    {
        return $this->fechaEnUso;
    }

    /**
     * Set esGes.
     *
     * @param int|null $esGes
     *
     * @return ReservaAtencion
     */
    public function setEsGes($esGes = null)
    {
        $this->esGes = $esGes;

        return $this;
    }

    /**
     * Get esGes.
     *
     * @return int|null
     */
    public function getEsGes()
    {
        return $this->esGes;
    }

    /**
     * Set pacienteNuevo.
     *
     * @param int|null $pacienteNuevo
     *
     * @return ReservaAtencion
     */
    public function setPacienteNuevo($pacienteNuevo = null)
    {
        $this->pacienteNuevo = $pacienteNuevo;

        return $this;
    }

    /**
     * Get pacienteNuevo.
     *
     * @return int|null
     */
    public function getPacienteNuevo()
    {
        return $this->pacienteNuevo;
    }

    /**
     * Set sobrecupo.
     *
     * @param bool|null $sobrecupo
     *
     * @return ReservaAtencion
     */
    public function setSobrecupo($sobrecupo = null)
    {
        $this->sobrecupo = $sobrecupo;

        return $this;
    }

    /**
     * Get sobrecupo.
     *
     * @return bool|null
     */
    public function getSobrecupo()
    {
        return $this->sobrecupo;
    }

    /**
     * Set digitoVerificadorPaciente.
     *
     * @param string|null $digitoVerificadorPaciente
     *
     * @return ReservaAtencion
     */
    public function setDigitoVerificadorPaciente($digitoVerificadorPaciente = null)
    {
        $this->digitoVerificadorPaciente = $digitoVerificadorPaciente;

        return $this;
    }

    /**
     * Get digitoVerificadorPaciente.
     *
     * @return string|null
     */
    public function getDigitoVerificadorPaciente()
    {
        return $this->digitoVerificadorPaciente;
    }

    /**
     * Set identificacionExtranjero.
     *
     * @param string|null $identificacionExtranjero
     *
     * @return ReservaAtencion
     */
    public function setIdentificacionExtranjero($identificacionExtranjero = null)
    {
        $this->identificacionExtranjero = $identificacionExtranjero;

        return $this;
    }

    /**
     * Get identificacionExtranjero.
     *
     * @return string|null
     */
    public function getIdentificacionExtranjero()
    {
        return $this->identificacionExtranjero;
    }

    /**
     * Set urlMasterZoom.
     *
     * @param string|null $urlMasterZoom
     *
     * @return ReservaAtencion
     */
    public function setUrlMasterZoom($urlMasterZoom = null)
    {
        $this->urlMasterZoom = $urlMasterZoom;

        return $this;
    }

    /**
     * Get urlMasterZoom.
     *
     * @return string|null
     */
    public function getUrlMasterZoom()
    {
        return $this->urlMasterZoom;
    }

    /**
     * Set meetingId.
     *
     * @param string|null $meetingId
     *
     * @return ReservaAtencion
     */
    public function setMeetingId($meetingId = null)
    {
        $this->meetingId = $meetingId;

        return $this;
    }

    /**
     * Get meetingId.
     *
     * @return string|null
     */
    public function getMeetingId()
    {
        return $this->meetingId;
    }

    /**
     * @return string|null
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * @param string|null $folio
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;
    }

    /**
     * Set idComuna.
     *
     * @param \Rebsol\HermesBundle\Entity\Comuna|null $idComuna
     *
     * @return ReservaAtencion
     */
    public function setIdComuna(\Rebsol\HermesBundle\Entity\Comuna $idComuna = null)
    {
        $this->idComuna = $idComuna;

        return $this;
    }

    /**
     * Get idComuna.
     *
     * @return \Rebsol\HermesBundle\Entity\Comuna|null
     */
    public function getIdComuna()
    {
        return $this->idComuna;
    }

    /**
     * Set idEspecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica|null $idEspecialidadMedica
     *
     * @return ReservaAtencion
     */
    public function setIdEspecialidadMedica(\Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidadMedica = null)
    {
        $this->idEspecialidadMedica = $idEspecialidadMedica;

        return $this;
    }

    /**
     * Get idEspecialidadMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\EspecialidadMedica|null
     */
    public function getIdEspecialidadMedica()
    {
        return $this->idEspecialidadMedica;
    }

    /**
     * Set idHorarioConsulta.
     *
     * @param \Rebsol\HermesBundle\Entity\HorarioConsulta|null $idHorarioConsulta
     *
     * @return ReservaAtencion
     */
    public function setIdHorarioConsulta(\Rebsol\HermesBundle\Entity\HorarioConsulta $idHorarioConsulta = null)
    {
        $this->idHorarioConsulta = $idHorarioConsulta;

        return $this;
    }

    /**
     * Get idHorarioConsulta.
     *
     * @return \Rebsol\HermesBundle\Entity\HorarioConsulta|null
     */
    public function getIdHorarioConsulta()
    {
        return $this->idHorarioConsulta;
    }

    /**
     * Set idPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\Paciente|null $idPaciente
     *
     * @return ReservaAtencion
     */
    public function setIdPaciente(\Rebsol\HermesBundle\Entity\Paciente $idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\Paciente|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \Rebsol\HermesBundle\Entity\PagoCuenta|null $idPagoCuenta
     *
     * @return ReservaAtencion
     */
    public function setIdPagoCuenta(\Rebsol\HermesBundle\Entity\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \Rebsol\HermesBundle\Entity\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

    /**
     * Set idPrevision.
     *
     * @param \Rebsol\HermesBundle\Entity\Prevision|null $idPrevision
     *
     * @return ReservaAtencion
     */
    public function setIdPrevision(\Rebsol\HermesBundle\Entity\Prevision $idPrevision = null)
    {
        $this->idPrevision = $idPrevision;

        return $this;
    }

    /**
     * Get idPrevision.
     *
     * @return \Rebsol\HermesBundle\Entity\Prevision|null
     */
    public function getIdPrevision()
    {
        return $this->idPrevision;
    }

    /**
     * Set idConvenio.
     *
     * @param \Rebsol\HermesBundle\Entity\Prevision|null $idConvenio
     *
     * @return ReservaAtencion
     */
    public function setIdConvenio(\Rebsol\HermesBundle\Entity\Prevision $idConvenio = null)
    {
        $this->idConvenio = $idConvenio;

        return $this;
    }

    /**
     * Get idConvenio.
     *
     * @return \Rebsol\HermesBundle\Entity\Prevision|null
     */
    public function getIdConvenio()
    {
        return $this->idConvenio;
    }

    /**
     * Set idSubespecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEspecialidadMedica|null $idSubespecialidadMedica
     *
     * @return ReservaAtencion
     */
    public function setIdSubespecialidadMedica(\Rebsol\HermesBundle\Entity\SubEspecialidadMedica $idSubespecialidadMedica = null)
    {
        $this->idSubespecialidadMedica = $idSubespecialidadMedica;

        return $this;
    }

    /**
     * Get idSubespecialidadMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEspecialidadMedica|null
     */
    public function getIdSubespecialidadMedica()
    {
        return $this->idSubespecialidadMedica;
    }

    /**
     * Set idTipoPrestacionAgenda.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrestacionAgenda|null $idTipoPrestacionAgenda
     *
     * @return ReservaAtencion
     */
    public function setIdTipoPrestacionAgenda(\Rebsol\HermesBundle\Entity\TipoPrestacionAgenda $idTipoPrestacionAgenda = null)
    {
        $this->idTipoPrestacionAgenda = $idTipoPrestacionAgenda;

        return $this;
    }

    /**
     * Get idTipoPrestacionAgenda.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPrestacionAgenda|null
     */
    public function getIdTipoPrestacionAgenda()
    {
        return $this->idTipoPrestacionAgenda;
    }

    /**
     * Set idUnidad.
     *
     * @param \Rebsol\HermesBundle\Entity\Unidad|null $idUnidad
     *
     * @return ReservaAtencion
     */
    public function setIdUnidad(\Rebsol\HermesBundle\Entity\Unidad $idUnidad = null)
    {
        $this->idUnidad = $idUnidad;

        return $this;
    }

    /**
     * Get idUnidad.
     *
     * @return \Rebsol\HermesBundle\Entity\Unidad|null
     */
    public function getIdUnidad()
    {
        return $this->idUnidad;
    }

    /**
     * Set idUsuarioFuncionario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioFuncionario
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioFuncionario(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFuncionario = null)
    {
        $this->idUsuarioFuncionario = $idUsuarioFuncionario;

        return $this;
    }

    /**
     * Get idUsuarioFuncionario.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioFuncionario()
    {
        return $this->idUsuarioFuncionario;
    }

    /**
     * Set idUsuarioFuncionarioConfirma.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioFuncionarioConfirma
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioFuncionarioConfirma(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFuncionarioConfirma = null)
    {
        $this->idUsuarioFuncionarioConfirma = $idUsuarioFuncionarioConfirma;

        return $this;
    }

    /**
     * Get idUsuarioFuncionarioConfirma.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioFuncionarioConfirma()
    {
        return $this->idUsuarioFuncionarioConfirma;
    }

    /**
     * Set idUsuarioEnviaCorreo.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioEnviaCorreo
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioEnviaCorreo(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioEnviaCorreo = null)
    {
        $this->idUsuarioEnviaCorreo = $idUsuarioEnviaCorreo;

        return $this;
    }

    /**
     * Get idUsuarioEnviaCorreo.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioEnviaCorreo()
    {
        return $this->idUsuarioEnviaCorreo;
    }

    /**
     * Set idUsuarioFuncionarioAnula.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioFuncionarioAnula
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioFuncionarioAnula(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFuncionarioAnula = null)
    {
        $this->idUsuarioFuncionarioAnula = $idUsuarioFuncionarioAnula;

        return $this;
    }

    /**
     * Get idUsuarioFuncionarioAnula.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioFuncionarioAnula()
    {
        return $this->idUsuarioFuncionarioAnula;
    }

    /**
     * Set idUsuarioSolicita.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioSolicita
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioSolicita(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioSolicita = null)
    {
        $this->idUsuarioSolicita = $idUsuarioSolicita;

        return $this;
    }

    /**
     * Get idUsuarioSolicita.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioSolicita()
    {
        return $this->idUsuarioSolicita;
    }

    /**
     * Set idUsuarioModifica.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioModifica
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioModifica(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioModifica = null)
    {
        $this->idUsuarioModifica = $idUsuarioModifica;

        return $this;
    }

    /**
     * Get idUsuarioModifica.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioModifica()
    {
        return $this->idUsuarioModifica;
    }

    /**
     * Set idUsuarioProfesional.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioProfesional
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioProfesional(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioProfesional = null)
    {
        $this->idUsuarioProfesional = $idUsuarioProfesional;

        return $this;
    }

    /**
     * Get idUsuarioProfesional.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioProfesional()
    {
        return $this->idUsuarioProfesional;
    }

    /**
     * Set idUsuarioFuncionarioRecepciona.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioFuncionarioRecepciona
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioFuncionarioRecepciona(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFuncionarioRecepciona = null)
    {
        $this->idUsuarioFuncionarioRecepciona = $idUsuarioFuncionarioRecepciona;

        return $this;
    }

    /**
     * Get idUsuarioFuncionarioRecepciona.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioFuncionarioRecepciona()
    {
        return $this->idUsuarioFuncionarioRecepciona;
    }

    /**
     * Set idSexo.
     *
     * @param \Rebsol\HermesBundle\Entity\Sexo|null $idSexo
     *
     * @return ReservaAtencion
     */
    public function setIdSexo(\Rebsol\HermesBundle\Entity\Sexo $idSexo = null)
    {
        $this->idSexo = $idSexo;

        return $this;
    }

    /**
     * Get idSexo.
     *
     * @return \Rebsol\HermesBundle\Entity\Sexo|null
     */
    public function getIdSexo()
    {
        return $this->idSexo;
    }

    /**
     * Set idEstadoReserva.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoReserva|null $idEstadoReserva
     *
     * @return ReservaAtencion
     */
    public function setIdEstadoReserva(\Rebsol\HermesBundle\Entity\EstadoReserva $idEstadoReserva = null)
    {
        $this->idEstadoReserva = $idEstadoReserva;

        return $this;
    }

    /**
     * Get idEstadoReserva.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoReserva|null
     */
    public function getIdEstadoReserva()
    {
        return $this->idEstadoReserva;
    }

    /**
     * Set idCargoAgendamiento.
     *
     * @param \Rebsol\HermesBundle\Entity\Cargo|null $idCargoAgendamiento
     *
     * @return ReservaAtencion
     */
    public function setIdCargoAgendamiento(\Rebsol\HermesBundle\Entity\Cargo $idCargoAgendamiento = null)
    {
        $this->idCargoAgendamiento = $idCargoAgendamiento;

        return $this;
    }

    /**
     * Get idCargoAgendamiento.
     *
     * @return \Rebsol\HermesBundle\Entity\Cargo|null
     */
    public function getIdCargoAgendamiento()
    {
        return $this->idCargoAgendamiento;
    }

    /**
     * Set idTipoAgendamiento.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoAgendamientoReserva|null $idTipoAgendamiento
     *
     * @return ReservaAtencion
     */
    public function setIdTipoAgendamiento(\Rebsol\HermesBundle\Entity\TipoAgendamientoReserva $idTipoAgendamiento = null)
    {
        $this->idTipoAgendamiento = $idTipoAgendamiento;

        return $this;
    }

    /**
     * Get idTipoAgendamiento.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoAgendamientoReserva|null
     */
    public function getIdTipoAgendamiento()
    {
        return $this->idTipoAgendamiento;
    }

    /**
     * Set idUsuarioExterno.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuarioExterno|null $idUsuarioExterno
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioExterno(\Rebsol\HermesBundle\Entity\UsuarioExterno $idUsuarioExterno = null)
    {
        $this->idUsuarioExterno = $idUsuarioExterno;

        return $this;
    }

    /**
     * Get idUsuarioExterno.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuarioExterno|null
     */
    public function getIdUsuarioExterno()
    {
        return $this->idUsuarioExterno;
    }

    /**
     * Set idTipoIdentificacionExtranjero.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero|null $idTipoIdentificacionExtranjero
     *
     * @return ReservaAtencion
     */
    public function setIdTipoIdentificacionExtranjero(\Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero $idTipoIdentificacionExtranjero = null)
    {
        $this->idTipoIdentificacionExtranjero = $idTipoIdentificacionExtranjero;

        return $this;
    }

    /**
     * Get idTipoIdentificacionExtranjero.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero|null
     */
    public function getIdTipoIdentificacionExtranjero()
    {
        return $this->idTipoIdentificacionExtranjero;
    }

    /**
     * Set idUsuarioEnUso.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioEnUso
     *
     * @return ReservaAtencion
     */
    public function setIdUsuarioEnUso(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioEnUso = null)
    {
        $this->idUsuarioEnUso = $idUsuarioEnUso;

        return $this;
    }

    /**
     * Get idUsuarioEnUso.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioEnUso()
    {
        return $this->idUsuarioEnUso;
    }

    /**
     * Set idTipoAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoAnulacion|null $idTipoAnulacion
     *
     * @return ReservaAtencion
     */
    public function setIdTipoAnulacion(\Rebsol\HermesBundle\Entity\TipoAnulacion $idTipoAnulacion = null)
    {
        $this->idTipoAnulacion = $idTipoAnulacion;

        return $this;
    }

    /**
     * Get idTipoAnulacion.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoAnulacion|null
     */
    public function getIdTipoAnulacion()
    {
        return $this->idTipoAnulacion;
    }

    /**
     * Set idPais.
     *
     * @param \Rebsol\HermesBundle\Entity\Pais|null $idPais
     *
     * @return ReservaAtencion
     */
    public function setIdPais(\Rebsol\HermesBundle\Entity\Pais $idPais = null)
    {
        $this->idPais = $idPais;

        return $this;
    }

    /**
     * Get idPais.
     *
     * @return \Rebsol\HermesBundle\Entity\Pais|null
     */
    public function getIdPais()
    {
        return $this->idPais;
    }

    /**
     * @return \EmpresaSolicitante
     */
    public function getIdEmpresaSolicitante()
    {
        return $this->idEmpresaSolicitante;
    }

    /**
     * @param \EmpresaSolicitante $idEmpresaSolicitante
     */
    public function setIdEmpresaSolicitante($idEmpresaSolicitante)
    {
        $this->idEmpresaSolicitante = $idEmpresaSolicitante;
    }

    /**
     * @ORM\PostPersist
     */
    public function trigerTildesPostPersist()
    {
        $this->nombres         = $this->limpiarCampos($this->nombres);
        $this->apellidoPaterno = $this->limpiarCampos($this->apellidoPaterno);
        $this->apellidoMaterno = $this->limpiarCampos($this->apellidoMaterno);
    }

    /**
     * @ORM\PostPersist
     */
    public function trigerLogNew()
    {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $oReservaAtencionLog = new ReservaAtencionLog();
        $oReservaAtencionLog->setIdReservaAtencion($this);
        $oReservaAtencionLog->setDescripcion('Agendamiento');
        $oReservaAtencionLog->setIdHorarioConsultaAntiguo(null);
        $oReservaAtencionLog->setIdHorarioConsultaNuevo($this->getIdHorarioConsulta());
        $oReservaAtencionLog->setFechaRegistro(new \DateTime());
        $oReservaAtencionTipoLog = $em->getRepository("RebsolHermesBundle:ReservaAtencionTipoLog")->find(1);
        $oReservaAtencionLog->setIdReservaTipoLog($oReservaAtencionTipoLog);
        $oReservaAtencionLog->setIdUsuarioModifica($this->getIdUsuarioFuncionario());
        $em->persist($oReservaAtencionLog);
        $em->flush();

        if ($this->confirmado == 1) {
            $oReservaAtencionLogConfirmacion = new ReservaAtencionLog();
            $oReservaAtencionLogConfirmacion->setIdReservaAtencion($this);
            $oReservaAtencionLogConfirmacion->setIdHorarioConsultaAntiguo(null);
            $oReservaAtencionLogConfirmacion->setIdHorarioConsultaNuevo($this->getIdHorarioConsulta());
            $oReservaAtencionLogConfirmacion->setFechaRegistro(new \DateTime());
            $oReservaAtencionTipoLog = $em->getRepository("RebsolHermesBundle:ReservaAtencionTipoLog")->find(6);
            $oReservaAtencionLogConfirmacion->setIdReservaTipoLog($oReservaAtencionTipoLog);
            $oReservaAtencionLogConfirmacion->setIdUsuarioModifica($this->getIdUsuarioFuncionario());
            $em->persist($oReservaAtencionLogConfirmacion);
            $em->flush();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function trigerTildesPreUpdate()
    {
        $this->nombres         = $this->limpiarCampos($this->nombres);
        $this->apellidoPaterno = $this->limpiarCampos($this->apellidoPaterno);
        $this->apellidoMaterno = $this->limpiarCampos($this->apellidoMaterno);
    }

    /**
     * @ORM\PreUpdate
     */
    public function trigerLogUpdate()
    {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Modificación del campo confirmado
        if ($eventArgs->hasChangedField('confirmado')) {

            $oReservaAtencionLogConfirmacion = new ReservaAtencionLog();

            if ($this->confirmado === 1) {
                $oReservaAtencionTipoLog = $em->getRepository("RebsolHermesBundle:ReservaAtencionTipoLog")->find(6);
                $oReservaAtencionLogConfirmacion->setDescripcion('Confirmar');

            } else {
                $oReservaAtencionTipoLog = $em->getRepository("RebsolHermesBundle:ReservaAtencionTipoLog")->find(5);
                $oReservaAtencionLogConfirmacion->setDescripcion('Quitar Confirmación');
            }

            $oReservaAtencionLogConfirmacion->setIdReservaAtencion($this);
            $oReservaAtencionLogConfirmacion->setIdHorarioConsultaAntiguo(null);
            $oReservaAtencionLogConfirmacion->setIdHorarioConsultaNuevo($this->getIdHorarioConsulta());
            $oReservaAtencionLogConfirmacion->setFechaRegistro(new \DateTime());
            $oReservaAtencionLogConfirmacion->setIdReservaTipoLog($oReservaAtencionTipoLog);
            $oReservaAtencionLogConfirmacion->setIdUsuarioModifica($this->getIdUsuarioFuncionario());
            $em->persist($oReservaAtencionLogConfirmacion);
        }

        if ( $eventArgs->hasChangedField('idEstadoReserva') ){

            if ($eventArgs->getOldValue('idEstadoReserva')->getId() == 1 && $eventArgs->getNewValue('idEstadoReserva')->getId() == 0) {
                $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
                $oReservaAtencionLog = new ReservaAtencionLog();
                $oReservaAtencionLog->setIdReservaAtencion($this);
                $oReservaAtencionLog->setIdHorarioConsultaAntiguo($this->retornarValor($eventArgs, 'idHorarioConsulta', false));
                $oReservaAtencionLog->setIdHorarioConsultaNuevo($this->retornarValor($eventArgs, 'idHorarioConsulta', true));
                $oReservaAtencionLog->setFechaRegistro(new \DateTime());
                $oReservaAtencionTipoLog = $em->getRepository("RebsolHermesBundle:ReservaAtencionTipoLog")->find(3);
                $oReservaAtencionLog->setIdReservaTipoLog($oReservaAtencionTipoLog);
                $oReservaAtencionLog->setIdUsuarioModifica($this->retornarValor($eventArgs, 'idUsuarioModifica', true));
                $em->persist($oReservaAtencionLog);
            }
        }

        if ($eventArgs->hasChangedField('nombres') || $eventArgs->hasChangedField('apellidoPaterno') || $eventArgs->hasChangedField('apellidoMaterno')
        || $eventArgs->hasChangedField('telefonoFijo') || $eventArgs->hasChangedField('telefonoContacto') || $eventArgs->hasChangedField('telefonoMovil')
        || $eventArgs->hasChangedField('direccion') || $eventArgs->hasChangedField('restoDireccion') || $eventArgs->hasChangedField('numero') || $eventArgs->hasChangedField('correoElectronico')
        || $eventArgs->hasChangedField('observacionReserva') ){
            $oReservaAtencionLog = new ReservaAtencionLog();
            $oReservaAtencionLog->setIdReservaAtencion($this);
            $oReservaAtencionLog->setFechaRegistro(new \DateTime());
            $oReservaAtencionTipoLog = $em->getRepository("RebsolHermesBundle:ReservaAtencionTipoLog")->find(2);
            $oReservaAtencionLog->setIdReservaTipoLog($oReservaAtencionTipoLog);
            $oReservaAtencionLog->setIdUsuarioModifica($this->retornarValor($eventArgs, 'idUsuarioModifica', true));
            $em->persist($oReservaAtencionLog);
        }
    }

    public function limpiarCampos($campo) {
        // Quitar las tildes del campo
        $campo = $this->quitarTildes($campo);

        //Transformar el campo en mayúsculas
        $campo = mb_strtoupper($campo);

        return $campo;
    }

    private function quitarTildes($string) {
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
            $string
            );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('E', 'E', 'E', 'E', 'E', 'E', 'E', 'E'),
            $string
            );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('I', 'I', 'I', 'I', 'I', 'I', 'I', 'I'),
            $string
            );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('O', 'O', 'O', 'O', 'O', 'O', 'O', 'O'),
            $string
            );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('U', 'U', 'U', 'U', 'U', 'U', 'U', 'U'),
            $string
            );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('Ñ', 'Ñ', 'c', 'C',),
            $string
            );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                "."),
            '',
            $string
            );
        return $string;
    }

    private function retornarValor($eventArgs, $campo, $isNew) {
        if ($eventArgs->hasChangedField($campo)) {
            if ($isNew) {
                $return = $eventArgs->getNewValue($campo);
            } else {
                $return = $eventArgs->getOldValue($campo);
            }
        } else {
            $return = $this->$campo;
        }

        return $return;
    }
}
