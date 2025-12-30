<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoPago
 *
 * @ORM\Table(name="estado_pago")
 * @ORM\Entity
 */
class EstadoPago
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
     * @ORM\Column(name="NOMBRE_ESTADO_PAGO", type="string", length=45, nullable=true)
     */
    private $nombreEstadoPago;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return EstadoPago
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
     * Set nombreEstadoPago.
     *
     * @param string|null $nombreEstadoPago
     *
     * @return EstadoPago
     */
    public function setNombreEstadoPago($nombreEstadoPago = null)
    {
        $this->nombreEstadoPago = $nombreEstadoPago;

        return $this;
    }

    /**
     * Get nombreEstadoPago.
     *
     * @return string|null
     */
    public function getNombreEstadoPago()
    {
        return $this->nombreEstadoPago;
    }
}
