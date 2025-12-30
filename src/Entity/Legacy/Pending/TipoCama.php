<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoCama
 *
 * @ORM\Table(name="tipo_cama")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoCamaRepository")
 */
class TipoCama
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
     * @ORM\Column(name="NOMBRE", type="string", length=100, nullable=false)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_VIRTUAL", type="boolean", nullable=false)
     */
    private $esVirtual;

    /**
     * @var \CategoriaCama
     *
     * @ORM\ManyToOne(targetEntity="CategoriaCama")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CATEGORIA_CAMA", referencedColumnName="ID")
     * })
     */
    private $idCategoriaCama;

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
     * @param string $nombre
     *
     * @return TipoCama
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set esVirtual.
     *
     * @param bool $esVirtual
     *
     * @return TipoCama
     */
    public function setEsVirtual($esVirtual)
    {
        $this->esVirtual = $esVirtual;

        return $this;
    }

    /**
     * Get esVirtual.
     *
     * @return bool
     */
    public function getEsVirtual()
    {
        return $this->esVirtual;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return TipoCama
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return TipoCama
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
     * Set idCategoriaCama.
     *
     * @param \Rebsol\HermesBundle\Entity\CategoriaCama $idCategoriaCama
     *
     * @return TipoCama
     */
    public function setIdCategoriaCama(\Rebsol\HermesBundle\Entity\CategoriaCama $idCategoriaCama)
    {
        $this->idCategoriaCama = $idCategoriaCama;

        return $this;
    }

    /**
     * Get idCategoriaCama.
     *
     * @return \Rebsol\HermesBundle\Entity\CategoriaCama
     */
    public function getIdCategoriaCama()
    {
        return $this->idCategoriaCama;
    }
}
