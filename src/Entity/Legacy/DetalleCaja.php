<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleCaja
 *
 * @ORM\Table(name="detalle_caja")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DetalleCajaRepository")
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
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja $idCaja
     *
     * @return DetalleCaja
     */
    public function setIdCaja(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja $idCaja)
    {
        $this->idCaja = $idCaja;

        return $this;
    }

    /**
     * Get idCaja.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja
     */
    public function getIdCaja()
    {
        return $this->idCaja;
    }

    /**
     * Set idBanco.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Banco|null $idBanco
     *
     * @return DetalleCaja
     */
    public function setIdBanco(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Banco $idBanco = null)
    {
        $this->idBanco = $idBanco;

        return $this;
    }

    /**
     * Get idBanco.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Banco|null
     */
    public function getIdBanco()
    {
        return $this->idBanco;
    }

    /**
     * Set idFormaPago.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago $idFormaPago
     *
     * @return DetalleCaja
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

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado $idEstado
     *
     * @return DetalleCaja
     */
    public function setIdEstado(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
