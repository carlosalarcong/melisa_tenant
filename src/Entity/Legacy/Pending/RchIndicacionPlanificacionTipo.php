<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RchIndicacionPlanificacionTipo
 *
 * @ORM\Table(name="rch_indicacion_planificacion_tipo")
 * @ORM\Entity
 */
class RchIndicacionPlanificacionTipo
{
    const PLANIFICACION_ESTANDAR = 1;
    const PLANIFICACION_UNICA = 2;
    const PLANIFICACION_VARIABLE = 3;
    const EJECUCION_SOS = 4;

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
     * @ORM\Column(name="NOMBRE", type="string", length=45, nullable=false)
     */
    private $nombre;

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
     * @return RchIndicacionPlanificacionTipo
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return RchIndicacionPlanificacionTipo
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
     * @return RchIndicacionPlanificacionTipo
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

}