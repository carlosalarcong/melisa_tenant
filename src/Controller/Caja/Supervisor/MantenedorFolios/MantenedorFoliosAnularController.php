<?php

namespace App\Controller\Caja\Supervisor\MantenedorFolios;

use Rebsol\HermesBundle\Entity\DetalleTalonario;
use App\Controller\Caja\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\MantenedorFolios\render;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MantenedorFoliosAnularController extends SupervisorController {

	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
	 */
	public function anularAction(Request $request, $idDetalleTalonario) {
		$this->ValidadPeticionAjax($request, 'Supervisor_MantenedorFolios');
		$em = $this->getDoctrine()->getManager();

		$oDetalleTalonario = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->
		findBy(array('id' => $idDetalleTalonario));

		$oEstadoAnulado = $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->
		find($this->container->getParameter('estado_detalle_talonario_anulada'));

		$fechaAnulacion = new \Datetime();

		foreach($oDetalleTalonario as $detalle){

			$detalle->setIdEstadoDetalleTalonario($oEstadoAnulado);
			$detalle->setIdUsuarioAnulacion($this->getUser());
			$detalle->setFechaAnulacion($fechaAnulacion);
			//Se prepara el objeto para que sea insertado
			$em->persist($detalle);
			//Se actualiza el dato en la base de datos
			$em->flush();
		}

		$bValidEstado = $this->aOpcionesEstado;
		$bValid = $bValidEstado["2"];
		return new Response(json_encode($bValid));

	}

	public function anularPorEmitirAction(Request $request) {

		$this->ValidadPeticionAjax($request, 'Supervisor_MantenedorFolios');
		$em           = $this->getDoctrine()->getManager();
		$numeroBoleta = $request->query->get('numeroBoleta');
		$idTalonario  = $request->query->get('idTalonario');
		$Talonario    = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario);
		//echo"<pre>";\Doctrine\Common\Util\Debug::dump($Talonario);exit;
		$oEstadoAnulado = $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->
		find($this->container->getParameter('estado_detalle_talonario_anulada'));

		$nuevoNumeroActual = $numeroBoleta + 1;

		$obtieneNumeroActual = $em->getRepository('RebsolHermesBundle:Talonario')->obtieneNumeroActual($idTalonario);

		if ($obtieneNumeroActual != null) {
			if ($obtieneNumeroActual[0]['numeroActual'] == $numeroBoleta) {
				$obtieneNumerosBoleta = $em->getRepository('RebsolHermesBundle:Talonario')->obtieneNumerosBoleta($idTalonario);
				$ofrecer_promocion=false;
				foreach ($obtieneNumerosBoleta as $onb) {
					if ($onb['numeroDocumento'] == $nuevoNumeroActual) {
						$ofrecer_promocion=true;
						$nuevoNumeroActual++;
					} else {
						$Talonario->setNumeroActual($nuevoNumeroActual);
						//Se prepara el objeto para que sea insertado
						$em->persist($Talonario);
						//Se actualiza el dato en la base de datos
						$em->flush();
					}
				}

				if($ofrecer_promocion != false){

					$Talonario->setNumeroActual($nuevoNumeroActual);
					//Se prepara el objeto para que sea insertado
					$em->persist($Talonario);
					//Se actualiza el dato en la base de datos
					$em->flush();
				}

			} else {

			}
		}

		$numeroBoleta      = (int)$numeroBoleta;
		$objUsuariosRebsol = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($this->getUser()->getId());

		$fechaAnulacion = new \Datetime();

		$DetalleTalonario = new DetalleTalonario();
		$DetalleTalonario->setIdUsuarioDetalleBoleta($objUsuariosRebsol);
		$DetalleTalonario->setFechaDetalleBoleta($fechaAnulacion);
		$DetalleTalonario->setIdTalonario($Talonario);
		$DetalleTalonario->setNumeroDocumento($numeroBoleta);
		$DetalleTalonario->setIdEstadoDetalleTalonario($oEstadoAnulado);
		$DetalleTalonario->setIdUsuarioAnulacion($objUsuariosRebsol);
		$DetalleTalonario->setFechaAnulacion($fechaAnulacion);
		//Se prepara el objeto para que sea insertado
		$em->persist($DetalleTalonario);
		//Se actualiza el dato en la base de datos
		$em->flush();

		$bValidEstado = $this->aOpcionesEstado;
		$bValid       = $bValidEstado["2"];
		$msj          = $bValid['msg'];

		return new Response(json_encode($msj));

	}
}