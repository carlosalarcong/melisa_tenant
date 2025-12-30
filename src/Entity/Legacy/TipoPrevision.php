<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPrevision
 *
 * @ORM\Table(name="tipo_prevision")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoPrevisionRepository")
 */
class TipoPrevision
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
     * @ORM\Column(name="CODIGO_TIPO_PREVISION", type="integer", nullable=true)
     */
    private $codigoTipoPrevision;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_TIPO_PREVISION", type="string", length=100, nullable=true)
     */
    private $nombreTipoPrevision;

    /**
     * @var int|null
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ES_CONVENIO", type="integer", nullable=true)
     */
    private $esConvenio;

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
     * Set codigoTipoPrevision.
     *
     * @param int|null $codigoTipoPrevision
     *
     * @return TipoPrevision
     */
    public function setCodigoTipoPrevision($codigoTipoPrevision = null)
    {
        $this->codigoTipoPrevision = $codigoTipoPrevision;

        return $this;
    }

    /**
     * Get codigoTipoPrevision.
     *
     * @return int|null
     */
    public function getCodigoTipoPrevision()
    {
        return $this->codigoTipoPrevision;
    }

    /**
     * Set nombreTipoPrevision.
     *
     * @param string|null $nombreTipoPrevision
     *
     * @return TipoPrevision
     */
    public function setNombreTipoPrevision($nombreTipoPrevision = null)
    {
        $this->nombreTipoPrevision = $nombreTipoPrevision;

        return $this;
    }

    /**
     * Get nombreTipoPrevision.
     *
     * @return string|null
     */
    public function getNombreTipoPrevision()
    {
        return $this->nombreTipoPrevision;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return TipoPrevision
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
     * Set esConvenio.
     *
     * @param int|null $esConvenio
     *
     * @return TipoPrevision
     */
    public function setEsConvenio($esConvenio = null)
    {
        $this->esConvenio = $esConvenio;

        return $this;
    }

    /**
     * Get esConvenio.
     *
     * @return int|null
     */
    public function getEsConvenio()
    {
        return $this->esConvenio;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return TipoPrevision
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
     * @return TipoPrevision
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
