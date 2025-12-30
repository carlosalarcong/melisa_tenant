<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelGrupoPerfil
 *
 * @ORM\Table(name="rel_grupo_perfil")
 * @ORM\Entity
 */
class RelGrupoPerfil
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
     * @var \Grupo
     *
     * @ORM\ManyToOne(targetEntity="Grupo", inversedBy="perfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_GRUPO", referencedColumnName="ID")
     * })
     */
    private $idGrupo;



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
     * Set idGrupo.
     *
     * @param \Rebsol\HermesBundle\Entity\Grupo $idGrupo
     *
     * @return RelGrupoPerfil
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
     * Set idPerfil.
     *
     * @param \Rebsol\HermesBundle\Entity\Perfil $idPerfil
     *
     * @return RelGrupoPerfil
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return RelGrupoPerfil
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
