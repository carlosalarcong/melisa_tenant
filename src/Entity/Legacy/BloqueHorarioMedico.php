<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BloqueHorarioMedico
 *
 * @ORM\Table(name="bloque_horario_medico")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\BloqueHorarioMedicoRepository")
 */
class BloqueHorarioMedico
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
     * @ORM\Column(name="FECHA_DESDE", type="date", nullable=false)
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_HASTA", type="date", nullable=false)
     */
    private $fechaHasta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HORA_INICIO", type="time", nullable=false)
     */
    private $horaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HORA_TERMINO", type="time", nullable=false)
     */
    private $horaTermino;

    /**
     * @var int
     *
     * @ORM\Column(name="DURACION_CONSULTA", type="integer", nullable=false)
     */
    private $duracionConsulta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ESPONTANEO", type="integer", nullable=true)
     */
    private $espontaneo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=true)
     */
    private $fechaCreacion;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_TELECONSULTA", type="boolean", nullable=true)
     */
    private $esTeleconsulta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_AGENDA", type="text", length=0, nullable=true)
     */
    private $observacionAgenda;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

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
     * @var \TipoAtencionFc
     *
     * @ORM\ManyToOne(targetEntity="TipoAtencionFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_ATENCION", referencedColumnName="ID")
     * })
     */
    private $idTipoAtencion;

    /**
     * @var \EstadoBloqueHorarioMedico
     *
     * @ORM\ManyToOne(targetEntity="EstadoBloqueHorarioMedico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \TipoAgenda
     *
     * @ORM\ManyToOne(targetEntity="TipoAgenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_AGENDA", referencedColumnName="ID")
     * })
     */
    private $idTipoAgenda;

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
     *   @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="ID")
     * })
     */
    private $idUsuario;

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
     * @var \TipoVisualizacion
     *
     * @ORM\ManyToOne(targetEntity="TipoVisualizacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_VISUALIZACION", referencedColumnName="ID")
     * })
     */
    private $idTipoVisualizacion;



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
     * Set fechaDesde.
     *
     * @param \DateTime $fechaDesde
     *
     * @return BloqueHorarioMedico
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde.
     *
     * @return \DateTime
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta.
     *
     * @param \DateTime $fechaHasta
     *
     * @return BloqueHorarioMedico
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta.
     *
     * @return \DateTime
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set horaInicio.
     *
     * @param \DateTime $horaInicio
     *
     * @return BloqueHorarioMedico
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;

        return $this;
    }

    /**
     * Get horaInicio.
     *
     * @return \DateTime
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * Set horaTermino.
     *
     * @param \DateTime $horaTermino
     *
     * @return BloqueHorarioMedico
     */
    public function setHoraTermino($horaTermino)
    {
        $this->horaTermino = $horaTermino;

        return $this;
    }

    /**
     * Get horaTermino.
     *
     * @return \DateTime
     */
    public function getHoraTermino()
    {
        return $this->horaTermino;
    }

    /**
     * Set duracionConsulta.
     *
     * @param int $duracionConsulta
     *
     * @return BloqueHorarioMedico
     */
    public function setDuracionConsulta($duracionConsulta)
    {
        $this->duracionConsulta = $duracionConsulta;

        return $this;
    }

    /**
     * Get duracionConsulta.
     *
     * @return int
     */
    public function getDuracionConsulta()
    {
        return $this->duracionConsulta;
    }

    /**
     * Set espontaneo.
     *
     * @param int|null $espontaneo
     *
     * @return BloqueHorarioMedico
     */
    public function setEspontaneo($espontaneo = null)
    {
        $this->espontaneo = $espontaneo;

        return $this;
    }

    /**
     * Get espontaneo.
     *
     * @return int|null
     */
    public function getEspontaneo()
    {
        return $this->espontaneo;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime|null $fechaCreacion
     *
     * @return BloqueHorarioMedico
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
     * Set esTeleconsulta.
     *
     * @param bool|null $esTeleconsulta
     *
     * @return BloqueHorarioMedico
     */
    public function setEsTeleconsulta($esTeleconsulta = null)
    {
        $this->esTeleconsulta = $esTeleconsulta;

        return $this;
    }

    /**
     * Get esTeleconsulta.
     *
     * @return bool|null
     */
    public function getEsTeleconsulta()
    {
        return $this->esTeleconsulta;
    }

    /**
     * Set observacionAgenda.
     *
     * @param string|null $observacionAgenda
     *
     * @return BloqueHorarioMedico
     */
    public function setObservacionAgenda($observacionAgenda = null)
    {
        $this->observacionAgenda = $observacionAgenda;

        return $this;
    }

    /**
     * Get observacionAgenda.
     *
     * @return string|null
     */
    public function getObservacionAgenda()
    {
        return $this->observacionAgenda;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoBloqueHorarioMedico $idEstado
     *
     * @return BloqueHorarioMedico
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\EstadoBloqueHorarioMedico $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoBloqueHorarioMedico
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario
     *
     * @return BloqueHorarioMedico
     */
    public function setIdUsuario(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioCreacion
     *
     * @return BloqueHorarioMedico
     */
    public function setIdUsuarioCreacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion = null)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return BloqueHorarioMedico
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
     * Set idUnidad.
     *
     * @param \Rebsol\HermesBundle\Entity\Unidad|null $idUnidad
     *
     * @return BloqueHorarioMedico
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
     * Set idEspecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica|null $idEspecialidadMedica
     *
     * @return BloqueHorarioMedico
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
     * Set idSubespecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEspecialidadMedica|null $idSubespecialidadMedica
     *
     * @return BloqueHorarioMedico
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
     * Set idTipoAgenda.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoAgenda $idTipoAgenda
     *
     * @return BloqueHorarioMedico
     */
    public function setIdTipoAgenda(\Rebsol\HermesBundle\Entity\TipoAgenda $idTipoAgenda)
    {
        $this->idTipoAgenda = $idTipoAgenda;

        return $this;
    }

    /**
     * Get idTipoAgenda.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoAgenda
     */
    public function getIdTipoAgenda()
    {
        return $this->idTipoAgenda;
    }

    /**
     * Set idTipoAtencion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoAtencionFc|null $idTipoAtencion
     *
     * @return BloqueHorarioMedico
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
     * Set idTipoVisualizacion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoVisualizacion|null $idTipoVisualizacion
     *
     * @return BloqueHorarioMedico
     */
    public function setIdTipoVisualizacion(\Rebsol\HermesBundle\Entity\TipoVisualizacion $idTipoVisualizacion = null)
    {
        $this->idTipoVisualizacion = $idTipoVisualizacion;

        return $this;
    }

    /**
     * Get idTipoVisualizacion.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoVisualizacion|null
     */
    public function getIdTipoVisualizacion()
    {
        return $this->idTipoVisualizacion;
    }
}
