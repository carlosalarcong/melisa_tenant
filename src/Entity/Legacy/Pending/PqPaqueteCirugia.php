<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PqPaqueteCirugia
 *
 * @ORM\Table(name="pq_paquete_cirugia")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PqPaqueteCirugiaRepository")
 */
class PqPaqueteCirugia
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
     * @var string
     *
     * @ORM\Column(name="PORCENTAJE_AJUSTE", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $porcentajeAjuste;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_ANULACION", type="datetime", nullable=true)
     */
    private $fechaAnulacion;

    /**
     * @var \PqPlan
     *
     * @ORM\ManyToOne(targetEntity="PqPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PQ_PLAN", referencedColumnName="ID")
     * })
     */
    private $idPqPlan;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_ANULACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioAnulacion;

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
     * @var \AccionClinica
     *
     * @ORM\ManyToOne(targetEntity="AccionClinica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ACCION_CLINICA", referencedColumnName="ID")
     * })
     */
    private $idAccionClinica;



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
     * @return PqPaqueteCirugia
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
     * Set porcentajeAjuste.
     *
     * @param string $porcentajeAjuste
     *
     * @return PqPaqueteCirugia
     */
    public function setPorcentajeAjuste($porcentajeAjuste)
    {
        $this->porcentajeAjuste = $porcentajeAjuste;

        return $this;
    }

    /**
     * Get porcentajeAjuste.
     *
     * @return string
     */
    public function getPorcentajeAjuste()
    {
        return $this->porcentajeAjuste;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return PqPaqueteCirugia
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaAnulacion.
     *
     * @param \DateTime|null $fechaAnulacion
     *
     * @return PqPaqueteCirugia
     */
    public function setFechaAnulacion($fechaAnulacion = null)
    {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAnulacion()
    {
        return $this->fechaAnulacion;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return PqPaqueteCirugia
     */
    public function setIdUsuarioCreacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return PqPaqueteCirugia
     */
    public function setIdUsuarioAnulacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioAnulacion = null)
    {
        $this->idUsuarioAnulacion = $idUsuarioAnulacion;

        return $this;
    }

    /**
     * Get idUsuarioAnulacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioAnulacion()
    {
        return $this->idUsuarioAnulacion;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return PqPaqueteCirugia
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
     * Set idPqPlan.
     *
     * @param \Rebsol\HermesBundle\Entity\PqPlan $idPqPlan
     *
     * @return PqPaqueteCirugia
     */
    public function setIdPqPlan(\Rebsol\HermesBundle\Entity\PqPlan $idPqPlan)
    {
        $this->idPqPlan = $idPqPlan;

        return $this;
    }

    /**
     * Get idPqPlan.
     *
     * @return \Rebsol\HermesBundle\Entity\PqPlan
     */
    public function getIdPqPlan()
    {
        return $this->idPqPlan;
    }

    /**
     * Set idAccionClinica.
     *
     * @param \Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica
     *
     * @return PqPaqueteCirugia
     */
    public function setIdAccionClinica(\Rebsol\HermesBundle\Entity\AccionClinica $idAccionClinica)
    {
        $this->idAccionClinica = $idAccionClinica;

        return $this;
    }

    /**
     * Get idAccionClinica.
     *
     * @return \Rebsol\HermesBundle\Entity\AccionClinica
     */
    public function getIdAccionClinica()
    {
        return $this->idAccionClinica;
    }
}
