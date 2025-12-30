<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonaDomicilio
 *
 * @ORM\Table(name="persona_domicilio")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PersonaDomicilioRepository")
 */
class PersonaDomicilio
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
     * @var int
     *
     * @ORM\Column(name="TIMESTAMP_FECHA", type="bigint", nullable=false)
     */
    private $timestampFecha;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_DOMICILIO", type="datetime", nullable=true)
     */
    private $fechaDomicilio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIRECCION", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RESTO_DIRECCION", type="string", length=255, nullable=true)
     */
    private $restoDireccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PERSONA", referencedColumnName="ID")
     * })
     */
    private $idPersona;

    /**
     * @var \Comuna
     *
     * @ORM\ManyToOne(targetEntity="Comuna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_COMUNA", referencedColumnName="ID")
     * })
     */
    private $idComuna;

    /**
     * @var \Pais
     *
     * @ORM\ManyToOne(targetEntity="Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAIS", referencedColumnName="ID")
     * })
     */
    private $idPais;

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
     * Set timestampFecha.
     *
     * @param int $timestampFecha
     *
     * @return PersonaDomicilio
     */
    public function setTimestampFecha($timestampFecha)
    {
        $this->timestampFecha = $timestampFecha;

        return $this;
    }

    /**
     * Get timestampFecha.
     *
     * @return int
     */
    public function getTimestampFecha()
    {
        return $this->timestampFecha;
    }

    /**
     * Set fechaDomicilio.
     *
     * @param \DateTime|null $fechaDomicilio
     *
     * @return PersonaDomicilio
     */
    public function setFechaDomicilio($fechaDomicilio = null)
    {
        $this->fechaDomicilio = $fechaDomicilio;

        return $this;
    }

    /**
     * Get fechaDomicilio.
     *
     * @return \DateTime|null
     */
    public function getFechaDomicilio()
    {
        return $this->fechaDomicilio;
    }

    /**
     * Set direccion.
     *
     * @param string|null $direccion
     *
     * @return PersonaDomicilio
     */
    public function setDireccion($direccion = null)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string|null
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set restoDireccion.
     *
     * @param string|null $restoDireccion
     *
     * @return PersonaDomicilio
     */
    public function setRestoDireccion($restoDireccion = null)
    {
        $this->restoDireccion = $restoDireccion;

        return $this;
    }

    /**
     * Get restoDireccion.
     *
     * @return string|null
     */
    public function getRestoDireccion()
    {
        return $this->restoDireccion;
    }

    /**
     * Set numero.
     *
     * @param string|null $numero
     *
     * @return PersonaDomicilio
     */
    public function setNumero($numero = null)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string|null
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set idComuna.
     *
     * @param \App\Entity\Comuna|null $idComuna
     *
     * @return PersonaDomicilio
     */
    public function setIdComuna(\App\Entity\Comuna $idComuna = null)
    {
        $this->idComuna = $idComuna;

        return $this;
    }

    /**
     * Get idComuna.
     *
     * @return \App\Entity\Comuna|null
     */
    public function getIdComuna()
    {
        return $this->idComuna;
    }

    /**
     * Set idPersona.
     *
     * @param \App\Entity\Persona|null $idPersona
     *
     * @return PersonaDomicilio
     */
    public function setIdPersona(\App\Entity\Persona $idPersona = null)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \App\Entity\Persona|null
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }

    /**
     * Set idPais.
     *
     * @param \App\Entity\Pais|null $idPais
     *
     * @return PersonaDomicilio
     */
    public function setIdPais(\App\Entity\Pais $idPais = null)
    {
        $this->idPais = $idPais;

        return $this;
    }

    /**
     * Get idPais.
     *
     * @return \App\Entity\Pais|null
     */
    public function getIdPais()
    {
        return $this->idPais;
    }
}
