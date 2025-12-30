<?php

namespace App\Controller\Caja\Supervisor\ConsolidadoCaja;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Form\Supervisor\ConsolidadoCaja\ConsolidadoCajaType;
use Symfony\Component\HttpFoundation\Response;

class ConsolidadoCajaInformeController extends SupervisorController
{
	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
	 */
	public function informeAction()
	{
		$fechaIngresada = $this->container->get('request_stack')->getCurrentRequest()->query->get('fechaIngresada');
		$Sucursal = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
		$this->get('session')->set('fecha', $fechaIngresada);
		$this->get('session')->set('sucursal', $Sucursal);

		return new Response("ok");
	}

	public function informerAction()
	{
		//Obtenemos la fecha ingresada y la sucursal mediante sesión.
		$fechaIngresada = $this->get('session')->get('fecha');
		$Sucursal       = $this->get('session')->get('sucursal');
		$em             = $this->getDoctrine()->getManager();

		//En caso de que no exista sucursal, tipo documento, ubicación caja, ubicación cajero y usuarios rebsol, enviamos mensajes de error.
		$eSucursal        = '';
		$eTipoDocumento   = '';
		$eUbicacionCaja   = '';
		$eUbicacionCajero = '';
		$eUsuariosRebsol  = '';

		//Formateamos la fecha
		$fechaAjaxReformat = new \DateTime(date("Y-m-d", strtotime($fechaIngresada)));
		$Fecha             = $fechaAjaxReformat->format("Y-m-d");

		//Repositorio para obtener todas las cajas abiertas, reabiertas y cerradas, filtradas por fecha y sucursal.
		$oCaja = $em->getRepository("RebsolHermesBundle:Caja")->informeCajaIndex($Fecha, $Sucursal);

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		//Si no hay datos, renderizamos a una vista con variables vacías.
		if($oCaja == null) {
			return $this->render('RecaudacionBundle:Supervisor/ConsolidadoCaja:informeVacio.html.twig',
				array(
					'oCaja'             => $oCaja
					,'coreApi'          => ($estadoApi === "core")? 1 : 0
					,'esucursal'        => $eSucursal
					,'eTipoDocumento'   => $eTipoDocumento
					,'eUbicacionCaja'   => $eUbicacionCaja
					,'eUbicacionCajero' => $eUbicacionCajero
					,'eUsuariosRebsol'  => $eUsuariosRebsol
				)
			);
		}

		//Obtenemos el id de la caja
		$idCaja = $oCaja[0]['idCaja'];

		//Obtenemos el objeto de la reapertura
		$oestadoReapertura = $em->getRepository("RebsolHermesBundle:EstadoReapertura")->find($this->container->getParameter('estado_reapertura_abierta'));

		//Creamos el formulario
		$form = $this->createForm(ConsolidadoCajaType::class, null, array('estado_activado' => $this->container->getParameter('estado_activo')));

		//Renderizamos a la vista informe.html.twig'
		return $this->render('RecaudacionBundle:Supervisor/ConsolidadoCaja:informe.html.twig',
			array(
				'form'             => $form->createView(),
				'oCaja'            => $oCaja,
				'kk'               => $oestadoReapertura->getId(),
				'idCaja'           => $idCaja,
				'esucursal'        => $eSucursal,
				'eTipoDocumento'   => $eTipoDocumento,
				'eUbicacionCaja'   => $eUbicacionCaja,
				'eUbicacionCajero' => $eUbicacionCajero,
				'eUsuariosRebsol'  => $eUsuariosRebsol,
				'coreApi'          => ($estadoApi === "core") ? 1 : 0
			)
		);
	}
}