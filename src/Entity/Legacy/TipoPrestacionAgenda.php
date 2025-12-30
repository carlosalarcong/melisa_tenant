<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPrestacionAgenda
 *
 * @ORM\Table(name="tipo_prestacion_agenda")
 * @ORM\Entity
 */
class TipoPrestacionAgenda
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
     * @ORM\Column(name="NOMBRE_TIPO_PRESTACION_AGENDA", type="string", length=55, nullable=true)
     */
    private $nombreTipoPrestacionAgenda;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return TipoPrestacionAgenda
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
     * Set nombreTipoPrestacionAgenda.
     *
     * @param string|null $nombreTipoPrestacionAgenda
     *
     * @return TipoPrestacionAgenda
     */
    public function setNombreTipoPrestacionAgenda($nombreTipoPrestacionAgenda = null)
    {
        $this->nombreTipoPrestacionAgenda = $nombreTipoPrestacionAgenda;

        return $this;
    }

    /**
     * Get nombreTipoPrestacionAgenda.
     *
     * @return string|null
     */
    public function getNombreTipoPrestacionAgenda()
    {
        return $this->nombreTipoPrestacionAgenda;
    }
}
