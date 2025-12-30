<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoUsuarios
 *
 * @ORM\Table(name="estado_usuarios")
 * @ORM\Entity
 */
class EstadoUsuarios
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
     * @ORM\Column(name="NOMBRE_ESTADO_USUARIOS", type="string", length=50, nullable=true)
     */
    private $nombreEstadoUsuarios;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return EstadoUsuarios
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
     * Set nombreEstadoUsuarios.
     *
     * @param string|null $nombreEstadoUsuarios
     *
     * @return EstadoUsuarios
     */
    public function setNombreEstadoUsuarios($nombreEstadoUsuarios = null)
    {
        $this->nombreEstadoUsuarios = $nombreEstadoUsuarios;

        return $this;
    }

    /**
     * Get nombreEstadoUsuarios.
     *
     * @return string|null
     */
    public function getNombreEstadoUsuarios()
    {
        return $this->nombreEstadoUsuarios;
    }
}
