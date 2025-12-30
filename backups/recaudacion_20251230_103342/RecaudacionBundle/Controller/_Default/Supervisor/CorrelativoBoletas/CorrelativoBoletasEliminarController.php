<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\CorrelativoBoletas;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorrelativoBoletasEliminarController extends SupervisorController
{

    /**
     * @param $request $idPlan id del plan
     * Descripción: eliminarTalonarioAction() Se eliminan los planes, pudiendo rechazarlo si el plan está asociado a un arancel
     * @return response json
     */
    public function eliminarAction(Request $request, $idTalonario)
    {
        //Validamos que la petición sea por ajax para evitar que al generar bootbox con el click derecho, nos salga el bootbox sin formato.
        $this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');

        $bValidDelete = $this->aOpcionesEliminar;
        $em = $this->getDoctrine()->getManager();
        $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario);

        $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        if ($folio['valor'] === '0') {
            $oTalonarioInactivos = $em->getRepository('RebsolHermesBundle:Talonario')->
            ObtieneTalonariosInactivosNuevos(
                $idTalonario,
                $this->container->getParameter('estado_inactivo'),
                $oTalonario->getNumeroActual(),
                $this->container->getParameter('estado_activo')
            );

            //Obtenemos el objeto de estado inactivo para posteriormente coloc�rselo a los datos activos.
            $oEstadoInactivo = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_inactivo'));

            if (!$oTalonarioInactivos) {
                $bValid = $bValidDelete["9"];
            } else {
                foreach ($oTalonarioInactivos as $talonario) {
                    $talonario->setIdEstado($oEstadoInactivo);
                    //Se prepara el objeto para que sea insertado
                    $em->persist($talonario);
                    //Se actualiza el dato en la base de datos
                    $em->flush();
                }
                $bValid = $bValidDelete["0"];
            }
        } else {

            $estadoFolioOcupada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                ->find($this->container->getParameter('EstadoTalonarioDetalle.ocupada'));
            $estadoFolioReservada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                ->find($this->container->getParameter('EstadoTalonarioDetalle.reservada'));
            $estadosFolios = array($estadoFolioOcupada->getId(), $estadoFolioReservada->getId());

            $consultaFolios = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                ->obtenerFoliosPorEstados($oTalonario->getId(), $estadosFolios);

            if (count($consultaFolios) > 0) {
                $bValid = $bValidDelete["2"];
            } else {
                $oTalonarioDetalles = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                    ->findBy(array('idTalonario' => $oTalonario->getId()));
                foreach ($oTalonarioDetalles as $folio){
                    $em->remove($folio);
                }
                $em->remove($oTalonario);
                $em->flush();
                $bValid = $bValidDelete["0"];
            }
        }
        return new Response(json_encode($bValid));
    }
}