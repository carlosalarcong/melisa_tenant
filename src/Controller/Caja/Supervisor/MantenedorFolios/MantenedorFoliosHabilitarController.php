<?php

namespace App\Controller\Caja\Supervisor\MantenedorFolios;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\MantenedorFolios\render;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MantenedorFoliosHabilitarController extends SupervisorController {

	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
	 */
	public function habilitarAction(Request $request, $idDetalleTalonario) {
		$this->ValidadPeticionAjax($request, 'Supervisor_MantenedorFolios');
		$em = $this->getDoctrine()->getManager();

		$oDetalleTalonario = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->
		findBy(array('id' => $idDetalleTalonario));

		$oEstadoAnulado = $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->
		find($this->container->getParameter('estado_detalle_talonario_emitidas'));

		$fechaAnulacion = new \Datetime();
		$idPaciente = $oDetalleTalonario[0]->getIdPaciente();


		if ($idPaciente == null) {

			foreach ($oDetalleTalonario as $detalle) {
				//Se prepara el objeto para que sea removido
				$em->remove($detalle);
				//Se actualiza el dato en la base de datos
				$em->flush();
			}
		} else {

			foreach ($oDetalleTalonario as $detalle) {

				$detalle->setIdEstadoDetalleTalonario($oEstadoAnulado);
				$detalle->setIdUsuarioDesanulacion($this->getUser());
				$detalle->setFechaDesanulacion($fechaAnulacion);
				//Se prepara el objeto para que sea insertado
				$em->persist($detalle);
				//Se actualiza el dato en la base de datos
				$em->flush();
			}
		}

		$bValidEstado = $this->aOpcionesEstado;
		$bValid = $bValidEstado["3"];
		return new Response(json_encode($bValid));

	}
}