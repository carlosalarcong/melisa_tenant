<?php

namespace App\Controller\Caja\Supervisor\AsientoContable;

use App\Controller\Caja\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AsientoContableUsuarioBySucursalController extends SupervisorController
{
    /**
     * @return Response
     * Descripción: indexAction() Muestra el listado de Cajas de una determinada Sucursal (idSucursal)
     */
    public function indexAction(Request $request)
    {
        $idSucursal = $this->container->get('request_stack')->getCurrentRequest()->query->get('idSucursal');
        $fechaIni = $this->container->get('request_stack')->getCurrentRequest()->query->get('fechaIni');
        $fechaFin = $this->container->get('request_stack')->getCurrentRequest()->query->get('fechaFin');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('RebsolHermesBundle:Sucursal')->find($idSucursal);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sucursal entity.');
        }

        $oSucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerCajerolUsuarioSucursal(
            array(
                'fechaIni' => $fechaIni,
                'fechaFin' => $fechaFin,
                'idSucursal' => $idSucursal
            )
        );

        return new Response(json_encode($oSucursalUsuario));
    }
}