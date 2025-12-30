<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormaPago
 *
 * @ORM\Table(name="forma_pago")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\FormaPagoRepository")
 */
class FormaPago
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
     * @ORM\Column(name="CODIGO", type="integer", nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE", type="string", length=40, nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="EMITE_BOLETA", type="integer", nullable=true)
     */
    private $emiteBoleta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="GARANTIA", type="integer", nullable=true)
     */
    private $garantia;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PAGO_PROFESIONAL", type="integer", nullable=true)
     */
    private $pagoProfesional;

    /**
     * @var int|null
     *
     * @ORM\Column(name="PAGO_WEB", type="integer", nullable=true)
     */
    private $pagoWeb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIPO_DOCUMENTO", type="string", length=4, nullable=true)
     */
    private $tipoDocumento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CUENTA_CONTABLE", type="integer", nullable=true)
     */
    private $cuentaContable;

    /**
     * @var bool
     *
     * @ORM\Column(name="VER_EN_CAJA", type="boolean", nullable=false)
     */
    private $verEnCaja = '0';

    /**
     * @var \FormaPago
     *
     * @ORM\ManyToOne(targetEntity="FormaPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FORMA_PAGO_PADRE", referencedColumnName="ID")
     * })
     */
    private $idFormaPagoPadre;

    /**
     * @var \FormaPagoTipo
     *
     * @ORM\ManyToOne(targetEntity="FormaPagoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_FORMA_PAGO", referencedColumnName="ID")
     * })
     */
    private $idTipoFormaPago;

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
     * Set codigo.
     *
     * @param int|null $codigo
     *
     * @return FormaPago
     */
    public function setCodigo($codigo = null)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return int|null
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return FormaPago
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

    /**
     * Set emiteBoleta.
     *
     * @param int|null $emiteBoleta
     *
     * @return FormaPago
     */
    public function setEmiteBoleta($emiteBoleta = null)
    {
        $this->emiteBoleta = $emiteBoleta;

        return $this;
    }

    /**
     * Get emiteBoleta.
     *
     * @return int|null
     */
    public function getEmiteBoleta()
    {
        return $this->emiteBoleta;
    }

    /**
     * Set garantia.
     *
     * @param int|null $garantia
     *
     * @return FormaPago
     */
    public function setGarantia($garantia = null)
    {
        $this->garantia = $garantia;

        return $this;
    }

    /**
     * Get garantia.
     *
     * @return int|null
     */
    public function getGarantia()
    {
        return $this->garantia;
    }

    /**
     * Set pagoProfesional.
     *
     * @param int|null $pagoProfesional
     *
     * @return FormaPago
     */
    public function setPagoProfesional($pagoProfesional = null)
    {
        $this->pagoProfesional = $pagoProfesional;

        return $this;
    }

    /**
     * Get pagoProfesional.
     *
     * @return int|null
     */
    public function getPagoProfesional()
    {
        return $this->pagoProfesional;
    }

    /**
     * Set pagoWeb.
     *
     * @param int|null $pagoWeb
     *
     * @return FormaPago
     */
    public function setPagoWeb($pagoWeb = null)
    {
        $this->pagoWeb = $pagoWeb;

        return $this;
    }

    /**
     * Get pagoWeb.
     *
     * @return int|null
     */
    public function getPagoWeb()
    {
        return $this->pagoWeb;
    }

    /**
     * Set tipoDocumento.
     *
     * @param string|null $tipoDocumento
     *
     * @return FormaPago
     */
    public function setTipoDocumento($tipoDocumento = null)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento.
     *
     * @return string|null
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set cuentaContable.
     *
     * @param int|null $cuentaContable
     *
     * @return FormaPago
     */
    public function setCuentaContable($cuentaContable = null)
    {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable.
     *
     * @return int|null
     */
    public function getCuentaContable()
    {
        return $this->cuentaContable;
    }

    /**
     * Set verEnCaja.
     *
     * @param bool $verEnCaja
     *
     * @return FormaPago
     */
    public function setVerEnCaja($verEnCaja)
    {
        $this->verEnCaja = $verEnCaja;

        return $this;
    }

    /**
     * Get verEnCaja.
     *
     * @return bool
     */
    public function getVerEnCaja()
    {
        return $this->verEnCaja;
    }

    /**
     * Set idTipoFormaPago.
     *
     * @param \Rebsol\HermesBundle\Entity\FormaPagoTipo|null $idTipoFormaPago
     *
     * @return FormaPago
     */
    public function setIdTipoFormaPago(\Rebsol\HermesBundle\Entity\FormaPagoTipo $idTipoFormaPago = null)
    {
        $this->idTipoFormaPago = $idTipoFormaPago;

        return $this;
    }

    /**
     * Get idTipoFormaPago.
     *
     * @return \Rebsol\HermesBundle\Entity\FormaPagoTipo|null
     */
    public function getIdTipoFormaPago()
    {
        return $this->idTipoFormaPago;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado|null $idEstado
     *
     * @return FormaPago
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
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa|null $idEmpresa
     *
     * @return FormaPago
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
     * Set idFormaPagoPadre.
     *
     * @param \Rebsol\HermesBundle\Entity\FormaPago|null $idFormaPagoPadre
     *
     * @return FormaPago
     */
    public function setIdFormaPagoPadre(\Rebsol\HermesBundle\Entity\FormaPago $idFormaPagoPadre = null)
    {
        $this->idFormaPagoPadre = $idFormaPagoPadre;

        return $this;
    }

    /**
     * Get idFormaPagoPadre.
     *
     * @return \Rebsol\HermesBundle\Entity\FormaPago|null
     */
    public function getIdFormaPagoPadre()
    {
        return $this->idFormaPagoPadre;
    }
}
