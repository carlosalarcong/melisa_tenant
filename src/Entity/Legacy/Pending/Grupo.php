<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grupo
 *
 * @ORM\Table(name="grupo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\GrupoRepository")
 */
class Grupo
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
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ADMINISTRADOR", type="integer", nullable=true)
     */
    private $administrador;

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
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="RelGrupoPerfil", mappedBy="idGrupo")
     */
    private $perfiles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->perfiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return Grupo
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set administrador.
     *
     * @param int|null $administrador
     *
     * @return Grupo
     */
    public function setAdministrador($administrador = null)
    {
        $this->administrador = $administrador;

        return $this;
    }

    /**
     * Get administrador.
     *
     * @return int|null
     */
    public function getAdministrador()
    {
        return $this->administrador;
    }

    /**
     * Add perfile.
     *
     * @param \Rebsol\HermesBundle\Entity\RelGrupoPerfil $perfile
     *
     * @return Grupo
     */
    public function addPerfile(\Rebsol\HermesBundle\Entity\RelGrupoPerfil $perfile)
    {
        $this->perfiles[] = $perfile;

        return $this;
    }

    /**
     * Remove perfile.
     *
     * @param \Rebsol\HermesBundle\Entity\RelGrupoPerfil $perfile
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerfile(\Rebsol\HermesBundle\Entity\RelGrupoPerfil $perfile)
    {
        return $this->perfiles->removeElement($perfile);
    }

    /**
     * Get perfiles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerfiles()
    {
        return $this->perfiles;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return Grupo
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa = null)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa|null
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Grupo
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
