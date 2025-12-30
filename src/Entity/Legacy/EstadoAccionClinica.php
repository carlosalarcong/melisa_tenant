<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoAccionClinica
 *
 * @ORM\Table(name="estado_accion_clinica")
 * @ORM\Entity
 */
class EstadoAccionClinica
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ESTADO_ACCION_CLINICA", type="string", length=45, nullable=true)
     */
    private $nombreEstadoAccionClinica;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return EstadoAccionClinica
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
     * Set nombreEstadoAccionClinica.
     *
     * @param string|null $nombreEstadoAccionClinica
     *
     * @return EstadoAccionClinica
     */
    public function setNombreEstadoAccionClinica($nombreEstadoAccionClinica = null)
    {
        $this->nombreEstadoAccionClinica = $nombreEstadoAccionClinica;

        return $this;
    }

    /**
     * Get nombreEstadoAccionClinica.
     *
     * @return string|null
     */
    public function getNombreEstadoAccionClinica()
    {
        return $this->nombreEstadoAccionClinica;
    }
}
