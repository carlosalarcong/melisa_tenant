<?php

namespace App\Controller\Caja\Supervisor\AutorizacionDescuentos;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\AutorizacionDescuentos\render;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AutorizacionDescuentosApruebaController extends SupervisorController {

	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
	 */
	public function apruebaAction(Request $request, $idDiferencia) {
		$this->ValidadPeticionAjax($request, 'Supervisor_AutorizacionDescuentos');
		$em = $this->getDoctrine()->getManager();

		//Buscamos el objeto que estén en estado "cajero pide autorización"
		$oDiferenciasEnEspera = $em->getRepository('RebsolHermesBundle:Diferencia')->
		findBy(array('id' => $idDiferencia));

		//Obtenemos el objeto de estado inactivo para posteriormente colocárselo a los datos activos.
		$oEstadoAutorizado = $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('estado_diferencia_autorizada'));
		$fechaAutorizacion = new \Datetime();

		foreach($oDiferenciasEnEspera as $enEspera){

			$enEspera->setIdEstadoDiferencia($oEstadoAutorizado);
			$enEspera->setIdUsuarioAutorizacion($this->getUser());
			$enEspera->setFechaAutorizacion($fechaAutorizacion);
			//Se prepara el objeto para que sea insertado
			$em->persist($enEspera);
			//Se actualiza el dato en la base de datos
			$em->flush();
		}

		$bValidEstado = $this->aOpcionesEstado;
		$bValid = $bValidEstado["1"];
		return new Response(json_encode($bValid));
	}

}