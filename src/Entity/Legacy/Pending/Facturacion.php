<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Facturacion
 *
 * @ORM\Table(name="facturacion")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\FacturacionRepository")
 */
class Facturacion
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
     * @ORM\Column(name="NUMERO_DOCUMENTO", type="integer", nullable=false)
     */
    private $NumeroDocumento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_FACTURACION", type="datetime", nullable=false)
     */
    private $fechaFacturacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_FACTURACION_SII", type="datetime", nullable=true)
     */
    private $fechaFacturacionSii;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_FACTURACION_COMERCIAL", type="datetime", nullable=true)
     */
    private $fechaFacturacionComercial;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_VENCIMIENTO", type="datetime", nullable=true)
     */
    private $fechaVencimiento;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CANCELACION", type="datetime", nullable=true)
     */
    private $fechaCancelacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_PAGO", type="datetime", nullable=true)
     */
    private $fechaPago;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_BONIFICADO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $TotalBonificado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TOTAL_COPAGO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $TotalCopago;

    /**
     * @var string
     *
     * @ORM\Column(name="TOTAL_FACTURACION", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $TotalFacturacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO_EXENTO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montoExento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MONTO_DIFERENCIA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montoDiferencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IVA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $iva;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TASA_IVA", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $tasaIva;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIPO_MONTO_DIFERENCIA", type="string", length=1, nullable=true)
     */
    private $tipoMontoDiferencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBSERVACION", type="text", length=0, nullable=true)
     */
    private $observacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GLOSA_DIFERENCIA", type="text", length=0, nullable=true)
     */
    private $glosaDiferencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIRECCION_DESTINO", type="string", length=100, nullable=true)
     */
    private $direccionDestino;

    /**
     * @var \TipoFacturacion
     *
     * @ORM\ManyToOne(targetEntity="TipoFacturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_FACTURACION", referencedColumnName="ID")
     * })
     */
    private $idTipoFacturacion;

    /**
     * @var \FormaPagoFacturacion
     *
     * @ORM\ManyToOne(targetEntity="FormaPagoFacturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FORMA_PAGO_FACTURACION", referencedColumnName="ID")
     * })
     */
    private $idFormaPagoFacturacion;

    /**
     * @var \Comuna
     *
     * @ORM\ManyToOne(targetEntity="Comuna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_COMUNA_DESTINO", referencedColumnName="ID")
     * })
     */
    private $idComunaDestino;

    /**
     * @var \IndicadorTraslado
     *
     * @ORM\ManyToOne(targetEntity="IndicadorTraslado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_INDICADOR_TRASLADO", referencedColumnName="ID")
     * })
     */
    private $idIndicadorTraslado;

    /**
     * @var \EstadoFacturacion
     *
     * @ORM\ManyToOne(targetEntity="EstadoFacturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_FACTURACION_SII", referencedColumnName="ID")
     * })
     */
    private $idEstadoFacturacionSii;

    /**
     * @var \RelEmpresaTipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="RelEmpresaTipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_REL_EMPRESA_TIPO_DOCUMENTO", referencedColumnName="ID")
     * })
     */
    private $idRelEmpresaTipoDocumento;

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
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PERSONA", referencedColumnName="ID")
     * })
     */
    private $idPersona;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_FACTURACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioFacturacion;

    /**
     * @var \TipoSentidoDiferencia
     *
     * @ORM\ManyToOne(targetEntity="TipoSentidoDiferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_SENTIDO_DIFERENCIA", referencedColumnName="ID")
     * })
     */
    private $idTipoSentidoDiferencia;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUCURSAL", referencedColumnName="ID")
     * })
     */
    private $idSucursal;

    /**
     * @var \EstadoFacturacion
     *
     * @ORM\ManyToOne(targetEntity="EstadoFacturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_FACTURACION_COMERCIAL", referencedColumnName="ID")
     * })
     */
    private $idEstadoFacturacionComercial;

    /**
     * @var \FormaPago
     *
     * @ORM\ManyToOne(targetEntity="FormaPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FORMA_PAGO", referencedColumnName="ID")
     * })
     */
    private $idFormaPago;

    /**
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresa;

    /**
     * @var \EstadoFacturacion
     *
     * @ORM\ManyToOne(targetEntity="EstadoFacturacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_FACTURACION", referencedColumnName="ID")
     * })
     */
    private $idEstadoFacturacion;

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
     * Set numeroDocumento.
     *
     * @param int $numeroDocumento
     *
     * @return Facturacion
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->NumeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento.
     *
     * @return int
     */
    public function getNumeroDocumento()
    {
        return $this->NumeroDocumento;
    }

    /**
     * Set fechaFacturacion.
     *
     * @param \DateTime $fechaFacturacion
     *
     * @return Facturacion
     */
    public function setFechaFacturacion($fechaFacturacion)
    {
        $this->fechaFacturacion = $fechaFacturacion;

        return $this;
    }

    /**
     * Get fechaFacturacion.
     *
     * @return \DateTime
     */
    public function getFechaFacturacion()
    {
        return $this->fechaFacturacion;
    }

    /**
     * Set fechaFacturacionSii.
     *
     * @param \DateTime|null $fechaFacturacionSii
     *
     * @return Facturacion
     */
    public function setFechaFacturacionSii($fechaFacturacionSii = null)
    {
        $this->fechaFacturacionSii = $fechaFacturacionSii;

        return $this;
    }

    /**
     * Get fechaFacturacionSii.
     *
     * @return \DateTime|null
     */
    public function getFechaFacturacionSii()
    {
        return $this->fechaFacturacionSii;
    }

    /**
     * Set fechaFacturacionComercial.
     *
     * @param \DateTime|null $fechaFacturacionComercial
     *
     * @return Facturacion
     */
    public function setFechaFacturacionComercial($fechaFacturacionComercial = null)
    {
        $this->fechaFacturacionComercial = $fechaFacturacionComercial;

        return $this;
    }

    /**
     * Get fechaFacturacionComercial.
     *
     * @return \DateTime|null
     */
    public function getFechaFacturacionComercial()
    {
        return $this->fechaFacturacionComercial;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Facturacion
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
     * @return Facturacion
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
     * Set fechaVencimiento.
     *
     * @param \DateTime|null $fechaVencimiento
     *
     * @return Facturacion
     */
    public function setFechaVencimiento($fechaVencimiento = null)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento.
     *
     * @return \DateTime|null
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * Set fechaCancelacion.
     *
     * @param \DateTime|null $fechaCancelacion
     *
     * @return Facturacion
     */
    public function setFechaCancelacion($fechaCancelacion = null)
    {
        $this->fechaCancelacion = $fechaCancelacion;

        return $this;
    }

    /**
     * Get fechaCancelacion.
     *
     * @return \DateTime|null
     */
    public function getFechaCancelacion()
    {
        return $this->fechaCancelacion;
    }

    /**
     * Set fechaPago.
     *
     * @param \DateTime|null $fechaPago
     *
     * @return Facturacion
     */
    public function setFechaPago($fechaPago = null)
    {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago.
     *
     * @return \DateTime|null
     */
    public function getFechaPago()
    {
        return $this->fechaPago;
    }

    /**
     * Set totalBonificado.
     *
     * @param string|null $totalBonificado
     *
     * @return Facturacion
     */
    public function setTotalBonificado($totalBonificado = null)
    {
        $this->TotalBonificado = $totalBonificado;

        return $this;
    }

    /**
     * Get totalBonificado.
     *
     * @return string|null
     */
    public function getTotalBonificado()
    {
        return $this->TotalBonificado;
    }

    /**
     * Set totalCopago.
     *
     * @param string|null $totalCopago
     *
     * @return Facturacion
     */
    public function setTotalCopago($totalCopago = null)
    {
        $this->TotalCopago = $totalCopago;

        return $this;
    }

    /**
     * Get totalCopago.
     *
     * @return string|null
     */
    public function getTotalCopago()
    {
        return $this->TotalCopago;
    }

    /**
     * Set totalFacturacion.
     *
     * @param string $totalFacturacion
     *
     * @return Facturacion
     */
    public function setTotalFacturacion($totalFacturacion)
    {
        $this->TotalFacturacion = $totalFacturacion;

        return $this;
    }

    /**
     * Get totalFacturacion.
     *
     * @return string
     */
    public function getTotalFacturacion()
    {
        return $this->TotalFacturacion;
    }

    /**
     * Set montoExento.
     *
     * @param string|null $montoExento
     *
     * @return Facturacion
     */
    public function setMontoExento($montoExento = null)
    {
        $this->montoExento = $montoExento;

        return $this;
    }

    /**
     * Get montoExento.
     *
     * @return string|null
     */
    public function getMontoExento()
    {
        return $this->montoExento;
    }

    /**
     * Set montoDiferencia.
     *
     * @param string|null $montoDiferencia
     *
     * @return Facturacion
     */
    public function setMontoDiferencia($montoDiferencia = null)
    {
        $this->montoDiferencia = $montoDiferencia;

        return $this;
    }

    /**
     * Get montoDiferencia.
     *
     * @return string|null
     */
    public function getMontoDiferencia()
    {
        return $this->montoDiferencia;
    }

    /**
     * Set iva.
     *
     * @param string|null $iva
     *
     * @return Facturacion
     */
    public function setIva($iva = null)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return string|null
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set tasaIva.
     *
     * @param string|null $tasaIva
     *
     * @return Facturacion
     */
    public function setTasaIva($tasaIva = null)
    {
        $this->tasaIva = $tasaIva;

        return $this;
    }

    /**
     * Get tasaIva.
     *
     * @return string|null
     */
    public function getTasaIva()
    {
        return $this->tasaIva;
    }

    /**
     * Set tipoMontoDiferencia.
     *
     * @param string|null $tipoMontoDiferencia
     *
     * @return Facturacion
     */
    public function setTipoMontoDiferencia($tipoMontoDiferencia = null)
    {
        $this->tipoMontoDiferencia = $tipoMontoDiferencia;

        return $this;
    }

    /**
     * Get tipoMontoDiferencia.
     *
     * @return string|null
     */
    public function getTipoMontoDiferencia()
    {
        return $this->tipoMontoDiferencia;
    }

    /**
     * Set observacion.
     *
     * @param string|null $observacion
     *
     * @return Facturacion
     */
    public function setObservacion($observacion = null)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion.
     *
     * @return string|null
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set glosaDiferencia.
     *
     * @param string|null $glosaDiferencia
     *
     * @return Facturacion
     */
    public function setGlosaDiferencia($glosaDiferencia = null)
    {
        $this->glosaDiferencia = $glosaDiferencia;

        return $this;
    }

    /**
     * Get glosaDiferencia.
     *
     * @return string|null
     */
    public function getGlosaDiferencia()
    {
        return $this->glosaDiferencia;
    }

    /**
     * Set direccionDestino.
     *
     * @param string|null $direccionDestino
     *
     * @return Facturacion
     */
    public function setDireccionDestino($direccionDestino = null)
    {
        $this->direccionDestino = $direccionDestino;

        return $this;
    }

    /**
     * Get direccionDestino.
     *
     * @return string|null
     */
    public function getDireccionDestino()
    {
        return $this->direccionDestino;
    }

    /**
     * Set idIndicadorTraslado.
     *
     * @param \Rebsol\HermesBundle\Entity\IndicadorTraslado|null $idIndicadorTraslado
     *
     * @return Facturacion
     */
    public function setIdIndicadorTraslado(\Rebsol\HermesBundle\Entity\IndicadorTraslado $idIndicadorTraslado = null)
    {
        $this->idIndicadorTraslado = $idIndicadorTraslado;

        return $this;
    }

    /**
     * Get idIndicadorTraslado.
     *
     * @return \Rebsol\HermesBundle\Entity\IndicadorTraslado|null
     */
    public function getIdIndicadorTraslado()
    {
        return $this->idIndicadorTraslado;
    }

    /**
     * Set idFormaPagoFacturacion.
     *
     * @param \Rebsol\HermesBundle\Entity\FormaPagoFacturacion|null $idFormaPagoFacturacion
     *
     * @return Facturacion
     */
    public function setIdFormaPagoFacturacion(\Rebsol\HermesBundle\Entity\FormaPagoFacturacion $idFormaPagoFacturacion = null)
    {
        $this->idFormaPagoFacturacion = $idFormaPagoFacturacion;

        return $this;
    }

    /**
     * Get idFormaPagoFacturacion.
     *
     * @return \Rebsol\HermesBundle\Entity\FormaPagoFacturacion|null
     */
    public function getIdFormaPagoFacturacion()
    {
        return $this->idFormaPagoFacturacion;
    }

    /**
     * Set idComunaDestino.
     *
     * @param \Rebsol\HermesBundle\Entity\Comuna|null $idComunaDestino
     *
     * @return Facturacion
     */
    public function setIdComunaDestino(\Rebsol\HermesBundle\Entity\Comuna $idComunaDestino = null)
    {
        $this->idComunaDestino = $idComunaDestino;

        return $this;
    }

    /**
     * Get idComunaDestino.
     *
     * @return \Rebsol\HermesBundle\Entity\Comuna|null
     */
    public function getIdComunaDestino()
    {
        return $this->idComunaDestino;
    }

    /**
     * Set idTipoSentidoDiferencia.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoSentidoDiferencia|null $idTipoSentidoDiferencia
     *
     * @return Facturacion
     */
    public function setIdTipoSentidoDiferencia(\Rebsol\HermesBundle\Entity\TipoSentidoDiferencia $idTipoSentidoDiferencia = null)
    {
        $this->idTipoSentidoDiferencia = $idTipoSentidoDiferencia;

        return $this;
    }

    /**
     * Get idTipoSentidoDiferencia.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoSentidoDiferencia|null
     */
    public function getIdTipoSentidoDiferencia()
    {
        return $this->idTipoSentidoDiferencia;
    }

    /**
     * Set idUsuarioFacturacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFacturacion
     *
     * @return Facturacion
     */
    public function setIdUsuarioFacturacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioFacturacion)
    {
        $this->idUsuarioFacturacion = $idUsuarioFacturacion;

        return $this;
    }

    /**
     * Get idUsuarioFacturacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioFacturacion()
    {
        return $this->idUsuarioFacturacion;
    }

    /**
     * Set idUsuarioAnulacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioAnulacion
     *
     * @return Facturacion
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
     * Set idEstadoFacturacion.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoFacturacion $idEstadoFacturacion
     *
     * @return Facturacion
     */
    public function setIdEstadoFacturacion(\Rebsol\HermesBundle\Entity\EstadoFacturacion $idEstadoFacturacion)
    {
        $this->idEstadoFacturacion = $idEstadoFacturacion;

        return $this;
    }

    /**
     * Get idEstadoFacturacion.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoFacturacion
     */
    public function getIdEstadoFacturacion()
    {
        return $this->idEstadoFacturacion;
    }

    /**
     * Set idEstadoFacturacionSii.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoFacturacion $idEstadoFacturacionSii
     *
     * @return Facturacion
     */
    public function setIdEstadoFacturacionSii(\Rebsol\HermesBundle\Entity\EstadoFacturacion $idEstadoFacturacionSii)
    {
        $this->idEstadoFacturacionSii = $idEstadoFacturacionSii;

        return $this;
    }

    /**
     * Get idEstadoFacturacionSii.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoFacturacion
     */
    public function getIdEstadoFacturacionSii()
    {
        return $this->idEstadoFacturacionSii;
    }

    /**
     * Set idEstadoFacturacionComercial.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoFacturacion $idEstadoFacturacionComercial
     *
     * @return Facturacion
     */
    public function setIdEstadoFacturacionComercial(\Rebsol\HermesBundle\Entity\EstadoFacturacion $idEstadoFacturacionComercial)
    {
        $this->idEstadoFacturacionComercial = $idEstadoFacturacionComercial;

        return $this;
    }

    /**
     * Get idEstadoFacturacionComercial.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoFacturacion
     */
    public function getIdEstadoFacturacionComercial()
    {
        return $this->idEstadoFacturacionComercial;
    }

    /**
     * Set idSucursal.
     *
     * @param \Rebsol\HermesBundle\Entity\Sucursal $idSucursal
     *
     * @return Facturacion
     */
    public function setIdSucursal(\Rebsol\HermesBundle\Entity\Sucursal $idSucursal)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \Rebsol\HermesBundle\Entity\Sucursal
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idRelEmpresaTipoDocumento.
     *
     * @param \Rebsol\HermesBundle\Entity\RelEmpresaTipoDocumento $idRelEmpresaTipoDocumento
     *
     * @return Facturacion
     */
    public function setIdRelEmpresaTipoDocumento(\Rebsol\HermesBundle\Entity\RelEmpresaTipoDocumento $idRelEmpresaTipoDocumento)
    {
        $this->idRelEmpresaTipoDocumento = $idRelEmpresaTipoDocumento;

        return $this;
    }

    /**
     * Get idRelEmpresaTipoDocumento.
     *
     * @return \Rebsol\HermesBundle\Entity\RelEmpresaTipoDocumento
     */
    public function getIdRelEmpresaTipoDocumento()
    {
        return $this->idRelEmpresaTipoDocumento;
    }

    /**
     * Set idFormaPago.
     *
     * @param \Rebsol\HermesBundle\Entity\FormaPago|null $idFormaPago
     *
     * @return Facturacion
     */
    public function setIdFormaPago(\Rebsol\HermesBundle\Entity\FormaPago $idFormaPago = null)
    {
        $this->idFormaPago = $idFormaPago;

        return $this;
    }

    /**
     * Get idFormaPago.
     *
     * @return \Rebsol\HermesBundle\Entity\FormaPago|null
     */
    public function getIdFormaPago()
    {
        return $this->idFormaPago;
    }

    /**
     * Set idPersona.
     *
     * @param \Rebsol\HermesBundle\Entity\Persona $idPersona
     *
     * @return Facturacion
     */
    public function setIdPersona(\Rebsol\HermesBundle\Entity\Persona $idPersona)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \Rebsol\HermesBundle\Entity\Persona
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }

    /**
     * Set idSubEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEmpresa|null $idSubEmpresa
     *
     * @return Facturacion
     */
    public function setIdSubEmpresa(\Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresa = null)
    {
        $this->idSubEmpresa = $idSubEmpresa;

        return $this;
    }

    /**
     * Get idSubEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEmpresa|null
     */
    public function getIdSubEmpresa()
    {
        return $this->idSubEmpresa;
    }

    /**
     * Set idTipoFacturacion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoFacturacion $idTipoFacturacion
     *
     * @return Facturacion
     */
    public function setIdTipoFacturacion(\Rebsol\HermesBundle\Entity\TipoFacturacion $idTipoFacturacion)
    {
        $this->idTipoFacturacion = $idTipoFacturacion;

        return $this;
    }

    /**
     * Get idTipoFacturacion.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoFacturacion
     */
    public function getIdTipoFacturacion()
    {
        return $this->idTipoFacturacion;
    }
}
