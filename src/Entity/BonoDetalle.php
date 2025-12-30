<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BonoDetalle
 *
 * @ORM\Table(name="bono_detalle")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\BonoDetalleRepository")
 */
class BonoDetalle
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
     * @var string
     *
     * @ORM\Column(name="FOLIO_BONO", type="string", length=100, nullable=false)
     */
    private $folioBono;

    /**
     * @var int|AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID", nullable=true)
     * })
     */
    private $idAccionClinica;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFolioBono()
    {
        return $this->folioBono;
    }

    /**
     * @param string $folioBono
     */
    public function setFolioBono($folioBono)
    {
        $this->folioBono = $folioBono;
    }

    /**
     * @return \AccionClinica
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * @param \AccionClinica $idAccionClinica
     */
    public function setIdAccionClinica($idAccionClinica)
    {
        $this->idAccionClinica = $idAccionClinica;
    }

    /**
     * @return \PagoCuenta
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

    /**
     * @param \PagoCuenta $idPagoCuenta
     */
    public function setIdPagoCuenta($idPagoCuenta)
    {
        $this->idPagoCuenta = $idPagoCuenta;
    }

}
