<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConsultaMedicaFc
 *
 * @ORM\Table(name="consulta_medica_fc")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ConsultaMedicaFcRepository")
 */
class ConsultaMedicaFc
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
     * @ORM\Column(name="NUMERACION", type="integer", nullable=false)
     */
    private $numeracion;

    /**
     * @var int
     *
     * @ORM\Column(name="CODIGO_DETALLE_CONSULTA_MEDICA", type="integer", nullable=false)
     */
    private $codigoDetalleConsultaMedica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DETALLE_CONSULTA_MEDICA", type="text", length=0, nullable=true)
     */
    private $detalleConsultaMedica;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=true)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ACTUALIZACION", type="datetime", nullable=true)
     */
    private $fechaActualizacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ROL_USUARIO", type="string", length=150, nullable=true)
     */
    private $nombreRolUsuario;

    /**
     * @var \FormularioEnoInformacion
     *
     * @ORM\ManyToOne(targetEntity="FormularioEnoInformacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FORMULARIO_ENO_INFORMACION", referencedColumnName="ID")
     * })
     */
    private $idFormularioEnoInformacion;

    /**
     * @var \EstadoNotificacion
     *
     * @ORM\ManyToOne(targetEntity="EstadoNotificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_NOTIFICACION_ENO", referencedColumnName="ID")
     * })
     */
    private $idEstadoNotificacionEno;

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
     * @var \ReservaAtencion
     *
     * @ORM\ManyToOne(targetEntity="ReservaAtencion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RESERVA_ATENCION", referencedColumnName="ID")
     * })
     */
    private $idReservaAtencion;

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
     * @var \Patologia
     *
     * @ORM\ManyToOne(targetEntity="Patologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PATOLOGIA", referencedColumnName="ID",  nullable = true)
     * })
     */
    private $idPatologia;

    /**
     * @var \Eno
     *
     * @ORM\ManyToOne(targetEntity="Eno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ENO", referencedColumnName="ID")
     * })
     */
    private $idEno;

    /**
     * @var \TipoAtencionFc
     *
     * @ORM\ManyToOne(targetEntity="TipoAtencionFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_ATENCION", referencedColumnName="ID")
     * })
     */
    private $idTipoAtencion;

    /**
     * @var \DatoIngreso
     *
     * @ORM\ManyToOne(targetEntity="DatoIngreso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_DATO_INGRESO", referencedColumnName="ID")
     * })
     */
    private $idDatoIngreso;

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
     * @var \Paciente
     *
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPaciente;

    /**
     * @var \PrecisionDiagnostica
     *
     * @ORM\ManyToOne(targetEntity="PrecisionDiagnostica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PRECISION_DIAGNOSTICA", referencedColumnName="ID")
     * })
     */
    private $idPrecisionDiagnostica;

    /**
     * @var \EstadoDiagnostico
     *
     * @ORM\ManyToOne(targetEntity="EstadoDiagnostico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_DIAGNOSTICO", referencedColumnName="ID")
     * })
     */
    private $idEstadoDiagnostico;

    /**
     * @var \ObstetriciaPacienteFc
     *
     * @ORM\ManyToOne(targetEntity="ObstetriciaPacienteFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_OBSTETRICIA_PACIENTE_FC", referencedColumnName="ID")
     * })
     */
    private $idObstetriciaPacienteFc;

    /**
     * @var \EstadoDiagnosticoUrgencia
     *
     * @ORM\ManyToOne(targetEntity="EstadoDiagnosticoUrgencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_DIAGNOSTICO_URGENCIA", referencedColumnName="ID")
     * })
     */
    private $idEstadoDiagnosticoUrgencia;

    /**
     * @var \ItemAtencionFc
     *
     * @ORM\ManyToOne(targetEntity="ItemAtencionFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ITEM_ATENCION", referencedColumnName="ID")
     * })
     */
    private $idItemAtencion;

    /**
     * @var \EstadoNotificacion
     *
     * @ORM\ManyToOne(targetEntity="EstadoNotificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_NOTIFICACION_GES", referencedColumnName="ID")
     * })
     */
    private $idEstadoNotificacionGes;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ENMIENDA", type="datetime", nullable=true)
     */
    private $fechaEnmienda;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ENMIENDA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioEnmienda;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ROL_USUARIO_ENMIENDA", type="string", length=150, nullable=true)
     */
    private $nombreRolUsuarioEnmienda;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ESTADO_ENMIENDA", type="boolean", nullable=true, options={"default": 0})
     */
    private $estadoEnmienda;

    /**
     * @var \ConsultaMedicaFc
     *
     * @ORM\ManyToOne(targetEntity="ConsultaMedicaFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONSULTA_MEDICA_FC_ENMIENDA", referencedColumnName="ID")
     * })
     */
    private $idConsultaMedicaFcEnmienda;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_GES_ONCOLOGICO", type="boolean", nullable=true)
     */
    private $esGesOncologico;

    /**
     * @var \EtapaGesOncologica
     *
     * @ORM\ManyToOne(targetEntity="EtapaGesOncologica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ETAPA_GES_ONCOLOGICA", referencedColumnName="ID")
     * })
     */
    private $idEtapaGesOncologica;

    /**
     * Set id
     *
     * @param int $id
     * @return ConsultaMedicaFc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set numeracion.
     *
     * @param int $numeracion
     *
     * @return ConsultaMedicaFc
     */
    public function setNumeracion($numeracion)
    {
        $this->numeracion = $numeracion;

        return $this;
    }

    /**
     * Get numeracion.
     *
     * @return int
     */
    public function getNumeracion()
    {
        return $this->numeracion;
    }

    /**
     * Set codigoDetalleConsultaMedica.
     *
     * @param int $codigoDetalleConsultaMedica
     *
     * @return ConsultaMedicaFc
     */
    public function setCodigoDetalleConsultaMedica($codigoDetalleConsultaMedica)
    {
        $this->codigoDetalleConsultaMedica = $codigoDetalleConsultaMedica;

        return $this;
    }

    /**
     * Get codigoDetalleConsultaMedica.
     *
     * @return int
     */
    public function getCodigoDetalleConsultaMedica()
    {
        return $this->codigoDetalleConsultaMedica;
    }

    /**
     * Set detalleConsultaMedica.
     *
     * @param string|null $detalleConsultaMedica
     *
     * @return ConsultaMedicaFc
     */
    public function setDetalleConsultaMedica($detalleConsultaMedica = null)
    {
        $this->detalleConsultaMedica = $detalleConsultaMedica;

        return $this;
    }

    /**
	 * Get detalleConsultaMedica
	 *
	 * @return string
	 */
	public function getDetalleConsultaMedica() {
		if ($this->getIdItemAtencion() != null) {
			if ($this->getIdItemAtencion()->getId() == 2) {
				// Solo para los Diagnósticos
				$arrDetalleConsultaMedica = explode('#@TEXT_DIAGNOSTICO@#', $this->detalleConsultaMedica);

				if (COUNT($arrDetalleConsultaMedica) == 2) {
					$strReturn = $arrDetalleConsultaMedica[0];
					if ($arrDetalleConsultaMedica[1] != '') {
						$strReturn .= ', '.$arrDetalleConsultaMedica[1];
					}
					return $strReturn;
				} else {
					return $arrDetalleConsultaMedica[0];
				}
			}
		}

		return $this->detalleConsultaMedica;
	}

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime|null $fechaCreacion
     *
     * @return ConsultaMedicaFc
     */
    public function setFechaCreacion($fechaCreacion = null)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime|null
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaActualizacion.
     *
     * @param \DateTime|null $fechaActualizacion
     *
     * @return ConsultaMedicaFc
     */
    public function setFechaActualizacion($fechaActualizacion = null)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Get fechaActualizacion.
     *
     * @return \DateTime|null
     */
    public function getFechaActualizacion()
    {
        return $this->fechaActualizacion;
    }

    /**
     * @return string|null
     */
    public function getNombreRolUsuario()
    {
        return $this->nombreRolUsuario;
    }

    /**
     * @param string|null $nombreRolUsuario
     */
    public function setNombreRolUsuario($nombreRolUsuario)
    {
        $this->nombreRolUsuario = $nombreRolUsuario;
    }

    /**
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuario
     *
     * @return ConsultaMedicaFc
     */
    public function setIdUsuario(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idEno.
     *
     * @param \Rebsol\HermesBundle\Entity\Eno|null $idEno
     *
     * @return ConsultaMedicaFc
     */
    public function setIdEno(\Rebsol\HermesBundle\Entity\Eno $idEno = null)
    {
        $this->idEno = $idEno;

        return $this;
    }

    /**
     * Get idEno.
     *
     * @return \Rebsol\HermesBundle\Entity\Eno|null
     */
    public function getIdEno()
    {
        return $this->idEno;
    }

    /**
     * Set idPatologia.
     *
     * @param \Rebsol\HermesBundle\Entity\Patologia|null $idPatologia
     *
     * @return ConsultaMedicaFc
     */
    public function setIdPatologia(\Rebsol\HermesBundle\Entity\Patologia $idPatologia = null)
    {
        $this->idPatologia = $idPatologia;

        return $this;
    }

    /**
     * Get idPatologia.
     *
     * @return \Rebsol\HermesBundle\Entity\Patologia|null
     */
    public function getIdPatologia()
    {
        return $this->idPatologia;
    }

    /**
     * Set idEstadoNotificacionGes.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoNotificacion|null $idEstadoNotificacionGes
     *
     * @return ConsultaMedicaFc
     */
    public function setIdEstadoNotificacionGes(\Rebsol\HermesBundle\Entity\EstadoNotificacion $idEstadoNotificacionGes = null)
    {
        $this->idEstadoNotificacionGes = $idEstadoNotificacionGes;

        return $this;
    }

    /**
     * Get idEstadoNotificacionGes.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoNotificacion|null
     */
    public function getIdEstadoNotificacionGes()
    {
        return $this->idEstadoNotificacionGes;
    }

    /**
     * Set idEstadoNotificacionEno.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoNotificacion|null $idEstadoNotificacionEno
     *
     * @return ConsultaMedicaFc
     */
    public function setIdEstadoNotificacionEno(\Rebsol\HermesBundle\Entity\EstadoNotificacion $idEstadoNotificacionEno = null)
    {
        $this->idEstadoNotificacionEno = $idEstadoNotificacionEno;

        return $this;
    }

    /**
     * Get idEstadoNotificacionEno.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoNotificacion|null
     */
    public function getIdEstadoNotificacionEno()
    {
        return $this->idEstadoNotificacionEno;
    }

    /**
     * Set idPrecisionDiagnostica.
     *
     * @param \Rebsol\HermesBundle\Entity\PrecisionDiagnostica|null $idPrecisionDiagnostica
     *
     * @return ConsultaMedicaFc
     */
    public function setIdPrecisionDiagnostica(\Rebsol\HermesBundle\Entity\PrecisionDiagnostica $idPrecisionDiagnostica = null)
    {
        $this->idPrecisionDiagnostica = $idPrecisionDiagnostica;

        return $this;
    }

    /**
     * Get idPrecisionDiagnostica.
     *
     * @return \Rebsol\HermesBundle\Entity\PrecisionDiagnostica|null
     */
    public function getIdPrecisionDiagnostica()
    {
        return $this->idPrecisionDiagnostica;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return ConsultaMedicaFc
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idEstadoDiagnostico.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoDiagnostico|null $idEstadoDiagnostico
     *
     * @return ConsultaMedicaFc
     */
    public function setIdEstadoDiagnostico(\Rebsol\HermesBundle\Entity\EstadoDiagnostico $idEstadoDiagnostico = null)
    {
        $this->idEstadoDiagnostico = $idEstadoDiagnostico;

        return $this;
    }

    /**
     * Get idEstadoDiagnostico.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoDiagnostico|null
     */
    public function getIdEstadoDiagnostico()
    {
        return $this->idEstadoDiagnostico;
    }

    /**
     * Set idEstadoDiagnosticoUrgencia.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoDiagnosticoUrgencia|null $idEstadoDiagnosticoUrgencia
     *
     * @return ConsultaMedicaFc
     */
    public function setIdEstadoDiagnosticoUrgencia(\Rebsol\HermesBundle\Entity\EstadoDiagnosticoUrgencia $idEstadoDiagnosticoUrgencia = null)
    {
        $this->idEstadoDiagnosticoUrgencia = $idEstadoDiagnosticoUrgencia;

        return $this;
    }

    /**
     * Get idEstadoDiagnosticoUrgencia.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoDiagnosticoUrgencia|null
     */
    public function getIdEstadoDiagnosticoUrgencia()
    {
        return $this->idEstadoDiagnosticoUrgencia;
    }

    /**
     * Set idItemAtencion.
     *
     * @param \Rebsol\HermesBundle\Entity\ItemAtencionFc|null $idItemAtencion
     *
     * @return ConsultaMedicaFc
     */
    public function setIdItemAtencion(\Rebsol\HermesBundle\Entity\ItemAtencionFc $idItemAtencion = null)
    {
        $this->idItemAtencion = $idItemAtencion;

        return $this;
    }

    /**
     * Get idItemAtencion.
     *
     * @return \Rebsol\HermesBundle\Entity\ItemAtencionFc|null
     */
    public function getIdItemAtencion()
    {
        return $this->idItemAtencion;
    }

    /**
     * Set idPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\Paciente|null $idPaciente
     *
     * @return ConsultaMedicaFc
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
     * Set idPrevision.
     *
     * @param \Rebsol\HermesBundle\Entity\Prevision|null $idPrevision
     *
     * @return ConsultaMedicaFc
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
     * Set idReservaAtencion.
     *
     * @param \Rebsol\HermesBundle\Entity\ReservaAtencion|null $idReservaAtencion
     *
     * @return ConsultaMedicaFc
     */
    public function setIdReservaAtencion(\Rebsol\HermesBundle\Entity\ReservaAtencion $idReservaAtencion = null)
    {
        $this->idReservaAtencion = $idReservaAtencion;

        return $this;
    }

    /**
     * Get idReservaAtencion.
     *
     * @return \Rebsol\HermesBundle\Entity\ReservaAtencion|null
     */
    public function getIdReservaAtencion()
    {
        return $this->idReservaAtencion;
    }

    /**
     * Set idTipoAtencion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoAtencionFc|null $idTipoAtencion
     *
     * @return ConsultaMedicaFc
     */
    public function setIdTipoAtencion(\Rebsol\HermesBundle\Entity\TipoAtencionFc $idTipoAtencion = null)
    {
        $this->idTipoAtencion = $idTipoAtencion;

        return $this;
    }

    /**
     * Get idTipoAtencion.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoAtencionFc|null
     */
    public function getIdTipoAtencion()
    {
        return $this->idTipoAtencion;
    }

    /**
     * Set idDatoIngreso.
     *
     * @param \Rebsol\HermesBundle\Entity\DatoIngreso|null $idDatoIngreso
     *
     * @return ConsultaMedicaFc
     */
    public function setIdDatoIngreso(\Rebsol\HermesBundle\Entity\DatoIngreso $idDatoIngreso = null)
    {
        $this->idDatoIngreso = $idDatoIngreso;

        return $this;
    }

    /**
     * Get idDatoIngreso.
     *
     * @return \Rebsol\HermesBundle\Entity\DatoIngreso|null
     */
    public function getIdDatoIngreso()
    {
        return $this->idDatoIngreso;
    }

    /**
     * Set idRelCamaPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\RelCamaPaciente|null $idRelCamaPaciente
     *
     * @return ConsultaMedicaFc
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
     * Set idFormularioEnoInformacion.
     *
     * @param \Rebsol\HermesBundle\Entity\FormularioEnoInformacion|null $idFormularioEnoInformacion
     *
     * @return ConsultaMedicaFc
     */
    public function setIdFormularioEnoInformacion(\Rebsol\HermesBundle\Entity\FormularioEnoInformacion $idFormularioEnoInformacion = null)
    {
        $this->idFormularioEnoInformacion = $idFormularioEnoInformacion;

        return $this;
    }

    /**
     * Get idFormularioEnoInformacion.
     *
     * @return \Rebsol\HermesBundle\Entity\FormularioEnoInformacion|null
     */
    public function getIdFormularioEnoInformacion()
    {
        return $this->idFormularioEnoInformacion;
    }

    /**
     * Set idObstetriciaPacienteFc.
     *
     * @param \Rebsol\HermesBundle\Entity\ObstetriciaPacienteFc|null $idObstetriciaPacienteFc
     *
     * @return ConsultaMedicaFc
     */
    public function setIdObstetriciaPacienteFc(\Rebsol\HermesBundle\Entity\ObstetriciaPacienteFc $idObstetriciaPacienteFc = null)
    {
        $this->idObstetriciaPacienteFc = $idObstetriciaPacienteFc;

        return $this;
    }

    /**
     * Get idObstetriciaPacienteFc.
     *
     * @return \Rebsol\HermesBundle\Entity\ObstetriciaPacienteFc|null
     */
    public function getIdObstetriciaPacienteFc()
    {
        return $this->idObstetriciaPacienteFc;
    }

    /**
     * A continuación se encuentran las funciones externas a la entidad.
     */

    /**
     * Obtener Diagnostico de la atención
     *
     * @return string
     */
    public function obtenerDiagnostico($strDetalleConsultaMedica = null) {
        /**
         * Ejemplo del detalleConsultaMedica:
         *     A12 Diagnostico 2#@TEXT_DIAGNOSTICO@#ASDADFAFAAFFAAFFAASFASFASFAFSAFS
         */
        if ($strDetalleConsultaMedica == null) {
            $strDetalleConsultaMedica = $this->detalleConsultaMedica;
        }
        $arrDetalleConsultaMedica = explode('#@TEXT_DIAGNOSTICO@#', $strDetalleConsultaMedica);
        if (array_key_exists(0, $arrDetalleConsultaMedica)) {
            return $arrDetalleConsultaMedica[0];
        }
        return '';
    }

    /**
     * Obtener Complemento del Diagnostico de la atención
     *
     * @return string
     */
    public function obtenerComplementoDiagnostico($strDetalleConsultaMedica = null) {
        /**
         * Ejemplo del detalleConsultaMedica:
         *     A12 Diagnostico 2#@TEXT_DIAGNOSTICO@#ASDADFAFAAFFAAFFAASFASFASFAFSAFS
         */
        if ($strDetalleConsultaMedica == null) {
            $strDetalleConsultaMedica = $this->detalleConsultaMedica;
        }
        $arrDetalleConsultaMedica = explode('#@TEXT_DIAGNOSTICO@#', $strDetalleConsultaMedica);
        if (COUNT($arrDetalleConsultaMedica) != 1) {
            return $arrDetalleConsultaMedica[1];
        }
        return '';
    }

    /**
     * @return \DateTime|null
     */
    public function getFechaEnmienda()
    {
        return $this->fechaEnmienda;
    }

    /**
     * @param \DateTime|null $fechaEnmienda
     */
    public function setFechaEnmienda($fechaEnmienda)
    {
        $this->fechaEnmienda = $fechaEnmienda;
    }

    /**
     * @return \UsuariosRebsol
     */
    public function getIdUsuarioEnmienda()
    {
        return $this->idUsuarioEnmienda;
    }

    /**
     * @param \UsuariosRebsol $idUsuarioEnmienda
     */
    public function setIdUsuarioEnmienda($idUsuarioEnmienda)
    {
        $this->idUsuarioEnmienda = $idUsuarioEnmienda;
    }

    /**
     * @return string|null
     */
    public function getNombreRolUsuarioEnmienda()
    {
        return $this->nombreRolUsuarioEnmienda;
    }

    /**
     * @param string|null $nombreRolUsuarioEnmienda
     */
    public function setNombreRolUsuarioEnmienda($nombreRolUsuarioEnmienda)
    {
        $this->nombreRolUsuarioEnmienda = $nombreRolUsuarioEnmienda;
    }

    /**
     * @return bool
     */
    public function isEstadoEnmienda()
    {
        return $this->estadoEnmienda;
    }

    /**
     * @param bool $estadoEnmienda
     */
    public function setEstadoEnmienda($estadoEnmienda)
    {
        $this->estadoEnmienda = $estadoEnmienda;
    }

    /**
     * @return \ConsultaMedicaFc
     */
    public function getIdConsultaMedicaFcEnmienda()
    {
        return $this->idConsultaMedicaFcEnmienda;
    }

    /**
     * @param \ConsultaMedicaFc $idConsultaMedicaFcEnmienda
     */
    public function setIdConsultaMedicaFcEnmienda($idConsultaMedicaFcEnmienda)
    {
        $this->idConsultaMedicaFcEnmienda = $idConsultaMedicaFcEnmienda;
    }

    /**
     * @return bool|null
     */
    public function getEsGesOncologico()
    {
        return $this->esGesOncologico;
    }

    /**
     * @param bool|null $esGesOncologico
     */
    public function setEsGesOncologico($esGesOncologico)
    {
        $this->esGesOncologico = $esGesOncologico;
    }

    /**
     * @return \EtapaGesOncologica
     */
    public function getIdEtapaGesOncologica()
    {
        return $this->idEtapaGesOncologica;
    }

    /**
     * @param \EtapaGesOncologica $idEtapaGesOncologica
     */
    public function setIdEtapaGesOncologica($idEtapaGesOncologica)
    {
        $this->idEtapaGesOncologica = $idEtapaGesOncologica;
    }


}
