<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelRchIndicacionPlanificacion
 *
 * @ORM\Table(name="rel_rch_indicacion_planificacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\RelRchIndicacionPlanificacionRepository")
 */
class RelRchIndicacionPlanificacion
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
     * @var int
     *
     * @ORM\Column(name="ID_REL_INDICACION", type="integer", nullable=false, options={"comment"="Relacion blanda con la tablas {rch_receta_detalle, rch_rel_indicaciones_cuidados,  accion_clinica_paciente}"})
     */
    private $idRelIndicacion;

    /**
     * @var \RchIndicacionPlanificacionTipoItem
     *
     * @ORM\ManyToOne(targetEntity="RchIndicacionPlanificacionTipoItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_INDICACION_PLANIFICACION_TIPO_ITEM", referencedColumnName="ID")
     * })
     */
    private $idRchIndicacionPlanificacionTipoItem;

    /**
     * @var \RchIndicacionPlanificacionTipo
     *
     * @ORM\ManyToOne(targetEntity="RchIndicacionPlanificacionTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_INDICACION_PLANIFICACION_TIPO", referencedColumnName="ID")
     * })
     */
    private $idRchIndicacionPlanificacionTipo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $fechaCreacion;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getIdRelIndicacion()
    {
        return $this->idRelIndicacion;
    }

    /**
     * @param int $idRelIndicacion
     */
    public function setIdRelIndicacion($idRelIndicacion)
    {
        $this->idRelIndicacion = $idRelIndicacion;
    }

    /**
     * @return \RchIndicacionPlanificacionTipoItem
     */
    public function getIdRchIndicacionPlanificacionTipoItem()
    {
        return $this->idRchIndicacionPlanificacionTipoItem;
    }

    /**
     * @param \RchIndicacionPlanificacionTipoItem $idRchIndicacionPlanificacionTipoItem
     */
    public function setIdRchIndicacionPlanificacionTipoItem($idRchIndicacionPlanificacionTipoItem)
    {
        $this->idRchIndicacionPlanificacionTipoItem = $idRchIndicacionPlanificacionTipoItem;
    }

    /**
     * @return \RchIndicacionPlanificacionTipo
     */
    public function getIdRchIndicacionPlanificacionTipo()
    {
        return $this->idRchIndicacionPlanificacionTipo;
    }

    /**
     * @param \RchIndicacionPlanificacionTipo $idRchIndicacionPlanificacionTipo
     */
    public function setIdRchIndicacionPlanificacionTipo($idRchIndicacionPlanificacionTipo)
    {
        $this->idRchIndicacionPlanificacionTipo = $idRchIndicacionPlanificacionTipo;
    }

    /**
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * @param \DateTime $fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    }
}