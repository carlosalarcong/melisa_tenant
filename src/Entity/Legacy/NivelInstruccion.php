<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * NivelInstruccion
 *
 * @ORM\Table(name="nivel_instruccion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\NivelInstruccionRepository")
 */
class NivelInstruccion
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
     * @var int|null
     *
     * @ORM\Column(name="CODIGO_NIVEL_INSTRUCCION", type="integer", nullable=true)
     */
    private $codigoNivelInstruccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_NIVEL_INSTRUCCION", type="string", length=100, nullable=true)
     */
    private $nombreNivelInstruccion;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigoNivelInstruccion.
     *
     * @param int|null $codigoNivelInstruccion
     *
     * @return NivelInstruccion
     */
    public function setCodigoNivelInstruccion($codigoNivelInstruccion = null)
    {
        $this->codigoNivelInstruccion = $codigoNivelInstruccion;

        return $this;
    }

    /**
     * Get codigoNivelInstruccion.
     *
     * @return int|null
     */
    public function getCodigoNivelInstruccion()
    {
        return $this->codigoNivelInstruccion;
    }

    /**
     * Set nombreNivelInstruccion.
     *
     * @param string|null $nombreNivelInstruccion
     *
     * @return NivelInstruccion
     */
    public function setNombreNivelInstruccion($nombreNivelInstruccion = null)
    {
        $this->nombreNivelInstruccion = $nombreNivelInstruccion;

        return $this;
    }

    /**
     * Get nombreNivelInstruccion.
     *
     * @return string|null
     */
    public function getNombreNivelInstruccion()
    {
        return $this->nombreNivelInstruccion;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return NivelInstruccion
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
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return NivelInstruccion
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa = null)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa|null
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return NivelInstruccion
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
