<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Raza
 *
 * @ORM\Table(name="raza")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\RazaRepository")
 */
class Raza
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
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \Especie
     *
     * @ORM\ManyToOne(targetEntity="Especie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESPECIE", referencedColumnName="ID")
     * })
     */
    private $idEspecie;



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
     * @return Raza
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
     * @return Raza
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
     * Set idEspecie.
     *
     * @param \Rebsol\HermesBundle\Entity\Especie|null $idEspecie
     *
     * @return Raza
     */
    public function setIdEspecie(\Rebsol\HermesBundle\Entity\Especie $idEspecie = null)
    {
        $this->idEspecie = $idEspecie;

        return $this;
    }

    /**
     * Get idEspecie.
     *
     * @return \Rebsol\HermesBundle\Entity\Especie|null
     */
    public function getIdEspecie()
    {
        return $this->idEspecie;
    }
}
