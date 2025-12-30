<?php

namespace App\Controller\Caja;

use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\AdjuntoType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\PrestacionType;
use App\Entity\Legacy\PersonaDomicilio;
use Symfony\Component\HttpFoundation\Request;

class BusquedaPacienteController extends RecaudacionController
{

    public function historialPacienteAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $historico = null;
        $historicoGarantia = null;
        $resultadoPagoHistoricos = null;
        $historicoReservasInpagas = null;
        $historicoTratamientos = null;
        $oPnatural = ($this->getSession('Pnatural')) ? $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('Pnatural')) : null;

        /*$estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }*/

        if (!$oPnatural and !$this->getSession('idPnaturalMascota')) {

            return $this->render('CajaBundle:Recaudacion/PostPago:Base.html.twig', array('ValidaDatos' => false));
        }

        $historico = $this->historicoDesdeListadoPacienteAgenda(/*$estadoApi, */$oPnatural->getId());
        $historicoGarantia = $this->historicoDesdeListadoPacienteGarantia($oPnatural->getId());

        /*if (!$this->getSession('idPnaturalMascota')) {
            dump('if');
            $historico = $this->historicoDesdeListadoPacienteAgenda($estadoApi, $oPnatural->getId());
            $historicoGarantia = $this->historicoDesdeListadoPacienteGarantia($oPnatural->getId());
        } else {
            dump('else');
            $historico = $this->historicoDesdeListadoMascota($estadoApi);
        }*/
        $resultadoPagoHistoricos = $this->rPagoCuenta()->GetPagosHistoricos($oPnatural->getId(), $this->estado('EstadoActivo'));

        /*if (!$this->getSession('idPnaturalMascota')) {

            if ($estadoApi === 'core') {
//dump(1);
                $resultadoPagoHistoricos = $this->rPagoCuenta()->GetPagosHistoricos($oPnatural->getId(), $this->estado('EstadoActivo'));
            } else {
//                dump(2);
                $resultadoPagoHistoricos = $this->rPagoCuenta()->GetPagosHistoricosApi1($oPnatural->getId(), $this->estado('EstadoActivo'));
            }

        } else {
//            dump(3);
            $resultadoPagoHistoricos = $this->GetResultadoPagoHistoricoIdPacienteMascota($this->estado('EstadoActivo'), $estadoApi);
        }*/
