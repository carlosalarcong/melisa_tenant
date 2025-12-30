<?php

namespace Rebsol\CajaBundle\Controller\Api\Unab\PagoCuenta;


use Rebsol\CajaBundle\Controller\Api\Unab\PagoCuenta\PagoCuentaController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaKccType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaChipType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\ResultadoBusqueda\DirectorioPacienteDuenoAddType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\ResultadoBusqueda\DirectorioPacienteMascotaAddType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\ResultadoBusqueda\DirectorioPacienteBusquedaAvanzadaType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\PagoType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\MediosPagoType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\OtrosMediosPagoType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\BusquedaAvanzadaDirectorioPacienteType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\PrestacionType;
use Rebsol\HermesBundle\Form\Type\Caja\Recaudacion\Pago\DiferenciaType;

use Rebsol\HermesBundle\Entity\CuentaPaciente;
use Rebsol\HermesBundle\Entity\CuentaPacienteLog;
use Rebsol\HermesBundle\Entity\DocumentoPago;
use Rebsol\HermesBundle\Entity\PagoCuenta;
use Rebsol\HermesBundle\Entity\DetallePagoCuenta;


/**
 * Class  RealizarPagoCuentaPacienteController
 * @package  \Rebsol\CajaBundle\Controller\Api\Unab\PagoCuenta
 * @author   Nombre del Autor
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  10/12/15  ]
 * Fecha de Actualización: [ ]
 */
class RealizarPagoCuentaPacienteController extends PagoCuentaController {

	var $obtenerSumaMontos          = 0;

	protected function rPaciente() {
		return $this->getDoctrine()->getRepository('RebsolHermesBundle:Paciente');
	}

