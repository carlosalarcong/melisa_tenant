<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Recaudacion\Pago\Dependencias;

use Rebsol\RecaudacionBundle\Controller\RecaudacionController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author ovaldenegro
 * @version 0.1.0
 * Fecha Creación: 08/11/2013
 * Participantes:N/N
 *
 */
class AvanzadaController extends RecaudacionController {

	public function indexAdvAction() {

		$apep      = $this->container->get('request_stack')->getCurrentRequest()->query->get('apep');
		$apem      = $this->container->get('request_stack')->getCurrentRequest()->query->get('apem');

		$fService  = $this->get('Caja_valida');
		$oEmpresa  = $this->ObtenerEmpresaLogin();
		$result    = $fService->validaadvDB($apep, $apem,  $oEmpresa);
		$resultlen = count($result);



		if($resultlen > 0){
			$result = "muchos";
		} else {
			$result = "no";
		}

		return new Response(json_encode($result));
	}

	public function domicilioAction() {

		$idPersona = $this->container->get('request_stack')->getCurrentRequest()->query->get('id');
		$fService  = $this->get('Caja_valida');
		$result    = $fService->consultaDomicilio($idPersona);

		return new Response(json_encode($result));

	}

	public function obtieneDatosNUsuariosAction() {

		$apep     = $this->container->get('request_stack')->getCurrentRequest()->query->get('apep');
		$apem     = $this->container->get('request_stack')->getCurrentRequest()->query->get('apem');
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$fService = $this->get('Caja_valida');
		$result   = $fService->ObtieneDatosNUsuarios($apep, $apem,  $oEmpresa);

		return $this->render('CajaBundle:Recaudacion\Pago\Pago_Form:ListaPaciente.html.twig', array(
			'resultados' => $result
			));
	}

	public function obtieneDatosUnUsuarioAction() {

		$idPersona = $this->container->get('request_stack')->getCurrentRequest()->query->get('idpaciente');
		$oEmpresa  = $this->ObtenerEmpresaLogin();
		$fService  = $this->get('Caja_valida');
		$result    = $fService->ObtieneDatosUnUsuariodesdeLista($idPersona,  $oEmpresa);

		return new Response(json_encode($result));

	}

	public function resultadoBusquedaAvanzadaPacienteAction(){

		$postData       = $this->container->get('request_stack')->getCurrentRequest()->get('busquedaAvanzadaPaciente');

		$nombre         = $postData["nombres"];
		$apPaterno      = $postData["apPaterno"];
		$apMaterno      = $postData["apMaterno"];
		$opcionBusqueda = $postData["opcionBusqueda"];

		if($opcionBusqueda == 0){
			$strlike      = "=";
			$strNombres   = $nombre;
			$strApPaterno = $apPaterno;
			$strApMaterno = $apMaterno;
		}

		if($opcionBusqueda == 1){
			$strlike      = "LIKE";
			$strNombres   = $nombre."%";
			$strApPaterno = $apPaterno."%";
			$strApMaterno = $apMaterno."%";
		}

		if($opcionBusqueda == 2){
			$strlike      = "LIKE";
			$strNombres   = "%".$nombre."%";
			$strApPaterno = "%".$apPaterno."%";
			$strApMaterno = "%".$apMaterno."%";
		}

		$oEmpresa    = $this->ObtenerEmpresaLogin();

		$rRepository = $this->get('recaudacion.Persona');
		$entities    = $rRepository->obtenerAvanzadaHSJDD($oEmpresa, $strNombres, $strApPaterno, $strApMaterno, $strlike);

		return $this->render('RecaudacionBundle:Recaudacion/Pago/Pago_Form:ListaPaciente.html.twig', array(
			'pacientes' => $entities
			));
	}


}
