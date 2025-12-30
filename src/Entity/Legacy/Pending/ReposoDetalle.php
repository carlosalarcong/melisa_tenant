<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReposoDetalle
 *
 * @ORM\Table(name="reposo_detalle")
 * @ORM\Entity
 */
class ReposoDetalle
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
     * @ORM\Column(name="NOMBRE", type="string", length=100, nullable=false)
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
     * @var \Reposo
     *
     * @ORM\ManyToOne(targetEntity="Reposo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REPOSO", referencedColumnName="ID")
     * })
     */
    private $idReposo;



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
     * @return ReposoDetalle
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
     * Set idReposo.
     *
     * @param \Rebsol\HermesBundle\Entity\Reposo $idReposo
     *
     * @return ReposoDetalle
     */
    public function setIdReposo(\Rebsol\HermesBundle\Entity\Reposo $idReposo)
    {
        $this->idReposo = $idReposo;

        return $this;
    }

    /**
     * Get idReposo.
     *
     * @return \Rebsol\HermesBundle\Entity\Reposo
     */
    public function getIdReposo()
    {
        return $this->idReposo;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return ReposoDetalle
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
