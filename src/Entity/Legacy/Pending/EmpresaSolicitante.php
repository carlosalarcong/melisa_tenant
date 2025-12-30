<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpresaSolicitante
 *
 * @ORM\Table(name="empresa_solicitante")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\EmpresaSolicitanteRepository")
 */
class EmpresaSolicitante
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
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="RUT", type="string", length=20, nullable=false)
     */
    private $rut;

    /**
     * @var string
     *
     * @ORM\Column(name="DV", type="string", length=1, nullable=false)
     */
    private $dv;

    /**
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID", nullable=false)
     * })
     */
    private $idEmpresa;

    /**
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID", nullable=false)
     * })
     */
    private $idEstado;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string|null $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getRut()
    {
        return $this->rut;
    }

    /**
     * @param string $rut
     */
    public function setRut($rut)
    {
        $this->rut = $rut;
    }

    /**
     * @return string
     */
    public function getDv()
    {
        return $this->dv;
    }

    /**
     * @param string $dv
     */
    public function setDv($dv)
    {
        $this->dv = $dv;
    }

    /**
     * @return \Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * @param \Empresa $idEmpresa
     */
    public function setIdEmpresa($idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;
    }

    /**
     * @return \Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * @param \Estado $idEstado
     */
    public function setIdEstado($idEstado)
    {
        $this->idEstado = $idEstado;
    }

}
