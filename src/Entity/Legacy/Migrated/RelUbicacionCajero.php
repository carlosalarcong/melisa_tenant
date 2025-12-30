<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelUbicacionCajero
 *
 * @ORM\Table(name="rel_ubicacion_cajero")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\RelUbicacionCajeroRepository")
 */
class RelUbicacionCajero
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
     * @ORM\Column(name="MONTO_INICIAL", type="integer", nullable=false)
     */
    private $montoInicial;

    /**
     * @var \UbicacionCaja
     *
     * @ORM\ManyToOne(targetEntity="UbicacionCaja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UBICACION_CAJA", referencedColumnName="ID")
     * })
     */
    private $idUbicacionCaja;

    /**
     * @var \EstadoRelUbicacionCajero
     *
     * @ORM\ManyToOne(targetEntity="EstadoRelUbicacionCajero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

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
     * Set montoInicial.
     *
     * @param int $montoInicial
     *
     * @return RelUbicacionCajero
     */
    public function setMontoInicial($montoInicial)
    {
        $this->montoInicial = $montoInicial;

        return $this;
    }

    /**
     * Get montoInicial.
     *
     * @return int
     */
    public function getMontoInicial()
    {
        return $this->montoInicial;
    }

    /**
     * Set idUsuario.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UsuariosRebsol $idUsuario
     *
     * @return RelUbicacionCajero
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
     * Set idEstado.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoRelUbicacionCajero $idEstado
     *
     * @return RelUbicacionCajero
     */
    public function setIdEstado(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoRelUbicacionCajero $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\EstadoRelUbicacionCajero
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idUbicacionCaja.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UbicacionCaja $idUbicacionCaja
     *
     * @return RelUbicacionCajero
     */
    public function setIdUbicacionCaja(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UbicacionCaja $idUbicacionCaja)
    {
        $this->idUbicacionCaja = $idUbicacionCaja;

        return $this;
    }

    /**
     * Get idUbicacionCaja.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\UbicacionCaja
     */
    public function getIdUbicacionCaja()
    {
        return $this->idUbicacionCaja;
    }
}
