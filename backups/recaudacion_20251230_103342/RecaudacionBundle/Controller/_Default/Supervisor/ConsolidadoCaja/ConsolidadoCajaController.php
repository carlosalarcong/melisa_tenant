<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\ConsolidadoCaja;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Form\Type\Supervisor\ConsolidadoCaja\ConsolidadoCajaType;
use Symfony\Component\HttpFoundation\Request;

class ConsolidadoCajaController extends SupervisorController {

		/**
		 * @return render
		 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
		 */
		public function indexAction(Request $request) {

			$estadoApi = $this->estado('EstadoApi');

			if($estadoApi != 'core'){
				if($estadoApi['rutaApi'] === 'ApiPV'){
					$estadoApi = 'core';
				}
			}

			$em = $this->getDoctrine()->getManager();

				//Obtenemos el login de la empresa
			$oEmpresa = $this->ObtenerEmpresaLogin();

				//Obtenemos el objeto de la sucursal según el usuario
			$SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());

			$eSucursal        = '';
			$eTipoDocumento   = '';
			$eUbicacionCaja   = '';
			$eUbicacionCajero = '';
			$eUsuariosRebsol  = '';
			$form = $this->createForm(ConsolidadoCajaType::class, null,
				array(
					'estado_activado' => $this->container->getParameter('estado_activo'),
					'idEstado'        => $this->container->getParameter('estado_activo'),
					'idEmpresa'       => $oEmpresa->getId(),
					'idSucursal'      => $SucursalUsuario->getId(),
					'database_default'=> $this->obtenerEntityManagerDefault()
					));

			return $this->render('RecaudacionBundle:Supervisor/ConsolidadoCaja:index.html.twig',
				array(
					'form'              => $form->createView(),
					'oSucursal'         => $SucursalUsuario->getId()
					,'esucursal'        => $eSucursal
					,'eTipoDocumento'   => $eTipoDocumento
					,'eUbicacionCaja'   => $eUbicacionCaja
					,'eUbicacionCajero' => $eUbicacionCajero
					,'eUsuariosRebsol'  => $eUsuariosRebsol
					,'coreApi'          => ($estadoApi === "core") ? 1 : 0
					));
		}
	}