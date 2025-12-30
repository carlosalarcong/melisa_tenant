<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\ConsolidadoCaja;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsolidadoCajaEditarController extends SupervisorController {

	public function GestionInformeCajaEditarAction(request $request, $id) {

		//Solicitar la renderización a CajaController.php enviando el idCaja
		return $this->RenderViewInformeCaja(
			array(
				'id'        => $id,
				'from'      => 1,
				'path'      => 'Supervisor\ConsolidadoCaja',
				'print'     => 0,
				'source'    => 'InformeCajaEditar'
				)
			);
	}

	public function editaNumeroDepositoAction(){

		$em = $this->getDoctrine()->getManager();

		$id             = $this->container->get('request_stack')->getCurrentRequest()->query->get('idDeposito');
		$numeroDeposito = $this->container->get('request_stack')->getCurrentRequest()->query->get('numeroDepositoNuevo');

		$idDeposito          = (int)$id;
		$numeroDepositoNuevo = (int)$numeroDeposito;

		$oDetalleCaja = $em->getRepository('RebsolHermesBundle:DetalleCaja')->find($idDeposito);

		if(!$oDetalleCaja ){
			$numeroDepositoNuevo = 'no';
		}else{
			$oDetalleCaja->setNumeroDeposito($numeroDepositoNuevo);
			$em->persist($oDetalleCaja);
			$em->flush();
		}
		return new Response(json_encode($numeroDepositoNuevo));
	}
}
