<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramaPatologia
 *
 * @ORM\Table(name="programa_patologia")
 * @ORM\Entity
 */
class ProgramaPatologia
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
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \Patologia
     *
     * @ORM\ManyToOne(targetEntity="Patologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PATOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idPatologia;

    /**
     * @var \Programa
     *
     * @ORM\ManyToOne(targetEntity="Programa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROGRAMA", referencedColumnName="ID")
     * })
     */
    private $idPrograma;

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
     * @return ProgramaPatologia
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return ProgramaPatologia
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
     * Set idPatologia.
     *
     * @param \Rebsol\HermesBundle\Entity\Patologia|null $idPatologia
     *
     * @return ProgramaPatologia
     */
    public function setIdPatologia(\Rebsol\HermesBundle\Entity\Patologia $idPatologia = null)
    {
        $this->idPatologia = $idPatologia;

        return $this;
    }

    /**
     * Get idPatologia.
     *
     * @return \Rebsol\HermesBundle\Entity\Patologia|null
     */
    public function getIdPatologia()
    {
        return $this->idPatologia;
    }

    /**
     * Set idPrograma.
     *
     * @param \Rebsol\HermesBundle\Entity\Programa|null $idPrograma
     *
     * @return ProgramaPatologia
     */
    public function setIdPrograma(\Rebsol\HermesBundle\Entity\Programa $idPrograma = null)
    {
        $this->idPrograma = $idPrograma;

        return $this;
    }

    /**
     * Get idPrograma.
     *
     * @return \Rebsol\HermesBundle\Entity\Programa|null
     */
    public function getIdPrograma()
    {
        return $this->idPrograma;
    }
}
