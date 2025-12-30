<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * BloqueoAgenda
 *
 * @ORM\Table(name="bloqueo_agenda")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\BloqueoAgendaRepository")
 */
class BloqueoAgenda
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
     * @ORM\Column(name="DIA", type="integer", nullable=false)
     */
    private $dia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_BLOQUEO", type="datetime", nullable=false)
     */
    private $fechaBloqueo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_DESBLOQUEO", type="datetime", nullable=true)
     */
    private $fechaDesbloqueo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRIPCION_BLOQUEO", type="text", length=0, nullable=true)
     */
    private $descripcionBloqueo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_INICIO_BLOQUEO", type="date", nullable=false)
     */
    private $fechaInicioBloqueo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_TERMINO_BLOQUEO", type="date", nullable=false)
     */
    private $fechaTerminoBloqueo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HORA_DESDE", type="time", nullable=false)
     */
    private $horaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HORA_HASTA", type="time", nullable=false)
     */
    private $horaHasta;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_DESBLOQUEO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioDesbloqueo;

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
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;

    /**
     * @var \TipoBloqueo
     *
     * @ORM\ManyToOne(targetEntity="TipoBloqueo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_BLOQUEO", referencedColumnName="ID")
     * })
     */
    private $idTipoBloqueo;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_BLOQUEO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioBloqueo;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dia.
     *
     * @param int $dia
     *
     * @return BloqueoAgenda
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia.
     *
     * @return int
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set fechaBloqueo.
     *
     * @param \DateTime $fechaBloqueo
     *
     * @return BloqueoAgenda
     */
    public function setFechaBloqueo($fechaBloqueo)
    {
        $this->fechaBloqueo = $fechaBloqueo;

        return $this;
    }

    /**
     * Get fechaBloqueo.
     *
     * @return \DateTime
     */
    public function getFechaBloqueo()
    {
        return $this->fechaBloqueo;
    }

    /**
     * Set fechaDesbloqueo.
     *
     * @param \DateTime|null $fechaDesbloqueo
     *
     * @return BloqueoAgenda
     */
    public function setFechaDesbloqueo($fechaDesbloqueo = null)
    {
        $this->fechaDesbloqueo = $fechaDesbloqueo;

        return $this;
    }

    /**
     * Get fechaDesbloqueo.
     *
     * @return \DateTime|null
     */
    public function getFechaDesbloqueo()
    {
        return $this->fechaDesbloqueo;
    }

    /**
     * Set descripcionBloqueo.
     *
     * @param string|null $descripcionBloqueo
     *
     * @return BloqueoAgenda
     */
    public function setDescripcionBloqueo($descripcionBloqueo = null)
    {
        $this->descripcionBloqueo = $descripcionBloqueo;

        return $this;
    }

    /**
     * Get descripcionBloqueo.
     *
     * @return string|null
     */
    public function getDescripcionBloqueo()
    {
        return $this->descripcionBloqueo;
    }

    /**
     * Set fechaInicioBloqueo.
     *
     * @param \DateTime $fechaInicioBloqueo
     *
     * @return BloqueoAgenda
     */
    public function setFechaInicioBloqueo($fechaInicioBloqueo)
    {
        $this->fechaInicioBloqueo = $fechaInicioBloqueo;

        return $this;
    }

    /**
     * Get fechaInicioBloqueo.
     *
     * @return \DateTime
     */
    public function getFechaInicioBloqueo()
    {
        return $this->fechaInicioBloqueo;
    }

    /**
     * Set fechaTerminoBloqueo.
     *
     * @param \DateTime $fechaTerminoBloqueo
     *
     * @return BloqueoAgenda
     */
    public function setFechaTerminoBloqueo($fechaTerminoBloqueo)
    {
        $this->fechaTerminoBloqueo = $fechaTerminoBloqueo;

        return $this;
    }

    /**
     * Get fechaTerminoBloqueo.
     *
     * @return \DateTime
     */
    public function getFechaTerminoBloqueo()
    {
        return $this->fechaTerminoBloqueo;
    }

    /**
     * Set horaDesde.
     *
     * @param \DateTime $horaDesde
     *
     * @return BloqueoAgenda
     */
    public function setHoraDesde($horaDesde)
    {
        $this->horaDesde = $horaDesde;

        return $this;
    }

    /**
     * Get horaDesde.
     *
     * @return \DateTime
     */
    public function getHoraDesde()
    {
        return $this->horaDesde;
    }

    /**
     * Set horaHasta.
     *
     * @param \DateTime $horaHasta
     *
     * @return BloqueoAgenda
     */
    public function setHoraHasta($horaHasta)
    {
        $this->horaHasta = $horaHasta;

        return $this;
    }

    /**
     * Get horaHasta.
     *
     * @return \DateTime
     */
    public function getHoraHasta()
    {
        return $this->horaHasta;
    }

    /**
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuario
     *
     * @return BloqueoAgenda
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return BloqueoAgenda
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
     * Set idTipoBloqueo.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoBloqueo|null $idTipoBloqueo
     *
     * @return BloqueoAgenda
     */
    public function setIdTipoBloqueo(\Rebsol\HermesBundle\Entity\TipoBloqueo $idTipoBloqueo = null)
    {
        $this->idTipoBloqueo = $idTipoBloqueo;

        return $this;
    }

    /**
     * Get idTipoBloqueo.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoBloqueo|null
     */
    public function getIdTipoBloqueo()
    {
        return $this->idTipoBloqueo;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return BloqueoAgenda
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
     * Set idUsuarioBloqueo.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioBloqueo
     *
     * @return BloqueoAgenda
     */
    public function setIdUsuarioBloqueo(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioBloqueo)
    {
        $this->idUsuarioBloqueo = $idUsuarioBloqueo;

        return $this;
    }

    /**
     * Get idUsuarioBloqueo.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioBloqueo()
    {
        return $this->idUsuarioBloqueo;
    }

    /**
     * Set idUsuarioDesbloqueo.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioDesbloqueo
     *
     * @return BloqueoAgenda
     */
    public function setIdUsuarioDesbloqueo(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioDesbloqueo = null)
    {
        $this->idUsuarioDesbloqueo = $idUsuarioDesbloqueo;

        return $this;
    }

    /**
     * Get idUsuarioDesbloqueo.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioDesbloqueo()
    {
        return $this->idUsuarioDesbloqueo;
    }
}
