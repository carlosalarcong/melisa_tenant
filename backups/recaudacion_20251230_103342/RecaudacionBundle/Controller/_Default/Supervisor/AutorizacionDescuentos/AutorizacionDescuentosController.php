<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\AutorizacionDescuentos;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Controller\Supervisor\AutorizacionDescuentos\render;
use Symfony\Component\HttpFoundation\Request;

class AutorizacionDescuentosController extends SupervisorController {

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

				//Obtenemos el objeto de la sucursal según el usuario
			$SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());
			$eSucursal        = '';
			$eTipoDocumento   = '';
			$eUbicacionCaja   = '';
			$eUbicacionCajero = '';
			$eUsuariosRebsol  = '';
				//Buscamos el objeto que estén en estado "cajero pide autorización"
			$oDiferenciasEnEspera = $em->getRepository('RebsolHermesBundle:Diferencia')->
			obtenerDiferencias(
				$this->container->getParameter('estado_diferencia_cajero_pide_autorizacion'),
				$SucursalUsuario->getId()
				);

				//Buscamos el objeto que estén en estado "rechazada"
			$oDiferenciasRechazadas = $em->getRepository('RebsolHermesBundle:Diferencia')->
			obtenerDiferencias(
				$this->container->getParameter('estado_diferencia_rechazada'),
				$SucursalUsuario->getId()
				);

				 //Buscamos el objeto que estén en estado "autorizada"
			$oDiferenciasAutorizadas = $em->getRepository('RebsolHermesBundle:Diferencia')->
			obtenerDiferencias(
				$this->container->getParameter('estado_diferencia_autorizada'),
				$SucursalUsuario->getId()
				);
				//Buscamos el objeto que estén en estado "no requiere autorización"
			$oDiferenciasNoAutorizacion = $em->getRepository('RebsolHermesBundle:Diferencia')->
			obtenerDiferencias(
				$this->container->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'),
				$SucursalUsuario->getId()
				);


            return $this->render('RecaudacionBundle:Supervisor/AutorizacionDescuentos:index.html.twig',
				array(
					'enEspera'          => $oDiferenciasEnEspera,
					'rechazadas'        => $oDiferenciasRechazadas,
					'autorizadas'       => $oDiferenciasAutorizadas,
					'sinAutorizacion'   => $oDiferenciasNoAutorizacion
					,'esucursal'        => $eSucursal
					,'eTipoDocumento'   => $eTipoDocumento
					,'eUbicacionCaja'   => $eUbicacionCaja
					,'eUbicacionCajero' => $eUbicacionCajero
					,'eUsuariosRebsol'  => $eUsuariosRebsol
					,'coreApi'          => ($estadoApi === "core")?1:0
					)
				);
		}

	}