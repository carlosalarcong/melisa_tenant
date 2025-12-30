<?php

namespace App\Controller\Caja\Supervisor\MantenedorFolios;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\MantenedorFolios\render;
use App\Form\Supervisor\MantenedorFolios\MantenedorFoliosType;
use Symfony\Component\HttpFoundation\Request;

class MantenedorFoliosController extends SupervisorController {

		/**
		 * @return render
		 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
		 */
		public function indexAction(Request $request) {

			$em = $this->getDoctrine()->getManager();

			$arrTalonarios = array();

			$oEmpresa = $this->ObtenerEmpresaLogin();
			$eSucursal        = '';
			$eTipoDocumento   = '';
			$eUbicacionCaja   = '';
			$eUbicacionCajero = '';
			$eUsuariosRebsol  = '';
			$idTalonario = $this->container->get('request_stack')->getCurrentRequest()->get('idTalonario');

			$estadoApi = $this->estado('EstadoApi');

			if($estadoApi != 'core'){
				if($estadoApi['rutaApi'] === 'ApiPV'){
					$estadoApi = 'core';
				}
			}

				//Obtenemos el objeto de la sucursal según el usuario
			$SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());

			$oTalonario = $em->getRepository("RebsolHermesBundle:Talonario")->
			findBy(
				array(
				 'idEstado'   => $this->container->getParameter('estado_activo'),
				 'idRelEmpresaTipoDocumento' => $idTalonario

				 )
				);

				//echo"<pre>";\Doctrine\Common\Util\Debug::dump($oTalonario);exit;

			$form = $this->createForm(MantenedorFoliosType::class, null,
				array(
					'estado_activado' => $this->container->getParameter('estado_activo'),
					'oEmpresa'        => $oEmpresa,
					'database_default'=> $this->obtenerEntityManagerDefault()
					)
				);


			if($idTalonario == ""){

				return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:index.html.twig',
					array(
						'oTalonario'        => $arrTalonarios,
						'form'              => $form->createView()
						,'esucursal'        => $eSucursal
						,'eTipoDocumento'   => $eTipoDocumento
						,'eUbicacionCaja'   => $eUbicacionCaja
						,'eUbicacionCajero' => $eUbicacionCajero
						,'eUsuariosRebsol'  => $eUsuariosRebsol
						,'coreApi'          => ($estadoApi === "core")?1:0,
						)
					);

			}else{
				foreach($oTalonario as $talonario){
				 $arrTalonarios[] = $talonario;
			 }

			 return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:indexDespuesDebuscar.html.twig',
				array(
				 'oTalonario'        => $arrTalonarios,
				 'form'              => $form->createView()
				 ,'esucursal'        => $eSucursal
				 ,'eTipoDocumento'   => $eTipoDocumento
				 ,'eUbicacionCaja'   => $eUbicacionCaja
				 ,'eUbicacionCajero' => $eUbicacionCajero
				 ,'eUsuariosRebsol'  => $eUsuariosRebsol
				 ,'coreApi'          => ($estadoApi === "core")?1:0,
				 )
				);
		 }

	 }
 }