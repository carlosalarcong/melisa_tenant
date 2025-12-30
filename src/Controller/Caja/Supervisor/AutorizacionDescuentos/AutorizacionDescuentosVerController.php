<?php

namespace App\Controller\Caja\Supervisor\AutorizacionDescuentos;

use App\Controller\Caja\Supervisor\SupervisorController;
use App\Controller\Caja\Supervisor\AutorizacionDescuentos\render;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AutorizacionDescuentosVerController extends SupervisorController {

    /**
     * @return render
     * Descripción: indexAction() Muetra el listado de las boletas en forma correlativa
     */
    public function verAction(Request $request, $idDiferencia) {

        $this->ValidadPeticionAjax($request, 'Supervisor_AutorizacionDescuentos');
        $em = $this->getDoctrine()->getManager();

        $diferencia = $em->getRepository('RebsolHermesBundle:Diferencia')->find($idDiferencia);

        if (!$diferencia) {
            throw $this->createNotFoundException('Unable to find diferencia.');
        }

        $renderView = $this->renderView('RecaudacionBundle:Supervisor/AutorizacionDescuentos:show.html.twig',
            array(
                 'diferencia'      => $diferencia
            )
        );
        return new Response($renderView);
    }

}