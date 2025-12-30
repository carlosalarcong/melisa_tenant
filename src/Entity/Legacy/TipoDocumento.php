<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoDocumento
 *
 * @ORM\Table(name="tipo_documento")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoDocumentoRepository")
 */
class TipoDocumento
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_SII", type="string", length=3, nullable=true)
     */
    private $codigoSii;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=70, nullable=false)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_DTE", type="boolean", nullable=false)
     */
    private $esDte = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_LOGISTICA", type="boolean", nullable=false)
     */
    private $esLogistica = '0';

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
     * Set id.
     *
     * @param int $id
     *
     * @return TipoDocumento
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
     * Set codigoSii.
     *
     * @param string|null $codigoSii
     *
     * @return TipoDocumento
     */
    public function setCodigoSii($codigoSii = null)
    {
        $this->codigoSii = $codigoSii;

        return $this;
    }

    /**
     * Get codigoSii.
     *
     * @return string|null
     */
    public function getCodigoSii()
    {
        return $this->codigoSii;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return TipoDocumento
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
     * Set esDte.
     *
     * @param bool $esDte
     *
     * @return TipoDocumento
     */
    public function setEsDte($esDte)
    {
        $this->esDte = $esDte;

        return $this;
    }

    /**
     * Get esDte.
     *
     * @return bool
     */
    public function getEsDte()
    {
        return $this->esDte;
    }

    /**
     * Set esLogistica.
     *
     * @param bool $esLogistica
     *
     * @return TipoDocumento
     */
    public function setEsLogistica($esLogistica)
    {
        $this->esLogistica = $esLogistica;

        return $this;
    }

    /**
     * Get esLogistica.
     *
     * @return bool
     */
    public function getEsLogistica()
    {
        return $this->esLogistica;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return TipoDocumento
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa = null)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa|null
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return TipoDocumento
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
