<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleCajaCheque
 *
 * @ORM\Table(name="detalle_caja_cheque")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DetalleCajaChequeRepository")
 */
class DetalleCajaCheque
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
     * @ORM\Column(name="NUMERO_DEPOSITO", type="integer", nullable=false)
     */
    private $numeroDeposito;

    /**
     * @var int
     *
     * @ORM\Column(name="NUMERO_CHEQUE", type="integer", nullable=false)
     */
    private $numeroCheque;

    /**
     * @var string
     *
     * @ORM\Column(name="MONTO", type="decimal", precision=10, scale=2, nullable=false)
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
     * @param int $numeroDeposito
     *
     * @return DetalleCajaCheque
     */
    public function setNumeroDeposito($numeroDeposito)
    {
        $this->numeroDeposito = $numeroDeposito;

        return $this;
    }

    /**
     * Get numeroDeposito.
     *
     * @return int
     */
    public function getNumeroDeposito()
    {
        return $this->numeroDeposito;
    }

    /**
     * Set numeroCheque.
     *
     * @param int $numeroCheque
     *
     * @return DetalleCajaCheque
     */
    public function setNumeroCheque($numeroCheque)
    {
        $this->numeroCheque = $numeroCheque;

        return $this;
    }

    /**
     * Get numeroCheque.
     *
     * @return int
     */
    public function getNumeroCheque()
    {
        return $this->numeroCheque;
    }

    /**
     * Set monto.
     *
     * @param string $monto
     *
     * @return DetalleCajaCheque
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto.
     *
     * @return string
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set idCaja.
     *
     * @param \App\Entity\Legacy\Legacy\Caja $idCaja
     *
     * @return DetalleCajaCheque
     */
    public function setIdCaja(\App\Entity\Legacy\Legacy\Caja $idCaja)
    {
        $this->idCaja = $idCaja;

        return $this;
    }

    /**
     * Get idCaja.
     *
     * @return \App\Entity\Legacy\Legacy\Caja
     */
    public function getIdCaja()
    {
        return $this->idCaja;
    }

    /**
     * Set idBanco.
     *
     * @param \App\Entity\Legacy\Legacy\Banco $idBanco
     *
     * @return DetalleCajaCheque
     */
    public function setIdBanco(\App\Entity\Legacy\Legacy\Banco $idBanco)
    {
        $this->idBanco = $idBanco;

        return $this;
    }

    /**
     * Get idBanco.
     *
     * @return \App\Entity\Legacy\Legacy\Banco
     */
    public function getIdBanco()
    {
        return $this->idBanco;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Legacy\Legacy\Estado $idEstado
     *
     * @return DetalleCajaCheque
     */
    public function setIdEstado(\App\Entity\Legacy\Legacy\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Legacy\Legacy\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
