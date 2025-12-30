<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\UbicacionCaja;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajaEliminarController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: eliminarAction() Elimina/Desactiva un determinado Nivel Instrucción (id)
	 */
	public function eliminarAction(Request $request, $id) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCaja');
		$bValidDelete = $this->aOpcionesEliminar;
		$bValid = "";
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('RebsolHermesBundle:UbicacionCaja')->find($id);

		if (!$entity) {
			$bValid = $bValidDelete["1"];
		}

		$sCommon = $this->get('common');

		$rRepository = $this->getDoctrine()->getRepository('RebsolHermesBundle:UbicacionCaja');
		$bValid = $rRepository->eliminarObjeto($entity, $sCommon->obtenerEstado($this->container->getParameter('estado_inactivo')));

		if(!$bValid){
			$bValid = $bValidDelete["2"];
		} else {
			$bValid = $bValidDelete["0"];
		}
		return new Response(json_encode($bValid));
	}
}