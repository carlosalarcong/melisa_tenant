<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RchIndicacionFarmacologica
 *
 * @ORM\Table(name="rch_indicacion_farmacologica")
 * @ORM\Entity
 */
class RchIndicacionFarmacologica
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INDICACION", type="text", length=0, nullable=true)
     */
    private $indicacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_REGIMEN", type="text", length=0, nullable=true)
     */
    private $observacionRegimen;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MOVILIZAR_CAMA", type="boolean", nullable=true)
     */
    private $movilizarCama = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TIENE_MEDIDA_COTENCION", type="boolean", nullable=true)
     */
    private $tieneMedidaCotencion = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TIENE_PROFILAXIS_FARMACOLOGICA", type="boolean", nullable=true)
     */
    private $tieneProfilaxisFarmacologica = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TIENE_KINE_DIURNA", type="boolean", nullable=true)
     */
    private $tieneKineDiurna = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="TIENE_KINE_NOCTURNA", type="boolean", nullable=true)
     */
    private $tieneKineNocturna = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="CANTIDAD_KINE_DIURNA", type="integer", nullable=true)
     */
    private $cantidadKineDiurna;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CANTIDAD_KINE_NOCTURNA", type="integer", nullable=true)
     */
    private $cantidadKineNocturna;

    /**
     * @var string|null
     *
     * @ORM\Column(name="EXAMEN", type="text", length=0, nullable=true)
     */
    private $examen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ENFERMERIA", type="text", length=0, nullable=true)
     */
    private $enfermeria;

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
     * @var \ReposoDetalle
     *
     * @ORM\ManyToOne(targetEntity="ReposoDetalle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REPOSO_DETALLE", referencedColumnName="ID")
     * })
     */
    private $idReposoDetalle;

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
     * @var \TerapiaVentilatoria
     *
     * @ORM\ManyToOne(targetEntity="TerapiaVentilatoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TERAPIA_VENTILATORIA", referencedColumnName="ID")
     * })
     */
    private $idTerapiaVentilatoria;

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
     * @var \TipoKinesiologia
     *
     * @ORM\ManyToOne(targetEntity="TipoKinesiologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_KINESIOLOGIA", referencedColumnName="ID")
     * })
     */
    private $idTipoKinesiologia;

    /**
     * @var \Aislamiento
     *
     * @ORM\ManyToOne(targetEntity="Aislamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_AISLAMIENTO", referencedColumnName="ID")
     * })
     */
    private $idAislamiento;

    /**
     * @var \Regimen
     *
     * @ORM\ManyToOne(targetEntity="Regimen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REGIMEN", referencedColumnName="ID")
     * })
     */
    private $idRegimen;

    /**
     * @var \ProfilaxisMecanica
     *
     * @ORM\ManyToOne(targetEntity="ProfilaxisMecanica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROFILAXIS_MECANICA", referencedColumnName="ID")
     * })
     */
    private $idProfilaxisMecanica;

    /**
     * @var \RchEstadoIndicacionFarmacologica
     *
     * @ORM\ManyToOne(targetEntity="RchEstadoIndicacionFarmacologica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RCH_ESTADO_INDICACION_FARMACOLOGICA", referencedColumnName="ID")
     * })
     */
    private $idRchEstadoIndicacionFarmacologica;

    /**
     * @var \Oxigenoterapia
     *
     * @ORM\ManyToOne(targetEntity="Oxigenoterapia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_OXIGENOTERAPIA", referencedColumnName="ID")
     * })
     */
    private $idOxigenoterapia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="TELEFONO", type="boolean", nullable=true, options={"default": 0})
     */
    private $telefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_DETALLE", type="string", nullable=true)
     */
    private $telefonoDetalle;


    /**
     * @var boolean
     *
     * @ORM\Column(name="VISITAS", type="boolean", nullable=true, options={"default": 0})
     */
    private $visitas;

    /**
     * @var string|null
     *
     * @ORM\Column(name="VISITAS_DETALLE", type="string", nullable=true)
     */
    private $visitasDetalle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CUIDADOR", type="boolean", nullable=true, options={"default": 0})
     */
    private $cuidador;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CUIDADOR_DIURNO", type="boolean", nullable=true, options={"default": 0})
     */
    private $cuidadorDiurno;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CUIDADOR_NOCTURNO", type="boolean", nullable=true, options={"default": 0})
     */
    private $cuidadorNocturno;

    /**
     * @var boolean
     *
     * @ORM\Column(name="TERAPIA_OCUPACIONAL", type="boolean", nullable=true, options={"default": 0})
     */
    private $terapiaOcupacional;

    /**
     * @var \Reposo
     *
     * @ORM\ManyToOne(targetEntity="Reposo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REPOSO", referencedColumnName="ID")
     * })
     */
    private $idReposo;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_INDICACIONES_CUIDADOS", type="boolean", nullable=true, options={"comment"="No debería aceptar nulo, por eso el valor default = 0"})
     */
    private $esIndicacionesCuidados = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_INGRESO_MEDICO", type="boolean", nullable=false, options={"default"="0"})
     */
    private $esIngresoMedico = '0';

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
     * @return RchIndicacionFarmacologica
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
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return RchIndicacionFarmacologica
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
     * Set indicacion.
     *
     * @param string|null $indicacion
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIndicacion($indicacion = null)
    {
        $this->indicacion = $indicacion;

        return $this;
    }

    /**
     * Get indicacion.
     *
     * @return string|null
     */
    public function getIndicacion()
    {
        return $this->indicacion;
    }

    /**
     * Set observacionRegimen.
     *
     * @param string|null $observacionRegimen
     *
     * @return RchIndicacionFarmacologica
     */
    public function setObservacionRegimen($observacionRegimen = null)
    {
        $this->observacionRegimen = $observacionRegimen;

        return $this;
    }

    /**
     * Get observacionRegimen.
     *
     * @return string|null
     */
    public function getObservacionRegimen()
    {
        return $this->observacionRegimen;
    }

    /**
     * Set movilizarCama.
     *
     * @param bool|null $movilizarCama
     *
     * @return RchIndicacionFarmacologica
     */
    public function setMovilizarCama($movilizarCama = null)
    {
        $this->movilizarCama = $movilizarCama;

        return $this;
    }

    /**
     * Get movilizarCama.
     *
     * @return bool|null
     */
    public function getMovilizarCama()
    {
        return $this->movilizarCama;
    }

    /**
     * Set tieneMedidaCotencion.
     *
     * @param bool|null $tieneMedidaCotencion
     *
     * @return RchIndicacionFarmacologica
     */
    public function setTieneMedidaCotencion($tieneMedidaCotencion = null)
    {
        $this->tieneMedidaCotencion = $tieneMedidaCotencion;

        return $this;
    }

    /**
     * Get tieneMedidaCotencion.
     *
     * @return bool|null
     */
    public function getTieneMedidaCotencion()
    {
        return $this->tieneMedidaCotencion;
    }

    /**
     * Set tieneProfilaxisFarmacologica.
     *
     * @param bool|null $tieneProfilaxisFarmacologica
     *
     * @return RchIndicacionFarmacologica
     */
    public function setTieneProfilaxisFarmacologica($tieneProfilaxisFarmacologica = null)
    {
        $this->tieneProfilaxisFarmacologica = $tieneProfilaxisFarmacologica;

        return $this;
    }

    /**
     * Get tieneProfilaxisFarmacologica.
     *
     * @return bool|null
     */
    public function getTieneProfilaxisFarmacologica()
    {
        return $this->tieneProfilaxisFarmacologica;
    }

    /**
     * Set tieneKineDiurna.
     *
     * @param bool|null $tieneKineDiurna
     *
     * @return RchIndicacionFarmacologica
     */
    public function setTieneKineDiurna($tieneKineDiurna = null)
    {
        $this->tieneKineDiurna = $tieneKineDiurna;

        return $this;
    }

    /**
     * Get tieneKineDiurna.
     *
     * @return bool|null
     */
    public function getTieneKineDiurna()
    {
        return $this->tieneKineDiurna;
    }

    /**
     * Set tieneKineNocturna.
     *
     * @param bool|null $tieneKineNocturna
     *
     * @return RchIndicacionFarmacologica
     */
    public function setTieneKineNocturna($tieneKineNocturna = null)
    {
        $this->tieneKineNocturna = $tieneKineNocturna;

        return $this;
    }

    /**
     * Get tieneKineNocturna.
     *
     * @return bool|null
     */
    public function getTieneKineNocturna()
    {
        return $this->tieneKineNocturna;
    }

    /**
     * Set cantidadKineDiurna.
     *
     * @param int|null $cantidadKineDiurna
     *
     * @return RchIndicacionFarmacologica
     */
    public function setCantidadKineDiurna($cantidadKineDiurna = null)
    {
        $this->cantidadKineDiurna = $cantidadKineDiurna;

        return $this;
    }

    /**
     * Get cantidadKineDiurna.
     *
     * @return int|null
     */
    public function getCantidadKineDiurna()
    {
        return $this->cantidadKineDiurna;
    }

    /**
     * Set cantidadKineNocturna.
     *
     * @param int|null $cantidadKineNocturna
     *
     * @return RchIndicacionFarmacologica
     */
    public function setCantidadKineNocturna($cantidadKineNocturna = null)
    {
        $this->cantidadKineNocturna = $cantidadKineNocturna;

        return $this;
    }

    /**
     * Get cantidadKineNocturna.
     *
     * @return int|null
     */
    public function getCantidadKineNocturna()
    {
        return $this->cantidadKineNocturna;
    }

    /**
     * Set examen.
     *
     * @param string|null $examen
     *
     * @return RchIndicacionFarmacologica
     */
    public function setExamen($examen = null)
    {
        $this->examen = $examen;

        return $this;
    }

    /**
     * Get examen.
     *
     * @return string|null
     */
    public function getExamen()
    {
        return $this->examen;
    }

    /**
     * Set enfermeria.
     *
     * @param string|null $enfermeria
     *
     * @return RchIndicacionFarmacologica
     */
    public function setEnfermeria($enfermeria = null)
    {
        $this->enfermeria = $enfermeria;

        return $this;
    }

    /**
     * Get enfermeria.
     *
     * @return string|null
     */
    public function getEnfermeria()
    {
        return $this->enfermeria;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return RchIndicacionFarmacologica
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
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return RchIndicacionFarmacologica
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
     * Set idRelCamaPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\RelCamaPaciente $idRelCamaPaciente
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdRelCamaPaciente(\Rebsol\HermesBundle\Entity\RelCamaPaciente $idRelCamaPaciente)
    {
        $this->idRelCamaPaciente = $idRelCamaPaciente;

        return $this;
    }

    /**
     * Get idRelCamaPaciente.
     *
     * @return \Rebsol\HermesBundle\Entity\RelCamaPaciente
     */
    public function getIdRelCamaPaciente()
    {
        return $this->idRelCamaPaciente;
    }

    /**
     * Set idRchEstadoIndicacionFarmacologica.
     *
     * @param \Rebsol\HermesBundle\Entity\RchEstadoIndicacionFarmacologica $idRchEstadoIndicacionFarmacologica
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdRchEstadoIndicacionFarmacologica(\Rebsol\HermesBundle\Entity\RchEstadoIndicacionFarmacologica $idRchEstadoIndicacionFarmacologica)
    {
        $this->idRchEstadoIndicacionFarmacologica = $idRchEstadoIndicacionFarmacologica;

        return $this;
    }

    /**
     * Get idRchEstadoIndicacionFarmacologica.
     *
     * @return \Rebsol\HermesBundle\Entity\RchEstadoIndicacionFarmacologica
     */
    public function getIdRchEstadoIndicacionFarmacologica()
    {
        return $this->idRchEstadoIndicacionFarmacologica;
    }

    /**
     * Set idAislamiento.
     *
     * @param \Rebsol\HermesBundle\Entity\Aislamiento|null $idAislamiento
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdAislamiento(\Rebsol\HermesBundle\Entity\Aislamiento $idAislamiento = null)
    {
        $this->idAislamiento = $idAislamiento;

        return $this;
    }

    /**
     * Get idAislamiento.
     *
     * @return \Rebsol\HermesBundle\Entity\Aislamiento|null
     */
    public function getIdAislamiento()
    {
        return $this->idAislamiento;
    }

    /**
     * Set idReposoDetalle.
     *
     * @param \Rebsol\HermesBundle\Entity\ReposoDetalle|null $idReposoDetalle
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdReposoDetalle(\Rebsol\HermesBundle\Entity\ReposoDetalle $idReposoDetalle = null)
    {
        $this->idReposoDetalle = $idReposoDetalle;

        return $this;
    }

    /**
     * Get idReposoDetalle.
     *
     * @return \Rebsol\HermesBundle\Entity\ReposoDetalle|null
     */
    public function getIdReposoDetalle()
    {
        return $this->idReposoDetalle;
    }

    /**
     * Set idRegimen.
     *
     * @param \Rebsol\HermesBundle\Entity\Regimen|null $idRegimen
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdRegimen(\Rebsol\HermesBundle\Entity\Regimen $idRegimen = null)
    {
        $this->idRegimen = $idRegimen;

        return $this;
    }

    /**
     * Get idRegimen.
     *
     * @return \Rebsol\HermesBundle\Entity\Regimen|null
     */
    public function getIdRegimen()
    {
        return $this->idRegimen;
    }

    /**
     * Set idProfilaxisMecanica.
     *
     * @param \Rebsol\HermesBundle\Entity\ProfilaxisMecanica|null $idProfilaxisMecanica
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdProfilaxisMecanica(\Rebsol\HermesBundle\Entity\ProfilaxisMecanica $idProfilaxisMecanica = null)
    {
        $this->idProfilaxisMecanica = $idProfilaxisMecanica;

        return $this;
    }

    /**
     * Get idProfilaxisMecanica.
     *
     * @return \Rebsol\HermesBundle\Entity\ProfilaxisMecanica|null
     */
    public function getIdProfilaxisMecanica()
    {
        return $this->idProfilaxisMecanica;
    }

    /**
     * Set idTipoKinesiologia.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoKinesiologia|null $idTipoKinesiologia
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdTipoKinesiologia(\Rebsol\HermesBundle\Entity\TipoKinesiologia $idTipoKinesiologia = null)
    {
        $this->idTipoKinesiologia = $idTipoKinesiologia;

        return $this;
    }

    /**
     * Get idTipoKinesiologia.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoKinesiologia|null
     */
    public function getIdTipoKinesiologia()
    {
        return $this->idTipoKinesiologia;
    }

    /**
     * Set idTerapiaVentilatoria.
     *
     * @param \Rebsol\HermesBundle\Entity\TerapiaVentilatoria|null $idTerapiaVentilatoria
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdTerapiaVentilatoria(\Rebsol\HermesBundle\Entity\TerapiaVentilatoria $idTerapiaVentilatoria = null)
    {
        $this->idTerapiaVentilatoria = $idTerapiaVentilatoria;

        return $this;
    }

    /**
     * Get idTerapiaVentilatoria.
     *
     * @return \Rebsol\HermesBundle\Entity\TerapiaVentilatoria|null
     */
    public function getIdTerapiaVentilatoria()
    {
        return $this->idTerapiaVentilatoria;
    }

    /**
     * Set idOxigenoterapia.
     *
     * @param \Rebsol\HermesBundle\Entity\Oxigenoterapia|null $idOxigenoterapia
     *
     * @return RchIndicacionFarmacologica
     */
    public function setIdOxigenoterapia(\Rebsol\HermesBundle\Entity\Oxigenoterapia $idOxigenoterapia = null)
    {
        $this->idOxigenoterapia = $idOxigenoterapia;

        return $this;
    }

    /**
     * Get idOxigenoterapia.
     *
     * @return \Rebsol\HermesBundle\Entity\Oxigenoterapia|null
     */
    public function getIdOxigenoterapia()
    {
        return $this->idOxigenoterapia;
    }

    /**
     * @return bool
     */
    public function isTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param bool $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }


    /**
     * @return bool
     */
    public function isVisitas()
    {
        return $this->visitas;
    }

    /**
     * @param bool $visitas
     */
    public function setVisitas($visitas)
    {
        $this->visitas = $visitas;
    }


    /**
     * @return string|null
     */
    public function getTelefonoDetalle()
    {
        return $this->telefonoDetalle;
    }

    /**
     * @param string|null $telefonoDetalle
     */
    public function setTelefonoDetalle($telefonoDetalle)
    {
        $this->telefonoDetalle = $telefonoDetalle;
    }

    /**
     * @return string|null
     */
    public function getVisitasDetalle()
    {
        return $this->visitasDetalle;
    }

    /**
     * @param string|null $visitasDetalle
     */
    public function setVisitasDetalle($visitasDetalle)
    {
        $this->visitasDetalle = $visitasDetalle;
    }

    /**
     * @return bool
     */
    public function isCuidador()
    {
        return $this->cuidador;
    }

    /**
     * @param bool $cuidador
     */
    public function setCuidador($cuidador)
    {
        $this->cuidador = $cuidador;
    }

    /**
     * @return string|null
     */
    public function getCuidadorDiurno()
    {
        return $this->CuidadorDiurno;
    }

    /**
     * @param string|null $cuidadorDiurno
     */
    public function setCuidadorDiurno($cuidadorDiurno)
    {
        $this->cuidadorDiurno = $cuidadorDiurno;
    }


    /**
     * @return string|null
     */
    public function getCuidadorNocturno()
    {
        return $this->CuidadorNocturno;
    }

    /**
     * @param string|null $cuidadorNocturno
     */
    public function setCuidadorNocturno($cuidadorNocturno)
    {
        $this->cuidadorNocturno = $cuidadorNocturno;
    }

    /**
     * @return bool
     */
    public function isTerapiaOcupacional()
    {
        return $this->terapiaOcupacional;
    }

    /**
     * @param bool $terapiaOcupacional
     */
    public function setTerapiaOcupacional($terapiaOcupacional)
    {
        $this->terapiaOcupacional = $terapiaOcupacional;
    }

    /**
     * Set idReposo.
     *
     * @param \Rebsol\HermesBundle\Entity\Reposo $idReposo
     *
     * @return ReposoDetalle
     */
    public function setIdReposo(\Rebsol\HermesBundle\Entity\Reposo $idReposo= null)
    {
        $this->idReposo = $idReposo;

        return $this;
    }

    /**
     * Get idReposo.
     *
     * @return \Rebsol\HermesBundle\Entity\Reposo
     */
    public function getIdReposo()
    {
        return $this->idReposo;
    }
    /**
     * Set esGES.
     *
     * @param bool|null $esIndicacionesCuidados
     *
     * @return AccionClinicaPaciente
     */
    public function setEsIndicacionesCuidados($esIndicacionesCuidados = null)
    {
        $this->esIndicacionesCuidados = $esIndicacionesCuidados;

        return $this;
    }

    /**
     * Get esGES.
     *
     * @return bool|null
     */
    public function getEsIndicacionesCuidados()
    {
        return $this->esIndicacionesCuidados;
    }

    /**
     * @return bool|null
     */
    public function getEsIngresoMedico()
    {
        return $this->esIngresoMedico;
    }

    /**
     * @param bool|null $esIngresoMedico
     */
    public function setEsIngresoMedico($esIngresoMedico)
    {
        $this->esIngresoMedico = $esIngresoMedico;
    }

}
