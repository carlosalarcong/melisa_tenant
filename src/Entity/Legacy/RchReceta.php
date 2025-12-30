<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RchReceta
 *
 * @ORM\Table(name="rch_receta")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\RchRecetaRepository")
 */
class RchReceta
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
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var string
     *
     * @ORM\Column(name="FOLIO", type="string", length=255, nullable=false)
     */
    private $folio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var string
     *
     * @ORM\Column(name="DIAGNOSTICO", type="text", length=0, nullable=false)
     */
    private $diagnostico;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_COMPLEMENTO", type="datetime", nullable=true)
     */
    private $fechaComplemento;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_DESPACHO", type="datetime", nullable=true)
     */
    private $fechaDespacho;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ATENCION", type="datetime", nullable=true)
     */
    private $fechaAtencion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_FINALIZA", type="datetime", nullable=true)
     */
    private $fechaFinaliza;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_FARMACIA", type="text", length=0, nullable=true)
     */
    private $observacionFarmacia;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_GES", type="boolean", nullable=false)
     */
    private $esGes = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_REGISTRO_MANUAL", type="datetime", nullable=true)
     */
    private $fechaRegistroManual;

    /**
     * @var int
     *
     * @ORM\Column(name="FALTANTE", type="integer", nullable=false)
     */
    private $faltante = '0';

    /**
     * @var \ConsultaMedicaFc
     *
     * @ORM\ManyToOne(targetEntity="ConsultaMedicaFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONSULTA_MEDICA_FC", referencedColumnName="ID")
     * })
     */
    private $idConsultaMedicaFc;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO_SOLICITUD", referencedColumnName="ID")
     * })
     */
    private $idServicioSolicitud;

    /**
     * @var \RelCamaPaciente
     *
     * @ORM\ManyToOne(targetEntity="RelCamaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REL_CAMA_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idRelCamaPaciente;

    /**
     * @var \Patologia
     *
     * @ORM\ManyToOne(targetEntity="Patologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PATOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idPatologia;

    /**
     * @var \RchIndicacionFarmacologica
     *
     * @ORM\ManyToOne(targetEntity="RchIndicacionFarmacologica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_INDICACION_FARMACOLOGICA", referencedColumnName="ID")
     * })
     */
    private $idRchIndicacionFarmacologica;

    /**
     * @var \Programa
     *
     * @ORM\ManyToOne(targetEntity="Programa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROGRAMA", referencedColumnName="ID")
     * })
     */
    private $idPrograma;

    /**
     * @var \EspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="EspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESPECIALIDAD", referencedColumnName="ID")
     * })
     */
    private $idEspecialidad;

    /**
     * @var \RchRecetaEstado
     *
     * @ORM\ManyToOne(targetEntity="RchRecetaEstado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_RECETA_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idRchRecetaEstado;

    /**
     * @var \RecienNacido
     *
     * @ORM\ManyToOne(targetEntity="RecienNacido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RECIEN_NACIDO", referencedColumnName="ID")
     * })
     */
    private $idRecienNacido;

    /**
     * @var \ProgramaPatologia
     *
     * @ORM\ManyToOne(targetEntity="ProgramaPatologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROGRAMA_PATOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idProgramaPatologia;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_COMPLEMENTO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioComplemento;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAnulacion;

    /**
     * @var \ProgramaPatologiaDetalle
     *
     * @ORM\ManyToOne(targetEntity="ProgramaPatologiaDetalle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROGRAMA_PATOLOGIA_DETALLE", referencedColumnName="ID")
     * })
     */
    private $idProgramaPatologiaDetalle;

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
     * @var \Paciente
     *
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPaciente;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_DESPACHO", referencedColumnName="ID")
     * })
     */
    private $idUsuarioDespacho;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_FINALIZA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioFinaliza;

    /**
     * @var \Bodega
     *
     * @ORM\ManyToOne(targetEntity="Bodega")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BODEGA", referencedColumnName="ID")
     * })
     */
    private $idBodega;

    /**
     * @var \RchRecetaDispensacion
     *
     * @ORM\ManyToOne(targetEntity="RchRecetaDispensacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_RECETA_DISPENSACION", referencedColumnName="ID")
     * })
     */
    private $idRchRecetaDispensacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ATENCION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAtencion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_REGISTRO_MANUAL", referencedColumnName="ID")
     * })
     */
    private $idUsuarioRegistroManual;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_INDICACION", type="boolean", nullable=false)
     */
    private $esIndicacion = '0';

    /**
     * @var \TipoReceta
     *
     * @ORM\ManyToOne(targetEntity="TipoReceta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_RECETA", referencedColumnName="ID")
     * })
     */
    private $idTipoReceta;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_MAGISTRAL", type="integer", nullable=true, options={"default" : 0})
     */
    private $esMagistral;

    /**
     * @var \RchTipoReceta
     *
     * @ORM\ManyToOne(targetEntity="RchTipoReceta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_TIPO_RECETA", referencedColumnName="ID")
     * })
     */
    private $idRchTipoReceta;


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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return RchReceta
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set folio.
     *
     * @param string $folio
     *
     * @return RchReceta
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio.
     *
     * @return string
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return RchReceta
     */
    public function setObservacion($observacion = null)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion.
     *
     * @return string|null
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set diagnostico.
     *
     * @param string $diagnostico
     *
     * @return RchReceta
     */
    public function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    /**
     * Get diagnostico.
     *
     * @return string
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     * Set fechaComplemento.
     *
     * @param \DateTime|null $fechaComplemento
     *
     * @return RchReceta
     */
    public function setFechaComplemento($fechaComplemento = null)
    {
        $this->fechaComplemento = $fechaComplemento;

        return $this;
    }

    /**
     * Get fechaComplemento.
     *
     * @return \DateTime|null
     */
    public function getFechaComplemento()
    {
        return $this->fechaComplemento;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return RchReceta
     */
    public function setFechaAnulacion($fechaAnulacion = null)
    {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAnulacion()
    {
        return $this->fechaAnulacion;
    }

    /**
     * Set fechaDespacho.
     *
     * @param \DateTime|null $fechaDespacho
     *
     * @return RchReceta
     */
    public function setFechaDespacho($fechaDespacho = null)
    {
        $this->fechaDespacho = $fechaDespacho;

        return $this;
    }

    /**
     * Get fechaDespacho.
     *
     * @return \DateTime|null
     */
    public function getFechaDespacho()
    {
        return $this->fechaDespacho;
    }

    /**
     * Set fechaAtencion.
     *
     * @param \DateTime|null $fechaAtencion
     *
     * @return RchReceta
     */
    public function setFechaAtencion($fechaAtencion = null)
    {
        $this->fechaAtencion = $fechaAtencion;

        return $this;
    }

    /**
     * Get fechaAtencion.
     *
     * @return \DateTime|null
     */
    public function getFechaAtencion()
    {
        return $this->fechaAtencion;
    }

    /**
     * Set fechaFinaliza.
     *
     * @param \DateTime|null $fechaFinaliza
     *
     * @return RchReceta
     */
    public function setFechaFinaliza($fechaFinaliza = null)
    {
        $this->fechaFinaliza = $fechaFinaliza;

        return $this;
    }

    /**
     * Get fechaFinaliza.
     *
     * @return \DateTime|null
     */
    public function getFechaFinaliza()
    {
        return $this->fechaFinaliza;
    }

    /**
     * Set fechaRegistroManual.
     *
     * @param \DateTime|null $fechaRegistroManual
     *
     * @return RchReceta
     */
    public function setFechaRegistroManual($fechaRegistroManual = null)
    {
        $this->fechaRegistroManual = $fechaRegistroManual;

        return $this;
    }

    /**
     * Get fechaRegistroManual.
     *
     * @return \DateTime|null
     */
    public function getFechaRegistroManual()
    {
        return $this->fechaRegistroManual;
    }

    /**
     * Set observacionFarmacia.
     *
     * @param string|null $observacionFarmacia
     *
     * @return RchReceta
     */
    public function setObservacionFarmacia($observacionFarmacia = null)
    {
        $this->observacionFarmacia = $observacionFarmacia;

        return $this;
    }

    /**
     * Get observacionFarmacia.
     *
     * @return string|null
     */
    public function getObservacionFarmacia()
    {
        return $this->observacionFarmacia;
    }

    /**
     * Set faltante.
     *
     * @param int $faltante
     *
     * @return RchReceta
     */
    public function setFaltante($faltante)
    {
        $this->faltante = $faltante;

        return $this;
    }

    /**
     * Get faltante.
     *
     * @return int
     */
    public function getFaltante()
    {
        return $this->faltante;
    }

    /**
     * Set esGes.
     *
     * @param bool $esGes
     *
     * @return RchReceta
     */
    public function setEsGes($esGes)
    {
        $this->esGes = $esGes;

        return $this;
    }

    /**
     * Get esGes.
     *
     * @return bool
     */
    public function getEsGes()
    {
        return $this->esGes;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return RchReceta
     */
    public function setIdUsuarioCreacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idRchRecetaDispensacion.
     *
     * @param \Rebsol\HermesBundle\Entity\RchRecetaDispensacion $idRchRecetaDispensacion
     *
     * @return RchReceta
     */
    public function setIdRchRecetaDispensacion(\Rebsol\HermesBundle\Entity\RchRecetaDispensacion $idRchRecetaDispensacion)
    {
        $this->idRchRecetaDispensacion = $idRchRecetaDispensacion;

        return $this;
    }

    /**
     * Get idRchRecetaDispensacion.
     *
     * @return \Rebsol\HermesBundle\Entity\RchRecetaDispensacion
     */
    public function getIdRchRecetaDispensacion()
    {
        return $this->idRchRecetaDispensacion;
    }

    /**
     * Set idRelCamaPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\RelCamaPaciente|null $idRelCamaPaciente
     *
     * @return RchReceta
     */
    public function setIdRelCamaPaciente(\Rebsol\HermesBundle\Entity\RelCamaPaciente $idRelCamaPaciente = null)
    {
        $this->idRelCamaPaciente = $idRelCamaPaciente;

        return $this;
    }

    /**
     * Get idRelCamaPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\RelCamaPaciente|null
     */
    public function getIdRelCamaPaciente()
    {
        return $this->idRelCamaPaciente;
    }

    /**
     * Set idUsuarioComplemento.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioComplemento
     *
     * @return RchReceta
     */
    public function setIdUsuarioComplemento(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioComplemento = null)
    {
        $this->idUsuarioComplemento = $idUsuarioComplemento;

        return $this;
    }

    /**
     * Get idUsuarioComplemento.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioComplemento()
    {
        return $this->idUsuarioComplemento;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return RchReceta
     */
    public function setIdUsuarioAnulacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioAnulacion = null)
    {
        $this->idUsuarioAnulacion = $idUsuarioAnulacion;

        return $this;
    }

    /**
     * Get idUsuarioAnulacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAnulacion()
    {
        return $this->idUsuarioAnulacion;
    }

    /**
     * Set idUsuarioDespacho.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioDespacho
     *
     * @return RchReceta
     */
    public function setIdUsuarioDespacho(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioDespacho = null)
    {
        $this->idUsuarioDespacho = $idUsuarioDespacho;

        return $this;
    }

    /**
     * Get idUsuarioDespacho.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioDespacho()
    {
        return $this->idUsuarioDespacho;
    }

    /**
     * Set idUsuarioAtencion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAtencion
     *
     * @return RchReceta
     */
    public function setIdUsuarioAtencion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioAtencion = null)
    {
        $this->idUsuarioAtencion = $idUsuarioAtencion;

        return $this;
    }

    /**
     * Get idUsuarioAtencion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAtencion()
    {
        return $this->idUsuarioAtencion;
    }

    /**
     * Set idUsuarioFinaliza.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioFinaliza
     *
     * @return RchReceta
     */
    public function setIdUsuarioFinaliza(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFinaliza = null)
    {
        $this->idUsuarioFinaliza = $idUsuarioFinaliza;

        return $this;
    }

    /**
     * Get idUsuarioFinaliza.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioFinaliza()
    {
        return $this->idUsuarioFinaliza;
    }

    /**
     * Set idUsuarioRegistroManual.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioRegistroManual
     *
     * @return RchReceta
     */
    public function setIdUsuarioRegistroManual(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioRegistroManual = null)
    {
        $this->idUsuarioRegistroManual = $idUsuarioRegistroManual;

        return $this;
    }

    /**
     * Get idUsuarioRegistroManual.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioRegistroManual()
    {
        return $this->idUsuarioRegistroManual;
    }

    /**
     * Set idRchRecetaEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\RchRecetaEstado $idRchRecetaEstado
     *
     * @return RchReceta
     */
    public function setIdRchRecetaEstado(\Rebsol\HermesBundle\Entity\RchRecetaEstado $idRchRecetaEstado)
    {
        $this->idRchRecetaEstado = $idRchRecetaEstado;

        return $this;
    }

    /**
     * Get idRchRecetaEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\RchRecetaEstado
     */
    public function getIdRchRecetaEstado()
    {
        return $this->idRchRecetaEstado;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return RchReceta
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
     * Set idConsultaMedicaFc.
     *
     * @param \Rebsol\HermesBundle\Entity\ConsultaMedicaFc|null $idConsultaMedicaFc
     *
     * @return RchReceta
     */
    public function setIdConsultaMedicaFc(\Rebsol\HermesBundle\Entity\ConsultaMedicaFc $idConsultaMedicaFc = null)
    {
        $this->idConsultaMedicaFc = $idConsultaMedicaFc;

        return $this;
    }

    /**
     * Get idConsultaMedicaFc.
     *
     * @return \Rebsol\HermesBundle\Entity\ConsultaMedicaFc|null
     */
    public function getIdConsultaMedicaFc()
    {
        return $this->idConsultaMedicaFc;
    }

    /**
     * Set idRecienNacido.
     *
     * @param \Rebsol\HermesBundle\Entity\RecienNacido|null $idRecienNacido
     *
     * @return RchReceta
     */
    public function setIdRecienNacido(\Rebsol\HermesBundle\Entity\RecienNacido $idRecienNacido = null)
    {
        $this->idRecienNacido = $idRecienNacido;

        return $this;
    }

    /**
     * Get idRecienNacido.
     *
     * @return \Rebsol\HermesBundle\Entity\RecienNacido|null
     */
    public function getIdRecienNacido()
    {
        return $this->idRecienNacido;
    }

    /**
     * Set idRchIndicacionFarmacologica.
     *
     * @param \Rebsol\HermesBundle\Entity\RchIndicacionFarmacologica|null $idRchIndicacionFarmacologica
     *
     * @return RchReceta
     */
    public function setIdRchIndicacionFarmacologica(\Rebsol\HermesBundle\Entity\RchIndicacionFarmacologica $idRchIndicacionFarmacologica = null)
    {
        $this->idRchIndicacionFarmacologica = $idRchIndicacionFarmacologica;

        return $this;
    }

    /**
     * Get idRchIndicacionFarmacologica.
     *
     * @return \Rebsol\HermesBundle\Entity\RchIndicacionFarmacologica|null
     */
    public function getIdRchIndicacionFarmacologica()
    {
        return $this->idRchIndicacionFarmacologica;
    }

    /**
     * Set idPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\Paciente $idPaciente
     *
     * @return RchReceta
     */
    public function setIdPaciente(\Rebsol\HermesBundle\Entity\Paciente $idPaciente)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\Paciente
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idServicioSolicitud.
     *
     * @param \Rebsol\HermesBundle\Entity\Servicio $idServicioSolicitud
     *
     * @return RchReceta
     */
    public function setIdServicioSolicitud(\Rebsol\HermesBundle\Entity\Servicio $idServicioSolicitud)
    {
        $this->idServicioSolicitud = $idServicioSolicitud;

        return $this;
    }

    /**
     * Get idServicioSolicitud.
     *
     * @return \Rebsol\HermesBundle\Entity\Servicio
     */
    public function getIdServicioSolicitud()
    {
        return $this->idServicioSolicitud;
    }

    /**
     * Set idBodega.
     *
     * @param \Rebsol\HermesBundle\Entity\Bodega|null $idBodega
     *
     * @return RchReceta
     */
    public function setIdBodega(\Rebsol\HermesBundle\Entity\Bodega $idBodega = null)
    {
        $this->idBodega = $idBodega;

        return $this;
    }

    /**
     * Get idBodega.
     *
     * @return \Rebsol\HermesBundle\Entity\Bodega|null
     */
    public function getIdBodega()
    {
        return $this->idBodega;
    }

    /**
     * Set idEspecialidad.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica|null $idEspecialidad
     *
     * @return RchReceta
     */
    public function setIdEspecialidad(\Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidad = null)
    {
        $this->idEspecialidad = $idEspecialidad;

        return $this;
    }

    /**
     * Get idEspecialidad.
     *
     * @return \Rebsol\HermesBundle\Entity\EspecialidadMedica|null
     */
    public function getIdEspecialidad()
    {
        return $this->idEspecialidad;
    }

    /**
     * Set idPatologia.
     *
     * @param \Rebsol\HermesBundle\Entity\Patologia|null $idPatologia
     *
     * @return RchReceta
     */
    public function setIdPatologia(\Rebsol\HermesBundle\Entity\Patologia $idPatologia = null)
    {
        $this->idPatologia = $idPatologia;

        return $this;
    }

    /**
     * Get idPatologia.
     *
     * @return \Rebsol\HermesBundle\Entity\Patologia|null
     */
    public function getIdPatologia()
    {
        return $this->idPatologia;
    }

    /**
     * Set idPrograma.
     *
     * @param \Rebsol\HermesBundle\Entity\Programa|null $idPrograma
     *
     * @return RchReceta
     */
    public function setIdPrograma(\Rebsol\HermesBundle\Entity\Programa $idPrograma = null)
    {
        $this->idPrograma = $idPrograma;

        return $this;
    }

    /**
     * Get idPrograma.
     *
     * @return \Rebsol\HermesBundle\Entity\Programa|null
     */
    public function getIdPrograma()
    {
        return $this->idPrograma;
    }

    /**
     * Set idProgramaPatologia.
     *
     * @param \Rebsol\HermesBundle\Entity\ProgramaPatologia|null $idProgramaPatologia
     *
     * @return RchReceta
     */
    public function setIdProgramaPatologia(\Rebsol\HermesBundle\Entity\ProgramaPatologia $idProgramaPatologia = null)
    {
        $this->idProgramaPatologia = $idProgramaPatologia;

        return $this;
    }

    /**
     * Get idProgramaPatologia.
     *
     * @return \Rebsol\HermesBundle\Entity\ProgramaPatologia|null
     */
    public function getIdProgramaPatologia()
    {
        return $this->idProgramaPatologia;
    }

    /**
     * Set idProgramaPatologiaDetalle.
     *
     * @param \Rebsol\HermesBundle\Entity\ProgramaPatologiaDetalle|null $idProgramaPatologiaDetalle
     *
     * @return RchReceta
     */
    public function setIdProgramaPatologiaDetalle(\Rebsol\HermesBundle\Entity\ProgramaPatologiaDetalle $idProgramaPatologiaDetalle = null)
    {
        $this->idProgramaPatologiaDetalle = $idProgramaPatologiaDetalle;

        return $this;
    }

    /**
     * Get idProgramaPatologiaDetalle.
     *
     * @return \Rebsol\HermesBundle\Entity\ProgramaPatologiaDetalle|null
     */
    public function getIdProgramaPatologiaDetalle()
    {
        return $this->idProgramaPatologiaDetalle;
    }

    /**
     * @return bool
     */
    public function isEsIndicacion()
    {
        return $this->esIndicacion;
    }

    /**
     * @param bool $esIndicacion
     */
    public function setEsIndicacion($esIndicacion)
    {
        $this->esIndicacion = $esIndicacion;
    }

    /**
     * @return \TipoReceta
     */
    public function getIdTipoReceta()
    {
        return $this->idTipoReceta;
    }

    /**
     * @param \TipoReceta $idTipoReceta
     */
    public function setIdTipoReceta($idTipoReceta)
    {
        $this->idTipoReceta = $idTipoReceta;
    }

    /**
     * @return bool
     */
    public function isEsMagistral()
    {
        return $this->esMagistral;
    }

    /**
     * @param bool $esMagistral
     */
    public function setEsMagistral($esMagistral)
    {
        $this->esMagistral = $esMagistral;
    }

    /**
     * @return \RchTipoReceta
     */
    public function getIdRchTipoReceta()
    {
        return $this->idRchTipoReceta;
    }

    /**
     * @param \RchTipoReceta $idRchTipoReceta
     */
    public function setIdRchTipoReceta($idRchTipoReceta)
    {
        $this->idRchTipoReceta = $idRchTipoReceta;
    }

}
