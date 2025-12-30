<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pnatural
 *
 * @ORM\Table(name="pnatural", indexes={@ORM\Index(name="IDX_PNATURAL_NOMBREPNATURAL", columns={"NOMBRE_PNATURAL"}), @ORM\Index(name="IDX_PNATURAL_APELLIDOPATERNO", columns={"APELLIDO_PATERNO"}), @ORM\Index(name="IDX_PNATURAL_APELLIDOMATERNO", columns={"APELLIDO_MATERNO"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PnaturalRepository")
 */
class Pnatural
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="NUMERO_HERMANO_GEMELO", type="integer", nullable=false)
     */
    private $numeroHermanoGemelo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_PNATURAL", type="string", length=60, nullable=false)
     */
    private $nombrePnatural;

    /**
     * @var string|null
     *
     * @ORM\Column(name="APELLIDO_PATERNO", type="string", length=45, nullable=true)
     */
    private $apellidoPaterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="APELLIDO_MATERNO", type="string", length=45, nullable=true)
     */
    private $apellidoMaterno;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_NACIMIENTO", type="datetime", nullable=true)
     */
    private $fechaNacimiento;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_DEFUNCION", type="datetime", nullable=true)
     */
    private $fechaDefuncion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION_NO_MEDICA", type="text", length=0, nullable=true)
     */
    private $observacionNoMedica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RUTA_FOTO_PNATURAL", type="string", length=255, nullable=true)
     */
    private $rutaFotoPnatural;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="VISUALIZACION_FICHA", type="boolean", nullable=true)
     */
    private $visualizacionFicha;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_VISUALIZACION_FICHA", type="datetime", nullable=true)
     */
    private $fechaVisualizacionFicha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="KCC", type="string", length=255, nullable=true)
     */
    private $kcc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COLOR", type="string", length=150, nullable=true)
     */
    private $color;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CHIP", type="string", length=255, nullable=true)
     */
    private $chip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PESO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $peso;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_SOCIAL", type="string", length=50, nullable=true)
     */
    private $nombreSocial;

    /**
     * @var \DetalleNivelInstruccion
     *
     * @ORM\ManyToOne(targetEntity="DetalleNivelInstruccion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_DETALLE_NIVEL_INSTRUCCION", referencedColumnName="ID")
     * })
     */
    private $idDetalleNivelInstruccion;

    /**
     * @var \EstadoConyugal
     *
     * @ORM\ManyToOne(targetEntity="EstadoConyugal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_CONYUGAL", referencedColumnName="ID")
     * })
     */
    private $idEstadoConyugal;

    /**
     * @var \Raza
     *
     * @ORM\ManyToOne(targetEntity="Raza")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RAZA", referencedColumnName="ID")
     * })
     */
    private $idRaza;

    /**
     * @var \Religion
     *
     * @ORM\ManyToOne(targetEntity="Religion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_RELIGION", referencedColumnName="ID")
     * })
     */
    private $idReligion;

    /**
     * @var \Ocupacion
     *
     * @ORM\ManyToOne(targetEntity="Ocupacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_OCUPACION", referencedColumnName="ID")
     * })
     */
    private $idOcupacion;

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
     * @var \Pais
     *
     * @ORM\ManyToOne(targetEntity="Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_NACIONALIDAD", referencedColumnName="ID")
     * })
     */
    private $idNacionalidad;

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
     * @var \TipoPnatural
     *
     * @ORM\ManyToOne(targetEntity="TipoPnatural")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PNATURAL", referencedColumnName="ID")
     * })
     */
    private $idTipoPnatural;

    /**
     * @var \PuebloOriginario
     *
     * @ORM\ManyToOne(targetEntity="PuebloOriginario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PUEBLO_ORIGINARIO", referencedColumnName="ID")
     * })
     */
    private $idPuebloOriginario;

    /**
     * @var \EstadoReproductivo
     *
     * @ORM\ManyToOne(targetEntity="EstadoReproductivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_REPRODUCTIVO", referencedColumnName="ID")
     * })
     */
    private $idEstadoReproductivo;

    /**
     * @Assert\Image(maxSize = "10000k")
     */
    protected $foto;

    /**
     * @var int
     *
     * @ORM\Column(name="CANTIDAD_HIJO", type="integer", nullable=true)
     */
    private $cantidadHijo;


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
     * Set numeroHermanoGemelo.
     *
     * @param int $numeroHermanoGemelo
     *
     * @return Pnatural
     */
    public function setNumeroHermanoGemelo($numeroHermanoGemelo)
    {
        $this->numeroHermanoGemelo = $numeroHermanoGemelo;

        return $this;
    }

    /**
     * Get numeroHermanoGemelo.
     *
     * @return int
     */
    public function getNumeroHermanoGemelo()
    {
        return $this->numeroHermanoGemelo;
    }

    /**
     * Set nombrePnatural.
     *
     * @param string $nombrePnatural
     *
     * @return Pnatural
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
     * @param string|null $apellidoPaterno
     *
     * @return Pnatural
     */
    public function setApellidoPaterno($apellidoPaterno = null)
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    /**
     * Get apellidoPaterno.
     *
     * @return string|null
     */
    public function getApellidoPaterno()
    {
        return $this->apellidoPaterno;
    }

    /**
     * Set apellidoMaterno.
     *
     * @param string|null $apellidoMaterno
     *
     * @return Pnatural
     */
    public function setApellidoMaterno($apellidoMaterno = null)
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    /**
     * Get apellidoMaterno.
     *
     * @return string|null
     */
    public function getApellidoMaterno()
    {
        return $this->apellidoMaterno;
    }

    /**
     * Set fechaNacimiento.
     *
     * @param \DateTime|null $fechaNacimiento
     *
     * @return Pnatural
     */
    public function setFechaNacimiento($fechaNacimiento = null)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento.
     *
     * @return \DateTime|null
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set fechaDefuncion.
     *
     * @param \DateTime|null $fechaDefuncion
     *
     * @return Pnatural
     */
    public function setFechaDefuncion($fechaDefuncion = null)
    {
        $this->fechaDefuncion = $fechaDefuncion;

        return $this;
    }

    /**
     * Get fechaDefuncion.
     *
     * @return \DateTime|null
     */
    public function getFechaDefuncion()
    {
        return $this->fechaDefuncion;
    }

    /**
     * Set observacionNoMedica.
     *
     * @param string|null $observacionNoMedica
     *
     * @return Pnatural
     */
    public function setObservacionNoMedica($observacionNoMedica = null)
    {
        $this->observacionNoMedica = $observacionNoMedica;

        return $this;
    }

    /**
     * Get observacionNoMedica.
     *
     * @return string|null
     */
    public function getObservacionNoMedica()
    {
        return $this->observacionNoMedica;
    }

    /**
     * Set rutaFotoPnatural.
     *
     * @param string|null $rutaFotoPnatural
     *
     * @return Pnatural
     */
    public function setRutaFotoPnatural($rutaFotoPnatural = null)
    {
        $this->rutaFotoPnatural = $rutaFotoPnatural;

        return $this;
    }

    /**
     * Get rutaFotoPnatural.
     *
     * @return string|null
     */
    public function getRutaFotoPnatural()
    {
        return $this->rutaFotoPnatural;
    }

    /**
     * Set visualizacionFicha.
     *
     * @param bool|null $visualizacionFicha
     *
     * @return Pnatural
     */
    public function setVisualizacionFicha($visualizacionFicha = null)
    {
        $this->visualizacionFicha = $visualizacionFicha;

        return $this;
    }

    /**
     * Get visualizacionFicha.
     *
     * @return bool|null
     */
    public function getVisualizacionFicha()
    {
        return $this->visualizacionFicha;
    }

    /**
     * Set fechaVisualizacionFicha.
     *
     * @param \DateTime|null $fechaVisualizacionFicha
     *
     * @return Pnatural
     */
    public function setFechaVisualizacionFicha($fechaVisualizacionFicha = null)
    {
        $this->fechaVisualizacionFicha = $fechaVisualizacionFicha;

        return $this;
    }

    /**
     * Get fechaVisualizacionFicha.
     *
     * @return \DateTime|null
     */
    public function getFechaVisualizacionFicha()
    {
        return $this->fechaVisualizacionFicha;
    }

    /**
     * Set kcc.
     *
     * @param string|null $kcc
     *
     * @return Pnatural
     */
    public function setKcc($kcc = null)
    {
        $this->kcc = $kcc;

        return $this;
    }

    /**
     * Get kcc.
     *
     * @return string|null
     */
    public function getKcc()
    {
        return $this->kcc;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     *
     * @return Pnatural
     */
    public function setColor($color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set chip.
     *
     * @param string|null $chip
     *
     * @return Pnatural
     */
    public function setChip($chip = null)
    {
        $this->chip = $chip;

        return $this;
    }

    /**
     * Get chip.
     *
     * @return string|null
     */
    public function getChip()
    {
        return $this->chip;
    }

    /**
     * Set peso.
     *
     * @param string|null $peso
     *
     * @return Pnatural
     */
    public function setPeso($peso = null)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso.
     *
     * @return string|null
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set nombreSocial.
     *
     * @param string|null $nombreSocial
     *
     * @return Pnatural
     */
    public function setNombreSocial($nombreSocial = null)
    {
        $this->nombreSocial = $nombreSocial;

        return $this;
    }

    /**
     * Get nombreSocial.
     *
     * @return string|null
     */
    public function getNombreSocial()
    {
        return $this->nombreSocial;
    }

    /**
     * Set idTipoPnatural.
     *
     * @param \App\Entity\TipoPnatural|null $idTipoPnatural
     *
     * @return Pnatural
     */
    public function setIdTipoPnatural(\App\Entity\TipoPnatural $idTipoPnatural = null)
    {
        $this->idTipoPnatural = $idTipoPnatural;

        return $this;
    }

    /**
     * Get idTipoPnatural.
     *
     * @return \App\Entity\TipoPnatural|null
     */
    public function getIdTipoPnatural()
    {
        return $this->idTipoPnatural;
    }

    /**
     * Set idDetalleNivelInstruccion.
     *
     * @param \App\Entity\DetalleNivelInstruccion|null $idDetalleNivelInstruccion
     *
     * @return Pnatural
     */
    public function setIdDetalleNivelInstruccion(\App\Entity\DetalleNivelInstruccion $idDetalleNivelInstruccion = null)
    {
        $this->idDetalleNivelInstruccion = $idDetalleNivelInstruccion;

        return $this;
    }

    /**
     * Get idDetalleNivelInstruccion.
     *
     * @return \App\Entity\DetalleNivelInstruccion|null
     */
    public function getIdDetalleNivelInstruccion()
    {
        return $this->idDetalleNivelInstruccion;
    }

    /**
     * Set idEstadoConyugal.
     *
     * @param \App\Entity\EstadoConyugal|null $idEstadoConyugal
     *
     * @return Pnatural
     */
    public function setIdEstadoConyugal(\App\Entity\EstadoConyugal $idEstadoConyugal = null)
    {
        $this->idEstadoConyugal = $idEstadoConyugal;

        return $this;
    }

    /**
     * Get idEstadoConyugal.
     *
     * @return \App\Entity\EstadoConyugal|null
     */
    public function getIdEstadoConyugal()
    {
        return $this->idEstadoConyugal;
    }

    /**
     * Set idOcupacion.
     *
     * @param \App\Entity\Ocupacion|null $idOcupacion
     *
     * @return Pnatural
     */
    public function setIdOcupacion(\App\Entity\Ocupacion $idOcupacion = null)
    {
        $this->idOcupacion = $idOcupacion;

        return $this;
    }

    /**
     * Get idOcupacion.
     *
     * @return \App\Entity\Ocupacion|null
     */
    public function getIdOcupacion()
    {
        return $this->idOcupacion;
    }

    /**
     * Set idPuebloOriginario.
     *
     * @param \App\Entity\PuebloOriginario|null $idPuebloOriginario
     *
     * @return Pnatural
     */
    public function setIdPuebloOriginario(\App\Entity\PuebloOriginario $idPuebloOriginario = null)
    {
        $this->idPuebloOriginario = $idPuebloOriginario;

        return $this;
    }

    /**
     * Get idPuebloOriginario.
     *
     * @return \App\Entity\PuebloOriginario|null
     */
    public function getIdPuebloOriginario()
    {
        return $this->idPuebloOriginario;
    }

    /**
     * Set idReligion.
     *
     * @param \App\Entity\Religion|null $idReligion
     *
     * @return Pnatural
     */
    public function setIdReligion(\App\Entity\Religion $idReligion = null)
    {
        $this->idReligion = $idReligion;

        return $this;
    }

    /**
     * Get idReligion.
     *
     * @return \App\Entity\Religion|null
     */
    public function getIdReligion()
    {
        return $this->idReligion;
    }

    /**
     * Set idSexo.
     *
     * @param \App\Entity\Sexo|null $idSexo
     *
     * @return Pnatural
     */
    public function setIdSexo(\App\Entity\Sexo $idSexo = null)
    {
        $this->idSexo = $idSexo;

        return $this;
    }

    /**
     * Get idSexo.
     *
     * @return \App\Entity\Sexo|null
     */
    public function getIdSexo()
    {
        return $this->idSexo;
    }

    /**
     * Set idNacionalidad.
     *
     * @param \App\Entity\Pais|null $idNacionalidad
     *
     * @return Pnatural
     */
    public function setIdNacionalidad(\App\Entity\Pais $idNacionalidad = null)
    {
        $this->idNacionalidad = $idNacionalidad;

        return $this;
    }

    /**
     * Get idNacionalidad.
     *
     * @return \App\Entity\Pais|null
     */
    public function getIdNacionalidad()
    {
        return $this->idNacionalidad;
    }

    /**
     * Set idRaza.
     *
     * @param \App\Entity\Raza|null $idRaza
     *
     * @return Pnatural
     */
    public function setIdRaza(\App\Entity\Raza $idRaza = null)
    {
        $this->idRaza = $idRaza;

        return $this;
    }

    /**
     * Get idRaza.
     *
     * @return \App\Entity\Raza|null
     */
    public function getIdRaza()
    {
        return $this->idRaza;
    }

    /**
     * Set idEstadoReproductivo.
     *
     * @param \App\Entity\EstadoReproductivo|null $idEstadoReproductivo
     *
     * @return Pnatural
     */
    public function setIdEstadoReproductivo(\App\Entity\EstadoReproductivo $idEstadoReproductivo = null)
    {
        $this->idEstadoReproductivo = $idEstadoReproductivo;

        return $this;
    }

    /**
     * Get idEstadoReproductivo.
     *
     * @return \App\Entity\EstadoReproductivo|null
     */
    public function getIdEstadoReproductivo()
    {
        return $this->idEstadoReproductivo;
    }

    /**
     * Set idPersona.
     *
     * @param \App\Entity\Legacy\Persona $idPersona
     *
     * @return Pnatural
     */
    public function setIdPersona(\App\Entity\Legacy\Persona $idPersona)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \App\Entity\Legacy\Persona
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }

    /**
    * @param UploadedFile $foto
    */
    public function setFoto(UploadedFile $foto = null)
    {
        $this->foto = $foto;
    }

    /**
    * @return UploadedFile
    */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * @return int
     */
    public function getCantidadHijo()
    {
        return $this->cantidadHijo;
    }

    /**
     * @param int $cantidadHijo
     */
    public function setCantidadHijo($cantidadHijo)
    {
        $this->cantidadHijo = $cantidadHijo;
    }

    public function subirFoto($directorioDestino, $idPnatural, $oPnatural)
    {
        if (null === $this->getFoto()) {
            return;
        }

        $nombreArchivoFoto = uniqid('pnatural_'.$idPnatural.'_').'.'.$this->getFoto()->guessExtension();
        $this->getFoto()->move($directorioDestino, $nombreArchivoFoto);
        $this->setRutaFotoPnatural($nombreArchivoFoto);
    }

    function getNombreCompleto()
    {
        $strNombre          = ucwords(mb_strtolower($this->nombrePnatural,"UTF-8"));
        $strApellidoPaterno = ucwords(mb_strtolower($this->apellidoPaterno,"UTF-8"));
        return sprintf('%s %s', $strNombre,$strApellidoPaterno);
    }


    /**
	 * OTROS
	 */

	/**
	 * 1 : SEBASTIAN ANDRÉS THOMSON HENRÍQUEZ
	 * 2 : THOMSON HENRIQUEZ, SEBASTIAN ANDRÉS
	 * 3 : SEBASTIAN ANDRÉS THOMSON
	 * 4 : THOMSON, SEBASTIAN ANDRÉS
	 * 5 : S. THOMSON
	 * 6 : THOMSON S.
	 * 7 : SEBASTIAN THOMSON HENRÍQUEZ
	 * 8 : THOMSON HENRIQUEZ, SEBASTIAN
	 * 9 : SEBASTIAN THOMSON
	 * 10: THOMSON, SEBASTIAN
	 */
	public function getNombrePorOrden($iOrden = 1) {
		$strNombre       = $this->nombrePnatural;
		$strPrimerNombre = current(explode(" ",$this->nombrePnatural));
		$strApePat       = $this->apellidoPaterno;
		$strApeMat       = $this->apellidoMaterno;

		if ($iOrden == 1) {
			$strReturn = $strNombre.' '.$strApePat.' '.$strApeMat;
		} elseif ($iOrden == 2) {
			$strReturn = $strApePat.' '.$strApeMat.' '.$strNombre;
		} elseif ($iOrden == 3) {
			$strReturn = $strNombre.' '.$strApePat;
		} elseif ($iOrden == 4) {
			$strReturn = $strApePat.', '.$strNombre;
		} elseif ($iOrden == 5) {
			$strReturn = $strNombre[0].'. '.$strApePat;
		} elseif ($iOrden == 6) {
			$strReturn = $strApePat.' '.$strNombre[0].'.';
		} elseif ($iOrden == 7) {
			$strReturn = $strPrimerNombre.' '.$strApePat.' '.$strApeMat;
		} elseif ($iOrden == 8) {
			$strReturn = $strApePat.' '.$strApeMat.', '.$strPrimerNombre;
		} elseif ($iOrden == 9) {
			$strReturn = $strPrimerNombre.' '.$strApePat;
		} elseif ($iOrden == 10) {
			$strReturn = $strApePat.', '.$strPrimerNombre;
		} else {
			$strReturn = $strNombre.' '.$strApePat.' '.$strApeMat;
		}
		return $strReturn;
	}

    function devuelve_nombre_persona($link, $rut, $nhg, $orden) {
		$persona2 = strtoupper($apellido_paterno)." ".strtoupper($apellido_materno).", ".strtoupper($nombres);
		/*un solo nombre*/
		$persona3 = strtoupper($arr_nombres[0])." ".strtoupper($apellido_paterno)." ".strtoupper($apellido_materno);
	}

    /**
     * @ORM\PrePersist
     */
    public function trigerTildesPrePersist() {
        $this->nombrePnatural = $this->limpiarCampos($this->nombrePnatural);
        $this->apellidoPaterno = $this->limpiarCampos($this->apellidoPaterno);
        $this->apellidoMaterno = $this->limpiarCampos($this->apellidoMaterno);
    }

    /**
     * @ORM\PreUpdate
     */
    public function trigerTildesPreUpdate() {
        $this->nombrePnatural = $this->limpiarCampos($this->nombrePnatural);
        $this->apellidoPaterno = $this->limpiarCampos($this->apellidoPaterno);
        $this->apellidoMaterno = $this->limpiarCampos($this->apellidoMaterno);
    }


    public function limpiarCampos($campo) {
		// Quitar las tildes del campo
		$campo = $this->quitarTildes($campo);

		//Transformar el campo en mayúsculas
		$campo = mb_strtoupper($campo);

		return $campo;
	}

    private function quitarTildes($string) {
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
            $string
            );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('E', 'E', 'E', 'E', 'E', 'E', 'E', 'E'),
            $string
            );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('I', 'I', 'I', 'I', 'I', 'I', 'I', 'I'),
            $string
            );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('O', 'O', 'O', 'O', 'O', 'O', 'O', 'O'),
            $string
            );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('U', 'U', 'U', 'U', 'U', 'U', 'U', 'U'),
            $string
            );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('Ñ', 'Ñ', 'c', 'C',),
            $string
            );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                "."),
            '',
            $string
            );
        return $string;
    }

}
