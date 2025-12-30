<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPrestacion
 *
 * @ORM\Table(name="tipo_prestacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoPrestacionRepository")
 */
class TipoPrestacion
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
     * @ORM\Column(name="NOMBRE_TIPO_PRESTACION", type="string", length=255, nullable=true)
     */
    private $nombreTipoPrestacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIPO_TIPO_PRESTACION", type="string", length=50, nullable=true)
     */
    private $tipoTipoPrestacion;

    /**
     * @var \CategoriaPrestacion
     *
     * @ORM\ManyToOne(targetEntity="CategoriaPrestacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CATEGORIA_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idCategoriaPrestacion;

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
     * Set nombreTipoPrestacion.
     *
     * @param string|null $nombreTipoPrestacion
     *
     * @return TipoPrestacion
     */
    public function setNombreTipoPrestacion($nombreTipoPrestacion = null)
    {
        $this->nombreTipoPrestacion = $nombreTipoPrestacion;

        return $this;
    }

    /**
     * Get nombreTipoPrestacion.
     *
     * @return string|null
     */
    public function getNombreTipoPrestacion()
    {
        return $this->nombreTipoPrestacion;
    }

    /**
     * Set tipoTipoPrestacion.
     *
     * @param string|null $tipoTipoPrestacion
     *
     * @return TipoPrestacion
     */
    public function setTipoTipoPrestacion($tipoTipoPrestacion = null)
    {
        $this->tipoTipoPrestacion = $tipoTipoPrestacion;

        return $this;
    }

    /**
     * Get tipoTipoPrestacion.
     *
     * @return string|null
     */
    public function getTipoTipoPrestacion()
    {
        return $this->tipoTipoPrestacion;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return TipoPrestacion
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
     * @return TipoPrestacion
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
     * Set idCategoriaPrestacion.
     *
     * @param \Rebsol\HermesBundle\Entity\CategoriaPrestacion|null $idCategoriaPrestacion
     *
     * @return TipoPrestacion
     */
    public function setIdCategoriaPrestacion(\Rebsol\HermesBundle\Entity\CategoriaPrestacion $idCategoriaPrestacion = null)
    {
        $this->idCategoriaPrestacion = $idCategoriaPrestacion;

        return $this;
    }

    /**
     * Get idCategoriaPrestacion.
     *
     * @return \Rebsol\HermesBundle\Entity\CategoriaPrestacion|null
     */
    public function getIdCategoriaPrestacion()
    {
        return $this->idCategoriaPrestacion;
    }
}
