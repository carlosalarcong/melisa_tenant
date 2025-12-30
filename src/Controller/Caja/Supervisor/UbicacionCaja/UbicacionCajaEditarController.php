<?php

namespace App\Controller\Caja\Supervisor\UbicacionCaja;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Form\Supervisor\UbicacionCaja\UbicacionCajaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajaEditarController extends SupervisorController {
	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: editarAction() Muestra el formulario para editar un determinado Nivel Instrucción (id)
	 */
	public function editarAction(Request $request, $id) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCaja');
		$em = $this->getDoctrine()->getManager();

		$oEmpresa = $this->ObtenerEmpresaLogin();

		$entity = $em->getRepository('RebsolHermesBundle:UbicacionCaja')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find UbicacionCaja entity.');
		}

		$editForm   = $this->createForm(UbicacionCajaType::class, $entity,
			array(
				'isNew'            => false,
				'oEmpresa'         => $oEmpresa,
				'estado_activado'  => $this->container->getParameter('estado_activo'),
				'oEntidad'         => $entity,
				'database_default' => $this->obtenerEntityManagerDefault()
				)
			);

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCaja:edit.html.twig',
			array(
				'entity'      => $entity
				,'edit_form'   => $editForm->createView()
				)
			);
		return new Response($renderView);
	}

	/**
	 * @param Request $request.
	 * @param integer $id id del Nivel Instrucción.
	 * @return Response()
	 * Descripción: actualizarAction() Valida el formulario para editar un determinado Nivel Instrucción (id)
	 */
	public function actualizarAction(Request $request, $id) {
		$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCaja');
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('RebsolHermesBundle:UbicacionCaja')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find UbicacionCaja entity.');
		}

		$oEmpresa = $this->ObtenerEmpresaLogin();
		$sMantenedores = $this->get('mantenedores');

		$editForm = $this->createForm(UbicacionCajaType::class, $entity,
			array(
				'isNew'           => false,
				'estado_activado' => $this->container->getParameter('estado_activo'),
				'oEmpresa'        => $oEmpresa,
				'sMantenedores'   => $sMantenedores,
				'iIdEntidad'      => $entity->getId(),
				'oEntidad'        => $entity,
				'database_default' => $this->obtenerEntityManagerDefault()
				)
			);

		$editForm->handleRequest($request);
		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em->persist($entity);
			$em->flush();

			$renderView = "Editado";
			return new Response($renderView);
		}

		$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCaja:edit.html.twig',array(
			'entity'      => $entity,
			'edit_form'   => $editForm->createView()
			)
		);
		return new Response($renderView);
	}
}
