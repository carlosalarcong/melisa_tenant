<?php

namespace App\Controller\Caja\Supervisor\ApoyoFacturacion;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use App\Form\Supervisor\ApoyoFacturacion\ApoyoFacturacionType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author jcontreras@rayensalud.com
 * Fecha Creación: 02/12/2020
 */
class ApoyoFacturacionController extends SupervisorController
{
    public function indexAction(Request $request)
    {
        $estadoApi = $this->estado('EstadoApi');

        if($estadoApi != 'core'){
            if($estadoApi['rutaApi'] === 'ApiPV'){
                $estadoApi = 'core';
            }
        }

        $em = $this->getDoctrine()->getManager();

        //Obtener parametro del maximo de días del reporte
        $maximoDias = $em->getRepository('RebsolHermesBundle:Parametro')
            ->obtenerParametro('MAXIMO_DIAS_REPORTE_PRODUCCION');

        $eSucursal = '';
        $eTipoDocumento = '';
        $eUsuariosRebsol = '';

        $form = $this->createForm(ApoyoFacturacionType::class, null,array());

        return $this->render('RecaudacionBundle:Supervisor/ApoyoFacturacion:index.html.twig',
            array(
                'form' => $form->createView()
            , 'maximoDias' => $maximoDias['valor']
            , 'esucursal'        => $eSucursal
            , 'eTipoDocumento'   => $eTipoDocumento
            , 'eUsuariosRebsol'  => $eUsuariosRebsol
            , 'coreApi'          => ($estadoApi === "core") ? 1 : 0
            )
        );
    }
}