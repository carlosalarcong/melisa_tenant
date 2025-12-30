<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaquetePrestacionDetalle
 *
 * @ORM\Table(name="paquete_prestacion_detalle")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PaquetePrestacionDetalleRepository")
 */
class PaquetePrestacionDetalle
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
     * @var \PaquetePrestacion
     *
     * @ORM\ManyToOne(targetEntity="PaquetePrestacion") 
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAQUETE_PRESTACION", referencedColumnName="ID")
     * })
     */
    private $idPaquetePrestacion;

    /**
     * @var int
     *
     * @ORM\Column(name="CANTIDAD", type="integer", nullable=false, options={"default"="1"})
     */
    private $cantidad;

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
     * Set idPaquetePrestacion.
     *
     * @param \Rebsol\HermesBundle\Entity\PaquetePrestacion $idPaquetePrestacion
     *
     * @return PaquetePrestacionDetalle
     */
    public function setIdPaquetePrestacion(\Rebsol\HermesBundle\Entity\PaquetePrestacion $idPaquetePrestacion)
    {
        $this->idPaquetePrestacion = $idPaquetePrestacion;

        return $this;
    }

    /**
     * Get idPaquetePrestacion.
     *
     * @return \Rebsol\HermesBundle\Entity\PaquetePrestacion
     */
    public function getIdPaquetePrestacion()
    {
        return $this->idPaquetePrestacion;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica
     *
     * @return PaquetePrestacionDetalle
     */
    public function setIdAccionClinica(\Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \Rebsol\HermesBundle\Entity\AccionClinica
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return PaquetePrestacionDetalle
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Get getCantidad.
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set setCantidad.
     *
     * @param int $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

}
