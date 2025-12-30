<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Box
 *
 * @ORM\Table(name="box")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\BoxRepository")
 */
class Box
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
     * @ORM\Column(name="NOMBRE_BOX", type="string", length=255, nullable=false)
     */
    private $nombreBox;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPCION_BOX", type="text", length=0, nullable=false)
     */
    private $descripcionBox;

    /**
     * @var int
     *
     * @ORM\Column(name="PISO", type="integer", nullable=false)
     */
    private $piso;

    /**
     * @var \Ubicacion
     *
     * @ORM\ManyToOne(targetEntity="Ubicacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UBICACION", referencedColumnName="ID")
     * })
     */
    private $idUbicacion;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombreBox.
     *
     * @param string $nombreBox
     *
     * @return Box
     */
    public function setNombreBox($nombreBox)
    {
        $this->nombreBox = $nombreBox;

        return $this;
    }

    /**
     * Get nombreBox.
     *
     * @return string
     */
    public function getNombreBox()
    {
        return $this->nombreBox;
    }

    /**
     * Set descripcionBox.
     *
     * @param string $descripcionBox
     *
     * @return Box
     */
    public function setDescripcionBox($descripcionBox)
    {
        $this->descripcionBox = $descripcionBox;

        return $this;
    }

    /**
     * Get descripcionBox.
     *
     * @return string
     */
    public function getDescripcionBox()
    {
        return $this->descripcionBox;
    }

    /**
     * Set piso.
     *
     * @param int $piso
     *
     * @return Box
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso.
     *
     * @return int
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Box
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

    /**
     * Set idUbicacion.
     *
     * @param \Rebsol\HermesBundle\Entity\Ubicacion $idUbicacion
     *
     * @return Box
     */
    public function setIdUbicacion(\Rebsol\HermesBundle\Entity\Ubicacion $idUbicacion)
    {
        $this->idUbicacion = $idUbicacion;

        return $this;
    }

    /**
     * Get idUbicacion.
     *
     * @return \Rebsol\HermesBundle\Entity\Ubicacion
     */
    public function getIdUbicacion()
    {
        return $this->idUbicacion;
    }
}
