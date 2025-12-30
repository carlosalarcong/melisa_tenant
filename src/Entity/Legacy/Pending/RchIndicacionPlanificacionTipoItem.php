<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RchIndicacionPlanificacionTipoItem
 *
 * @ORM\Table(name="rch_indicacion_planificacion_tipo_item")
 * @ORM\Entity
 */
class RchIndicacionPlanificacionTipoItem
{
    const INDICACION_FARMACOLOGICA = 1;
    const INDICACION_CUIDADO = 2;
    const INDICACION_PROCEDIMIENTO = 3;

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
     * @return RchIndicacionPlanificacionTipoItem
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
     * @return RchIndicacionPlanificacionTipoItem
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
     * @return RchIndicacionPlanificacionTipoItem
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