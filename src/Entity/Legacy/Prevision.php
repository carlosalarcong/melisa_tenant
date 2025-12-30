<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prevision
 *
 * @ORM\Table(name="prevision")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\PrevisionRepository")
 */
class Prevision
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
     * @ORM\Column(name="NOMBRE_PREVISION", type="string", length=100, nullable=true)
     */
    private $nombrePrevision;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CODIGO_PREVISION", type="integer", nullable=true)
     */
    private $codigoPrevision;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_IMED", type="integer", nullable=true)
     */
    private $idImed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_INTERFAZ", type="string", length=100, nullable=true)
     */
    private $codigoInterfaz;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ABREVIADO", type="string", length=45, nullable=true)
     */
    private $nombreAbreviado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="VALOR_DEFAULT", type="integer", nullable=true)
     */
    private $valorDefault;

    /**
     * @var int|null
     *
     * @ORM\Column(name="COPAGO", type="integer", nullable=true)
     */
    private $copago;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ICONO", type="string", length=45, nullable=true)
     */
    private $icono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_ITEM_PRESUPUESTARIO", type="string", length=5, nullable=true)
     */
    private $codigoItemPresupuestario;

    /**
     * @var int
     *
     * @ORM\Column(name="TIPO_PRESTACION", type="integer", nullable=false)
     */
    private $tipoPrestacion;

    /**
     * @var \TipoPrevision
     *
     * @ORM\ManyToOne(targetEntity="TipoPrevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_PREVISION", referencedColumnName="ID")
     * })
     */
    private $idTipoPrevision;

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
     * @var \Pjuridica
     *
     * @ORM\ManyToOne(targetEntity="Pjuridica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PJURIDICA", referencedColumnName="ID")
     * })
     */
    private $idPjuridica;

    /**
     * @var string
     *
     * @ORM\Column(name="PREVISION_HL7", type="string", length=30, nullable=true)
     */
    private $previsionHl7;

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
     * Set nombrePrevision.
     *
     * @param string|null $nombrePrevision
     *
     * @return Prevision
     */
    public function setNombrePrevision($nombrePrevision = null)
    {
        $this->nombrePrevision = $nombrePrevision;

        return $this;
    }

    /**
     * Get nombrePrevision.
     *
     * @return string|null
     */
    public function getNombrePrevision()
    {
        return $this->nombrePrevision;
    }

    /**
     * Set codigoPrevision.
     *
     * @param int|null $codigoPrevision
     *
     * @return Prevision
     */
    public function setCodigoPrevision($codigoPrevision = null)
    {
        $this->codigoPrevision = $codigoPrevision;

        return $this;
    }

    /**
     * Get codigoPrevision.
     *
     * @return int|null
     */
    public function getCodigoPrevision()
    {
        return $this->codigoPrevision;
    }

    /**
     * Set idImed.
     *
     * @param int|null $idImed
     *
     * @return Prevision
     */
    public function setIdImed($idImed = null)
    {
        $this->idImed = $idImed;

        return $this;
    }

    /**
     * Get idImed.
     *
     * @return int|null
     */
    public function getIdImed()
    {
        return $this->idImed;
    }

    /**
     * Set codigoInterfaz.
     *
     * @param string|null $codigoInterfaz
     *
     * @return Prevision
     */
    public function setCodigoInterfaz($codigoInterfaz = null)
    {
        $this->codigoInterfaz = $codigoInterfaz;

        return $this;
    }

    /**
     * Get codigoInterfaz.
     *
     * @return string|null
     */
    public function getCodigoInterfaz()
    {
        return $this->codigoInterfaz;
    }

    /**
     * Set nombreAbreviado.
     *
     * @param string|null $nombreAbreviado
     *
     * @return Prevision
     */
    public function setNombreAbreviado($nombreAbreviado = null)
    {
        $this->nombreAbreviado = $nombreAbreviado;

        return $this;
    }

    /**
     * Get nombreAbreviado.
     *
     * @return string|null
     */
    public function getNombreAbreviado()
    {
        return $this->nombreAbreviado;
    }

    /**
     * Set copago.
     *
     * @param int|null $copago
     *
     * @return Prevision
     */
    public function setCopago($copago = null)
    {
        $this->copago = $copago;

        return $this;
    }

    /**
     * Get copago.
     *
     * @return int|null
     */
    public function getCopago()
    {
        return $this->copago;
    }

    /**
     * Set valorDefault.
     *
     * @param int|null $valorDefault
     *
     * @return Prevision
     */
    public function setValorDefault($valorDefault = null)
    {
        $this->valorDefault = $valorDefault;

        return $this;
    }

    /**
     * Get valorDefault.
     *
     * @return int|null
     */
    public function getValorDefault()
    {
        return $this->valorDefault;
    }

    /**
     * Set icono.
     *
     * @param string|null $icono
     *
     * @return Prevision
     */
    public function setIcono($icono = null)
    {
        $this->icono = $icono;

        return $this;
    }

    /**
     * Get icono.
     *
     * @return string|null
     */
    public function getIcono()
    {
        return $this->icono;
    }

    /**
     * Set codigoItemPresupuestario.
     *
     * @param string|null $codigoItemPresupuestario
     *
     * @return Prevision
     */
    public function setCodigoItemPresupuestario($codigoItemPresupuestario = null)
    {
        $this->codigoItemPresupuestario = $codigoItemPresupuestario;

        return $this;
    }

    /**
     * Get codigoItemPresupuestario.
     *
     * @return string|null
     */
    public function getCodigoItemPresupuestario()
    {
        return $this->codigoItemPresupuestario;
    }

    /**
     * Set tipoPrestacion.
     *
     * @param int $tipoPrestacion
     *
     * @return Prevision
     */
    public function setTipoPrestacion($tipoPrestacion)
    {
        $this->tipoPrestacion = $tipoPrestacion;

        return $this;
    }

    /**
     * Get tipoPrestacion.
     *
     * @return int
     */
    public function getTipoPrestacion()
    {
        return $this->tipoPrestacion;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return Prevision
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
     * @return Prevision
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
     * Set idTipoPrevision.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoPrevision|null $idTipoPrevision
     *
     * @return Prevision
     */
    public function setIdTipoPrevision(\Rebsol\HermesBundle\Entity\TipoPrevision $idTipoPrevision = null)
    {
        $this->idTipoPrevision = $idTipoPrevision;

        return $this;
    }

    /**
     * Get idTipoPrevision.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoPrevision|null
     */
    public function getIdTipoPrevision()
    {
        return $this->idTipoPrevision;
    }

    /**
     * Set idPjuridica.
     *
     * @param \Rebsol\HermesBundle\Entity\Pjuridica|null $idPjuridica
     *
     * @return Prevision
     */
    public function setIdPjuridica(\Rebsol\HermesBundle\Entity\Pjuridica $idPjuridica = null)
    {
        $this->idPjuridica = $idPjuridica;

        return $this;
    }

    /**
     * Get idPjuridica.
     *
     * @return \Rebsol\HermesBundle\Entity\Pjuridica|null
     */
    public function getIdPjuridica()
    {
        return $this->idPjuridica;
    }

    /**
     * @return string
     */
    public function getPrevisionHl7()
    {
        return $this->previsionHl7;
    }

    /**
     * @param string $previsionHl7
     */
    public function setPrevisionHl7($previsionHl7)
    {
        $this->previsionHl7 = $previsionHl7;
    }

}
