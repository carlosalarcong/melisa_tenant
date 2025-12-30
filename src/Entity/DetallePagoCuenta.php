<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetallePagoCuenta
 *
 * @ORM\Table(name="detalle_pago_cuenta")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\DetallePagoCuentaRepository")
 */
class DetallePagoCuenta
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
     * @ORM\Column(name="GARANTIA", type="integer", nullable=false)
     */
    private $garantia;

    /**
     * @var int
     *
     * @ORM\Column(name="MONTO_PAGO_CUENTA", type="integer", nullable=false)
     */
    private $montoPagoCuenta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_MONEDA", type="integer", nullable=true)
     */
    private $idMoneda;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_DETALLE_PAGO", type="datetime", nullable=false)
     */
    private $fechaDetallePago;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_EMPRESA", type="string", length=50, nullable=true)
     */
    private $nombreEmpresa;

    /**
     * @var int
     *
     * @ORM\Column(name="CODIGO_CONTROL_FACTURACION", type="integer", nullable=false)
     */
    private $codigoControlFacturacion;

    /**
     * @var \MotivoGratuidad
     *
     * @ORM\ManyToOne(targetEntity="MotivoGratuidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_MOTIVO_GRATUIDAD", referencedColumnName="ID")
     * })
     */
    private $idMotivoGratuidad;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PREVISION", referencedColumnName="ID")
     * })
     */
    private $idPrevision;

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
     * @var \FormaPago
     *
     * @ORM\ManyToOne(targetEntity="FormaPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FORMA_PAGO", referencedColumnName="ID")
     * })
     */
    private $idFormaPago;

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
     * @var int
     *
     * @ORM\Column(name="FOLIO_GARANTIA", type="integer", nullable=true)
     */
    private $folioGarantia;

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
     * Set garantia.
     *
     * @param int $garantia
     *
     * @return DetallePagoCuenta
     */
    public function setGarantia($garantia)
    {
        $this->garantia = $garantia;

        return $this;
    }

    /**
     * Get garantia.
     *
     * @return int
     */
    public function getGarantia()
    {
        return $this->garantia;
    }

    /**
     * Set montoPagoCuenta.
     *
     * @param int $montoPagoCuenta
     *
     * @return DetallePagoCuenta
     */
    public function setMontoPagoCuenta($montoPagoCuenta)
    {
        $this->montoPagoCuenta = $montoPagoCuenta;

        return $this;
    }

    /**
     * Get montoPagoCuenta.
     *
     * @return int
     */
    public function getMontoPagoCuenta()
    {
        return $this->montoPagoCuenta;
    }

    /**
     * Set idMoneda.
     *
     * @param int|null $idMoneda
     *
     * @return DetallePagoCuenta
     */
    public function setIdMoneda($idMoneda = null)
    {
        $this->idMoneda = $idMoneda;

        return $this;
    }

    /**
     * Get idMoneda.
     *
     * @return int|null
     */
    public function getIdMoneda()
    {
        return $this->idMoneda;
    }

    /**
     * Set fechaDetallePago.
     *
     * @param \DateTime $fechaDetallePago
     *
     * @return DetallePagoCuenta
     */
    public function setFechaDetallePago($fechaDetallePago)
    {
        $this->fechaDetallePago = $fechaDetallePago;

        return $this;
    }

    /**
     * Get fechaDetallePago.
     *
     * @return \DateTime
     */
    public function getFechaDetallePago()
    {
        return $this->fechaDetallePago;
    }

    /**
     * Set nombreEmpresa.
     *
     * @param string|null $nombreEmpresa
     *
     * @return DetallePagoCuenta
     */
    public function setNombreEmpresa($nombreEmpresa = null)
    {
        $this->nombreEmpresa = $nombreEmpresa;

        return $this;
    }

    /**
     * Get nombreEmpresa.
     *
     * @return string|null
     */
    public function getNombreEmpresa()
    {
        return $this->nombreEmpresa;
    }

    /**
     * Set codigoControlFacturacion.
     *
     * @param int $codigoControlFacturacion
     *
     * @return DetallePagoCuenta
     */
    public function setCodigoControlFacturacion($codigoControlFacturacion)
    {
        $this->codigoControlFacturacion = $codigoControlFacturacion;

        return $this;
    }

    /**
     * Get codigoControlFacturacion.
     *
     * @return int
     */
    public function getCodigoControlFacturacion()
    {
        return $this->codigoControlFacturacion;
    }

    /**
     * Set idPagoCuenta.
     *
     * @param \App\Entity\PagoCuenta|null $idPagoCuenta
     *
     * @return DetallePagoCuenta
     */
    public function setIdPagoCuenta(\App\Entity\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \App\Entity\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

    /**
     * Set idPrevision.
     *
     * @param \App\Entity\Prevision|null $idPrevision
     *
     * @return DetallePagoCuenta
     */
    public function setIdPrevision(\App\Entity\Prevision $idPrevision = null)
    {
        $this->idPrevision = $idPrevision;

        return $this;
    }

    /**
     * Get idPrevision.
     *
     * @return \App\Entity\Prevision|null
     */
    public function getIdPrevision()
    {
        return $this->idPrevision;
    }

    /**
     * Set idConvenio.
     *
     * @param \App\Entity\Prevision|null $idConvenio
     *
     * @return DetallePagoCuenta
     */
    public function setIdConvenio(\App\Entity\Prevision $idConvenio = null)
    {
        $this->idConvenio = $idConvenio;

        return $this;
    }

    /**
     * Get idConvenio.
     *
     * @return \App\Entity\Prevision|null
     */
    public function getIdConvenio()
    {
        return $this->idConvenio;
    }

    /**
     * Set idMotivoGratuidad.
     *
     * @param \App\Entity\MotivoGratuidad|null $idMotivoGratuidad
     *
     * @return DetallePagoCuenta
     */
    public function setIdMotivoGratuidad(\App\Entity\MotivoGratuidad $idMotivoGratuidad = null)
    {
        $this->idMotivoGratuidad = $idMotivoGratuidad;

        return $this;
    }

    /**
     * Get idMotivoGratuidad.
     *
     * @return \App\Entity\MotivoGratuidad|null
     */
    public function getIdMotivoGratuidad()
    {
        return $this->idMotivoGratuidad;
    }

    /**
     * Set idFormaPago.
     *
     * @param \App\Entity\FormaPago|null $idFormaPago
     *
     * @return DetallePagoCuenta
     */
    public function setIdFormaPago(\App\Entity\FormaPago $idFormaPago = null)
    {
        $this->idFormaPago = $idFormaPago;

        return $this;
    }

    /**
     * Get idFormaPago.
     *
     * @return \App\Entity\FormaPago|null
     */
    public function getIdFormaPago()
    {
        return $this->idFormaPago;
    }

    /**
     * @return int
     */
    public function getFolioGarantia()
    {
        return $this->folioGarantia;
    }

    /**
     * @param int $folioGarantia
     */
    public function setFolioGarantia($folioGarantia)
    {
        $this->folioGarantia = $folioGarantia;
    }

}
