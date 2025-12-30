<?php

namespace App\Controller\Caja\Supervisor\MantenedorFolios;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\MantenedorFolios\render;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MantenedorFoliosAuditoriaController extends SupervisorController {

		/**
		 * @return render
		 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
		 */
		public function auditoriaAction(Request $request, $idDetalleTalonario) {

			$this->ValidadPeticionAjax($request, 'Supervisor_MantenedorFolios');
			$em = $this->getDoctrine()->getManager();

			$oDetalleBoleta = $em->getRepository("RebsolHermesBundle:DetalleTalonario")->
			findBy(
				array('id' => $idDetalleTalonario)
				);


			if (!$oDetalleBoleta) {
				throw $this->createNotFoundException('Unable to find detalle boleta.');
			}

			$renderView = $this->renderView('RecaudacionBundle:Supervisor/MantenedorFolios:show.html.twig',
				array('oDetalleBoleta' => $oDetalleBoleta));

			return new Response($renderView);
		}
	}