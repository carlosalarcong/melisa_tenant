<?php


namespace App\Controller\Caja\Supervisor\MantenedorFolios;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;

class MantenedorFoliosVerController extends SupervisorController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
     */
    public function verAction($idTipoDocumento)
    {

			$em = $this->getDoctrine()->getManager();

			$estadoApi = $this->estado('EstadoApi');

			if($estadoApi != 'core'){
				if($estadoApi['rutaApi'] === 'ApiPV'){
					$estadoApi = 'core';
				}
			}

			$eSucursal      = '';
			$eTipoDocumento = '';
			$eUbicacionCaja = '';
			$eUbicacionCajero = '';
			$eUsuariosRebsol  = '';
			$oTalonario = $em->getRepository("RebsolHermesBundle:Talonario")->
			findBy(
				array(
					'idEstado'   => $this->container->getParameter('estado_activo'),
					'id'         => $idTipoDocumento
					)
				);
			$idTalonario = $oTalonario[0]->getId();

			$numeroInicio = $oTalonario[0]->getNumeroInicio();
			$numeroTermino = $oTalonario[0]->getNumeroTermino();

        $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

			$oDetalleBoleta = $em->getRepository("RebsolHermesBundle:DetalleTalonario")->
			findBy(
				array('idTalonario' => $idTipoDocumento)
				);

        //boletas de manera dinámica si el folio es por caja
        if ($folio['valor'] === '0') {
            for ($i = $numeroInicio; $i <= $numeroTermino; $i++) {
                $porEmitir[] = $i;
            }
        } else {
            //boletas de la tabla talonario_detalle si el folio es global
            $oTalonarioDetalles = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                ->findBy(array('idTalonario' => $idTalonario));
        }

        if ($oDetalleBoleta != null) {
            foreach ($oDetalleBoleta as $detalle) {
                $estadoActivo = $detalle->getIdEstadoDetalleTalonario()->getId();
            }//exit;

        } else {

            if ($folio['valor'] === '0') {
                return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:ver.html.twig', array(
                    'oTalonario' => $oTalonario,
                    'oDetalleBoleta' => $oDetalleBoleta,
                    'esucursal' => $eSucursal,
                    'boletas' => $porEmitir, //boletas de manera dinámica si el folio es por caja
                    'eTipoDocumento' => $eTipoDocumento,
                    'eUbicacionCaja' => $eUbicacionCaja,
                    'eUbicacionCajero' => $eUbicacionCajero,
                    'eUsuariosRebsol' => $eUsuariosRebsol,
                    'idTalonario' => $idTalonario,
                    'coreApi' => ($this->estado('EstadoApi') === "core") ? 1 : 0,
                    'folioGlobal' => $folio['valor']
                ));
            } else {

                return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:ver.html.twig', array(
                    'oTalonario' => $oTalonario,
                    'oDetalleBoleta' => $oDetalleBoleta,
                    'esucursal' => $eSucursal,
                    'oTalonarioDetalles' => $oTalonarioDetalles,//boletas de la tabla talonario_detalle si el folio es global
                    'eTipoDocumento' => $eTipoDocumento,
                    'eUbicacionCaja' => $eUbicacionCaja,
                    'eUbicacionCajero' => $eUbicacionCajero,
                    'eUsuariosRebsol' => $eUsuariosRebsol,
                    'idTalonario' => $idTalonario,
                    'coreApi' => ($this->estado('EstadoApi') === "core") ? 1 : 0,
                    'folioGlobal' => $folio['valor']
                ));
            }


        }
        if ($folio['valor'] === '0') {
            return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:ver.html.twig', array(
                'oTalonario' => $oTalonario,
                'oDetalleBoleta' => $oDetalleBoleta,
                'boletas' => $porEmitir,//boletas de manera dinámica si el folio es por caja
                'esucursal' => $eSucursal,
                'eTipoDocumento' => $eTipoDocumento,
                'eUbicacionCaja' => $eUbicacionCaja,
                'eUbicacionCajero' => $eUbicacionCajero,
                'eUsuariosRebsol' => $eUsuariosRebsol,
                'idTalonario' => $idTalonario,
                'coreApi' => ($this->estado('EstadoApi') === "core") ? 1 : 0

            ));
        } else {
            return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:ver.html.twig', array(
                'oTalonario' => $oTalonario,
                'oDetalleBoleta' => $oDetalleBoleta,
                'oTalonarioDetalles' => $oTalonarioDetalles,//boletas de la tabla talonario_detalle si el folio es global
                'esucursal' => $eSucursal,
                'eTipoDocumento' => $eTipoDocumento,
                'eUbicacionCaja' => $eUbicacionCaja,
                'eUbicacionCajero' => $eUbicacionCajero,
                'eUsuariosRebsol' => $eUsuariosRebsol,
                'idTalonario' => $idTalonario,
                'coreApi' => ($this->estado('EstadoApi') === "core") ? 1 : 0

            ));
        }

    }
}