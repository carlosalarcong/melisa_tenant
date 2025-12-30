<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PabSolicitud
 *
 * @ORM\Table(name="pab_solicitud")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PabSolicitudRepository")
 */
class PabSolicitud
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
     * @var string
     *
     * @ORM\Column(name="DIAGNOSTICO", type="text", length=0, nullable=false)
     */
    private $diagnostico;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_PROBABLE_CIRUGIA", type="datetime", nullable=true)
     */
    private $fechaProbableCirugia;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="TIEMPO_ESTIMADO_CIRUGIA", type="time", nullable=true)
     */
    private $tiempoEstimadoCirugia;

    /**
     * @var bool
     *
     * @ORM\Column(name="REQUIERE_RAYOS", type="boolean", nullable=false)
     */
    private $requiereRayos;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_GES", type="boolean", nullable=false)
     */
    private $esGes;

    /**
     * @var bool
     *
     * @ORM\Column(name="REQUIERE_BIOPSIA", type="boolean", nullable=false)
     */
    private $requiereBiopsia;

    /**
     * @var bool
     *
     * @ORM\Column(name="REQUIERE_DADORES", type="boolean", nullable=false)
     */
    private $requiereDadores;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CANTIDAD_DADORES", type="integer", nullable=true)
     */
    private $cantidadDadores;

    /**
     * @var bool
     *
     * @ORM\Column(name="NECESITA_ARSENALERA", type="boolean", nullable=false)
     */
    private $necesitaArsenalera;

    /**
     * @var bool
     *
     * @ORM\Column(name="NECESITA_ANESTESISTA", type="boolean", nullable=false)
     */
    private $necesitaAnestesista;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INSUMO_EXTERNO", type="text", length=0, nullable=true)
     */
    private $insumoExterno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_SOLICITUD", type="datetime", nullable=false)
     */
    private $fechaSolicitud;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_ANULACION", type="text", length=0, nullable=true)
     */
    private $observacionAnulacion;

    /**
     * @var \ConsultaMedicaFc
     *
     * @ORM\ManyToOne(targetEntity="ConsultaMedicaFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONSULTA_MEDICA_FC", referencedColumnName="ID")
     * })
     */
    private $idConsultaMedicaFc;

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
     * @var \Patologia
     *
     * @ORM\ManyToOne(targetEntity="Patologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PATOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idPatologia;

    /**
     * @var \PabGrupoSanguineo
     *
     * @ORM\ManyToOne(targetEntity="PabGrupoSanguineo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_GRUPO_SANGUINEO", referencedColumnName="ID")
     * })
     */
    private $idGrupoSanguineo;

    /**
     * @var \PabTipoIntervencion
     *
     * @ORM\ManyToOne(targetEntity="PabTipoIntervencion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAB_TIPO_INTERVENCION", referencedColumnName="ID")
     * })
     */
    private $idPabTipoIntervencion;

    /**
     * @var \EspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="EspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESPECIALIDAD", referencedColumnName="ID")
     * })
     */
    private $idEspecialidad;

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
     * @var \PabMotivoAnulacion
     *
     * @ORM\ManyToOne(targetEntity="PabMotivoAnulacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MOTIVO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idMotivoAnulacion;

    /**
     * @var \Pnatural
     *
     * @ORM\ManyToOne(targetEntity="Pnatural")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PNATURAL", referencedColumnName="ID")
     * })
     */
    private $idPnatural;

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
     * @var \PabAnestesia
     *
     * @ORM\ManyToOne(targetEntity="PabAnestesia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ANESTESIA", referencedColumnName="ID")
     * })
     */
    private $idAnestesia;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SOLICITUD", referencedColumnName="ID")
     * })
     */
    private $idUsuarioSolicitud;



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
     * Set diagnostico.
     *
     * @param string $diagnostico
     *
     * @return PabSolicitud
     */
    public function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    /**
     * Get diagnostico.
     *
     * @return string
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     * Set fechaProbableCirugia.
     *
     * @param \DateTime|null $fechaProbableCirugia
     *
     * @return PabSolicitud
     */
    public function setFechaProbableCirugia($fechaProbableCirugia = null)
    {
        $this->fechaProbableCirugia = $fechaProbableCirugia;

        return $this;
    }

    /**
     * Get fechaProbableCirugia.
     *
     * @return \DateTime|null
     */
    public function getFechaProbableCirugia()
    {
        return $this->fechaProbableCirugia;
    }

    /**
     * Set tiempoEstimadoCirugia.
     *
     * @param \DateTime|null $tiempoEstimadoCirugia
     *
     * @return PabSolicitud
     */
    public function setTiempoEstimadoCirugia($tiempoEstimadoCirugia = null)
    {
        $this->tiempoEstimadoCirugia = $tiempoEstimadoCirugia;

        return $this;
    }

    /**
     * Get tiempoEstimadoCirugia.
     *
     * @return \DateTime|null
     */
    public function getTiempoEstimadoCirugia()
    {
        return $this->tiempoEstimadoCirugia;
    }

    /**
     * Set requiereRayos.
     *
     * @param bool $requiereRayos
     *
     * @return PabSolicitud
     */
    public function setRequiereRayos($requiereRayos)
    {
        $this->requiereRayos = $requiereRayos;

        return $this;
    }

    /**
     * Get requiereRayos.
     *
     * @return bool
     */
    public function getRequiereRayos()
    {
        return $this->requiereRayos;
    }

    /**
     * Set esGes.
     *
     * @param bool $esGes
     *
     * @return PabSolicitud
     */
    public function setEsGes($esGes)
    {
        $this->esGes = $esGes;

        return $this;
    }

    /**
     * Get esGes.
     *
     * @return bool
     */
    public function getEsGes()
    {
        return $this->esGes;
    }

    /**
     * Set requiereBiopsia.
     *
     * @param bool $requiereBiopsia
     *
     * @return PabSolicitud
     */
    public function setRequiereBiopsia($requiereBiopsia)
    {
        $this->requiereBiopsia = $requiereBiopsia;

        return $this;
    }

    /**
     * Get requiereBiopsia.
     *
     * @return bool
     */
    public function getRequiereBiopsia()
    {
        return $this->requiereBiopsia;
    }

    /**
     * Set requiereDadores.
     *
     * @param bool $requiereDadores
     *
     * @return PabSolicitud
     */
    public function setRequiereDadores($requiereDadores)
    {
        $this->requiereDadores = $requiereDadores;

        return $this;
    }

    /**
     * Get requiereDadores.
     *
     * @return bool
     */
    public function getRequiereDadores()
    {
        return $this->requiereDadores;
    }

    /**
     * Set cantidadDadores.
     *
     * @param int|null $cantidadDadores
     *
     * @return PabSolicitud
     */
    public function setCantidadDadores($cantidadDadores = null)
    {
        $this->cantidadDadores = $cantidadDadores;

        return $this;
    }

    /**
     * Get cantidadDadores.
     *
     * @return int|null
     */
    public function getCantidadDadores()
    {
        return $this->cantidadDadores;
    }

    /**
     * Set necesitaArsenalera.
     *
     * @param bool $necesitaArsenalera
     *
     * @return PabSolicitud
     */
    public function setNecesitaArsenalera($necesitaArsenalera)
    {
        $this->necesitaArsenalera = $necesitaArsenalera;

        return $this;
    }

    /**
     * Get necesitaArsenalera.
     *
     * @return bool
     */
    public function getNecesitaArsenalera()
    {
        return $this->necesitaArsenalera;
    }

    /**
     * Set necesitaAnestesista.
     *
     * @param bool $necesitaAnestesista
     *
     * @return PabSolicitud
     */
    public function setNecesitaAnestesista($necesitaAnestesista)
    {
        $this->necesitaAnestesista = $necesitaAnestesista;

        return $this;
    }

    /**
     * Get necesitaAnestesista.
     *
     * @return bool
     */
    public function getNecesitaAnestesista()
    {
        return $this->necesitaAnestesista;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return PabSolicitud
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
     * Set insumoExterno.
     *
     * @param string|null $insumoExterno
     *
     * @return PabSolicitud
     */
    public function setInsumoExterno($insumoExterno = null)
    {
        $this->insumoExterno = $insumoExterno;

        return $this;
    }

    /**
     * Get insumoExterno.
     *
     * @return string|null
     */
    public function getInsumoExterno()
    {
        return $this->insumoExterno;
    }

    /**
     * Set fechaSolicitud.
     *
     * @param \DateTime $fechaSolicitud
     *
     * @return PabSolicitud
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
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return PabSolicitud
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
     * Set observacionAnulacion.
     *
     * @param string|null $observacionAnulacion
     *
     * @return PabSolicitud
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
     * Set idPnatural.
     *
     * @param \Rebsol\HermesBundle\Entity\Pnatural $idPnatural
     *
     * @return PabSolicitud
     */
    public function setIdPnatural(\Rebsol\HermesBundle\Entity\Pnatural $idPnatural)
    {
        $this->idPnatural = $idPnatural;

        return $this;
    }

    /**
     * Get idPnatural.
     *
     * @return \Rebsol\HermesBundle\Entity\Pnatural
     */
    public function getIdPnatural()
    {
        return $this->idPnatural;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return PabSolicitud
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
     * Set idProfesional.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idProfesional
     *
     * @return PabSolicitud
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
     * Set idEspecialidad.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidad
     *
     * @return PabSolicitud
     */
    public function setIdEspecialidad(\Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidad)
    {
        $this->idEspecialidad = $idEspecialidad;

        return $this;
    }

    /**
     * Get idEspecialidad.
     *
     * @return \Rebsol\HermesBundle\Entity\EspecialidadMedica
     */
    public function getIdEspecialidad()
    {
        return $this->idEspecialidad;
    }

    /**
     * Set idAnestesia.
     *
     * @param \Rebsol\HermesBundle\Entity\PabAnestesia|null $idAnestesia
     *
     * @return PabSolicitud
     */
    public function setIdAnestesia(\Rebsol\HermesBundle\Entity\PabAnestesia $idAnestesia = null)
    {
        $this->idAnestesia = $idAnestesia;

        return $this;
    }

    /**
     * Get idAnestesia.
     *
     * @return \Rebsol\HermesBundle\Entity\PabAnestesia|null
     */
    public function getIdAnestesia()
    {
        return $this->idAnestesia;
    }

    /**
     * Set idGrupoSanguineo.
     *
     * @param \Rebsol\HermesBundle\Entity\PabGrupoSanguineo|null $idGrupoSanguineo
     *
     * @return PabSolicitud
     */
    public function setIdGrupoSanguineo(\Rebsol\HermesBundle\Entity\PabGrupoSanguineo $idGrupoSanguineo = null)
    {
        $this->idGrupoSanguineo = $idGrupoSanguineo;

        return $this;
    }

    /**
     * Get idGrupoSanguineo.
     *
     * @return \Rebsol\HermesBundle\Entity\PabGrupoSanguineo|null
     */
    public function getIdGrupoSanguineo()
    {
        return $this->idGrupoSanguineo;
    }

    /**
     * Set idServicioSolicitud.
     *
     * @param \Rebsol\HermesBundle\Entity\Servicio $idServicioSolicitud
     *
     * @return PabSolicitud
     */
    public function setIdServicioSolicitud(\Rebsol\HermesBundle\Entity\Servicio $idServicioSolicitud)
    {
        $this->idServicioSolicitud = $idServicioSolicitud;

        return $this;
    }

    /**
     * Get idServicioSolicitud.
     *
     * @return \Rebsol\HermesBundle\Entity\Servicio
     */
    public function getIdServicioSolicitud()
    {
        return $this->idServicioSolicitud;
    }

    /**
     * Set idPatologia.
     *
     * @param \Rebsol\HermesBundle\Entity\Patologia|null $idPatologia
     *
     * @return PabSolicitud
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
     * Set idUsuarioSolicitud.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioSolicitud
     *
     * @return PabSolicitud
     */
    public function setIdUsuarioSolicitud(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioSolicitud)
    {
        $this->idUsuarioSolicitud = $idUsuarioSolicitud;

        return $this;
    }

    /**
     * Get idUsuarioSolicitud.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioSolicitud()
    {
        return $this->idUsuarioSolicitud;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return PabSolicitud
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
     * Set idMotivoAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\PabMotivoAnulacion|null $idMotivoAnulacion
     *
     * @return PabSolicitud
     */
    public function setIdMotivoAnulacion(\Rebsol\HermesBundle\Entity\PabMotivoAnulacion $idMotivoAnulacion = null)
    {
        $this->idMotivoAnulacion = $idMotivoAnulacion;

        return $this;
    }

    /**
     * Get idMotivoAnulacion.
     *
     * @return \Rebsol\HermesBundle\Entity\PabMotivoAnulacion|null
     */
    public function getIdMotivoAnulacion()
    {
        return $this->idMotivoAnulacion;
    }

    /**
     * Set idPabTipoIntervencion.
     *
     * @param \Rebsol\HermesBundle\Entity\PabTipoIntervencion $idPabTipoIntervencion
     *
     * @return PabSolicitud
     */
    public function setIdPabTipoIntervencion(\Rebsol\HermesBundle\Entity\PabTipoIntervencion $idPabTipoIntervencion)
    {
        $this->idPabTipoIntervencion = $idPabTipoIntervencion;

        return $this;
    }

    /**
     * Get idPabTipoIntervencion.
     *
     * @return \Rebsol\HermesBundle\Entity\PabTipoIntervencion
     */
    public function getIdPabTipoIntervencion()
    {
        return $this->idPabTipoIntervencion;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return PabSolicitud
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idConsultaMedicaFc.
     *
     * @param \Rebsol\HermesBundle\Entity\ConsultaMedicaFc|null $idConsultaMedicaFc
     *
     * @return PabSolicitud
     */
    public function setIdConsultaMedicaFc(\Rebsol\HermesBundle\Entity\ConsultaMedicaFc $idConsultaMedicaFc = null)
    {
        $this->idConsultaMedicaFc = $idConsultaMedicaFc;

        return $this;
    }

    /**
     * Get idConsultaMedicaFc.
     *
     * @return \Rebsol\HermesBundle\Entity\ConsultaMedicaFc|null
     */
    public function getIdConsultaMedicaFc()
    {
        return $this->idConsultaMedicaFc;
    }

    /**
     * Set idDatoIngreso.
     *
     * @param \Rebsol\HermesBundle\Entity\DatoIngreso|null $idDatoIngreso
     *
     * @return PabSolicitud
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
}