//        dump($resultadoPagoHistoricos); exit();

        $historicoReservasInpagas = $this->rPagoCuenta()->GetReservasInpagoHistoricos($oPnatural->getId(), $em);
        /*if (!$this->getSession('idPnaturalMascota')) {

            if ($estadoApi === 'core') {

                $historicoReservasInpagas = $this->rPagoCuenta()->GetReservasInpagoHistoricos($oPnatural->getId(), $em);
            } else {

                $historicoReservasInpagas = $this->rPagoCuenta()->GetReservasInpagoHistoricosApi1($oPnatural->getId(), $this->estado('EstadoActivo'));
            }
        } else {

            $historicoReservasInpagas = $this->GetReservasInpagoHistoricoIdPacienteMascota($this->estado('EstadoActivo'), $estadoApi);
        }*/

        if ($this->getSession('esTratamiento') == 0) {
            $historicoTratamientos = $this->rPagoCuenta()->GetTratamientosHistoricos($oPnatural->getId());

        } else {

            $historicoTratamientos = NULL;
        }

        if (count($historico) < 1 and count($resultadoPagoHistoricos) < 1 and count($historicoReservasInpagas) < 1 and is_null($historicoTratamientos)) {

            return $this->render('RecaudacionBundle:Recaudacion/PostPago:indexHistorial.html.twig',
                array(
                    'ValidaDatos' => false
                )
            );

        }

        $prestacionesReserva = (count($historicoReservasInpagas) > 0) ? $this->GetPrestacionesPorReserva($historicoReservasInpagas) : null;
        $adjuntoform = $this->createForm(AdjuntoType::class, null, array(
            'method' => 'POST',
            'action' => $this->generateUrl('Caja_PostPago_SubirPDF')
        ));

        /////////////
        $oPnatural = ($this->getSession('Pnatural')) ? $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('Pnatural')) : null;
        $arrayExamenPacienteFc = array();
        if ($oPnatural) {
            $oPersona = $oPnatural->getIdPersona();
            $idPersona = $oPersona->getId();
            $iIdEstado = $this->container->getParameter("estado_activo");
            $arrayExamenPacienteFc = $this->get('registroClinicoElectronico.Paciente')->obtenerExamenPacienteFcCaja($idPersona, $iIdEstado);

            $toDelete = [];
            foreach ($arrayExamenPacienteFc as $key => $oExamenPacienteFc) {
                $oExamenPacienteFcReal = $em->getRepository('RebsolHermesBundle:ExamenPacienteFc')->find($oExamenPacienteFc['idExamenPacienteFc']);
                $oOrden = $em->getRepository('RebsolHermesBundle:Orden')->findOneBy(['idExamenPacienteFc' => $oExamenPacienteFc['idExamenPacienteFc']]);
                if ($oOrden == null) {
                    //si es que no eixste orden es porque no tiene código
                    if ($oExamenPacienteFcReal->getIdPagoCuenta()) {
                        //si tiene pago cuenta, es porque se realizó un paoo asociado a ese examen
                        if ($oExamenPacienteFcReal->getIdPagoCuenta()->getIdEstadoPago()->getId() == 0) {
                            //si está en estado 0 (anulado) vuelve al listado
                            $arrExamenPacienteFcDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findBy(['idExamenPacienteFc' => $oExamenPacienteFcReal]);
                            $arrayExamenPacienteFc[$key]['arrExamenPacienteFcDetalle'] = $arrExamenPacienteFcDetalle;
                        } else {
                            $toDelete[] = $key;
                        }
                    } else {
                        $arrExamenPacienteFcDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findBy(['idExamenPacienteFc' => $oExamenPacienteFcReal]);
                        $arrayExamenPacienteFc[$key]['arrExamenPacienteFcDetalle'] = $arrExamenPacienteFcDetalle;
                    }

                } else {
                    $oEntrada = $em->getRepository('RebsolHermesBundle:Entrada')->findOneBy(['idOrden' => $oOrden->getId()]);
                    if (!is_null($oEntrada) && $oEntrada->getFechaPago() == null) {
                        $arrExamenPacienteFcDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findBy(['idExamenPacienteFc' => $oExamenPacienteFcReal]);
                        $arrayExamenPacienteFc[$key]['arrExamenPacienteFcDetalle'] = $arrExamenPacienteFcDetalle;
                    } else {
                        if (is_null($oEntrada)) {
                            $arrExamenPacienteFcDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findBy(['idExamenPacienteFc' => $oExamenPacienteFcReal]);
                            $arrayExamenPacienteFc[$key]['arrExamenPacienteFcDetalle'] = $arrExamenPacienteFcDetalle;
                        } else {
                            $toDelete[] = $key;
                        }
                    }
                }
            }

            foreach ($toDelete as $key) {
                unset($arrayExamenPacienteFc[$key]);
            }

            foreach ($arrayExamenPacienteFc as $key => $value){
                foreach ($value['arrExamenPacienteFcDetalle'] as $examenPacienteDetalle){
                    if ($examenPacienteDetalle->getIdAccionClinica() === null) {
                        $arrayExamenPacienteFc[$key]['tieneAccionClinica'] = false;
                        break;
                    } else {
                        $arrayExamenPacienteFc[$key]['tieneAccionClinica'] = true;
                    }
                }
            }
        }
        ///////////
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $domiclio = new PersonaDomicilio();
        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser()->getId());
        $prestacionForm = $this->createForm(PrestacionType::class, $domiclio,
            array(
                'validaform' => null,
                'iEmpresa' => $oEmpresa,
                'estado_activado' => $this->parametro('Estado.activo'),
                'sucursal' => $SucursalUsuario,
                'database_default' => $this->obtenerEntityManagerDefault()
            )
        );


        $arrayHistorialPaciente = array(
            'adjuntoForm' => $adjuntoform->createView(),
            'ValidaDatos' => true,
            'ValidaHistoricoPago' => (count($historico) < 1 and count($resultadoPagoHistoricos) < 1) ? false : true,
            'ValidaHistoricoGarantias' => (count($historicoGarantia) < 1 ) ? false : true,
            'ValidaHistoricoReserva' => (count($historicoReservasInpagas) < 1) ? false : true,
            'ValidaHistoricoTratamiento' => is_null($historicoTratamientos) ? false : true,
            'historico' => $historico,
            'historicoGarantia' => $historicoGarantia,
            'historicoConBoleta' => $resultadoPagoHistoricos,
            'historicoReservas' => $historicoReservasInpagas,
            'historicoTratamiento' => $historicoTratamientos,
            'prestacionesReserva' => $prestacionesReserva,
            'paciente' => $oPnatural,
            'VarCajaHoy' => $this->getSession('VarCajaHoy'),
            'estadoT' => $this->estado('EstadoBoletaActiva'),
//            'coreApi' => ($estadoApi === 'core') ? 1 : 0,
            'from' => $this->getSession('from'),
            'arrayExamenPacienteFc' => $arrayExamenPacienteFc,

            'prestacionForm' => $prestacionForm->createView(),
        );
