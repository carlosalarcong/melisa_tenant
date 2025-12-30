<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaquetePrestacion
 *
 * @ORM\Table(name="paquete_prestacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PaquetePrestacionRepository")
 */
class PaquetePrestacion
{

    const TIPO_STRING = "Paquete Prestación";

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
     * @var bool
     *
     * @ORM\Column(name="ES_PROGRAMA", type="boolean", nullable=false)
     */
    private $esPrograma;

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
     * @ORM\ManyToOne(targetEntity="AccionClinica", cascade={"persist"})
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
     * @ORM\OneToMany(targetEntity="PaquetePrestacionDetalle", mappedBy="idPaquetePrestacion", cascade={"persist"})
     */
    private $paquetePrestacionDetalles;

    private $tieneExamenes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paquetePrestacionDetalles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return bool
     */
    public function getEsPrograma()
    {
        return $this->esPrograma;
    }

    /**
     * @param bool $esPrograma
     */
    public function setEsPrograma($esPrograma)
    {
        $this->esPrograma = $esPrograma;
    }

    /**
     * Add paquetePrestacionDetalle.
     *
     * @param \Rebsol\HermesBundle\Entity\PaquetePrestacionDetalle $paquetePrestacionDetalle
     *
     * @return PaquetePrestacion
     */
    public function addIdPaquetePrestacionDetalle(\Rebsol\HermesBundle\Entity\PaquetePrestacionDetalle $paquetePrestacionDetalle)
    {
        $paquetePrestacionDetalle->setIdPaquetePrestacion($this);
        $this->paquetePrestacionDetalles[] = $paquetePrestacionDetalle;

        return $this;
    }

    /**
     * Remove paquetePrestacionDetalle.
     *
     * @param \Rebsol\HermesBundle\Entity\PaquetePrestacionDetalle $paquetePrestacionDetalle
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdPaquetePrestacionDetalle(\Rebsol\HermesBundle\Entity\PaquetePrestacionDetalle $paquetePrestacionDetalle)
    {
        return $this->paquetePrestacionDetalles->removeElement($paquetePrestacionDetalle);
    }

    /**
     * Get paquetePrestacionDetalles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdPaquetePrestacionDetalles()
    {
        return $this->paquetePrestacionDetalles;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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
     * @return PaquetePrestacion
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

    public function setTieneExamenes($tieneExamenes)
    {
        $this->tieneExamenes = $tieneExamenes;

        return $this;
    }

    public function getTieneExamenes()
    {
        return $this->tieneExamenes;
    }
}
