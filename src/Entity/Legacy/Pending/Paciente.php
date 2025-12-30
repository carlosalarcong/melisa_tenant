<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paciente
 *
 * @ORM\Table(name="paciente")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PacienteRepository")
 */
class Paciente
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
     * @ORM\Column(name="EVENTO", type="integer", nullable=false)
     */
    private $evento;

    /**
     * @var int
     *
     * @ORM\Column(name="NUMERO_ATENCION", type="integer", nullable=false)
     */
    private $numeroAtencion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_INGRESO", type="datetime", nullable=false)
     */
    private $fechaIngreso;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ES_EXTERNO", type="integer", nullable=true)
     */
    private $esExterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PROFESIONAL_EXTERNO", type="string", length=150, nullable=true)
     */
    private $profesionalExterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ORDEN_EXAMEN", type="string", length=100, nullable=true)
     */
    private $ordenExamen;

    /**
     * @var \DerivadorExterno
     *
     * @ORM\ManyToOne(targetEntity="DerivadorExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_DERIVADOR_EXTERNO", referencedColumnName="ID")
     * })
     */
    private $idDerivadorExterno;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONVENIO", referencedColumnName="ID")
     * })
     */
    private $idConvenio;

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
     * @var \PrPlan
     *
     * @ORM\ManyToOne(targetEntity="PrPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PLAN", referencedColumnName="ID")
     * })
     */
    private $idPlan;

    /**
     * @var \Pnatural
     *
     * @ORM\ManyToOne(targetEntity="Pnatural")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PNATURAL", referencedColumnName="ID")
     * })
     */
    private $idPnatural;

    /**
     * @var \Pnatural
     *
     * @ORM\ManyToOne(targetEntity="Pnatural")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TUTOR", referencedColumnName="ID")
     * })
     */
    private $idTutor;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PROFESIONAL", referencedColumnName="ID")
     * })
     */
    private $idProfesional;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FINANCIADOR", referencedColumnName="ID")
     * })
     */
    private $idFinanciador;

    /**
     * @var \Origen
     *
     * @ORM\ManyToOne(targetEntity="Origen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ORIGEN", referencedColumnName="ID")
     * })
     */
    private $idOrigen;

    /**
     * @var \TipoAtencionFc
     *
     * @ORM\ManyToOne(targetEntity="TipoAtencionFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_ATENCION_FC", referencedColumnName="ID")
     * })
     */
    private $idTipoAtencionFc;

    /**
     * @var \EmpresaSolicitante
     *
     * @ORM\ManyToOne(targetEntity="EmpresaSolicitante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA_SOLICITANTE", referencedColumnName="ID")
     * })
     */
    private $idEmpresaSolicitante;

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
     * Set evento.
     *
     * @param int $evento
     *
     * @return Paciente
     */
    public function setEvento($evento)
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get evento.
     *
     * @return int
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * Set numeroAtencion.
     *
     * @param int $numeroAtencion
     *
     * @return Paciente
     */
    public function setNumeroAtencion($numeroAtencion)
    {
        $this->numeroAtencion = $numeroAtencion;

        return $this;
    }

    /**
     * Get numeroAtencion.
     *
     * @return int
     */
    public function getNumeroAtencion()
    {
        return $this->numeroAtencion;
    }

    /**
     * Set fechaIngreso.
     *
     * @param \DateTime $fechaIngreso
     *
     * @return Paciente
     */
    public function setFechaIngreso($fechaIngreso)
    {
        $this->fechaIngreso = $fechaIngreso;

        return $this;
    }

    /**
     * Get fechaIngreso.
     *
     * @return \DateTime
     */
    public function getFechaIngreso()
    {
        return $this->fechaIngreso;
    }

    /**
     * Set esExterno.
     *
     * @param int|null $esExterno
     *
     * @return Paciente
     */
    public function setEsExterno($esExterno = null)
    {
        $this->esExterno = $esExterno;

        return $this;
    }

    /**
     * Get esExterno.
     *
     * @return int|null
     */
    public function getEsExterno()
    {
        return $this->esExterno;
    }

    /**
     * Set profesionalExterno.
     *
     * @param string|null $profesionalExterno
     *
     * @return Paciente
     */
    public function setProfesionalExterno($profesionalExterno = null)
    {
        $this->profesionalExterno = $profesionalExterno;

        return $this;
    }

    /**
     * Get profesionalExterno.
     *
     * @return string|null
     */
    public function getProfesionalExterno()
    {
        return $this->profesionalExterno;
    }

    /**
     * Set ordenExamen.
     *
     * @param string|null $ordenExamen
     *
     * @return Paciente
     */
    public function setOrdenExamen($ordenExamen = null)
    {
        $this->ordenExamen = $ordenExamen;

        return $this;
    }

    /**
     * Get ordenExamen.
     *
     * @return string|null
     */
    public function getOrdenExamen()
    {
        return $this->ordenExamen;
    }

    /**
     * Set idPnatural.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural $idPnatural
     *
     * @return Paciente
     */
    public function setIdPnatural(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural $idPnatural)
    {
        $this->idPnatural = $idPnatural;

        return $this;
    }

    /**
     * Get idPnatural.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural
     */
    public function getIdPnatural()
    {
        return $this->idPnatural;
    }

    /**
     * Set idTipoAtencionFc.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TipoAtencionFc $idTipoAtencionFc
     *
     * @return Paciente
     */
    public function setIdTipoAtencionFc(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TipoAtencionFc $idTipoAtencionFc)
    {
        $this->idTipoAtencionFc = $idTipoAtencionFc;

        return $this;
    }

    /**
     * Get idTipoAtencionFc.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TipoAtencionFc
     */
    public function getIdTipoAtencionFc()
    {
        return $this->idTipoAtencionFc;
    }

    /**
     * Set idProfesional.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null $idProfesional
     *
     * @return Paciente
     */
    public function setIdProfesional(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idProfesional = null)
    {
        $this->idProfesional = $idProfesional;

        return $this;
    }

    /**
     * Get idProfesional.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol|null
     */
    public function getIdProfesional()
    {
        return $this->idProfesional;
    }

    /**
     * Set idOrigen.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Origen|null $idOrigen
     *
     * @return Paciente
     */
    public function setIdOrigen(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Origen $idOrigen = null)
    {
        $this->idOrigen = $idOrigen;

        return $this;
    }

    /**
     * Get idOrigen.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Origen|null
     */
    public function getIdOrigen()
    {
        return $this->idOrigen;
    }

    /**
     * Set idFinanciador.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idFinanciador
     *
     * @return Paciente
     */
    public function setIdFinanciador(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idFinanciador)
    {
        $this->idFinanciador = $idFinanciador;

        return $this;
    }

    /**
     * Get idFinanciador.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision
     */
    public function getIdFinanciador()
    {
        return $this->idFinanciador;
    }

    /**
     * Set idConvenio.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision|null $idConvenio
     *
     * @return Paciente
     */
    public function setIdConvenio(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision $idConvenio = null)
    {
        $this->idConvenio = $idConvenio;

        return $this;
    }

    /**
     * Get idConvenio.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Prevision|null
     */
    public function getIdConvenio()
    {
        return $this->idConvenio;
    }

    /**
     * Set idPlan.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PrPlan|null $idPlan
     *
     * @return Paciente
     */
    public function setIdPlan(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PrPlan $idPlan = null)
    {
        $this->idPlan = $idPlan;

        return $this;
    }

    /**
     * Get idPlan.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\PrPlan|null
     */
    public function getIdPlan()
    {
        return $this->idPlan;
    }

    /**
     * Set idDerivadorExterno.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DerivadorExterno|null $idDerivadorExterno
     *
     * @return Paciente
     */
    public function setIdDerivadorExterno(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DerivadorExterno $idDerivadorExterno = null)
    {
        $this->idDerivadorExterno = $idDerivadorExterno;

        return $this;
    }

    /**
     * Get idDerivadorExterno.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DerivadorExterno|null
     */
    public function getIdDerivadorExterno()
    {
        return $this->idDerivadorExterno;
    }

    /**
     * Set idEmpresa.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Empresa $idEmpresa
     *
     * @return Paciente
     */
    public function setIdEmpresa(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idTutor.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural|null $idTutor
     *
     * @return Paciente
     */
    public function setIdTutor(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural $idTutor = null)
    {
        $this->idTutor = $idTutor;

        return $this;
    }

    /**
     * Get idTutor.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Pnatural|null
     */
    public function getIdTutor()
    {
        return $this->idTutor;
    }

    /**
     * @return \EmpresaSolicitante
     */
    public function getIdEmpresaSolicitante()
    {
        return $this->idEmpresaSolicitante;
    }

    /**
     * @param \EmpresaSolicitante $idEmpresaSolicitante
     */
    public function setIdEmpresaSolicitante($idEmpresaSolicitante)
    {
        $this->idEmpresaSolicitante = $idEmpresaSolicitante;
    }


}