//dump($arrayHistorialPaciente); exit();
        $esAmbulatorio = $request->request->get('esAmbulatorio');
        return $this->render('RecaudacionBundle:InformacionHistoricaPaciente:_listadoHistoricoPaciente.html.twig', $arrayHistorialPaciente);
//        if ($esAmbulatorio === 'true') {
//            return $this->render('CajaBundle:_Default/PagoCuenta/PostPago:indexHistorial.html.twig', $arrayHistorialPaciente);
//        } else {
//            return $this->render('CajaBundle:Recaudacion/PostPago:indexHistorial.html.twig', $arrayHistorialPaciente);
//        }
    }

    private function historicoDesdeListadoPacienteAgenda($id)
    {
        return $this->rPaciente()->HistoricoPagosPaciente($id);
    }

    private function historicoDesdeListadoPacienteGarantia($id)
    {
        return $this->rPaciente()->HistoricoPagosPacienteGarantia($id);
    }



    public function busquedaPacienteAction(){

        return $this->render('RecaudacionBundle:Default\BusquedaPaciente:_busquedaPaciente.html.twig');
    }

    public function busquedaBasicaAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('RecaudacionBundle:Default\BusquedaPaciente:_busquedaBasica.html.twig',
            array(
//                'coreApi' => ($estadoApi === "core") ? 1 : 0,
                /*'busquedaBasicaForm' => $busquedaBasicaForm->createView(),
                'busquedaAvanzadaForm' => $busquedaAvanzadaForm->createView(),
                'informacionPacienteForm' => $pagoPacienteForm->createView(),
                'prestacionForm' => $prestacionForm->createView(),
                'ValidaHistoricoGarantias' => true,
                'ValidaHistoricoPago' => true,
                'ValidaHistoricoReserva' => true,
                'ValidaHistoricoTratamiento' => true,*/
            )
        );
    }

    public function busquedaAvanzadaAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('RecaudacionBundle:Default\BusquedaPaciente:_busquedaAvanzada.html.twig',
            array(
//                'coreApi' => ($estadoApi === "core") ? 1 : 0,
                /*'busquedaBasicaForm' => $busquedaBasicaForm->createView(),
                'busquedaAvanzadaForm' => $busquedaAvanzadaForm->createView(),
                'informacionPacienteForm' => $pagoPacienteForm->createView(),
                'prestacionForm' => $prestacionForm->createView(),
                'ValidaHistoricoGarantias' => true,
                'ValidaHistoricoPago' => true,
                'ValidaHistoricoReserva' => true,
                'ValidaHistoricoTratamiento' => true,*/
            )
        );
    }
}
