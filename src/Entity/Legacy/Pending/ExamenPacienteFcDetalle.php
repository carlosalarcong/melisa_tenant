<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExamenPacienteFcDetalle
 *
 * @ORM\Table(name="examen_paciente_fc_detalle")
 * @ORM\Entity
 */
class ExamenPacienteFcDetalle
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
     * @ORM\Column(name="CANTIDAD", type="integer", nullable=false)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXTO_ACCION_CLINICA", type="text", length=0, nullable=false)
     */
    private $textoAccionClinica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ARCHIVO", type="string", length=255, nullable=true)
     */
    private $nombreArchivo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TEXTO_EXAMEN", type="string", length=255, nullable=true)
     */
    private $textoExamen;

    /**
     * @var \EstadoPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_PAGO", referencedColumnName="ID")
     * })
     */
    private $idEstadoPago;

    /**
     * @var \ExamenPacienteFc
     *
     * @ORM\ManyToOne(targetEntity="ExamenPacienteFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EXAMEN_PACIENTE_FC", referencedColumnName="ID")
     * })
     */
    private $idExamenPacienteFc;

    /**
     * @var \ExamenPrestacion
     *
     * @ORM\ManyToOne(targetEntity="ExamenPrestacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EXAMEN_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idExamenPrestacion;

    /**
     * @var \AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idAccionClinica;

    /**
     * @var \LugarCuerpo
     *
     * @ORM\ManyToOne(targetEntity="LugarCuerpo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_LUGAR_CUERPO", referencedColumnName="ID")
     * })
     */
    private $idLugarCuerpo;

    /**
     * @var \UbicacionCuerpo
     *
     * @ORM\ManyToOne(targetEntity="UbicacionCuerpo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UBICACION_CUERPO", referencedColumnName="ID")
     * })
     */
    private $idUbicacionCuerpo;

    /**
     * @var \PaquetePrestacion
     *
     * @ORM\ManyToOne(targetEntity="PaquetePrestacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idPaquetePrestacion;

    /**
     * @var \PaqueteExamen
     *
     * @ORM\ManyToOne(targetEntity="PaqueteExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_EXAMEN", referencedColumnName="ID")
     * })
     */
    private $idPaqueteExamen;



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
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set textoAccionClinica.
     *
     * @param string $textoAccionClinica
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setTextoAccionClinica($textoAccionClinica)
    {
        $this->textoAccionClinica = $textoAccionClinica;

        return $this;
    }

    /**
     * Get textoAccionClinica.
     *
     * @return string
     */
    public function getTextoAccionClinica()
    {
        return $this->textoAccionClinica;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return ExamenPacienteFcDetalle
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
     * Set nombreArchivo.
     *
     * @param string|null $nombreArchivo
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setNombreArchivo($nombreArchivo = null)
    {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    /**
     * Get nombreArchivo.
     *
     * @return string|null
     */
    public function getNombreArchivo()
    {
        return $this->nombreArchivo;
    }

    /**
     * Set textoExamen.
     *
     * @param string|null $textoExamen
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setTextoExamen($textoExamen = null)
    {
        $this->textoExamen = $textoExamen;

        return $this;
    }

    /**
     * Get textoExamen.
     *
     * @return string|null
     */
    public function getTextoExamen()
    {
        return $this->textoExamen;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \Rebsol\HermesBundle\Entity\AccionClinica|null $idAccionClinica
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdAccionClinica(\Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica = null)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \Rebsol\HermesBundle\Entity\AccionClinica|null
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * Set idEstadoPago.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoPago|null $idEstadoPago
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdEstadoPago(\Rebsol\HermesBundle\Entity\EstadoPago $idEstadoPago = null)
    {
        $this->idEstadoPago = $idEstadoPago;

        return $this;
    }

    /**
     * Get idEstadoPago.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoPago|null
     */
    public function getIdEstadoPago()
    {
        return $this->idEstadoPago;
    }

    /**
     * Set idExamenPacienteFc.
     *
     * @param \Rebsol\HermesBundle\Entity\ExamenPacienteFc|null $idExamenPacienteFc
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdExamenPacienteFc(\Rebsol\HermesBundle\Entity\ExamenPacienteFc $idExamenPacienteFc = null)
    {
        $this->idExamenPacienteFc = $idExamenPacienteFc;

        return $this;
    }

    /**
     * Get idExamenPacienteFc.
     *
     * @return \Rebsol\HermesBundle\Entity\ExamenPacienteFc|null
     */
    public function getIdExamenPacienteFc()
    {
        return $this->idExamenPacienteFc;
    }

    /**
     * Set idLugarCuerpo.
     *
     * @param \Rebsol\HermesBundle\Entity\LugarCuerpo|null $idLugarCuerpo
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdLugarCuerpo(\Rebsol\HermesBundle\Entity\LugarCuerpo $idLugarCuerpo = null)
    {
        $this->idLugarCuerpo = $idLugarCuerpo;

        return $this;
    }

    /**
     * Get idLugarCuerpo.
     *
     * @return \Rebsol\HermesBundle\Entity\LugarCuerpo|null
     */
    public function getIdLugarCuerpo()
    {
        return $this->idLugarCuerpo;
    }

    /**
     * Set idUbicacionCuerpo.
     *
     * @param \Rebsol\HermesBundle\Entity\UbicacionCuerpo|null $idUbicacionCuerpo
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdUbicacionCuerpo(\Rebsol\HermesBundle\Entity\UbicacionCuerpo $idUbicacionCuerpo = null)
    {
        $this->idUbicacionCuerpo = $idUbicacionCuerpo;

        return $this;
    }

    /**
     * Get idUbicacionCuerpo.
     *
     * @return \Rebsol\HermesBundle\Entity\UbicacionCuerpo|null
     */
    public function getIdUbicacionCuerpo()
    {
        return $this->idUbicacionCuerpo;
    }

    /**
     * Set idExamenPrestacion.
     *
     * @param \Rebsol\HermesBundle\Entity\ExamenPrestacion|null $idExamenPrestacion
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdExamenPrestacion(\Rebsol\HermesBundle\Entity\ExamenPrestacion $idExamenPrestacion = null)
    {
        $this->idExamenPrestacion = $idExamenPrestacion;

        return $this;
    }

    /**
     * Get idExamenPrestacion.
     *
     * @return \Rebsol\HermesBundle\Entity\ExamenPrestacion|null
     */
    public function getIdExamenPrestacion()
    {
        return $this->idExamenPrestacion;
    }

    /**
     * Set idPaqueteExamen.
     *
     * @param \Rebsol\HermesBundle\Entity\PaqueteExamen|null $idPaqueteExamen
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdPaqueteExamen(\Rebsol\HermesBundle\Entity\PaqueteExamen $idPaqueteExamen = null)
    {
        $this->idPaqueteExamen = $idPaqueteExamen;

        return $this;
    }

    /**
     * Get idPaqueteExamen.
     *
     * @return \Rebsol\HermesBundle\Entity\PaqueteExamen|null
     */
    public function getIdPaqueteExamen()
    {
        return $this->idPaqueteExamen;
    }

    /**
     * Set idPaquetePrestacion.
     *
     * @param \Rebsol\HermesBundle\Entity\PaquetePrestacion|null $idPaquetePrestacion
     *
     * @return ExamenPacienteFcDetalle
     */
    public function setIdPaquetePrestacion(\Rebsol\HermesBundle\Entity\PaquetePrestacion $idPaquetePrestacion = null)
    {
        $this->idPaquetePrestacion = $idPaquetePrestacion;

        return $this;
    }

    /**
     * Get idPaquetePrestacion.
     *
     * @return \Rebsol\HermesBundle\Entity\PaquetePrestacion|null
     */
    public function getIdPaquetePrestacion()
    {
        return $this->idPaquetePrestacion;
    }
}
