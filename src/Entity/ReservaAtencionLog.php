<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReservaAtencionLog
 *
 * @ORM\Table(name="reserva_atencion_log")
 * @ORM\Entity
 */
class ReservaAtencionLog
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
     * @ORM\Column(name="DESCRIPCION", type="text", length=0, nullable=true)
     */
    private $descripcion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_REGISTRO", type="datetime", nullable=true)
     */
    private $fechaRegistro;

    /**
     * @var \HorarioConsulta
     *
     * @ORM\ManyToOne(targetEntity="HorarioConsulta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_HORARIO_CONSULTA_NUEVO", referencedColumnName="ID")
     * })
     */
    private $idHorarioConsultaNuevo;

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
     * @var \ReservaAtencionTipoLog
     *
     * @ORM\ManyToOne(targetEntity="ReservaAtencionTipoLog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RESERVA_TIPO_LOG", referencedColumnName="ID")
     * })
     */
    private $idReservaTipoLog;

    /**
     * @var \HorarioConsulta
     *
     * @ORM\ManyToOne(targetEntity="HorarioConsulta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_HORARIO_CONSULTA_ANTIGUO", referencedColumnName="ID")
     * })
     */
    private $idHorarioConsultaAntiguo;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return ReservaAtencionLog
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechaRegistro.
     *
     * @param \DateTime|null $fechaRegistro
     *
     * @return ReservaAtencionLog
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
     * Set idUsuarioModifica.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioModifica
     *
     * @return ReservaAtencionLog
     */
    public function setIdUsuarioModifica(\App\Entity\UsuariosRebsol $idUsuarioModifica = null)
    {
        $this->idUsuarioModifica = $idUsuarioModifica;

        return $this;
    }

    /**
     * Get idUsuarioModifica.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioModifica()
    {
        return $this->idUsuarioModifica;
    }

    /**
     * Set idHorarioConsultaAntiguo.
     *
     * @param \App\Entity\HorarioConsulta|null $idHorarioConsultaAntiguo
     *
     * @return ReservaAtencionLog
     */
    public function setIdHorarioConsultaAntiguo(\App\Entity\HorarioConsulta $idHorarioConsultaAntiguo = null)
    {
        $this->idHorarioConsultaAntiguo = $idHorarioConsultaAntiguo;

        return $this;
    }

    /**
     * Get idHorarioConsultaAntiguo.
     *
     * @return \App\Entity\HorarioConsulta|null
     */
    public function getIdHorarioConsultaAntiguo()
    {
        return $this->idHorarioConsultaAntiguo;
    }

    /**
     * Set idHorarioConsultaNuevo.
     *
     * @param \App\Entity\HorarioConsulta|null $idHorarioConsultaNuevo
     *
     * @return ReservaAtencionLog
     */
    public function setIdHorarioConsultaNuevo(\App\Entity\HorarioConsulta $idHorarioConsultaNuevo = null)
    {
        $this->idHorarioConsultaNuevo = $idHorarioConsultaNuevo;

        return $this;
    }

    /**
     * Get idHorarioConsultaNuevo.
     *
     * @return \App\Entity\HorarioConsulta|null
     */
    public function getIdHorarioConsultaNuevo()
    {
        return $this->idHorarioConsultaNuevo;
    }

    /**
     * Set idReservaAtencion.
     *
     * @param \App\Entity\ReservaAtencion|null $idReservaAtencion
     *
     * @return ReservaAtencionLog
     */
    public function setIdReservaAtencion(\App\Entity\ReservaAtencion $idReservaAtencion = null)
    {
        $this->idReservaAtencion = $idReservaAtencion;

        return $this;
    }

    /**
     * Get idReservaAtencion.
     *
     * @return \App\Entity\ReservaAtencion|null
     */
    public function getIdReservaAtencion()
    {
        return $this->idReservaAtencion;
    }

    /**
     * Set idReservaTipoLog.
     *
     * @param \App\Entity\ReservaAtencionTipoLog|null $idReservaTipoLog
     *
     * @return ReservaAtencionLog
     */
    public function setIdReservaTipoLog(\App\Entity\ReservaAtencionTipoLog $idReservaTipoLog = null)
    {
        $this->idReservaTipoLog = $idReservaTipoLog;

        return $this;
    }

    /**
     * Get idReservaTipoLog.
     *
     * @return \App\Entity\ReservaAtencionTipoLog|null
     */
    public function getIdReservaTipoLog()
    {
        return $this->idReservaTipoLog;
    }
}
