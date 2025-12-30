<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ocupacion
 *
 * @ORM\Table(name="ocupacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\OcupacionRepository")
 */
class Ocupacion
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
     * @ORM\Column(name="CODIGO_OCUPACION", type="integer", nullable=false)
     */
    private $codigoOcupacion;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_OCUPACION", type="string", length=100, nullable=false)
     */
    private $nombreOcupacion;

    /**
     * @var int
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=false)
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
     * Set codigoOcupacion.
     *
     * @param int $codigoOcupacion
     *
     * @return Ocupacion
     */
    public function setCodigoOcupacion($codigoOcupacion)
    {
        $this->codigoOcupacion = $codigoOcupacion;

        return $this;
    }

    /**
     * Get codigoOcupacion.
     *
     * @return int
     */
    public function getCodigoOcupacion()
    {
        return $this->codigoOcupacion;
    }

    /**
     * Set nombreOcupacion.
     *
     * @param string $nombreOcupacion
     *
     * @return Ocupacion
     */
    public function setNombreOcupacion($nombreOcupacion)
    {
        $this->nombreOcupacion = $nombreOcupacion;

        return $this;
    }

    /**
     * Get nombreOcupacion.
     *
     * @return string
     */
    public function getNombreOcupacion()
    {
        return $this->nombreOcupacion;
    }

    /**
     * Set valorDefault.
     *
     * @param int $valorDefault
     *
     * @return Ocupacion
     */
    public function setValorDefault($valorDefault)
    {
        $this->valorDefault = $valorDefault;

        return $this;
    }

    /**
     * Get valorDefault.
     *
     * @return int
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
     * @return Ocupacion
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
     * @return Ocupacion
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
