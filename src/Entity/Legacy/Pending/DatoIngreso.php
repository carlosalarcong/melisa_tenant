<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatoIngreso
 *
 * @ORM\Table(name="dato_ingreso")
 * @ORM\Entity
 *
 */
class DatoIngreso
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
     * @ORM\Column(name="FECHA_INGRESO", type="datetime", nullable=true)
     */
    private $fechaIngreso;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_PREADMISION", type="datetime", nullable=true)
     */
    private $fechaPreadmision;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="date", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var int
     *
     * @ORM\Column(name="NUMERO", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var bool
     *
     * @ORM\Column(name="INGRESO_QUIRURGICO", type="boolean", nullable=false)
     */
    private $ingresoQuirurgico;

    /**
     * @var bool
     *
     * @ORM\Column(name="ORDEN_MEDICA", type="boolean", nullable=false)
     */
    private $ordenMedica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="string", length=240, nullable=true)
     */
    private $observacion;

    /**
     * @var string
     *
     * @ORM\Column(name="EMERGENCIA_AVISO", type="string", length=100, nullable=true)
     */
    private $emergenciaAviso;

    /**
     * @var string
     *
     * @ORM\Column(name="EMERGENCIA_TELEFONO", type="string", length=10, nullable=true)
     */
    private $emergenciaTelefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ARCHIVO_ORDEN_MEDICA", type="string", length=50, nullable=true)
     */
    private $nombreArchivoOrdenMedica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OTRO_ORIGEN", type="string", length=255, nullable=true)
     */
    private $otroOrigen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_ANULACION", type="string", length=2000, nullable=true)
     */
    private $observacionAnulacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="CON_HONORARIOS", type="boolean", nullable=false, options={"default"="1"})
     */
    private $conHonorarios = true;

    /**
     * @var int|null
     *
     * @ORM\Column(name="DAU", type="integer", nullable=true)
     */
    private $dau;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_PREADMISION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioPreAdmision;

    /**
     * @var \RelCamaPaciente
     *
     * @ORM\ManyToOne(targetEntity="RelCamaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REL_CAMA_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idRelCamaPaciente;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAnulacion;

    /**
     * @var \TipoCuenta
     *
     * @ORM\ManyToOne(targetEntity="TipoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idTipoCuenta;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO", referencedColumnName="ID")
     * })
     */
    private $idServicio;

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
     * @var \PrPlan
     *
     * @ORM\ManyToOne(targetEntity="PrPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PR_PLAN", referencedColumnName="ID")
     * })
     */
    private $idPrPlan;

    /**
     * @var \PqPlan
     *
     * @ORM\ManyToOne(targetEntity="PqPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PQ_PLAN", referencedColumnName="ID")
     * })
     */
    private $idPqPlan;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_INGRESO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioIngreso;

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
     *   @ORM\JoinColumn(name="ID_PROFESIONAL", referencedColumnName="ID")
     * })
     */
    private $idProfesional;

    /**
     * @var \MotivoAnulacionIngreso
     *
     * @ORM\ManyToOne(targetEntity="MotivoAnulacionIngreso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MOTIVO_ANULACION_INGRESO", referencedColumnName="ID")
     * })
     */
    private $idMotivoAnulacionIngreso;

    /**
     * @var \PqPaqueteCirugia
     *
     * @ORM\ManyToOne(targetEntity="PqPaqueteCirugia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_CIRUGIA", referencedColumnName="ID")
     * })
     */
    private $idPaqueteCirugia;

    /**
     * @var \Origen
     *
     * @ORM\ManyToOne(targetEntity="Origen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ORIGEN", referencedColumnName="ID")
     * })
     */
    private $idOrigen;

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
     * @var \Presupuesto
     *
     * @ORM\ManyToOne(targetEntity="Presupuesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PRESUPUESTO", referencedColumnName="ID")
     * })
     */
    private $idPresupuesto;

    /**
     * @var \EstadoIngreso
     *
     * @ORM\ManyToOne(targetEntity="EstadoIngreso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_INGRESO", referencedColumnName="ID")
     * })
     */
    private $idEstadoIngreso;

    /**
     * @var \ParentescoPersona
     *
     * @ORM\ManyToOne(targetEntity="ParentescoPersona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PARENTESCO_PERSONA", referencedColumnName="ID")
     * })
     */

    private $idParentescoPersona;


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
     * @var string|null
     *
     * @ORM\Column(name="MEDICO_DERIVADOR", type="string", length=255, nullable=true)
     */

        private $medicoDerivador;

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
     * Set fechaIngreso.
     *
     * @param \DateTime|null $fechaIngreso
     *
     * @return DatoIngreso
     */
    public function setFechaIngreso($fechaIngreso = null)
    {
        $this->fechaIngreso = $fechaIngreso;

        return $this;
    }

    /**
     * Get fechaIngreso.
     *
     * @return \DateTime|null
     */
    public function getFechaIngreso()
    {
        return $this->fechaIngreso;
    }

    /**
     * Set fechaPreadmision.
     *
     * @param \DateTime|null $fechaPreadmision
     *
     * @return DatoIngreso
     */
    public function setFechaPreadmision($fechaPreadmision = null)
    {
        $this->fechaPreadmision = $fechaPreadmision;

        return $this;
    }

    /**
     * Get fechaPreadmision.
     *
     * @return \DateTime|null
     */
    public function getFechaPreadmision()
    {
        return $this->fechaPreadmision;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return DatoIngreso
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
     * Set numero.
     *
     * @param int $numero
     *
     * @return DatoIngreso
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set dau.
     *
     * @param int|null $dau
     *
     * @return DatoIngreso
     */
    public function setDau($dau = null)
    {
        $this->dau = $dau;

        return $this;
    }

    /**
     * Get dau.
     *
     * @return int|null
     */
    public function getDau()
    {
        return $this->dau;
    }

    /**
     * Set ingresoQuirurgico.
     *
     * @param bool $ingresoQuirurgico
     *
     * @return DatoIngreso
     */
    public function setIngresoQuirurgico($ingresoQuirurgico)
    {
        $this->ingresoQuirurgico = $ingresoQuirurgico;

        return $this;
    }

    /**
     * Get ingresoQuirurgico.
     *
     * @return bool
     */
    public function getIngresoQuirurgico()
    {
        return $this->ingresoQuirurgico;
    }

    /**
     * Set ordenMedica.
     *
     * @param bool $ordenMedica
     *
     * @return DatoIngreso
     */
    public function setOrdenMedica($ordenMedica)
    {
        $this->ordenMedica = $ordenMedica;

        return $this;
    }

    /**
     * Get ordenMedica.
     *
     * @return bool
     */
    public function getOrdenMedica()
    {
        return $this->ordenMedica;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return DatoIngreso
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
     * Set emergenciaAviso.
     *
     * @param string $emergenciaAviso
     *
     * @return DatoIngreso
     */
    public function setEmergenciaAviso($emergenciaAviso)
    {
        $this->emergenciaAviso = $emergenciaAviso;

        return $this;
    }

    /**
     * Get emergenciaAviso.
     *
     * @return string
     */
    public function getEmergenciaAviso()
    {
        return $this->emergenciaAviso;
    }

    /**
     * Set emergenciaTelefono.
     *
     * @param string $emergenciaTelefono
     *
     * @return DatoIngreso
     */
    public function setEmergenciaTelefono($emergenciaTelefono)
    {
        $this->emergenciaTelefono = $emergenciaTelefono;

        return $this;
    }

    /**
     * Get emergenciaTelefono.
     *
     * @return string
     */
    public function getEmergenciaTelefono()
    {
        return $this->emergenciaTelefono;
    }

    /**
     * Set nombreArchivoOrdenMedica.
     *
     * @param string|null $nombreArchivoOrdenMedica
     *
     * @return DatoIngreso
     */
    public function setNombreArchivoOrdenMedica($nombreArchivoOrdenMedica = null)
    {
        $this->nombreArchivoOrdenMedica = $nombreArchivoOrdenMedica;

        return $this;
    }

    /**
     * Get nombreArchivoOrdenMedica.
     *
     * @return string|null
     */
    public function getNombreArchivoOrdenMedica()
    {
        return $this->nombreArchivoOrdenMedica;
    }

    /**
     * Set otroOrigen.
     *
     * @param string|null $otroOrigen
     *
     * @return DatoIngreso
     */
    public function setOtroOrigen($otroOrigen = null)
    {
        $this->otroOrigen = $otroOrigen;

        return $this;
    }

    /**
     * Get otroOrigen.
     *
     * @return string|null
     */
    public function getOtroOrigen()
    {
        return $this->otroOrigen;
    }

    /**
     * Set observacionAnulacion.
     *
     * @param string|null $observacionAnulacion
     *
     * @return DatoIngreso
     */
    public function setObservacionAnulacion($observacionAnulacion = null)
    {
        $this->observacionAnulacion = $observacionAnulacion;

        return $this;
    }

    /**
     * Get observacionAnulacion.
     *
     * @return string|null
     */
    public function getObservacionAnulacion()
    {
        return $this->observacionAnulacion;
    }

    /**
     * Set conHonorarios.
     *
     * @param bool $conHonorarios
     *
     * @return DatoIngreso
     */
    public function setConHonorarios($conHonorarios)
    {
        $this->conHonorarios = $conHonorarios;

        return $this;
    }

    /**
     * Get conHonorarios.
     *
     * @return bool
     */
    public function getConHonorarios()
    {
        return $this->conHonorarios;
    }

    /**
     * Set idEstadoIngreso.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoIngreso $idEstadoIngreso
     *
     * @return DatoIngreso
     */
    public function setIdEstadoIngreso(\Rebsol\HermesBundle\Entity\EstadoIngreso $idEstadoIngreso)
    {
        $this->idEstadoIngreso = $idEstadoIngreso;

        return $this;
    }

    /**
     * Get idEstadoIngreso.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoIngreso
     */
    public function getIdEstadoIngreso()
    {
        return $this->idEstadoIngreso;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return DatoIngreso
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idServicio.
     *
     * @param \Rebsol\HermesBundle\Entity\Servicio $idServicio
     *
     * @return DatoIngreso
     */
    public function setIdServicio(\Rebsol\HermesBundle\Entity\Servicio $idServicio)
    {
        $this->idServicio = $idServicio;

        return $this;
    }

    /**
     * Get idServicio.
     *
     * @return \Rebsol\HermesBundle\Entity\Servicio
     */
    public function getIdServicio()
    {
        return $this->idServicio;
    }

    /**
     * Set idEspecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidadMedica
     *
     * @return DatoIngreso
     */
    public function setIdEspecialidadMedica(\Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidadMedica)
    {
        $this->idEspecialidadMedica = $idEspecialidadMedica;

        return $this;
    }

    /**
     * Get idEspecialidadMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\EspecialidadMedica
     */
    public function getIdEspecialidadMedica()
    {
        return $this->idEspecialidadMedica;
    }

    /**
     * Set idPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\Paciente $idPaciente
     *
     * @return DatoIngreso
     */
    public function setIdPaciente(\Rebsol\HermesBundle\Entity\Paciente $idPaciente)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\Paciente
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idTipoCuenta.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoCuenta $idTipoCuenta
     *
     * @return DatoIngreso
     */
    public function setIdTipoCuenta(\Rebsol\HermesBundle\Entity\TipoCuenta $idTipoCuenta)
    {
        $this->idTipoCuenta = $idTipoCuenta;

        return $this;
    }

    /**
     * Get idTipoCuenta.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoCuenta
     */
    public function getIdTipoCuenta()
    {
        return $this->idTipoCuenta;
    }

    /**
     * Set idRelCamaPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\RelCamaPaciente|null $idRelCamaPaciente
     *
     * @return DatoIngreso
     */
    public function setIdRelCamaPaciente(\Rebsol\HermesBundle\Entity\RelCamaPaciente $idRelCamaPaciente = null)
    {
        $this->idRelCamaPaciente = $idRelCamaPaciente;

        return $this;
    }

    /**
     * Get idRelCamaPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\RelCamaPaciente|null
     */
    public function getIdRelCamaPaciente()
    {
        return $this->idRelCamaPaciente;
    }

    /**
     * Set idUsuarioIngreso.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioIngreso
     *
     * @return DatoIngreso
     */
    public function setIdUsuarioIngreso(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioIngreso = null)
    {
        $this->idUsuarioIngreso = $idUsuarioIngreso;

        return $this;
    }

    /**
     * Get idUsuarioIngreso.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioIngreso()
    {
        return $this->idUsuarioIngreso;
    }

    /**
     * Set idUsuarioPreAdmision.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioPreAdmision
     *
     * @return DatoIngreso
     */
    public function setIdUsuarioPreAdmision(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioPreAdmision = null)
    {
        $this->idUsuarioPreAdmision = $idUsuarioPreAdmision;

        return $this;
    }

    /**
     * Get idUsuarioPreAdmision.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioPreAdmision()
    {
        return $this->idUsuarioPreAdmision;
    }

    /**
     * Set idProfesional.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idProfesional
     *
     * @return DatoIngreso
     */
    public function setIdProfesional(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idProfesional)
    {
        $this->idProfesional = $idProfesional;

        return $this;
    }

    /**
     * Get idProfesional.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdProfesional()
    {
        return $this->idProfesional;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return DatoIngreso
     */
    public function setIdUsuarioAnulacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioAnulacion = null)
    {
        $this->idUsuarioAnulacion = $idUsuarioAnulacion;

        return $this;
    }

    /**
     * Get idUsuarioAnulacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAnulacion()
    {
        return $this->idUsuarioAnulacion;
    }

    /**
     * Set idMotivoAnulacionIngreso.
     *
     * @param \Rebsol\HermesBundle\Entity\MotivoAnulacionIngreso|null $idMotivoAnulacionIngreso
     *
     * @return DatoIngreso
     */
    public function setIdMotivoAnulacionIngreso(\Rebsol\HermesBundle\Entity\MotivoAnulacionIngreso $idMotivoAnulacionIngreso = null)
    {
        $this->idMotivoAnulacionIngreso = $idMotivoAnulacionIngreso;

        return $this;
    }

    /**
     * Get idMotivoAnulacionIngreso.
     *
     * @return \Rebsol\HermesBundle\Entity\MotivoAnulacionIngreso|null
     */
    public function getIdMotivoAnulacionIngreso()
    {
        return $this->idMotivoAnulacionIngreso;
    }

    /**
     * Set idPresupuesto.
     *
     * @param \Rebsol\HermesBundle\Entity\Presupuesto|null $idPresupuesto
     *
     * @return DatoIngreso
     */
    public function setIdPresupuesto(\Rebsol\HermesBundle\Entity\Presupuesto $idPresupuesto = null)
    {
        $this->idPresupuesto = $idPresupuesto;

        return $this;
    }

    /**
     * Get idPresupuesto.
     *
     * @return \Rebsol\HermesBundle\Entity\Presupuesto|null
     */
    public function getIdPresupuesto()
    {
        return $this->idPresupuesto;
    }

    /**
     * Set idPabAgenda.
     *
     * @param \Rebsol\HermesBundle\Entity\PabAgenda|null $idPabAgenda
     *
     * @return DatoIngreso
     */
    public function setIdPabAgenda(\Rebsol\HermesBundle\Entity\PabAgenda $idPabAgenda = null)
    {
        $this->idPabAgenda = $idPabAgenda;

        return $this;
    }

    /**
     * Get idPabAgenda.
     *
     * @return \Rebsol\HermesBundle\Entity\PabAgenda|null
     */
    public function getIdPabAgenda()
    {
        return $this->idPabAgenda;
    }

    /**
     * Set idOrigen.
     *
     * @param \Rebsol\HermesBundle\Entity\Origen|null $idOrigen
     *
     * @return DatoIngreso
     */
    public function setIdOrigen(\Rebsol\HermesBundle\Entity\Origen $idOrigen = null)
    {
        $this->idOrigen = $idOrigen;

        return $this;
    }

    /**
     * Get idOrigen.
     *
     * @return \Rebsol\HermesBundle\Entity\Origen|null
     */
    public function getIdOrigen()
    {
        return $this->idOrigen;
    }

    /**
     * Set idPrPlan.
     *
     * @param \Rebsol\HermesBundle\Entity\PrPlan|null $idPrPlan
     *
     * @return DatoIngreso
     */
    public function setIdPrPlan(\Rebsol\HermesBundle\Entity\PrPlan $idPrPlan = null)
    {
        $this->idPrPlan = $idPrPlan;

        return $this;
    }

    /**
     * Get idPrPlan.
     *
     * @return \Rebsol\HermesBundle\Entity\PrPlan|null
     */
    public function getIdPrPlan()
    {
        return $this->idPrPlan;
    }

    /**
     * Set idPaqueteCirugia.
     *
     * @param \Rebsol\HermesBundle\Entity\PqPaqueteCirugia|null $idPaqueteCirugia
     *
     * @return DatoIngreso
     */
    public function setIdPaqueteCirugia(\Rebsol\HermesBundle\Entity\PqPaqueteCirugia $idPaqueteCirugia = null)
    {
        $this->idPaqueteCirugia = $idPaqueteCirugia;

        return $this;
    }

    /**
     * Get idPaqueteCirugia.
     *
     * @return \Rebsol\HermesBundle\Entity\PqPaqueteCirugia|null
     */
    public function getIdPaqueteCirugia()
    {
        return $this->idPaqueteCirugia;
    }

    /**
     * @return \ParentescoPersona
     */
    public function getIdParentescoPersona()
    {
        return $this->idParentescoPersona;
    }

    /**
     * @param \ParentescoPersona $idParentescoPersona
     */
    public function setIdParentescoPersona($idParentescoPersona)
    {
        $this->idParentescoPersona = $idParentescoPersona;
    }



    /**
     * @return string|null
     */
    public function getMedicoDerivador()
    {
        return $this->medicoDerivador;
    }

    /**
     * @param string|null $medicoDerivador
     */
    public function setMedicoDerivador($medicoDerivador)
    {
        $this->medicoDerivador = $medicoDerivador;
    }

    /**
     * Get idPqPlan.
     *
     * @return \Rebsol\HermesBundle\Entity\PqPlan|null
     */
    public function getIdPqPlan()
    {
        return $this->idPqPlan;
    }

    /**
     * Set idPqPlan.
     *
     * @param \Rebsol\HermesBundle\Entity\PqPlan|null  $idPqPlan
     *
     * @return DatoIngreso
     */
    public function setIdPqPlan(\Rebsol\HermesBundle\Entity\PqPlan $idPqPlan = null)
    {
        $this->idPqPlan = $idPqPlan;
        return $this;
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

}
