<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoConyugal
 *
 * @ORM\Table(name="estado_conyugal")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\EstadoConyugalRepository")
 */
class EstadoConyugal
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
     * @ORM\Column(name="CODIGO_ESTADO_CONYUGAL", type="integer", nullable=false)
     */
    private $codigoEstadoConyugal;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_ESTADO_CONYUGAL", type="string", length=100, nullable=false)
     */
    private $nombreEstadoConyugal;

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
     * Set codigoEstadoConyugal.
     *
     * @param int $codigoEstadoConyugal
     *
     * @return EstadoConyugal
     */
    public function setCodigoEstadoConyugal($codigoEstadoConyugal)
    {
        $this->codigoEstadoConyugal = $codigoEstadoConyugal;

        return $this;
    }

    /**
     * Get codigoEstadoConyugal.
     *
     * @return int
     */
    public function getCodigoEstadoConyugal()
    {
        return $this->codigoEstadoConyugal;
    }

    /**
     * Set nombreEstadoConyugal.
     *
     * @param string $nombreEstadoConyugal
     *
     * @return EstadoConyugal
     */
    public function setNombreEstadoConyugal($nombreEstadoConyugal)
    {
        $this->nombreEstadoConyugal = $nombreEstadoConyugal;

        return $this;
    }

    /**
     * Get nombreEstadoConyugal.
     *
     * @return string
     */
    public function getNombreEstadoConyugal()
    {
        return $this->nombreEstadoConyugal;
    }

    /**
     * Set valorDefault.
     *
     * @param int $valorDefault
     *
     * @return EstadoConyugal
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
     * @return EstadoConyugal
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
     * @return EstadoConyugal
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
