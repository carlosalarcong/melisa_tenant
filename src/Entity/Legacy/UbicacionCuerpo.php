<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UbicacionCuerpo
 *
 * @ORM\Table(name="ubicacion_cuerpo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\UbicacionCuerpoRepository")
 * @Gedmo\Loggable
 */
class UbicacionCuerpo
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
     * @Gedmo\Versioned
     * @ORM\Column(name="NOMBRE_UBICACION_CUERPO", type="string", length=255, nullable=true)
     */
    private $nombreUbicacionCuerpo;

    /**
     * @var \Estado
     *
     * @Gedmo\Versioned
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
     * Set nombreUbicacionCuerpo.
     *
     * @param string|null $nombreUbicacionCuerpo
     *
     * @return UbicacionCuerpo
     */
    public function setNombreUbicacionCuerpo($nombreUbicacionCuerpo = null)
    {
        $this->nombreUbicacionCuerpo = $nombreUbicacionCuerpo;

        return $this;
    }

    /**
     * Get nombreUbicacionCuerpo.
     *
     * @return string|null
     */
    public function getNombreUbicacionCuerpo()
    {
        return $this->nombreUbicacionCuerpo;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return UbicacionCuerpo
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
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return UbicacionCuerpo
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado = null)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado|null
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }
}
