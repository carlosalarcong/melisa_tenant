<?php

namespace App\Controller\Caja\Recaudacion\GestionCaja;

use App\Entity\Legacy\Caja;
use App\Entity\Legacy\DetalleCaja;
use App\Entity\Legacy\DetalleCajaCheque;
use App\Controller\Caja\RecaudacionController;
use App\Form\Recaudacion\Pago\CerrarCajaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GestionCajaController extends RecaudacionController
{

	//APERTURA CAJA
	public function gestionAbrirCajaAction()
	{
		$em               = $this->getDoctrine()->getManager();
		$oUser            = $this->getUser();
		$oSucursal        = $this->rCaja()->GetSucursalAperturaCaja($oUser->getId());

		$oUbicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
			"idUsuario" => $oUser,
			"idEstado"  => $this->estado('EstadoActivo')
			));

		$oCaja = new Caja();

		$oCaja->setIdUsuario($oUser);
		$oCaja->setFechaApertura(new \DateTime("now"));
		$oCaja->setMontoInicial($oUbicacionCajero->getmontoInicial());
		$oCaja->setIdSucursal($oSucursal);
		$oCaja->setIdUbicacionCajero($oUbicacionCajero);

		$em->persist($oCaja);

		$em->flush();

		return $this->redirect($this->generateUrl('recaudacion_index'));
	}

	//CIERRE CAJA
	public function gestionCerrarCajaAction(request $request, $id)
	{
		$em              = $this->getDoctrine()->getManager();
		$oCaja           = $em->getRepository("RebsolHermesBundle:Caja")->find($id);
		$oTiposFormaPago = $em->getRepository("RebsolHermesBundle:FormaPagoTipo")->findBy(array("idEstado" => $this->estado('EstadoActivo')));

		foreach ($oTiposFormaPago as $tipo) {
			$arrayTiposFormasPago[] = $tipo->getid();
		}

		///Formulario
		$CerrarCaja = $this->createForm(CerrarCajaType::class, null, array(
			'idFrom' => $arrayTiposFormasPago,
			'estado_activado' => $this->estado('EstadoActivo')
			));

		// CajaBundle:Recaudacion/Pago/
		return $this->render('RecaudacionBundle:Recaudacion\GestionCaja\Form_OpenClose:CloseForm.html.twig', array(
			'form'  => $CerrarCaja->createView(),
			'items' => $this->GetArrayItemsCierreCaja($oCaja->getId(), $oTiposFormaPago, $this->estado('EstadoActivo'), $em),
			'caja'  => $oCaja
			));
	}

	public function gestionCerrarCajaCerradoAction(Request $request, $id)
	{
		$em              = $this->getDoctrine()->getManager();
		$oEmpresa        = $this->ObtenerEmpresaLogin();
		$oCaja           = $em->getRepository('RebsolHermesBundle:Caja')->find($id);

		$oTiposFormaPago = $em->getRepository('RebsolHermesBundle:FormaPagoTipo')->findBy(
			array(
				'idEstado' => $this->estado('EstadoActivo')
				)
			);

		$oDetalleCaja    = $em->getRepository('RebsolHermesBundle:DetalleCaja')->findBy(
			array(
				'idCaja' => $oCaja
				)
			);

		$fecha                = new \DateTime();
		$arrayTiposFormasPago = array();

		foreach ($oTiposFormaPago as $tipo) {
			$arrayTiposFormasPago[] = $tipo->getid();
		}

		$cerrarCaja = $this->createForm(CerrarCajaType::class, null,
			array(
				'idFrom'          => $arrayTiposFormasPago,
				'estado_activado' => $this->estado('EstadoActivo')
				)
			);
		$cerrarCaja->handleRequest($request);

		return new Response(($this->insertDataCierreCaja($id, $oDetalleCaja, $oTiposFormaPago, $oCaja, $fecha, $oEmpresa, $cerrarCaja, $em) ) ? 'Cerrado' : false);
	}

	//FUNCIONES CIERRE CAJA
	private function GetArrayItemsCierreCaja($id, $oTiposFormaPago, $oEstadoAct, $em)
	{
		$arrayTipos         = array();
		$arrayTiposC        = array();
		$arrTipoMontoAux    = array();
		$oEmpresa           = $this->ObtenerEmpresaLogin();

		foreach ($oTiposFormaPago as $tipos)
		{
			($tipos->getId() == 8) ? $arrayTiposC[$tipos->getid()] = $this->rCaja()->obtenerMontoCierreCaja($id, $tipos->getId(), $this->container->getParameter('EstadoPago.pagadoNormal')) :
			$arrayTipos[$tipos->getid()] = $this->rCaja()->obtenerMontoCierreCaja($id, $tipos->getId(), $this->container->getParameter('EstadoPago.pagadoNormal'));
		}

		/// Array con formas de pago Efectivo (1) y Cheque al día (8)
		foreach ($arrayTipos as $key => $tipos)
		{
			if ($tipos && ($key == 1 || $key == 8))
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPago")->findOneBy(array("idTipoFormaPago" => $key, "idEstado" => $oEstadoAct, "idEmpresa" => $oEmpresa));
				$arrTipoMontoAux[$key] = array(
					"suma" => $tipos,
					"nombre" => "Monto " . $oFormaPago->getnombre(),
					"idtipo" => $key,
					"cc" => 0
					);
			}
		}

		///Array con cuentas corrientes Clinica
		foreach ($arrayTiposC as $key => $tipos)
		{
			if ($tipos != NULL)
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPago")->findOneBy(array("idTipoFormaPago" => $key, "idEstado" => $oEstadoAct, "idEmpresa" => $oEmpresa));
				$ArrPagosCuentaCorrienteEmpresa = $this->rCaja()->ObtenerItemsPagosCuentaCorrienteEmpresa($oFormaPago->getid(), $id, $oEstadoAct->getId(), $oEmpresa->getId());
				if(!empty($ArrPagosCuentaCorrienteEmpresa)){
					foreach($ArrPagosCuentaCorrienteEmpresa as  $a){
						$monto = (!$a)?"Monto " . $oFormaPago->getnombre() . " " . $a['banco']:"Monto " . $oFormaPago->getnombre();
						$cc    = (!$a)?0:1;
						$suma  = (!$a)?$tipos:$a['montoTotalForma'];
						$arrTipoMontoAux[$key] = array(
							"suma"   => $suma,
							"nombre" => $monto,
							"cc"     => $cc,
							"idtipo" => $key
							);
					}
				}
			}
		}
		return $arrTipoMontoAux;
	}

	private function insertDataCierreCaja($id, $oDetalleCajaVerificaAnteriores, $oTiposFormaPago, $oCaja, $Fecha, $oEmpresa, $CerrarCaja, $em)
	{
		$oEstadoReaperturaCerrada = $this->estado('EstadoReaperturaCerrada');
		$oEstadoAct               = $this->estado('EstadoActivo');
		$oEstadoInc               = $this->estado('EstadoInc');

		$oPagoCuentaMontoTotal    = $em->getRepository('RebsolHermesBundle:PagoCuenta')->findBy(
			array(
				'idCaja' => $oCaja->getid()
				)
			);

		$Suma                     = 0;
		$arrayTipos               = array();
		$arrayTiposC              = array();

		foreach ($oTiposFormaPago as $tipos) {
			if($tipos->getId() == 8) {
				/** cheque al día */
				$arrayTiposC[$tipos->getid()] = $this->rCaja()->obtenerMontoCierreCaja($id, $tipos->getId(), $oEstadoAct->getId());
			} else {
				/** el resto de los medios de pago */
				$arrayTipos[$tipos->getid()] = $this->rCaja()->obtenerMontoCierreCaja($id, $tipos->getId(), $oEstadoAct->getId());
			}
		}

		if (count($oDetalleCajaVerificaAnteriores) > 0) {
			foreach ($oDetalleCajaVerificaAnteriores as $deca) {
				$odeca = $em->getRepository('RebsolHermesBundle:DetalleCaja')->find($deca->getid());
				$odeca->setIdEstado($oEstadoInc);
				$em->persist($odeca);
			}
		}

		if ($oPagoCuentaMontoTotal) {
			foreach ($oPagoCuentaMontoTotal as $monto) {
				$Suma = $Suma + $monto->GetMonto();
			}
		}

		$oCaja->setFechaCierre($Fecha);
		$oCaja->setMontoReal($Suma);
		$oCaja->setidEstadoReapertura($oEstadoReaperturaCerrada);
		$oCaja->setSuperavit($CerrarCaja['superavit']->getData());
		$oCaja->setDeficit($CerrarCaja['deficit']->getData());;
		$em->persist($oCaja);

		foreach ($arrayTipos as $key => $tipos) {
			if ($tipos && $key == 1 || $key == 8) {
				$oFormaPago = $em->getRepository('RebsolHermesBundle:FormaPago')->findOneBy(
					array(
						'idTipoFormaPago' => $key,
						'idEstado'        => $oEstadoAct,
						'idEmpresa'       => $oEmpresa
						)
					);

				$oBanco = $this->rCaja()->GetBancoIfCuentaCorriente($oCaja->getId(), $oFormaPago->getId());

				if (!$oBanco) {
					$oDetalleCaja = new DetalleCaja();
					$oDetalleCaja->setIdCaja($oCaja);
					$oDetalleCaja->setIdEstado($oEstadoAct);
					$oDetalleCaja->setIdFormaPago($oFormaPago);
					$oDetalleCaja->setNumeroDeposito($CerrarCaja['deposito_' . $oFormaPago->getIdTipoFormaPago()->getid()]->getData());
					$oDetalleCaja->setMonto($tipos);
					$oDetalleCaja->setIdBanco(null);

					$em->persist($oDetalleCaja);
				}
			}
		}

		foreach ($arrayTiposC as $key => $tipos) {
			if ($tipos) {
				$oFormaPago = $em->getRepository('RebsolHermesBundle:FormaPago')->findOneBy(
					array(
						'idTipoFormaPago' => $key,
						'idEstado'        => $oEstadoAct,
						'idEmpresa'       => $oEmpresa
						)
					);

				$oBanco = $this->rCaja()->GetBancoIfCuentaCorriente($oCaja->getId(), $oFormaPago->getId());

				foreach ($oBanco as $Banco) {

					$oBanco = $em->getRepository("RebsolHermesBundle:Banco")->find($Banco['banco']);

					$oDetalleCaja = new DetalleCaja();
					$oDetalleCaja->setIdCaja($oCaja);
					$oDetalleCaja->setIdEstado($oEstadoAct);
					$oDetalleCaja->setIdFormaPago($oFormaPago);
					$oDetalleCaja->setNumeroDeposito($CerrarCaja['deposito_8_2']->getData());
					$oDetalleCaja->setMonto($tipos);
					$oDetalleCaja->setIdBanco($oBanco);

					$em->persist($oDetalleCaja);

					$cheques = $this->rCaja()->GetCheques($oCaja->getId(), $oFormaPago->getId());

					foreach ($cheques as $cheque) {
						$oDetalleCajaCheque = new DetalleCajaCheque();
						$oDetalleCajaCheque->setIdCaja($oCaja);
						$oDetalleCajaCheque->setIdEstado($oEstadoAct);
						$oDetalleCajaCheque->setnumeroCheque($cheque['numeroCheque']);
						$NumeroDeposito =  ($cheque['cuentaCorriente']!=null)? $CerrarCaja['deposito_8_2']->getData():
						$CerrarCaja['deposito_' . $oFormaPago->getid()]->getData();
						$oDetalleCajaCheque->setNumeroDeposito($NumeroDeposito);
						$oDetalleCajaCheque->setMonto($cheque['monto']);
						$oDetalleCajaCheque->setIdBanco($oBanco);

						$em->persist($oDetalleCajaCheque);
					}

				}
			}
		}
		$em->flush();

		return true;
	}

	/** INFORME CAJA */
	public function gestionInformeCajasAction(Request $request, $id)
	{
		return $this->render('RecaudacionBundle:Recaudacion\GestionCaja\Informes:InformeCajas.html.twig',
			array(
				'cajas'  => $this->rCaja()->GetCajasInforme($id),
				'cajero' => $this->rCaja()->GetCajeroInformeCajas($id)
				)
			);
	}

	public function gestionInformeGrantiasAction()
	{
		return $this->render('RecaudacionBundle:Recaudacion\GestionCaja\Informes:InformeGarantias.html.twig',
			array(
				'garantias' => $this->rCaja()->GetCajasGarantias($this->ObtenerEmpresaLogin()->getId())
				)
			);
	}

	public function gestionInformeCajaAction(Request $request, $id)
	{
		return $this->RenderViewInformeCaja(
			array(
				'id'        => $id,
				'print'     => 0,
				'from'      => 0,
				'path'      => 'Recaudacion\GestionCaja\Informes',
				'source'    => 'InformeCaja'
				)
			);
	}

	public function gestionInformeCajaPagosWebAction(Request $request) {
		return $this->RenderViewInformeCajaPagosWeb(
			array(
				'id'        => 0,
				'print'     => 0,
				'from'      => 0,
				'path'      => 'Recaudacion\GestionCaja\Informes',
				'source'    => 'InformeCajaWeb'
			)
		);
	}

	public function gestionInformeCajaPagosWebImprimirAction()
	{
		$html = $this->RenderViewInformeCajaPagosWeb(
			array(
				'id'        => 0,
				'print'     => 1,
				'from'      => 0,
				'path'      => 'Recaudacion\GestionCaja\Informes',
				'source'    => 'InformeCajaPDFWeb'
				)
			);

		return new Response( $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
			array(
				'orientation'  => 'Landscape'
				,'lowquality'  => false
				, 'page-width' => 1000
				,'zoom'        => '.6'
				,'page-size'   => 'Legal'
				)
			), 200,
		array(
			'Content-Type' => 'application/pdf'
			)
		);
	}

	public function gestionInformeCajaImprimirAction()
	{
		$html = $this->RenderViewInformeCaja(
			array(
				'id'        => $this->request('valoridCaja'),
				'print'     => 1,
				'from'      => 0,
				'path'      => 'Recaudacion\GestionCaja\Informes',
				'source'    => 'InformeCajaPDF'
				)
			);

		return new Response( $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
			array(
				'orientation'  => 'Landscape'
				,'lowquality'  => false
				, 'page-width' => 1000
				,'zoom'        => '.6'
				,'page-size'   => 'Legal'
				)
			), 200,
		array(
			'Content-Type' => 'application/pdf'
			)
		);
	}

	public function traeCorrelativoAction()
	{
		$em      = $this->getDoctrine()->getManager();
		$oCaja   = $em->getRepository('RebsolHermesBundle:Caja')->find($this->ajax('caja'));
		$session = $this->container->get('request_stack')->getCurrentRequest()->getSession();

		if (!$oCaja) {

			$session->getFlashBag()->add('errorCajaRecaudacion', 'No Tiene Caja Regsitrada');
			$respuesta = "no";
		} else {

			$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->findBy(
				array(
					"idUbicacionCaja" => $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid(),
					"idEstado"        => $this->estado('EstadoActivo'),
					"idEstadoPila"    => $this->estado('EstadoPilaActiva')
					)
				);

			$arrTalonario = array();
			foreach ($oTalonario as $t)
			{
				$arrTalonario[] = $t->getId();
			}
			$this->get('session')->set('idTalonario', $arrTalonario);
			$respuesta = "ok";
		}

		return new Response($respuesta);
	}
}
