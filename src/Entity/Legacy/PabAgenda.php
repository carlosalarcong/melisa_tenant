<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PabAgenda
 *
 * @ORM\Table(name="pab_agenda")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PabAgendaRepository")
 */
class PabAgenda
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
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_SUSPENSION", type="datetime", nullable=true)
     */
    private $fechaSuspension;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_EGRESO", type="datetime", nullable=true)
     */
    private $fechaEgreso;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_INGRESO_RECUPERACION", type="datetime", nullable=true)
     */
    private $fechaIngresoRecuperacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_SUSPENSION", type="text", length=0, nullable=true)
     */
    private $observacionSuspension;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_LIBERA", type="datetime", nullable=true)
     */
    private $fechaLibera;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_AGENDA", type="datetime", nullable=false)
     */
    private $fechaAgenda;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

    /**
     * @var \PabEstadoPaciente
     *
     * @ORM\ManyToOne(targetEntity="PabEstadoPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAB_ESTADO_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPabEstadoPaciente;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_INGRESO_RECUPERACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioIngresoRecuperacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_EGRESO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioEgreso;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_LIBERA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioLibera;

    /**
     * @var \PabSolicitud
     *
     * @ORM\ManyToOne(targetEntity="PabSolicitud")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAB_SOLICITUD", referencedColumnName="ID")
     * })
     */
    private $idPabSolicitud;

    /**
     * @var \PabCausaSuspension
     *
     * @ORM\ManyToOne(targetEntity="PabCausaSuspension")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAB_CAUSA_SUSPENSION", referencedColumnName="ID")
     * })
     */
    private $idPabCausaSuspension;

    /**
     * @var \Pabellon
     *
     * @ORM\ManyToOne(targetEntity="Pabellon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PABELLON", referencedColumnName="ID")
     * })
     */
    private $idPabellon;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_SUSPENSION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioSuspension;



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
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return PabAgenda
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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return PabAgenda
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaSuspension.
     *
     * @param \DateTime|null $fechaSuspension
     *
     * @return PabAgenda
     */
    public function setFechaSuspension($fechaSuspension = null)
    {
        $this->fechaSuspension = $fechaSuspension;

        return $this;
    }

    /**
     * Get fechaSuspension.
     *
     * @return \DateTime|null
     */
    public function getFechaSuspension()
    {
        return $this->fechaSuspension;
    }

    /**
     * Set fechaEgreso.
     *
     * @param \DateTime|null $fechaEgreso
     *
     * @return PabAgenda
     */
    public function setFechaEgreso($fechaEgreso = null)
    {
        $this->fechaEgreso = $fechaEgreso;

        return $this;
    }

    /**
     * Get fechaEgreso.
     *
     * @return \DateTime|null
     */
    public function getFechaEgreso()
    {
        return $this->fechaEgreso;
    }

    /**
     * Set fechaIngresoRecuperacion.
     *
     * @param \DateTime|null $fechaIngresoRecuperacion
     *
     * @return PabAgenda
     */
    public function setFechaIngresoRecuperacion($fechaIngresoRecuperacion = null)
    {
        $this->fechaIngresoRecuperacion = $fechaIngresoRecuperacion;

        return $this;
    }

    /**
     * Get fechaIngresoRecuperacion.
     *
     * @return \DateTime|null
     */
    public function getFechaIngresoRecuperacion()
    {
        return $this->fechaIngresoRecuperacion;
    }

    /**
     * Set observacionSuspension.
     *
     * @param string|null $observacionSuspension
     *
     * @return PabAgenda
     */
    public function setObservacionSuspension($observacionSuspension = null)
    {
        $this->observacionSuspension = $observacionSuspension;

        return $this;
    }

    /**
     * Get observacionSuspension.
     *
     * @return string|null
     */
    public function getObservacionSuspension()
    {
        return $this->observacionSuspension;
    }

    /**
     * Set fechaLibera.
     *
     * @param \DateTime|null $fechaLibera
     *
     * @return PabAgenda
     */
    public function setFechaLibera($fechaLibera = null)
    {
        $this->fechaLibera = $fechaLibera;

        return $this;
    }

    /**
     * Get fechaLibera.
     *
     * @return \DateTime|null
     */
    public function getFechaLibera()
    {
        return $this->fechaLibera;
    }

    /**
     * Set fechaAgenda.
     *
     * @param \DateTime $fechaAgenda
     *
     * @return PabAgenda
     */
    public function setFechaAgenda($fechaAgenda)
    {
        $this->fechaAgenda = $fechaAgenda;

        return $this;
    }

    /**
     * Get fechaAgenda.
     *
     * @return \DateTime
     */
    public function getFechaAgenda()
    {
        return $this->fechaAgenda;
    }

    /**
     * Set horaInicio.
     *
     * @param \DateTime $horaInicio
     *
     * @return PabAgenda
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
     * @return PabAgenda
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
     * Set idPabEstadoPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\PabEstadoPaciente $idPabEstadoPaciente
     *
     * @return PabAgenda
     */
    public function setIdPabEstadoPaciente(\Rebsol\HermesBundle\Entity\PabEstadoPaciente $idPabEstadoPaciente)
    {
        $this->idPabEstadoPaciente = $idPabEstadoPaciente;

        return $this;
    }

    /**
     * Get idPabEstadoPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\PabEstadoPaciente
     */
    public function getIdPabEstadoPaciente()
    {
        return $this->idPabEstadoPaciente;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return PabAgenda
     */
    public function setIdUsuarioCreacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idUsuarioSuspension.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioSuspension
     *
     * @return PabAgenda
     */
    public function setIdUsuarioSuspension(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioSuspension = null)
    {
        $this->idUsuarioSuspension = $idUsuarioSuspension;

        return $this;
    }

    /**
     * Get idUsuarioSuspension.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioSuspension()
    {
        return $this->idUsuarioSuspension;
    }

    /**
     * Set idUsuarioEgreso.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioEgreso
     *
     * @return PabAgenda
     */
    public function setIdUsuarioEgreso(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioEgreso = null)
    {
        $this->idUsuarioEgreso = $idUsuarioEgreso;

        return $this;
    }

    /**
     * Get idUsuarioEgreso.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioEgreso()
    {
        return $this->idUsuarioEgreso;
    }

    /**
     * Set idUsuarioIngresoRecuperacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioIngresoRecuperacion
     *
     * @return PabAgenda
     */
    public function setIdUsuarioIngresoRecuperacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioIngresoRecuperacion = null)
    {
        $this->idUsuarioIngresoRecuperacion = $idUsuarioIngresoRecuperacion;

        return $this;
    }

    /**
     * Get idUsuarioIngresoRecuperacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioIngresoRecuperacion()
    {
        return $this->idUsuarioIngresoRecuperacion;
    }

    /**
     * Set idPabCausaSuspension.
     *
     * @param \Rebsol\HermesBundle\Entity\PabCausaSuspension|null $idPabCausaSuspension
     *
     * @return PabAgenda
     */
    public function setIdPabCausaSuspension(\Rebsol\HermesBundle\Entity\PabCausaSuspension $idPabCausaSuspension = null)
    {
        $this->idPabCausaSuspension = $idPabCausaSuspension;

        return $this;
    }

    /**
     * Get idPabCausaSuspension.
     *
     * @return \Rebsol\HermesBundle\Entity\PabCausaSuspension|null
     */
    public function getIdPabCausaSuspension()
    {
        return $this->idPabCausaSuspension;
    }

    /**
     * Set idUsuarioLibera.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioLibera
     *
     * @return PabAgenda
     */
    public function setIdUsuarioLibera(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioLibera = null)
    {
        $this->idUsuarioLibera = $idUsuarioLibera;

        return $this;
    }

    /**
     * Get idUsuarioLibera.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioLibera()
    {
        return $this->idUsuarioLibera;
    }

    /**
     * Set idPabellon.
     *
     * @param \Rebsol\HermesBundle\Entity\Pabellon $idPabellon
     *
     * @return PabAgenda
     */
    public function setIdPabellon(\Rebsol\HermesBundle\Entity\Pabellon $idPabellon)
    {
        $this->idPabellon = $idPabellon;

        return $this;
    }

    /**
     * Get idPabellon.
     *
     * @return \Rebsol\HermesBundle\Entity\Pabellon
     */
    public function getIdPabellon()
    {
        return $this->idPabellon;
    }

    /**
     * Set idPabSolicitud.
     *
     * @param \Rebsol\HermesBundle\Entity\PabSolicitud $idPabSolicitud
     *
     * @return PabAgenda
     */
    public function setIdPabSolicitud(\Rebsol\HermesBundle\Entity\PabSolicitud $idPabSolicitud)
    {
        $this->idPabSolicitud = $idPabSolicitud;

        return $this;
    }

    /**
     * Get idPabSolicitud.
     *
     * @return \Rebsol\HermesBundle\Entity\PabSolicitud
     */
    public function getIdPabSolicitud()
    {
        return $this->idPabSolicitud;
    }
}
