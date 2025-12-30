<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleDocumentoPago
 *
 * @ORM\Table(name="detalle_documento_pago")
 * @ORM\Entity
 */
class DetalleDocumentoPago
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
     * @var string
     *
     * @ORM\Column(name="MONTO_DOCUMENTO", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $montoDocumento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_PAGO_DOCUMENTO", type="datetime", nullable=false)
     */
    private $fechaPagoDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO_DOCUMENTO_DETALLE", type="string", length=30, nullable=false)
     */
    private $numeroDocumentoDetalle;

    /**
     * @var \CondicionPago
     *
     * @ORM\ManyToOne(targetEntity="CondicionPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CONDICION_PAGO", referencedColumnName="ID")
     * })
     */
    private $idCondicionPago;

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
     * @var \DocumentoPago
     *
     * @ORM\ManyToOne(targetEntity="DocumentoPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_DOCUMENTO_PAGO", referencedColumnName="ID")
     * })
     */
    private $idDocumentoPago;



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
     * Set montoDocumento.
     *
     * @param string $montoDocumento
     *
     * @return DetalleDocumentoPago
     */
    public function setMontoDocumento($montoDocumento)
    {
        $this->montoDocumento = $montoDocumento;

        return $this;
    }

    /**
     * Get montoDocumento.
     *
     * @return string
     */
    public function getMontoDocumento()
    {
        return $this->montoDocumento;
    }

    /**
     * Set fechaPagoDocumento.
     *
     * @param \DateTime $fechaPagoDocumento
     *
     * @return DetalleDocumentoPago
     */
    public function setFechaPagoDocumento($fechaPagoDocumento)
    {
        $this->fechaPagoDocumento = $fechaPagoDocumento;

        return $this;
    }

    /**
     * Get fechaPagoDocumento.
     *
     * @return \DateTime
     */
    public function getFechaPagoDocumento()
    {
        return $this->fechaPagoDocumento;
    }

    /**
     * Set numeroDocumentoDetalle.
     *
     * @param string $numeroDocumentoDetalle
     *
     * @return DetalleDocumentoPago
     */
    public function setNumeroDocumentoDetalle($numeroDocumentoDetalle)
    {
        $this->numeroDocumentoDetalle = $numeroDocumentoDetalle;

        return $this;
    }

    /**
     * Get numeroDocumentoDetalle.
     *
     * @return string
     */
    public function getNumeroDocumentoDetalle()
    {
        return $this->numeroDocumentoDetalle;
    }

    /**
     * Set idDocumentoPago.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DocumentoPago $idDocumentoPago
     *
     * @return DetalleDocumentoPago
     */
    public function setIdDocumentoPago(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DocumentoPago $idDocumentoPago)
    {
        $this->idDocumentoPago = $idDocumentoPago;

        return $this;
    }

    /**
     * Get idDocumentoPago.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DocumentoPago
     */
    public function getIdDocumentoPago()
    {
        return $this->idDocumentoPago;
    }

    /**
     * Set idCondicionPago.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\CondicionPago $idCondicionPago
     *
     * @return DetalleDocumentoPago
     */
    public function setIdCondicionPago(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\CondicionPago $idCondicionPago)
    {
        $this->idCondicionPago = $idCondicionPago;

        return $this;
    }

    /**
     * Get idCondicionPago.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\CondicionPago
     */
    public function getIdCondicionPago()
    {
        return $this->idCondicionPago;
    }

    /**
     * Set idFormaPago.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago $idFormaPago
     *
     * @return DetalleDocumentoPago
     */
    public function setIdFormaPago(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago $idFormaPago)
    {
        $this->idFormaPago = $idFormaPago;

        return $this;
    }

    /**
     * Get idFormaPago.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago
     */
    public function getIdFormaPago()
    {
        return $this->idFormaPago;
    }
}
