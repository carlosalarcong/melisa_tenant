<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoAnulacion
 *
 * @ORM\Table(name="tipo_anulacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\TipoAnulacionRepository")
 */
class TipoAnulacion
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
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
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
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;

    /**
     * @var string
     *
     * @ORM\Column(name="CODE_HL7", type="string", length=15, nullable=true)
     */
    private $codeHl7;

    /**
     * @var string
     *
     * @ORM\Column(name="DISPLAY_HL7", type="string", length=100, nullable=true)
     */
    private $displayHl7;

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
     * @return TipoAnulacion
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
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return TipoAnulacion
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa
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
     * @return TipoAnulacion
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
     * @return string
     */
    public function getCodeHl7()
    {
        return $this->codeHl7;
    }

    /**
     * @param string $codeHl7
     */
    public function setCodeHl7($codeHl7)
    {
        $this->codeHl7 = $codeHl7;
    }

    /**
     * @return string
     */
    public function getDisplayHl7()
    {
        return $this->displayHl7;
    }

    /**
     * @param string $displayHl7
     */
    public function setDisplayHl7($displayHl7)
    {
        $this->displayHl7 = $displayHl7;
    }

}
