<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servicio
 *
 * @ORM\Table(name="servicio")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ServicioRepository")
 */
class Servicio
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
     * @ORM\Column(name="CODIGO", type="string", length=50, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ABREVIADO", type="string", length=50, nullable=true)
     */
    private $nombreAbreviado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_RECUPERACION", type="boolean", nullable=false)
     */
    private $esRecuperacion = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_HPARCIAL", type="boolean", nullable=false)
     */
    private $esHparcial = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="TIENE_BOTIQUIN", type="boolean", nullable=false)
     */
    private $tieneBotiquin = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_URGENCIA", type="boolean", nullable=false)
     */
    private $esUrgencia = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_ANAMNESIS", type="boolean", nullable=true)
     */
    private $minAnamnesis;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_DIAGNOSTICO", type="boolean", nullable=true)
     */
    private $minDiagnostico;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_EVOLUCION", type="boolean", nullable=true)
     */
    private $minEvolucion;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_SIGNOS_VITALES", type="boolean", nullable=true)
     */
    private $minSignosVitales;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_EXAMEN_FISICO", type="boolean", nullable=true)
     */
    private $minExamenFisico;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_PRESTACIONES", type="boolean", nullable=true)
     */
    private $minPrestaciones;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="MIN_OBSERVACION_CLINICA", type="boolean", nullable=true)
     */
    private $minObservacionClinica;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UNIDAD", referencedColumnName="ID")
     * })
     */
    private $idUnidad;

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
     * @var \TipoServicio
     *
     * @ORM\ManyToOne(targetEntity="TipoServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_SERVICIO", referencedColumnName="ID")
     * })
     */
    private $idTipoServicio;

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
     * Set codigo.
     *
     * @param string|null $codigo
     *
     * @return Servicio
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Servicio
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set nombreAbreviado.
     *
     * @param string|null $nombreAbreviado
     *
     * @return Servicio
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
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return Servicio
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
     * Set esRecuperacion.
     *
     * @param bool $esRecuperacion
     *
     * @return Servicio
     */
    public function setEsRecuperacion($esRecuperacion)
    {
        $this->esRecuperacion = $esRecuperacion;

        return $this;
    }

    /**
     * Get esRecuperacion.
     *
     * @return bool
     */
    public function getEsRecuperacion()
    {
        return $this->esRecuperacion;
    }

    /**
     * Set esUrgencia.
     *
     * @param bool $esUrgencia
     *
     * @return Servicio
     */
    public function setEsUrgencia($esUrgencia)
    {
        $this->esUrgencia = $esUrgencia;

        return $this;
    }

    /**
     * Get esUrgencia.
     *
     * @return bool
     */
    public function getEsUrgencia()
    {
        return $this->esUrgencia;
    }

    /**
     * Set tieneBotiquin.
     *
     * @param bool $tieneBotiquin
     *
     * @return Servicio
     */
    public function setTieneBotiquin($tieneBotiquin)
    {
        $this->tieneBotiquin = $tieneBotiquin;

        return $this;
    }

    /**
     * Get tieneBotiquin.
     *
     * @return bool
     */
    public function getTieneBotiquin()
    {
        return $this->tieneBotiquin;
    }

    /**
     * Set minAnamnesis.
     *
     * @param bool|null $minAnamnesis
     *
     * @return Servicio
     */
    public function setMinAnamnesis($minAnamnesis = null)
    {
        $this->minAnamnesis = $minAnamnesis;

        return $this;
    }

    /**
     * Get minAnamnesis.
     *
     * @return bool|null
     */
    public function getMinAnamnesis()
    {
        return $this->minAnamnesis;
    }

    /**
     * Set minDiagnostico.
     *
     * @param bool|null $minDiagnostico
     *
     * @return Servicio
     */
    public function setMinDiagnostico($minDiagnostico = null)
    {
        $this->minDiagnostico = $minDiagnostico;

        return $this;
    }

    /**
     * Get minDiagnostico.
     *
     * @return bool|null
     */
    public function getMinDiagnostico()
    {
        return $this->minDiagnostico;
    }

    /**
     * Set minEvolucion.
     *
     * @param bool|null $minEvolucion
     *
     * @return Servicio
     */
    public function setMinEvolucion($minEvolucion = null)
    {
        $this->minEvolucion = $minEvolucion;

        return $this;
    }

    /**
     * Get minEvolucion.
     *
     * @return bool|null
     */
    public function getMinEvolucion()
    {
        return $this->minEvolucion;
    }

    /**
     * Set minSignosVitales.
     *
     * @param bool|null $minSignosVitales
     *
     * @return Servicio
     */
    public function setMinSignosVitales($minSignosVitales = null)
    {
        $this->minSignosVitales = $minSignosVitales;

        return $this;
    }

    /**
     * Get minSignosVitales.
     *
     * @return bool|null
     */
    public function getMinSignosVitales()
    {
        return $this->minSignosVitales;
    }

    /**
     * Set minExamenFisico.
     *
     * @param bool|null $minExamenFisico
     *
     * @return Servicio
     */
    public function setMinExamenFisico($minExamenFisico = null)
    {
        $this->minExamenFisico = $minExamenFisico;

        return $this;
    }

    /**
     * Get minExamenFisico.
     *
     * @return bool|null
     */
    public function getMinExamenFisico()
    {
        return $this->minExamenFisico;
    }

    /**
     * Set minPrestaciones.
     *
     * @param bool|null $minPrestaciones
     *
     * @return Servicio
     */
    public function setMinPrestaciones($minPrestaciones = null)
    {
        $this->minPrestaciones = $minPrestaciones;

        return $this;
    }

    /**
     * Get minPrestaciones.
     *
     * @return bool|null
     */
    public function getMinPrestaciones()
    {
        return $this->minPrestaciones;
    }

    /**
     * Set minObservacionClinica.
     *
     * @param bool|null $minObservacionClinica
     *
     * @return Servicio
     */
    public function setMinObservacionClinica($minObservacionClinica = null)
    {
        $this->minObservacionClinica = $minObservacionClinica;

        return $this;
    }

    /**
     * Get minObservacionClinica.
     *
     * @return bool|null
     */
    public function getMinObservacionClinica()
    {
        return $this->minObservacionClinica;
    }

    /**
     * Set idUnidad.
     *
     * @param \Rebsol\HermesBundle\Entity\Unidad $idUnidad
     *
     * @return Servicio
     */
    public function setIdUnidad(\Rebsol\HermesBundle\Entity\Unidad $idUnidad)
    {
        $this->idUnidad = $idUnidad;

        return $this;
    }

    /**
     * Get idUnidad.
     *
     * @return \Rebsol\HermesBundle\Entity\Unidad
     */
    public function getIdUnidad()
    {
        return $this->idUnidad;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Servicio
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
     * Set idTipoServicio.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoServicio $idTipoServicio
     *
     * @return Servicio
     */
    public function setIdTipoServicio(\Rebsol\HermesBundle\Entity\TipoServicio $idTipoServicio)
    {
        $this->idTipoServicio = $idTipoServicio;

        return $this;
    }

    /**
     * Get idTipoServicio.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoServicio
     */
    public function getIdTipoServicio()
    {
        return $this->idTipoServicio;
    }

    /**
     * Set idRchTipoReceta.
     *
     * @param \Rebsol\HermesBundle\Entity\RchTipoReceta|null $idRchTipoReceta
     *
     * @return Servicio
     */
    public function setIdRchTipoReceta(\Rebsol\HermesBundle\Entity\RchTipoReceta $idRchTipoReceta = null)
    {
        $this->idRchTipoReceta = $idRchTipoReceta;

        return $this;
    }

    /**
     * Get idRchTipoReceta.
     *
     * @return \Rebsol\HermesBundle\Entity\RchTipoReceta|null
     */
    public function getIdRchTipoReceta()
    {
        return $this->idRchTipoReceta;
    }

    /**
     * Get esHparcial.
     *
     * @return bool
     */
    public function getEsHparcial()
    {
        return $this->esHparcial;
    }

    /**
     * @param bool $esHparcial
     */
    public function setEsHparcial($esHparcial)
    {
        $this->esHparcial = $esHparcial;
    }


}
