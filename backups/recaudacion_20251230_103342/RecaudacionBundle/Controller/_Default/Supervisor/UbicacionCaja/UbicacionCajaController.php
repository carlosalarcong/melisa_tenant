<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\UbicacionCaja;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Controller\Supervisor\UbicacionCaja\render;
use Symfony\Component\HttpFoundation\Request;

class UbicacionCajaController extends SupervisorController {
	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de los Niveles de Instrucción de la Empresa (oEmpresa)
	 */
	public function indexAction(Request $request) {
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$rRepository = $this->getDoctrine()->getRepository('RebsolHermesBundle:UbicacionCaja');
		$entities = $rRepository->ObtenerListado($oEmpresa);
		$eSucursal      = '';
		$eTipoDocumento = '';
		$eUbicacionCaja = '';
		$eUbicacionCajero = '';
		$eUsuariosRebsol  = '';
		$bValidarRequisitos = $this->validarRequisitos();

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		return $this->render('RecaudacionBundle:Supervisor/UbicacionCaja:index.html.twig',
			array(
				'entities'            => $entities
				,'bValidarRequisitos' => $bValidarRequisitos
				,'esucursal'          => $eSucursal
				,'eTipoDocumento'     => $eTipoDocumento
				,'eUbicacionCaja'     => $eUbicacionCaja
				,'eUbicacionCajero'   => $eUbicacionCajero
				,'eUsuariosRebsol'    => $eUsuariosRebsol
				,'coreApi'          => ($estadoApi === "core")?1:0,
				)
			);
	}

	/**
	 * @return boolean
	 * Descripción: validarRequisitos() Valida que se cumplan los
	 * requisitos para poder utilizar el mantenedor.
	 */
	public function validarRequisitos() {
		$oEmpresa = $this->ObtenerEmpresaLogin();

		$rRepository = $this->getDoctrine()->getRepository('RebsolHermesBundle:Sucursal');

		$entities = $rRepository->findBy(
			array(
				'idEmpresa' =>$oEmpresa->getId()
				,'idEstado' =>$this->container->getParameter('estado_activo')
				)
			);

		return (COUNT($entities)==0)? true : false;
	}
}