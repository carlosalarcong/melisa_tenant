<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubEspecialidadMedica
 *
 * @ORM\Table(name="sub_especialidad_medica")
 * @ORM\Entity
 */
class SubEspecialidadMedica
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
     * @ORM\Column(name="NOMBRE_SUB_ESPECIALIDAD_MEDICA", type="string", length=45, nullable=true)
     */
    private $nombreSubEspecialidadMedica;

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
     * @var \EspecialidadMedica
     *
     * @ORM\ManyToOne(targetEntity="EspecialidadMedica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESPECIALIDAD_MEDICA", referencedColumnName="ID")
     * })
     */
    private $idEspecialidadMedica;



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
     * Set nombreSubEspecialidadMedica.
     *
     * @param string|null $nombreSubEspecialidadMedica
     *
     * @return SubEspecialidadMedica
     */
    public function setNombreSubEspecialidadMedica($nombreSubEspecialidadMedica = null)
    {
        $this->nombreSubEspecialidadMedica = $nombreSubEspecialidadMedica;

        return $this;
    }

    /**
     * Get nombreSubEspecialidadMedica.
     *
     * @return string|null
     */
    public function getNombreSubEspecialidadMedica()
    {
        return $this->nombreSubEspecialidadMedica;
    }

    /**
     * Set idEspecialidadMedica.
     *
     * @param \Rebsol\HermesBundle\Entity\EspecialidadMedica|null $idEspecialidadMedica
     *
     * @return SubEspecialidadMedica
     */
    public function setIdEspecialidadMedica(\Rebsol\HermesBundle\Entity\EspecialidadMedica $idEspecialidadMedica = null)
    {
        $this->idEspecialidadMedica = $idEspecialidadMedica;

        return $this;
    }

    /**
     * Get idEspecialidadMedica.
     *
     * @return \Rebsol\HermesBundle\Entity\EspecialidadMedica|null
     */
    public function getIdEspecialidadMedica()
    {
        return $this->idEspecialidadMedica;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return SubEspecialidadMedica
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
