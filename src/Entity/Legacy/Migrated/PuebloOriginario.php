<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PuebloOriginario
 *
 * @ORM\Table(name="pueblo_originario")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PuebloOriginarioRepository")
 */
class PuebloOriginario
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
     * @ORM\Column(name="CODIGO_PUEBLO_ORIGINARIO", type="integer", nullable=true)
     */
    private $codigoPuebloOriginario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_PUEBLO_ORIGINARIO", type="string", length=100, nullable=true)
     */
    private $nombrePuebloOriginario;

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
     * Set codigoPuebloOriginario.
     *
     * @param int|null $codigoPuebloOriginario
     *
     * @return PuebloOriginario
     */
    public function setCodigoPuebloOriginario($codigoPuebloOriginario = null)
    {
        $this->codigoPuebloOriginario = $codigoPuebloOriginario;

        return $this;
    }

    /**
     * Get codigoPuebloOriginario.
     *
     * @return int|null
     */
    public function getCodigoPuebloOriginario()
    {
        return $this->codigoPuebloOriginario;
    }

    /**
     * Set nombrePuebloOriginario.
     *
     * @param string|null $nombrePuebloOriginario
     *
     * @return PuebloOriginario
     */
    public function setNombrePuebloOriginario($nombrePuebloOriginario = null)
    {
        $this->nombrePuebloOriginario = $nombrePuebloOriginario;

        return $this;
    }

    /**
     * Get nombrePuebloOriginario.
     *
     * @return string|null
     */
    public function getNombrePuebloOriginario()
    {
        return $this->nombrePuebloOriginario;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return PuebloOriginario
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
     * @return PuebloOriginario
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
     * @return PuebloOriginario
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
