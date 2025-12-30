<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tratamiento
 *
 * @ORM\Table(name="tratamiento")
 * @ORM\Entity
 */
class Tratamiento
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
     * @ORM\Column(name="NOMBRE_TRATAMIENTO", type="string", length=255, nullable=false)
     */
    private $nombreTratamiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

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
     * @var \EstadoTratamiento
     *
     * @ORM\ManyToOne(targetEntity="EstadoTratamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \TipoTratamiento
     *
     * @ORM\ManyToOne(targetEntity="TipoTratamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_TRATAMIENTO", referencedColumnName="ID")
     * })
     */
    private $idTipoTratamiento;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombreTratamiento.
     *
     * @param string $nombreTratamiento
     *
     * @return Tratamiento
     */
    public function setNombreTratamiento($nombreTratamiento)
    {
        $this->nombreTratamiento = $nombreTratamiento;

        return $this;
    }

    /**
     * Get nombreTratamiento.
     *
     * @return string
     */
    public function getNombreTratamiento()
    {
        return $this->nombreTratamiento;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Tratamiento
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
     * Set idPnatural.
     *
     * @param \App\Entity\Legacy\Pnatural $idPnatural
     *
     * @return Tratamiento
     */
    public function setIdPnatural(\App\Entity\Legacy\Pnatural $idPnatural)
    {
        $this->idPnatural = $idPnatural;

        return $this;
    }

    /**
     * Get idPnatural.
     *
     * @return \App\Entity\Legacy\Pnatural
     */
    public function getIdPnatural()
    {
        return $this->idPnatural;
    }

    /**
     * Set idTipoTratamiento.
     *
     * @param \App\Entity\TipoTratamiento $idTipoTratamiento
     *
     * @return Tratamiento
     */
    public function setIdTipoTratamiento(\App\Entity\TipoTratamiento $idTipoTratamiento)
    {
        $this->idTipoTratamiento = $idTipoTratamiento;

        return $this;
    }

    /**
     * Get idTipoTratamiento.
     *
     * @return \App\Entity\TipoTratamiento
     */
    public function getIdTipoTratamiento()
    {
        return $this->idTipoTratamiento;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\EstadoTratamiento $idEstado
     *
     * @return Tratamiento
     */
    public function setIdEstado(\App\Entity\EstadoTratamiento $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\EstadoTratamiento
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \App\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return Tratamiento
     */
    public function setIdUsuarioCreacion(\App\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \App\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }
}
