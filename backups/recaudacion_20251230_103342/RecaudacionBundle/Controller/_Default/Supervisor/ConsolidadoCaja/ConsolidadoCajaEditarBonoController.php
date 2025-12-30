<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\ConsolidadoCaja;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Form\Type\Supervisor\ConsolidadoCaja\MediosPago2Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsolidadoCajaEditarBonoController extends SupervisorController {

	public function editarBonoAction($idPago){

		$informacionPaciente = array();
		$arrayFormaPago      = array();
		$arrayOtrosFormaPago = array();

		//Obtenemos por sesión la sucursal
		$sucursal = $this->get('session')->get('sucursal');

		$em     = $this->getDoctrine()->getManager();

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		//Obtenemos la información del paciente del pago
		$InformacionPaciente = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerInformacionPaciente($idPago, ($estadoApi === 'core') ? 1 : 0);
		//Obtenemos el objeto empresa
		$oEmpresa = $this->ObtenerEmpresaLogin();

		//Obtenemos el objeto estado activo
		$oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('Estado.activo'));

		//Declaramos un array para almacenar las formas de pago
		$arrayFormasPago = array();

		//Obtenemos las formas de pago por estado y empresa
		$idFormaspago = $em->getRepository('RebsolHermesBundle:FormaPago')->ObtieneFormaPagoEditarBono($oEstado, $oEmpresa);

		//Obtenemos las formas de pago por el id del pago
		$idCoord = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->ObtieneFormaPagoPorDocumento($idPago);
		//echo"<pre>";var_dump($idCoord);exit;
		//Recorremos las formas de pago para obtener las coordenadas dinámicamente
		for($i = 0; $i<count($idCoord);$i++){
			$indice[] = $i;
		}

		//Declaramos el array que contendrá las formas de pago
		$arrayFormasPago[] = 0;

		//Recorremos las formas de pago para obtener las coordenadas dinámicamente
		foreach ($idFormaspago as $id){
			$arrayFormasPago[] = $id['id'];

		}
		//echo"<pre>";var_dump($arrayFormasPago);exit;
		//Declaramos el repositorio forma de pago
		$rRepository = $this->getDoctrine()->getRepository("RebsolHermesBundle:FormaPago");

		//La información del paciente obtenida, la recorremos para obtener las formas de pago utilizadas por dicho paciente
		foreach($InformacionPaciente as $paciente){
			$idTipoFormaPago   = $paciente['idTipoFormaPago'];
			$ListadoMediosPago = $rRepository->ListadoFormasDePagoParaMediosPago2($oEmpresa->getId(), $oEstado->getId(), $idTipoFormaPago);
			$arrayFormaPago[]  = array_shift($ListadoMediosPago);
		}
		//echo"<pre>";var_dump($arrayFormaPago);exit;
		//Recorremos las otras formas de pago que son Lasik e Imed
		foreach ($idFormaspago as $id){
			$arrayFormasOtrosPago[] = $id['id'];
		}

		//La información del paciente obtenida, la recorremos para obtener las formas de pago utilizadas por dicho paciente pero que son Lasik e Imed
		foreach($InformacionPaciente as $paciente){
			$idTipoFormaPago   = $paciente['idTipoFormaPago'];
			$ListadoOtrosMedios = $rRepository->ListadoFormasDePagoParaOtrosMedios2($idTipoFormaPago);
			$arrayOtrosFormaPago[] = array_shift($ListadoOtrosMedios);
		}

		//Creamos el formulario
		$edit_form = $this->createForm(MediosPago2Type::class, null,
			array(
				'validaform'      => null,
				'idFrom'          => $arrayFormasPago,
				'idCantidad'      => $indice,
				'clone'           => false,
				'nuevo'           => true,
				'sucursal'        => $sucursal,
				'iEmpresa'        => $oEmpresa->getId(),
				'estado_activado' => $this->container->getParameter('estado_activo'),
				'idFromOtros'     => $arrayFormasOtrosPago,
				'database_default'=> $this->obtenerEntityManagerDefault()
				)
			);
		//echo"<pre>";\Doctrine\Common\Util\Debug::dump($edit_form);exit;
		//Obtenemos el tipo de forma de pago
		$obtenerTipoFormaPago = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerTipoFormaPago($idPago);
		//echo"<pre>";var_dump($obtenerTipoFormaPago);exit;
		//echo"<pre>";\Doctrine\Common\Util\Debug::dump($obtenerTipoFormaPago);exit;

		//Declaramos las cantidades de cada uno de las formas de pago para que aumente su posición a medida que se va recorriendo.
		$idCantidad1  = 0;
		$idCantidad2  = 0;
		$idCantidad3  = 0;
		$idCantidad4  = 0;
		$idCantidad5  = 0;
		$idCantidad6  = 0;
		$idCantidad7  = 0;
		$idCantidad8  = 0;
		$idCantidad9  = 0;
		$idCantidad10 = 0;

		//Array que almacenará la información y que será enviada a la vista.
		$arrayx[] = 0;

		/*Recorrido basado en el DQL $obtenerTipoFormaPago para que muestre los valores de cada uno de las formas de pago utilizadas en el pago de
		forma dinámica*/




		foreach($obtenerTipoFormaPago as $t){
			//echo($t['tipoForma']);exit;
			//Si es forma de pago de tipo Efectivo...
			if($t['tipoForma'] == 1){
				//echo"<pre>";var_dump($idFormaspago);exit;
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad1]->setData($t['montoForma']);
				$idCantidad1 = $idCantidad1 + 1;
			}
			//Si es forma de pago de tipo Gratuidad...
			elseif($t['tipoForma'] == 2){
				$oGratuidad = $em->getRepository('RebsolHermesBundle:MotivoGratuidad')->find($t['idMotivoGratuidad']);
				$edit_form['idGratuidad']->setData($oGratuidad);
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad2]->setData($t['montoForma']);
				$idCantidad2 = $idCantidad2 + 1;
			}
			//Si es forma de pago de tipo Bono Electrónico...
			elseif($t['tipoForma'] == 3){
				$edit_form['bono_'.$t['tipoForma'].'_'.$idCantidad3]->setData($t['numeroBonoElectronico']);
				$edit_form['Bonificacion_'.$t['tipoForma'].'_'.$idCantidad3]->setData($t['montoDoc']);
				$edit_form['Seguro_'.$t['tipoForma'].'_'.$idCantidad3]->setData($t['seguro']);
				$edit_form['copago_'.$t['tipoForma'].'_'.$idCantidad3]->setData($t['imed']);
				$idCantidad3 = $idCantidad3 + 1;
			}
			//Si es forma de pago de tipo Tarjeta de Crédito...
			elseif($t['tipoForma'] == 4){
				//QUITAR ESTA VALIDACIÓN CUANDO ESTÉ REGULARIZADO
				if(!$t['idTarjetaCredito']){
					$tar = 1;
				}else{
					$tar = $t['idTarjetaCredito'];
				}
				//HASTA ACÁ
				$oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')->find($tar);
				$edit_form['TarjetaCredito']->setData($oTarjetaCredito);
				$edit_form['voucher_'.$t['tipoForma'].'_'.$idCantidad4]->setData($t['numeroTarjeta']);
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad4]->setData($t['montoDoc']);
				$idCantidad4 = $idCantidad4 + 1;
			}
			//Si es forma de pago de tipo Bono Manual...
			elseif($t['tipoForma'] == 5){
				$edit_form['bono_'.$t['tipoForma'].'_'.$idCantidad5]->setData($t['numeroCheque']);
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad5]->setData($t['montoDoc']);
				$idCantidad5 = $idCantidad5 + 1;
			}
			//Si es forma de pago de tipo Tarjeta de Débito...
			elseif($t['tipoForma'] == 6){
				//QUITAR ESTA VALIDACIÓN CUANDO ESTÉ REGULARIZADO
				if(!$t['idBanco']){
					$tar = 1;
				}else{
					$tar = $t['idBanco'];
					//var_dump($tar);exit;
				}
				//HASTA ACÁ
				$oTarjetaCredito = $em->getRepository('RebsolHermesBundle:Banco')->find($tar);
				$edit_form['TarjetaDebito']->setData($oTarjetaCredito);
				$edit_form['voucher_'.$t['tipoForma'].'_'.$idCantidad6]->setData($t['numeroTarjeta']);
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad6]->setData($t['montoDoc']);
				$idCantidad6 = $idCantidad6 + 1;
			}
			//Si es forma de pago de tipo Cheque a Fecha...
			elseif($t['tipoForma'] == 7){
				$edit_form['cheque_'.$t['tipoForma'].'_'.$idCantidad7]->setData($t['numeroCheque']);
				//Obtenemos y enviamos el objeto por ser de tipo entity
				$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($t['idBanco']);
				$edit_form['banco_'.$t['tipoForma'].'_'.$idCantidad7]->setData($oBanco);
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad7]->setData($t['montoDocumento']);
				$edit_form['rut_'.$t['tipoForma'].'_'.$idCantidad7]->setData($t['rutCheque']);
				$edit_form['nombre_'.$t['tipoForma'].'_'.$idCantidad7]->setData($t['nombreCheque']);
				//Obtenemos y enviamos el objeto por ser de tipo entity
				$oCondicion = $em->getRepository('RebsolHermesBundle:CondicionPago')->find($t['idCondicion']);
				$edit_form['condicion_'.$t['tipoForma'].'_'.$idCantidad7]->setData($oCondicion);
				$idCantidad7 = $idCantidad7 + 1;
			}
			//Si es forma de pago de tipo Cheque al Día...
			elseif($t['tipoForma'] == 8){
				$numeroCheque = $edit_form['cheque_'.$t['tipoForma'].'_'.$idCantidad8]->setData($t['numeroCheque']);
				//Obtenemos y enviamos el objeto por ser un entity
				$oBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($t['idBanco']);
				$edit_form['banco_'.$t['tipoForma'].'_'.$idCantidad8]->setData($oBanco);
				$edit_form['monto_'.$t['tipoForma'].'_'.$idCantidad8]->setData($t['montoDocumento']);
				$edit_form['rut_'.$t['tipoForma'].'_'.$idCantidad8]->setData($t['rutCheque']);
				$edit_form['nombre_'.$t['tipoForma'].'_'.$idCantidad8]->setData($t['nombreCheque']);
				$edit_form['condicion_'.$t['tipoForma'].'_'.$idCantidad8]->setData($t['condicion']);
				$idCantidad8 = $idCantidad8 + 1;
			}
			//Si es forma de pago de tipo Carta Convenio Lasik...
			elseif($t['tipoForma'] == 9){
				//var_dump('folio_'.$t['tipoForma']);exit;
				$edit_form['folio_'.$t['tipoForma']]->setData($t['numeroBonoElectronico']);
				$edit_form['monto_'.$t['tipoForma']]->setData($t['montoDoc']);
				$idCantidad9 = $idCantidad9 + 1;
			}
			//Si es forma de pago de tipo Carta Convenio Imed...
			elseif($t['tipoForma'] == 10){
				$edit_form['folio_'.$t['tipoForma']]->setData($t['numeroBonoElectronico']);
				$edit_form['monto_'.$t['tipoForma']]->setData($t['montoDoc']);
				$idCantidad10 = $idCantidad10 + 1;
			}

		}

		//Desglosamos el array para validar según la forma de pago
		$arrayx =
		array(
			'1'  =>  $idCantidad1,
			'2'  =>  $idCantidad2,
			'3'  =>  $idCantidad3,
			'4'  =>  $idCantidad4,
			'5'  =>  $idCantidad5,
			'6'  =>  $idCantidad6,
			'7'  =>  $idCantidad7,
			'8'  =>  $idCantidad8,
			'9'  =>  $idCantidad9,
			'10' => $idCantidad10
			);

		//============================================================================
		// VALIDACIONES PARA LAS FORMAS DE PAGO QUE SE PUEDEN GENERAR DINÁMICAMENTE
		//============================================================================

		//Si es bono electrónico, tiene una cantidad mas de 0 y es distinto de nulo, se recorre mediante un for para que aparezca según cantidad
		if (($arrayx['3'] == $idCantidad3) && ($arrayx['3'] != null)) {
			for ($i = 0; $i < $arrayx['3']; $i++) {
				$arrayBonoElectronico[] = $i;
			}
		}
		//De lo contrario, se le asigna un 0 y muestra sólo 1 vez
		else{
			$arrayBonoElectronico[] = 0;
		}
		//Si es bono manual, tiene una cantidad mas de 0 y es distinto de nulo, se recorre mediante un for para que aparezca según cantidad
		if (($arrayx['5'] == $idCantidad5) && ($arrayx['5'] != null)) {
			for ($i = 0; $i < $arrayx['5']; $i++) {
				$arrayBonoManual[] = $i;
			}
		}
		//De lo contrario, se le asigna un 0 y muestra sólo 1 vez
		else{
			$arrayBonoManual[] = 0;
		}
		//Si es cheque a fecha, tiene una cantidad mas de 0 y es distinto de nulo, se recorre mediante un for para que aparezca según cantidad
		if (($arrayx['7'] == $idCantidad7) && ($arrayx['7'] != null)) {
			for ($i = 0; $i < $arrayx['7']; $i++) {
				$arrayChequeFecha[] = $i;
			}
		}
		//De lo contrario, se le asigna un 0 y muestra sólo 1 vez
		else{
			$arrayChequeFecha[] = 0;
		}
		//Si es cheque al día, tiene una cantidad mas de 0 y es distinto de nulo, se recorre mediante un for para que aparezca según cantidad
		if (($arrayx['8'] == $idCantidad8) && ($arrayx['8'] != null)) {
			for ($i = 0; $i < $arrayx['8']; $i++) {
				$arrayChequeDia[] = $i;
			}
		}
		//De lo contrario, se le asigna un 0 y muestra sólo 1 vez
		else{
			$arrayChequeDia[] = 0;
		}

		// echo "<pre>"; var_dump($informacionPaciente); exit(-1);
		//Renderizamos a InformeCajaEditarBono.html.twig',
		$renderView = $this->renderView('RecaudacionBundle:Supervisor/ConsolidadoCaja:InformeCajaEditarBono.html.twig', array(
				'informacionPaciente' => $InformacionPaciente,
				'mediospago_form'     => $edit_form->createView(),
				'idPago'              => $idPago,
				'listadoMediosPagos'  => $arrayFormaPago,
				'listadoOtrosMedios'  => $arrayOtrosFormaPago,
				'cantidades3'         => $arrayBonoElectronico,
				'cantidades5'         => $arrayBonoManual,
				'cantidades7'         => $arrayChequeFecha,
				'cantidades8'         => $arrayChequeDia,
				'cantidad'            => 0,
				'BonoElectronico'     => $arrayx['3'],
				'BonoManual'          => $arrayx['5'],
				'ChequeFecha'         => $arrayx['7'],
				'ChequeDia'           => $arrayx['8'],
				'Lasik'               => $arrayx['9'],
				'Imed'                => $arrayx['10'],
				'coreApi'             => ($estadoApi === "core")?1:0
				)
			);
		return new Response($renderView);
	}


	public function actualizarBonoAction(Request $request, $idPago)
	{
		//Obtenemos por sesión la sucursal
		$sucursal = $this->get('session')->get('sucursal');

		$em = $this->getDoctrine()->getManager();

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		$idPago = (int)$idPago;

		//Obtenemos la información del paciente del pago
		$InformacionPaciente = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerInformacionPaciente($idPago, ($estadoApi === "core")?1:0);

		//Obtenemos el objeto empresa
		$oEmpresa = $this->ObtenerEmpresaLogin();

		//Obtenemos el objeto estado activo
		$oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));

		//Declaramos un array para almacenar las formas de pago
		$arrayFormasPago = array();

		//Obtenemos las formas de pago por estado y empresa
		$idFormaspago = $em->getRepository('RebsolHermesBundle:FormaPago')->ObtieneFormaPagoEditarBono($oEstado, $oEmpresa);

		//Obtenemos las formas de pago por el id del pago
		$idCoord = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->ObtieneFormaPagoPorDocumento($idPago);

		//Recorremos las formas de pago para obtener las coordenadas dinámicamente
		for($i = 0; $i<count($idCoord);$i++){
			$indice[] = $i;
		}

		//Recorremos las formas de pago para obtener las coordenadas dinámicamente
		foreach ($idFormaspago as $id){
			$arrayFormasPago[] = $id['id'];
		}

		//Declaramos el repositorio forma de pago
		$rRepository = $this->getDoctrine()->getRepository("RebsolHermesBundle:FormaPago");

		//La información del paciente obtenida, la recorremos para obtener las formas de pago utilizadas por dicho paciente
		foreach($InformacionPaciente as $paciente){
			$idTipoFormaPago   = $paciente['idTipoFormaPago'];
			$ListadoMediosPago = $rRepository->ListadoFormasDePagoParaMediosPago2($oEmpresa, $oEstado, $idTipoFormaPago);
			$arrayFormaPago[]  = array_shift($ListadoMediosPago);

		}

		$ListadoOtrosMedios = $rRepository->ListadoFormasDePagoParaOtrosMedios($oEmpresa, $oEstado);

		$arrayOtrosFormasPago = array();

		foreach ($ListadoOtrosMedios as $id){

			$arrayOtrosFormasPago[] = $id['id'];
		}

		//Creamos el formulario
		$edit_form = $this->createForm(MediosPago2Type::class, null,
			array(
				'validaform'      => null,
				'idFrom'          => $arrayFormasPago,
				'idCantidad'      => $indice,
				'clone'           => false,
				'nuevo'           => true,
				'sucursal'        => $sucursal,
				'iEmpresa'        => $oEmpresa->getId(),
				'estado_activado' => $this->container->getParameter('estado_activo'),
				'idFromOtros'     => $arrayOtrosFormasPago,
				'database_default'=> $this->obtenerEntityManagerDefault()
				)
			);

		//Obtenemos el tipo de forma de pago
		$obtenerTipoFormaPago = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerTipoFormaPago($idPago);

		//Declaramos las cantidades de cada uno de las formas de pago para que aumente su posición a medida que se va recorriendo.
		$idCantidad2  = 0;
		$idCantidad3  = 0;
		$idCantidad4  = 0;
		$idCantidad5  = 0;
		$idCantidad6  = 0;
		$idCantidad7  = 0;
		$idCantidad8  = 0;
		$idCantidad9  = 0;
		$idCantidad10 = 0;

		$edit_form->handleRequest($request);

		//Obtenemos cada uno de las formas de pago para validar si existen y proceder a grabar en la base de datos
		foreach($obtenerTipoFormaPago as $t){

			//Si es forma de pago de tipo Gratuidad...
			if($t['tipoForma'] == 2){
				$nombreGratuidad = $edit_form['idGratuidad']->getData();
				if(!$nombreGratuidad){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}
				$idCantidad2 = $idCantidad2 + 1;
				$oDetallePagoCuenta = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->find($t['idDetallePagoCuenta']);

				$oDetallePagoCuenta->setIdMotivoGratuidad($nombreGratuidad);
				$em->persist($oDetallePagoCuenta);
			}
			//Si es forma de pago de tipo Bono Electrónico...
			elseif($t['tipoForma'] == 3){
				$numeroBono = $edit_form['bono_' . $t['tipoForma'] . '_' . $idCantidad3]->getData();
				if(!$numeroBono){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}
				$idCantidad3 = $idCantidad3 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setNumeroDocumentoGeneral($numeroBono);
				$em->persist($oDocumentoPago);
			}
			//Si es forma de pago de tipo Tarjeta de Crédito...
			elseif($t['tipoForma'] == 4){

				$nombreTarjetaCredito = $edit_form['TarjetaCredito']->getData();
				$numeroVoucher = $edit_form['voucher_'.$t['tipoForma'].'_'.$idCantidad4]->getData();
				if(!$nombreTarjetaCredito || !$numeroVoucher){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}
				$idCantidad4 = $idCantidad4 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setIdTarjetaCredito($nombreTarjetaCredito);
				$oDocumentoPago->setNumeroVoucher($numeroVoucher);
				$em->persist($oDocumentoPago);
			}
			//Si es forma de pago de tipo Bono Manual...
			elseif($t['tipoForma'] == 5){

				$numeroBonoManual = $edit_form['bono_'.$t['tipoForma'].'_'.$idCantidad5]->getData();
				if(!$numeroBonoManual){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}
				$idCantidad5 = $idCantidad5 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setNumeroDocumento($numeroBonoManual);
				$em->persist($oDocumentoPago);
			}
			//Si es forma de pago de tipo Tarjeta de Débito...
			elseif($t['tipoForma'] == 6){

				//Obtenemos el nombre de la tarjeta de débito con su objeto
				$nombreTarjetaDebito = $edit_form['TarjetaDebito']->getData();

				//Obtenemos el id del banco (que es la tarjeta de débito).
				$idBanco = $nombreTarjetaDebito->getId();

				//Con el id, obtenemos el objeto banco para posteriormente setearlo.
				$objBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($idBanco);

				//Obtenemos el número del voucher para setearlo
				$numeroVoucher = $edit_form['voucher_'.$t['tipoForma'].'_'.$idCantidad6]->getData();

				//Si ambas variables están vacías, se renderiza a la pantalla anterior
				if(!$nombreTarjetaDebito || !$numeroVoucher){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}
				$idCantidad6 = $idCantidad6 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);
				$oDocumentoPago->setIdBanco($objBanco);
				$oDocumentoPago->setNumeroVoucher($numeroVoucher);
				$em->persist($oDocumentoPago);
			}
			//Si es forma de pago de tipo Cheque a Fecha...
			elseif($t['tipoForma'] == 7){

				/*$numeroCheque = $edit_form['cheque_'.$t['tipoForma'].'_'.'0']->getData();
				$nombreBanco = $edit_form['banco_'.$t['tipoForma'].'_'.'0']->getData();
				$nombrePersona = $edit_form['nombre_'.$t['tipoForma'].'_'.'0']->getData();*/

				$numeroCheque = $edit_form['cheque_'.$t['tipoForma'].'_'.$idCantidad7]->getData();
				$nombreBanco = $edit_form['banco_'.$t['tipoForma'].'_'.$idCantidad7]->getData();
				$nombrePersona = $edit_form['nombre_'.$t['tipoForma'].'_'.$idCantidad7]->getData();

				if(!$numeroCheque || !$nombreBanco || !$nombrePersona){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}

				$idCantidad7 = $idCantidad7 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setNumeroDocumento($numeroCheque);
				$oDocumentoPago->setIdBanco($nombreBanco);
				$oDocumentoPago->setNombrePropietario($nombrePersona);
				$em->persist($oDocumentoPago);
			}
			//Si es forma de pago de tipo Cheque a Fecha...
			elseif($t['tipoForma'] == 8){

				/*$numeroCheque = $edit_form['cheque_'.$t['tipoForma'].'_'.'0']->getData();
				$nombreBanco = $edit_form['banco_'.$t['tipoForma'].'_'.'0']->getData();
				$nombrePersona = $edit_form['nombre_'.$t['tipoForma'].'_'.'0']->getData();*/

				$numeroCheque = $edit_form['cheque_'.$t['tipoForma'].'_'.$idCantidad8]->getData();
				$nombreBanco = $edit_form['banco_'.$t['tipoForma'].'_'.$idCantidad8]->getData();
				$nombrePersona = $edit_form['nombre_'.$t['tipoForma'].'_'.$idCantidad8]->getData();

				if(!$numeroCheque || !$nombreBanco || !$nombrePersona){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}

				$idCantidad8 = $idCantidad8 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setNumeroDocumento($numeroCheque);
				$oDocumentoPago->setIdBanco($nombreBanco);
				$oDocumentoPago->setNombrePropietario($nombrePersona);
				$em->persist($oDocumentoPago);
			}
			//Si es forma de pago de tipo Carta Convenio Lasik...
			elseif($t['tipoForma'] == 9){
				$numeroFolio = $edit_form['folio_'.$t['tipoForma']]->getData();

				if(!$numeroFolio){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}

				$idCantidad9 = $idCantidad9 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setNumeroDocumentoGeneral($numeroFolio);
				$em->persist($oDocumentoPago);

			}
			//Si es forma de pago de tipo Carta Convenio Imed...
			elseif($t['tipoForma'] == 10){
				$numeroFolio = $edit_form['folio_'.$t['tipoForma']]->getData();

				if(!$numeroFolio){
					return $this->redirect($this->generateUrl('Caja_Supervisor_ConsolidadoCaja_EditarBono', array('idPago' => $idPago)));
				}

				$idCantidad9 = $idCantidad10 + 1;
				$oDocumentoPago = $em->getRepository('RebsolHermesBundle:DocumentoPago')->find($t['idDocumentoPago']);

				$oDocumentoPago->setNumeroDocumentoGeneral($numeroFolio);
				$em->persist($oDocumentoPago);
			}

			$em->flush();
		}
			//Se genera el modal para que despliegue el mensaje exitoso en la vista
		$this->get('session')->getFlashBag()->add('EditarBono', 'Your changes were saved!');
		return new Response("Editado");
	}

}

