<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Patologia
 *
 * @ORM\Table(name="patologia")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PatologiaRepository")
 */
class Patologia
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
     * @ORM\Column(name="NUMERO_PATOLOGIA", type="integer", nullable=true)
     */
    private $numeroPatologia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="EDAD_MAYOR_QUE", type="integer", nullable=true)
     */
    private $edadMayorQue;

    /**
     * @var int|null
     *
     * @ORM\Column(name="EDAD_MENOR_QUE", type="integer", nullable=true)
     */
    private $edadMenorQue;

    /**
     * @var int|null
     *
     * @ORM\Column(name="EDAD_RANGO_INICIO", type="integer", nullable=true)
     */
    private $edadRangoInicio;

    /**
     * @var int|null
     *
     * @ORM\Column(name="EDAD_RANGO_FIN", type="integer", nullable=true)
     */
    private $edadRangoFin;

    /**
     * @var \Sexo
     *
     * @ORM\ManyToOne(targetEntity="Sexo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SEXO", referencedColumnName="ID")
     * })
     */
    private $idSexo;

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
     * @var \TipoPatologia
     *
     * @ORM\ManyToOne(targetEntity="TipoPatologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PATOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idTipoPatologia;



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
     * Set numeroPatologia.
     *
     * @param int|null $numeroPatologia
     *
     * @return Patologia
     */
    public function setNumeroPatologia($numeroPatologia = null)
    {
        $this->numeroPatologia = $numeroPatologia;

        return $this;
    }

    /**
     * Get numeroPatologia.
     *
     * @return int|null
     */
    public function getNumeroPatologia()
    {
        return $this->numeroPatologia;
    }

    /**
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return Patologia
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set edadMayorQue.
     *
     * @param int|null $edadMayorQue
     *
     * @return Patologia
     */
    public function setEdadMayorQue($edadMayorQue = null)
    {
        $this->edadMayorQue = $edadMayorQue;

        return $this;
    }

    /**
     * Get edadMayorQue.
     *
     * @return int|null
     */
    public function getEdadMayorQue()
    {
        return $this->edadMayorQue;
    }

    /**
     * Set edadMenorQue.
     *
     * @param int|null $edadMenorQue
     *
     * @return Patologia
     */
    public function setEdadMenorQue($edadMenorQue = null)
    {
        $this->edadMenorQue = $edadMenorQue;

        return $this;
    }

    /**
     * Get edadMenorQue.
     *
     * @return int|null
     */
    public function getEdadMenorQue()
    {
        return $this->edadMenorQue;
    }

    /**
     * Set edadRangoInicio.
     *
     * @param int|null $edadRangoInicio
     *
     * @return Patologia
     */
    public function setEdadRangoInicio($edadRangoInicio = null)
    {
        $this->edadRangoInicio = $edadRangoInicio;

        return $this;
    }

    /**
     * Get edadRangoInicio.
     *
     * @return int|null
     */
    public function getEdadRangoInicio()
    {
        return $this->edadRangoInicio;
    }

    /**
     * Set edadRangoFin.
     *
     * @param int|null $edadRangoFin
     *
     * @return Patologia
     */
    public function setEdadRangoFin($edadRangoFin = null)
    {
        $this->edadRangoFin = $edadRangoFin;

        return $this;
    }

    /**
     * Get edadRangoFin.
     *
     * @return int|null
     */
    public function getEdadRangoFin()
    {
        return $this->edadRangoFin;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Patologia
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
     * Set idSexo.
     *
     * @param \Rebsol\HermesBundle\Entity\Sexo|null $idSexo
     *
     * @return Patologia
     */
    public function setIdSexo(\Rebsol\HermesBundle\Entity\Sexo $idSexo = null)
    {
        $this->idSexo = $idSexo;

        return $this;
    }

    /**
     * Get idSexo.
     *
     * @return \Rebsol\HermesBundle\Entity\Sexo|null
     */
    public function getIdSexo()
    {
        return $this->idSexo;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return Patologia
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
     * @return \TipoPatologia
     */
    public function getIdTipoPatologia()
    {
        return $this->idTipoPatologia;
    }

    /**
     * @param \TipoPatologia $idTipoPatologia
     */
    public function setIdTipoPatologia($idTipoPatologia)
    {
        $this->idTipoPatologia = $idTipoPatologia;
    }

}
