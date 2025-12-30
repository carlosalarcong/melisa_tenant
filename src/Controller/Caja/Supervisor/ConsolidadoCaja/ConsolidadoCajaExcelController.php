<?php

namespace App\Controller\Caja\Supervisor\ConsolidadoCaja;

use App\Controller\Caja\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;

class ConsolidadoCajaExcelController extends SupervisorController
{

	public function GestionInformeCajaAction(request $request, $id)
	{
		$em                  = $this->getDoctrine()->getManager();
		$oEmpresa            = $this->ObtenerEmpresaLogin();
		$oEstadoAct          = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$oEstadoBletaActiva  = $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find(1);
		$oEstadoPagoAnulada  = $em->getRepository('RebsolHermesBundle:EstadoPago')->find(0);
		$oEstadoPagoGarantia = $em->getRepository('RebsolHermesBundle:EstadoPago')->find(2);
		$oEstadoPagoActiva   = $em->getRepository('RebsolHermesBundle:EstadoPago')->find(1);
		$idUser              = $this->getUser();
		$oUser               = $em->getRepository("RebsolHermesBundle:UsuariosRebsol")->find($idUser);

		//Obtenemos el objeto de la sucursal según el usuario
		$fechaIngresada = $this->get('session')->get('fecha');
		$fechaFormat    = new \DateTime($fechaIngresada);
		$fechaFormat    = $fechaFormat->format('Y-m-d');
		$fecha          = explode("-",$fechaIngresada);
		$fechaIngresada = $fecha[2]."-".$fecha[1]."-".$fecha[0]."%";
		$Sucursal       = $this->get('session')->get('sucursal');

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		if($estadoApi === "core"){
			$oCaja  = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerOCaja($fechaIngresada, $Sucursal, $idUser);
			$oCajac = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCajaC($fechaFormat, $Sucursal, $oEstadoPagoActiva);

			$arrayPagosCuenta = array();

			foreach($oCajac as $c){
				$arrayPagosCuenta[] = $c['idPagoCuenta'];
			}

			$arrayBoletas = array();

			foreach($arrayPagosCuenta as $c){

				$BoletasQueryResult = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerBoletas($c, $oEstadoBletaActiva);

				if($BoletasQueryResult){
					foreach($BoletasQueryResult as $bo){
						$arrayBoletas[] = $bo;
					}
				}
			}

			//Garantía
			//Obtenemos todas las garantías de la fecha y de la sucursal
			$oCajag = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantias($fechaIngresada, $Sucursal, $oEstadoPagoGarantia);

			//Normal Pagos Anulados
			//Obtenemos todas los pagos que se han anulado.
			$oCajaAnul = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerPagosAnulados($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);

			//Garantía Pagos Anulados
			//Obtenemos todas las garantías anuladas
			$oCajaAnulg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantiasAnuladas($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);
		} else {
			/////////// API //////////
			$oCaja   = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerOCajaApi1($fechaIngresada, $Sucursal, $idUser);
			$oCajac = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCajaCApi1($fechaFormat, $Sucursal, $oEstadoPagoActiva);

			$arrayPagosCuenta = array();

			foreach($oCajac as $c){
				$arrayPagosCuenta[] = $c['idPagoCuenta'];
			}

			$arrayBoletas = array();

			foreach($arrayPagosCuenta as $c){
				$BoletasQueryResult = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerBoletas($c, $oEstadoBletaActiva);

				if($BoletasQueryResult){
					foreach($BoletasQueryResult as $bo){
						$arrayBoletas[] = $bo;
					}
				}
			}

			//Garantía
			//Obtenemos todas las garantías de la fecha y de la sucursal
			$oCajag = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantiasApi1($fechaIngresada, $Sucursal, $oEstadoPagoGarantia);

			//Normal Pagos Anulados
			//Obtenemos todas los pagos que se han anulado.
			$oCajaAnul = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerPagosAnuladosApi1($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);

			//Garantía Pagos Anulados
			//Obtenemos todas las garantías anuladas
			$oCajaAnulg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantiasAnuladasApi1($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);
		}

		//Obtenemos las formas de pago por fecha y sucursal
		$oFormaPagoQuery = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerFormaPago($fechaFormat, $Sucursal);
		
		//Obtenemos las formas de pago por fecha y sucursal, pero de la garantía
		$oFormaPagoQueryg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerFormaPagoGarantias($fechaIngresada, $Sucursal);

		//Obtiene el detalle de la caja
		$oCajaDetalles = $em->getRepository("RebsolHermesBundle:Caja")->find($id);

		//Obtiene el monto del copago, seguro y bonificación.
		$oDocumentoPagoDetalles = $em->getRepository("RebsolHermesBundle:DocumentoPago")->obtenerDocumentoPagoDetalles($id);

		//Obtiene los tipos de forma de pago
		$oTiposFormaPago = $em->getRepository("RebsolHermesBundle:FormaPagoTipo")->findBy(array("idEstado" => $oEstadoAct));
		$arrMediosMonto = array();

		foreach($oTiposFormaPago as $tfp){
			$arrMediosMonto[] = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCopago($id, $tfp);
		}
		
		//Obtenemos todos los documentos de pago por fecha y sucursal
		$ResultadoDocumentosQuery = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDocumentosDePago($fechaIngresada, $Sucursal);

		//Se obtienen los tipos de forma de pagos
		$oTiposFormaPagos = $em->getRepository("RebsolHermesBundle:FormaPago")->findBy(array("idEstado" => $oEstadoAct));

		$arrayTipos = array();
		foreach ($oTiposFormaPagos as $tipos)
		{
			$varAun = $em->getRepository("RebsolHermesBundle:PagoCuenta")->
			obtenerMontoCierreCajayBonos($id, $tipos->getId(), $oEstadoAct->getId());
			$arrayTipos[$tipos->getid()] = $varAun;
		}

		$oDetalleCaja = array();
		foreach ($arrayTipos as $key => $tiposs)
		{
			if ($tiposs != NULL && $key != 9 && $key != 10)
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPagoTipo")->findOneBy(array("id" => $key, "idEstado" => $oEstadoAct));
				foreach($tiposs as $tipos){
					if($oFormaPago != null){
						$oDetalleCaja[$key] =
						array(
							"monto"     => $tipos['montoForma'],
							"bono"      => $tipos['numeroBono'],
							"formaPago" => $oFormaPago->getnombre(),
							"idtipo"    => $key,
						);
					}
				}
			}
		}

