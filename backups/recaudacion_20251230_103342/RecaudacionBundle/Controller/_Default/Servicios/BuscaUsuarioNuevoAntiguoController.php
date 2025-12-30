<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Servicios;

use Rebsol\RecaudacionBundle\Controller\RecaudacionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BuscaUsuarioNuevoAntiguoController extends RecaudacionController {

	public function indexAction(Request $request) {
		$idPersona = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPersona');
		$fechahoy =  date("Y-m-d");
		$fService = $this->get('Caja_valida');
		$result= $fService->UsuarioNuevo($idPersona, $fechahoy);
		return new Response(json_encode($result));
	}
}
