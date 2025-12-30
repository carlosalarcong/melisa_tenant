<?php

namespace App\Controller\Caja\Supervisor\CorrelativoBoletas;

use App\Controller\Caja\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorrelativoBoletasVerController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: verAction() Muestra la información de un determinado Nivel Instrucción (id)
	 */
	public function verAction(Request $request, $idTalonario) {
		$this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');
		$em = $this->getDoctrine()->getManager();

		$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario);

		$NombreEstado = $oTalonario->getIdEstadoPila()->getNombre();

		if (!$oTalonario) {
			throw $this->createNotFoundException('Unable to find Talonario entity.');
		}

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/CorrelativoBoletas:show.html.twig', array('oTalonario' => $oTalonario, 'nombreEstado' => $NombreEstado));
		return new Response($renderView);
	}
}