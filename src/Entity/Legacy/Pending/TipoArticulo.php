<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoArticulo
 *
 * @ORM\Table(name="tipo_articulo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoArticuloRepository")
 */
class TipoArticulo
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
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_FARMACO", type="boolean", nullable=false)
     */
    private $esFarmaco = '0';

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
     * @var \Bodega
     *
     * @ORM\ManyToOne(targetEntity="Bodega")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BODEGA", referencedColumnName="ID")
     * })
     */
    private $idBodega;



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
     * @return TipoArticulo
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
     * Set codigo.
     *
     * @param string $codigo
     *
     * @return TipoArticulo
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
     * Set esFarmaco.
     *
     * @param bool $esFarmaco
     *
     * @return TipoArticulo
     */
    public function setEsFarmaco($esFarmaco)
    {
        $this->esFarmaco = $esFarmaco;

        return $this;
    }

    /**
     * Get esFarmaco.
     *
     * @return bool
     */
    public function getEsFarmaco()
    {
        return $this->esFarmaco;
    }

    /**
     * Set idBodega.
     *
     * @param \Rebsol\HermesBundle\Entity\Bodega $idBodega
     *
     * @return TipoArticulo
     */
    public function setIdBodega(\Rebsol\HermesBundle\Entity\Bodega $idBodega)
    {
        $this->idBodega = $idBodega;

        return $this;
    }

    /**
     * Get idBodega.
     *
     * @return \Rebsol\HermesBundle\Entity\Bodega
     */
    public function getIdBodega()
    {
        return $this->idBodega;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return TipoArticulo
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
}
