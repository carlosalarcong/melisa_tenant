<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoDiagnosticoUrgencia
 *
 * @ORM\Table(name="estado_diagnostico_urgencia")
 * @ORM\Entity
 */
class EstadoDiagnosticoUrgencia
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
     * @ORM\Column(name="NOMBRE", type="string", length=45, nullable=true)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="NO_GES", type="boolean", nullable=false,options={"default": 0})
     */
    private $noGes = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_GES", type="boolean", nullable=false,options={"default": 0})
     */
    private $esGes = '0';

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
     * Set id.
     *
     * @param int $id
     *
     * @return EstadoDiagnosticoUrgencia
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * @param string|null $nombre
     *
     * @return EstadoDiagnosticoUrgencia
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * @return bool|string
     */
    public function getNoGes()
    {
        return $this->noGes;
    }

    /**
     * @param bool|string $noGes
     */
    public function setNoGes($noGes)
    {
        $this->noGes = $noGes;
    }

    /**
     * @return bool|string
     */
    public function getEsGes()
    {
        return $this->esGes;
    }

    /**
     * @param bool|string $esGes
     */
    public function setEsGes($esGes)
    {
        $this->esGes = $esGes;
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
