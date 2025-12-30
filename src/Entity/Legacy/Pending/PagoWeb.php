<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PagoWeb
 *
 * @ORM\Table(name="pago_web")
 * @ORM\Entity
 */
class PagoWeb
{
    const APROBADO = 0;
    const RECHAZADO_REINTENTAR = -1;
    const RECHAZADO = -2;
    const ERROR_INTERNO = -3;
    const RECHAZO_EMISOR = -4;
    const RECHAZO_FRAUDE = -5;

    const VD = 'Venta Débito';
    const VN = 'Venta Normal';
    const VC = 'Venta en cuotas';
    const SI = '3 cuotas sin interés';
    const S2 = '2 cuotas sin interés';
    const NC = 'N Cuotas sin interés';
    const VP = 'Venta Prepago';

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
     * @ORM\Column(name="FECHA_TRANSACCION", type="datetime", nullable=true)
     */
    private $fechaTransaccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="AUTHORIZATION_CODE", type="string", length=255, nullable=true)
     */
    private $authorizationCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PAYMENT_TYPE_CODE", type="string", length=255, nullable=true)
     */
    private $paymentTypeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RESPONSE_CODE", type="integer", nullable=true)
     */
    private $responseCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="AMOUNT", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="BUY_ORDER", type="integer", nullable=true)
     */
    private $buyOrder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOKEN_WS", type="string", length=255, nullable=true)
     */
    private $tokenWs;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CARD_NUMBER", type="integer", nullable=true)
     */
    private $cardNumber;

    /**
     * @var int|null
     *
     * @ORM\Column(name="SHARES_NUMBER", type="integer", nullable=true)
     */
    private $sharesNumber;

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
     * Set fechaTransaccion.
     *
     * @param \DateTime|null $fechaTransaccion
     *
     * @return PagoWeb
     */
    public function setFechaTransaccion($fechaTransaccion = null)
    {
        $this->fechaTransaccion = $fechaTransaccion;

        return $this;
    }

    /**
     * Get fechaTransaccion.
     *
     * @return \DateTime|null
     */
    public function getFechaTransaccion()
    {
        return $this->fechaTransaccion;
    }

    /**
     * Set authorizationCode.
     *
     * @param string|null $authorizationCode
     *
     * @return PagoWeb
     */
    public function setAuthorizationCode($authorizationCode = null)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * Get authorizationCode.
     *
     * @return string|null
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * Set paymentTypeCode.
     *
     * @param string|null $paymentTypeCode
     *
     * @return PagoWeb
     */
    public function setPaymentTypeCode($paymentTypeCode = null)
    {
        $this->paymentTypeCode = $paymentTypeCode;

        return $this;
    }

    /**
     * Get paymentTypeCode.
     *
     * @return string|null
     */
    public function getPaymentTypeCode()
    {
        return $this->paymentTypeCode;
    }

    /**
     * Set responseCode.
     *
     * @param int|null $responseCode
     *
     * @return PagoWeb
     */
    public function setResponseCode($responseCode = null)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * Get responseCode.
     *
     * @return int|null
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Set amount.
     *
     * @param int|null $amount
     *
     * @return PagoWeb
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set buyOrder.
     *
     * @param int|null $buyOrder
     *
     * @return PagoWeb
     */
    public function setBuyOrder($buyOrder = null)
    {
        $this->buyOrder = $buyOrder;

        return $this;
    }

    /**
     * Get buyOrder.
     *
     * @return int|null
     */
    public function getBuyOrder()
    {
        return $this->buyOrder;
    }

    /**
     * Set tokenWs.
     *
     * @param string|null $tokenWs
     *
     * @return PagoWeb
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
     * Set cardNumber.
     *
     * @param int|null $cardNumber
     *
     * @return PagoWeb
     */
    public function setCardNumber($cardNumber = null)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * Get cardNumber.
     *
     * @return int|null
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set sharesNumber.
     *
     * @param int|null $sharesNumber
     *
     * @return PagoWeb
     */
    public function setSharesNumber($sharesNumber = null)
    {
        $this->sharesNumber = $sharesNumber;

        return $this;
    }

    /**
     * Get sharesNumber.
     *
     * @return int|null
     */
    public function getSharesNumber()
    {
        return $this->sharesNumber;
    }

    public $estados =
    [
        'APROBADO' => self::APROBADO,
        'RECHAZADO_REINTENTAR' => self::RECHAZADO_REINTENTAR,
        'RECHAZADO' => self::RECHAZADO,
        'ERROR_INTERNO' => self::ERROR_INTERNO,
        'RECHAZO_EMISOR' => self::RECHAZO_EMISOR,
        'RECHAZO_FRAUDE' => self::RECHAZO_FRAUDE
    ];

    public function getEstados()
    {
        return $this->estados;
    }

    public $tiposPagos =
    [
        'VD' => self::VD,
        'VN' => self::VN,
        'VC' => self::VC,
        'SI' => self::SI,
        'S2' => self::S2,
        'NC' => self::NC,
        'VP' => self::VP,
    ];

    public function getTiposPagos()
    {
        return $this->tiposPagos;
    }

}
