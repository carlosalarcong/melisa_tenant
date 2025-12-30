<?php

namespace App\Controller\Caja\Supervisor\UbicacionCaja;

use App\Entity\Legacy\UbicacionCaja;
use App\Controller\Caja\Supervisor\SupervisorController;
use App\Form\Supervisor\UbicacionCaja\UbicacionCajaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajaNuevoController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @return Response()
	 * Descripción: nuevoAction() Muestra el formulario para crear un nuevo Nivel Instrucción
	 */
	public function nuevoAction(Request $request) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCaja');
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$entity = new UbicacionCaja();

		$form   = $this->createForm(UbicacionCajaType::class, $entity,
			array(
				'isNew'           => true,
				'oEmpresa'        => $oEmpresa,
				'estado_activado' => $this->container->getParameter('estado_activo')
				)
			);

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCaja:new.html.twig',
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

		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCaja');
		$em = $this->getDoctrine()->getManager();
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$entity  = new UbicacionCaja();
		$sCommon = $this->get('common');
		$entity->setIdEstado($sCommon->obtenerEstado());
		$sMantenedores = $this->get('mantenedores');

		//Obtenemos el objeto de la sucursal según el usuario
		$SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());

		$form = $this->createForm(UbicacionCajaType::class, $entity,
			array(
				'isNew'            => true,
				'estado_activado' => $this->container->getParameter('estado_activo'),
				'oEmpresa'        => $oEmpresa,
				'sMantenedores'   => $sMantenedores
				)
			);

		$form->handleRequest($request);

		if ($this->ValidarFormulario($form)) {

			$Descripcion = $form['descripcion']->getData();
			$Sucursal    = $form['idSucursal']->getData();
			$nombreCaja  = $form['nombre']->getData();

			$objSucursal   = $this->getDoctrine()->getRepository('RebsolHermesBundle:Sucursal')->find($Sucursal->getId());

			$entity->setDescripcion($Descripcion);
			$entity->setNombre($nombreCaja);
			$entity->setIdSucursal($objSucursal);
			$em->persist($entity);
			$em->flush();

			$renderView = "Creado";
			return new Response($renderView);
		}

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCaja:new.html.twig',
			array(
				'entity' => $entity,
				'form'   => $form->createView()
				)
			);
		return new Response($renderView);
	}
}
