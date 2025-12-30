<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Empresa
 *
 * @ORM\Table(name="empresa")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\EmpresaRepository")
 */
class Empresa
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
     * @ORM\Column(name="RUT_EMPRESA", type="integer", nullable=false)
     */
    private $rutEmpresa;

    /**
     * @var string
     *
     * @ORM\Column(name="DIGITO_VERIFICADOR", type="string", length=1, nullable=false)
     */
    private $digitoVerificador;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_EMPRESA", type="string", length=255, nullable=true)
     */
    private $nombreEmpresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIRECCION", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SITIO_WEB", type="string", length=255, nullable=true)
     */
    private $sitioWeb;

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
     * @ORM\Column(name="PATH_LOGO_EMPRESA", type="string", length=255, nullable=true)
     */
    private $pathLogoEmpresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MAIL", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ENCARGADO", type="string", length=255, nullable=true)
     */
    private $encargado;

    /**
     * @var int
     *
     * @ORM\Column(name="CANTIDAD_LICENCIAS", type="integer", nullable=false)
     */
    private $cantidadLicencias;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO", type="string", length=50, nullable=true)
     */
    private $codigo;

    /**
     * @var bool
     *
     * @ORM\Column(name="INTEGRACION_AWS", type="boolean", nullable=false, options={"default"="1"})
     */
    private $integracionAws = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="TELECONSULTA", type="boolean", nullable=false)
     */
    private $teleconsulta = '0';

    /**
     * @var \TipoHospital
     *
     * @ORM\ManyToOne(targetEntity="TipoHospital")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_HOSPITAL", referencedColumnName="ID")
     * })
     */
    private $idTipoHospital;

    /**
     * @var \ServicioSalud
     *
     * @ORM\ManyToOne(targetEntity="ServicioSalud")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO_SALUD", referencedColumnName="ID")
     * })
     */
    private $idServicioSalud;

    /**
     * @var \NivelFonasa
     *
     * @ORM\ManyToOne(targetEntity="NivelFonasa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_NIVEL_FONASA", referencedColumnName="ID")
     * })
     */
    private $idNivelFonasa;

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
     * @var \Arancel
     *
     * @ORM\ManyToOne(targetEntity="Arancel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ARANCEL", referencedColumnName="ID")
     * })
     */
    private $idArancel;

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
     * @var \TipoIdentificacionExtranjero
     *
     * @ORM\ManyToOne(targetEntity="TipoIdentificacionExtranjero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_IDENTIFICACION_DEFAULT", referencedColumnName="ID")
     * })
     */
    private $idTipoIdentificacionDefault;



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
     * @param string|null $codigo
     *
     * @return Empresa
     */
    public function setCodigo($codigo = null)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string|null
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set rutEmpresa.
     *
     * @param int $rutEmpresa
     *
     * @return Empresa
     */
    public function setRutEmpresa($rutEmpresa)
    {
        $this->rutEmpresa = $rutEmpresa;

        return $this;
    }

    /**
     * Get rutEmpresa.
     *
     * @return int
     */
    public function getRutEmpresa()
    {
        return $this->rutEmpresa;
    }

    /**
     * Set digitoVerificador.
     *
     * @param string $digitoVerificador
     *
     * @return Empresa
     */
    public function setDigitoVerificador($digitoVerificador)
    {
        $this->digitoVerificador = $digitoVerificador;

        return $this;
    }

    /**
     * Get digitoVerificador.
     *
     * @return string
     */
    public function getDigitoVerificador()
    {
        return $this->digitoVerificador;
    }

    /**
     * Set nombreEmpresa.
     *
     * @param string|null $nombreEmpresa
     *
     * @return Empresa
     */
    public function setNombreEmpresa($nombreEmpresa = null)
    {
        $this->nombreEmpresa = $nombreEmpresa;

        return $this;
    }

    /**
     * Get nombreEmpresa.
     *
     * @return string|null
     */
    public function getNombreEmpresa()
    {
        return $this->nombreEmpresa;
    }

    /**
     * Set direccion.
     *
     * @param string|null $direccion
     *
     * @return Empresa
     */
    public function setDireccion($direccion = null)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string|null
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set sitioWeb.
     *
     * @param string|null $sitioWeb
     *
     * @return Empresa
     */
    public function setSitioWeb($sitioWeb = null)
    {
        $this->sitioWeb = $sitioWeb;

        return $this;
    }

    /**
     * Get sitioWeb.
     *
     * @return string|null
     */
    public function getSitioWeb()
    {
        return $this->sitioWeb;
    }

    /**
     * Set telefonoFijo.
     *
     * @param string|null $telefonoFijo
     *
     * @return Empresa
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
     * @return Empresa
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
     * Set pathLogoEmpresa.
     *
     * @param string|null $pathLogoEmpresa
     *
     * @return Empresa
     */
    public function setPathLogoEmpresa($pathLogoEmpresa = null)
    {
        $this->pathLogoEmpresa = $pathLogoEmpresa;

        return $this;
    }

    /**
     * Get pathLogoEmpresa.
     *
     * @return string|null
     */
    public function getPathLogoEmpresa()
    {
        return $this->pathLogoEmpresa;
    }

    /**
     * Set mail.
     *
     * @param string|null $mail
     *
     * @return Empresa
     */
    public function setMail($mail = null)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string|null
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set encargado.
     *
     * @param string|null $encargado
     *
     * @return Empresa
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
     * Set cantidadLicencias.
     *
     * @param int $cantidadLicencias
     *
     * @return Empresa
     */
    public function setCantidadLicencias($cantidadLicencias)
    {
        $this->cantidadLicencias = $cantidadLicencias;

        return $this;
    }

    /**
     * Get cantidadLicencias.
     *
     * @return int
     */
    public function getCantidadLicencias()
    {
        return $this->cantidadLicencias;
    }

    /**
     * Set integracionAws.
     *
     * @param bool $integracionAws
     *
     * @return Empresa
     */
    public function setIntegracionAws($integracionAws)
    {
        $this->integracionAws = $integracionAws;

        return $this;
    }

    /**
     * Get integracionAws.
     *
     * @return bool
     */
    public function getIntegracionAws()
    {
        return $this->integracionAws;
    }

    /**
     * Set teleconsulta.
     *
     * @param bool $teleconsulta
     *
     * @return Empresa
     */
    public function setTeleconsulta($teleconsulta)
    {
        $this->teleconsulta = $teleconsulta;

        return $this;
    }

    /**
     * Get teleconsulta.
     *
     * @return bool
     */
    public function getTeleconsulta()
    {
        return $this->teleconsulta;
    }

    /**
     * Set idArancel.
     *
     * @param \Rebsol\HermesBundle\Entity\Arancel|null $idArancel
     *
     * @return Empresa
     */
    public function setIdArancel(\Rebsol\HermesBundle\Entity\Arancel $idArancel = null)
    {
        $this->idArancel = $idArancel;

        return $this;
    }

    /**
     * Get idArancel.
     *
     * @return \Rebsol\HermesBundle\Entity\Arancel|null
     */
    public function getIdArancel()
    {
        return $this->idArancel;
    }

    /**
     * Set idComuna.
     *
     * @param \Rebsol\HermesBundle\Entity\Comuna|null $idComuna
     *
     * @return Empresa
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Empresa
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
     * Set idNivelFonasa.
     *
     * @param \Rebsol\HermesBundle\Entity\NivelFonasa|null $idNivelFonasa
     *
     * @return Empresa
     */
    public function setIdNivelFonasa(\Rebsol\HermesBundle\Entity\NivelFonasa $idNivelFonasa = null)
    {
        $this->idNivelFonasa = $idNivelFonasa;

        return $this;
    }

    /**
     * Get idNivelFonasa.
     *
     * @return \Rebsol\HermesBundle\Entity\NivelFonasa|null
     */
    public function getIdNivelFonasa()
    {
        return $this->idNivelFonasa;
    }

    /**
     * Set idServicioSalud.
     *
     * @param \Rebsol\HermesBundle\Entity\ServicioSalud|null $idServicioSalud
     *
     * @return Empresa
     */
    public function setIdServicioSalud(\Rebsol\HermesBundle\Entity\ServicioSalud $idServicioSalud = null)
    {
        $this->idServicioSalud = $idServicioSalud;

        return $this;
    }

    /**
     * Get idServicioSalud.
     *
     * @return \Rebsol\HermesBundle\Entity\ServicioSalud|null
     */
    public function getIdServicioSalud()
    {
        return $this->idServicioSalud;
    }

    /**
     * Set idTipoHospital.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoHospital|null $idTipoHospital
     *
     * @return Empresa
     */
    public function setIdTipoHospital(\Rebsol\HermesBundle\Entity\TipoHospital $idTipoHospital = null)
    {
        $this->idTipoHospital = $idTipoHospital;

        return $this;
    }

    /**
     * Get idTipoHospital.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoHospital|null
     */
    public function getIdTipoHospital()
    {
        return $this->idTipoHospital;
    }

    /**
     * Set idTipoIdentificacionDefault.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero $idTipoIdentificacionDefault
     *
     * @return Empresa
     */
    public function setIdTipoIdentificacionDefault(\Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero $idTipoIdentificacionDefault)
    {
        $this->idTipoIdentificacionDefault = $idTipoIdentificacionDefault;

        return $this;
    }

    /**
     * Get idTipoIdentificacionDefault.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero
     */
    public function getIdTipoIdentificacionDefault()
    {
        return $this->idTipoIdentificacionDefault;
    }
}
