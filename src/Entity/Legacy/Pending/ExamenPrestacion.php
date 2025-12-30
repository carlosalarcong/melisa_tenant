<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExamenPrestacion
 *
 * @ORM\Table(name="examen_prestacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ExamenPrestacionRepository")
 */
class ExamenPrestacion
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
     * @ORM\Column(name="NOMBRE_EXAMEN", type="string", length=155, nullable=false)
     */
    private $nombreExamen;

    /**
     * @var int
     *
     * @ORM\Column(name="ORDEN", type="integer", nullable=false)
     */
    private $orden;

    /**
     * @var bool
     *
     * @ORM\Column(name="TIENE_TEXTO_EXAMEN", type="boolean", nullable=false)
     */
    private $tieneTextoExamen;

    /**
     * @var \TipoPrestacionExamen
     *
     * @ORM\ManyToOne(targetEntity="TipoPrestacionExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PRESTACION_EXAMENES", referencedColumnName="ID")
     * })
     */
    private $idTipoPrestacionExamenes;

    /**
     * @var \AgrupacionExamen
     *
     * @ORM\ManyToOne(targetEntity="AgrupacionExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CATEGORIA_EXAMEN", referencedColumnName="ID")
     * })
     */
    private $idCategoriaExamen;

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
     * @var \AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idAccionClinica;

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
     * @var \AgrupacionExamen
     *
     * @ORM\ManyToOne(targetEntity="AgrupacionExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_AGRUPACION_EXAMEN", referencedColumnName="ID")
     * })
     */
    private $idAgrupacionExamen;



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
     * Set nombreExamen.
     *
     * @param string $nombreExamen
     *
     * @return ExamenPrestacion
     */
    public function setNombreExamen($nombreExamen)
    {
        $this->nombreExamen = $nombreExamen;

        return $this;
    }

    /**
     * Get nombreExamen.
     *
     * @return string
     */
    public function getNombreExamen()
    {
        return $this->nombreExamen;
    }

    /**
     * Set orden.
     *
     * @param int $orden
     *
     * @return ExamenPrestacion
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set tieneTextoExamen.
     *
     * @param bool $tieneTextoExamen
     *
     * @return ExamenPrestacion
     */
    public function setTieneTextoExamen($tieneTextoExamen)
    {
        $this->tieneTextoExamen = $tieneTextoExamen;

        return $this;
    }

    /**
     * Get tieneTextoExamen.
     *
     * @return bool
     */
    public function getTieneTextoExamen()
    {
        return $this->tieneTextoExamen;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica
     *
     * @return ExamenPrestacion
     */
    public function setIdAccionClinica(\Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \Rebsol\HermesBundle\Entity\AccionClinica
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * Set idAgrupacionExamen.
     *
     * @param \Rebsol\HermesBundle\Entity\AgrupacionExamen $idAgrupacionExamen
     *
     * @return ExamenPrestacion
     */
    public function setIdAgrupacionExamen(\Rebsol\HermesBundle\Entity\AgrupacionExamen $idAgrupacionExamen)
    {
        $this->idAgrupacionExamen = $idAgrupacionExamen;

        return $this;
    }

    /**
     * Get idAgrupacionExamen.
     *
     * @return \Rebsol\HermesBundle\Entity\AgrupacionExamen
     */
    public function getIdAgrupacionExamen()
    {
        return $this->idAgrupacionExamen;
    }

    /**
     * Set idCategoriaExamen.
     *
     * @param \Rebsol\HermesBundle\Entity\AgrupacionExamen $idCategoriaExamen
     *
     * @return ExamenPrestacion
     */
    public function setIdCategoriaExamen(\Rebsol\HermesBundle\Entity\AgrupacionExamen $idCategoriaExamen)
    {
        $this->idCategoriaExamen = $idCategoriaExamen;

        return $this;
    }

    /**
     * Get idCategoriaExamen.
     *
     * @return \Rebsol\HermesBundle\Entity\AgrupacionExamen
     */
    public function getIdCategoriaExamen()
    {
        return $this->idCategoriaExamen;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return ExamenPrestacion
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
     * @return ExamenPrestacion
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
     * Set idTipoPrestacionExamenes.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrestacionExamen $idTipoPrestacionExamenes
     *
     * @return ExamenPrestacion
     */
    public function setIdTipoPrestacionExamenes(\Rebsol\HermesBundle\Entity\TipoPrestacionExamen $idTipoPrestacionExamenes)
    {
        $this->idTipoPrestacionExamenes = $idTipoPrestacionExamenes;

        return $this;
    }

    /**
     * Get idTipoPrestacionExamenes.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPrestacionExamen
     */
    public function getIdTipoPrestacionExamenes()
    {
        return $this->idTipoPrestacionExamenes;
    }
}
