<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleCaja
 *
 * @ORM\Table(name="detalle_caja")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\DetalleCajaRepository")
 */
class DetalleCaja
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
     * @ORM\Column(name="NUMERO_DEPOSITO", type="string", length=25, nullable=true)
     */
    private $numeroDeposito;

    /**
     * @var int
     *
     * @ORM\Column(name="MONTO", type="integer", nullable=false)
     */
    private $monto;

    /**
     * @var \Banco
     *
     * @ORM\ManyToOne(targetEntity="Banco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BANCO", referencedColumnName="ID")
     * })
     */
    private $idBanco;

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
     * @var \Caja
     *
     * @ORM\ManyToOne(targetEntity="Caja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CAJA", referencedColumnName="ID")
     * })
     */
    private $idCaja;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set numeroDeposito.
     *
     * @param string|null $numeroDeposito
     *
     * @return DetalleCaja
     */
    public function setNumeroDeposito($numeroDeposito = null)
    {
        $this->numeroDeposito = $numeroDeposito;

        return $this;
    }

    /**
     * Get numeroDeposito.
     *
     * @return string|null
     */
    public function getNumeroDeposito()
    {
        return $this->numeroDeposito;
    }

    /**
     * Set monto.
     *
     * @param int $monto
     *
     * @return DetalleCaja
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto.
     *
     * @return int
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set idCaja.
     *
     * @param \App\Entity\Caja $idCaja
     *
     * @return DetalleCaja
     */
    public function setIdCaja(\App\Entity\Caja $idCaja)
    {
        $this->idCaja = $idCaja;

        return $this;
    }

    /**
     * Get idCaja.
     *
     * @return \App\Entity\Caja
     */
    public function getIdCaja()
    {
        return $this->idCaja;
    }

    /**
     * Set idBanco.
     *
     * @param \App\Entity\Banco|null $idBanco
     *
     * @return DetalleCaja
     */
    public function setIdBanco(\App\Entity\Banco $idBanco = null)
    {
        $this->idBanco = $idBanco;

        return $this;
    }

    /**
     * Get idBanco.
     *
     * @return \App\Entity\Banco|null
     */
    public function getIdBanco()
    {
        return $this->idBanco;
    }

    /**
     * Set idFormaPago.
     *
     * @param \App\Entity\FormaPago $idFormaPago
     *
     * @return DetalleCaja
     */
    public function setIdFormaPago(\App\Entity\FormaPago $idFormaPago)
    {
        $this->idFormaPago = $idFormaPago;

        return $this;
    }

    /**
     * Get idFormaPago.
     *
     * @return \App\Entity\FormaPago
     */
    public function getIdFormaPago()
    {
        return $this->idFormaPago;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Estado $idEstado
     *
     * @return DetalleCaja
     */
    public function setIdEstado(\App\Entity\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
