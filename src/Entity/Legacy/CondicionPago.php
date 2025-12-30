<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CondicionPago
 *
 * @ORM\Table(name="condicion_pago")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\CondicionPagoRepository")
 */
class CondicionPago
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
     * @ORM\Column(name="CODIGO_INTERFAZ", type="string", length=10, nullable=false)
     */
    private $codigoInterfaz;

    /**
     * @var int
     *
     * @ORM\Column(name="PLAZO_MAXIMO", type="integer", nullable=false)
     */
    private $plazoMaximo;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_AL_DIA", type="boolean", nullable=false)
     */
    private $esAlDia = '0';

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
     * @return CondicionPago
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
     * Set codigoInterfaz.
     *
     * @param string $codigoInterfaz
     *
     * @return CondicionPago
     */
    public function setCodigoInterfaz($codigoInterfaz)
    {
        $this->codigoInterfaz = $codigoInterfaz;

        return $this;
    }

    /**
     * Get codigoInterfaz.
     *
     * @return string
     */
    public function getCodigoInterfaz()
    {
        return $this->codigoInterfaz;
    }

    /**
     * Set plazoMaximo.
     *
     * @param int $plazoMaximo
     *
     * @return CondicionPago
     */
    public function setPlazoMaximo($plazoMaximo)
    {
        $this->plazoMaximo = $plazoMaximo;

        return $this;
    }

    /**
     * Get plazoMaximo.
     *
     * @return int
     */
    public function getPlazoMaximo()
    {
        return $this->plazoMaximo;
    }

    /**
     * Set esAlDia.
     *
     * @param bool $esAlDia
     *
     * @return CondicionPago
     */
    public function setEsAlDia($esAlDia)
    {
        $this->esAlDia = $esAlDia;

        return $this;
    }

    /**
     * Get esAlDia.
     *
     * @return bool
     */
    public function getEsAlDia()
    {
        return $this->esAlDia;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return CondicionPago
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
     * @return CondicionPago
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
