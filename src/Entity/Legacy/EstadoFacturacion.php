<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoFacturacion
 *
 * @ORM\Table(name="estado_facturacion")
 * @ORM\Entity
 */
class EstadoFacturacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=100, nullable=false)
     */
    private $nombre;

    /**
     * @var \CategoriaFacturacion
     *
     * @ORM\ManyToOne(targetEntity="CategoriaFacturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CATEGORIA_FACTURACION", referencedColumnName="ID")
     * })
     */
    private $idCategoriaFacturacion;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return EstadoFacturacion
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
     * @return EstadoFacturacion
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
     * Set idCategoriaFacturacion.
     *
     * @param \Rebsol\HermesBundle\Entity\CategoriaFacturacion $idCategoriaFacturacion
     *
     * @return EstadoFacturacion
     */
    public function setIdCategoriaFacturacion(\Rebsol\HermesBundle\Entity\CategoriaFacturacion $idCategoriaFacturacion)
    {
        $this->idCategoriaFacturacion = $idCategoriaFacturacion;

        return $this;
    }

    /**
     * Get idCategoriaFacturacion.
     *
     * @return \Rebsol\HermesBundle\Entity\CategoriaFacturacion
     */
    public function getIdCategoriaFacturacion()
    {
        return $this->idCategoriaFacturacion;
    }
}
