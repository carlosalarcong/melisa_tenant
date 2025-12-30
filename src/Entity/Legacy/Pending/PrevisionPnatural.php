<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrevisionPnatural
 *
 * @ORM\Table(name="prevision_pnatural")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PrevisionPnaturalRepository")
 */
class PrevisionPnatural
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
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_PREVISION", type="datetime", nullable=false)
     */
    private $fechaPrevision;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=100, nullable=true)
     */
    private $descripcion;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PREVISION", referencedColumnName="ID")
     * })
     */
    private $idPrevision;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONVENIO", referencedColumnName="ID")
     * })
     */
    private $idConvenio;

    /**
     * @var \Paciente
     *
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPaciente;

    /**
     * @var \Pnatural
     *
     * @ORM\ManyToOne(targetEntity="Pnatural")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PNATURAL", referencedColumnName="ID")
     * })
     */
    private $idPnatural;



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
     * Set fechaPrevision.
     *
     * @param \DateTime $fechaPrevision
     *
     * @return PrevisionPnatural
     */
    public function setFechaPrevision($fechaPrevision)
    {
        $this->fechaPrevision = $fechaPrevision;

        return $this;
    }

    /**
     * Get fechaPrevision.
     *
     * @return \DateTime
     */
    public function getFechaPrevision()
    {
        return $this->fechaPrevision;
    }

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return PrevisionPnatural
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente|null $idPaciente
     *
     * @return PrevisionPnatural
     */
    public function setIdPaciente(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente $idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idPnatural.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural $idPnatural
     *
     * @return PrevisionPnatural
     */
    public function setIdPnatural(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural $idPnatural)
    {
        $this->idPnatural = $idPnatural;

        return $this;
    }

    /**
     * Get idPnatural.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural
     */
    public function getIdPnatural()
    {
        return $this->idPnatural;
    }

    /**
     * Set idPrevision.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idPrevision
     *
     * @return PrevisionPnatural
     */
    public function setIdPrevision(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idPrevision = null)
    {
        $this->idPrevision = $idPrevision;

        return $this;
    }

    /**
     * Get idPrevision.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision
     */
    public function getIdPrevision()
    {
        return $this->idPrevision;
    }

    /**
     * Set idConvenio.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision|null $idConvenio
     *
     * @return PrevisionPnatural
     */
    public function setIdConvenio(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idConvenio = null)
    {
        $this->idConvenio = $idConvenio;

        return $this;
    }

    /**
     * Get idConvenio.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision|null
     */
    public function getIdConvenio()
    {
        return $this->idConvenio;
    }
}
