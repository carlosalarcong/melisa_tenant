<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelUsuarioPerfil
 *
 * @ORM\Table(name="rel_usuario_perfil")
 * @ORM\Entity
 */
class RelUsuarioPerfil
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
     * @ORM\Column(name="FECHA_TERMINO", type="datetime", nullable=true)
     */
    private $fechaTermino;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PERFIL", referencedColumnName="ID")
     * })
     */
    private $idPerfil;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol", inversedBy="perfilesIndividuales")
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
     * Set fechaTermino.
     *
     * @param \DateTime|null $fechaTermino
     *
     * @return RelUsuarioPerfil
     */
    public function setFechaTermino($fechaTermino = null)
    {
        $this->fechaTermino = $fechaTermino;

        return $this;
    }

    /**
     * Get fechaTermino.
     *
     * @return \DateTime|null
     */
    public function getFechaTermino()
    {
        return $this->fechaTermino;
    }

    /**
     * Set idPerfil.
     *
     * @param \Rebsol\HermesBundle\Entity\Perfil $idPerfil
     *
     * @return RelUsuarioPerfil
     */
    public function setIdPerfil(\Rebsol\HermesBundle\Entity\Perfil $idPerfil)
    {
        $this->idPerfil = $idPerfil;

        return $this;
    }

    /**
     * Get idPerfil.
     *
     * @return \Rebsol\HermesBundle\Entity\Perfil
     */
    public function getIdPerfil()
    {
        return $this->idPerfil;
    }

    /**
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario
     *
     * @return RelUsuarioPerfil
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return RelUsuarioPerfil
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
}
