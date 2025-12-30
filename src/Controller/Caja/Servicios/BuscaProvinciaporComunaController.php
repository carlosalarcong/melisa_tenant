<?php

namespace App\Controller\Caja\Servicios;

use App\Controller\Caja\RecaudacionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BuscaProvinciaporComunaController extends RecaudacionController {

	public function indexAction(Request $request) {

		$em       = $this->getDoctrine()->getManager();
		$oEstado  = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$comuna   = $this->container->get('request_stack')->getCurrentRequest()->query->get('comuna');
		$fService = $this->get('Caja_valida');
		$result   = $fService->ProvinciaporComuna($comuna, $oEstado);

		return new Response(json_encode($result));

	}
}
