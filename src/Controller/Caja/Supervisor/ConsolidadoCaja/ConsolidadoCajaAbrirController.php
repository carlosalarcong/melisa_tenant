<?php

namespace App\Controller\Caja\Supervisor\ConsolidadoCaja;

use App\Controller\Caja\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsolidadoCajaAbrirController extends SupervisorController {

	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
	 */
	public function abrirAction(request $request, $id) {

		$em             = $this->getDoctrine()->getManager();

		//Formateamos la fecha
		$fechaHoy       = new \Datetime();
		$fechaHoyFormat = strtotime($fechaHoy->format('d-m-Y'));

		//Obtenemos el objeto caja
		$oCaja           = $em->getRepository('RebsolHermesBundle:Caja')->find($id);

		$fechaCaja       = $oCaja->getFechaApertura();
		$fechaCajaFormat = strtotime($fechaCaja->format('d-m-Y'));

		//Abre la caja
		$CajasSinCerrar = $em->getRepository('RebsolHermesBundle:Caja')->abrirCaja($oCaja->getIdUsuario()->getId());

		//Obtenemos el objeto de estado inactivo para posteriormente colocárselo a los datos activos.
		$oEstadoReaperturaAbierta = $em->getRepository('RebsolHermesBundle:EstadoReapertura')->
		find($this->container->getParameter('estado_reapertura_abierta'));
		if(count($CajasSinCerrar)== 0){

			if($fechaCajaFormat < $fechaHoyFormat){

				$oCaja->setFechaCierre(NULL);
				$oCaja->setFechaReapertura($fechaHoy);
				$oCaja->setIdUsuarioReapertura($this->getUser());
				$oCaja->setIdEstadoReapertura($oEstadoReaperturaAbierta);
				//Se prepara el objeto para que sea insertado
				$em->persist($oCaja);
				//Se actualiza el dato en la base de datos
				$em->flush();

				$bValidEstado = $this->aOpcionesEstado;
				$bValid = $bValidEstado["9"];
				return new Response(json_encode($bValid));
			}
			else if($fechaCajaFormat == $fechaHoyFormat){

				$oCaja->setFechaCierre(NULL);
				$oCaja->setFechaReapertura(NULL);
				$oCaja->setIdUsuarioReapertura(NULL);
				$oCaja->setIdEstadoReapertura(NULL);
				//Se prepara el objeto para que sea insertado
				$em->persist($oCaja);
				//Se actualiza el dato en la base de datos
				$em->flush();

				$bValidEstado = $this->aOpcionesEstado;
				$bValid = $bValidEstado["6"];
				return new Response(json_encode($bValid));
			}

//           $bValidEstado = $this->aOpcionesEstado;
//                $bValid = $bValidEstado["6"];
//                return new Response(json_encode($bValid));

		}else{
			$bValidEstado = $this->aOpcionesEstado;
			$bValid = $bValidEstado["5"];
			return new Response(json_encode($bValid));
		}

	}
}