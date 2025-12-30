<?php

namespace App\Controller\Caja\Supervisor\UbicacionCajero;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Form\Supervisor\UbicacionCajero\UbicacionCajeroType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UbicacionCajeroEditarController extends SupervisorController {
		/**
		 * @param Request $request.
		 * @param integer $id id del Nivel Instrucción.
		 * @return Response()
		 * Descripción: editarAction() Muestra el formulario para editar un determinado Nivel Instrucción (id)
		 */
		public function editarAction(Request $request, $id) {
			$this->ValidadPeticionAjax($request, 'Supervisor__UbicacionCajero');
			$em = $this->getDoctrine()->getManager();
			$oEmpresa = $this->ObtenerEmpresaLogin();

			$entity = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->find($id);

			if (!$entity) {
				throw $this->createNotFoundException('Unable to find UbicacionCajero entity.');
			}
			$idUsuarioOriginal = $entity->getIdUsuario()->getId();

			$entities                   = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->DatosMaestrosMedicos($oEmpresa->getId());
			$arrUsuariosAdministrativos = array();
			foreach ($entities as $value) {
				if ( ($value['ProfC'] == 0 || $value['verCaja'] == 1) AND $value['idestado'] == $this->container->getParameter('estado_usuario_activo') ) {
					$arrUsuariosAdministrativos[] = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($value['idUR']);
				}
			}

			$editForm   = $this->createForm(UbicacionCajeroType::class, $entity,
				array(
					'isNew'                       => false,
					'oEmpresa'                   => $oEmpresa,
					'estado_activado'            => $this->container->getParameter('estado_activo'),
					'arrUsuariosAdministrativos' => $arrUsuariosAdministrativos,
					'oEntidad'                   => $entity,
					'idUsuarioOriginal'          => $idUsuarioOriginal
					)
				);

			$arrUsuario = $entity->getIdUsuario()->getId();

			$editForm['idUsuario']->setData($arrUsuario);
			$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCajero:edit.html.twig',
				array(
					'entity'      => $entity,
					'edit_form'   => $editForm->createView()
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

			$this->ValidadPeticionAjax($request, 'Supervisor_UbicacionCajero');

			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->find($id);


			if (!$entity) {
				throw $this->createNotFoundException('Unable to find UbicacionCajero entity.');
			}

			$oEmpresa = $this->ObtenerEmpresaLogin();
			$sMantenedores = $this->get('mantenedores');

			$entities                   = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->DatosMaestrosMedicos($oEmpresa->getId());

			$arrUsuariosAdministrativos = array();
			foreach ($entities as $value) {

				if ( ($value['ProfC'] == 0 || $value['verCaja'] == 1) AND $value['idestado'] == $this->container->getParameter('estado_usuario_activo')) {
					$arrUsuariosAdministrativos[] = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($value['idUR']);
				}
			}

			$editForm = $this->createForm(UbicacionCajeroType::class, $entity, array(
					'isNew'                       => false,
					'estado_activado'            => $this->container->getParameter('EstadoRelUbicacionCajero.Activo'),
					'oEmpresa'                   => $oEmpresa,
					'sMantenedores'              => $sMantenedores,
					'iIdEntidad'                 => $entity->getId(),
					'arrUsuariosAdministrativos' => $arrUsuariosAdministrativos,
					'oEntidad'                   => $entity
					)
				);

			$editForm->handleRequest($request);

			if ($editForm['idUsuario']->getData()) {

				$oRelacion = $this->getDoctrine()->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy( array(
					'idEstado' => $this->container->getParameter('EstadoRelUbicacionCajero.Activo'),
					'idUsuario' => $editForm['idUsuario']->getData()
					)
				);

				$usuarioOriginal = $estadoPilaOriginal = $editForm['idUsuarioOriginal']->getData();
				$usuarioOriginal = (int)$usuarioOriginal;

				if ($usuarioOriginal == $editForm['idUsuario']->getData()) {

				} else {
					if (!is_null($oRelacion) AND $editForm['idEstado']->getData()->getId() == $this->container->getParameter('EstadoRelUbicacionCajero.Activo')) {
						$editForm['idUsuario']->addError(new FormError("Cajero sólo puede estar en una caja a la vez."));
					}
				}

			}

			if ($editForm->isSubmitted() && $editForm->isValid()) {
				$iIdUsuario = $editForm['idUsuario']->getData();
				$oUsuario   = $this->getDoctrine()->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($iIdUsuario);
				$entity->setIdUsuario($oUsuario);
				$em->persist($entity);
				$em->flush();

				$renderView = "Editado";
				return new Response($renderView);
			}

			$renderView = $this->renderView('RecaudacionBundle:Supervisor/UbicacionCajero:edit.html.twig',
				array(
					'entity'      => $entity,
					'edit_form'   => $editForm->createView()
					)
				);
			return new Response($renderView);
		}
	}
