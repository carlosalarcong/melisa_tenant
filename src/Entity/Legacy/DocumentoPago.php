<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentoPago
 *
 * @ORM\Table(name="documento_pago")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DocumentoPagoRepository")
 */
class DocumentoPago
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
     * @var bool
     *
     * @ORM\Column(name="GARANTIA", type="boolean", nullable=false)
     */
    private $garantia;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO_DOCUMENTO_GENERAL", type="string", length=30, nullable=false)
     */
    private $numeroDocumentoGeneral;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_RECEPCION_DOCUMENTO", type="datetime", nullable=false)
     */
    private $fechaRecepcionDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="MONTO_TOTAL_DOCUMENTO", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $montoTotalDocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RUT_PROPIETARIO", type="string", length=30, nullable=true)
     */
    private $rutPropietario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_PROPIETARIO", type="string", length=255, nullable=true)
     */
    private $nombrePropietario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_DOCUMENTO", type="string", length=50, nullable=true)
     */
    private $numeroDocumento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="NUMERO_VOUCHER", type="string", nullable=true)
     */
    private $numeroVoucher;

    /**
     * @var int|null
     *
     * @ORM\Column(name="TIPO_CAMBIO", type="integer", nullable=true)
     */
    private $tipoCambio;

    /**
     * @var int|null
     *
     * @ORM\Column(name="MONTO_EXTRANJERO", type="integer", nullable=true)
     */
    private $montoExtranjero;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_VENCIMIENTO", type="datetime", nullable=true)
     */
    private $fechaVencimiento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CUOTAS", type="integer", nullable=true)
     */
    private $cuotas;

    /**
     * @var int|null
     *
     * @ORM\Column(name="COPAGO_IMED", type="integer", nullable=true)
     */
    private $copagoImed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_EMPRESA", type="text", length=0, nullable=true)
     */
    private $nombreEmpresa;

    /**
     * @var int|null
     *
     * @ORM\Column(name="SEGURO_COMPLEMENTARIO", type="integer", nullable=true)
     */
    private $seguroComplementario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PATENTE_VEHICULO", type="string", length=255, nullable=true)
     */
    private $patenteVehiculo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_POLIZA", type="string", length=255, nullable=true)
     */
    private $numeroPoliza;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_POLIZA", type="datetime", nullable=true)
     */
    private $fechaPoliza;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COMISARIA", type="string", length=255, nullable=true)
     */
    private $comisaria;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_PARTE", type="string", length=255, nullable=true)
     */
    private $numeroParte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="JUZGADO_FISCALIA", type="string", length=255, nullable=true)
     */
    private $juzgadoFiscalia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO_CONTACTO", type="string", length=255, nullable=true)
     */
    private $numeroContacto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COD_AUTORIZACION", type="string", length=50, nullable=true)
     */
    private $codAutorizacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_DOCUMENTO", type="datetime", nullable=true)
     */
    private $fechaDocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ULTIMOS_4_NUMEROS", type="string", length=10, nullable=true)
     */
    private $ultimos4Numeros;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TARJETA_TIPO", type="string", length=10, nullable=true)
     */
    private $tarjetaTipo;

    /**
     * @var \DetallePagoCuenta
     *
     * @ORM\ManyToOne(targetEntity="DetallePagoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_DETALLE_PAGO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idDetallePagoCuenta;

    /**
     * @var \Banco
     *
     * @ORM\ManyToOne(targetEntity="Banco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_BANCO", referencedColumnName="ID")
     * })
     */
    private $idBanco;

    /**
     * @var \TarjetaCredito
     *
     * @ORM\ManyToOne(targetEntity="TarjetaCredito")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TARJETA_CREDITO", referencedColumnName="ID")
     * })
     */
    private $idTarjetaCredito;

    /**
     * @var \Caja
     *
     * @ORM\ManyToOne(targetEntity="Caja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CAJA", referencedColumnName="ID")
     * })
     */
    private $idCaja;

    /**
     * @var \Paciente
     *
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PACIENTE", referencedColumnName="ID")
     * })
     */
    private $idPaciente;

    /**
     * @var \AdministradorSeguro
     *
     * @ORM\ManyToOne(targetEntity="AdministradorSeguro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ADMINISTRADOR_SEGURO", referencedColumnName="ID")
     * })
     */
    private $idAdministradorSeguro;

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
     * @var \FormaPago
     *
     * @ORM\ManyToOne(targetEntity="FormaPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_FORMA_PAGO", referencedColumnName="ID")
     * })
     */
    private $idFormaPago;



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
     * Set garantia.
     *
     * @param bool $garantia
     *
     * @return DocumentoPago
     */
    public function setGarantia($garantia)
    {
        $this->garantia = $garantia;

        return $this;
    }

    /**
     * Get garantia.
     *
     * @return bool
     */
    public function getGarantia()
    {
        return $this->garantia;
    }

    /**
     * Set numeroDocumentoGeneral.
     *
     * @param string $numeroDocumentoGeneral
     *
     * @return DocumentoPago
     */
    public function setNumeroDocumentoGeneral($numeroDocumentoGeneral)
    {
        $this->numeroDocumentoGeneral = $numeroDocumentoGeneral;

        return $this;
    }

    /**
     * Get numeroDocumentoGeneral.
     *
     * @return string
     */
    public function getNumeroDocumentoGeneral()
    {
        return $this->numeroDocumentoGeneral;
    }

    /**
     * Set fechaRecepcionDocumento.
     *
     * @param \DateTime $fechaRecepcionDocumento
     *
     * @return DocumentoPago
     */
    public function setFechaRecepcionDocumento($fechaRecepcionDocumento)
    {
        $this->fechaRecepcionDocumento = $fechaRecepcionDocumento;

        return $this;
    }

    /**
     * Get fechaRecepcionDocumento.
     *
     * @return \DateTime
     */
    public function getFechaRecepcionDocumento()
    {
        return $this->fechaRecepcionDocumento;
    }

    /**
     * Set montoTotalDocumento.
     *
     * @param string $montoTotalDocumento
     *
     * @return DocumentoPago
     */
    public function setMontoTotalDocumento($montoTotalDocumento)
    {
        $this->montoTotalDocumento = $montoTotalDocumento;

        return $this;
    }

    /**
     * Get montoTotalDocumento.
     *
     * @return string
     */
    public function getMontoTotalDocumento()
    {
        return $this->montoTotalDocumento;
    }

    /**
     * Set rutPropietario.
     *
     * @param string|null $rutPropietario
     *
     * @return DocumentoPago
     */
    public function setRutPropietario($rutPropietario = null)
    {
        $this->rutPropietario = $rutPropietario;

        return $this;
    }

    /**
     * Get rutPropietario.
     *
     * @return string|null
     */
    public function getRutPropietario()
    {
        return $this->rutPropietario;
    }

    /**
     * Set nombrePropietario.
     *
     * @param string|null $nombrePropietario
     *
     * @return DocumentoPago
     */
    public function setNombrePropietario($nombrePropietario = null)
    {
        $this->nombrePropietario = $nombrePropietario;

        return $this;
    }

    /**
     * Get nombrePropietario.
     *
     * @return string|null
     */
    public function getNombrePropietario()
    {
        return $this->nombrePropietario;
    }

    /**
     * Set numeroDocumento.
     *
     * @param string|null $numeroDocumento
     *
     * @return DocumentoPago
     */
    public function setNumeroDocumento($numeroDocumento = null)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento.
     *
     * @return string|null
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set numeroVoucher.
     *
     * @param int|null $numeroVoucher
     *
     * @return DocumentoPago
     */
    public function setNumeroVoucher($numeroVoucher = null)
    {
        $this->numeroVoucher = $numeroVoucher;

        return $this;
    }

    /**
     * Get numeroVoucher.
     *
     * @return int|null
     */
    public function getNumeroVoucher()
    {
        return $this->numeroVoucher;
    }

    /**
     * Set tipoCambio.
     *
     * @param int|null $tipoCambio
     *
     * @return DocumentoPago
     */
    public function setTipoCambio($tipoCambio = null)
    {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio.
     *
     * @return int|null
     */
    public function getTipoCambio()
    {
        return $this->tipoCambio;
    }

    /**
     * Set montoExtranjero.
     *
     * @param int|null $montoExtranjero
     *
     * @return DocumentoPago
     */
    public function setMontoExtranjero($montoExtranjero = null)
    {
        $this->montoExtranjero = $montoExtranjero;

        return $this;
    }

    /**
     * Get montoExtranjero.
     *
     * @return int|null
     */
    public function getMontoExtranjero()
    {
        return $this->montoExtranjero;
    }

    /**
     * Set fechaVencimiento.
     *
     * @param \DateTime|null $fechaVencimiento
     *
     * @return DocumentoPago
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
     * Set cuotas.
     *
     * @param int|null $cuotas
     *
     * @return DocumentoPago
     */
    public function setCuotas($cuotas = null)
    {
        $this->cuotas = $cuotas;

        return $this;
    }

    /**
     * Get cuotas.
     *
     * @return int|null
     */
    public function getCuotas()
    {
        return $this->cuotas;
    }

    /**
     * Set copagoImed.
     *
     * @param int|null $copagoImed
     *
     * @return DocumentoPago
     */
    public function setCopagoImed($copagoImed = null)
    {
        $this->copagoImed = $copagoImed;

        return $this;
    }

    /**
     * Get copagoImed.
     *
     * @return int|null
     */
    public function getCopagoImed()
    {
        return $this->copagoImed;
    }

    /**
     * Set nombreEmpresa.
     *
     * @param string|null $nombreEmpresa
     *
     * @return DocumentoPago
     */
    public function setNombreEmpresa($nombreEmpresa = null)
    {
        $this->nombreEmpresa = $nombreEmpresa;

        return $this;
    }

    /**
     * Get nombreEmpresa.
     *
     * @return string|null
     */
    public function getNombreEmpresa()
    {
        return $this->nombreEmpresa;
    }

    /**
     * Set seguroComplementario.
     *
     * @param int|null $seguroComplementario
     *
     * @return DocumentoPago
     */
    public function setSeguroComplementario($seguroComplementario = null)
    {
        $this->seguroComplementario = $seguroComplementario;

        return $this;
    }

    /**
     * Get seguroComplementario.
     *
     * @return int|null
     */
    public function getSeguroComplementario()
    {
        return $this->seguroComplementario;
    }

    /**
     * Set patenteVehiculo.
     *
     * @param string|null $patenteVehiculo
     *
     * @return DocumentoPago
     */
    public function setPatenteVehiculo($patenteVehiculo = null)
    {
        $this->patenteVehiculo = $patenteVehiculo;

        return $this;
    }

    /**
     * Get patenteVehiculo.
     *
     * @return string|null
     */
    public function getPatenteVehiculo()
    {
        return $this->patenteVehiculo;
    }

    /**
     * Set numeroPoliza.
     *
     * @param string|null $numeroPoliza
     *
     * @return DocumentoPago
     */
    public function setNumeroPoliza($numeroPoliza = null)
    {
        $this->numeroPoliza = $numeroPoliza;

        return $this;
    }

    /**
     * Get numeroPoliza.
     *
     * @return string|null
     */
    public function getNumeroPoliza()
    {
        return $this->numeroPoliza;
    }

    /**
     * Set fechaPoliza.
     *
     * @param \DateTime|null $fechaPoliza
     *
     * @return DocumentoPago
     */
    public function setFechaPoliza($fechaPoliza = null)
    {
        $this->fechaPoliza = $fechaPoliza;

        return $this;
    }

    /**
     * Get fechaPoliza.
     *
     * @return \DateTime|null
     */
    public function getFechaPoliza()
    {
        return $this->fechaPoliza;
    }

    /**
     * Set comisaria.
     *
     * @param string|null $comisaria
     *
     * @return DocumentoPago
     */
    public function setComisaria($comisaria = null)
    {
        $this->comisaria = $comisaria;

        return $this;
    }

    /**
     * Get comisaria.
     *
     * @return string|null
     */
    public function getComisaria()
    {
        return $this->comisaria;
    }

    /**
     * Set numeroParte.
     *
     * @param string|null $numeroParte
     *
     * @return DocumentoPago
     */
    public function setNumeroParte($numeroParte = null)
    {
        $this->numeroParte = $numeroParte;

        return $this;
    }

    /**
     * Get numeroParte.
     *
     * @return string|null
     */
    public function getNumeroParte()
    {
        return $this->numeroParte;
    }

    /**
     * Set juzgadoFiscalia.
     *
     * @param string|null $juzgadoFiscalia
     *
     * @return DocumentoPago
     */
    public function setJuzgadoFiscalia($juzgadoFiscalia = null)
    {
        $this->juzgadoFiscalia = $juzgadoFiscalia;

        return $this;
    }

    /**
     * Get juzgadoFiscalia.
     *
     * @return string|null
     */
    public function getJuzgadoFiscalia()
    {
        return $this->juzgadoFiscalia;
    }

    /**
     * Set numeroContacto.
     *
     * @param string|null $numeroContacto
     *
     * @return DocumentoPago
     */
    public function setNumeroContacto($numeroContacto = null)
    {
        $this->numeroContacto = $numeroContacto;

        return $this;
    }

    /**
     * Get numeroContacto.
     *
     * @return string|null
     */
    public function getNumeroContacto()
    {
        return $this->numeroContacto;
    }

    /**
     * Set fechaDocumento.
     *
     * @param \DateTime|null $fechaDocumento
     *
     * @return DocumentoPago
     */
    public function setFechaDocumento($fechaDocumento = null)
    {
        $this->fechaDocumento = $fechaDocumento;

        return $this;
    }

    /**
     * Get fechaDocumento.
     *
     * @return \DateTime|null
     */
    public function getFechaDocumento()
    {
        return $this->fechaDocumento;
    }

    /**
     * Set codAutorizacion.
     *
     * @param string|null $codAutorizacion
     *
     * @return DocumentoPago
     */
    public function setCodAutorizacion($codAutorizacion = null)
    {
        $this->codAutorizacion = $codAutorizacion;

        return $this;
    }

    /**
     * Get codAutorizacion.
     *
     * @return string|null
     */
    public function getCodAutorizacion()
    {
        return $this->codAutorizacion;
    }

    /**
     * Set ultimos4Numeros.
     *
     * @param string|null $ultimos4Numeros
     *
     * @return DocumentoPago
     */
    public function setUltimos4Numeros($ultimos4Numeros = null)
    {
        $this->ultimos4Numeros = $ultimos4Numeros;

        return $this;
    }

    /**
     * Get ultimos4Numeros.
     *
     * @return string|null
     */
    public function getUltimos4Numeros()
    {
        return $this->ultimos4Numeros;
    }

    /**
     * Set tarjetaTipo.
     *
     * @param string|null $tarjetaTipo
     *
     * @return DocumentoPago
     */
    public function setTarjetaTipo($tarjetaTipo = null)
    {
        $this->tarjetaTipo = $tarjetaTipo;

        return $this;
    }

    /**
     * Get tarjetaTipo.
     *
     * @return string|null
     */
    public function getTarjetaTipo()
    {
        return $this->tarjetaTipo;
    }

    /**
     * Set idPaciente.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente $idPaciente
     *
     * @return DocumentoPago
     */
    public function setIdPaciente(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente $idPaciente)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Paciente
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set idDetallePagoCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DetallePagoCuenta $idDetallePagoCuenta
     *
     * @return DocumentoPago
     */
    public function setIdDetallePagoCuenta(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DetallePagoCuenta $idDetallePagoCuenta)
    {
        $this->idDetallePagoCuenta = $idDetallePagoCuenta;

        return $this;
    }

    /**
     * Get idDetallePagoCuenta.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\DetallePagoCuenta
     */
    public function getIdDetallePagoCuenta()
    {
        return $this->idDetallePagoCuenta;
    }

    /**
     * Set idFormaPago.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago $idFormaPago
     *
     * @return DocumentoPago
     */
    public function setIdFormaPago(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago $idFormaPago)
    {
        $this->idFormaPago = $idFormaPago;

        return $this;
    }

    /**
     * Get idFormaPago.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\FormaPago
     */
    public function getIdFormaPago()
    {
        return $this->idFormaPago;
    }

    /**
     * Set idSucursal.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Sucursal $idSucursal
     *
     * @return DocumentoPago
     */
    public function setIdSucursal(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Sucursal $idSucursal)
    {
        $this->idSucursal = $idSucursal;

        return $this;
    }

    /**
     * Get idSucursal.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Sucursal
     */
    public function getIdSucursal()
    {
        return $this->idSucursal;
    }

    /**
     * Set idBanco.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Banco|null $idBanco
     *
     * @return DocumentoPago
     */
    public function setIdBanco(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Banco $idBanco = null)
    {
        $this->idBanco = $idBanco;

        return $this;
    }

    /**
     * Get idBanco.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Banco|null
     */
    public function getIdBanco()
    {
        return $this->idBanco;
    }

    /**
     * Set idCaja.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja $idCaja
     *
     * @return DocumentoPago
     */
    public function setIdCaja(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja $idCaja  = null)
    {
        $this->idCaja = $idCaja;

        return $this;
    }

    /**
     * Get idCaja.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\Caja
     */
    public function getIdCaja()
    {
        return $this->idCaja;
    }

    /**
     * Set idTarjetaCredito.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TarjetaCredito|null $idTarjetaCredito
     *
     * @return DocumentoPago
     */
    public function setIdTarjetaCredito(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TarjetaCredito $idTarjetaCredito = null)
    {
        $this->idTarjetaCredito = $idTarjetaCredito;

        return $this;
    }

    /**
     * Get idTarjetaCredito.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\TarjetaCredito|null
     */
    public function getIdTarjetaCredito()
    {
        return $this->idTarjetaCredito;
    }

    /**
     * Set idAdministradorSeguro.
     *
     * @param \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AdministradorSeguro|null $idAdministradorSeguro
     *
     * @return DocumentoPago
     */
    public function setIdAdministradorSeguro(\App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AdministradorSeguro $idAdministradorSeguro = null)
    {
        $this->idAdministradorSeguro = $idAdministradorSeguro;

        return $this;
    }

    /**
     * Get idAdministradorSeguro.
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Legacy\Legacy\Legacy\AdministradorSeguro|null
     */
    public function getIdAdministradorSeguro()
    {
        return $this->idAdministradorSeguro;
    }
}
