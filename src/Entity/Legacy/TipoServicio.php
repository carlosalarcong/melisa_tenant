<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TipoServicio
 *
 * @ORM\Table(name="tipo_servicio")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoServicioRepository")
 * @Gedmo\Loggable
 */
class TipoServicio
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
     * @Gedmo\Versioned
     * @ORM\Column(name="NOMBRE", type="string", length=100, nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

    /**
     * @var \CategoriaServicio
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="CategoriaServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CATEGORIA_SERVICIO", referencedColumnName="ID")
     * })
     */
    private $idCategoriaServicio;

    /**
     * @var \Estado
     *
     * @Gedmo\Versioned
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
     * @param string|null $nombre
     *
     * @return TipoServicio
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
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return TipoServicio
     */
    public function setValorDefault($valorDefault = null)
    {
        $this->valorDefault = $valorDefault;

        return $this;
    }

    /**
     * Get valorDefault.
     *
     * @return int|null
     */
    public function getValorDefault()
    {
        return $this->valorDefault;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return TipoServicio
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
     * @return TipoServicio
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

    /**
     * Set idCategoriaServicio.
     *
     * @param \Rebsol\HermesBundle\Entity\CategoriaServicio|null $idCategoriaServicio
     *
     * @return TipoServicio
     */
    public function setIdCategoriaServicio(\Rebsol\HermesBundle\Entity\CategoriaServicio $idCategoriaServicio = null)
    {
        $this->idCategoriaServicio = $idCategoriaServicio;

        return $this;
    }

    /**
     * Get idCategoriaServicio.
     *
     * @return \Rebsol\HermesBundle\Entity\CategoriaServicio|null
     */
    public function getIdCategoriaServicio()
    {
        return $this->idCategoriaServicio;
    }
}
