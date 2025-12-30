<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecienNacido
 *
 * @ORM\Table(name="recien_nacido")
 * @ORM\Entity
 */
class RecienNacido
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
     * @ORM\Column(name="NOMBRE_PNATURAL", type="string", length=60, nullable=false)
     */
    private $nombrePnatural;

    /**
     * @var string
     *
     * @ORM\Column(name="APELLIDO_PATERNO", type="string", length=45, nullable=false)
     */
    private $apellidoPaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="APELLIDO_MATERNO", type="string", length=45, nullable=false)
     */
    private $apellidoMaterno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_NACIMIENTO", type="datetime", nullable=false)
     */
    private $fechaNacimiento;

    /**
     * @var int
     *
     * @ORM\Column(name="MINUTOS_APEGO", type="integer", nullable=false)
     */
    private $minutosApego;

    /**
     * @var bool
     *
     * @ORM\Column(name="ASISTE_PADRE_PRE_PARTO", type="boolean", nullable=false)
     */
    private $asistePadrePreParto = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ASISTE_PADRE_PARTO", type="boolean", nullable=false)
     */
    private $asistePadreParto = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="INDICACION_ANESTESIA", type="time", nullable=false)
     */
    private $indicacionAnestesia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="PRIMERA_DOSIS_ANESTESIA", type="time", nullable=false)
     */
    private $primeraDosisAnestesia;

    /**
     * @var int
     *
     * @ORM\Column(name="CIRCUNFERENCIA_CRANEANA_CM", type="integer", nullable=false)
     */
    private $circunferenciaCraneanaCm;

    /**
     * @var int
     *
     * @ORM\Column(name="CIRCUNFERENCIA_CRANEANA_MM", type="integer", nullable=false)
     */
    private $circunferenciaCraneanaMm;

    /**
     * @var int
     *
     * @ORM\Column(name="APGAR1", type="integer", nullable=false)
     */
    private $apgar1;

    /**
     * @var int
     *
     * @ORM\Column(name="APGAR2", type="integer", nullable=false)
     */
    private $apgar2;

    /**
     * @var int
     *
     * @ORM\Column(name="PESO", type="integer", nullable=false)
     */
    private $peso;

    /**
     * @var int
     *
     * @ORM\Column(name="TALLA_CM", type="integer", nullable=false)
     */
    private $tallaCm;

    /**
     * @var int
     *
     * @ORM\Column(name="TALLA_MM", type="integer", nullable=false)
     */
    private $tallaMm;

    /**
     * @var int
     *
     * @ORM\Column(name="SEMANAS_GESTACION", type="integer", nullable=false)
     */
    private $semanasGestacion;

    /**
     * @var int
     *
     * @ORM\Column(name="DIAS_GESTACION", type="integer", nullable=false)
     */
    private $diasGestacion;

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
     * @var \Sexo
     *
     * @ORM\ManyToOne(targetEntity="Sexo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SEXO", referencedColumnName="ID")
     * })
     */
    private $idSexo;

    /**
     * @var \TipoParto
     *
     * @ORM\ManyToOne(targetEntity="TipoParto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PARTO", referencedColumnName="ID")
     * })
     */
    private $idTipoParto;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PERSONA", referencedColumnName="ID")
     * })
     */
    private $idPersona;

    /**
     * @var \EstadoRecienNacido
     *
     * @ORM\ManyToOne(targetEntity="EstadoRecienNacido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_RECIEN_NACIDO", referencedColumnName="ID")
     * })
     */
    private $idEstadoRecienNacido;

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
     * @var \Paciente
     *
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPaciente;



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
     * @return RecienNacido
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
     * Set nombrePnatural.
     *
     * @param string $nombrePnatural
     *
     * @return RecienNacido
     */
    public function setNombrePnatural($nombrePnatural)
    {
        $this->nombrePnatural = $nombrePnatural;

        return $this;
    }

    /**
     * Get nombrePnatural.
     *
     * @return string
     */
    public function getNombrePnatural()
    {
        return $this->nombrePnatural;
    }

    /**
     * Set apellidoPaterno.
     *
     * @param string $apellidoPaterno
     *
     * @return RecienNacido
     */
    public function setApellidoPaterno($apellidoPaterno)
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    /**
     * Get apellidoPaterno.
     *
     * @return string
     */
    public function getApellidoPaterno()
    {
        return $this->apellidoPaterno;
    }

    /**
     * Set apellidoMaterno.
     *
     * @param string $apellidoMaterno
     *
     * @return RecienNacido
     */
    public function setApellidoMaterno($apellidoMaterno)
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    /**
     * Get apellidoMaterno.
     *
     * @return string
     */
    public function getApellidoMaterno()
    {
        return $this->apellidoMaterno;
    }

    /**
     * Set fechaNacimiento.
     *
     * @param \DateTime $fechaNacimiento
     *
     * @return RecienNacido
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento.
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set minutosApego.
     *
     * @param int $minutosApego
     *
     * @return RecienNacido
     */
    public function setMinutosApego($minutosApego)
    {
        $this->minutosApego = $minutosApego;

        return $this;
    }

    /**
     * Get minutosApego.
     *
     * @return int
     */
    public function getMinutosApego()
    {
        return $this->minutosApego;
    }

    /**
     * Set asistePadrePreParto.
     *
     * @param bool $asistePadrePreParto
     *
     * @return RecienNacido
     */
    public function setAsistePadrePreParto($asistePadrePreParto)
    {
        $this->asistePadrePreParto = $asistePadrePreParto;

        return $this;
    }

    /**
     * Get asistePadrePreParto.
     *
     * @return bool
     */
    public function getAsistePadrePreParto()
    {
        return $this->asistePadrePreParto;
    }

    /**
     * Set asistePadreParto.
     *
     * @param bool $asistePadreParto
     *
     * @return RecienNacido
     */
    public function setAsistePadreParto($asistePadreParto)
    {
        $this->asistePadreParto = $asistePadreParto;

        return $this;
    }

    /**
     * Get asistePadreParto.
     *
     * @return bool
     */
    public function getAsistePadreParto()
    {
        return $this->asistePadreParto;
    }

    /**
     * Set indicacionAnestesia.
     *
     * @param \DateTime $indicacionAnestesia
     *
     * @return RecienNacido
     */
    public function setIndicacionAnestesia($indicacionAnestesia)
    {
        $this->indicacionAnestesia = $indicacionAnestesia;

        return $this;
    }

    /**
     * Get indicacionAnestesia.
     *
     * @return \DateTime
     */
    public function getIndicacionAnestesia()
    {
        return $this->indicacionAnestesia;
    }

    /**
     * Set primeraDosisAnestesia.
     *
     * @param \DateTime $primeraDosisAnestesia
     *
     * @return RecienNacido
     */
    public function setPrimeraDosisAnestesia($primeraDosisAnestesia)
    {
        $this->primeraDosisAnestesia = $primeraDosisAnestesia;

        return $this;
    }

    /**
     * Get primeraDosisAnestesia.
     *
     * @return \DateTime
     */
    public function getPrimeraDosisAnestesia()
    {
        return $this->primeraDosisAnestesia;
    }

    /**
     * Set circunferenciaCraneanaCm.
     *
     * @param int $circunferenciaCraneanaCm
     *
     * @return RecienNacido
     */
    public function setCircunferenciaCraneanaCm($circunferenciaCraneanaCm)
    {
        $this->circunferenciaCraneanaCm = $circunferenciaCraneanaCm;

        return $this;
    }

    /**
     * Get circunferenciaCraneanaCm.
     *
     * @return int
     */
    public function getCircunferenciaCraneanaCm()
    {
        return $this->circunferenciaCraneanaCm;
    }

    /**
     * Set circunferenciaCraneanaMm.
     *
     * @param int $circunferenciaCraneanaMm
     *
     * @return RecienNacido
     */
    public function setCircunferenciaCraneanaMm($circunferenciaCraneanaMm)
    {
        $this->circunferenciaCraneanaMm = $circunferenciaCraneanaMm;

        return $this;
    }

    /**
     * Get circunferenciaCraneanaMm.
     *
     * @return int
     */
    public function getCircunferenciaCraneanaMm()
    {
        return $this->circunferenciaCraneanaMm;
    }

    /**
     * Set apgar1.
     *
     * @param int $apgar1
     *
     * @return RecienNacido
     */
    public function setApgar1($apgar1)
    {
        $this->apgar1 = $apgar1;

        return $this;
    }

    /**
     * Get apgar1.
     *
     * @return int
     */
    public function getApgar1()
    {
        return $this->apgar1;
    }

    /**
     * Set apgar2.
     *
     * @param int $apgar2
     *
     * @return RecienNacido
     */
    public function setApgar2($apgar2)
    {
        $this->apgar2 = $apgar2;

        return $this;
    }

    /**
     * Get apgar2.
     *
     * @return int
     */
    public function getApgar2()
    {
        return $this->apgar2;
    }

    /**
     * Set peso.
     *
     * @param int $peso
     *
     * @return RecienNacido
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso.
     *
     * @return int
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set tallaCm.
     *
     * @param int $tallaCm
     *
     * @return RecienNacido
     */
    public function setTallaCm($tallaCm)
    {
        $this->tallaCm = $tallaCm;

        return $this;
    }

    /**
     * Get tallaCm.
     *
     * @return int
     */
    public function getTallaCm()
    {
        return $this->tallaCm;
    }

    /**
     * Set tallaMm.
     *
     * @param int $tallaMm
     *
     * @return RecienNacido
     */
    public function setTallaMm($tallaMm)
    {
        $this->tallaMm = $tallaMm;

        return $this;
    }

    /**
     * Get tallaMm.
     *
     * @return int
     */
    public function getTallaMm()
    {
        return $this->tallaMm;
    }

    /**
     * Set semanasGestacion.
     *
     * @param int $semanasGestacion
     *
     * @return RecienNacido
     */
    public function setSemanasGestacion($semanasGestacion)
    {
        $this->semanasGestacion = $semanasGestacion;

        return $this;
    }

    /**
     * Get semanasGestacion.
     *
     * @return int
     */
    public function getSemanasGestacion()
    {
        return $this->semanasGestacion;
    }

    /**
     * Set diasGestacion.
     *
     * @param int $diasGestacion
     *
     * @return RecienNacido
     */
    public function setDiasGestacion($diasGestacion)
    {
        $this->diasGestacion = $diasGestacion;

        return $this;
    }

    /**
     * Get diasGestacion.
     *
     * @return int
     */
    public function getDiasGestacion()
    {
        return $this->diasGestacion;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return RecienNacido
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
     * Set idSexo.
     *
     * @param \Rebsol\HermesBundle\Entity\Sexo $idSexo
     *
     * @return RecienNacido
     */
    public function setIdSexo(\Rebsol\HermesBundle\Entity\Sexo $idSexo)
    {
        $this->idSexo = $idSexo;

        return $this;
    }

    /**
     * Get idSexo.
     *
     * @return \Rebsol\HermesBundle\Entity\Sexo
     */
    public function getIdSexo()
    {
        return $this->idSexo;
    }

    /**
     * Set idTipoParto.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoParto $idTipoParto
     *
     * @return RecienNacido
     */
    public function setIdTipoParto(\Rebsol\HermesBundle\Entity\TipoParto $idTipoParto)
    {
        $this->idTipoParto = $idTipoParto;

        return $this;
    }

    /**
     * Get idTipoParto.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoParto
     */
    public function getIdTipoParto()
    {
        return $this->idTipoParto;
    }

    /**
     * Set idEstadoRecienNacido.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoRecienNacido $idEstadoRecienNacido
     *
     * @return RecienNacido
     */
    public function setIdEstadoRecienNacido(\Rebsol\HermesBundle\Entity\EstadoRecienNacido $idEstadoRecienNacido)
    {
        $this->idEstadoRecienNacido = $idEstadoRecienNacido;

        return $this;
    }

    /**
     * Get idEstadoRecienNacido.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoRecienNacido
     */
    public function getIdEstadoRecienNacido()
    {
        return $this->idEstadoRecienNacido;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return RecienNacido
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
     * Set idPaciente.
     *
     * @param \Rebsol\HermesBundle\Entity\Paciente $idPaciente
     *
     * @return RecienNacido
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
     * Set idPersona.
     *
     * @param \Rebsol\HermesBundle\Entity\Persona $idPersona
     *
     * @return RecienNacido
     */
    public function setIdPersona(\Rebsol\HermesBundle\Entity\Persona $idPersona)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \Rebsol\HermesBundle\Entity\Persona
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }
}
