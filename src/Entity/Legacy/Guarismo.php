<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Guarismo
 *
 * @ORM\Table(name="guarismo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\GuarismoRepository")
 */
class Guarismo
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=45, nullable=false)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_PABELLON", type="boolean", nullable=false)
     */
    private $esPabellon;

    /**
     * @var int
     *
     * @ORM\Column(name="ES_CERO", type="integer", nullable=false)
     */
    private $esCero;



    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Guarismo
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * @return Guarismo
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
     * Set esPabellon.
     *
     * @param bool $esPabellon
     *
     * @return Guarismo
     */
    public function setEsPabellon($esPabellon)
    {
        $this->esPabellon = $esPabellon;

        return $this;
    }

    /**
     * Get esPabellon.
     *
     * @return bool
     */
    public function getEsPabellon()
    {
        return $this->esPabellon;
    }

    /**
     * Set esCero.
     *
     * @param int $esCero
     *
     * @return Guarismo
     */
    public function setEsCero($esCero)
    {
        $this->esCero = $esCero;

        return $this;
    }

    /**
     * Get esCero.
     *
     * @return int
     */
    public function getEsCero()
    {
        return $this->esCero;
    }
}
