<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estado
 *
 * @ORM\Table(name="estado")
 * @ORM\Entity
 */
class Estado
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
     * @ORM\Column(name="NOMBRE_ESTADO", type="string", length=45, nullable=false)
     */
    private $nombreEstado;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Estado
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
     * Set nombreEstado.
     *
     * @param string $nombreEstado
     *
     * @return Estado
     */
    public function setNombreEstado($nombreEstado)
    {
        $this->nombreEstado = $nombreEstado;

        return $this;
    }

    /**
     * Get nombreEstado.
     *
     * @return string
     */
    public function getNombreEstado()
    {
        return $this->nombreEstado;
    }
}
