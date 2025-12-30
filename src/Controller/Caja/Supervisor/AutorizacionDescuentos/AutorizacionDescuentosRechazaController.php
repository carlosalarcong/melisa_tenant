<?php

namespace App\Controller\Caja\Supervisor\AutorizacionDescuentos;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\AutorizacionDescuentos\render;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AutorizacionDescuentosRechazaController extends SupervisorController {

	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
	 */
	public function rechazaAction(Request $request, $idDiferencia) {
		$this->ValidadPeticionAjax($request, 'Supervisor_AutorizacionDescuentos');
		$em = $this->getDoctrine()->getManager();

		//Buscamos el objeto que estén en estado "cajero pide autorización"
		$oDiferenciasEnEspera = $em->getRepository('RebsolHermesBundle:Diferencia')->
		findBy(array('id' => $idDiferencia));

		//Obtenemos el objeto de estado inactivo para posteriormente colocárselo a los datos activos.
		$oEstadoRechazado = $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('estado_diferencia_rechazada'));
		$fechaAnulacion = new \Datetime();

		foreach($oDiferenciasEnEspera as $enEspera){

			$enEspera->setIdEstadoDiferencia($oEstadoRechazado);
			$enEspera->setIdUsuarioAnulacion($this->getUser());
			$enEspera->setFechaAnulacion($fechaAnulacion);
			//Se prepara el objeto para que sea insertado
			$em->persist($enEspera);
			//Se actualiza el dato en la base de datos
			$em->flush();
		}

		$bValidEstado = $this->aOpcionesEstado;
		$bValid = $bValidEstado["4"];
		return new Response(json_encode($bValid));
	}

}