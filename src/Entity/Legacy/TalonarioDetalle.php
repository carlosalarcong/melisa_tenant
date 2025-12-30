<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TalonarioDetalle
 *
 * @ORM\Table(name="talonario_detalle")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TalonarioDetalleRepository")
 */
class TalonarioDetalle
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
     * @ORM\Column(name="NUMERO_DOCUMENTO", type="integer", nullable=true)
     */
    private $numeroDocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $monto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_DETALLE_BOLETA", type="datetime", nullable=false)
     */
    private $fechaDetalleBoleta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_SUB_EMPRESA_FACTURADORA", type="integer", nullable=true)
     */
    private $idSubEmpresaFacturadora;

    /**
     * @var \Talonario
     *
     * @ORM\ManyToOne(targetEntity="Talonario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TALONARIO", referencedColumnName="ID")
     * })
     */
    private $idTalonario;

    /**
     * @var \EstadoTalonarioDetalle
     *
     * @ORM\ManyToOne(targetEntity="EstadoTalonarioDetalle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_TALONARIO_DETALLE", referencedColumnName="ID")
     * })
     */
    private $idEstadoTalonarioDetalle;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_DETALLE_BOLETA", referencedColumnName="ID")
     * })
     */
    private $idUsuarioDetalleBoleta;

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
     * Set numeroDocumento.
     *
     * @param int|null $numeroDocumento
     *
     * @return TalonarioDetalle
     */
    public function setNumeroDocumento($numeroDocumento = null)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento.
     *
     * @return int|null
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set monto.
     *
     * @param string|null $monto
     *
     * @return TalonarioDetalle
     */
    public function setMonto($monto = null)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto.
     *
     * @return string|null
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set fechaDetalleBoleta.
     *
     * @param \DateTime $fechaDetalleBoleta
     *
     * @return TalonarioDetalle
     */
    public function setFechaDetalleBoleta($fechaDetalleBoleta)
    {
        $this->fechaDetalleBoleta = $fechaDetalleBoleta;

        return $this;
    }

    /**
     * Get fechaDetalleBoleta.
     *
     * @return \DateTime
     */
    public function getFechaDetalleBoleta()
    {
        return $this->fechaDetalleBoleta;
    }

    /**
     * Set idSubEmpresaFacturadora.
     *
     * @param int|null $idSubEmpresaFacturadora
     *
     * @return TalonarioDetalle
     */
    public function setIdSubEmpresaFacturadora($idSubEmpresaFacturadora = null)
    {
        $this->idSubEmpresaFacturadora = $idSubEmpresaFacturadora;

        return $this;
    }

    /**
     * Get idSubEmpresaFacturadora.
     *
     * @return int|null
     */
    public function getIdSubEmpresaFacturadora()
    {
        return $this->idSubEmpresaFacturadora;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Paciente|null $idPaciente
     *
     * @return TalonarioDetalle
     */
    public function setIdPaciente(\App\Entity\Legacy\Legacy\Paciente $idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Paciente|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idTalonario.
     *
     * @param \App\Entity\Legacy\Legacy\Talonario|null $idTalonario
     *
     * @return TalonarioDetalle
     */
    public function setIdTalonario(\App\Entity\Legacy\Legacy\Talonario $idTalonario = null)
    {
        $this->idTalonario = $idTalonario;

        return $this;
    }

    /**
     * Get idTalonario.
     *
     * @return \App\Entity\Legacy\Legacy\Talonario|null
     */
    public function getIdTalonario()
    {
        return $this->idTalonario;
    }

    /**
     * Set idEstadoTalonarioDetalle.
     *
     * @param \App\Entity\Legacy\Legacy\EstadoTalonarioDetalle $idEstadoTalonarioDetalle
     *
     * @return TalonarioDetalle
     */
    public function setIdEstadoTalonarioDetalle(\App\Entity\Legacy\Legacy\EstadoTalonarioDetalle $idEstadoTalonarioDetalle)
    {
        $this->idEstadoTalonarioDetalle = $idEstadoTalonarioDetalle;

        return $this;
    }

    /**
     * Get idEstadoTalonarioDetalle.
     *
     * @return \App\Entity\Legacy\Legacy\EstadoTalonarioDetalle
     */
    public function getIdEstadoTalonarioDetalle()
    {
        return $this->idEstadoTalonarioDetalle;
    }

    /**
     * Set idUsuarioDetalleBoleta.
     *
     * @param \App\Entity\Legacy\Legacy\UsuariosRebsol|null $idUsuarioDetalleBoleta
     *
     * @return TalonarioDetalle
     */
    public function setIdUsuarioDetalleBoleta(\App\Entity\Legacy\Legacy\UsuariosRebsol $idUsuarioDetalleBoleta = null)
    {
        $this->idUsuarioDetalleBoleta = $idUsuarioDetalleBoleta;

        return $this;
    }

    /**
     * Get idUsuarioDetalleBoleta.
     *
     * @return \App\Entity\Legacy\Legacy\UsuariosRebsol|null
     */
    public function getIdUsuarioDetalleBoleta()
    {
        return $this->idUsuarioDetalleBoleta;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\PagoCuenta|null $idPagoCuenta
     *
     * @return TalonarioDetalle
     */
    public function setIdPagoCuenta(\App\Entity\Legacy\Legacy\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \App\Entity\Legacy\Legacy\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

}
