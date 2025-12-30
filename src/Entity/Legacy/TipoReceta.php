<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoReceta
 *
 * @ORM\Table(name="tipo_receta")
 * @ORM\Entity
 */
class TipoReceta
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
     * @ORM\Column(name="NOMBRE_TIPO_RECETA", type="string", length=45, nullable=true)
     */
    private $nombreTipoReceta;

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
     * @return TipoReceta
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
     * Set nombreTipoReceta.
     *
     * @param string|null $nombreTipoReceta
     *
     * @return TipoReceta
     */
    public function setNombreTipoReceta($nombreTipoReceta = null)
    {
        $this->nombreTipoReceta = $nombreTipoReceta;

        return $this;
    }

    /**
     * Get nombreTipoReceta.
     *
     * @return string|null
     */
    public function getNombreTipoReceta()
    {
        return $this->nombreTipoReceta;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return TipoReceta
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
