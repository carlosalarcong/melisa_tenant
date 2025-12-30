<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarjetaCredito
 *
 * @ORM\Table(name="tarjeta_credito")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TarjetaCreditoRepository")
 */
class TarjetaCredito
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
     * @ORM\Column(name="NOMBRE", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ABREVIACION", type="string", length=50, nullable=true)
     */
    private $abreviacion;

    /**
     * @var \TarjetaCreditoTipo
     *
     * @ORM\ManyToOne(targetEntity="TarjetaCreditoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TARJETA_CREDITO_TIPO", referencedColumnName="ID")
     * })
     */
    private $idTarjetaCreditoTipo;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return TarjetaCredito
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set abreviacion.
     *
     * @param string|null $abreviacion
     *
     * @return TarjetaCredito
     */
    public function setAbreviacion($abreviacion = null)
    {
        $this->abreviacion = $abreviacion;

        return $this;
    }

    /**
     * Get abreviacion.
     *
     * @return string|null
     */
    public function getAbreviacion()
    {
        return $this->abreviacion;
    }

    /**
     * Set idTarjetaCreditoTipo.
     *
     * @param \Rebsol\HermesBundle\Entity\TarjetaCreditoTipo|null $idTarjetaCreditoTipo
     *
     * @return TarjetaCredito
     */
    public function setIdTarjetaCreditoTipo(\Rebsol\HermesBundle\Entity\TarjetaCreditoTipo $idTarjetaCreditoTipo = null)
    {
        $this->idTarjetaCreditoTipo = $idTarjetaCreditoTipo;

        return $this;
    }

    /**
     * Get idTarjetaCreditoTipo.
     *
     * @return \Rebsol\HermesBundle\Entity\TarjetaCreditoTipo|null
     */
    public function getIdTarjetaCreditoTipo()
    {
        return $this->idTarjetaCreditoTipo;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return TarjetaCredito
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
