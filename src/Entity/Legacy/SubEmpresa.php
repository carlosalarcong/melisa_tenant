<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubEmpresa
 *
 * @ORM\Table(name="sub_empresa")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\SubEmpresaRepository")
 */
class SubEmpresa
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
     * @ORM\Column(name="DV", type="string", length=1, nullable=false)
     */
    private $dv;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=45, nullable=false)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_AFECTA", type="boolean", nullable=false)
     */
    private $esAfecta = '0';

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
     * @var \Pjuridica
     *
     * @ORM\ManyToOne(targetEntity="Pjuridica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PJURIDICA", referencedColumnName="ID")
     * })
     */
    private $idPjuridica;



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
     * @return SubEmpresa
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
     * Set dv.
     *
     * @param string $dv
     *
     * @return SubEmpresa
     */
    public function setDv($dv)
    {
        $this->dv = $dv;

        return $this;
    }

    /**
     * Get dv.
     *
     * @return string
     */
    public function getDv()
    {
        return $this->dv;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return SubEmpresa
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
     * Set esAfecta.
     *
     * @param bool $esAfecta
     *
     * @return SubEmpresa
     */
    public function setEsAfecta($esAfecta)
    {
        $this->esAfecta = $esAfecta;

        return $this;
    }

    /**
     * Get esAfecta.
     *
     * @return bool
     */
    public function getEsAfecta()
    {
        return $this->esAfecta;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return SubEmpresa
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
     * @return SubEmpresa
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
     * Set idPjuridica.
     *
     * @param \Rebsol\HermesBundle\Entity\Pjuridica $idPjuridica
     *
     * @return SubEmpresa
     */
    public function setIdPjuridica(\Rebsol\HermesBundle\Entity\Pjuridica $idPjuridica)
    {
        $this->idPjuridica = $idPjuridica;

        return $this;
    }

    /**
     * Get idPjuridica.
     *
     * @return \Rebsol\HermesBundle\Entity\Pjuridica
     */
    public function getIdPjuridica()
    {
        return $this->idPjuridica;
    }

    public function __toString(){
        return $this->nombre == null ? "" : $this->nombre;
    }
}
