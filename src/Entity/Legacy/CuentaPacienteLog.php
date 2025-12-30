<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaPacienteLog
 *
 * @ORM\Table(name="cuenta_paciente_log")
 * @ORM\Entity
 */
class CuentaPacienteLog
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
     * @ORM\Column(name="FECHA_ESTADO_CUENTA", type="datetime", nullable=false)
     */
    private $fechaEstadoCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="SALDO_CUENTA", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $saldoCuenta;

    /**
     * @var int
     *
     * @ORM\Column(name="NUMERO_ACCION", type="integer", nullable=false)
     */
    private $numeroAccion;

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
     * @var \CuentaPaciente
     *
     * @ORM\ManyToOne(targetEntity="CuentaPaciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idCuenta;

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
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO", referencedColumnName="ID")
     * })
     */
    private $idUsuario;



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
     * Set fechaEstadoCuenta.
     *
     * @param \DateTime $fechaEstadoCuenta
     *
     * @return CuentaPacienteLog
     */
    public function setFechaEstadoCuenta($fechaEstadoCuenta)
    {
        $this->fechaEstadoCuenta = $fechaEstadoCuenta;

        return $this;
    }

    /**
     * Get fechaEstadoCuenta.
     *
     * @return \DateTime
     */
    public function getFechaEstadoCuenta()
    {
        return $this->fechaEstadoCuenta;
    }

    /**
     * Set saldoCuenta.
     *
     * @param string $saldoCuenta
     *
     * @return CuentaPacienteLog
     */
    public function setSaldoCuenta($saldoCuenta)
    {
        $this->saldoCuenta = $saldoCuenta;

        return $this;
    }

    /**
     * Get saldoCuenta.
     *
     * @return string
     */
    public function getSaldoCuenta()
    {
        return $this->saldoCuenta;
    }

    /**
     * Set numeroAccion.
     *
     * @param int $numeroAccion
     *
     * @return CuentaPacienteLog
     */
    public function setNumeroAccion($numeroAccion)
    {
        $this->numeroAccion = $numeroAccion;

        return $this;
    }

    /**
     * Get numeroAccion.
     *
     * @return int
     */
    public function getNumeroAccion()
    {
        return $this->numeroAccion;
    }

    /**
     * Set idCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\CuentaPaciente $idCuenta
     *
     * @return CuentaPacienteLog
     */
    public function setIdCuenta(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\CuentaPaciente $idCuenta)
    {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * Get idCuenta.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\CuentaPaciente
     */
    public function getIdCuenta()
    {
        return $this->idCuenta;
    }

    /**
     * Set idUsuario.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuario
     *
     * @return CuentaPacienteLog
     */
    public function setIdUsuario(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idEstadoCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoCuenta $idEstadoCuenta
     *
     * @return CuentaPacienteLog
     */
    public function setIdEstadoCuenta(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoCuenta $idEstadoCuenta)
    {
        $this->idEstadoCuenta = $idEstadoCuenta;

        return $this;
    }

    /**
     * Get idEstadoCuenta.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoCuenta
     */
    public function getIdEstadoCuenta()
    {
        return $this->idEstadoCuenta;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente $idPaciente
     *
     * @return CuentaPacienteLog
     */
    public function setIdPaciente(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente $idPaciente)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }
}
