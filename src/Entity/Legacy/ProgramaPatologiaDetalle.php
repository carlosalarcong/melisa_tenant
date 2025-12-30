<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramaPatologiaDetalle
 *
 * @ORM\Table(name="programa_patologia_detalle")
 * @ORM\Entity
 */
class ProgramaPatologiaDetalle
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
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_INTERNO", type="string", length=10, nullable=true)
     */
    private $codigoInterno;

    /**
     * @var \ProgramaPatologia
     *
     * @ORM\ManyToOne(targetEntity="ProgramaPatologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROGRAMA_PATOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idProgramaPatologia;

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
     * @return ProgramaPatologiaDetalle
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
     * Set codigoInterno.
     *
     * @param string|null $codigoInterno
     *
     * @return ProgramaPatologiaDetalle
     */
    public function setCodigoInterno($codigoInterno = null)
    {
        $this->codigoInterno = $codigoInterno;

        return $this;
    }

    /**
     * Get codigoInterno.
     *
     * @return string|null
     */
    public function getCodigoInterno()
    {
        return $this->codigoInterno;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return ProgramaPatologiaDetalle
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
     * Set idProgramaPatologia.
     *
     * @param \Rebsol\HermesBundle\Entity\ProgramaPatologia|null $idProgramaPatologia
     *
     * @return ProgramaPatologiaDetalle
     */
    public function setIdProgramaPatologia(\Rebsol\HermesBundle\Entity\ProgramaPatologia $idProgramaPatologia = null)
    {
        $this->idProgramaPatologia = $idProgramaPatologia;

        return $this;
    }

    /**
     * Get idProgramaPatologia.
     *
     * @return \Rebsol\HermesBundle\Entity\ProgramaPatologia|null
     */
    public function getIdProgramaPatologia()
    {
        return $this->idProgramaPatologia;
    }
}