	/**
	 * [cuentaPagarMediosAction description]
	 * @return jsonResponse
	 */
	public function cuentaPagarMediosAction(Request $request) {

		$session              = $session = $request->getSession();
		$em                   = $this->getDoctrine()->getManager();

		$oEmpresa             = $this->ObtenerEmpresaLogin();
		$oUser                = $this->getUser();
		$idPaciente           = $session->get('caja.idPaciente');

		$oPaciente            = !empty($idPaciente) ? $em->getRepository('RebsolHermesBundle:Paciente')->find($idPaciente) : null;

		if ($oPaciente !== null ) {
			$idConvenio           = ($oPaciente->getIdConvenio() !== null) ? $oPaciente->getIdConvenio()->getId() : null;
		}

		$oConvenio            = !empty($idConvenio) ? $em->getRepository('RebsolHermesBundle:Prevision')->find($idConvenio) : null;

		$auxAfecta            = 0;
		$auxExenta            = 0;
		$auxMedioPago         = 0;
		$idCantidad           = 20;
		$auxSumaBonos         = 0;
		$auxBonoCount         = 0;
		$arrayOtrosFormasPago = array();
		$arrayFormasPago      = array();
		$arrayTodasFormasPago = array();
		$oFecha               = new \DateTime('now');
		$totalCuenta          = 0;

		if($session->get('caja') != null){
			$this->get('session')->set('caja.ubicacionCajero', $session->get('caja'));
		}

		$oCaja                     = $em->getRepository('RebsolHermesBundle:Caja')->find($session->get('caja.ubicacionCajero'));
		$oInterfazImed             = ( $this->getSession('idInterfazImed') != null ) ? $em->getRepository('RebsolHermesBundle:InterfazImed')->find($this->getSession('idInterfazImed')) : null;
		$unserializeArrayFormaPago = ( $oInterfazImed != null ) ? unserialize( $oInterfazImed->getListaForPag() ) : null;
		$garantia                  =  1;

		/* ESTADOS */
		$estadoActivo                   = $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo');
		$estadoPagoActiva               = $this->obtenerServicioGlobales()->obtenerEstado('EstadoPagoActiva');
		$estadoPagoGarantia             = $this->obtenerServicioGlobales()->obtenerEstado('EstadoPagoGarantia');
		$estadoCuentaCerradaPagada      = $this->obtenerServicioGlobales()->obtenerEstado('EstadoCuentaCerradaPagadaTotal');
		$cerradaPagadaConSaldoPendiente = $em->getReference('RebsolHermesBundle:EstadoCuenta', $this->container->getParameter('EstadoCuenta.cerradaPagadaConSaldoPendiente'));
		$estadoGarantiaRegularizada     = $this->obtenerServicioGlobales()->obtenerEstado('EstadoPagoRegularizada');

		/* USO REPOSITORIO */
		$idFormaspago              = $this->obtenerServicioGlobales()->rPagoCuenta()->Formaspago($estadoActivo, $oEmpresa);

		$oPnatural = null;

		if( $oPaciente  !== null) {
			$oPnatural          = $oPaciente->getIdPnatural()->getId();
		}

		/**
		* [$oPnatural Posición de hay array en 0
		* (Búsqueda de Ids por Todos las macotas de un Cliente getIdPnaturalPadre()->getId() ) ]
		* @var [type]
		*/
		if($this->obtenerServicioGlobales()->obtenerEstado('EstadoApi') === 'Api1'){

			$oPnatural                 = $em->getRepository('RebsolHermesBundle:RelPnaturalParentesco')->findBy(array(
				'idPnaturalHijo' => $oPnaturalMascota
				)
			)[0]->getIdPnaturalPadre()->getId();

		}

		$evento = null;

		if( $oPnatural !== null ) {
			$evento                    = ($oPnatural) ? $this->rPaciente()->obtenerEventos($oPnatural) : $this->rPaciente()->obtenerEventos($oPnatural);
		}

		foreach ($idFormaspago as $valorIdFormasPago) {
			($valorIdFormasPago['gr'] == 0) ? $arrayFormasPago[ ] = $valorIdFormasPago['id'] : $arrayOtrosFormasPago[ ] = $valorIdFormasPago['id'];
			$arrayTodasFormasPago[ ]  = $valorIdFormasPago['id'];
		}


		$parametrosArrayIds = [
		'idUsuario' => $oUser,
		'idEstado'  => $estadoActivo
		];

		$oUbicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy($parametrosArrayIds);
		$oFinanciador     = $em->getRepository('RebsolHermesBundle:Prevision')->find($oPaciente->getIdFinanciador()->getId());
		$oSucursal        = ($this->getSession('sucursal'))?$em->getRepository('RebsolHermesBundle:Sucursal')->find($this->getSession('sucursal')):null;

		$mediosPagoParametrosArray = [
		'validaform'       => null,
		'idFrom'           => $arrayTodasFormasPago,
		'idFromOtros'      => $arrayOtrosFormasPago,
		'idCantidad'       => $idCantidad,
		'clone'            => false,
		'nuevo'            => true,
		'sucursal'         => $oUbicacionCajero->getIdUbicacionCaja()->getIdSucursal()->getId(),
		'iEmpresa'         => $oEmpresa->getId(),
		'estado_activado'  => $estadoActivo,
		];

		$mediosPagoform = $this->createForm(MediosPagoType::class, null, $mediosPagoParametrosArray );
		$mediosPagoform->handleRequest($request);
		$auxxx          = $request->get('rebsol_hermesbundle_MediosPagoType');

		$auxEmisionBoleta                       = 0;
		$countEmisionBoleta                     = 0;
		$montoRestaBoletaMedioPagoNoEmiteBoleta = 0;
		$idCantidad                             = 0;

		$idDatoIngreso = $this->getSession('caja.inputIdDatoIngreso');
		$saldoMonto    = (int)$this->getSession('caja.inputMonto');

		$oCuentaPaciente = $em->getRepository('RebsolHermesBundle:CuentaPaciente')->findOneBy( array(
			'idPaciente' => $oPaciente
			)
		);

		if( $saldoMonto === 0 ) {

			$oCuentaPaciente->setIdEstadoCuenta($estadoCuentaCerradaPagada);
			$oCuentaPaciente->setIdPaciente($oPaciente);
			$oCuentaPaciente->setSaldoCuenta(0);

			$descuento = $oCuentaPaciente->getTotalDescuento();
			$descuento = $descuento + $request->get( 'descuento_cuenta_paciente' );
			$oCuentaPaciente->setTotalDescuento( $descuento );

			$em->persist($oCuentaPaciente);

			$oPagoCuenta = new PagoCuenta();
			$oPagoCuenta->setIdPaciente($oPaciente);

			($garantia == 0 ) ? $oPagoCuenta->setIdEstadoPago($estadoPagoActiva) : $oPagoCuenta->setIdEstadoPago( $estadoPagoGarantia );
			$oPagoCuenta->setIdCuentaPaciente($oCuentaPaciente);
			$oPagoCuenta->setIdCaja($oCaja);
			$oPagoCuenta->setIdUsuario($oUser);
			$oPagoCuenta->setFechaPago($oFecha);
			$oPagoCuenta->setNumeroDocumento(NULL);
			$oPagoCuenta->setImpuesto(NULL);
			$oPagoCuenta->setMonto(0);
			$oPagoCuenta->setIdSubEmpresa(NULL);

			$em->persist($oPagoCuenta);

			$oCuentaPacienteLog = new CuentaPacienteLog();
			$oCuentaPacienteLog->setSaldoCuenta(0);
			$oCuentaPacienteLog->setFechaEstadoCuenta($oFecha);
			$oCuentaPacienteLog->setNumeroAccion($evento);
			$oCuentaPacienteLog->setIdCuenta($oCuentaPaciente);
			$oCuentaPacienteLog->setIdEstadoCuenta($estadoCuentaCerradaPagada);
			$oCuentaPacienteLog->setIdUsuario($oUser);
			$oCuentaPacienteLog->setIdPaciente($oPaciente);

			$em->persist($oCuentaPacienteLog);

		} else {

			$oCuentaPaciente->setIdEstadoCuenta($cerradaPagadaConSaldoPendiente);
			$oCuentaPaciente->setIdPaciente($oPaciente);
			$oCuentaPaciente->setSaldoCuenta($saldoMonto);

			$descuento = $oCuentaPaciente->getTotalDescuento();
			$descuento = $descuento + $request->get( 'descuento_cuenta_paciente' );
			$oCuentaPaciente->setTotalDescuento( $descuento );

			$em->persist($oCuentaPaciente);

			$oPagoCuenta = $em->getRepository('RebsolHermesBundle:PagoCuenta')->findOneBy(array(
				'idPaciente' => $idPaciente
				)
			);

			$oPagoCuenta->setIdEstadoPago($estadoGarantiaRegularizada);
			$em->persist($oPagoCuenta);

			$oPagoCuenta = new PagoCuenta();
			$oPagoCuenta->setIdPaciente($oPaciente);
			$oPagoCuenta->setIdEstadoPago( $estadoGarantiaRegularizada );
			$oPagoCuenta->setIdCuentaPaciente($oCuentaPaciente);
			$oPagoCuenta->setIdCaja($oCaja);
			$oPagoCuenta->setIdUsuario($oUser);
			$oPagoCuenta->setFechaPago($oFecha);
			$oPagoCuenta->setNumeroDocumento(NULL);
			$oPagoCuenta->setImpuesto(NULL);
			$oPagoCuenta->setMonto($saldoMonto);
			$oPagoCuenta->setIdSubEmpresa(NULL);
			$em->persist($oPagoCuenta);

			$oCuentaPacienteLog = new CuentaPacienteLog();
			$oCuentaPacienteLog->setSaldoCuenta($saldoMonto);
			$oCuentaPacienteLog->setFechaEstadoCuenta($oFecha);
			$oCuentaPacienteLog->setNumeroAccion($evento);
			$oCuentaPacienteLog->setIdCuenta($oCuentaPaciente);
			$oCuentaPacienteLog->setIdEstadoCuenta($cerradaPagadaConSaldoPendiente);
			$oCuentaPacienteLog->setIdUsuario($oUser);
			$oCuentaPacienteLog->setIdPaciente($oPaciente);
			$em->persist($oCuentaPacienteLog);

		}

		if($garantia == 1){

			foreach ($arrayOtrosFormasPago as $idForm){

				if ($mediosPagoform['medioPago_' . $idForm]->getData()){

					$oFormasPago     = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);
					$oFormasPagoTipo = $oFormasPago->getIdTipoFormaPago()->getId();
					$bonoElectronico = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoElectronico'));
					$bonoManual      = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoManual'));
					$chequeFecha     = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeFecha'));
					$chequeDia       = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeDia'));

					if ( $bonoElectronico  || $bonoManual || $chequeFecha || $chequeDia ){

						/** FORMA DE PAGO SENSILLA Y QUE NO ES DINAMICA EN MULTIPLES FORMULARIOS */
						/** EFECTIVO */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.Efectivo')){

							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);

							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);
						}
						/** GRATUIDAD */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.Gratuidad')){

							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);

