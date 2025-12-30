<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cama
 *
 * @ORM\Table(name="cama")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\CamaRepository")
 * @Gedmo\Loggable
 */
class Cama
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
     * @Gedmo\Versioned
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
     * @var int
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="ORDEN", type="integer", nullable=false)
     */
    private $orden;

    /**
     * @var \TipoCama
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="TipoCama")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_CAMA", referencedColumnName="ID")
     * })
     */
    private $idTipoCama;

    /**
     * @var \EstadoCama
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="EstadoCama")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_CAMA", referencedColumnName="ID")
     * })
     */
    private $idEstadoCama;

    /**
     * @var \Sala
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Sala")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SALA", referencedColumnName="ID")
     * })
     */
    private $idSala;



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
     * @return Cama
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
     * Set orden.
     *
     * @param int $orden
     *
     * @return Cama
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set esVirtual.
     *
     * @param bool $esVirtual
     *
     * @return Cama
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
     * Set idEstadoCama.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoCama $idEstadoCama
     *
     * @return Cama
     */
    public function setIdEstadoCama(\Rebsol\HermesBundle\Entity\EstadoCama $idEstadoCama)
    {
        $this->idEstadoCama = $idEstadoCama;

        return $this;
    }

    /**
     * Get idEstadoCama.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoCama
     */
    public function getIdEstadoCama()
    {
        return $this->idEstadoCama;
    }

    /**
     * Set idSala.
     *
     * @param \Rebsol\HermesBundle\Entity\Sala $idSala
     *
     * @return Cama
     */
    public function setIdSala(\Rebsol\HermesBundle\Entity\Sala $idSala)
    {
        $this->idSala = $idSala;

        return $this;
    }

    /**
     * Get idSala.
     *
     * @return \Rebsol\HermesBundle\Entity\Sala
     */
    public function getIdSala()
    {
        return $this->idSala;
    }

    /**
     * Set idTipoCama.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoCama $idTipoCama
     *
     * @return Cama
     */
    public function setIdTipoCama(\Rebsol\HermesBundle\Entity\TipoCama $idTipoCama)
    {
        $this->idTipoCama = $idTipoCama;

        return $this;
    }

    /**
     * Get idTipoCama.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoCama
     */
    public function getIdTipoCama()
    {
        return $this->idTipoCama;
    }
}
