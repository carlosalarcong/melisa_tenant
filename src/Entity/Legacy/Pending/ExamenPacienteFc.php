<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExamenPacienteFc
 *
 * @ORM\Table(name="examen_paciente_fc")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ExamenPacienteFcRepository")
 */
class ExamenPacienteFc
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
     * @var int|null
     *
     * @ORM\Column(name="NUMERACION", type="integer", nullable=true)
     */
    private $numeracion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_SOLICITUD", type="datetime", nullable=true)
     */
    private $fechaSolicitud;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ES_EXTERNO", type="integer", nullable=true)
     */
    private $esExterno;

    /**
     * @var \TipoPrestacionExamen
     *
     * @ORM\ManyToOne(targetEntity="TipoPrestacionExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PRESTACION_EXAMENES", referencedColumnName="ID")
     * })
     */
    private $idTipoPrestacionExamenes;

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
     * @var \ConsultaMedicaFc
     *
     * @ORM\ManyToOne(targetEntity="ConsultaMedicaFc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONSULTA_MEDICA", referencedColumnName="ID")
     * })
     */
    private $idConsultaMedica;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_OTRA_PERSONA", referencedColumnName="ID")
     * })
     */
    private $idOtraPersona;

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
     * @var \PagoCuenta
     *
     * @ORM\ManyToOne(targetEntity="PagoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAGO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idPagoCuenta;



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
     * Set numeracion.
     *
     * @param int|null $numeracion
     *
     * @return ExamenPacienteFc
     */
    public function setNumeracion($numeracion = null)
    {
        $this->numeracion = $numeracion;

        return $this;
    }

    /**
     * Get numeracion.
     *
     * @return int|null
     */
    public function getNumeracion()
    {
        return $this->numeracion;
    }

    /**
     * Set fechaSolicitud.
     *
     * @param \DateTime|null $fechaSolicitud
     *
     * @return ExamenPacienteFc
     */
    public function setFechaSolicitud($fechaSolicitud = null)
    {
        $this->fechaSolicitud = $fechaSolicitud;

        return $this;
    }

    /**
     * Get fechaSolicitud.
     *
     * @return \DateTime|null
     */
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return ExamenPacienteFc
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
     * Set esExterno.
     *
     * @param int|null $esExterno
     *
     * @return ExamenPacienteFc
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
     * Set idUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuario
     *
     * @return ExamenPacienteFc
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
     * Set idConsultaMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\ConsultaMedicaFc|null $idConsultaMedica
     *
     * @return ExamenPacienteFc
     */
    public function setIdConsultaMedica(\Rebsol\HermesBundle\Entity\ConsultaMedicaFc $idConsultaMedica = null)
    {
        $this->idConsultaMedica = $idConsultaMedica;

        return $this;
    }

    /**
     * Get idConsultaMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\ConsultaMedicaFc|null
     */
    public function getIdConsultaMedica()
    {
        return $this->idConsultaMedica;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return ExamenPacienteFc
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
     * Set idOtraPersona.
     *
     * @param \Rebsol\HermesBundle\Entity\Persona|null $idOtraPersona
     *
     * @return ExamenPacienteFc
     */
    public function setIdOtraPersona(\Rebsol\HermesBundle\Entity\Persona $idOtraPersona = null)
    {
        $this->idOtraPersona = $idOtraPersona;

        return $this;
    }

    /**
     * Get idOtraPersona.
     *
     * @return \Rebsol\HermesBundle\Entity\Persona|null
     */
    public function getIdOtraPersona()
    {
        return $this->idOtraPersona;
    }

    /**
     * Set idTipoPrestacionExamenes.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrestacionExamen|null $idTipoPrestacionExamenes
     *
     * @return ExamenPacienteFc
     */
    public function setIdTipoPrestacionExamenes(\Rebsol\HermesBundle\Entity\TipoPrestacionExamen $idTipoPrestacionExamenes = null)
    {
        $this->idTipoPrestacionExamenes = $idTipoPrestacionExamenes;

        return $this;
    }

    /**
     * Get idTipoPrestacionExamenes.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPrestacionExamen|null
     */
    public function getIdTipoPrestacionExamenes()
    {
        return $this->idTipoPrestacionExamenes;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \Rebsol\HermesBundle\Entity\PagoCuenta|null $idPagoCuenta
     *
     * @return ExamenPacienteFc
     */
    public function setIdPagoCuenta(\Rebsol\HermesBundle\Entity\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \Rebsol\HermesBundle\Entity\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }
}
