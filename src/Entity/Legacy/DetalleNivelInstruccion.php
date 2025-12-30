<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleNivelInstruccion
 *
 * @ORM\Table(name="detalle_nivel_instruccion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\DetalleNivelInstruccionRepository")
 */
class DetalleNivelInstruccion
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
     * @var int
     *
     * @ORM\Column(name="CODIGO_DETALLE_NIVEL_INSTRUCCION", type="integer", nullable=false)
     */
    private $codigoDetalleNivelInstruccion;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_DETALLE_NIVEL_INSTRUCCION", type="string", length=100, nullable=false)
     */
    private $nombreDetalleNivelInstruccion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

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
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;

    /**
     * @var \NivelInstruccion
     *
     * @ORM\ManyToOne(targetEntity="NivelInstruccion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_NIVEL_INSTRUCCION", referencedColumnName="ID")
     * })
     */
    private $idNivelInstruccion;



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
     * Set codigoDetalleNivelInstruccion.
     *
     * @param int $codigoDetalleNivelInstruccion
     *
     * @return DetalleNivelInstruccion
     */
    public function setCodigoDetalleNivelInstruccion($codigoDetalleNivelInstruccion)
    {
        $this->codigoDetalleNivelInstruccion = $codigoDetalleNivelInstruccion;

        return $this;
    }

    /**
     * Get codigoDetalleNivelInstruccion.
     *
     * @return int
     */
    public function getCodigoDetalleNivelInstruccion()
    {
        return $this->codigoDetalleNivelInstruccion;
    }

    /**
     * Set nombreDetalleNivelInstruccion.
     *
     * @param string $nombreDetalleNivelInstruccion
     *
     * @return DetalleNivelInstruccion
     */
    public function setNombreDetalleNivelInstruccion($nombreDetalleNivelInstruccion)
    {
        $this->nombreDetalleNivelInstruccion = $nombreDetalleNivelInstruccion;

        return $this;
    }

    /**
     * Get nombreDetalleNivelInstruccion.
     *
     * @return string
     */
    public function getNombreDetalleNivelInstruccion()
    {
        return $this->nombreDetalleNivelInstruccion;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return DetalleNivelInstruccion
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
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return DetalleNivelInstruccion
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return DetalleNivelInstruccion
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
     * Set idNivelInstruccion.
     *
     * @param \Rebsol\HermesBundle\Entity\NivelInstruccion $idNivelInstruccion
     *
     * @return DetalleNivelInstruccion
     */
    public function setIdNivelInstruccion(\Rebsol\HermesBundle\Entity\NivelInstruccion $idNivelInstruccion)
    {
        $this->idNivelInstruccion = $idNivelInstruccion;

        return $this;
    }

    /**
     * Get idNivelInstruccion.
     *
     * @return \Rebsol\HermesBundle\Entity\NivelInstruccion
     */
    public function getIdNivelInstruccion()
    {
        return $this->idNivelInstruccion;
    }
}
