<?php

namespace App\Controller\Caja\Supervisor\MantenedorFolios;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\MantenedorFolios\render;
use App\Form\Supervisor\MantenedorFolios\MantenedorFoliosEditarType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MantenedorFoliosEditarController extends SupervisorController {

		/**
		 * @return render
		 * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
		 */
		public function editarAction(Request $request, $idDetalleTalonario) {

			$this->ValidadPeticionAjax($request, 'Supervisor_MantenedorFolios');
			$em = $this->getDoctrine()->getManager();

			$oDetalleBoleta = $em->getRepository("RebsolHermesBundle:DetalleTalonario")->
			findBy(
				array('id' => $idDetalleTalonario)
				);

			$idDetalleBoleta = $oDetalleBoleta[0]->getId();

			if (!$oDetalleBoleta) {
				throw $this->createNotFoundException('Unable to find detalle boleta.');
			}

			$edit_form = $this->createForm(MantenedorFoliosEditarType::class, null,
				array(
					'estado_activado' => $this->container->getParameter('estado_activo')
					)
				);

			$renderView = $this->renderView('RecaudacionBundle:Supervisor/MantenedorFolios:edit.html.twig',
				array(
				 'oDetalleBoleta' => $oDetalleBoleta,
				 'edit_form'      => $edit_form->createView(),
				 'idDetalleBoleta'=> $idDetalleBoleta
				 )
				);

			return new Response($renderView);
		}

		public function actualizarAction(Request $request, $idDetalleTalonario) {

			$this->ValidadPeticionAjax($request, 'Supervisor_MantenedorFolios');
			$em = $this->getDoctrine()->getManager();

			$oDetalleBoleta = $em->getRepository("RebsolHermesBundle:DetalleTalonario")->
			findBy(
				array('id' => $idDetalleTalonario)
				);
			$idDetalleBoleta = $oDetalleBoleta[0]->getId();

			$idTalonario = $oDetalleBoleta[0]->getIdTalonario()->getId();

			$idUsuarioBoleta = $oDetalleBoleta[0]->getIdUsuarioDetalleBoleta()->getId();

			if (!$oDetalleBoleta) {
				throw $this->createNotFoundException('Unable to find detalle boleta.');
			}

			$edit_form = $this->createForm(MantenedorFoliosEditarType::class, null,
				array(
					'estado_activado' => $this->container->getParameter('Estado.activo')
					)
				);

			$edit_form->handleRequest($request);

			$numeroIngresado = $edit_form['numeroDocumento']->getData();
			$oTalonario = $em->getRepository("RebsolHermesBundle:Talonario")->find($idTalonario);

			$idUbicacionCaja           = $oTalonario->getIdUbicacionCaja()->getId();
			$idSucursal                = $oTalonario->getIdSucursal()->getId();
			$idSubEmpresa              = $oTalonario->getIdSubEmpresa()->getId();
			$idRelEmpresaTipoDocumento = $oTalonario->getIdRelEmpresaTipoDocumento()->getId();
			$idTalonario               = $oTalonario->getId();


			$obtieneBoleta = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->obtenerPrimerBoletaPorEmitir(
				$numeroIngresado,
				$idUsuarioBoleta,
				$idSubEmpresa,
				$idSucursal,
				$idRelEmpresaTipoDocumento,
				$idUbicacionCaja,
				$idTalonario
				);

			$inicioTalonario = $oTalonario->getNumeroInicio();
			$terminoTalonario = $oTalonario->getNumeroTermino();

			$numeroIngresado = (int)$numeroIngresado;

			$primeraBoleta = (int) $inicioTalonario;
			$ultimaBoleta  = (int) $terminoTalonario;



			if (($obtieneBoleta != null) || ($numeroIngresado > $ultimaBoleta) || ($numeroIngresado < $primeraBoleta) ) {

				if (($numeroIngresado > $ultimaBoleta) || ($numeroIngresado < $primeraBoleta)) {
					$edit_form['numeroDocumento']->addError(new FormError("La boleta " . $numeroIngresado . " no pertenece a este talonario"));
				} elseif ($obtieneBoleta[0]->getIdEstadoDetalleTalonario()->getId() == 2) {
					$edit_form['numeroDocumento']->addError(new FormError("La boleta " . $numeroIngresado . " se encuentra anulada"));
				} elseif ($obtieneBoleta[0]->getIdEstadoDetalleTalonario()->getId() == 1) {
					$edit_form['numeroDocumento']->addError(new FormError("La boleta " . $numeroIngresado . " se encuentra ocupada"));
				}
			}


			if ($edit_form->isSubmitted() && $edit_form->isValid()) {

				$oDetalleBoleta = $em->getRepository("RebsolHermesBundle:DetalleTalonario")->find($idDetalleTalonario);

				$oDetalleBoleta->setNumeroDocumento($numeroIngresado);
				$em->persist($oDetalleBoleta);
				$em->flush();

				$renderView = "Editado";
				return new Response($renderView);
			}

			$renderView = $this->renderView('RecaudacionBundle:Supervisor/MantenedorFolios:edit.html.twig',
				array(
				 'oDetalleBoleta' => $oDetalleBoleta,
				 'edit_form'      => $edit_form->createView(),
				 'idDetalleBoleta'=> $idDetalleBoleta
				 )
				);

			return new Response($renderView);
		}
	}