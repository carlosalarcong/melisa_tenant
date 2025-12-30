<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrPlan
 *
 * @ORM\Table(name="pr_plan")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PrPlanRepository")
 */
class PrPlan
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="es_paquete", type="boolean", nullable=true)
     */
    private $esPaquete;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="es_plan_teleconsulta", type="boolean", nullable=true)
     */
    private $esPlanTeleconsulta;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_INHABIL", type="boolean", nullable=false)
     */
    private $esInhabil = '0';

    /**
     * @var \PrPlan
     *
     * @ORM\ManyToOne(targetEntity="PrPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PR_PLAN_PAQUETE_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idPrPlanPaquetePrestacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAnulacion;

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
     * @var \RelSucursalPrevision
     *
     * @ORM\ManyToOne(targetEntity="RelSucursalPrevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REL_SUCURSAL_PREVISION", referencedColumnName="ID")
     * })
     */
    private $idRelSucursalPrevision;



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
     * @return PrPlan
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
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return PrPlan
     */
    public function setFechaAnulacion($fechaAnulacion = null)
    {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAnulacion()
    {
        return $this->fechaAnulacion;
    }

    /**
     * Set esPaquete.
     *
     * @param bool|null $esPaquete
     *
     * @return PrPlan
     */
    public function setEsPaquete($esPaquete = null)
    {
        $this->esPaquete = $esPaquete;

        return $this;
    }

    /**
     * Get esPaquete.
     *
     * @return bool|null
     */
    public function getEsPaquete()
    {
        return $this->esPaquete;
    }

    /**
     * Set esPlanTeleconsulta.
     *
     * @param bool|null $esPlanTeleconsulta
     *
     * @return PrPlan
     */
    public function setEsPlanTeleconsulta($esPlanTeleconsulta = null)
    {
        $this->esPlanTeleconsulta = $esPlanTeleconsulta;

        return $this;
    }

    /**
     * Get esPlanTeleconsulta.
     *
     * @return bool|null
     */
    public function getEsPlanTeleconsulta()
    {
        return $this->esPlanTeleconsulta;
    }

    /**
     * Set esInhabil.
     *
     * @param bool $esInhabil
     *
     * @return PrPlan
     */
    public function setEsInhabil($esInhabil)
    {
        $this->esInhabil = $esInhabil;

        return $this;
    }

    /**
     * Get esInhabil.
     *
     * @return bool
     */
    public function getEsInhabil()
    {
        return $this->esInhabil;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return PrPlan
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
     * Set idRelSucursalPrevision.
     *
     * @param \Rebsol\HermesBundle\Entity\RelSucursalPrevision $idRelSucursalPrevision
     *
     * @return PrPlan
     */
    public function setIdRelSucursalPrevision(\Rebsol\HermesBundle\Entity\RelSucursalPrevision $idRelSucursalPrevision)
    {
        $this->idRelSucursalPrevision = $idRelSucursalPrevision;

        return $this;
    }

    /**
     * Get idRelSucursalPrevision.
     *
     * @return \Rebsol\HermesBundle\Entity\RelSucursalPrevision
     */
    public function getIdRelSucursalPrevision()
    {
        return $this->idRelSucursalPrevision;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return PrPlan
     */
    public function setIdUsuarioAnulacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioAnulacion = null)
    {
        $this->idUsuarioAnulacion = $idUsuarioAnulacion;

        return $this;
    }

    /**
     * Get idUsuarioAnulacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAnulacion()
    {
        return $this->idUsuarioAnulacion;
    }

    /**
     * Set idPrPlanPaquetePrestacion.
     *
     * @param \Rebsol\HermesBundle\Entity\PrPlan|null $idPrPlanPaquetePrestacion
     *
     * @return PrPlan
     */
    public function setIdPrPlanPaquetePrestacion(\Rebsol\HermesBundle\Entity\PrPlan $idPrPlanPaquetePrestacion = null)
    {
        $this->idPrPlanPaquetePrestacion = $idPrPlanPaquetePrestacion;

        return $this;
    }

    /**
     * Get idPrPlanPaquetePrestacion.
     *
     * @return \Rebsol\HermesBundle\Entity\PrPlan|null
     */
    public function getIdPrPlanPaquetePrestacion()
    {
        return $this->idPrPlanPaquetePrestacion;
    }
}
