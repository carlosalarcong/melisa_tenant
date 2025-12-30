<?php

namespace App\Controller\Caja\Supervisor\AsientoContable;

use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author jcontreras@rayensalud.com
 * Fecha Creación: 20/11/2020
 */
class AsientoContableInformeController extends SupervisorController
{
    public function informeAction(Request $request)
    {
        $fechaIni = $request->query->get('fechaIni');
        $fechaFin = $request->query->get('fechaFin');
        $idSucursal = $request->query->get('idSucursal');
        $idUsuario = $request->query->get('idUsuario');

        $em = $this->getDoctrine()->getManager();

        $fechaIniArr = explode("-", $fechaIni);
        $diaInicio      = $fechaIniArr[0];
        $mesInicio      = $fechaIniArr[1];
        $anyoInicio     = $fechaIniArr[2];
        $fechaIni = $anyoInicio . '-' . $mesInicio . '-'. $diaInicio . ' 00:00:00';

        $fechaFinArr = explode("-", $fechaFin);
        $diaFin      = $fechaFinArr[0];
        $mesFin      = $fechaFinArr[1];
        $anyoFin     = $fechaFinArr[2];
        $fechaFin = $anyoFin . '-' . $mesFin . '-'. $diaFin . ' 23:59:59';


        $asientosContablesArray = $em->getRepository("RebsolHermesBundle:DocumentoPago")
            ->obtenerAsientosContables(
                array(
                    'fechaIni'   => $fechaIni,
                    'fechaFin'   => $fechaFin,
                    'idSucursal' => $idSucursal,
                    'idUsuario'  => $idUsuario
                )
            );
        foreach ($asientosContablesArray as $key => $result) {
            $asientosContablesArray[$key]['identificacionExtranjero'] = $this->get('CommonServices')->formatearRut($result['identificacionExtranjero']);
        }
        $cuentasHaber = $em->getRepository("RebsolHermesBundle:FormaPago")->obtenerFormaPagoPadre();

        if (count($asientosContablesArray) == 0) {
            return $this->render('RecaudacionBundle:Supervisor\AsientoContable:InformeAsientoContableVacio.html.twig');
        }

        return $this->render('RecaudacionBundle:Supervisor\AsientoContable:InformeAsientoContable.html.twig',array(
            'asientosContablesArray'    => $asientosContablesArray,
            'cuentasHaber'              => $cuentasHaber,
            'fechaIni'                  => $fechaIni,
            'fechaFin'                  => $fechaFin,
            'idSucursal'                => $idSucursal,
            'idUsuario'                 => $idUsuario
        ));
    }
}