<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdministradorSeguro
 *
 * @ORM\Table(name="administrador_seguro")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\AdministradorSeguroRepository")
 */
class AdministradorSeguro
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
     * @ORM\Column(name="CODIGO_ADMINISTRADOR_SEGURO", type="integer", nullable=true)
     */
    private $codigoAdministradorSeguro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ADMINISTRADOR_SEGURO", type="string", length=100, nullable=true)
     */
    private $nombreAdministradorSeguro;

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
     * Set codigoAdministradorSeguro.
     *
     * @param int|null $codigoAdministradorSeguro
     *
     * @return AdministradorSeguro
     */
    public function setCodigoAdministradorSeguro($codigoAdministradorSeguro = null)
    {
        $this->codigoAdministradorSeguro = $codigoAdministradorSeguro;

        return $this;
    }

    /**
     * Get codigoAdministradorSeguro.
     *
     * @return int|null
     */
    public function getCodigoAdministradorSeguro()
    {
        return $this->codigoAdministradorSeguro;
    }

    /**
     * Set nombreAdministradorSeguro.
     *
     * @param string|null $nombreAdministradorSeguro
     *
     * @return AdministradorSeguro
     */
    public function setNombreAdministradorSeguro($nombreAdministradorSeguro = null)
    {
        $this->nombreAdministradorSeguro = $nombreAdministradorSeguro;

        return $this;
    }

    /**
     * Get nombreAdministradorSeguro.
     *
     * @return string|null
     */
    public function getNombreAdministradorSeguro()
    {
        return $this->nombreAdministradorSeguro;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return AdministradorSeguro
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
     * @return AdministradorSeguro
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
     * @return AdministradorSeguro
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
