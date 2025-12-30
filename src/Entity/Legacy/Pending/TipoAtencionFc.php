<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoAtencionFc
 *
 * @ORM\Table(name="tipo_atencion_fc")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoAtencionFcRepository")
 */
class TipoAtencionFc
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
     * @ORM\Column(name="NOMBRE_TIPO_ATENCION_FC", type="string", length=255, nullable=true)
     */
    private $nombreTipoAtencionFc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RUTA", type="string", length=255, nullable=true)
     */
    private $ruta;

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
     * @return TipoAtencionFc
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
     * Set nombreTipoAtencionFc.
     *
     * @param string|null $nombreTipoAtencionFc
     *
     * @return TipoAtencionFc
     */
    public function setNombreTipoAtencionFc($nombreTipoAtencionFc = null)
    {
        $this->nombreTipoAtencionFc = $nombreTipoAtencionFc;

        return $this;
    }

    /**
     * Get nombreTipoAtencionFc.
     *
     * @return string|null
     */
    public function getNombreTipoAtencionFc()
    {
        return $this->nombreTipoAtencionFc;
    }

    /**
     * Set ruta.
     *
     * @param string|null $ruta
     *
     * @return TipoAtencionFc
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return TipoAtencionFc
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
