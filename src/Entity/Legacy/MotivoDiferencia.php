<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotivoDiferencia
 *
 * @ORM\Table(name="motivo_diferencia")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\MotivoDiferenciaRepository")
 */
class MotivoDiferencia
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
     * @ORM\Column(name="NOMBRE", type="string", length=50, nullable=false)
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
     * @var \TipoSentidoDiferencia
     *
     * @ORM\ManyToOne(targetEntity="TipoSentidoDiferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_SENTIDO_DIFERENCIA", referencedColumnName="ID")
     * })
     */
    private $idTipoSentidoDiferencia;



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
     * @return MotivoDiferencia
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
     * Set idTipoSentidoDiferencia.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoSentidoDiferencia $idTipoSentidoDiferencia
     *
     * @return MotivoDiferencia
     */
    public function setIdTipoSentidoDiferencia(\Rebsol\HermesBundle\Entity\TipoSentidoDiferencia $idTipoSentidoDiferencia)
    {
        $this->idTipoSentidoDiferencia = $idTipoSentidoDiferencia;

        return $this;
    }

    /**
     * Get idTipoSentidoDiferencia.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoSentidoDiferencia
     */
    public function getIdTipoSentidoDiferencia()
    {
        return $this->idTipoSentidoDiferencia;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return MotivoDiferencia
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
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return MotivoDiferencia
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
}
