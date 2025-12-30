<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccionClinica
 *
 * @ORM\Table(name="accion_clinica")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\AccionClinicaRepository")
 */
class AccionClinica
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
     * @ORM\Column(name="CODIGO_ACCION_CLINICA", type="string", length=20, nullable=false)
     */
    private $codigoAccionClinica;

    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO_FONASA", type="string", length=20, nullable=false)
     */
    private $codigoFonasa;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_ACCION_CLINICA", type="text", length=0, nullable=false)
     */
    private $nombreAccionClinica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ABREVIADO", type="string", length=45, nullable=true)
     */
    private $nombreAbreviado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CODIGO_CUENTA_CONTABLE_ERP", type="integer", nullable=true)
     */
    private $codigoCuentaContableErp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DURACION", type="time", nullable=true)
     */
    private $duracion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRIPCION", type="text", length=0, nullable=true)
     */
    private $descripcion;

    /**
     * @var bool
     *
     * @ORM\Column(name="APLICA_RECARGO", type="boolean", nullable=false)
     */
    private $aplicaRecargo = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_IMED", type="boolean", nullable=false)
     */
    private $esImed = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_IMED", type="string", length=20, nullable=true)
     */
    private $codigoImed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_INTERFAZ", type="string", length=20, nullable=true)
     */
    private $codigoInterfaz;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PARTICIPACION", type="integer", nullable=true)
     */
    private $participacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_AFECTO", type="boolean", nullable=false, options={"default"="1"})
     */
    private $esAfecto = '0';

    /**
     * @var \Guarismo
     *
     * @ORM\ManyToOne(targetEntity="Guarismo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_GUARISMO", referencedColumnName="ID")
     * })
     */
    private $idGuarismo;

    /**
     * @var \ItemPresupuestario
     *
     * @ORM\ManyToOne(targetEntity="ItemPresupuestario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ITEM_PRESUPUESTARIO", referencedColumnName="ID")
     * })
     */
    private $idItemPresupuestario;

    /**
     * @var \TipoPrestacion
     *
     * @ORM\ManyToOne(targetEntity="TipoPrestacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idTipoPrestacion;

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
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;

    /**
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA_FACTURADORA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresaFacturadora;

    /**
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresa;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_PROCEDIMIENTO", type="boolean", nullable=false, options={"default"="0"})
     */
    private $esProcedimiento = '0';

    /**
     * @var \AccionClinicaTipoExterno
     *
     * @ORM\ManyToOne(targetEntity="AccionClinicaTipoExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA_TIPO_EXTERNO", referencedColumnName="ID")
     * })
     */
    private $idAccionClinicaTipoExterno;


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
     * Set codigoAccionClinica.
     *
     * @param string $codigoAccionClinica
     *
     * @return AccionClinica
     */
    public function setCodigoAccionClinica($codigoAccionClinica)
    {
        $this->codigoAccionClinica = $codigoAccionClinica;

        return $this;
    }

    /**
     * Get codigoAccionClinica.
     *
     * @return string
     */
    public function getCodigoAccionClinica()
    {
        return $this->codigoAccionClinica;
    }

    /**
     * Set codigoFonasa.
     *
     * @param string $codigoFonasa
     *
     * @return AccionClinica
     */
    public function setCodigoFonasa($codigoFonasa)
    {
        $this->codigoFonasa = $codigoFonasa;

        return $this;
    }

    /**
     * Get codigoFonasa.
     *
     * @return string
     */
    public function getCodigoFonasa()
    {
        return $this->codigoFonasa;
    }

    /**
     * Set nombreAccionClinica.
     *
     * @param string $nombreAccionClinica
     *
     * @return AccionClinica
     */
    public function setNombreAccionClinica($nombreAccionClinica)
    {
        $this->nombreAccionClinica = $nombreAccionClinica;

        return $this;
    }

    /**
     * Get nombreAccionClinica.
     *
     * @return string
     */
    public function getNombreAccionClinica()
    {
        return $this->nombreAccionClinica;
    }

    /**
     * Set nombreAbreviado.
     *
     * @param string|null $nombreAbreviado
     *
     * @return AccionClinica
     */
    public function setNombreAbreviado($nombreAbreviado = null)
    {
        $this->nombreAbreviado = $nombreAbreviado;

        return $this;
    }

    /**
     * Get nombreAbreviado.
     *
     * @return string|null
     */
    public function getNombreAbreviado()
    {
        return $this->nombreAbreviado;
    }

    /**
     * Set codigoCuentaContableErp.
     *
     * @param int|null $codigoCuentaContableErp
     *
     * @return AccionClinica
     */
    public function setCodigoCuentaContableErp($codigoCuentaContableErp = null)
    {
        $this->codigoCuentaContableErp = $codigoCuentaContableErp;

        return $this;
    }

    /**
     * Get codigoCuentaContableErp.
     *
     * @return int|null
     */
    public function getCodigoCuentaContableErp()
    {
        return $this->codigoCuentaContableErp;
    }

    /**
     * Set duracion.
     *
     * @param \DateTime|null $duracion
     *
     * @return AccionClinica
     */
    public function setDuracion($duracion = null)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion.
     *
     * @return \DateTime|null
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return AccionClinica
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set aplicaRecargo.
     *
     * @param bool $aplicaRecargo
     *
     * @return AccionClinica
     */
    public function setAplicaRecargo($aplicaRecargo)
    {
        $this->aplicaRecargo = $aplicaRecargo;

        return $this;
    }

    /**
     * Get aplicaRecargo.
     *
     * @return bool
     */
    public function getAplicaRecargo()
    {
        return $this->aplicaRecargo;
    }

    /**
     * Set esImed.
     *
     * @param bool $esImed
     *
     * @return AccionClinica
     */
    public function setEsImed($esImed)
    {
        $this->esImed = $esImed;

        return $this;
    }

    /**
     * Get esImed.
     *
     * @return bool
     */
    public function getEsImed()
    {
        return $this->esImed;
    }

    /**
     * Set codigoImed.
     *
     * @param string|null $codigoImed
     *
     * @return AccionClinica
     */
    public function setCodigoImed($codigoImed = null)
    {
        $this->codigoImed = $codigoImed;

        return $this;
    }

    /**
     * Get codigoImed.
     *
     * @return string|null
     */
    public function getCodigoImed()
    {
        return $this->codigoImed;
    }

    /**
     * Set codigoInterfaz.
     *
     * @param string|null $codigoInterfaz
     *
     * @return AccionClinica
     */
    public function setCodigoInterfaz($codigoInterfaz = null)
    {
        $this->codigoInterfaz = $codigoInterfaz;

        return $this;
    }

    /**
     * Get codigoInterfaz.
     *
     * @return string|null
     */
    public function getCodigoInterfaz()
    {
        return $this->codigoInterfaz;
    }

    /**
     * Set participacion.
     *
     * @param int|null $participacion
     *
     * @return AccionClinica
     */
    public function setParticipacion($participacion = null)
    {
        $this->participacion = $participacion;

        return $this;
    }

    /**
     * Get participacion.
     *
     * @return int|null
     */
    public function getParticipacion()
    {
        return $this->participacion;
    }

    /**
     * Set esAfecto.
     *
     * @param bool $esAfecto
     *
     * @return AccionClinica
     */
    public function setEsAfecto($esAfecto)
    {
        $this->esAfecto = $esAfecto;

        return $this;
    }

    /**
     * Get esAfecto.
     *
     * @return bool
     */
    public function getEsAfecto()
    {
        return $this->esAfecto;
    }

    /**
     * Set idGuarismo.
     *
     * @param \Rebsol\HermesBundle\Entity\Guarismo $idGuarismo
     *
     * @return AccionClinica
     */
    public function setIdGuarismo(\Rebsol\HermesBundle\Entity\Guarismo $idGuarismo)
    {
        $this->idGuarismo = $idGuarismo;

        return $this;
    }

    /**
     * Get idGuarismo.
     *
     * @return \Rebsol\HermesBundle\Entity\Guarismo
     */
    public function getIdGuarismo()
    {
        return $this->idGuarismo;
    }

    /**
     * Set idSubEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresa
     *
     * @return AccionClinica
     */
    public function setIdSubEmpresa(\Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresa)
    {
        $this->idSubEmpresa = $idSubEmpresa;

        return $this;
    }

    /**
     * Get idSubEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEmpresa
     */
    public function getIdSubEmpresa()
    {
        return $this->idSubEmpresa;
    }

    /**
     * Set idSubEmpresaFacturadora.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEmpresa|null $idSubEmpresaFacturadora
     *
     * @return AccionClinica
     */
    public function setIdSubEmpresaFacturadora(\Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresaFacturadora = null)
    {
        $this->idSubEmpresaFacturadora = $idSubEmpresaFacturadora;

        return $this;
    }

    /**
     * Get idSubEmpresaFacturadora.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEmpresa|null
     */
    public function getIdSubEmpresaFacturadora()
    {
        return $this->idSubEmpresaFacturadora;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return AccionClinica
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
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return AccionClinica
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
     * Set idTipoPrestacion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrestacion $idTipoPrestacion
     *
     * @return AccionClinica
     */
    public function setIdTipoPrestacion(\Rebsol\HermesBundle\Entity\TipoPrestacion $idTipoPrestacion)
    {
        $this->idTipoPrestacion = $idTipoPrestacion;

        return $this;
    }

    /**
     * Get idTipoPrestacion.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPrestacion
     */
    public function getIdTipoPrestacion()
    {
        return $this->idTipoPrestacion;
    }

    /**
     * Set idItemPresupuestario.
     *
     * @param \Rebsol\HermesBundle\Entity\ItemPresupuestario|null $idItemPresupuestario
     *
     * @return AccionClinica
     */
    public function setIdItemPresupuestario(\Rebsol\HermesBundle\Entity\ItemPresupuestario $idItemPresupuestario = null)
    {
        $this->idItemPresupuestario = $idItemPresupuestario;

        return $this;
    }

    /**
     * Get idItemPresupuestario.
     *
     * @return \Rebsol\HermesBundle\Entity\ItemPresupuestario|null
     */
    public function getIdItemPresupuestario()
    {
        return $this->idItemPresupuestario;
    }

    /**
     * Set idNivelFonasa.
     *
     * @param \Rebsol\HermesBundle\Entity\NivelFonasa|null $idNivelFonasa
     *
     * @return AccionClinica
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

    public function getCodigoCompleto(){
        return $this->getCodigoAccionClinica(). " - " . $this->getCodigoFonasa();
    }

    /**
     * Set esAfecto.
     *
     * @param bool $esProcedimiento
     *
     * @return AccionClinica
     */
    public function setEsProcedimiento($esProcedimiento)
    {
        $this->esProcedimiento = $esProcedimiento;

        return $this;
    }

    /**
     * Get esProcedimiento.
     *
     * @return bool
     */
    public function getEsProcedimiento()
    {
        return $this->esProcedimiento;
    }

    /**
     * @return \AccionClinicaTipoExterno
     */
    public function getIdAccionClinicaTipoExterno()
    {
        return $this->idAccionClicaTipoExterno;
    }

    /**
     * @param \AccionClinicaTipoExterno $idAccionClinicaTipoExterno
     */
    public function setIdAccionClinicaTipoExterno($idAccionClinicaTipoExterno)
    {
        $this->idAccionClinicaTipoExterno = $idAccionClinicaTipoExterno;
    }


}
