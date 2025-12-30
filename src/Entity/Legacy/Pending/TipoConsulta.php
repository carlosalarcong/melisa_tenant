<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoConsulta
 *
 * @ORM\Table(name="tipo_consulta")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoConsultaRepository")
 */
class TipoConsulta
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
     * @ORM\Column(name="CODIGO", type="integer", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_TIPO_CONSULTA", type="string", length=255, nullable=false)
     */
    private $nombreTipoConsulta;

    /**
     * @var string
     *
     * @ORM\Column(name="COLOR_DISPONIBLE", type="string", length=45, nullable=false)
     */
    private $colorDisponible;

    /**
     * @var string
     *
     * @ORM\Column(name="COLOR_RESERVADO", type="string", length=45, nullable=false)
     */
    private $colorReservado;

    /**
     * @var string
     *
     * @ORM\Column(name="COLOR_SOBRECUPO", type="string", length=45, nullable=false)
     */
    private $colorSobrecupo;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_TRATAMIENTO", type="boolean", nullable=false)
     */
    private $esTratamiento;

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
     * Set codigo.
     *
     * @param int $codigo
     *
     * @return TipoConsulta
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombreTipoConsulta.
     *
     * @param string $nombreTipoConsulta
     *
     * @return TipoConsulta
     */
    public function setNombreTipoConsulta($nombreTipoConsulta)
    {
        $this->nombreTipoConsulta = $nombreTipoConsulta;

        return $this;
    }

    /**
     * Get nombreTipoConsulta.
     *
     * @return string
     */
    public function getNombreTipoConsulta()
    {
        return $this->nombreTipoConsulta;
    }

    /**
     * Set colorDisponible.
     *
     * @param string $colorDisponible
     *
     * @return TipoConsulta
     */
    public function setColorDisponible($colorDisponible)
    {
        $this->colorDisponible = $colorDisponible;

        return $this;
    }

    /**
     * Get colorDisponible.
     *
     * @return string
     */
    public function getColorDisponible()
    {
        return $this->colorDisponible;
    }

    /**
     * Set colorReservado.
     *
     * @param string $colorReservado
     *
     * @return TipoConsulta
     */
    public function setColorReservado($colorReservado)
    {
        $this->colorReservado = $colorReservado;

        return $this;
    }

    /**
     * Get colorReservado.
     *
     * @return string
     */
    public function getColorReservado()
    {
        return $this->colorReservado;
    }

    /**
     * Set colorSobrecupo.
     *
     * @param string $colorSobrecupo
     *
     * @return TipoConsulta
     */
    public function setColorSobrecupo($colorSobrecupo)
    {
        $this->colorSobrecupo = $colorSobrecupo;

        return $this;
    }

    /**
     * Get colorSobrecupo.
     *
     * @return string
     */
    public function getColorSobrecupo()
    {
        return $this->colorSobrecupo;
    }

    /**
     * Set esTratamiento.
     *
     * @param bool $esTratamiento
     *
     * @return TipoConsulta
     */
    public function setEsTratamiento($esTratamiento)
    {
        $this->esTratamiento = $esTratamiento;

        return $this;
    }

    /**
     * Get esTratamiento.
     *
     * @return bool
     */
    public function getEsTratamiento()
    {
        return $this->esTratamiento;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return TipoConsulta
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
     * @return TipoConsulta
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
}
