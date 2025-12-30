<?php
namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\AsientoContable;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Controller\Supervisor\AsientoContable\render;
use Rebsol\RecaudacionBundle\Form\Type\Supervisor\AsientoContable\AsientoContableType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author jcontreras@rayensalud.com
 * @version 1.0.0
 * Fecha Creación: 17/11/2020
 * Participantes:
 *
 */
class AsientoContableController extends SupervisorController
{

    /**
     * @return render
     * Descripción: indexAction() Muetra las cuentas contables en
     */
    public function indexAction(Request $request)
    {

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        $em = $this->getDoctrine()->getManager();

        //Obtener parametro del maximo de días del reporte
        $maximoDias = $em->getRepository('RebsolHermesBundle:Parametro')
            ->obtenerParametro('MAXIMO_DIAS_REPORTE_ASIENTOS_CONTABLES');

        //Obtenemos el login de la empresa
        $oEmpresa = $this->ObtenerEmpresaLogin();

        //Obtenemos el objeto de la sucursal según el usuario
        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());

        $eSucursal = '';
        $eTipoDocumento = '';
        $eUbicacionCaja = '';
        $eUbicacionCajero = '';
        $eUsuariosRebsol = '';
        $form = $this->createForm(AsientoContableType::class, null,
            array(
                'estado_activado'  => $this->container->getParameter('estado_activo'),
                'idEstado'         => $this->container->getParameter('estado_activo'),
                'idEmpresa'        => $oEmpresa->getId(),
                'idSucursal'       => $SucursalUsuario->getId(),
                'database_default' => $this->obtenerEntityManagerDefault(),
                'oEmpresa'         => $oEmpresa
            ));

        return $this->render('RecaudacionBundle:Supervisor/AsientoContable:index.html.twig',
            array(
                'form' => $form->createView(),
                'oSucursal' => $SucursalUsuario->getId(),
                'esucursal' => $eSucursal,
                'eTipoDocumento' => $eTipoDocumento,
                'eUbicacionCaja' => $eUbicacionCaja,
                'eUbicacionCajero' => $eUbicacionCajero,
                'eUsuariosRebsol' => $eUsuariosRebsol,
                'coreApi' => ($estadoApi === "core") ? 1 : 0,
                'maximoDias' => $maximoDias['valor']
            )
        );
    }
}
