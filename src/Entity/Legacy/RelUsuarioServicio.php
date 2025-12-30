<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelUsuarioServicio
 *
 * @ORM\Table(name="rel_usuario_servicio")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\RelUsuarioServicioRepository")
 */
class RelUsuarioServicio
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
     * @var \EstadoRelUsuarioServicio
     *
     * @ORM\ManyToOne(targetEntity="EstadoRelUsuarioServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

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
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario
     *
     * @return RelUsuarioServicio
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
     * Set idServicio.
     *
     * @param \Rebsol\HermesBundle\Entity\Servicio $idServicio
     *
     * @return RelUsuarioServicio
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoRelUsuarioServicio $idEstado
     *
     * @return RelUsuarioServicio
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\EstadoRelUsuarioServicio $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoRelUsuarioServicio
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
