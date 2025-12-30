<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RchIndicacionPlanificacion
 *
 * @ORM\Table(name="rch_indicacion_planificacion")
 * @ORM\Entity
 */
class RchIndicacionPlanificacion
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
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $fechaCreacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_INICIO_HORA", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $fechaInicioHora;

    /**
     * @var \RelRchIndicacionPlanificacion
     *
     * @ORM\ManyToOne(targetEntity="RelRchIndicacionPlanificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REL_RCH_INDICACION_PLANIFICACION", referencedColumnName="ID")
     * })
     */
    private $idRelRchIndicacionPlanificacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

    /**
     * @var \RchRecetaDetallePrescripcionTipo
     *
     * @ORM\ManyToOne(targetEntity="RchRecetaDetallePrescripcionTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_RECETA_DETALLE_PRESCRIPCION_TIPO", referencedColumnName="ID")
     * })
     */
    private $idRchRecetaDetallePrescripcionTipo;

    /**
     * @var int
     *
     * @ORM\Column(name="INTERVALO", type="integer", nullable=true)
     */
    private $intervalo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="string", length=2000, nullable=true)
     */
    private $observacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_PERMANENTE", type="boolean", nullable=false)
     */
    private $esPermanente = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="CANTIDAD_DIAS_CONSUMO", type="integer", nullable=true)
     */
    private $cantidadDiasConsumo;

    /**
     * @var \RchRecetaOrigenMedicamento
     *
     * @ORM\ManyToOne(targetEntity="RchRecetaOrigenMedicamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_RECETA_ORIGEN_MEDICAMENTO", referencedColumnName="ID")
     * })
     */
    private $idRchRecetaOrigenMedicamento;

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

    /**
     * @return \DateTime
     */
    public function getFechaInicioHora()
    {
        return $this->fechaInicioHora;
    }

    /**
     * @param \DateTime $fechaInicioHora
     */
    public function setFechaInicioHora($fechaInicioHora)
    {
        $this->fechaInicioHora = $fechaInicioHora;
    }

    /**
     * @return \RelRchIndicacionPlanificacion
     */
    public function getIdRelRchIndicacionPlanificacion()
    {
        return $this->idRelRchIndicacionPlanificacion;
    }

    /**
     * @param \RelRchIndicacionPlanificacion $idRelRchIndicacionPlanificacion
     */
    public function setIdRelRchIndicacionPlanificacion($idRelRchIndicacionPlanificacion)
    {
        $this->idRelRchIndicacionPlanificacion = $idRelRchIndicacionPlanificacion;
    }

    /**
     * @return \UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * @param \UsuariosRebsol $idUsuarioCreacion
     */
    public function setIdUsuarioCreacion($idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;
    }

    /**
     * @return \RchRecetaDetallePrescripcionTipo
     */
    public function getIdRchRecetaDetallePrescripcionTipo()
    {
        return $this->idRchRecetaDetallePrescripcionTipo;
    }

    /**
     * @param \RchRecetaDetallePrescripcionTipo $idRchRecetaDetallePrescripcionTipo
     */
    public function setIdRchRecetaDetallePrescripcionTipo($idRchRecetaDetallePrescripcionTipo)
    {
        $this->idRchRecetaDetallePrescripcionTipo = $idRchRecetaDetallePrescripcionTipo;
    }

    /**
     * @return int
     */
    public function getIntervalo()
    {
        return $this->intervalo;
    }

    /**
     * @param int $intervalo
     */
    public function setIntervalo($intervalo)
    {
        $this->intervalo = $intervalo;
    }

    /**
     * @return string|null
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * @param string|null $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * @return bool
     */
    public function isEsPermanente()
    {
        return $this->esPermanente;
    }

    /**
     * @param bool $esPermanente
     */
    public function setEsPermanente($esPermanente)
    {
        $this->esPermanente = $esPermanente;
    }

    /**
     * @return int
     */
    public function getCantidadDiasConsumo()
    {
        return $this->cantidadDiasConsumo;
    }

    /**
     * @param int $cantidadDiasConsumo
     */
    public function setCantidadDiasConsumo($cantidadDiasConsumo)
    {
        $this->cantidadDiasConsumo = $cantidadDiasConsumo;
    }

    /**
     * @return \RchRecetaOrigenMedicamento
     */
    public function getIdRchRecetaOrigenMedicamento()
    {
        return $this->idRchRecetaOrigenMedicamento;
    }

    /**
     * @param \RchRecetaOrigenMedicamento $idRchRecetaOrigenMedicamento
     */
    public function setIdRchRecetaOrigenMedicamento($idRchRecetaOrigenMedicamento)
    {
        $this->idRchRecetaOrigenMedicamento = $idRchRecetaOrigenMedicamento;
    }

}