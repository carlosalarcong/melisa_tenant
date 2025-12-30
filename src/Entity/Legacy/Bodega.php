<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bodega
 *
 * @ORM\Table(name="bodega")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\BodegaRepository")
 */
class Bodega
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
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ABREVIADO", type="string", length=255, nullable=true)
     */
    private $nombreAbreviado;

    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var bool
     *
     * @ORM\Column(name="CREA_ARTICULO", type="boolean", nullable=false)
     */
    private $creaArticulo;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_VIRTUAL", type="boolean", nullable=false)
     */
    private $esVirtual = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_FARMACIA", type="boolean", nullable=false)
     */
    private $esFarmacia = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_RECEPCION_AUTOMATICA", type="boolean", nullable=false)
     */
    private $esRecepcionAutomatica = '0';

    /**
     * @var \Bodega
     *
     * @ORM\ManyToOne(targetEntity="Bodega")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BODEGA_PADRE", referencedColumnName="ID")
     * })
     */
    private $idBodegaPadre;

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
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO", referencedColumnName="ID")
     * })
     */
    private $idServicio;

    /**
     * @var \TipoBodega
     *
     * @ORM\ManyToOne(targetEntity="TipoBodega")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_BODEGA", referencedColumnName="ID")
     * })
     */
    private $idTipoBodega;



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
     * @return Bodega
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
     * Set nombreAbreviado.
     *
     * @param string|null $nombreAbreviado
     *
     * @return Bodega
     */
    public function setNombreAbreviado($nombreAbreviado = null)
    {
        $this->nombreAbreviado = $nombreAbreviado;

        return $this;
    }

    /**
     * Get nombreAbreviado.
     *
     * @return string|null
     */
    public function getNombreAbreviado()
    {
        return $this->nombreAbreviado;
    }

    /**
     * Set codigo.
     *
     * @param string $codigo
     *
     * @return Bodega
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
     * Set creaArticulo.
     *
     * @param bool $creaArticulo
     *
     * @return Bodega
     */
    public function setCreaArticulo($creaArticulo)
    {
        $this->creaArticulo = $creaArticulo;

        return $this;
    }

    /**
     * Get creaArticulo.
     *
     * @return bool
     */
    public function getCreaArticulo()
    {
        return $this->creaArticulo;
    }

    /**
     * Set esVirtual.
     *
     * @param bool $esVirtual
     *
     * @return Bodega
     */
    public function setEsVirtual($esVirtual)
    {
        $this->esVirtual = $esVirtual;

        return $this;
    }

    /**
     * Get esVirtual.
     *
     * @return bool
     */
    public function getEsVirtual()
    {
        return $this->esVirtual;
    }

    /**
     * Set esFarmacia.
     *
     * @param bool $esFarmacia
     *
     * @return Bodega
     */
    public function setEsFarmacia($esFarmacia)
    {
        $this->esFarmacia = $esFarmacia;

        return $this;
    }

    /**
     * Get esFarmacia.
     *
     * @return bool
     */
    public function getEsFarmacia()
    {
        return $this->esFarmacia;
    }

    /**
     * Set esRecepcionAutomatica.
     *
     * @param bool $esRecepcionAutomatica
     *
     * @return Bodega
     */
    public function setEsRecepcionAutomatica($esRecepcionAutomatica)
    {
        $this->esRecepcionAutomatica = $esRecepcionAutomatica;

        return $this;
    }

    /**
     * Get esRecepcionAutomatica.
     *
     * @return bool
     */
    public function getEsRecepcionAutomatica()
    {
        return $this->esRecepcionAutomatica;
    }

    /**
     * Set idServicio.
     *
     * @param \Rebsol\HermesBundle\Entity\Servicio|null $idServicio
     *
     * @return Bodega
     */
    public function setIdServicio(\Rebsol\HermesBundle\Entity\Servicio $idServicio = null)
    {
        $this->idServicio = $idServicio;

        return $this;
    }

    /**
     * Get idServicio.
     *
     * @return \Rebsol\HermesBundle\Entity\Servicio|null
     */
    public function getIdServicio()
    {
        return $this->idServicio;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Bodega
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
     * Set idTipoBodega.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoBodega|null $idTipoBodega
     *
     * @return Bodega
     */
    public function setIdTipoBodega(\Rebsol\HermesBundle\Entity\TipoBodega $idTipoBodega = null)
    {
        $this->idTipoBodega = $idTipoBodega;

        return $this;
    }

    /**
     * Get idTipoBodega.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoBodega|null
     */
    public function getIdTipoBodega()
    {
        return $this->idTipoBodega;
    }

    /**
     * Set idBodegaPadre.
     *
     * @param \Rebsol\HermesBundle\Entity\Bodega|null $idBodegaPadre
     *
     * @return Bodega
     */
    public function setIdBodegaPadre(\Rebsol\HermesBundle\Entity\Bodega $idBodegaPadre = null)
    {
        $this->idBodegaPadre = $idBodegaPadre;

        return $this;
    }

    /**
     * Get idBodegaPadre.
     *
     * @return \Rebsol\HermesBundle\Entity\Bodega|null
     */
    public function getIdBodegaPadre()
    {
        return $this->idBodegaPadre;
    }
}
