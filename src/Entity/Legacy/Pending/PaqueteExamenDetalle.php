<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaqueteExamenDetalle
 *
 * @ORM\Table(name="paquete_examen_detalle")
 * @ORM\Entity
 */
class PaqueteExamenDetalle
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
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idAccionClinica;

    /**
     * @var \PaqueteExamen
     *
     * @ORM\ManyToOne(targetEntity="PaqueteExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_EXAMEN", referencedColumnName="ID")
     * })
     */
    private $idPaqueteExamen;



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
     * Set idPaqueteExamen.
     *
     * @param \Rebsol\HermesBundle\Entity\PaqueteExamen $idPaqueteExamen
     *
     * @return PaqueteExamenDetalle
     */
    public function setIdPaqueteExamen(\Rebsol\HermesBundle\Entity\PaqueteExamen $idPaqueteExamen)
    {
        $this->idPaqueteExamen = $idPaqueteExamen;

        return $this;
    }

    /**
     * Get idPaqueteExamen.
     *
     * @return \Rebsol\HermesBundle\Entity\PaqueteExamen
     */
    public function getIdPaqueteExamen()
    {
        return $this->idPaqueteExamen;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica
     *
     * @return PaqueteExamenDetalle
     */
    public function setIdAccionClinica(\Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \Rebsol\HermesBundle\Entity\AccionClinica
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return PaqueteExamenDetalle
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
}