		$arrayTiposs = array();
		foreach ($oTiposFormaPagos as $tipos)
		{
			$arrayTiposs[$tipos->getid()] = $em->getRepository("RebsolHermesBundle:PagoCuenta")->
			obtenerMontoCierreCaja($id, $tipos->getId(), $oEstadoAct->getId());
		}

		$oDetalleCajat = array();
		foreach ($arrayTiposs as $key => $tipos)
		{
			if ($tipos != NULL && $key != 9 && $key != 10)
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPago")->
				findOneBy(
					array(
						"idTipoFormaPago" => $key,
						"idEstado"        => $oEstadoAct,
						"idEmpresa"       => $oEmpresa
					)
				);
				$oDetalleCajat[$key] =
				array(
					"monto"     => $tipos,
					"formaPago" => $oFormaPago->getnombre(),
					"idtipo"    => $key
				);
			}
		}

		$ResultadoDocumentosQueryg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDocumentosDePagoGarantias($fechaIngresada, $Sucursal);

		//==============================
		//          CUADRATURA
		//==============================

		//Todos los Medios de Pago
		$ResultadoCuadratura = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDatosCuadratura($fechaFormat, $Sucursal);

		//Todos los Bonos Manuales
		$ResultadoCuadraturaBono = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDatosCuadraturaBono($fechaIngresada, $Sucursal);

		//Todos los Depositos
		$oDetalleCajaDeposito = $em->getRepository('RebsolHermesBundle:DetalleCaja')->ObtieneDetalleCajaDeposito($fechaIngresada, $Sucursal, $oEstadoAct);

		//Todas las Garantias
		$ResultadoCuadraturaGarantia = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDatosCuadraturaGarantia($fechaIngresada, $Sucursal);

		//Renderizamos a InformeCaja.html.twig'
		return $this->render('RecaudacionBundle:Supervisor\ConsolidadoCaja:InformeCaja.html.twig',
			array(
				'caja'                        => $oCaja,
				'cajac'                       => $oCajac,
				'cajag'                       => $oCajag,
				'cajaAnul'                    => $oCajaAnul,
				'cajaAnulg'                   => $oCajaAnulg,
				//Datos para construir informe superior
				'cajaDetalles'                => $oCajaDetalles,
				'numerosBoletas'              => $arrayBoletas,
				'formaPago'                   => $oFormaPagoQuery,
				'formaPagog'                  =>$oFormaPagoQueryg,
				'detalleCount'                => $oDocumentoPagoDetalles,
				'copagoCount'                 => $arrMediosMonto,
				'documentos'                  => $ResultadoDocumentosQuery,
				'documentosg'                 => $ResultadoDocumentosQueryg,
				'detalleCaja'                 => $oDetalleCaja,
				'detalleCajaTodo'             => $oDetalleCajat,
				//Datos para cuadratura
				'detallesMontosTipos'         => $ResultadoCuadratura,
				'detallesMontosBonosManuales' => $ResultadoCuadraturaBono,
				'detalleMontosDeposito'       => $oDetalleCajaDeposito,
				'detalleMontosGarantias'      => $ResultadoCuadraturaGarantia
			)
		);
	}
        
        //NUeva Funcion consolidado de 
	public function GestionInformeCaja2Action(request $request, $id)
	{
		$em                  = $this->getDoctrine()->getManager();
		$oEmpresa            = $this->ObtenerEmpresaLogin();
		$oEstadoAct          = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$oEstadoBletaActiva  = $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find(1);
		$oEstadoPagoAnulada  = $em->getRepository('RebsolHermesBundle:EstadoPago')->find(0);
		$oEstadoPagoGarantia = $em->getRepository('RebsolHermesBundle:EstadoPago')->find(2);
		$oEstadoPagoActiva   = $em->getRepository('RebsolHermesBundle:EstadoPago')->find(1);
		$idUser              = $this->getUser();
		$oUser               = $em->getRepository("RebsolHermesBundle:UsuariosRebsol")->find($idUser);
		//Obtenemos el objeto de la sucursal según el usuario
		$fechaIngresada = $this->get('session')->get('fecha');
		$fechaFormat    = new \DateTime($fechaIngresada);
		$fechaFormat    = $fechaFormat->format('Y-m-d');
		$fecha          = explode("-",$fechaIngresada);
		$fechaIngresada = $fecha[2]."-".$fecha[1]."-".$fecha[0]."%";
		$Sucursal       = $this->get('session')->get('sucursal');

		$estadoApi = $this->estado('EstadoApi');

        $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		if($estadoApi === "core"){
			$oCaja  = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerOCaja($fechaIngresada, $Sucursal, $idUser);
			$oCajac = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCajaC2($fechaFormat, $Sucursal, $oEstadoPagoActiva, $folio['valor']);
			$oCajaPagoWeb = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCajaC2($fechaFormat, $Sucursal, $oEstadoPagoActiva, $folio['valor'], true);
            $oCajac = array_merge($oCajac, $oCajaPagoWeb);
            array_multisort(array_column($oCajac, 'datePagoCuenta'), SORT_DESC, $oCajac);
			$arrayPagosCuenta = array();
			foreach($oCajac as $c){
				$arrayPagosCuenta[] = $c['idPagoCuenta'];
			}
			$arrayBoletas = array();
			foreach($arrayPagosCuenta as $c){
				$BoletasQueryResult = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerBoletas($c, $oEstadoBletaActiva);
				if($BoletasQueryResult){
					foreach($BoletasQueryResult as $bo){
						$arrayBoletas[] = $bo;
					}
				}
			}
			//Garantía
			//Obtenemos todas las garantías de la fecha y de la sucursal
			$oCajag = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantias($fechaIngresada, $Sucursal, $oEstadoPagoGarantia);
			//Normal Pagos Anulados
			//Obtenemos todas los pagos que se han anulado.
			$oCajaAnul = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerPagosAnulados($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);
//            dump($oCajaAnul);exit();
			//Garantía Pagos Anulados
			//Obtenemos todas las garantías anuladas
			$oCajaAnulg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantiasAnuladas($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);
		} else {
			/////////// API //////////
			$oCaja   = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerOCajaApi1($fechaIngresada, $Sucursal, $idUser);
			$oCajac = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCajaCApi1($fechaFormat, $Sucursal, $oEstadoPagoActiva);
			$arrayPagosCuenta = array();
			foreach($oCajac as $c){
				$arrayPagosCuenta[] = $c['idPagoCuenta'];
			}
			$arrayBoletas = array();
			foreach($arrayPagosCuenta as $c){
				$BoletasQueryResult = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerBoletas($c, $oEstadoBletaActiva);
				if($BoletasQueryResult){
					foreach($BoletasQueryResult as $bo){
						$arrayBoletas[] = $bo;
					}
				}
			}
			//Garantía
			//Obtenemos todas las garantías de la fecha y de la sucursal
			$oCajag = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantiasApi1($fechaIngresada, $Sucursal, $oEstadoPagoGarantia);
			//Normal Pagos Anulados
			//Obtenemos todas los pagos que se han anulado.
			$oCajaAnul = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerPagosAnuladosApi1($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);
			//Garantía Pagos Anulados
			//Obtenemos todas las garantías anuladas
			$oCajaAnulg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerGarantiasAnuladasApi1($fechaIngresada, $Sucursal, $oEstadoPagoAnulada);
		}
		//Obtenemos las formas de pago por fecha y sucursal
		$oFormaPagoQuery = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerFormaPago($fechaFormat, $Sucursal);
		
		//Obtenemos las formas de pago por fecha y sucursal, pero de la garantía
		$oFormaPagoQueryg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerFormaPagoGarantias($fechaIngresada, $Sucursal);
		//Obtiene el detalle de la caja
		$oCajaDetalles = $em->getRepository("RebsolHermesBundle:Caja")->find($id);
		//Obtiene el monto del copago, seguro y bonificación.
		$oDocumentoPagoDetalles = $em->getRepository("RebsolHermesBundle:DocumentoPago")->obtenerDocumentoPagoDetalles($id);
		//Obtiene los tipos de forma de pago
		$oTiposFormaPago = $em->getRepository("RebsolHermesBundle:FormaPagoTipo")->findBy(array("idEstado" => $oEstadoAct));
		$arrMediosMonto = array();
		foreach($oTiposFormaPago as $tfp){
			$arrMediosMonto[] = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerCopago($id, $tfp);
		}
		
		//Obtenemos todos los documentos de pago por fecha y sucursal
		$ResultadoDocumentosQuery = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDocumentosDePago($fechaIngresada, $Sucursal);
		//Se obtienen los tipos de forma de pagos
		$oTiposFormaPagos = $em->getRepository("RebsolHermesBundle:FormaPago")->findBy(array("idEstado" => $oEstadoAct));
		$arrayTipos = array();
		foreach ($oTiposFormaPagos as $tipos)
		{
			$varAun = $em->getRepository("RebsolHermesBundle:PagoCuenta")->
			obtenerMontoCierreCajayBonos($id, $tipos->getId(), $oEstadoAct->getId());
			$arrayTipos[$tipos->getid()] = $varAun;
		}
		$oDetalleCaja = array();
		foreach ($arrayTipos as $key => $tiposs)
		{
			if ($tiposs != NULL && $key != 9 && $key != 10)
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPagoTipo")->findOneBy(array("id" => $key, "idEstado" => $oEstadoAct));
				foreach($tiposs as $tipos){
					if($oFormaPago != null){
						$oDetalleCaja[$key] =
						array(
							"monto"     => $tipos['montoForma'],
							"bono"      => $tipos['numeroBono'],
							"formaPago" => $oFormaPago->getnombre(),
							"idtipo"    => $key,
						);
					}
				}
			}
		}
		$arrayTiposs = array();
		foreach ($oTiposFormaPagos as $tipos)
		{
			$arrayTiposs[$tipos->getid()] = $em->getRepository("RebsolHermesBundle:PagoCuenta")->
			obtenerMontoCierreCaja($id, $tipos->getId(), $oEstadoAct->getId());
		}
		$oDetalleCajat = array();
		foreach ($arrayTiposs as $key => $tipos)
		{
			if ($tipos != NULL && $key != 9 && $key != 10)
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPago")->
				findOneBy(
					array(
						"idTipoFormaPago" => $key,
						"idEstado"        => $oEstadoAct,
						"idEmpresa"       => $oEmpresa
					)
				);
				$oDetalleCajat[$key] =
				array(
					"monto"     => $tipos,
					"formaPago" => $oFormaPago->getnombre(),
					"idtipo"    => $key
				);
			}
		}
		$ResultadoDocumentosQueryg = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDocumentosDePagoGarantias($fechaIngresada, $Sucursal);
		//==============================
		//          CUADRATURA
		//==============================
		//Todos los Medios de Pago
		$ResultadoCuadratura = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDatosCuadratura($fechaFormat, $Sucursal);
		//Todos los Bonos Manuales
		$ResultadoCuadraturaBono = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDatosCuadraturaBono($fechaIngresada, $Sucursal);
		//Todos los Depositos
		$oDetalleCajaDeposito = $em->getRepository('RebsolHermesBundle:DetalleCaja')->ObtieneDetalleCajaDeposito($fechaIngresada, $Sucursal, $oEstadoAct);
		//Todas las Garantias
		$ResultadoCuadraturaGarantia = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerDatosCuadraturaGarantia($fechaIngresada, $Sucursal);

        $oSucursal = $em->getRepository('RebsolHermesBundle:Sucursal')->find($Sucursal);

		//Renderizamos a InformeCaja.html.twig'
		return $this->render('RecaudacionBundle:Supervisor\ConsolidadoCaja:InformeCaja2.html.twig',
			array(
                'sucursal'                    => $oSucursal->getNombreSucursal(),
				'caja'                        => $oCaja,
				'cajac'                       => $oCajac,
				'cajag'                       => $oCajag,
				'cajaAnul'                    => $oCajaAnul,
				'cajaAnulg'                   => $oCajaAnulg,
				//Datos para construir informe superior
				'cajaDetalles'                => $oCajaDetalles,
				'numerosBoletas'              => $arrayBoletas,
				'formaPago'                   => $oFormaPagoQuery,
				'formaPagog'                  =>$oFormaPagoQueryg,
				'detalleCount'                => $oDocumentoPagoDetalles,
				'copagoCount'                 => $arrMediosMonto,
				'documentos'                  => $ResultadoDocumentosQuery,
				'documentosg'                 => $ResultadoDocumentosQueryg,
				'detalleCaja'                 => $oDetalleCaja,
				'detalleCajaTodo'             => $oDetalleCajat,
				//Datos para cuadratura
				'detallesMontosTipos'         => $ResultadoCuadratura,
				'detallesMontosBonosManuales' => $ResultadoCuadraturaBono,
				'detalleMontosDeposito'       => $oDetalleCajaDeposito,
				'detalleMontosGarantias'      => $ResultadoCuadraturaGarantia
			)
		);
	}
        
}
