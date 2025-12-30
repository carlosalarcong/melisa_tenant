<?php

namespace App\Controller\Caja\Supervisor\ApoyoFacturacion;

use App\Controller\Caja\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;


/**
 * @author jcontreras@rayensalud.com
 * Fecha Creación: 02/12/2020
 */
class ApoyoFacturacionInformeController extends SupervisorController
{
    public function informeAction(Request $request)
    {
        $mes = $this->container->get('request_stack')->getCurrentRequest()->query->get('mes');
        $fecha = preg_split('[-]', $mes);
        $fechaInicio = new \DateTime($fecha[1].'-'.$fecha[0].'-01 00:00:00');
        $fechaFinal = $fechaInicio->format('Y-m-t 23:59:00');

        $em = $this->getDoctrine()->getManager();

        $registros = $em->getRepository('RebsolHermesBundle:AccionClinicaPaciente')->obtienePagosApoyoFacturacion($fechaInicio, $fechaFinal);

        foreach ($registros as $key => $registro) {
            if($registro['idTipoIdentificacionExtranjeroPaciente'] === 1){
                $registros[$key]['rutPaciente'] = $this->get('CommonServices')->formatearRut($registro['rutPaciente']);
            }
            if($registro['idTipoIdentificacionExtranjeroCajero'] === 1){
                $registros[$key]['rutCajero'] = $this->get('CommonServices')->formatearRut($registro['rutCajero']);
            }
        }

        return $this->render('RecaudacionBundle:Supervisor\ApoyoFacturacion:informeApoyoFacturacion.html.twig', array(
            'mes'        => $mes,
            'registros' => $registros
        ));
    }
}