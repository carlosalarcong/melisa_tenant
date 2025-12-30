<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comuna
 *
 * @ORM\Table(name="comuna")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ComunaRepository")
 */
class Comuna
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
     * @ORM\Column(name="CODIGO_COMUNA", type="integer", nullable=true)
     */
    private $codigoComuna;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_COMUNA", type="string", length=150, nullable=true)
     */
    private $nombreComuna;

    /**
     * @var \Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROVINCIA", referencedColumnName="ID")
     * })
     */
    private $idProvincia;

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
     * Set id.
     *
     * @param int $id
     *
     * @return Comuna
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
     * Set codigoComuna.
     *
     * @param int|null $codigoComuna
     *
     * @return Comuna
     */
    public function setCodigoComuna($codigoComuna = null)
    {
        $this->codigoComuna = $codigoComuna;

        return $this;
    }

    /**
     * Get codigoComuna.
     *
     * @return int|null
     */
    public function getCodigoComuna()
    {
        return $this->codigoComuna;
    }

    /**
     * Set nombreComuna.
     *
     * @param string|null $nombreComuna
     *
     * @return Comuna
     */
    public function setNombreComuna($nombreComuna = null)
    {
        $this->nombreComuna = $nombreComuna;

        return $this;
    }

    /**
     * Get nombreComuna.
     *
     * @return string|null
     */
    public function getNombreComuna()
    {
        return $this->nombreComuna;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Comuna
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
     * Set idProvincia.
     *
     * @param \Rebsol\HermesBundle\Entity\Provincia|null $idProvincia
     *
     * @return Comuna
     */
    public function setIdProvincia(\Rebsol\HermesBundle\Entity\Provincia $idProvincia = null)
    {
        $this->idProvincia = $idProvincia;

        return $this;
    }

    /**
     * Get idProvincia.
     *
     * @return \Rebsol\HermesBundle\Entity\Provincia|null
     */
    public function getIdProvincia()
    {
        return $this->idProvincia;
    }
}
