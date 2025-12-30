<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaqueteExamen
 *
 * @ORM\Table(name="paquete_examen")
 * @ORM\Entity
 */
class PaqueteExamen
{

    const TIPO_STRING = "Paquete Examen";

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
     * @ORM\Column(name="CODIGO", type="string", length=50, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ULTIMA_MODIFICACION", type="datetime", nullable=true)
     */
    private $fechaUltimaModificacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="FACTURABLE", type="boolean", nullable=false)
     */
    private $facturable;

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
     * @var \Bodega
     *
     * @ORM\ManyToOne(targetEntity="Bodega")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CENTRO_COSTO", referencedColumnName="ID")
     * })
     */
    private $idCentroCosto;

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
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ULTIMA_MODIFICACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioUltimaModificacion;


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="PaqueteExamenDetalle", mappedBy="idPaqueteExamen")
     */
    private $paqueteExamenDetalles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paqueteExamenDetalles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fechaCreacion = new \DateTime();
        $this->fechaUltimaModificacion = new \DateTime();
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
     * Set codigo.
     *
     * @param string $codigo
     *
     * @return PaqueteExamen
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return PaqueteExamen
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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return PaqueteExamen
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
     * Set fechaUltimaModificacion.
     *
     * @param \DateTime|null $fechaUltimaModificacion
     *
     * @return PaqueteExamen
     */
    public function setFechaUltimaModificacion($fechaUltimaModificacion = null)
    {
        $this->fechaUltimaModificacion = $fechaUltimaModificacion;

        return $this;
    }

    /**
     * Get fechaUltimaModificacion.
     *
     * @return \DateTime|null
     */
    public function getFechaUltimaModificacion()
    {
        return $this->fechaUltimaModificacion;
    }

    /**
     * Set facturable.
     *
     * @param bool $facturable
     *
     * @return PaqueteExamen
     */
    public function setFacturable($facturable)
    {
        $this->facturable = $facturable;

        return $this;
    }

    /**
     * Get facturable.
     *
     * @return bool
     */
    public function getFacturable()
    {
        return $this->facturable;
    }

    /**
     * Add paqueteExamenDetalle.
     *
     * @param \Rebsol\HermesBundle\Entity\PaqueteExamenDetalle $paqueteExamenDetalle
     *
     * @return PaqueteExamen
     */
    public function addPaqueteExamenDetalle(\Rebsol\HermesBundle\Entity\PaqueteExamenDetalle $paqueteExamenDetalle)
    {
        $this->paqueteExamenDetalles[] = $paqueteExamenDetalle;

        return $this;
    }

    /**
     * Remove paqueteExamenDetalle.
     *
     * @param \Rebsol\HermesBundle\Entity\PaqueteExamenDetalle $paqueteExamenDetalle
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePaqueteExamenDetalle(\Rebsol\HermesBundle\Entity\PaqueteExamenDetalle $paqueteExamenDetalle)
    {
        return $this->paqueteExamenDetalles->removeElement($paqueteExamenDetalle);
    }

    /**
     * Get paqueteExamenDetalles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaqueteExamenDetalles()
    {
        return $this->paqueteExamenDetalles;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return PaqueteExamen
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idCentroCosto.
     *
     * @param \Rebsol\HermesBundle\Entity\Bodega $idCentroCosto
     *
     * @return PaqueteExamen
     */
    public function setIdCentroCosto(\Rebsol\HermesBundle\Entity\Bodega $idCentroCosto)
    {
        $this->idCentroCosto = $idCentroCosto;

        return $this;
    }

    /**
     * Get idCentroCosto.
     *
     * @return \Rebsol\HermesBundle\Entity\Bodega
     */
    public function getIdCentroCosto()
    {
        return $this->idCentroCosto;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return PaqueteExamen
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
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return PaqueteExamen
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
     * Set idUsuarioUltimaModificacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioUltimaModificacion
     *
     * @return PaqueteExamen
     */
    public function setIdUsuarioUltimaModificacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioUltimaModificacion = null)
    {
        $this->idUsuarioUltimaModificacion = $idUsuarioUltimaModificacion;

        return $this;
    }

    /**
     * Get idUsuarioUltimaModificacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioUltimaModificacion()
    {
        return $this->idUsuarioUltimaModificacion;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica
     *
     * @return PaqueteExamen
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

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getClassNameFormatted()
    {
        return (string) self::TIPO_STRING;
    }

}
