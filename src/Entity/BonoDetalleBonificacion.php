<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BonoDetalleBonificacion
 *
 * @ORM\Table(name="bono_detalle_bonificacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\BonoDetalleBonificacionRepository")
 */
class BonoDetalleBonificacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CODIGO_BONO_ADICIONAL", type="integer", nullable=true)
     */
    private $codigoBonoAdicional;

    /**
     * @var string
     *
     * @ORM\Column(name="GLOSA_BONO_ADICIONAL", type="string", length=50, nullable=true)
     */
    private $glosaBonoAdicional;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_BONO_ADICIONAL", type="integer", nullable=true)
     */
    private $montoBonoAdicional;

    /**
     * @var \BonoDetalle
     *
     * @ORM\ManyToOne(targetEntity="BonoDetalle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BONO_DETALLE", referencedColumnName="ID")
     * })
     */
    private $idBonoDetalle;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getCodigoBonoAdicional()
    {
        return $this->codigoBonoAdicional;
    }

    /**
     * @param int|null $codigoBonoAdicional
     */
    public function setCodigoBonoAdicional($codigoBonoAdicional)
    {
        $this->codigoBonoAdicional = $codigoBonoAdicional;
    }

    /**
     * @return string
     */
    public function getGlosaBonoAdicional()
    {
        return $this->glosaBonoAdicional;
    }

    /**
     * @param string $glosaBonoAdicional
     */
    public function setGlosaBonoAdicional($glosaBonoAdicional)
    {
        $this->glosaBonoAdicional = $glosaBonoAdicional;
    }

    /**
     * @return int|null
     */
    public function getMontoBonoAdicional()
    {
        return $this->montoBonoAdicional;
    }

    /**
     * @param int|null $montoBonoAdicional
     */
    public function setMontoBonoAdicional($montoBonoAdicional)
    {
        $this->montoBonoAdicional = $montoBonoAdicional;
    }

    /**
     * @return \BonoDetalle
     */
    public function getIdBonoDetalle()
    {
        return $this->idBonoDetalle;
    }

    /**
     * @param \BonoDetalle $idBonoDetalle
     */
    public function setIdBonoDetalle($idBonoDetalle)
    {
        $this->idBonoDetalle = $idBonoDetalle;
    }

}
