<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\UbicacionCaja;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajaVerController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: verAction() Muestra la información de un determinado Nivel Instrucción (id)
	 */
	public function verAction(Request $request, $id) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCaja');
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('RebsolHermesBundle:UbicacionCaja')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find UbicacionCaja entity.');
		}

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCaja:show.html.twig',
			array(
			   'entity'      => $entity
			   )
			);
		return new Response($renderView);
	}
}