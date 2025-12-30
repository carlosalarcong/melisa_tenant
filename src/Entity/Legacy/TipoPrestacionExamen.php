<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPrestacionExamen
 *
 * @ORM\Table(name="tipo_prestacion_examen")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoPrestacionExamenRepository")
 */
class TipoPrestacionExamen
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
     * @ORM\Column(name="NOMBRE_TIPO_PRESTACION_EXAMENES", type="string", length=255, nullable=true)
     */
    private $nombreTipoPrestacionExamenes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RUTA", type="string", length=255, nullable=true)
     */
    private $ruta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIPO_TIPO_PRESTACION", type="string", length=50, nullable=true)
     */
    private $tipoTipoPrestacion;

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
     * Set nombreTipoPrestacionExamenes.
     *
     * @param string|null $nombreTipoPrestacionExamenes
     *
     * @return TipoPrestacionExamen
     */
    public function setNombreTipoPrestacionExamenes($nombreTipoPrestacionExamenes = null)
    {
        $this->nombreTipoPrestacionExamenes = $nombreTipoPrestacionExamenes;

        return $this;
    }

    /**
     * Get nombreTipoPrestacionExamenes.
     *
     * @return string|null
     */
    public function getNombreTipoPrestacionExamenes()
    {
        return $this->nombreTipoPrestacionExamenes;
    }

    /**
     * Set ruta.
     *
     * @param string|null $ruta
     *
     * @return TipoPrestacionExamen
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
     * Set tipoTipoPrestacion.
     *
     * @param string|null $tipoTipoPrestacion
     *
     * @return TipoPrestacionExamen
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
     * @return TipoPrestacionExamen
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
     * @return TipoPrestacionExamen
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
