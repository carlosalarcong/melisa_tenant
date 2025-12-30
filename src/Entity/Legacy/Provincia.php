<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provincia
 *
 * @ORM\Table(name="provincia")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ProvinciaRepository")
 */
class Provincia
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CODIGO_PROVINCIA", type="integer", nullable=true)
     */
    private $codigoProvincia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_PROVINCIA", type="string", length=100, nullable=true)
     */
    private $nombreProvincia;

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
     * @var \Region
     *
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REGION", referencedColumnName="ID")
     * })
     */
    private $idRegion;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Provincia
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set codigoProvincia.
     *
     * @param int|null $codigoProvincia
     *
     * @return Provincia
     */
    public function setCodigoProvincia($codigoProvincia = null)
    {
        $this->codigoProvincia = $codigoProvincia;

        return $this;
    }

    /**
     * Get codigoProvincia.
     *
     * @return int|null
     */
    public function getCodigoProvincia()
    {
        return $this->codigoProvincia;
    }

    /**
     * Set nombreProvincia.
     *
     * @param string|null $nombreProvincia
     *
     * @return Provincia
     */
    public function setNombreProvincia($nombreProvincia = null)
    {
        $this->nombreProvincia = $nombreProvincia;

        return $this;
    }

    /**
     * Get nombreProvincia.
     *
     * @return string|null
     */
    public function getNombreProvincia()
    {
        return $this->nombreProvincia;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Provincia
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
     * Set idRegion.
     *
     * @param \Rebsol\HermesBundle\Entity\Region|null $idRegion
     *
     * @return Provincia
     */
    public function setIdRegion(\Rebsol\HermesBundle\Entity\Region $idRegion = null)
    {
        $this->idRegion = $idRegion;

        return $this;
    }

    /**
     * Get idRegion.
     *
     * @return \Rebsol\HermesBundle\Entity\Region|null
     */
    public function getIdRegion()
    {
        return $this->idRegion;
    }
}
