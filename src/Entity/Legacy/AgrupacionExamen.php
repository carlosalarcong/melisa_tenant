<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgrupacionExamen
 *
 * @ORM\Table(name="agrupacion_examen")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\AgrupacionExamenRepository")
 */
class AgrupacionExamen
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
     * @ORM\Column(name="NOMBRE_AGRUPACION", type="string", length=255, nullable=true)
     */
    private $nombreAgrupacion;

    /**
     * @var \TipoPrestacionExamen
     *
     * @ORM\ManyToOne(targetEntity="TipoPrestacionExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PRESTACION_EXAMEN", referencedColumnName="ID")
     * })
     */
    private $idTipoPrestacionExamen;

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
     * Set nombreAgrupacion.
     *
     * @param string|null $nombreAgrupacion
     *
     * @return AgrupacionExamen
     */
    public function setNombreAgrupacion($nombreAgrupacion = null)
    {
        $this->nombreAgrupacion = $nombreAgrupacion;

        return $this;
    }

    /**
     * Get nombreAgrupacion.
     *
     * @return string|null
     */
    public function getNombreAgrupacion()
    {
        return $this->nombreAgrupacion;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return AgrupacionExamen
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
     * Set idTipoPrestacionExamen.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrestacionExamen|null $idTipoPrestacionExamen
     *
     * @return AgrupacionExamen
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

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return AgrupacionExamen
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
