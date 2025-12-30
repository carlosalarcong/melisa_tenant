<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotivoGratuidad
 *
 * @ORM\Table(name="motivo_gratuidad")
 * @ORM\Entity
 */
class MotivoGratuidad
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
     * @ORM\Column(name="NOMBRE", type="string", length=50, nullable=false)
     */
    private $nombre;

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
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;

    /**
     * @var \TipoGratuidad
     *
     * @ORM\ManyToOne(targetEntity="TipoGratuidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_GRATUIDAD", referencedColumnName="ID")
     * })
     */
    private $idTipoGratuidad;



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
     * @param string $nombre
     *
     * @return MotivoGratuidad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set idTipoGratuidad.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoGratuidad $idTipoGratuidad
     *
     * @return MotivoGratuidad
     */
    public function setIdTipoGratuidad(\Rebsol\HermesBundle\Entity\TipoGratuidad $idTipoGratuidad)
    {
        $this->idTipoGratuidad = $idTipoGratuidad;

        return $this;
    }

    /**
     * Get idTipoGratuidad.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoGratuidad
     */
    public function getIdTipoGratuidad()
    {
        return $this->idTipoGratuidad;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return MotivoGratuidad
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
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return MotivoGratuidad
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }
}
