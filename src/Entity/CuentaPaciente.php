<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaPaciente
 *
 * @ORM\Table(name="cuenta_paciente")
 * @ORM\Entity
 */
class CuentaPaciente
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
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_CUENTA", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $totalCuenta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="AFECTO_CUENTA", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $afectoCuenta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PREGUNTA_UNO", type="integer", nullable=true)
     */
    private $preguntaUno;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PREGUNTA_DOS", type="integer", nullable=true)
     */
    private $preguntaDos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_PRECUENTA", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $totalPrecuenta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="NUMERO_PRECUENTA", type="integer", nullable=true)
     */
    private $numeroPrecuenta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_DESCUENTO", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $totalDescuento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RUT_CORRIGE_SALDO", type="integer", nullable=true)
     */
    private $rutCorrigeSaldo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SALDO_CUENTA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $saldoCuenta;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_MODIFICACION", type="datetime", nullable=true)
     */
    private $fechaModificacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_MODIFICACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioModificacion;

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
     * @var \EstadoCuenta
     *
     * @ORM\ManyToOne(targetEntity="EstadoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idEstadoCuenta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_CUENTA_PAQUETIZADO", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $totalCuentaPaquetizado;

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
     * Set totalCuenta.
     *
     * @param string|null $totalCuenta
     *
     * @return CuentaPaciente
     */
    public function setTotalCuenta($totalCuenta = null)
    {
        $this->totalCuenta = $totalCuenta;

        return $this;
    }

    /**
     * Get totalCuenta.
     *
     * @return string|null
     */
    public function getTotalCuenta()
    {
        return $this->totalCuenta;
    }

    /**
     * Set afectoCuenta.
     *
     * @param string|null $afectoCuenta
     *
     * @return CuentaPaciente
     */
    public function setAfectoCuenta($afectoCuenta = null)
    {
        $this->afectoCuenta = $afectoCuenta;

        return $this;
    }

    /**
     * Get afectoCuenta.
     *
     * @return string|null
     */
    public function getAfectoCuenta()
    {
        return $this->afectoCuenta;
    }

    /**
     * Set preguntaUno.
     *
     * @param int|null $preguntaUno
     *
     * @return CuentaPaciente
     */
    public function setPreguntaUno($preguntaUno = null)
    {
        $this->preguntaUno = $preguntaUno;

        return $this;
    }

    /**
     * Get preguntaUno.
     *
     * @return int|null
     */
    public function getPreguntaUno()
    {
        return $this->preguntaUno;
    }

    /**
     * Set preguntaDos.
     *
     * @param int|null $preguntaDos
     *
     * @return CuentaPaciente
     */
    public function setPreguntaDos($preguntaDos = null)
    {
        $this->preguntaDos = $preguntaDos;

        return $this;
    }

    /**
     * Get preguntaDos.
     *
     * @return int|null
     */
    public function getPreguntaDos()
    {
        return $this->preguntaDos;
    }

    /**
     * Set totalPrecuenta.
     *
     * @param string|null $totalPrecuenta
     *
     * @return CuentaPaciente
     */
    public function setTotalPrecuenta($totalPrecuenta = null)
    {
        $this->totalPrecuenta = $totalPrecuenta;

        return $this;
    }

    /**
     * Get totalPrecuenta.
     *
     * @return string|null
     */
    public function getTotalPrecuenta()
    {
        return $this->totalPrecuenta;
    }

    /**
     * Set numeroPrecuenta.
     *
     * @param int|null $numeroPrecuenta
     *
     * @return CuentaPaciente
     */
    public function setNumeroPrecuenta($numeroPrecuenta = null)
    {
        $this->numeroPrecuenta = $numeroPrecuenta;

        return $this;
    }

    /**
     * Get numeroPrecuenta.
     *
     * @return int|null
     */
    public function getNumeroPrecuenta()
    {
        return $this->numeroPrecuenta;
    }

    /**
     * Set totalDescuento.
     *
     * @param string|null $totalDescuento
     *
     * @return CuentaPaciente
     */
    public function setTotalDescuento($totalDescuento = null)
    {
        $this->totalDescuento = $totalDescuento;

        return $this;
    }

    /**
     * Get totalDescuento.
     *
     * @return string|null
     */
    public function getTotalDescuento()
    {
        return $this->totalDescuento;
    }

    /**
     * Set rutCorrigeSaldo.
     *
     * @param int|null $rutCorrigeSaldo
     *
     * @return CuentaPaciente
     */
    public function setRutCorrigeSaldo($rutCorrigeSaldo = null)
    {
        $this->rutCorrigeSaldo = $rutCorrigeSaldo;

        return $this;
    }

    /**
     * Get rutCorrigeSaldo.
     *
     * @return int|null
     */
    public function getRutCorrigeSaldo()
    {
        return $this->rutCorrigeSaldo;
    }

    /**
     * Set saldoCuenta.
     *
     * @param string|null $saldoCuenta
     *
     * @return CuentaPaciente
     */
    public function setSaldoCuenta($saldoCuenta = null)
    {
        $this->saldoCuenta = $saldoCuenta;

        return $this;
    }

    /**
     * Get saldoCuenta.
     *
     * @return string|null
     */
    public function getSaldoCuenta()
    {
        return $this->saldoCuenta;
    }

    /**
     * Set fechaModificacion.
     *
     * @param \DateTime|null $fechaModificacion
     *
     * @return CuentaPaciente
     */
    public function setFechaModificacion($fechaModificacion = null)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Get fechaModificacion.
     *
     * @return \DateTime|null
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Paciente|null $idPaciente
     *
     * @return CuentaPaciente
     */
    public function setIdPaciente(\App\Entity\Paciente $idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Paciente|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idEstadoCuenta.
     *
     * @param \App\Entity\EstadoCuenta|null $idEstadoCuenta
     *
     * @return CuentaPaciente
     */
    public function setIdEstadoCuenta(\App\Entity\EstadoCuenta $idEstadoCuenta = null)
    {
        $this->idEstadoCuenta = $idEstadoCuenta;

        return $this;
    }

    /**
     * Get idEstadoCuenta.
     *
     * @return \App\Entity\EstadoCuenta|null
     */
    public function getIdEstadoCuenta()
    {
        return $this->idEstadoCuenta;
    }

    /**
     * Set idUsuarioModificacion.
     *
     * @param \App\Entity\UsuariosRebsol|null $idUsuarioModificacion
     *
     * @return CuentaPaciente
     */
    public function setIdUsuarioModificacion(\App\Entity\UsuariosRebsol $idUsuarioModificacion = null)
    {
        $this->idUsuarioModificacion = $idUsuarioModificacion;

        return $this;
    }

    /**
     * Get idUsuarioModificacion.
     *
     * @return \App\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioModificacion()
    {
        return $this->idUsuarioModificacion;
    }

    /**
     * Set totalCuentaPaquetizado.
     *
     * @param string|null $totalCuentaPaquetizado
     *
     * @return CuentaPaciente
     */
    public function setTotalCuentaPaquetizado($totalCuentaPaquetizado = null)
    {
        $this->totalCuentaPaquetizado = $totalCuentaPaquetizado;

        return $this;
    }

    /**
     * Get totalCuentaPaquetizado.
     *
     * @return string|null
     */
    public function getTotalCuentaPaquetizado()
    {
        return $this->totalCuentaPaquetizado;
    }
}
