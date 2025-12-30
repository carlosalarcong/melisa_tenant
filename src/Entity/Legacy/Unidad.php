<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Unidad
 *
 * @ORM\Table(name="unidad")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\UnidadRepository")
 * @Gedmo\Loggable
 */
class Unidad
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
     * @var string|null
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="NOMBRE_UNIDAD", type="string", length=255, nullable=true)
     */
    private $nombreUnidad;

    /**
     * @var int|null
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

    /**
     * @var \TipoPrestacionExamen
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="TipoPrestacionExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PRESTACION_EXAMEN", referencedColumnName="ID")
     * })
     */
    private $idTipoPrestacionExamen;

    /**
     * @var \Estado
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \Sucursal
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;



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
     * Set nombreUnidad.
     *
     * @param string|null $nombreUnidad
     *
     * @return Unidad
     */
    public function setNombreUnidad($nombreUnidad = null)
    {
        $this->nombreUnidad = $nombreUnidad;

        return $this;
    }

    /**
     * Get nombreUnidad.
     *
     * @return string|null
     */
    public function getNombreUnidad()
    {
        return $this->nombreUnidad;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return Unidad
     */
    public function setValorDefault($valorDefault = null)
    {
        $this->valorDefault = $valorDefault;

        return $this;
    }

    /**
     * Get valorDefault.
     *
     * @return int|null
     */
    public function getValorDefault()
    {
        return $this->valorDefault;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Unidad
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

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal|null $idSucursal
     *
     * @return Unidad
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal = null)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal|null
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idTipoPrestacionExamen.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrestacionExamen|null $idTipoPrestacionExamen
     *
     * @return Unidad
     */
    public function setIdTipoPrestacionExamen(\Rebsol\HermesBundle\Entity\TipoPrestacionExamen $idTipoPrestacionExamen = null)
    {
        $this->idTipoPrestacionExamen = $idTipoPrestacionExamen;

        return $this;
    }

    /**
     * Get idTipoPrestacionExamen.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPrestacionExamen|null
     */
    public function getIdTipoPrestacionExamen()
    {
        return $this->idTipoPrestacionExamen;
    }
}
