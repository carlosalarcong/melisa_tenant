<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObstetriciaPacienteFc
 *
 * @ORM\Table(name="obstetricia_paciente_fc")
 * @ORM\Entity
 */
class ObstetriciaPacienteFc
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=true)
     */
    private $fechaCreacion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="GESTAS", type="integer", nullable=true)
     */
    private $gestas;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PARTOS", type="integer", nullable=true)
     */
    private $partos;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ABORTOS", type="integer", nullable=true)
     */
    private $abortos;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FUR", type="datetime", nullable=true)
     */
    private $fur;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FUR_OP", type="datetime", nullable=true)
     */
    private $furOp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FPP", type="datetime", nullable=true)
     */
    private $fpp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FPC", type="datetime", nullable=true)
     */
    private $fpc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FPP_OP", type="datetime", nullable=true)
     */
    private $fppOp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FPC_OP", type="datetime", nullable=true)
     */
    private $fpcOp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_PRENATAL", type="datetime", nullable=true)
     */
    private $fechaPrenatal;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_PROFESIONAL", referencedColumnName="ID")
     * })
     */
    private $idUsuarioProfesional;

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
     * Set fechaCreacion.
     *
     * @param \DateTime|null $fechaCreacion
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFechaCreacion($fechaCreacion = null)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime|null
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set gestas.
     *
     * @param int|null $gestas
     *
     * @return ObstetriciaPacienteFc
     */
    public function setGestas($gestas = null)
    {
        $this->gestas = $gestas;

        return $this;
    }

    /**
     * Get gestas.
     *
     * @return int|null
     */
    public function getGestas()
    {
        return $this->gestas;
    }

    /**
     * Set partos.
     *
     * @param int|null $partos
     *
     * @return ObstetriciaPacienteFc
     */
    public function setPartos($partos = null)
    {
        $this->partos = $partos;

        return $this;
    }

    /**
     * Get partos.
     *
     * @return int|null
     */
    public function getPartos()
    {
        return $this->partos;
    }

    /**
     * Set abortos.
     *
     * @param int|null $abortos
     *
     * @return ObstetriciaPacienteFc
     */
    public function setAbortos($abortos = null)
    {
        $this->abortos = $abortos;

        return $this;
    }

    /**
     * Get abortos.
     *
     * @return int|null
     */
    public function getAbortos()
    {
        return $this->abortos;
    }

    /**
     * Set fur.
     *
     * @param \DateTime|null $fur
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFur($fur = null)
    {
        $this->fur = $fur;

        return $this;
    }

    /**
     * Get fur.
     *
     * @return \DateTime|null
     */
    public function getFur()
    {
        return $this->fur;
    }

    /**
     * Set furOp.
     *
     * @param \DateTime|null $furOp
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFurOp($furOp = null)
    {
        $this->furOp = $furOp;

        return $this;
    }

    /**
     * Get furOp.
     *
     * @return \DateTime|null
     */
    public function getFurOp()
    {
        return $this->furOp;
    }

    /**
     * Set fpp.
     *
     * @param \DateTime|null $fpp
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFpp($fpp = null)
    {
        $this->fpp = $fpp;

        return $this;
    }

    /**
     * Get fpp.
     *
     * @return \DateTime|null
     */
    public function getFpp()
    {
        return $this->fpp;
    }

    /**
     * Set fpc.
     *
     * @param \DateTime|null $fpc
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFpc($fpc = null)
    {
        $this->fpc = $fpc;

        return $this;
    }

    /**
     * Get fpc.
     *
     * @return \DateTime|null
     */
    public function getFpc()
    {
        return $this->fpc;
    }

    /**
     * Set fppOp.
     *
     * @param \DateTime|null $fppOp
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFppOp($fppOp = null)
    {
        $this->fppOp = $fppOp;

        return $this;
    }

    /**
     * Get fppOp.
     *
     * @return \DateTime|null
     */
    public function getFppOp()
    {
        return $this->fppOp;
    }

    /**
     * Set fpcOp.
     *
     * @param \DateTime|null $fpcOp
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFpcOp($fpcOp = null)
    {
        $this->fpcOp = $fpcOp;

        return $this;
    }

    /**
     * Get fpcOp.
     *
     * @return \DateTime|null
     */
    public function getFpcOp()
    {
        return $this->fpcOp;
    }

    /**
     * Set fechaPrenatal.
     *
     * @param \DateTime|null $fechaPrenatal
     *
     * @return ObstetriciaPacienteFc
     */
    public function setFechaPrenatal($fechaPrenatal = null)
    {
        $this->fechaPrenatal = $fechaPrenatal;

        return $this;
    }

    /**
     * Get fechaPrenatal.
     *
     * @return \DateTime|null
     */
    public function getFechaPrenatal()
    {
        return $this->fechaPrenatal;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return ObstetriciaPacienteFc
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

    /**
     * Set idUsuarioProfesional.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioProfesional
     *
     * @return ObstetriciaPacienteFc
     */
    public function setIdUsuarioProfesional(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioProfesional = null)
    {
        $this->idUsuarioProfesional = $idUsuarioProfesional;

        return $this;
    }

    /**
     * Get idUsuarioProfesional.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioProfesional()
    {
        return $this->idUsuarioProfesional;
    }
}
