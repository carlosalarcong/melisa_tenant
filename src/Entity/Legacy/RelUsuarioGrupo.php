<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelUsuarioGrupo
 *
 * @ORM\Table(name="rel_usuario_grupo")
 * @ORM\Entity
 */
class RelUsuarioGrupo
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
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \Grupo
     *
     * @ORM\ManyToOne(targetEntity="Grupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_GRUPO", referencedColumnName="ID")
     * })
     */
    private $idGrupo;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol", inversedBy="perfilesGrupo")
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
     * @return RelUsuarioGrupo
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
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario
     *
     * @return RelUsuarioGrupo
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
     * Set idGrupo.
     *
     * @param \Rebsol\HermesBundle\Entity\Grupo $idGrupo
     *
     * @return RelUsuarioGrupo
     */
    public function setIdGrupo(\Rebsol\HermesBundle\Entity\Grupo $idGrupo)
    {
        $this->idGrupo = $idGrupo;

        return $this;
    }

    /**
     * Get idGrupo.
     *
     * @return \Rebsol\HermesBundle\Entity\Grupo
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return RelUsuarioGrupo
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
