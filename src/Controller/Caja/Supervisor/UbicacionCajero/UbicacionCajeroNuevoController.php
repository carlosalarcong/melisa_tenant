<?php

namespace App\Controller\Caja\Supervisor\UbicacionCajero;

use Rebsol\HermesBundle\Entity\RelUbicacionCajero;
use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use App\Form\Supervisor\UbicacionCajero\UbicacionCajeroType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajeroNuevoController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @return Response()
	 * Descripción: nuevoAction() Muestra el formulario para crear un nuevo Nivel Instrucción
	 */
	public function nuevoAction(Request $request) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCajero');
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$entity   = new RelUbicacionCajero();

		$entities                   = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->DatosMaestrosMedicos($oEmpresa->getId());
		$arrUsuariosAdministrativos = array();
		foreach ($entities as $value) {
			if ( ($value['ProfC'] == 0 || $value['verCaja'] == 1) AND $value['idestado'] == $this->container->getParameter('estado_usuario_activo')) {
				$arrUsuariosAdministrativos[] = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($value['idUR']);
			}
		}
		//echo"<pre>";var_dump($this->container->getParameter('estado_activo'), $oEmpresa->getId());exit;
		$form   = $this->createForm(UbicacionCajeroType::class, $entity,
			array(
			   'isNew'                       => true,
			   'oEmpresa'                   => $oEmpresa,
			   'arrUsuariosAdministrativos' => $arrUsuariosAdministrativos,
			   'estado_activado'            => $this->container->getParameter('estado_activo')
			   )
			);

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCajero:new.html.twig',
			array(
			   'entity' => $entity
			   ,'form'   => $form->createView()
			   )
			);
		return new Response($renderView);
	}

	/**
	 * @param Request $request.
	 * @return Response()
	 * Descripción: crearAction() Valida el formulario para crear un nuevo Nivel Instrucción
	 */
	public function crearAction(Request $request) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCajero');
		$em = $this->getDoctrine()->getManager();
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$entity  = new RelUbicacionCajero();
		// $sCommon = $this->get('common');
		$entity->setIdEstado($em->getRepository('RebsolHermesBundle:EstadoRelUbicacionCajero')->find($this->container->getParameter('EstadoRelUbicacionCajero.Activo')));
		$sMantenedores = $this->get('mantenedores');
		$arrRequest = $request->request->get('rebsol_hermesbundle_ubicacioncajeroType');


		$entities                   = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->DatosMaestrosMedicos($oEmpresa->getId());
		$arrUsuariosAdministrativos = array();
		foreach ($entities as $value) {
			if ( ($value['ProfC'] == 0 || $value['verCaja'] == 1) AND $value['idestado'] == $this->container->getParameter('estado_usuario_activo') ) {
				$arrUsuariosAdministrativos[] = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($value['idUR']);
			}
		}

		$form = $this->createForm(UbicacionCajeroType::class, $entity,
			array(
			   'isNew'                       => true,
			   'estado_activado'            => $this->container->getParameter('estado_activo'),
			   'oEmpresa'                   => $oEmpresa,
			   'arrUsuariosAdministrativos' => $arrUsuariosAdministrativos,
			   'sMantenedores'              => $sMantenedores
			   )
			);

		$form->handleRequest($request);

		if ($form['idUsuario']->getData()) {
			$oRelacion = $this->getDoctrine()->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(
				array(
					'idEstado' => $this->container->getParameter('estado_activo'),
					'idUsuario' => $form['idUsuario']->getData()
					)
				);
			if(!is_null($oRelacion)){
				$form['idUsuario']->addError(new FormError("Cajero sólo puede estar en una caja a la vez."));
			}
		}

		if ($this->ValidarFormulario($form)) {
			$iIdUsuario = $form['idUsuario']->getData();
			$oUsuario   = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($iIdUsuario);
			$entity->setIdUsuario($oUsuario);

			$oUbicacionCaja = $form['idUbicacionCaja']->getData();
			$oUbicacionCaja = $this->getDoctrine()->getRepository('RebsolHermesBundle:UbicacionCaja')->find($oUbicacionCaja->getId());
			$entity->setIdUbicacionCaja($oUbicacionCaja);
			$em->persist($entity);
			$em->flush();

			$renderView = "Creado";
			return new Response($renderView);
		}

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCajero:new.html.twig',
			array(
			   'entity' => $entity,
			   'form'   => $form->createView()
			   )
			);
		return new Response($renderView);
	}
}
