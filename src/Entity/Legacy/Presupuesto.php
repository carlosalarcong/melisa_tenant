<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presupuesto
 *
 * @ORM\Table(name="presupuesto")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PresupuestoRepository")
 */
class Presupuesto
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
     * @ORM\Column(name="NUMERO", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_PROBABLE", type="datetime", nullable=true)
     */
    private $fechaProbable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ES_AMBULATORIA", type="integer", nullable=true)
     */
    private $esAmbulatoria;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OTRO_ORIGEN", type="string", length=255, nullable=true)
     */
    private $otroOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="PIE_PRESUPUESTO", type="text", length=0, nullable=false)
     */
    private $piePresupuesto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="CON_HONORARIOS", type="boolean", nullable=false, options={"default"="1"})
     */
    private $conHonorarios = true;

    /**
     * @var \PqPlan
     *
     * @ORM\ManyToOne(targetEntity="PqPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PQ_PLAN", referencedColumnName="ID")
     * })
     */
    private $idPqPlan;

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
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONVENIO", referencedColumnName="ID")
     * })
     */
    private $idConvenio;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PERSONA", referencedColumnName="ID")
     * })
     */
    private $idPersona;

    /**
     * @var \TipoCuenta
     *
     * @ORM\ManyToOne(targetEntity="TipoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idTipoCuenta;

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
     * @var \PrPlan
     *
     * @ORM\ManyToOne(targetEntity="PrPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PR_PLAN", referencedColumnName="ID")
     * })
     */
    private $idPrPlan;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROFESIONAL", referencedColumnName="ID")
     * })
     */
    private $idProfesional;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FINANCIADOR", referencedColumnName="ID")
     * })
     */
    private $idFinanciador;

    /**
     * @var \Origen
     *
     * @ORM\ManyToOne(targetEntity="Origen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ORIGEN", referencedColumnName="ID")
     * })
     */
    private $idOrigen;



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
     * Set numero.
     *
     * @param int $numero
     *
     * @return Presupuesto
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set fechaProbable.
     *
     * @param \DateTime|null $fechaProbable
     *
     * @return Presupuesto
     */
    public function setFechaProbable($fechaProbable = null)
    {
        $this->fechaProbable = $fechaProbable;

        return $this;
    }

    /**
     * Get fechaProbable.
     *
     * @return \DateTime|null
     */
    public function getFechaProbable()
    {
        return $this->fechaProbable;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Presupuesto
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set esAmbulatoria.
     *
     * @param int|null $esAmbulatoria
     *
     * @return Presupuesto
     */
    public function setEsAmbulatoria($esAmbulatoria = null)
    {
        $this->esAmbulatoria = $esAmbulatoria;

        return $this;
    }

    /**
     * Get esAmbulatoria.
     *
     * @return int|null
     */
    public function getEsAmbulatoria()
    {
        return $this->esAmbulatoria;
    }

    /**
     * Set otroOrigen.
     *
     * @param string|null $otroOrigen
     *
     * @return Presupuesto
     */
    public function setOtroOrigen($otroOrigen = null)
    {
        $this->otroOrigen = $otroOrigen;

        return $this;
    }

    /**
     * Get otroOrigen.
     *
     * @return string|null
     */
    public function getOtroOrigen()
    {
        return $this->otroOrigen;
    }

    /**
     * Set piePresupuesto.
     *
     * @param string $piePresupuesto
     *
     * @return Presupuesto
     */
    public function setPiePresupuesto($piePresupuesto)
    {
        $this->piePresupuesto = $piePresupuesto;

        return $this;
    }

    /**
     * Get piePresupuesto.
     *
     * @return string
     */
    public function getPiePresupuesto()
    {
        return $this->piePresupuesto;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return Presupuesto
     */
    public function setObservacion($observacion = null)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion.
     *
     * @return string|null
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set conHonorarios.
     *
     * @param bool $conHonorarios
     *
     * @return Presupuesto
     */
    public function setConHonorarios($conHonorarios)
    {
        $this->conHonorarios = $conHonorarios;

        return $this;
    }

    /**
     * Get conHonorarios.
     *
     * @return bool
     */
    public function getConHonorarios()
    {
        return $this->conHonorarios;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return Presupuesto
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idFinanciador.
     *
     * @param \Rebsol\HermesBundle\Entity\Prevision $idFinanciador
     *
     * @return Presupuesto
     */
    public function setIdFinanciador(\Rebsol\HermesBundle\Entity\Prevision $idFinanciador)
    {
        $this->idFinanciador = $idFinanciador;

        return $this;
    }

    /**
     * Get idFinanciador.
     *
     * @return \Rebsol\HermesBundle\Entity\Prevision
     */
    public function getIdFinanciador()
    {
        return $this->idFinanciador;
    }

    /**
     * Set idConvenio.
     *
     * @param \Rebsol\HermesBundle\Entity\Prevision|null $idConvenio
     *
     * @return Presupuesto
     */
    public function setIdConvenio(\Rebsol\HermesBundle\Entity\Prevision $idConvenio = null)
    {
        $this->idConvenio = $idConvenio;

        return $this;
    }

    /**
     * Get idConvenio.
     *
     * @return \Rebsol\HermesBundle\Entity\Prevision|null
     */
    public function getIdConvenio()
    {
        return $this->idConvenio;
    }

    /**
     * Set idTipoCuenta.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoCuenta $idTipoCuenta
     *
     * @return Presupuesto
     */
    public function setIdTipoCuenta(\Rebsol\HermesBundle\Entity\TipoCuenta $idTipoCuenta)
    {
        $this->idTipoCuenta = $idTipoCuenta;

        return $this;
    }

    /**
     * Get idTipoCuenta.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoCuenta
     */
    public function getIdTipoCuenta()
    {
        return $this->idTipoCuenta;
    }

    /**
     * Set idOrigen.
     *
     * @param \Rebsol\HermesBundle\Entity\Origen|null $idOrigen
     *
     * @return Presupuesto
     */
    public function setIdOrigen(\Rebsol\HermesBundle\Entity\Origen $idOrigen = null)
    {
        $this->idOrigen = $idOrigen;

        return $this;
    }

    /**
     * Get idOrigen.
     *
     * @return \Rebsol\HermesBundle\Entity\Origen|null
     */
    public function getIdOrigen()
    {
        return $this->idOrigen;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Presupuesto
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
     * Set idProfesional.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idProfesional
     *
     * @return Presupuesto
     */
    public function setIdProfesional(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idProfesional)
    {
        $this->idProfesional = $idProfesional;

        return $this;
    }

    /**
     * Get idProfesional.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdProfesional()
    {
        return $this->idProfesional;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return Presupuesto
     */
    public function setIdUsuarioCreacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idPersona.
     *
     * @param \Rebsol\HermesBundle\Entity\Persona $idPersona
     *
     * @return Presupuesto
     */
    public function setIdPersona(\Rebsol\HermesBundle\Entity\Persona $idPersona)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \Rebsol\HermesBundle\Entity\Persona
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }

    /**
     * Set idPrPlan.
     *
     * @param \Rebsol\HermesBundle\Entity\PrPlan|null $idPrPlan
     *
     * @return Presupuesto
     */
    public function setIdPrPlan(\Rebsol\HermesBundle\Entity\PrPlan $idPrPlan = null)
    {
        $this->idPrPlan = $idPrPlan;

        return $this;
    }

    /**
     * Get idPrPlan.
     *
     * @return \Rebsol\HermesBundle\Entity\PrPlan|null
     */
    public function getIdPrPlan()
    {
        return $this->idPrPlan;
    }

    /**
     * Set idPqPlan.
     *
     * @param \Rebsol\HermesBundle\Entity\PqPlan|null $idPqPlan
     *
     * @return Presupuesto
     */
    public function setIdPqPlan(\Rebsol\HermesBundle\Entity\PqPlan $idPqPlan = null)
    {
        $this->idPqPlan = $idPqPlan;

        return $this;
    }

    /**
     * Get idPqPlan.
     *
     * @return \Rebsol\HermesBundle\Entity\PqPlan|null
     */
    public function getIdPqPlan()
    {
        return $this->idPqPlan;
    }
}
