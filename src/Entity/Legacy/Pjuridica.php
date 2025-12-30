<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pjuridica
 *
 * @ORM\Table(name="pjuridica")
 * @ORM\Entity
 */
class Pjuridica
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
     * @var int|null
     *
     * @ORM\Column(name="ACTECO", type="integer", nullable=true)
     */
    private $acteco;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_CUENTA", type="string", length=255, nullable=true)
     */
    private $codigoCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="RAZON_SOCIAL", type="string", length=255, nullable=false)
     */
    private $razonSocial;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GIRO", type="string", length=100, nullable=true)
     */
    private $giro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_CONTACTO", type="string", length=100, nullable=true)
     */
    private $nombreContacto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_SUCURSAL", type="string", length=100, nullable=true)
     */
    private $nombreSucursal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CODIGO_SUCURSAL", type="integer", nullable=true)
     */
    private $codigoSucursal;

    /**
     * @var \Giro
     *
     * @ORM\ManyToOne(targetEntity="Giro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_GIRO", referencedColumnName="ID")
     * })
     */
    private $idGiro;

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
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \TipoPjuridica
     *
     * @ORM\ManyToOne(targetEntity="TipoPjuridica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PJURIDICA", referencedColumnName="ID")
     * })
     */
    private $idTipoPjuridica;



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
     * Set acteco.
     *
     * @param int|null $acteco
     *
     * @return Pjuridica
     */
    public function setActeco($acteco = null)
    {
        $this->acteco = $acteco;

        return $this;
    }

    /**
     * Get acteco.
     *
     * @return int|null
     */
    public function getActeco()
    {
        return $this->acteco;
    }

    /**
     * Set codigoCuenta.
     *
     * @param string|null $codigoCuenta
     *
     * @return Pjuridica
     */
    public function setCodigoCuenta($codigoCuenta = null)
    {
        $this->codigoCuenta = $codigoCuenta;

        return $this;
    }

    /**
     * Get codigoCuenta.
     *
     * @return string|null
     */
    public function getCodigoCuenta()
    {
        return $this->codigoCuenta;
    }

    /**
     * Set razonSocial.
     *
     * @param string $razonSocial
     *
     * @return Pjuridica
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial.
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set giro.
     *
     * @param string|null $giro
     *
     * @return Pjuridica
     */
    public function setGiro($giro = null)
    {
        $this->giro = $giro;

        return $this;
    }

    /**
     * Get giro.
     *
     * @return string|null
     */
    public function getGiro()
    {
        return $this->giro;
    }

    /**
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return Pjuridica
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
     * Set nombreContacto.
     *
     * @param string|null $nombreContacto
     *
     * @return Pjuridica
     */
    public function setNombreContacto($nombreContacto = null)
    {
        $this->nombreContacto = $nombreContacto;

        return $this;
    }

    /**
     * Get nombreContacto.
     *
     * @return string|null
     */
    public function getNombreContacto()
    {
        return $this->nombreContacto;
    }

    /**
     * Set nombreSucursal.
     *
     * @param string|null $nombreSucursal
     *
     * @return Pjuridica
     */
    public function setNombreSucursal($nombreSucursal = null)
    {
        $this->nombreSucursal = $nombreSucursal;

        return $this;
    }

    /**
     * Get nombreSucursal.
     *
     * @return string|null
     */
    public function getNombreSucursal()
    {
        return $this->nombreSucursal;
    }

    /**
     * Set codigoSucursal.
     *
     * @param int|null $codigoSucursal
     *
     * @return Pjuridica
     */
    public function setCodigoSucursal($codigoSucursal = null)
    {
        $this->codigoSucursal = $codigoSucursal;

        return $this;
    }

    /**
     * Get codigoSucursal.
     *
     * @return int|null
     */
    public function getCodigoSucursal()
    {
        return $this->codigoSucursal;
    }

    /**
     * Set idTipoPjuridica.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPjuridica|null $idTipoPjuridica
     *
     * @return Pjuridica
     */
    public function setIdTipoPjuridica(\Rebsol\HermesBundle\Entity\TipoPjuridica $idTipoPjuridica = null)
    {
        $this->idTipoPjuridica = $idTipoPjuridica;

        return $this;
    }

    /**
     * Get idTipoPjuridica.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPjuridica|null
     */
    public function getIdTipoPjuridica()
    {
        return $this->idTipoPjuridica;
    }

    /**
     * Set idPersona.
     *
     * @param \Rebsol\HermesBundle\Entity\Persona $idPersona
     *
     * @return Pjuridica
     */
    public function setIdPersona(\Rebsol\HermesBundle\Entity\Persona $idPersona)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \Rebsol\HermesBundle\Entity\Persona
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }

    /**
     * Set idGiro.
     *
     * @param \Rebsol\HermesBundle\Entity\Giro|null $idGiro
     *
     * @return Pjuridica
     */
    public function setIdGiro(\Rebsol\HermesBundle\Entity\Giro $idGiro = null)
    {
        $this->idGiro = $idGiro;

        return $this;
    }

    /**
     * Get idGiro.
     *
     * @return \Rebsol\HermesBundle\Entity\Giro|null
     */
    public function getIdGiro()
    {
        return $this->idGiro;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Pjuridica
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
