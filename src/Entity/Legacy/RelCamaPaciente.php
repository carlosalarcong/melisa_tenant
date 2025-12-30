<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelCamaPaciente
 *
 * @ORM\Table(name="rel_cama_paciente")
 * @ORM\Entity
 */
class RelCamaPaciente
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
     * @var \EstadoRelCamaPaciente
     *
     * @ORM\ManyToOne(targetEntity="EstadoRelCamaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_REL_CAMA_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idEstadoRelCamaPaciente;

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
     * @var \Cama
     *
     * @ORM\ManyToOne(targetEntity="Cama")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CAMA", referencedColumnName="ID")
     * })
     */
    private $idCama;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_INICIO", type="datetime", nullable=true)
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_FIN", type="datetime", nullable=true)
     */
    private $fechaFin;

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
     * Set idCama.
     *
     * @param \Rebsol\HermesBundle\Entity\Cama $idCama
     *
     * @return RelCamaPaciente
     */
    public function setIdCama(\Rebsol\HermesBundle\Entity\Cama $idCama)
    {
        $this->idCama = $idCama;

        return $this;
    }

    /**
     * Get idCama.
     *
     * @return \Rebsol\HermesBundle\Entity\Cama
     */
    public function getIdCama()
    {
        return $this->idCama;
    }

    /**
     * Set idPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\Paciente $idPaciente
     *
     * @return RelCamaPaciente
     */
    public function setIdPaciente(\Rebsol\HermesBundle\Entity\Paciente $idPaciente)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\Paciente
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idEstadoRelCamaPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoRelCamaPaciente $idEstadoRelCamaPaciente
     *
     * @return RelCamaPaciente
     */
    public function setIdEstadoRelCamaPaciente(\Rebsol\HermesBundle\Entity\EstadoRelCamaPaciente $idEstadoRelCamaPaciente)
    {
        $this->idEstadoRelCamaPaciente = $idEstadoRelCamaPaciente;

        return $this;
    }

    /**
     * Get idEstadoRelCamaPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoRelCamaPaciente
     */
    public function getIdEstadoRelCamaPaciente()
    {
        return $this->idEstadoRelCamaPaciente;
    }

    /**
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param \DateTime $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * @param \DateTime $fechaFin
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

}
