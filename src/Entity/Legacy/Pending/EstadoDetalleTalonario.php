<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoDetalleTalonario
 *
 * @ORM\Table(name="estado_detalle_talonario")
 * @ORM\Entity
 */
class EstadoDetalleTalonario
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
     * @ORM\Column(name="NOMBRE", type="string", length=45, nullable=true)
     */
    private $nombre;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return EstadoDetalleTalonario
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
     * @param string|null $nombre
     *
     * @return EstadoDetalleTalonario
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }
}
