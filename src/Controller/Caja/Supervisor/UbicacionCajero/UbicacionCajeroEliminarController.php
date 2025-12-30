<?php

namespace App\Controller\Caja\Supervisor\UbicacionCajero;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajeroEliminarController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: eliminarAction() Elimina/Desactiva un determinado Nivel Instrucción (id)
	 */
	public function eliminarAction(Request $request, $id) {

		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCajero');

		$bValidDelete = $this->aOpcionesEliminar;
		$bValid       = "";
		$em           = $this->getDoctrine()->getManager();
		$entity       = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->find($id);

		if (!$entity) {
			$bValid = $bValidDelete["1"];
		}

		$rRepository = $this->getDoctrine()->getRepository('RebsolHermesBundle:RelUbicacionCajero');

		$estadoRelUbicacionCajeroInactivo = $em->getRepository('RebsolHermesBundle:EstadoRelUbicacionCajero')->find($this->container->getParameter('EstadoRelUbicacionCajero.Inactivo'));
		$bValid                           = $rRepository->eliminarObjeto($entity, $estadoRelUbicacionCajeroInactivo);

		if(!$bValid){
			$bValid = $bValidDelete["2"];
		} else {
			$bValid = $bValidDelete["0"];
		}
		return new Response(json_encode($bValid));
	}
}