							/** Obtiene Objeto de Gratuidad */
							$oTipoGratuidad   = $em->getRepository('RebsolHermesBundle:TipoGratuidad')->find($mediosPagoform['idGratuidad']->getData());
							$oMotivoGratuidad = $em->getRepository('RebsolHermesBundle:MotivoGratuidad')->findOneBy(array("idTipoGratuidad" => $oTipoGratuidad));

							/** Obtiene Objeto de Gratuidad */
							$oDetallePagoCuenta->setIdMotivoGratuidad($oMotivoGratuidad);
							$em->persist($oDetallePagoCuenta);
						}

						/** CREDITO */
						if ($oFormasPagoTipo ===  $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.TarjetaCredito')){
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral(0);
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')->find($mediosPagoform['TarjetaCredito']->getData());
							$oDocumentoPago->setIdTarjetaCredito($oTarjetaCredito);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}

						/** DEBITO */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.TarjetaDebito')){
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral(0);
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($mediosPagoform['TarjetaDebito__'. $idForm. '_0']->getData());
							$oDocumentoPago->setIdBanco($oBanco);
							$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}

						/** LASIK */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ConvenioLasik'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['folio_' . $idForm]->getData());
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm]->getData());
							$oDocumentoPago->setNumeroVoucher(NULL);
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}
						/** IMED */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ConvenioImed'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['folio_' . $idForm]->getData());
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm]->getData());
							$oDocumentoPago->setNumeroVoucher(NULL);
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}
					}

					$bonoElectronicoUno = ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoElectronico'));
					$bonoManualUno      = ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoManual'));
					$chequeFechaUno     = ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeFecha'));
					$chequeDiaUno       = ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeDia'));

					if ( $bonoElectronicoUno  || $bonoManualUno || $chequeFechaUno || $chequeDiaUno) {
						/** DOCUMENTOS QUE PUEDEN TENER VARIOS PAGOS EN UNA SOLA FORMA DE PAGO */
						/** CHEQUE FECHA */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeFecha'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();

							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDocumentoPago = new DocumentoPago();
								$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
								$oDocumentoPago->setIdPaciente($oPaciente);
								$oDocumentoPago->setIdCaja($oCaja);
								$oDocumentoPago->setGarantia($garantia);
								$oDocumentoPago->setIdFormaPago($oFormasPago);
								$oDocumentoPago->setNumeroDocumentoGeneral(0);
								$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
								$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $mediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
								$oDocumentoPago->setIdBanco($oBanco);
								$s = $mediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
								$s = str_replace('.', '', $s);
								$s = str_replace('-', '', $s);
								$oDocumentoPago->setRutPropietario($s);
								$oDocumentoPago->setNombrePropietario($mediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroDocumento($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setIdSucursal($oSucursal);
								$em->persist($oDocumentoPago);


								$oDetalleDocumentoPago = new DetalleDocumentoPago();
								$oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
								$oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
								$oDetalleDocumentoPago->setMontoDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
								$oDetalleDocumentoPago->setNumeroDocumentoDetalle($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());



								$oCondicionPago = $em->getRepository('RebsolHermesBundle:CondicionPago')->find($auxxx['condicion_' . $idForm . '_' . $cant]);
								$oDetalleDocumentoPago->setidCondicionPago($oCondicionPago);
								$em->persist($oDetalleDocumentoPago);
							}
						}

						/** CHEQUE DIA */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeDia'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();

							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDocumentoPago = new DocumentoPago();
								$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
								$oDocumentoPago->setIdPaciente($oPaciente);
								$oDocumentoPago->setIdCaja($oCaja);
								$oDocumentoPago->setGarantia($garantia);
								$oDocumentoPago->setIdFormaPago($oFormasPago);
								$oDocumentoPago->setNumeroDocumentoGeneral(0);
								$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
								$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $mediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
								$oDocumentoPago->setIdBanco($oBanco);
								$s = $mediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
								$s = str_replace('.', '', $s);
								$s = str_replace('-', '', $s);
								$oDocumentoPago->setRutPropietario($s);
								$oDocumentoPago->setNombrePropietario($mediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroDocumento($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setIdSucursal($oSucursal);
								$em->persist($oDocumentoPago);

								$oDetalleDocumentoPago = new DetalleDocumentoPago();
								$oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
								$oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
								$oDetalleDocumentoPago->setMontoDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
								$oDetalleDocumentoPago->setNumeroDocumentoDetalle($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
								$oCondicionPagoAlDia = $em->getRepository('RebsolHermesBundle:CondicionPago')->findOneBy(array('codigoInterfaz' => "AL_DIA", 'idEmpresa' => $oEmpresa, 'idEstado' => $EstadoActivo));
								$oDetalleDocumentoPago->setidCondicionPago($oCondicionPagoAlDia);
								$em->persist($oDetalleDocumentoPago);
							}
						}
						/** BONO ELECTRONICO */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoElectronico'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
							$oDetallePagoCuenta->setIdMoneda(NULL);
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setNombreEmpresa(NULL);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$oDetallePagoCuenta->setIdMotivoGratuidad(NULL);
							$em->persist($oDetallePagoCuenta);

							$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();

							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDocumentoPago = new DocumentoPago();
								$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
								$oDocumentoPago->setIdPaciente($oPaciente);
								$oDocumentoPago->setIdCaja($oCaja);
								$oDocumentoPago->setGarantia($garantia);
								$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['bono_' . $idForm . '_' . $cant]->getData()); //Numero bono
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData());
							$oDocumentoPago->setCopagoImed($mediosPagoform['copago_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setIdSucursal($oSucursal);
							$oDocumentoPago->setSeguroComplementario($mediosPagoform['Seguro_' . $idForm . '_' . $idCantidad]->getData());
							$em->persist($oDocumentoPago);
						}
					}
					/** BONO MANUAL */
					if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoManual'))
					{
						$auxMedioPago = $auxMedioPago + 1;
						$oDetallePagoCuenta = new DetallePagoCuenta();
						$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
						$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

						$oDetallePagoCuenta->setGarantia($garantia);
						$oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
						$oDetallePagoCuenta->setIdPrevision($oFinanciador);
						$oDetallePagoCuenta->setIdConvenio($oConvenio);
						$oDetallePagoCuenta->setFechaDetallePago($oFecha);
						$oDetallePagoCuenta->setCodigoControlFacturacion(0);
						$em->persist($oDetallePagoCuenta);

						$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();

						for ($i = 1; $i <= $maxCantidad; $i++)
						{
							$cant = $i - 1;
							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
							$oDocumentoPago->setNumeroDocumento($mediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}
					}
				}
			}
		}

	} else {

		foreach ($arrayFormasPago as $idForm)
		{
			if ($mediosPagoform['medioPago_' . $idForm]->getData())
			{

				$oFormasPago = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);
				if($oFormasPago->getEmiteBoleta()==0){
					$auxEmisionBoleta = $auxEmisionBoleta + 1;
					$montoRestaBoletaMedioPagoNoEmiteBoleta =   $montoRestaBoletaMedioPagoNoEmiteBoleta +
					intval($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
				}else{
					$countEmisionBoleta = $countEmisionBoleta +1;
				}

				$oFormasPagoTipo = $oFormasPago->getIdTipoFormaPago()->getId();

				$bonoElectronico = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoElectronico'));
				$bonoManual      = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoManual'));
				$chequeFecha     = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeFecha'));
				$chequeDia       = ($oFormasPagoTipo !== $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeDia'));

				if ( $bonoElectronico  || $bonoManual || $chequeFecha || $chequeDia){

					/** FORMA DE PAGO SENSILLA Y QUE NO ES DINAMICA EN MULTIPLES FORMULARIOS */
					/** EFECTIVO */
					if ($oFormasPagoTipo === 1)
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.Efectivo'))
						{
							$auxMedioPago = $auxMedioPago + 0;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);
						}
						/** GRATUIDAD */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.Gratuidad'))
						{
							$auxMedioPago = $auxMedioPago + 0;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							/* Obtiene Objeto de Gratuidad */
							$oMotivoGratuidad = $em->getRepository('RebsolHermesBundle:MotivoGratuidad')->find($mediosPagoform['idGratuidad_'. $idForm]->getData());
							/* Obtiene Objeto de Gratuidad */
							$oDetallePagoCuenta->setIdMotivoGratuidad($oMotivoGratuidad);
							$em->persist($oDetallePagoCuenta);
						}

						/** CREDITO */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.TarjetaCredito')) {

							$auxMedioPago = $auxMedioPago + 0;
							$oDetallePagoCuenta = new DetallePagoCuenta();

							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral(0);
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')->find($mediosPagoform['TarjetaCredito_' . $idForm . '_'.$idCantidad]->getData());
							$oDocumentoPago->setIdTarjetaCredito($oTarjetaCredito);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);

						}

						/** DEBITO */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.TarjetaDebito'))
						{
							$auxMedioPago = $auxMedioPago + 0;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral(0);
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());


							$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($mediosPagoform['TarjetaDebito__'. $idForm. '_0']->getData());
							$oDocumentoPago->setIdBanco($oBanco);
							$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}

						/** LASIK */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ConvenioLasik'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['folio_' . $idForm]->getData());
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm]->getData());
							$oDocumentoPago->setNumeroVoucher(NULL);
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}

						/** IMED */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ConvenioImed'))
						{
							$auxMedioPago = $auxMedioPago + 1;
							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm]->getData());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$oDocumentoPago = new DocumentoPago();
							$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
							$oDocumentoPago->setIdPaciente($oPaciente);
							$oDocumentoPago->setIdCaja($oCaja);
							$oDocumentoPago->setGarantia($garantia);
							$oDocumentoPago->setIdFormaPago($oFormasPago);
							$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['folio_' . $idForm]->getData());
							$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
							$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm]->getData());
							$oDocumentoPago->setNumeroVoucher(NULL);
							$oDocumentoPago->setIdSucursal($oSucursal);
							$em->persist($oDocumentoPago);
						}

					}
					if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoElectronico') ||
						$oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoManual') ||
						$oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeFecha') ||
						$oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeDia'))
					{
						/** DOCUMENTOS QUE PUEDEN TENER VARIOS PAGOS EN UNA SOLA FORMA DE PAGO */
						/**  CHEQUE FECHA */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeFecha'))
						{
							$auxMedioPago = $auxMedioPago + 0;
							$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();


							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDetallePagoCuenta = new DetallePagoCuenta();
								$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
								$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
								$oDetallePagoCuenta->setGarantia($garantia);
								$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oDetallePagoCuenta->setIdPrevision($oFinanciador);
								$oDetallePagoCuenta->setIdConvenio($oConvenio);
								$oDetallePagoCuenta->setFechaDetallePago($oFecha);
								$oDetallePagoCuenta->setCodigoControlFacturacion(0);
								$em->persist($oDetallePagoCuenta);
							}


							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDocumentoPago = new DocumentoPago();
								$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
								$oDocumentoPago->setIdPaciente($oPaciente);
								$oDocumentoPago->setIdCaja($oCaja);
								$oDocumentoPago->setGarantia($garantia);
								$oDocumentoPago->setIdFormaPago($oFormasPago);
								$oDocumentoPago->setNumeroDocumentoGeneral(0);
								$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
								$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $mediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
								$oDocumentoPago->setIdBanco($oBanco);
								$s = $mediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
								$s = str_replace('.', '', $s);
								$s = str_replace('-', '', $s);
								$oDocumentoPago->setRutPropietario($s);
								$oDocumentoPago->setNombrePropietario($mediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroDocumento($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setIdSucursal($oSucursal);
								$em->persist($oDocumentoPago);


								$oDetalleDocumentoPago = new DetalleDocumentoPago();
								$oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
								$oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
								$oDetalleDocumentoPago->setMontoDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
								$oDetalleDocumentoPago->setNumeroDocumentoDetalle($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());

								$oCondicionPago = $em->getRepository('RebsolHermesBundle:CondicionPago')->find($auxxx['condicion_' . $idForm . '_' . $cant]);
								$oDetalleDocumentoPago->setidCondicionPago($oCondicionPago);
								$em->persist($oDetalleDocumentoPago);
							}
						}
						/** CHEQUE DIA */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.ChequeDia')) {
							$auxMedioPago = $auxMedioPago + 0;

							$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();

							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDetallePagoCuenta = new DetallePagoCuenta();
								$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
								$oDetallePagoCuenta->setIdFormaPago($oFormasPago);
								$oDetallePagoCuenta->setGarantia($garantia);
								$oDetallePagoCuenta->setMontoPagoCuenta($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oDetallePagoCuenta->setIdPrevision($oFinanciador);
								$oDetallePagoCuenta->setIdConvenio($oConvenio);
								$oDetallePagoCuenta->setFechaDetallePago($oFecha);
								$oDetallePagoCuenta->setCodigoControlFacturacion(0);
								$em->persist($oDetallePagoCuenta);
							}

							for ($i = 1; $i <= $maxCantidad; $i++)
							{

								$cant = $i - 1;
								$oDocumentoPago = new DocumentoPago();
								$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
								$oDocumentoPago->setIdPaciente($oPaciente);
								$oDocumentoPago->setIdCaja($oCaja);
								$oDocumentoPago->setGarantia($garantia);
								$oDocumentoPago->setIdFormaPago($oFormasPago);
								$oDocumentoPago->setNumeroDocumentoGeneral(0);
								$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
								$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $mediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
								$oDocumentoPago->setIdBanco($oBanco);
								$s = $mediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
								$s = str_replace('.', '', $s);
								$s = str_replace('-', '', $s);
								$oDocumentoPago->setRutPropietario($s);
								$oDocumentoPago->setNombrePropietario($mediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroDocumento($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setNumeroVoucher($mediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setIdSucursal($oSucursal);
								$em->persist($oDocumentoPago);

								$oDetalleDocumentoPago = new DetalleDocumentoPago();
								$oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
								$oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
								$oDetalleDocumentoPago->setMontoDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
								$oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
								$oDetalleDocumentoPago->setNumeroDocumentoDetalle($mediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());

								$oCondicionPagoAlDia = $em->getRepository('RebsolHermesBundle:CondicionPago')->findOneBy(
									array(
										'codigoInterfaz' => "AL_DIA",
										'idEmpresa' => $oEmpresa,
										'idEstado' => $EstadoActivo
										)
									);

								$oDetalleDocumentoPago->setidCondicionPago($oCondicionPagoAlDia);

								$em->persist($oDetalleDocumentoPago);
							}
						}

						/** BONO ELECTRONICO */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoElectronico')){

							if($auxAfecta || $auxExenta){
								$auxMedioPago    = $auxMedioPago + 0;
								$auxBonoCount    = $auxBonoCount + 1;
							}else{
								$auxMedioPago    = $auxMedioPago + 1;
							}
							$maxCantidad            = $mediosPagoform['dinamico_' . $idForm]->getData();
							$MontoTotalBonificacion = 0;
							$MontoTotalseguroCom    = 0;
							for ($i = 1; $i <= $maxCantidad; $i++){
								$cant                   = $i - 1;
								$MontoTotalBonificacion =   $MontoTotalBonificacion +
								$mediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData();
								$MontoTotalseguroCom    =   $MontoTotalseguroCom +
								$mediosPagoform['Seguro_' . $idForm . '_' . $cant]->getData();;


							}
							if(!empty($arrayUnserializeFP)){
								$iii = 3;
							}else{
								$iii = 2;
							}

							for($i = 1;$i<=$iii;$i++) {

								${"oDetallePagoCuenta".$i} = new DetallePagoCuenta();
								switch ($i){
									case 1:
									$oFormasPagoForBonoElecronico = $oFormasPago;
									${"oDetallePagoCuenta".$i}->setMontoPagoCuenta($MontoTotalBonificacion);
									break;
									case 2:
									$oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
									->findOneBy(array(
										'idTipoFormaPago'   =>$this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.SeguroComplementario'),
										'idEmpresa'         =>$oEmpresa->getId()));
									${"oDetallePagoCuenta".$i}->setMontoPagoCuenta($MontoTotalseguroCom);
									break;
									case 3:
									$Exedente = 0;
									if($oInterfazImed){
										if(!empty($arrayUnserializeFP)){
											foreach($arrayUnserializeFP as $forma){
												if(intval($forma->CodForPag) == 6){

													$Exedente    = $forma->MtoTransac + $Exedente;
													$oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
													->findOneBy(array(
														'idTipoFormaPago'   =>$this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.Exedente'),
														'idEmpresa'         =>$oEmpresa->getId()));
													${"oDetallePagoCuenta".$i}->setMontoPagoCuenta($Exedente);
												}
											}
										}

									}
									break;
									case 4:
									/**  EFECTIVO  */
									$oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);
									break;
								}
								${"oDetallePagoCuenta".$i}->setIdPagoCuenta($oPagoCuenta);
								${"oDetallePagoCuenta".$i}->setIdFormaPago($oFormasPagoForBonoElecronico);
								${"oDetallePagoCuenta".$i}->setGarantia($garantia);
								${"oDetallePagoCuenta".$i}->setIdMoneda(NULL);
								${"oDetallePagoCuenta".$i}->setIdPrevision($oFinanciador);
								${"oDetallePagoCuenta".$i}->setIdConvenio($oConvenio);
								${"oDetallePagoCuenta".$i}->setFechaDetallePago($oFecha);
								${"oDetallePagoCuenta".$i}->setNombreEmpresa(NULL);
								${"oDetallePagoCuenta".$i}->setCodigoControlFacturacion(0);
								${"oDetallePagoCuenta".$i}->setIdMotivoGratuidad(NULL);
								$em->persist(${"oDetallePagoCuenta".$i});

							}

							for ($i = 1; $i <= $maxCantidad; $i++){
								$cant                   = $i - 1;
								if(!empty($arrayUnserializeFP)){
									$eee = 3;
								}else{
									$eee = 2;
								}

								for($e = 1;$e<=$eee;$e++) {
									${"oDocumentoPago".$e} = new DocumentoPago();
									switch ($e){
										case 1:
										$oFormasPagoForBonoElecronico = $oFormasPago;
										break;
										case 2:
										$oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
										->findOneBy(array(
											'idTipoFormaPago'   =>$this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.SeguroComplementario'),
											'idEmpresa'         =>$oEmpresa->getId()));
										break;
										case 3:

										if($oInterfazImed){
											if(!empty($arrayUnserializeFP)){
												foreach($arrayUnserializeFP as $forma){
													if(intval($forma->CodForPag) == 6){
														$oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
														->findOneBy(array(
															'idTipoFormaPago'   =>$this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.Excedente'),
															'idEmpresa'         =>$oEmpresa->getId()));
													}
												}
											}
										}
										break;
									}

									${"oDocumentoPago".$e}->setIdDetallePagoCuenta(${"oDetallePagoCuenta".$e});
									${"oDocumentoPago".$e}->setIdPaciente($oPaciente);
									${"oDocumentoPago".$e}->setIdCaja($oCaja);
									${"oDocumentoPago".$e}->setGarantia($garantia);
									${"oDocumentoPago".$e}->setIdFormaPago($oFormasPagoForBonoElecronico);
									${"oDocumentoPago".$e}->setNumeroDocumentoGeneral($mediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
									${"oDocumentoPago".$e}->setFechaRecepcionDocumento($oFecha);
									${"oDocumentoPago".$e}->setIdSucursal($oSucursal);

									if($auxAfecta || $auxExenta){
										$auxSumaBonos   =   $auxSumaBonos +
										$mediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData() +
										$mediosPagoform['copago_' . $idForm . '_' . $cant]->getData();
									}
									switch ($e){
										case 1:
										${"oDocumentoPago".$e}->setMontoTotalDocumento($mediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData());
										${"oDocumentoPago".$e}->setCopagoImed($mediosPagoform['copago_' . $idForm . '_' . $cant]->getData());
										break;
										case 2:
										${"oDocumentoPago".$e}->setMontoTotalDocumento($mediosPagoform['Seguro_' . $idForm . '_' . $cant]->getData());
										${"oDocumentoPago".$e}->setCopagoImed(0);
										break;
										case 3:
										${"oDocumentoPago".$e}->setMontoTotalDocumento($mediosPagoform['exedente_' . $idForm]->getData());
										${"oDocumentoPago".$e}->setCopagoImed(0);
										break;
									}
									$em->persist(${"oDocumentoPago".$e});

								}
							}

						}
						/** BONO MANUAL */
						if ($oFormasPagoTipo === $this->obtenerServicioGlobales()->obtenerParametro('FormaPagoTipo.BonoManual'))
						{
							if($auxAfecta || $auxExenta){
								$auxMedioPago = $auxMedioPago + 0;
								$auxBonoCount = $auxBonoCount + 1;
							}else{
								$auxMedioPago = $auxMedioPago + 1;
							}

							$oDetallePagoCuenta = new DetallePagoCuenta();
							$oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
							$oDetallePagoCuenta->setIdFormaPago($oFormasPago);

							$oDetallePagoCuenta->setGarantia($garantia);
							$oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
							$oDetallePagoCuenta->setIdPrevision($oFinanciador);
							$oDetallePagoCuenta->setIdConvenio($oConvenio);
							$oDetallePagoCuenta->setFechaDetallePago($oFecha);
							$oDetallePagoCuenta->setCodigoControlFacturacion(0);
							$em->persist($oDetallePagoCuenta);

							$maxCantidad = $mediosPagoform['dinamico_' . $idForm]->getData();

							for ($i = 1; $i <= $maxCantidad; $i++)
							{
								$cant = $i - 1;
								$oDocumentoPago = new DocumentoPago();
								$oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
								$oDocumentoPago->setIdPaciente($oPaciente);
								$oDocumentoPago->setIdCaja($oCaja);
								$oDocumentoPago->setGarantia($garantia);
								$oDocumentoPago->setIdFormaPago($oFormasPago);
								$oDocumentoPago->setNumeroDocumentoGeneral($mediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setFechaRecepcionDocumento($oFecha);
								$oDocumentoPago->setMontoTotalDocumento($mediosPagoform['monto_' . $idForm . '_' . $cant]->getData());

								if($auxAfecta || $auxExenta){
									$auxSumaBonos = $auxSumaBonos + $mediosPagoform['monto_' . $idForm . '_' . $cant]->getData();
								}

								$oDocumentoPago->setNumeroDocumento($mediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
								$oDocumentoPago->setIdSucursal($oSucursal);
								$em->persist($oDocumentoPago);
							}
						}
					}
				}

			}
		}

		$em->flush();

		if($oPaciente) {
			$tipoAtencionFc = $oPaciente->getIdTipoAtencionFc();
			if($tipoAtencionFc->getNombreTipoAtencionFc() == 'URGENCIA') {

				$oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($estadoActivo);
				$oDatoIngreso = $em->getRepository('RebsolHermesBundle:DatoIngreso')->findOneBy(['idPaciente' => $oPaciente]);

				$oPnatural = $oPaciente->getIdPnatural();
				$idPersona = $oPnatural->getIdPersona()->getId();

				$iIdItemAtencion       = 6;
				$iIdEstado             = $this->container->getParameter('estado_activo');
				$oFecha = new \Datetime(date("Y-m-d H:i:s"));

				$oConsultaMedicaFc = $em->getRepository('RebsolHermesBundle:ConsultaMedicaFc')->findOneBy(
					array(
						"idPaciente" => $oPaciente->getId()
						,"idItemAtencion"    => $iIdItemAtencion
						,"idEstado"          => $iIdEstado
				));

				$arrExamenPacienteFc = $em->getRepository('RebsolHermesBundle:ExamenPacienteFc')->findBy(['idConsultaMedica' => $oConsultaMedicaFc, 'idEstado' => $oEstado]);

				foreach($arrExamenPacienteFc as $oExamenPacienteFc) {

					$arrExamenPacienteFcDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findBy(['idExamenPacienteFc' => $oExamenPacienteFc]);
					$arrPrestaciones = [];
					$arrSolicitudesAgenda = []; 
					foreach($arrExamenPacienteFcDetalle as $oExamenPacienteFcDetalle) {
						$arrPrestaciones[] = $oExamenPacienteFcDetalle->getIdAccionClinica()->getId();
						$arrSolicitudesAgenda[] = $oExamenPacienteFcDetalle->getId();
					}

					$parametros = [
						'modulo'              => 'CAJA',
						'idEmpresa'           => $oEmpresa->getId(),
						'idPersona'           => $idPersona, //tabla Persona
						'nombreSala'          => null,
						'idPaciente'          => $oPaciente->getId(),
						'examenPacienteFc'    => $oExamenPacienteFc,
						'solicitudesAgenda'   => $arrSolicitudesAgenda,
						'idReservaAtencion'   => null,
						'usuarioTransaccion'  => $oDatoIngreso->getIdProfesional()->getId(),
						'idsAccionesClinicas' => $arrPrestaciones,
						'oPagoCuenta'		  => $oPagoCuenta,
						'moduloIngreso' => 'Urgencia'
					];
					
					$this->get('Ris_Lis')->procesarRisLisCajaUrgencia($parametros);

				}

			}
		}

		$this->clearSesionVar();

		return new Response('pagado');

	}


	private function clearSesionVar() {

		$this->killSession('caja.idPaciente');
		$this->killSession('idInterfazImed');
		$this->killSession('api');
		$this->killSession('pacienteApi');
		$this->killSession('persona');
		$this->killSession('garantia');
		$this->killSession('ListaTratamiento');
		$this->killSession('ListaDiferencia');
		$this->killSession('ListaDiferenciaSaldo');
		$this->killSession('idSubEmpresaItem');
		$this->killSession('idDiferenciaSaldo');

		$this->killSession('idDiferencia');
		$this->killSession('derivadoRutExt');


		$this->killSession('sucursal');
		$this->killSession('financiador');
		$this->killSession('convenio');
		$this->killSession('plan');
		$this->killSession('origen');
		$this->killSession('derivadoInt');
		$this->killSession('derivadoExt');
		$this->killSession('ListaPrestacion');
		$this->killSession('caja');
		$this->killSession('vSumaCantidad');
		$this->killSession('idReservaAtencion');
		$this->killSession('idPacienteGarantia');

		return true;
	}


	protected function killSession($eliminarSesion) {
		return $this->get('session')->remove($eliminarSesion);
	}


}
