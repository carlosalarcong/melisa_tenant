<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sucursal
 *
 * @ORM\Table(name="sucursal")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\SucursalRepository")
 */
class Sucursal
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
     * @ORM\Column(name="NOMBRE_SUCURSAL", type="string", length=45, nullable=true)
     */
    private $nombreSucursal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIRECCION_SUCURSAL", type="string", length=255, nullable=true)
     */
    private $direccionSucursal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_FIJO", type="string", length=45, nullable=true)
     */
    private $telefonoFijo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_MOVIL", type="string", length=45, nullable=true)
     */
    private $telefonoMovil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MAIL_SUCURSAL", type="string", length=255, nullable=true)
     */
    private $mailSucursal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ENCARGADO", type="string", length=255, nullable=true)
     */
    private $encargado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_COMERCIO", type="string", length=20, nullable=true)
     */
    private $codigoComercio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_SUCURSAL", type="string", length=20, nullable=true)
     */
    private $codigoSucursal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IMED_CODIGO_LUGAR", type="string", length=20, nullable=true)
     */
    private $imedCodigoLugar;

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
     * @var \Comuna
     *
     * @ORM\ManyToOne(targetEntity="Comuna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_COMUNA", referencedColumnName="ID")
     * })
     */
    private $idComuna;



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
     * Set nombreSucursal.
     *
     * @param string|null $nombreSucursal
     *
     * @return Sucursal
     */
    public function setNombreSucursal($nombreSucursal = null)
    {
        $this->nombreSucursal = $nombreSucursal;

        return $this;
    }

    /**
     * Get nombreSucursal.
     *
     * @return string|null
     */
    public function getNombreSucursal()
    {
        return $this->nombreSucursal;
    }

    /**
     * Set direccionSucursal.
     *
     * @param string|null $direccionSucursal
     *
     * @return Sucursal
     */
    public function setDireccionSucursal($direccionSucursal = null)
    {
        $this->direccionSucursal = $direccionSucursal;

        return $this;
    }

    /**
     * Get direccionSucursal.
     *
     * @return string|null
     */
    public function getDireccionSucursal()
    {
        return $this->direccionSucursal;
    }

    /**
     * Set telefonoFijo.
     *
     * @param string|null $telefonoFijo
     *
     * @return Sucursal
     */
    public function setTelefonoFijo($telefonoFijo = null)
    {
        $this->telefonoFijo = $telefonoFijo;

        return $this;
    }

    /**
     * Get telefonoFijo.
     *
     * @return string|null
     */
    public function getTelefonoFijo()
    {
        return $this->telefonoFijo;
    }

    /**
     * Set telefonoMovil.
     *
     * @param string|null $telefonoMovil
     *
     * @return Sucursal
     */
    public function setTelefonoMovil($telefonoMovil = null)
    {
        $this->telefonoMovil = $telefonoMovil;

        return $this;
    }

    /**
     * Get telefonoMovil.
     *
     * @return string|null
     */
    public function getTelefonoMovil()
    {
        return $this->telefonoMovil;
    }

    /**
     * Set mailSucursal.
     *
     * @param string|null $mailSucursal
     *
     * @return Sucursal
     */
    public function setMailSucursal($mailSucursal = null)
    {
        $this->mailSucursal = $mailSucursal;

        return $this;
    }

    /**
     * Get mailSucursal.
     *
     * @return string|null
     */
    public function getMailSucursal()
    {
        return $this->mailSucursal;
    }

    /**
     * Set encargado.
     *
     * @param string|null $encargado
     *
     * @return Sucursal
     */
    public function setEncargado($encargado = null)
    {
        $this->encargado = $encargado;

        return $this;
    }

    /**
     * Get encargado.
     *
     * @return string|null
     */
    public function getEncargado()
    {
        return $this->encargado;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return Sucursal
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
     * Set codigoComercio.
     *
     * @param string|null $codigoComercio
     *
     * @return Sucursal
     */
    public function setCodigoComercio($codigoComercio = null)
    {
        $this->codigoComercio = $codigoComercio;

        return $this;
    }

    /**
     * Get codigoComercio.
     *
     * @return string|null
     */
    public function getCodigoComercio()
    {
        return $this->codigoComercio;
    }

    /**
     * Set idComuna.
     *
     * @param \Rebsol\HermesBundle\Entity\Comuna|null $idComuna
     *
     * @return Sucursal
     */
    public function setIdComuna(\Rebsol\HermesBundle\Entity\Comuna $idComuna = null)
    {
        $this->idComuna = $idComuna;

        return $this;
    }

    /**
     * Get idComuna.
     *
     * @return \Rebsol\HermesBundle\Entity\Comuna|null
     */
    public function getIdComuna()
    {
        return $this->idComuna;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return Sucursal
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
     * @return string|null
     */
    public function getCodigoSucursal()
    {
        return $this->codigoSucursal;
    }

    /**
     * @param string|null $codigoSucursal
     */
    public function setCodigoSucursal($codigoSucursal)
    {
        $this->codigoSucursal = $codigoSucursal;
    }

    /**
     * @return string|null
     */
    public function getImedCodigoLugar()
    {
        return $this->imedCodigoLugar;
    }

    /**
     * @param string|null $imedCodigoLugar
     */
    public function setImedCodigoLugar($imedCodigoLugar)
    {
        $this->imedCodigoLugar = $imedCodigoLugar;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Sucursal
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
