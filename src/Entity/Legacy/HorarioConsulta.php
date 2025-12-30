<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HorarioConsulta
 *
 * @ORM\Table(name="horario_consulta")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\HorarioConsultaRepository")
 */
class HorarioConsulta
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_INICIO_HORARIO", type="datetime", nullable=true)
     */
    private $fechaInicioHorario;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_TERMINO_HORARIO", type="datetime", nullable=true)
     */
    private $fechaTerminoHorario;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_HORARIO_FUSION", type="integer", nullable=true)
     */
    private $idHorarioFusion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_CONTROL", type="integer", nullable=true)
     */
    private $idControl;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="CONCURRENCIA", type="boolean", nullable=true)
     */
    private $concurrencia;

    /**
     * @var int|null
     *
     * @ORM\Column(name="DURACION_CONSULTA", type="integer", nullable=true)
     */
    private $duracionConsulta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ESPONTANEO", type="integer", nullable=true)
     */
    private $espontaneo;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_TELECONSULTA", type="boolean", nullable=true)
     */
    private $esTeleconsulta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOKEN_WS", type="string", length=65, nullable=true)
     */
    private $tokenWs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DATOS_SIN_REGISTRO_WS", type="text", length=0, nullable=true)
     */
    private $datosSinRegistroWs;

    /**
     * @var \SubEspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="SubEspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUBESPECIALIDAD_MEDICA", referencedColumnName="ID")
     * })
     */
    private $idSubespecialidadMedica;

    /**
     * @var \BloqueoAgenda
     *
     * @ORM\ManyToOne(targetEntity="BloqueoAgenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BLOQUEO_AGENDA", referencedColumnName="ID")
     * })
     */
    private $idBloqueoAgenda;

    /**
     * @var \Box
     *
     * @ORM\ManyToOne(targetEntity="Box")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BOX_AUXILIAR", referencedColumnName="ID")
     * })
     */
    private $idBoxAuxiliar;

    /**
     * @var \HorarioConsulta
     *
     * @ORM\ManyToOne(targetEntity="HorarioConsulta", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_HORARIO_CONSULTA_SALA", referencedColumnName="ID")
     * })
     */
    private $idHorarioConsultaSala;

    /**
     * @var \BloqueHorarioMedicoDia
     *
     * @ORM\ManyToOne(targetEntity="BloqueHorarioMedicoDia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BLOQUE_HORARIO_MEDICO_DIA", referencedColumnName="ID")
     * })
     */
    private $idBloqueHorarioMedicoDia;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;

    /**
     * @var \Box
     *
     * @ORM\ManyToOne(targetEntity="Box")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BOX", referencedColumnName="ID")
     * })
     */
    private $idBox;

    /**
     * @var \EstadoHorario
     *
     * @ORM\ManyToOne(targetEntity="EstadoHorario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_HORARIO", referencedColumnName="ID")
     * })
     */
    private $idEstadoHorario;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="ID")
     * })
     */
    private $idUsuario;

    /**
     * @var \UsuarioExterno
     *
     * @ORM\ManyToOne(targetEntity="UsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_EXTERNO_WS", referencedColumnName="ID")
     * })
     */
    private $idUsuarioExternoWs;

    /**
     * @var \EspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="EspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESPECIALIDAD_MEDICA", referencedColumnName="ID")
     * })
     */
    private $idEspecialidadMedica;

    /**
     * @var \TipoConsulta
     *
     * @ORM\ManyToOne(targetEntity="TipoConsulta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_CONSULTA", referencedColumnName="ID")
     * })
     */
    private $idTipoConsulta;



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
     * Set fechaInicioHorario.
     *
     * @param \DateTime|null $fechaInicioHorario
     *
     * @return HorarioConsulta
     */
    public function setFechaInicioHorario($fechaInicioHorario = null)
    {
        $this->fechaInicioHorario = $fechaInicioHorario;

        return $this;
    }

    /**
     * Get fechaInicioHorario.
     *
     * @return \DateTime|null
     */
    public function getFechaInicioHorario()
    {
        return $this->fechaInicioHorario;
    }

    /**
     * Set fechaTerminoHorario.
     *
     * @param \DateTime|null $fechaTerminoHorario
     *
     * @return HorarioConsulta
     */
    public function setFechaTerminoHorario($fechaTerminoHorario = null)
    {
        $this->fechaTerminoHorario = $fechaTerminoHorario;

        return $this;
    }

    /**
     * Get fechaTerminoHorario.
     *
     * @return \DateTime|null
     */
    public function getFechaTerminoHorario()
    {
        return $this->fechaTerminoHorario;
    }

    /**
     * Set idHorarioFusion.
     *
     * @param int|null $idHorarioFusion
     *
     * @return HorarioConsulta
     */
    public function setIdHorarioFusion($idHorarioFusion = null)
    {
        $this->idHorarioFusion = $idHorarioFusion;

        return $this;
    }

    /**
     * Get idHorarioFusion.
     *
     * @return int|null
     */
    public function getIdHorarioFusion()
    {
        return $this->idHorarioFusion;
    }

    /**
     * Set idControl.
     *
     * @param int|null $idControl
     *
     * @return HorarioConsulta
     */
    public function setIdControl($idControl = null)
    {
        $this->idControl = $idControl;

        return $this;
    }

    /**
     * Get idControl.
     *
     * @return int|null
     */
    public function getIdControl()
    {
        return $this->idControl;
    }

    /**
     * Set concurrencia.
     *
     * @param bool|null $concurrencia
     *
     * @return HorarioConsulta
     */
    public function setConcurrencia($concurrencia = null)
    {
        $this->concurrencia = $concurrencia;

        return $this;
    }

    /**
     * Get concurrencia.
     *
     * @return bool|null
     */
    public function getConcurrencia()
    {
        return $this->concurrencia;
    }

    /**
     * Set duracionConsulta.
     *
     * @param int|null $duracionConsulta
     *
     * @return HorarioConsulta
     */
    public function setDuracionConsulta($duracionConsulta = null)
    {
        $this->duracionConsulta = $duracionConsulta;

        return $this;
    }

    /**
     * Get duracionConsulta.
     *
     * @return int|null
     */
    public function getDuracionConsulta()
    {
        return $this->duracionConsulta;
    }

    /**
     * Set espontaneo.
     *
     * @param int|null $espontaneo
     *
     * @return HorarioConsulta
     */
    public function setEspontaneo($espontaneo = null)
    {
        $this->espontaneo = $espontaneo;

        return $this;
    }

    /**
     * Get espontaneo.
     *
     * @return int|null
     */
    public function getEspontaneo()
    {
        return $this->espontaneo;
    }

    /**
     * Set esTeleconsulta.
     *
     * @param bool|null $esTeleconsulta
     *
     * @return HorarioConsulta
     */
    public function setEsTeleconsulta($esTeleconsulta = null)
    {
        $this->esTeleconsulta = $esTeleconsulta;

        return $this;
    }

    /**
     * Get esTeleconsulta.
     *
     * @return bool|null
     */
    public function getEsTeleconsulta()
    {
        return $this->esTeleconsulta;
    }

    /**
     * Set tokenWs.
     *
     * @param string|null $tokenWs
     *
     * @return HorarioConsulta
     */
    public function setTokenWs($tokenWs = null)
    {
        $this->tokenWs = $tokenWs;

        return $this;
    }

    /**
     * Get tokenWs.
     *
     * @return string|null
     */
    public function getTokenWs()
    {
        return $this->tokenWs;
    }

    /**
     * Set datosSinRegistroWs.
     *
     * @param string|null $datosSinRegistroWs
     *
     * @return HorarioConsulta
     */
    public function setDatosSinRegistroWs($datosSinRegistroWs = null)
    {
        $this->datosSinRegistroWs = $datosSinRegistroWs;

        return $this;
    }

    /**
     * Get datosSinRegistroWs.
     *
     * @return string|null
     */
    public function getDatosSinRegistroWs()
    {
        return $this->datosSinRegistroWs;
    }

    /**
     * Set idEstadoHorario.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoHorario|null $idEstadoHorario
     *
     * @return HorarioConsulta
     */
    public function setIdEstadoHorario(\Rebsol\HermesBundle\Entity\EstadoHorario $idEstadoHorario = null)
    {
        $this->idEstadoHorario = $idEstadoHorario;

        return $this;
    }

    /**
     * Get idEstadoHorario.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoHorario|null
     */
    public function getIdEstadoHorario()
    {
        return $this->idEstadoHorario;
    }

    /**
     * Set idBoxAuxiliar.
     *
     * @param \Rebsol\HermesBundle\Entity\Box|null $idBoxAuxiliar
     *
     * @return HorarioConsulta
     */
    public function setIdBoxAuxiliar(\Rebsol\HermesBundle\Entity\Box $idBoxAuxiliar = null)
    {
        $this->idBoxAuxiliar = $idBoxAuxiliar;

        return $this;
    }

    /**
     * Get idBoxAuxiliar.
     *
     * @return \Rebsol\HermesBundle\Entity\Box|null
     */
    public function getIdBoxAuxiliar()
    {
        return $this->idBoxAuxiliar;
    }

    /**
     * Set idBox.
     *
     * @param \Rebsol\HermesBundle\Entity\Box|null $idBox
     *
     * @return HorarioConsulta
     */
    public function setIdBox(\Rebsol\HermesBundle\Entity\Box $idBox = null)
    {
        $this->idBox = $idBox;

        return $this;
    }

    /**
     * Get idBox.
     *
     * @return \Rebsol\HermesBundle\Entity\Box|null
     */
    public function getIdBox()
    {
        return $this->idBox;
    }

    /**
     * Set idEspecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica|null $idEspecialidadMedica
     *
     * @return HorarioConsulta
     */
    public function setIdEspecialidadMedica(\Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidadMedica = null)
    {
        $this->idEspecialidadMedica = $idEspecialidadMedica;

        return $this;
    }

    /**
     * Get idEspecialidadMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\EspecialidadMedica|null
     */
    public function getIdEspecialidadMedica()
    {
        return $this->idEspecialidadMedica;
    }

    /**
     * Set idSubespecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEspecialidadMedica|null $idSubespecialidadMedica
     *
     * @return HorarioConsulta
     */
    public function setIdSubespecialidadMedica(\Rebsol\HermesBundle\Entity\SubEspecialidadMedica $idSubespecialidadMedica = null)
    {
        $this->idSubespecialidadMedica = $idSubespecialidadMedica;

        return $this;
    }

    /**
     * Get idSubespecialidadMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEspecialidadMedica|null
     */
    public function getIdSubespecialidadMedica()
    {
        return $this->idSubespecialidadMedica;
    }

    /**
     * Set idTipoConsulta.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoConsulta|null $idTipoConsulta
     *
     * @return HorarioConsulta
     */
    public function setIdTipoConsulta(\Rebsol\HermesBundle\Entity\TipoConsulta $idTipoConsulta = null)
    {
        $this->idTipoConsulta = $idTipoConsulta;

        return $this;
    }

    /**
     * Get idTipoConsulta.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoConsulta|null
     */
    public function getIdTipoConsulta()
    {
        return $this->idTipoConsulta;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal|null $idSucursal
     *
     * @return HorarioConsulta
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal = null)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal|null
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idBloqueoAgenda.
     *
     * @param \Rebsol\HermesBundle\Entity\BloqueoAgenda|null $idBloqueoAgenda
     *
     * @return HorarioConsulta
     */
    public function setIdBloqueoAgenda(\Rebsol\HermesBundle\Entity\BloqueoAgenda $idBloqueoAgenda = null)
    {
        $this->idBloqueoAgenda = $idBloqueoAgenda;

        return $this;
    }

    /**
     * Get idBloqueoAgenda.
     *
     * @return \Rebsol\HermesBundle\Entity\BloqueoAgenda|null
     */
    public function getIdBloqueoAgenda()
    {
        return $this->idBloqueoAgenda;
    }

    /**
     * Set idBloqueHorarioMedicoDia.
     *
     * @param \Rebsol\HermesBundle\Entity\BloqueHorarioMedicoDia|null $idBloqueHorarioMedicoDia
     *
     * @return HorarioConsulta
     */
    public function setIdBloqueHorarioMedicoDia(\Rebsol\HermesBundle\Entity\BloqueHorarioMedicoDia $idBloqueHorarioMedicoDia = null)
    {
        $this->idBloqueHorarioMedicoDia = $idBloqueHorarioMedicoDia;

        return $this;
    }

    /**
     * Get idBloqueHorarioMedicoDia.
     *
     * @return \Rebsol\HermesBundle\Entity\BloqueHorarioMedicoDia|null
     */
    public function getIdBloqueHorarioMedicoDia()
    {
        return $this->idBloqueHorarioMedicoDia;
    }

    /**
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuario
     *
     * @return HorarioConsulta
     */
    public function setIdUsuario(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idHorarioConsultaSala.
     *
     * @param \Rebsol\HermesBundle\Entity\HorarioConsulta|null $idHorarioConsultaSala
     *
     * @return HorarioConsulta
     */
    public function setIdHorarioConsultaSala(\Rebsol\HermesBundle\Entity\HorarioConsulta $idHorarioConsultaSala = null)
    {
        $this->idHorarioConsultaSala = $idHorarioConsultaSala;

        return $this;
    }

    /**
     * Get idHorarioConsultaSala.
     *
     * @return \Rebsol\HermesBundle\Entity\HorarioConsulta|null
     */
    public function getIdHorarioConsultaSala()
    {
        return $this->idHorarioConsultaSala;
    }

    /**
     * Set idUsuarioExternoWs.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuarioExterno|null $idUsuarioExternoWs
     *
     * @return HorarioConsulta
     */
    public function setIdUsuarioExternoWs(\Rebsol\HermesBundle\Entity\UsuarioExterno $idUsuarioExternoWs = null)
    {
        $this->idUsuarioExternoWs = $idUsuarioExternoWs;

        return $this;
    }

    /**
     * Get idUsuarioExternoWs.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuarioExterno|null
     */
    public function getIdUsuarioExternoWs()
    {
        return $this->idUsuarioExternoWs;
    }
}
