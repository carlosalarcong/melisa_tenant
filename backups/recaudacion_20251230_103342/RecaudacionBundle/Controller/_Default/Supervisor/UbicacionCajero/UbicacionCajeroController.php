<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\UbicacionCajero;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Controller\Supervisor\UbicacionCajero\render;
use Symfony\Component\HttpFoundation\Request;

class UbicacionCajeroController extends SupervisorController {
	/**
	 * @return render
	 * Descripción: indexAction() Muetra el listado de los Niveles de Instrucción de la Empresa (oEmpresa)
	 */
	public function indexAction(Request $request) {

		$oEmpresa           = $this->ObtenerEmpresaLogin();
		$rRepository        = $this->getDoctrine()->getRepository('RebsolHermesBundle:RelUbicacionCajero');
		$entities           = $rRepository->ObtenerListado($oEmpresa);
		$eSucursal          = '';
		$eTipoDocumento     = '';
		$eUbicacionCaja     = '';
		$eUbicacionCajero   = '';
		$eUsuariosRebsol    = '';
		$bValidarRequisitos = $this->validarRequisitos();

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		return $this->render('RecaudacionBundle:Supervisor/UbicacionCajero:index.html.twig',
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

		$arrErrores = array();
		// Profesionales
		$entities = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->DatosMaestrosMedicos($oEmpresa->getId());
		$bTiene   = false;
		foreach ($entities as $value) {
			if ( ($value['ProfC'] == 0 || $value['verCaja'] == 1) AND ($bTiene == false)) {
				$bTiene = true;
			}
		}
		if (!$bTiene) { $arrErrores[] = "Profesional"; }
		// validar ubicaciones caja
		$entities = $this->getDoctrine()->getRepository('RebsolHermesBundle:UbicacionCaja')->ObtenerListado($oEmpresa);
		$bTiene   = false;
		foreach ($entities as $value) {
			if ( ($value->getIdEstado()->getId() == $this->container->getParameter('EstadoRelUbicacionCajero.Activo') ) AND ($bTiene == false)) {
				$bTiene = true;
			}
		}
		if (!$bTiene) { $arrErrores[] = "Ubicación Caja"; }

		return $arrErrores;
	}
}