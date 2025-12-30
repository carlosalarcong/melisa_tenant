<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Origen
 *
 * @ORM\Table(name="origen")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\OrigenRepository")
 */
class Origen
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
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=255, nullable=false)
     */
    private $codigo;

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
     * @var \TipoOrigen
     *
     * @ORM\ManyToOne(targetEntity="TipoOrigen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_ORIGEN", referencedColumnName="ID")
     * })
     */
    private $idTipoOrigen;



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
     * Set codigo.
     *
     * @param string $codigo
     *
     * @return Origen
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return Origen
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
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return Origen
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

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal|null $idSucursal
     *
     * @return Origen
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal = null)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal|null
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idTipoOrigen.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoOrigen|null $idTipoOrigen
     *
     * @return Origen
     */
    public function setIdTipoOrigen(\Rebsol\HermesBundle\Entity\TipoOrigen $idTipoOrigen = null)
    {
        $this->idTipoOrigen = $idTipoOrigen;

        return $this;
    }

    /**
     * Get idTipoOrigen.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoOrigen|null
     */
    public function getIdTipoOrigen()
    {
        return $this->idTipoOrigen;
    }
}
