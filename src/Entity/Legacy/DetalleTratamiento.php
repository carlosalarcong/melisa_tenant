<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleTratamiento
 *
 * @ORM\Table(name="detalle_tratamiento")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DetalleTratamientoRepository")
 */
class DetalleTratamiento
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
     * @ORM\Column(name="CANTIDAD_TOTAL", type="integer", nullable=false)
     */
    private $cantidadTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="CANTIDAD_PAGADA", type="integer", nullable=false)
     */
    private $cantidadPagada;

    /**
     * @var int
     *
     * @ORM\Column(name="CANTIDAD_REALIZADA", type="integer", nullable=false)
     */
    private $cantidadRealizada;

    /**
     * @var \Articulo
     *
     * @ORM\ManyToOne(targetEntity="Articulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ARTICULO", referencedColumnName="ID")
     * })
     */
    private $idArticulo;

    /**
     * @var \Tratamiento
     *
     * @ORM\ManyToOne(targetEntity="Tratamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TRATAMIENTO", referencedColumnName="ID")
     * })
     */
    private $idTratamiento;

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
     * @var \AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idAccionClinica;



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
     * Set cantidadTotal.
     *
     * @param int $cantidadTotal
     *
     * @return DetalleTratamiento
     */
    public function setCantidadTotal($cantidadTotal)
    {
        $this->cantidadTotal = $cantidadTotal;

        return $this;
    }

    /**
     * Get cantidadTotal.
     *
     * @return int
     */
    public function getCantidadTotal()
    {
        return $this->cantidadTotal;
    }

    /**
     * Set cantidadPagada.
     *
     * @param int $cantidadPagada
     *
     * @return DetalleTratamiento
     */
    public function setCantidadPagada($cantidadPagada)
    {
        $this->cantidadPagada = $cantidadPagada;

        return $this;
    }

    /**
     * Get cantidadPagada.
     *
     * @return int
     */
    public function getCantidadPagada()
    {
        return $this->cantidadPagada;
    }

    /**
     * Set cantidadRealizada.
     *
     * @param int $cantidadRealizada
     *
     * @return DetalleTratamiento
     */
    public function setCantidadRealizada($cantidadRealizada)
    {
        $this->cantidadRealizada = $cantidadRealizada;

        return $this;
    }

    /**
     * Get cantidadRealizada.
     *
     * @return int
     */
    public function getCantidadRealizada()
    {
        return $this->cantidadRealizada;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Estado $idEstado
     *
     * @return DetalleTratamiento
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

    /**
     * Set idTratamiento.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Tratamiento $idTratamiento
     *
     * @return DetalleTratamiento
     */
    public function setIdTratamiento(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Tratamiento $idTratamiento)
    {
        $this->idTratamiento = $idTratamiento;

        return $this;
    }

    /**
     * Get idTratamiento.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Tratamiento
     */
    public function getIdTratamiento()
    {
        return $this->idTratamiento;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AccionClinica|null $idAccionClinica
     *
     * @return DetalleTratamiento
     */
    public function setIdAccionClinica(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AccionClinica $idAccionClinica = null)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AccionClinica|null
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * Set idArticulo.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Articulo|null $idArticulo
     *
     * @return DetalleTratamiento
     */
    public function setIdArticulo(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Articulo $idArticulo = null)
    {
        $this->idArticulo = $idArticulo;

        return $this;
    }

    /**
     * Get idArticulo.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Articulo|null
     */
    public function getIdArticulo()
    {
        return $this->idArticulo;
    }
}
