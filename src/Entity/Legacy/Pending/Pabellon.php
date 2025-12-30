<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pabellon
 *
 * @ORM\Table(name="pabellon")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PabellonRepository")
 */
class Pabellon
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
     * @ORM\Column(name="NOMBRE", type="string", length=150, nullable=false)
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
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SERVICIO", referencedColumnName="ID")
     * })
     */
    private $idServicio;



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
     * @return Pabellon
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
     * Set idServicio.
     *
     * @param \Rebsol\HermesBundle\Entity\Servicio $idServicio
     *
     * @return Pabellon
     */
    public function setIdServicio(\Rebsol\HermesBundle\Entity\Servicio $idServicio)
    {
        $this->idServicio = $idServicio;

        return $this;
    }

    /**
     * Get idServicio.
     *
     * @return \Rebsol\HermesBundle\Entity\Servicio
     */
    public function getIdServicio()
    {
        return $this->idServicio;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Pabellon
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
}
