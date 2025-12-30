<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DerivadorExterno
 *
 * @ORM\Table(name="derivador_externo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\DerivadorExternoRepository")
 */
class DerivadorExterno
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
     * @ORM\Column(name="RUT", type="integer", nullable=false)
     */
    private $rut;

    /**
     * @var string
     *
     * @ORM\Column(name="DIGITO_VERIFICADOR", type="string", length=1, nullable=false)
     */
    private $digitoVerificador;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

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
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;



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
     * Set rut.
     *
     * @param int $rut
     *
     * @return DerivadorExterno
     */
    public function setRut($rut)
    {
        $this->rut = $rut;

        return $this;
    }

    /**
     * Get rut.
     *
     * @return int
     */
    public function getRut()
    {
        return $this->rut;
    }

    /**
     * Set digitoVerificador.
     *
     * @param string $digitoVerificador
     *
     * @return DerivadorExterno
     */
    public function setDigitoVerificador($digitoVerificador)
    {
        $this->digitoVerificador = $digitoVerificador;

        return $this;
    }

    /**
     * Get digitoVerificador.
     *
     * @return string
     */
    public function getDigitoVerificador()
    {
        return $this->digitoVerificador;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return DerivadorExterno
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
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return DerivadorExterno
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set idEmpresa.
     *
     * @param \App\Entity\Empresa $idEmpresa
     *
     * @return DerivadorExterno
     */
    public function setIdEmpresa(\App\Entity\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \App\Entity\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idEstado.
     *
     * @param \App\Entity\Estado $idEstado
     *
     * @return DerivadorExterno
     */
    public function setIdEstado(\App\Entity\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \App\Entity\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \App\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return DerivadorExterno
     */
    public function setIdUsuarioCreacion(\App\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \App\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }
}
