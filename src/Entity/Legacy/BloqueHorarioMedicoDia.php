<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BloqueHorarioMedicoDia
 *
 * @ORM\Table(name="bloque_horario_medico_dia")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\BloqueHorarioMedicoDiaRepository")
 */
class BloqueHorarioMedicoDia
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
     * @var int|null
     *
     * @ORM\Column(name="DIA", type="smallint", nullable=true)
     */
    private $dia;

    /**
     * @var \BloqueHorarioMedico
     *
     * @ORM\ManyToOne(targetEntity="BloqueHorarioMedico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BLOQUE_HORARIO_MEDICO", referencedColumnName="ID")
     * })
     */
    private $idBloqueHorarioMedico;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dia.
     *
     * @param int|null $dia
     *
     * @return BloqueHorarioMedicoDia
     */
    public function setDia($dia = null)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia.
     *
     * @return int|null
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set idBloqueHorarioMedico.
     *
     * @param \Rebsol\HermesBundle\Entity\BloqueHorarioMedico|null $idBloqueHorarioMedico
     *
     * @return BloqueHorarioMedicoDia
     */
    public function setIdBloqueHorarioMedico(\Rebsol\HermesBundle\Entity\BloqueHorarioMedico $idBloqueHorarioMedico = null)
    {
        $this->idBloqueHorarioMedico = $idBloqueHorarioMedico;

        return $this;
    }

    /**
     * Get idBloqueHorarioMedico.
     *
     * @return \Rebsol\HermesBundle\Entity\BloqueHorarioMedico|null
     */
    public function getIdBloqueHorarioMedico()
    {
        return $this->idBloqueHorarioMedico;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return BloqueHorarioMedicoDia
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
