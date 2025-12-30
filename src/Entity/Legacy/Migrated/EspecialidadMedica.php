<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EspecialidadMedica
 *
 * @ORM\Table(name="especialidad_medica")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\EspecialidadMedicaRepository")
 */
class EspecialidadMedica
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
     * @ORM\Column(name="CODIGO_ESPECIALIDAD_MEDICA", type="integer", nullable=false)
     */
    private $codigoEspecialidadMedica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_IMED", type="string", length=10, nullable=true)
     */
    private $codigoImed;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_ESPECIALIDAD_MEDICA", type="string", length=60, nullable=false)
     */
    private $nombreEspecialidadMedica;

    /**
     * @var int
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=false)
     */
    private $valorDefault;

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
     * @var boolean
     *
     * @ORM\Column(name="ES_CONFIDENCIAL", type="boolean", nullable=false, options={"default":"0"})
     */
    private $esConfidencial = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="ES_AGENDABLE", type="boolean", nullable=false, options={"default"="1"})
     */
    private $esAgendable = '0';

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
     * @ORM\Column(name="SPECIALTY_HL7", type="string", length=100, nullable=true)
     */
    private $specialtyHl7;

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
     * Set codigoEspecialidadMedica.
     *
     * @param int $codigoEspecialidadMedica
     *
     * @return EspecialidadMedica
     */
    public function setCodigoEspecialidadMedica($codigoEspecialidadMedica)
    {
        $this->codigoEspecialidadMedica = $codigoEspecialidadMedica;

        return $this;
    }

    /**
     * Get codigoEspecialidadMedica.
     *
     * @return int
     */
    public function getCodigoEspecialidadMedica()
    {
        return $this->codigoEspecialidadMedica;
    }

    /**
     * Set codigoImed.
     *
     * @param string|null $codigoImed
     *
     * @return EspecialidadMedica
     */
    public function setCodigoImed($codigoImed = null)
    {
        $this->codigoImed = $codigoImed;

        return $this;
    }

    /**
     * Get codigoImed.
     *
     * @return string|null
     */
    public function getCodigoImed()
    {
        return $this->codigoImed;
    }

    /**
     * Set nombreEspecialidadMedica.
     *
     * @param string $nombreEspecialidadMedica
     *
     * @return EspecialidadMedica
     */
    public function setNombreEspecialidadMedica($nombreEspecialidadMedica)
    {
        $this->nombreEspecialidadMedica = $nombreEspecialidadMedica;

        return $this;
    }

    /**
     * Get nombreEspecialidadMedica.
     *
     * @return string
     */
    public function getNombreEspecialidadMedica()
    {
        return $this->nombreEspecialidadMedica;
    }

    /**
     * Set valorDefault.
     *
     * @param int $valorDefault
     *
     * @return EspecialidadMedica
     */
    public function setValorDefault($valorDefault)
    {
        $this->valorDefault = $valorDefault;

        return $this;
    }

    /**
     * Get valorDefault.
     *
     * @return int
     */
    public function getValorDefault()
    {
        return $this->valorDefault;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return EspecialidadMedica
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
     * @return EspecialidadMedica
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
     * Set esConfidencial
     *
     * @param boolean $esConfidencial
     * @return EspecialidadMedica
     */
    public function setEsConfidencial($esConfidencial)
    {
        $this->esConfidencial = $esConfidencial;

        return $this;
    }

    /**
     * Get esConfidencial
     *
     * @return boolean
     */
    public function getEsConfidencial()
    {
        return $this->esConfidencial;
    }

    /**
     * @return bool
     */
    public function getEsAgendable()
    {
        return $this->esAgendable;
    }

    /**
     * @param bool $esAgendable
     */
    public function setEsAgendable($esAgendable)
    {
        $this->esAgendable = $esAgendable;
    }

    /**
     * @return string
     */
    public function getSpecialtyHl7()
    {
        return $this->specialtyHl7;
    }

    /**
     * @param string $specialtyHl7
     */
    public function setSpecialtyHl7($specialtyHl7)
    {
        $this->specialtyHl7 = $specialtyHl7;
    }

}
