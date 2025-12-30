<?php

namespace App\Controller\Caja\Supervisor\CorrelativoBoletas;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\CorrelativoBoletas\render;
use Symfony\Component\HttpFoundation\Request;

class CorrelativoBoletasController extends SupervisorController {

		/**
		 * @return render
		 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
		 */
		public function indexAction(Request $request) {

			$em = $this->getDoctrine()->getManager();

			$estadoApi = $this->estado('EstadoApi');

			if($estadoApi != 'core'){
				if($estadoApi['rutaApi'] === 'ApiPV'){
					$estadoApi = 'core';
				}
			}

				//Obtenemos el objeto de la sucursal según el usuario
			$SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());
			$eSucursal      = '';
			$eTipoDocumento = '';
			$eUbicacionCaja = '';
			$eUbicacionCajero = '';
			$eUsuariosRebsol  = '';
				//Buscamos el id y nombre de la sucursal
			$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->
			findBy(
				array(
				 'idEstado'   => $this->container->getParameter('estado_activo'),
				 'idSucursal' => $SucursalUsuario->getId()

				 )
				);


				//var_dump($oTalonario[0]->getIdEstadoPila()->getNombre());exit;
			if($oTalonario == null){
				return $this->render('RecaudacionBundle:Supervisor/CorrelativoBoletas:index.html.twig',
					array(
						'oTalonario'       => $oTalonario,
						'esucursal'        => $eSucursal,
						'eTipoDocumento'   => $eTipoDocumento,
						'eUbicacionCaja'   => $eUbicacionCaja,
						'eUbicacionCajero' => $eUbicacionCajero,
						'eUsuariosRebsol'  => $eUsuariosRebsol,
						'coreApi'          => ($estadoApi === "core")?1:0
						)
					);
			}else{
				$NombreEstado = $oTalonario[0]->getIdEstadoPila()->getNombre();
			}
			return $this->render('RecaudacionBundle:Supervisor/CorrelativoBoletas:index.html.twig',
			 array(
				 'oTalonario'       => $oTalonario,
				 'nombreEstado'     => $NombreEstado,
				 'esucursal'        => $eSucursal,
				 'eTipoDocumento'   => $eTipoDocumento,
				 'eUbicacionCaja'   => $eUbicacionCaja,
				 'eUbicacionCajero' => $eUbicacionCajero,
				 'eUsuariosRebsol'  => $eUsuariosRebsol,
				 'coreApi'          => ($estadoApi === "core")?1:0
				 ));
		}

	}