<?php

namespace App\Controller\Caja\Supervisor\CorrelativoBoletas;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorrelativoBoletasInformacionController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: verAction() Muestra la información de un determinado Nivel Instrucción (id)
	 */
	public function informacionAction(Request $request, $idTalonario) {

		$this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');
		$em = $this->getDoctrine()->getManager();

		$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario);


		if (!$oTalonario) {
			throw $this->createNotFoundException('Unable to find Talonario entity.');
		}

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/CorrelativoBoletas:informacion.html.twig', array('oTalonario' => $oTalonario));
		return new Response($renderView);
	}
}