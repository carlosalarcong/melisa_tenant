<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemAtencionFc
 *
 * @ORM\Table(name="item_atencion_fc")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ItemAtencionFcRepository")
 */
class ItemAtencionFc
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ITEM_ATENCION_FC", type="string", length=255, nullable=true)
     */
    private $nombreItemAtencionFc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IMAGEN", type="string", length=100, nullable=true)
     */
    private $imagen;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ORDEN", type="integer", nullable=true)
     */
    private $orden;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RUTA", type="string", length=255, nullable=true)
     */
    private $ruta;

    /**
     * @var int
     *
     * @ORM\Column(name="TIEMPO_LIMITE", type="integer", nullable=false)
     */
    private $tiempoLimite;

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
     * @return ItemAtencionFc
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
     * Set nombreItemAtencionFc.
     *
     * @param string|null $nombreItemAtencionFc
     *
     * @return ItemAtencionFc
     */
    public function setNombreItemAtencionFc($nombreItemAtencionFc = null)
    {
        $this->nombreItemAtencionFc = $nombreItemAtencionFc;

        return $this;
    }

    /**
     * Get nombreItemAtencionFc.
     *
     * @return string|null
     */
    public function getNombreItemAtencionFc()
    {
        return $this->nombreItemAtencionFc;
    }

    /**
     * Set imagen.
     *
     * @param string|null $imagen
     *
     * @return ItemAtencionFc
     */
    public function setImagen($imagen = null)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen.
     *
     * @return string|null
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set orden.
     *
     * @param int|null $orden
     *
     * @return ItemAtencionFc
     */
    public function setOrden($orden = null)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return int|null
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set ruta.
     *
     * @param string|null $ruta
     *
     * @return ItemAtencionFc
     */
    public function setRuta($ruta = null)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta.
     *
     * @return string|null
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set tiempoLimite.
     *
     * @param int $tiempoLimite
     *
     * @return ItemAtencionFc
     */
    public function setTiempoLimite($tiempoLimite)
    {
        $this->tiempoLimite = $tiempoLimite;

        return $this;
    }

    /**
     * Get tiempoLimite.
     *
     * @return int
     */
    public function getTiempoLimite()
    {
        return $this->tiempoLimite;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return ItemAtencionFc
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
