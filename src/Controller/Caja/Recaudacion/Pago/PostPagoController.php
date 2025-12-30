<?php

namespace App\Controller\Caja\Recaudacion\Pago;

use Rebsol\DteBundle\Services\DteAces\ConexionAces\Constant\ConexionAcesConstant;
use App\Entity\Legacy\AccionClinicaPacienteLog;
use App\Entity\Legacy\ArticuloPacienteLog;
use App\Entity\Legacy\CuentaPacienteLog;
use App\Entity\Legacy\DetalleTalonario;
use App\Entity\Legacy\ReservaAtencionLog;
use App\Controller\Caja\Pago\render;
use App\Controller\Caja\RecaudacionController;
use App\Form\Recaudacion\Pago\AdjuntoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PostPagoController extends RecaudacionController
{

    /**
     * URL del WS de IMED
     * @var string
     * @access private
     */
    var $UrlWS = "";

    /**
     * @return render
     * Descripción: IndexAction() Genera Informe del PaGo efectuado y el historico del Paciente
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $oPaciente = ($this->getSession('Pnatural')) ? $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('Pnatural')) : null;
        if (!$oPaciente) {
            return $this->render('CajaBundle:Recaudacion/PostPago:Base.html.twig', array('ValidaDatos' => false));
        }

        $historico = $this->rPaciente()->HistoricoPagosPaciente($oPaciente->getId());
        $this->killSession('Pnatural');

        return $this->render('RecaudacionBundle:Recaudacion/PostPago:Base.html.twig', array(
            'ValidaDatos' => true,
            'historico' => $historico,
            'paciente' => $oPaciente));
    }

    /**
     * [HistorialPacienteAction description]
     */
    public function historialPacienteAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $historico = null;
        $historicoGarantia = null;
        $resultadoPagoHistoricos = null;
        $historicoReservasInpagas = null;
        $oPnatural = ($request->request->get('idPersona') != '') ?
            $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array('idPersona' => $request->request->get('idPersona'))) :
            $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('Pnatural'));

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        if (!$oPnatural and !$this->getSession('idPnaturalMascota')) {

            return $this->render('RecaudacionBundle:Recaudacion/PostPago:Base.html.twig', array('ValidaDatos' => false));
        }

        if (!$this->getSession('idPnaturalMascota')) {
            $historico = $this->historicoDesdeListadoPacienteAgenda($estadoApi, $oPnatural->getId());
            $historicoGarantia = $this->historicoDesdeListadoPacienteGarantia($oPnatural->getId());
        } else {
            $historico = $this->historicoDesdeListadoMascota($estadoApi);
        }

        if (!$this->getSession('idPnaturalMascota')) {

            if ($estadoApi === 'core') {

                $resultadoPagoHistoricos = $this->rPagoCuenta()->GetPagosHistoricos($oPnatural->getId(), $this->estado('EstadoActivo'), true);
            } else {

                $resultadoPagoHistoricos = $this->rPagoCuenta()->GetPagosHistoricosApi1($oPnatural->getId(), $this->estado('EstadoActivo'));
            }

        } else {

            $resultadoPagoHistoricos = $this->GetResultadoPagoHistoricoIdPacienteMascota($this->estado('EstadoActivo'), $estadoApi);
        }

        if (!$this->getSession('idPnaturalMascota')) {

            if ($estadoApi === 'core') {

                $historicoReservasInpagas = $this->rPagoCuenta()->GetReservasInpagoHistoricos($oPnatural->getId(), $em);
            } else {

                $historicoReservasInpagas = $this->rPagoCuenta()->GetReservasInpagoHistoricosApi1($oPnatural->getId(), $this->estado('EstadoActivo'));
            }
        } else {

            $historicoReservasInpagas = $this->GetReservasInpagoHistoricoIdPacienteMascota($this->estado('EstadoActivo'), $estadoApi);
        }

        if ($this->getSession('esTratamiento') == 0) {

            $historicoTratamientos = (!$this->getSession('idPnaturalMascota'))
                ? ($estadoApi === 'core') ? $this->rPagoCuenta()->GetTratamientosHistoricos($oPnatural->getId()) : null : null;

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
        $oPnatural = ($request->request->get('idPersona') != '') ?
            $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array('idPersona' => $request->request->get('idPersona'))) :
            $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('Pnatural'));

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

        $arrayHistorialPaciente = [
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
            'coreApi' => ($estadoApi === 'core') ? 1 : 0,
            'from' => $this->getSession('from'),
            'arrayExamenPacienteFc' => $arrayExamenPacienteFc
        ];

        $esAmbulatorio = $request->request->get('esAmbulatorio');

        if ($esAmbulatorio === 'true') {
            return $this->render('RecaudacionBundle:PagoCuenta/PostPago:indexHistorial.html.twig', $arrayHistorialPaciente);
        } else {
            return $this->render('RecaudacionBundle:Recaudacion/PostPago:indexHistorial.html.twig', $arrayHistorialPaciente);
        }
    }

    /**
     * [historicoDesdeListadoMascota description]
     */
    private function historicoDesdeListadoMascota($estadoApi)
    {
        $em = $this->getDoctrine()->getManager();
        $oDueno = ($this->getSession('idPnaturalMascota')) ? $em->getRepository("RebsolHermesBundle:Pnatural")->obtenerPadrePnat($this->getSession('idPnaturalMascota')) : null;
        $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array('idPersona' => $oDueno->getIdPersona()->getId()));
        return ($estadoApi === "core") ? $this->rPaciente()->HistoricoPagosPaciente($oPnatural->getId()) : $this->rPaciente()->HistoricoPagosPacienteApi1($oPnatural->getId());
    }

    private function historicoDesdeListadoPacienteAgenda($estadoApi, $id)
    {
        return ($estadoApi === 'core') ? $this->rPaciente()->HistoricoPagosPaciente($id, true) : $this->rPaciente()->HistoricoPagosPacienteApi1($id);
    }

    private function historicoDesdeListadoPacienteGarantia($id)
    {
        return $this->rPaciente()->HistoricoPagosPacienteGarantia($id);
    }

    private function GetReservasInpagoHistoricoIdPacienteMascota($estadoActivo, $estadoApi)
    {
        $em = $this->getDoctrine()->getManager();
        $oDueno = ($this->getSession('idPnaturalMascota')) ? $em->getRepository("RebsolHermesBundle:Pnatural")->obtenerPadrePnat($this->getSession('idPnaturalMascota')) : null;
        $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array('idPersona' => $oDueno->getIdPersona()->getId()));
        return ($estadoApi === "core") ? $this->rPagoCuenta()->GetReservasInpagoHistoricos($oPnatural->getId(), $estadoActivo) : $this->rPagoCuenta()->GetReservasInpagoHistoricosApi1($oPnatural->getId(), $estadoActivo);
    }

    private function GetGarantiasHistoricoIdPacienteMascota($estadoActivo, $estadoApi)
    {
        $em = $this->getDoctrine()->getManager();
        $oDueno = ($this->getSession('idPnaturalMascota')) ? $em->getRepository("RebsolHermesBundle:Pnatural")->obtenerPadrePnat($this->getSession('idPnaturalMascota')) : null;
        $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array('idPersona' => $oDueno->getIdPersona()->getId()));
        return ($estadoApi === "core") ? $this->rPagoCuenta()->GetGarantiaHistoricos($oPnatural->getId(), $estadoActivo) : $this->rPagoCuenta()->GetGarantiaHistoricosApi1($oPnatural->getId(), $estadoActivo);
    }

    private function GetResultadoPagoHistoricoIdPacienteMascota($estadoActivo, $estadoApi)
    {
        $em = $this->getDoctrine()->getManager();
        $oDueno = ($this->getSession('idPnaturalMascota')) ? $em->getRepository("RebsolHermesBundle:Pnatural")->obtenerPadrePnat($this->getSession('idPnaturalMascota')) : null;
        $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array('idPersona' => $oDueno->getIdPersona()->getId()));
        return ($estadoApi === "core") ? $this->rPagoCuenta()->GetPagosHistoricos($oPnatural->getId(), $estadoActivo) : $this->rPagoCuenta()->GetPagosHistoricosApi1($oPnatural->getId(), $estadoActivo);
    }

    private function GetPrestacionesPorReserva($arr)
    {
        $arrReservas = array();
        foreach ($arr as $a) {
            $arrReservas[] = $a['idReserva'];
        }
        return $this->rPagoCuenta()->GetPrestacionesPorReserva($arrReservas);
    }

    public function anulacionPagoCajaAction(Request $request, $id)
    {
        return $this->AnulacionPago(array(
            'id' => $id,
            'from' => 0,
            'api' => 0
        ));
    }
    public function anulacionPagoCajaPagoCuentaAction(Request $request, $id)
    {
        return $this->AnulacionPagoPagoCuenta(array(
            'idPagoCuenta' => $id,
        ));
    }

    public function anulacionPagoAgendaAction()
    {
        return $this->AnulacionPago(array(
            'id' => $this->container->get('request_stack')->getCurrentRequest()->query->get('id'),
            'from' => 1,
            'api' => 0
        ));
    }

    public function anulacionPagoAgendaApi1Action()
    {
        return $this->AnulacionPago(array(
            'id' => $this->container->get('request_stack')->getCurrentRequest()->query->get('id'),
            'from' => 1,
            'api' => 1
        ));
    }

    protected function AnulacionPago($arr)
    {
        $em = $this->getDoctrine()->getManager();
        $oUser = $this->getUser();
        $oReservaAtencion = ($arr['from'] == 1) ? $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($arr['id']) : NULL;
        $oPagoCuenta = ($arr['from'] == 1) ? $oReservaAtencion->getIdPagoCuenta() : $em->getRepository('RebsolHermesBundle:PagoCuenta')->find($arr['id']);
        $oPaciente = ($arr['from'] == 1) ? $oReservaAtencion->getIdPaciente() : $oPagoCuenta->getIdPaciente();
        $oCuentaPaciente = $em->getRepository('RebsolHermesBundle:CuentaPaciente')->findOneBy(array('idPaciente' => $oPaciente));
        $oDetalleTalonario = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->findOneBy(array('idPagoCuenta' => $oPagoCuenta));
        $oAccionClinicaPaciente = $em->getRepository('RebsolHermesBundle:AccionClinicaPaciente')->findBy(array('idPaciente' => $oPaciente));
        $oArticuloPaciente = $em->getRepository('RebsolHermesBundle:ArticuloPaciente')->findBy(array('idPaciente' => $oPaciente));
        $oEstadoPago = $this->estado('EstadoPagoAnulada');
        $oEstadoCuenta = $this->estado('EstadoCuentaAnulada');
        $oEstadoDetalleTalonario = $this->estado('EstadoBoletaAnulada');
        $oFecha = new \DateTime();

        if (($arr['from'] == 1) ? $this->rPagoCuenta()->ValidaFechasCajas($oUser->getid(), $oReservaAtencion->getFechaRecepcion(), $oFecha) : true) {

            if ($this->rPagoCuenta()->ValidaFacturable($oDetalleTalonario)) {

                //RESERVA ATENCION
                if ($arr['from'] != 1) {
                    $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->findOneBy(array('idPagoCuenta' => $oPagoCuenta->getId()));
                }

                if ($oReservaAtencion) {
                    ($arr['api'] == 0) ? $oReservaAtencion->setIdPaciente(NULL) : NULL;
                    $oReservaAtencion->setIdPagoCuenta(NULL);
                    $oReservaAtencion->setFechaRecepcion(NULL);
                    $oReservaAtencion->setRecepcionado(0);

                    $estadoApi = $this->estado('EstadoApi');

                    if ($estadoApi != 'core') {
                        if ($estadoApi['rutaApi'] === 'ApiPV') {
                            if ($oReservaAtencion->getMeetingId()) {
                                $curl = curl_init();

                                $usuarioZoom = $this->container->getParameter('ApiZoom.User');
                                $passZoom = $this->container->getParameter('ApiZoom.Password');
                                $urlApi = $this->container->getParameter('ApiZoom.Url');

                                $params['id'] = $oReservaAtencion->getMeetingId();
                                $params['email'] = $oReservaAtencion->getCorreoElectronico();
                                $params['send_email'] = true;
                                $params_json = json_encode($params);

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $urlApi . "Meeting/DeleteMeeting",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => "",
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_POSTFIELDS => $params_json,
                                    CURLOPT_USERPWD => $usuarioZoom . ":" . $passZoom,
                                    CURLOPT_HTTPHEADER => array(
                                        "Content-Type: application/json"
                                    ),
                                ));

                                $response = curl_exec($curl);
                                $err = curl_error($curl);
                                curl_close($curl);

                                $oReservaAtencion->setUrlMasterZoom(NULL);
                                $oReservaAtencion->setMeetingId(NULL);
                            }
                        }
                    }

                    $em->persist($oReservaAtencion);
                }

                //PAGO CUENTA
                $oPagoCuenta->setIdEstadoPago($oEstadoPago);
                $oPagoCuenta->setIdUsuarioAnulacion($oUser);
                $oPagoCuenta->setFechaAnulacion($oFecha);
                $em->persist($oPagoCuenta);

                //ACCION CLINICA PACIENTE
                $oCuentaPaciente->setIdEstadoCuenta($oEstadoCuenta);
                $em->persist($oCuentaPaciente);

                //CUENTA PACIENTE LOG
                $oCuentaPacienteLog = new CuentaPacienteLog();
                $evento = $this->rPaciente()->obtenerEventos($oPaciente->getidPNatural());
                $oCuentaPacienteLog->setSaldoCuenta(00.00);
                $oCuentaPacienteLog->setFechaEstadoCuenta($oFecha);
                $oCuentaPacienteLog->setNumeroAccion($evento);
                $oCuentaPacienteLog->setIdCuenta($oCuentaPaciente);
                $oCuentaPacienteLog->setIdEstadoCuenta($oEstadoCuenta);
                $oCuentaPacienteLog->setIdUsuario($oUser);
                $oCuentaPacienteLog->setIdPaciente($oPaciente);
                $em->persist($oCuentaPacienteLog);

                //ACCION CLINICA PACIENTE
                if ($oAccionClinicaPaciente) {
                    foreach ($oAccionClinicaPaciente as $a) {
                        $a->setIdEstadoPago($oEstadoPago);
                        if ($a->getIdDetalleTratamiento()){
                            $oDetalleTratamiento = $this->setDetalleTratamiento($a);
                            $oTratamiento = $this->setTratamiento($oDetalleTratamiento->getIdTratamiento());
                            $em->persist($oDetalleTratamiento);
                            $em->persist($oTratamiento);
                        }
                        $em->persist($a);
                    }
                }

                // ARTICULO PACIENTE
                if ($oArticuloPaciente) {
                    foreach ($oArticuloPaciente as $a) {
                        $a->setIdEstadoPago($oEstadoPago);
                        if ($a->getIdDetalleTratamiento()){
                            $oDetalleTratamiento = $this->setDetalleTratamiento($a);
                            $oTratamiento = $this->setTratamiento($oDetalleTratamiento->getIdTratamiento());
                            $em->persist($oDetalleTratamiento);
                            $em->persist($oTratamiento);
                        }
                        $em->persist($a);
                    }
                }

                if ($oDetalleTalonario) {
                    foreach ($oDetalleTalonario as $b) {
                        $b->setIdEstadoDetalleTalonario($oEstadoDetalleTalonario);
                        $em->persist($b);
                    }
                }
                if ($arr['from'] == 1) {
                    //RESERVA ATENCION LOG
                    $oReservaAtencionLog = new ReservaAtencionLog();
                    $oReservaAtencionLog->setIdReservaAtencion($oReservaAtencion);
                    $oReservaAtencionLog->setIdHorarioConsultaNuevo($oReservaAtencion->getIdHorarioConsulta());
                    $oReservaAtencionLog->setFechaRegistro($oFecha);
                    $oReservaAtencionLog->setIdReservaTipoLog($this->tipo('TipoLogAnulado'));
                    $oReservaAtencionLog->setIdUsuarioModifica($oUser);
                    $em->persist($oReservaAtencionLog);
                }

                $evitaAnulacionImed = $em->getRepository("RebsolHermesBundle:Parametro")->obtenerParametro('EVITAR_ANULACION_EN_IMED');
                $evitaAnulacionImed = $evitaAnulacionImed['valor'] === '1';

                if (!$evitaAnulacionImed) {
                    $ointerfazImed = $em->getRepository('RebsolHermesBundle:InterfazImed')->findOneBy(array('idPagoCuenta' => $oPagoCuenta->getId()));
                    if ($ointerfazImed) {
                        $Var = $em->getRepository('RebsolHermesBundle:PagoCuenta')->SetGlobalsVar($this->ObtenerEmpresaLogin(), $this->getUser());
                        $arrayUnserialize = unserialize($ointerfazImed->getListaBonos());
                        foreach ($arrayUnserialize as $bono) {
                            $client = new \nusoap_client($Var['IMED_WS'], true);
                            $param = array(
                                'CodUsuario' => $ointerfazImed->getCodUsuario(),
                                'CodClave' => $ointerfazImed->getCodClave(),
                                'CodFinanciador' => $ointerfazImed->getCodFinanciador(),
                                'CodLugar' => $ointerfazImed->getCodLugar(),
                                'FolioBono' => $bono['FolioBono'],
                                'RutCajero' => $ointerfazImed->getRutCajero()
                            );
                            $answerResult = $client->call('AnulBonInterfaz', $param);
                            if ($answerResult['CodError'] == 1) {
                                return new Response(json_encode(array(
                                    'motive' => $answerResult['GloError'],
                                    'done' => 0)));
                            }
                        }
                        $ointerfazImed->setEstado(1);
                        $em->persist($ointerfazImed);
                    }
                }

                //anular exámenes
                // $iEstadoInactivo   = $this->container->getParameter("estado_inactivo");
                // $oEstadoInactivo   = $em->getRepository("RebsolHermesBundle:Estado")->find($iEstadoInactivo);
                // $iIdEstado = $this->container->getParameter("estado_activo");
                // $oEstado = $em->getRepository("RebsolHermesBundle:Estado")->find($iIdEstado);
                // $arrExamenesPacientesFc = $em->getRepository('RebsolHermesBundle:ExamenPacienteFc')->findBy([
                // 	'idPagoCuenta' => $oPagoCuenta,
                // 	'idEstado' => $oEstado
                // ]);
                // foreach($arrExamenesPacientesFc as $oExamenPacienteFc) {

                // 	$oOrden = $em->getRepository('RebsolHermesBundle:Orden')->findOneBy([
                // 		'idExamenPacienteFc' => $oExamenPacienteFc
                // 	]);

                // 	if(!is_null($oOrden)){
                // 		$oEntrada = $em->getRepository('RebsolHermesBundle:Entrada')->findOneBy(['idOrden' => $oOrden->getId()]);
                // 		if(!is_null($oEntrada)){
                // 			$oEntrada->setEstadoOrden('CA');
                // 			$oEntrada->setFechaAnulacion($oFecha->format("YmdHis"));
                // 			$oOrden->setFechaAnulacion($oFecha->format("YmdHis"));
                // 			$oOrden->setIdUsuarioAnulacion($this->getUser()->getId());
                // 			$oExamenPacienteFc->setIdEstado($oEstadoInactivo);
                // 		}
                // 	}
                // }

                $arrOrdenes = $em->getRepository('RebsolHermesBundle:Orden')->findBy([
                    'idPagoCuenta' => $oPagoCuenta->getId()
                ]);

                foreach ($arrOrdenes as $oOrden) {
                    $oEntrada = $em->getRepository('RebsolHermesBundle:Entrada')->findOneBy(['idOrden' => $oOrden->getId()]);
                    if (!is_null($oEntrada)) {
                        $oEntrada->setEstadoOrden('CA');
                        $oEntrada->setFechaAnulacion($oFecha->format("YmdHis"));
                        $oEntrada->setEstadoLecturaOrden(0);
                        $oOrden->setFechaAnulacion($oFecha->format("YmdHis"));
                        $oOrden->setIdUsuarioAnulacion($this->getUser()->getId());
                    }
                }

                //
                $em->flush();

                return new Response(json_encode(array(
                    'motive' => 'Anulado Correctamente',
                    'done' => 1)));
            } else {
                return new Response(json_encode(array(
                    'motive' => $this->ErrorImedHermes('pagoConFactura'),
                    'done' => 0)));
            }
        } else {
            return new Response(json_encode(array(
                'motive' => $this->ErrorImedHermes('fechaCajaAnulacion'),
                'done' => 0)));
        }
    }

    public function pagoGarantiaAction(Request $request)
    {
        if($this->ajax('id')){
            return new Response(json_encode($this->rPagoCuenta()->ObtenerDatosPagoGarantia($this->ajax('id'))));
        }
    }

    public function VerificaSubEmpresaActualCajero_PrestacionesInsumosAction()
    {
        return new Response(json_encode(($this->rPagoCuenta()->SubEmpresaActualCajeroPrestacionesInsumos($this->ajax('id'), $this->getSession('idTalonario')) == 0) ? 1 : 0));
    }

    public function pagoGarantiaBuscarPrestacionesAction(Request $request)
    {
        $this->get('session')->set('idPacienteGarantia', $this->ajax('id'));
        if(!$request->query->get('idPagoCuenta')){
            $PrestacionesGarantia = ($this->rPagoCuenta()->BuscarPrestacionesGarantia($this->get('session')->get('idPacienteGarantia'))) ? $this->rPagoCuenta()->BuscarPrestacionesGarantia($this->get('session')->get('idPacienteGarantia')) : null;
        }else{
            $PrestacionesGarantia = $this->rPagoCuenta()->BuscarPrestacionesGarantiaPrePagoCuenta($request->query->get('idPagoCuenta'));
        }

        return new Response(json_encode(($PrestacionesGarantia) ? $PrestacionesGarantia : 0));
    }

    public function pagoGarantiaBuscarHonorarioAction()
    {
        $em = $this->getDoctrine()->getManager();
        $d = $this->ajax('datos');

        $ArrTalonarios = $this->getSession('idTalonario');
        if ($this->SubEmpresaPorPrestacionTalonario($d['idPrestacion'], $ArrTalonarios)) {
            $fechahoy = new \DateTime();
            $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($d['idPrestacion']);
            $iPabellon = $oAccionClinica->getidGuarismo()->getId();
            $idAbiertaPrecio = $this->rPagoCuenta()->DatosAbiertaPrecioParaImedPrestacionesHonorarios($oAccionClinica->getid(), $d['plan'], $oEstadoAct, $fechahoy->format("Y-m-d H:i:s"));
            $this->setsession('idSubEmpresaItem', $oAccionClinica->getIdSubEmpresa()->getId());
            if (!$idAbiertaPrecio) {
                $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($d['idPrestacion']);
                return new Response(json_encode(array(
                    'id' => $oAccionClinica->getId(),
                    'nombre' => $oAccionClinica->getNombreAccionClinica(),
                    'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                    'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                    'precio' => "0",
                    'idad' => "",
                    'nombreItem' => "",
                    'pabellon' => "",
                    'cantidad' => "0"
                )));
            } else {
                $oAbiertaDistribucion = $em->getRepository('RebsolHermesBundle:AbiertaDistribucion')->findBy(array("idPrecio" => $idAbiertaPrecio, "idEstado" => $oEstadoAct));
                $ItemsArray = array();
                foreach ($oAbiertaDistribucion as $c) {
                    $ItemsArray[] = $c->getid();
                }
                return new Response(json_encode($this->ObtenerHonarioGarantia($d['idPrestacion'], $idAbiertaPrecio, $d['plan'], $fechahoy, $oEstadoAct, $ItemsArray, $iPabellon, $d['sucursal'], $d['TipoAtencion'], $d['idPaciente'])));
            }
        } else {
            return new Response("nosubempresa");
        }
    }

    public function pagoGarantiaBuscarArticulosAction(Request $request)
    {
        $this->get('session')->set('idPacienteGarantia', $this->ajax('id'));
        if(!$request->query->get('idPagoCuenta')){
            $ArticulosGarantia = ($this->rPagoCuenta()->BuscarArticuloGarantia($this->get('session')->get('idPacienteGarantia'))) ? $this->rPagoCuenta()->BuscarArticuloGarantia($this->get('session')->get('idPacienteGarantia')) : null;
        }else{
            $ArticulosGarantia = $this->rPagoCuenta()->BuscarArticuloGarantiaPrePagoCuenta($request->query->get('idPagoCuenta'));
       }

        return new Response(json_encode(($ArticulosGarantia) ? $ArticulosGarantia : 0));
    }

    public function pagoGarantiaBuscaOtrosMediosAction(Request $request)
    {
        if($this->ajax('id')){
            return new Response(json_encode($this->rPagoCuenta()->BuscaOtrosMediosGarantia($this->ajax('id'))));
        }
//        return new Response(json_encode($this->rPagoCuenta()->BuscaOtrosMediosGarantiaPrePago($request->query->get('idPrePagoCuenta'))));

    }

    public function pagoReservaAction()
    {
        return new Response(json_encode($this->rPagoCuenta()->ObtenerDatosPagoReserva($this->ajax('id'))));
    }

    public function reservaVerificaSubEmpresaActualCajeroPrestacionesInsumosAction()
    {
        return new Response(json_encode(($this->rPagoCuenta()->SubEmpresaActualCajeroPrestacionesInsumosReserva($this->ajax('id'), $this->estado('EstadoActivo'), $this->getSession('idTalonario')) == 0) ? 1 : 0));
    }

    public function pagoReservaBuscarPrestacionesAction()
    {
        return new Response(json_encode($this->rPagoCuenta()->BuscarPrestacionesReserva($this->ajax('id'), $this->estado('EstadoActivo'))));
    }

    /**
     * Detalles Pago
     */
    public function detallesPagoAction(Request $request, $id)
    {

        return new Response($this->DetallePagoRender(
            array(
                'id' => $id,
                'from' => 0
            )
        )
        );
    }

    public function imprimirDetallePagoAction()
    {

        $html = $this->DetallePagoRender(array(
                'id' => $this->request('valoridPago'),
                'from' => 1
            )
        );

        return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200,
            array(
                'Content-Type' => 'application/pdf',
            )
        );
    }

    /**
     * Detalles Pago Contructor
     */
    protected function DetallePagoRender($arr)
    {

        $oEmpresa = $this->ObtenerEmpresaLogin();
        $source = ($arr['from'] == 0) ? 'DetallePago' : 'DetallePagoPDF';

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        $detalles = ($estadoApi === 'core') ? $this->rPagoCuenta()->GetDetallePago($arr['id']) : $this->rPagoCuenta()->GetDetallePagoApi1($arr['id']);

        return $this->renderView('RecaudacionBundle:Recaudacion/PostPago/DetallePago:' . $source . '.html.twig',
            array(
                'detalles' => $detalles,
                'medioPagos' => $this->rPagoCuenta()->GetMediosPagoDetallePago($arr['id']),
                'empresa' => $oEmpresa,
                'estadoActivoTalonario' => $this->estado('EstadoBoletaActiva')->getId(),
                'idPago' => $arr['id'],
                'coreApi' => ($estadoApi === 'core') ? 1 : 0,
                'ip' => ($arr['from'] == 1) ? $this->container->get('request_stack')->getCurrentRequest()->getHost() : null,
                'edadBaseDocumento' => ($arr['from'] == 1) ? $this->getSession('edadBaseDocumento') : null,
            )
        );
    }

    public function detallesAtencionAction(Request $request, $id)
    {
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $sFunciones             = $this->get('libreria_funciones');
        $estadoApi = $this->estado('EstadoApi');
        $em = $this->getDoctrine()->getManager();

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }
        $detalles = $this->rPagoCuenta()->getDetalleAtencionNuevo($id);
        $atenciones = $this->rPagoCuenta()->getDetalleAtencionAccionClinica($id);
        $detalles['totalCuenta'] = 0;
        foreach ($atenciones as $atencion) {
            $detalles['totalCuenta'] = $detalles['totalCuenta'] + intval($atencion['precioCobrado']);
        }

        $detalles['totalCuenta'] = floatval($detalles['totalCuenta']);
        $edad = $sFunciones->devuelveEdad($detalles['fechaN']->format('d-m-Y'), $detalles['fechaIngreso']->format('d-m-Y'));
        $detalles['edad'] = intval($edad[0])." años ".intval($edad[1])." meses ".intval($edad[2])." días ";

        if ( $detalles['idTipoAtencion'] === $this->container->getParameter('ambulatoria') && is_null($detalles['idSucursal'])) {
            $oPagoCuenta = $em->getRepository('RebsolHermesBundle:PagoCuenta')->findOneBy(array('idPaciente' => $id));

            $detalles['nombreSucursal'] = $oPagoCuenta->getIdCaja()->getIdSucursal()->getNombreSucursal() ? $oPagoCuenta->getIdCaja()->getIdSucursal()->getNombreSucursal(): '';
            $detalles['direccionSucursal'] = $oPagoCuenta->getIdCaja()->getIdSucursal()->getDireccionSucursal() ? $oPagoCuenta->getIdCaja()->getIdSucursal()->getDireccionSucursal(): '';
            $detalles['telefonoFijo'] = $oPagoCuenta->getIdCaja()->getIdSucursal()->getTelefonoFijo() ? $oPagoCuenta->getIdCaja()->getIdSucursal()->getTelefonoFijo(): '';
            $detalles['telefonoMovil'] = $oPagoCuenta->getIdCaja()->getIdSucursal()->getTelefonoMovil() ? $oPagoCuenta->getIdCaja()->getIdSucursal()->getTelefonoMovil(): '';
        } else {
            $oSucursal = $em->getRepository('RebsolHermesBundle:Sucursal')->find($detalles['idSucursal']);

            $detalles['nombreSucursal'] = $oSucursal->getNombreSucursal() ? $oSucursal->getNombreSucursal(): '';
            $detalles['direccionSucursal'] = $oSucursal->getDireccionSucursal() ? $oSucursal->getDireccionSucursal(): '';
            $detalles['telefonoFijo'] = $oSucursal->getTelefonoFijo() ? $oSucursal->getTelefonoFijo(): '';
            $detalles['telefonoMovil'] = $oSucursal->getTelefonoMovil() ? $oSucursal->getTelefonoMovil(): '';
        }

        $detalleUsuarios = array();

        if ( $detalles['idTipoAtencion'] === $this->container->getParameter('ambulatoria')) {
            $detalleUsuarios1 = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->obtenerUsuarioAtencion($id);
            $detalleUsuarios2 = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerUsuarioAtencion($id);
            $detalleUsuarios3 = $em->getRepository('RebsolHermesBundle:ConsultaMedicaFc')->obtenerUsuarioAtencion($id);
            $detalleUsuarios = array_merge($detalleUsuarios1,$detalleUsuarios2,$detalleUsuarios3);

        } else {
            $detalleUsuarios1 = $this->get('admision.DatoIngreso')->obtenerUsuarioIngreso($id);
            $detalleUsuarios2 = $em->getRepository('RebsolHermesBundle:Traslado')->obtenerUsuarioIngresoEnfermeria($id);
            $detalleUsuarios3 = $em->getRepository('RebsolHermesBundle:PagoCuenta')->obtenerUsuarioAtencionPago($id);
            $detalleUsuarios = array_merge($detalleUsuarios1,$detalleUsuarios2, array('usuariosPago' => $detalleUsuarios3));

        }

        $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/DetalleAtencion:DetalleAtencion.html.twig', array(
            'detalles' => $detalles,
            'atenciones' => $atenciones,
            'atencionesA' => $this->rPagoCuenta()->getDetalleAtencionArticulo($id),
            'diferencia' => ($this->rPagoCuenta()->getDetalleDiferencia($id)) ? $this->rPagoCuenta()->getDetalleDiferencia($id) : null,
            'empresa' => $oEmpresa,
            'idPaciente' => $id,
            'coreApi' => ($estadoApi === "core") ? 1 : 0,
            'detalleUsuarios' => $detalleUsuarios,
        )
        );
        return new Response($renderView);
    }

    public function imprimirDetalleAtencionAction()
    {
        $id = $this->request('valoridPaciente');
        $ip = $this->container->get('request_stack')->getCurrentRequest()->getHost();
        $edadBaseDocumento = $this->getSession('edadBaseDocumento');
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $sFunciones             = $this->get('libreria_funciones');
        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }
        $detalles = ($estadoApi === "core") ? $this->rPagoCuenta()->getDetalleAtencion($id) : $this->rPagoCuenta()->getDetalleAtencionApi1($id);
        $atenciones = $this->rPagoCuenta()->getDetalleAtencionAccionClinica($id);
        $detalles['totalCuenta'] = 0;
        foreach ($atenciones as $atencion) {
            $detalles['totalCuenta'] = $detalles['totalCuenta'] + intval($atencion['precioCobrado']);
        }
        $detalles['totalCuenta'] = floatval($detalles['totalCuenta']);
        $edad = $sFunciones->devuelveEdad($detalles['fechaN']->format('d-m-Y'), $detalles['fechaIngreso']->format('d-m-Y'));
        $detalles['edad'] = intval($edad[0])." años ".intval($edad[1])." meses ".intval($edad[2])." días ";

        $html = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/DetalleAtencion:DetalleAtencionPDF.html.twig', array(
            'detalles' => $detalles,
            'atenciones' => $atenciones,
            'atencionesA' => $this->rPagoCuenta()->getDetalleAtencionArticulo($id),
            'diferencia' => ($this->rPagoCuenta()->getDetalleDiferencia($id)) ? $this->rPagoCuenta()->getDetalleDiferencia($id) : null,
            'empresa' => $oEmpresa,
            'edadBaseDocumento' => $edadBaseDocumento,
            'ip' => $ip,
            'coreApi' => ($estadoApi === "core") ? 1 : 0));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
                'Content-Type' => 'application/pdf',
            )
        );
    }

    /* Detalles Boleta */
    public function detallesBoletaAction(Request $request, $id)
    {
        $arrayOptions = array();
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $rPagoCuenta = $this->rPagoCuenta();

        $detallesBoleta = null;
        $arrayOptions['idPaciente'] = (int)$id;
        $arrayOptions['estadoBoletaActiva'] = $this->estado('EstadoBoletaActiva');

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        if ($estadoApi === 'core') {
            $detallesBoleta = $rPagoCuenta->GetDetalleBoleta($arrayOptions);
        } else {
            $detallesBoleta = $rPagoCuenta->GetDetalleBoletaApi1($arrayOptions['idPaciente'], $arrayOptions['estadoBoletaActiva']);
        }

        if (count($detallesBoleta) > 1) {
            $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:Boletas.html.twig', array(
                    'detalles' => $detallesBoleta,
                    'empresa' => $oEmpresa,
                    'edicion' => 0,
                    'coreApi' => ($estadoApi === 'core') ? 1 : 0
                )
            );
        } else {
            $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:Boleta.html.twig', array(
                    'detalles' => $detallesBoleta = array_shift($detallesBoleta),
                    'empresa' => $oEmpresa,
                    'edicion' => 0,
                    'coreApi' => ($estadoApi === 'core') ? 1 : 0
                )
            );
        }

        return new Response($renderView);
    }

    public function imprimirDetalleBoletaAction()
    {

        $id = $this->request('valoridBoleta');
        $ip = $this->container->get('request_stack')->getCurrentRequest()->getHost();
        $oEmpresa = $this->ObtenerEmpresaLogin();

        // echo "<pre>"; \Doctrine\Common\Util\Debug::dump(get_class($this->rPagoCuenta())); exit(-1);

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        if (($estadoApi === 'core')) {

            $html = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:BoletaPDF.html.twig',
                array(
                    'detalles' => $this->rPagoCuenta()->GetDetalleBoletaPrint($id),
                    'empresa' => $oEmpresa,
                    'ip' => $ip,
                    'coreApi' => ($estadoApi === 'core') ? 1 : 0
                )
            );

            return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html,
                array(
                    // 'orientation'  => 'Landscape'
                    'lowquality' => false
                , 'page-width' => 1000
                , 'zoom' => '.7'
                , 'page-size' => 'letter'
                )
            ), 200,
                array(
                    'Content-Type' => 'application/pdf'
                )
            );

        } else {
            $detalles = $this->rPagoCuenta()->GetDetalleBoletaPrintApi1($id);

            $total = intval($detalles['monto']);
            $con_letra = strtoupper($this->ValorEnLetras($total, "pesos"));
            $conLetrasSaltolinea = wordwrap($con_letra, 40, "<br />\n");

            $html = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:BoletaPDFUNAB.html.twig', array(
                'detalles' => $detalles,
                'empresa' => $oEmpresa,
                'ip' => $ip,
                'montoLetras' => $conLetrasSaltolinea,
                'atenciones' => $this->rPagoCuenta()->getDetalleAtencionAccionClinica($detalles['p']),
                'atencionesA' => $this->rPagoCuenta()->getDetalleAtencionArticulo($detalles['p']),
                'coreApi' => ($estadoApi === "core") ? 1 : 0
            ));
            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
                    'Content-Type' => 'application/pdf',
                )
            );
        }
    }

    public function postPagoBoletaAction()
    {
        $detallesBoleta = array();
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $arrayOptions['idPaciente'] = (int)$this->getSession('PacienteBloteas');
        $arrayOptions['estadoBoletaActiva'] = $this->estado('EstadoBoletaActiva');

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        if ($estadoApi === 'core') {
            $detallesBoleta = $this->rPagoCuenta()->GetDetalleBoleta($arrayOptions);
        } else {
            $detallesBoleta = $this->rPagoCuenta()->GetDetalleBoletaApi1($arrayOptions['idPaciente'], $arrayOptions['estadoBoletaActiva']);
        }

        if (count($detallesBoleta) > 1) {
            $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:Boletas.html.twig', array(
                    'detalles' => $detallesBoleta,
                    'empresa' => $oEmpresa,
                    'edicion' => 1,
                    'coreApi' => ($estadoApi === 'core') ? 1 : 0
                )
            );
        } else {
            $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:Boleta.html.twig', array(
                    'detalles' => $detallesBoleta = array_shift($detallesBoleta),
                    'empresa' => $oEmpresa,
                    'edicion' => 1,
                    'coreApi' => ($estadoApi === 'core') ? 1 : 0
                )
            );
        }

        return new Response($renderView);
    }

    public function modificaBoletaManualAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = (int)$this->ajax('idDetalleTalonario');
        $numeroBoleta = (int)$this->ajax('numero');
        $oDetalleTalonario = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->find($id);

        if ($oDetalleTalonario) {
            $oDetalleTalonario->setNumeroDocumento($numeroBoleta);
            $em->persist($oDetalleTalonario);
            $em->flush();
            $r = $numeroBoleta;
        } else {
            $r = "no";
        }

        return new Response(json_encode($r));
    }

    public function ModificaBoletaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $oDetalleTalonario = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->find($this->ajax('id'));
        $fecha = new \DateTime("now");
        $oUser = $this->getUser();
        $monto = intval($oDetalleTalonario->getMonto());
        $oPaciente = $oDetalleTalonario->getidPaciente();
        $oCaja = $oDetalleTalonario->getidCaja();
        $oTalonario = $oDetalleTalonario->getIdTalonario();
        $oUsuario = $oDetalleTalonario->getIdUsuarioDetalleBoleta();
        $oPagoCuenta = $oDetalleTalonario->getidPagoCuenta();

        /////////////////////// MODIFICA ANTIGUA BOLETA/////////////////////////////////
        $oDetalleTalonario->setFechaAnulacion($fecha);
        $oDetalleTalonario->setIdEstadoDetalleTalonario($this->estado('EstadoBoletaAnulada'));
        $oDetalleTalonario->setIdUsuarioAnulacion($oUser);
        $em->persist($oDetalleTalonario);

        //////////////////////CREA NUEVA BOLETA//////////////////////////////////////////////
        $oDetalleTalonarioNueva = new DetalleTalonario();
        $oDetalleTalonarioNueva->setNumeroDocumento($oTalonario->getNumeroActual());
        $oDetalleTalonarioNueva->setMonto($monto);
        $oDetalleTalonarioNueva->setFechaDetalleBoleta($fecha);
        $oDetalleTalonarioNueva->setIdPaciente($oPaciente);
        $oDetalleTalonarioNueva->setIdCaja($oCaja);
        $oDetalleTalonarioNueva->setIdTalonario($oTalonario);
        $oDetalleTalonarioNueva->setIdEstadoDetalleTalonario($this->estado('EstadoBoletaActiva'));
        $oDetalleTalonarioNueva->setIdUsuarioDetalleBoleta($oUsuario);
        $oDetalleTalonarioNueva->setIdPagoCuenta($oPagoCuenta);
        $em->persist($oDetalleTalonarioNueva);

        /////////////////////MODIFICA CORRELATIVO TALONARIO///////////////////
        $oTalonario->setNumeroActual($oTalonario->getNumeroActual() + 1);
        $em->persist($oTalonario);

        ///////////////////////GENERA CAMBIOS/////////////////////////////////////////////////
        $em->flush();

        ///////////////////////CAMBIA CORRELATIVO/////////////////////////////////////////////////
        $boleta = $oDetalleTalonarioNueva->getid();
        $this->get('session')->set('detalletalonario', $boleta);

        //////////////////// GENERA VARIABLE DE SESSION DE TALONARIO ///////////////
        //             $this->get('session')->set('idTalonario', $oTalonario->getId());

        //////////////////RESULTADO////////////////////////
        return new Response(json_encode($oDetalleTalonarioNueva->getNumeroDocumento()));
    }

    ////// Documentos Pago
    public function documentosPostPagoAction()
    {
        $id = $this->getSession('PacienteBloteas');
        $oEmpresa = $this->ObtenerEmpresaLogin();

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }
        $arrayOptions = array('idPaciente' => $id,
            'estadoBoletaActiva' => $this->estado('EstadoBoletaActiva')
        );

        $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:Boletas.html.twig'
            , array('detalles' => $detallesBoleta = ($estadoApi === "core") ? $this->rPagoCuenta()->GetDetalleBoleta($arrayOptions) : $this->rPagoCuenta()->GetDetalleBoletaApi1($id, $this->estado('EstadoBoletaActiva')),
                'empresa' => $oEmpresa,
                'edicion' => 1,
                'coreApi' => ($estadoApi === "core") ? 1 : 0));
        return new Response($renderView);
    }

    ////// Otras Funciones Menores
    public function verificaCorrelativoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $x = 0;
        $idTalonario = $this->getSession('idTalonario');
        if ($idTalonario) {
            for ($i = 0; $i < count($idTalonario); $i++) {
                $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario[$i]);
                $x = ($oTalonario) ? ($oTalonario->getnumeroActual() >= $oTalonario->getnumeroTermino()) ? $x++ : $x : $x;
            }
            $validaTalonario = (count($idTalonario) == $x) ? "no" : "ok";
            return new Response(json_encode($validaTalonario));
        } else {
            $this->get('session')->remove('idTalonario');
            $validaTalonario = "no";
            return new Response(json_encode($validaTalonario));
        }
    }

    public function guardaFechaEnBaseDocumentoAction()
    {
        $this->setSession('edadBaseDocumento', $this->ajax('edad'));
        return new Response("ok");
    }

    var $Void = "";
    var $SP = " ";
    var $Dot = ".";
    var $Zero = "0";
    var $Neg = "Menos";

    function ValorEnLetras($x, $Moneda)
    {
        $s = "";
        $Ent = "";
        $Frc = "";
        $Signo = "";

        if (floatVal($x) < 0)
            $Signo = $this->Neg . " ";
        else
            $Signo = "";

        if (intval(number_format($x, 2, '.', '')) != $x)
            $s = number_format($x, 2, '.', '');
        else
            $s = number_format($x, 2, '.', '');

        $Pto = strpos($s, $this->Dot);

        if ($Pto === false) {
            $Ent = $s;
            $Frc = $this->Void;
        } else {
            $Ent = substr($s, 0, $Pto);
            $Frc = substr($s, $Pto + 1);
        }

        if ($Ent == $this->Zero || $Ent == $this->Void)
            $s = "Cero ";
        elseif (strlen($Ent) > 7) {
            $s = $this->SubValLetra(intval(substr($Ent, 0, strlen($Ent) - 6))) .
                "Millones " . $this->SubValLetra(intval(substr($Ent, -6, 6)));
        } else {
            $s = $this->SubValLetra(intval($Ent));
        }

        if (substr($s, -9, 9) == "Millones " || substr($s, -7, 7) == "Millón ")
            $s = $s . "de ";

        $s = $s . $Moneda;

        if ($Frc != $this->Void) {
            $s = $s;
        }
        $letrass = $Signo . $s;
        return ($letrass);
    }

    function SubValLetra($numero)
    {
        $Ptr = "";
        $n = 0;
        $i = 0;
        $x = "";
        $Rtn = "";
        $Tem = "";

        $x = trim("$numero");
        $n = strlen($x);

        $Tem = $this->Void;
        $i = $n;

        while ($i > 0) {
            $Tem = $this->Parte(intval(substr($x, $n - $i, 1) .
                str_repeat($this->Zero, $i - 1)));
            if ($Tem != "Cero")
                $Rtn .= $Tem . $this->SP;
            $i = $i - 1;
        }

        $Rtn = str_replace(" Mil Mil", " Un Mil", $Rtn);
        while (1) {
            $Ptr = strpos($Rtn, "Mil ");
            if (!($Ptr === false)) {
                if (!(strpos($Rtn, "Mil ", $Ptr + 1) === false))
                    $this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
                else
                    break;
            } else break;
        }

        $Ptr = -1;
        do {
            $Ptr = strpos($Rtn, "Cien ", $Ptr + 1);
            if (!($Ptr === false)) {
                $Tem = substr($Rtn, $Ptr + 5, 1);
                if ($Tem == "M" || $Tem == $this->Void)
                    ;
                else
                    $this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
            }
        } while (!($Ptr === false));

        $Rtn = str_replace("Diez Un", "Once", $Rtn);
        $Rtn = str_replace("Diez Dos", "Doce", $Rtn);
        $Rtn = str_replace("Diez Tres", "Trece", $Rtn);
        $Rtn = str_replace("Diez Cuatro", "Catorce", $Rtn);
        $Rtn = str_replace("Diez Cinco", "Quince", $Rtn);
        $Rtn = str_replace("Diez Seis", "Dieciseis", $Rtn);
        $Rtn = str_replace("Diez Siete", "Diecisiete", $Rtn);
        $Rtn = str_replace("Diez Ocho", "Dieciocho", $Rtn);
        $Rtn = str_replace("Diez Nueve", "Diecinueve", $Rtn);
        $Rtn = str_replace("Veinte Un", "Veintiun", $Rtn);
        $Rtn = str_replace("Veinte Dos", "Veintidos", $Rtn);
        $Rtn = str_replace("Veinte Tres", "Veintitres", $Rtn);
        $Rtn = str_replace("Veinte Cuatro", "Veinticuatro", $Rtn);
        $Rtn = str_replace("Veinte Cinco", "Veinticinco", $Rtn);
        $Rtn = str_replace("Veinte Seis", "Veintiseís", $Rtn);
        $Rtn = str_replace("Veinte Siete", "Veintisiete", $Rtn);
        $Rtn = str_replace("Veinte Ocho", "Veintiocho", $Rtn);
        $Rtn = str_replace("Veinte Nueve", "Veintinueve", $Rtn);

        if (substr($Rtn, 0, 1) == "M") $Rtn = "Un " . $Rtn;
        for ($i = 65; $i <= 88; $i++) {
            if ($i != 77)
                $Rtn = str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
        }
        $Rtn = str_replace("*", "a", $Rtn);
        return ($Rtn);
    }

    function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
    {
        $x = substr($x, 0, $Ptr) . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
    }

    function Parte($x)
    {
        $Rtn = '';
        $t = '';
        $i = '';
        do {
            switch ($x) {
                case 0:
                    $t = "Cero";
                    break;
                case 1:
                    $t = "Un";
                    break;
                case 2:
                    $t = "Dos";
                    break;
                case 3:
                    $t = "Tres";
                    break;
                case 4:
                    $t = "Cuatro";
                    break;
                case 5:
                    $t = "Cinco";
                    break;
                case 6:
                    $t = "Seis";
                    break;
                case 7:
                    $t = "Siete";
                    break;
                case 8:
                    $t = "Ocho";
                    break;
                case 9:
                    $t = "Nueve";
                    break;
                case 10:
                    $t = "Diez";
                    break;
                case 20:
                    $t = "Veinte";
                    break;
                case 30:
                    $t = "Treinta";
                    break;
                case 40:
                    $t = "Cuarenta";
                    break;
                case 50:
                    $t = "Cincuenta";
                    break;
                case 60:
                    $t = "Sesenta";
                    break;
                case 70:
                    $t = "Setenta";
                    break;
                case 80:
                    $t = "Ochenta";
                    break;
                case 90:
                    $t = "Noventa";
                    break;
                case 100:
                    $t = "Cien";
                    break;
                case 200:
                    $t = "Doscientos";
                    break;
                case 300:
                    $t = "Trescientos";
                    break;
                case 400:
                    $t = "Cuatrocientos";
                    break;
                case 500:
                    $t = "Quinientos";
                    break;
                case 600:
                    $t = "Seiscientos";
                    break;
                case 700:
                    $t = "Setecientos";
                    break;
                case 800:
                    $t = "Ochocientos";
                    break;
                case 900:
                    $t = "Novecientos";
                    break;
                case 1000:
                    $t = "Mil";
                    break;
                case 1000000:
                    $t = "Millón";
                    break;
            }

            if ($t == $this->Void) {
                $i = $i + 1;
                $x = $x / 1000;
                if ($x == 0) $i = 0;
            } else
                break;

        } while ($i != 0);

        $Rtn = $t;
        switch ($i) {
            case 0:
                $t = $this->Void;
                break;
            case 1:
                $t = " Mil";
                break;
            case 2:
                $t = " Millones";
                break;
            case 3:
                $t = " Billones";
                break;
        }
        return ($Rtn . $t);
    }

    /**
     * [subirPDFAction description]
     * @param Request $request [description]
     * @return [type]           [description]
     */
    public function subirPDFAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $idPaciente = $request->get('idPaciente');

        if (0 < $_FILES['archivo']['error']) {
            return new Response(json_encode(array('error' => 1)));
        }

        $rutaTemporal = $_FILES['archivo']['tmp_name'];
        $nombreOriginalArchivo = $_FILES['archivo']['name'];
        $formatoArchivo = explode('.', $nombreOriginalArchivo);
        $extensionArchivo = end($formatoArchivo);

        /**
         * Subir Ficha Pnatural
         */
        $nombreArchivo = uniqid('orden_examen_' . $idPaciente . '_') . '.' . $extensionArchivo;
        $rutaArchivo = $rutaTemporal;

        $servidorArchivos = $this->get('hermesTools.ServidorArchivos');
        $servidorArchivos->subirRecursoEmpresa([
            'rutaArchivo' => $this->container->getParameter('hermes.caja.orden_examen'),
            'nombreArchivo' => $nombreArchivo,
            'rutaServidor' => $rutaArchivo,
        ]);

        $oPaciente = $em->getRepository("RebsolHermesBundle:Paciente")->find($idPaciente);
        $oPaciente->setOrdenExamen($nombreArchivo);
        $em->persist($oPaciente);
        $em->flush();
        return new Response(json_encode(array('error' => 0)));
    }

    public function eliminarPDFAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $idPaciente = $request->get('idPaciente');
        $ruta = $this->get('kernel')->getRootDir() . '/../web/uploads/caja/ordenExamen/';
        $oPaciente = $em->getRepository("RebsolHermesBundle:Paciente")->find($idPaciente);

        $servidorArchivos = $this->get('hermesTools.ServidorArchivos');
        /*$bEliminar = $servidorArchivos->eliminarRecursoEmpresa([
            'rutaArchivo' => $this->container->getParameter('hermes.caja.orden_examen'),
            'nombreArchivo' => $oPaciente->getOrdenExamen(),
        ]);*/

        $recurso = $this->container->getParameter('hermes.caja.orden_examen') . $oPaciente->getOrdenExamen();
        $bEliminar = $servidorArchivos->obtenerRecursoEmpresa($recurso, true);

        if (!$bEliminar) {
            return new Response(json_encode(array('error' => 1)));
        } else {
            $oPaciente->setOrdenExamen(null);
            $em->persist($oPaciente);
            $em->flush();
            return new Response(json_encode(array('error' => 0)));
        }
    }

    public function abrirPDFAction()
    {
        $em = $this->getDoctrine()->getManager();
        $idPaciente = $this->ajax('idPaciente');
        $oPaciente = $em->getRepository("RebsolHermesBundle:Paciente")->find($idPaciente);

        if (!$oPaciente->getOrdenExamen()) {
            return new Response(json_encode(array('error' => 1)));
        } else {
            $servidorArchivos = $this->get('hermesTools.ServidorArchivos');

            $recurso = $this->container->getParameter('hermes.caja.orden_examen') . $oPaciente->getOrdenExamen();

            $path = $servidorArchivos->obtenerRecursoEmpresa($recurso, true);

            return new Response(json_encode(array('error' => 0, 'url' => $path)));
        }
    }

    private function quitarTildes($string)
    {
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('E', 'E', 'E', 'E', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('I', 'I', 'I', 'I', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('O', 'O', 'O', 'O', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(" ", '', $string);


        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('U', 'U', 'U', 'U', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('Ñ', 'Ñ', 'c', 'C',),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                "."),
            '',
            $string
        );
        return $string;
    }

    public function reenviarDteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $oPagoCuentaDetalle = $em->getRepository('RebsolHermesBundle:PagoCuentaDetalle')
            ->find($this->container->get('request_stack')->getCurrentRequest()->query->get('idPagoCuentaDetalle'));
        $oPagoCuenta = $em->getRepository('RebsolHermesBundle:PagoCuenta')
            ->find($oPagoCuentaDetalle->getIdPagoCuenta());
        $dataPendiente = unserialize($oPagoCuentaDetalle->getDataPendiente());
        $idEmpresa = $this->container->get('request_stack')->getCurrentRequest()->query->get('idEmpresa');

        $aces = $em->getRepository('RebsolHermesBundle:Parametro')
            ->obtenerParametrosAces($idEmpresa);

        $dataPendiente['u'] = $aces[ConexionAcesConstant::ACES_NOMBRE_USUARIO];
        $dataPendiente['p'] = $aces[ConexionAcesConstant::ACES_CLAVE_USUARIO];
        $dataPendiente['apikey'] = $aces[ConexionAcesConstant::ACES_API_KEY];

        $dteService = $this->get('dteAcesService');
        $boletas = $dteService->reenviarDte($dataPendiente, $idEmpresa);

        $this->actualizarPagoCuentaDetalle($boletas['boleta'], $oPagoCuenta, $oPagoCuentaDetalle);

        if ($boletas['boleta'][0]['detalle']['error'] !== '') {
            return new JsonResponse(array(
                'success' => false,
                'mensaje' => $boletas['boleta'][0]['detalle']['error'],
                'urlDte' => ''
            ));
        } else {
            return new JsonResponse(array(
                'success' => true,
                'mensaje' => 'Documento Electrónico emitido exitosamente',
                'urlDte' => $boletas['boleta'][0]['urlDte']
            ));
        }
    }

    public function actualizarPagoCuentaDetalle($boletas, $oPagoCuenta, $oPagoCuentaDetalle)
    {
        $em = $this->getDoctrine()->getManager();
        $boleta_error = false;
        foreach ($boletas as $boleta) {
            if ($boleta['detalle']['error'] !== '') {
                $oPagoCuentaDetalle->setEnviadoDte(false);
                $oPagoCuentaDetalle->setDetalleDte($boleta['detalle']);
                ($boleta['dataPendiente'] !== '') ? $oPagoCuentaDetalle->setDataPendiente(serialize($boleta['dataPendiente'])) : $oPagoCuentaDetalle->setDataPendiente('');
                $boleta_error = true;
            } else {
                $oPagoCuentaDetalle->setEnviadoDte(true);
                $oPagoCuentaDetalle->setUrlDte($boleta['urlDte']);
                $oPagoCuentaDetalle->setUrlProdDte($boleta['urlProdDte']);
                $oPagoCuentaDetalle->setDataPendiente(null);
                $oPagoCuentaDetalle->setDetalleDte(null);
            }
            $oPagoCuentaDetalle->setIdPagoCuenta($oPagoCuenta);
            $em->persist($oPagoCuentaDetalle);
        }
        $em->flush();
        return $boleta_error;

    }

    public function postPagoBoletaDteAction(Request $request)
    {
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $boletasDTE = $request->get("boletasDTE");

        $em = $this->getDoctrine()->getManager();
        $oPagoCuentaDetalles = $em->getRepository('RebsolHermesBundle:PagoCuentaDetalle')
            ->findBy(array('idPagoCuenta' => intval($boletasDTE['idPago'])));

        $individual = array();
        $i = 0;
        $envioProd = false;
        foreach ((array)$oPagoCuentaDetalles as $pagoCuentaDetalle) {
            //Se verifica servidor de envío como de almacenamiento
            if ($pagoCuentaDetalle->getConsultaUrlProd() !== null || !$pagoCuentaDetalle->getConsultaUrlProd()) {
                if (!$pagoCuentaDetalle->getEnviadoDte()) {
                    $individual['urlDte'][$i] = '';
                    $individual['dataPendiente'][$i] = $pagoCuentaDetalle->getId();
                } elseif ($this->verificarUrl($pagoCuentaDetalle->getUrlDte())) {
                    $individual['urlDte'][$i] = $pagoCuentaDetalle->getUrlDte();
                    $individual['dataPendiente'][$i] = '';
                } elseif ($this->verificarUrl($pagoCuentaDetalle->getUrlProdDte())) {
                    $individual['urlDte'][$i] = $pagoCuentaDetalle->getUrlProdDte();
                    $individual['dataPendiente'][$i] = '';
                    $pagoCuentaDetalle->setConsultaUrlProd(true);
                    $em->persist($pagoCuentaDetalle);
                    $envioProd = true;
                } else {
                    $individual['urlDte'][$i] = '';
                    $individual['dataPendiente'][$i] = '';
                }
            } else {
                if ($this->verificarUrl($pagoCuentaDetalle->getUrlProdDte())) {
                    $individual['urlDte'][$i] = $pagoCuentaDetalle->getUrlDte();
                    $individual['dataPendiente'][$i] = '';
                } else {
                    $individual['urlDte'][$i] = '';
                    $individual['dataPendiente'][$i] = '';
                }
            }

            $i++;
        }
        if($envioProd) $em->flush();
        $individual['evento'] = $boletasDTE['evento'];
        $renderView = $this->renderView('RecaudacionBundle:Recaudacion/PostPago/Boleta:BoletaDte.html.twig', array(
                'individual' => $individual,
                'idEmpresa'  => $oEmpresa->getId(),
            )
        );

        return new Response($renderView);
    }

    public function verificarUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);
        //Si este return no completa en completitud la respuesta desde aces hacer lo de folios con un parámetro de minutos
        return $data !== '-3|Documento no encontrado';

    }


    public function regularizaGarantiaAction(Request $request)
    {
        $idPagoCuenta = $request->request->get('idPagoCuenta');
        $observacionRegularizacion = $request->request->get('observacionRegularizacion');
        $EstadoPagoRegularizada     = $this->estado('EstadoPagoRegularizada');
        $em = $this->getDoctrine()->getManager();
        $oFecha               = new \DateTime('now');
        $oPagoCuenta_Temp = $em->getRepository('RebsolHermesBundle:PagoCuenta')->findOneBy(array('id' => $idPagoCuenta));

        if ($oPagoCuenta_Temp) {
            $oPagoCuenta_Temp->setIdEstadoPago($EstadoPagoRegularizada);
            $oPagoCuenta_Temp->setFechaRegularizacion($oFecha);
            $oPagoCuenta_Temp->setObservacionRegularizacion($observacionRegularizacion);
            $em->persist($oPagoCuenta_Temp);
            try {
                $em->flush();
                return new JsonResponse(array('mensaje' => "regularizado", 'data' => null));
            } catch (\Exception $e) {
                return new JsonResponse(array('mensaje' => 'failure', 'data' => $e->getMessage()));
            }
        }
        return new JsonResponse(array('mensaje' => "failure", 'data' => 'error no existe idPagoCuenta'));
    }
    protected function AnulacionPagoPagoCuenta($arr)
    {
        $em = $this->getDoctrine()->getManager();
        $oUser = $this->getUser();
        $oPagoCuenta = $em->getRepository('RebsolHermesBundle:PagoCuenta')->find($arr['idPagoCuenta']);
        $oPrePagoCuentaDetalle = $em->getRepository('RebsolHermesBundle:PrePagoCuentaDetalle')->findBy(array('idPagoCuenta' => $arr['idPagoCuenta']));
        $oEstadoInactivo = $em->getReference('RebsolHermesBundle:Estado', $this->container->getParameter('Estado.inactivo'));
        $oPaciente = $oPagoCuenta->getIdPaciente();
        $oCuentaPaciente = $em->getRepository('RebsolHermesBundle:CuentaPaciente')->findOneBy(array('idPaciente' => $oPaciente));
        $oDetalleTalonario = $em->getRepository('RebsolHermesBundle:DetalleTalonario')->findBy(array('idPagoCuenta' => $oPagoCuenta));
        $oAccionClinicaPaciente = $em->getRepository('RebsolHermesBundle:AccionClinicaPaciente')->findBy(array('idPagoCuenta' => $oPagoCuenta));
        $oArticuloPaciente = $em->getRepository('RebsolHermesBundle:ArticuloPaciente')->findBy(array('idPagoCuenta' => $oPagoCuenta));

        $oEstadoPago = $this->estado('EstadoPagoAnulada');
        $oEstadoDetalleTalonario = $this->estado('EstadoBoletaAnulada');
        $oFecha = new \DateTime();

        foreach ($oPrePagoCuentaDetalle as $prePagoCuentaDetalle) {
            $prePagoCuentaDetalle->setIdEstado($oEstadoInactivo);
            $prePagoCuentaDetalle->setIdUsuarioAnulacion($oUser);
            $prePagoCuentaDetalle->setFechaAnulacion($oFecha);
            $em->persist($prePagoCuentaDetalle);
        }

        //PAGO CUENTA
        $oPagoCuenta->setIdEstadoPago($oEstadoPago);
        $oPagoCuenta->setIdUsuarioAnulacion($oUser);
        $oPagoCuenta->setFechaAnulacion($oFecha);
        $em->persist($oPagoCuenta);

        //CUENTA PACIENTE
        $arrayCerrada = array(4,5,6,7,8,9,10);
        if ( in_array($oCuentaPaciente->getIdEstadoCuenta()->getId() , $arrayCerrada) ) {
            $oEstadoCuenta = $this->estado('EstadoCerradaRevisionInterna');
        } else {
            $oEstadoCuenta = $this->estado('EstadoAbiertaPendientePago');
        }
        $oCuentaPaciente->setIdEstadoCuenta($oEstadoCuenta);

        $em->persist($oCuentaPaciente);

        //CUENTA PACIENTE LOG
        $oCuentaPacienteLog = new CuentaPacienteLog();
        $evento = $this->rPaciente()->obtenerEventos($oPaciente->getidPNatural());
        $oCuentaPacienteLog->setSaldoCuenta(00.00);
        $oCuentaPacienteLog->setFechaEstadoCuenta($oFecha);
        $oCuentaPacienteLog->setNumeroAccion($evento);
        $oCuentaPacienteLog->setIdCuenta($oCuentaPaciente);
        $oCuentaPacienteLog->setIdEstadoCuenta($oEstadoCuenta);
        $oCuentaPacienteLog->setIdUsuario($oUser);
        $oCuentaPacienteLog->setIdPaciente($oPaciente);
        $em->persist($oCuentaPacienteLog);

        foreach ($oAccionClinicaPaciente as $accionClinicaPaciente) {
            $accionClinicaPacienteClone = clone ($accionClinicaPaciente);
            $log = new AccionClinicaPacienteLog();
            $oAccionClinicaPacienteLog = $this->setCloneObjectRecord($accionClinicaPacienteClone, $log);
            $oAccionClinicaPacienteLog->setId(null);
            $oAccionClinicaPacienteLog->setIdAccionClinicaPaciente($accionClinicaPacienteClone->getId());

            $accionClinicaPaciente->setPorcentajeDescuento(null);
            $accionClinicaPaciente->setTotalDescuento(null);
            $accionClinicaPaciente->setIdEstadoPago(null);
            $accionClinicaPaciente->setIdPagoCuenta(null);
            $accionClinicaPaciente->setMontoAfecto(null);
            $accionClinicaPaciente->setMontoExento(null);
            $accionClinicaPaciente->setMontoAfectoSinIva(null);
            $accionClinicaPaciente->setMontoIva(null);
            $accionClinicaPaciente->setPrecioDiferencia(null);
            $accionClinicaPaciente->setIdMotivoDiferencia(null);
            $em->persist($oAccionClinicaPacienteLog);
            $em->persist($accionClinicaPaciente);
        }

        foreach ($oArticuloPaciente as $articuloPaciente) {
            $articuloPacienteClone = clone ($articuloPaciente);
            $log = new ArticuloPacienteLog();
            $oArticuloPacienteLog = $this->setCloneObjectRecord($articuloPacienteClone, $log);
            $oArticuloPacienteLog->setId(null);
            $oArticuloPacienteLog->setIdArticuloPaciente($articuloPacienteClone->getId());

            $articuloPaciente->setPorcentajeDescuento(null);
            $articuloPaciente->setTotalDescuento(null);
            $articuloPaciente->setIdEstadoPago(null);
            $articuloPaciente->setIdPagoCuenta(null);
            $em->persist($oArticuloPacienteLog);
            $em->persist($articuloPaciente);
        }

        if ($oDetalleTalonario) {
            foreach ($oDetalleTalonario as $b) {
                $b->setIdEstadoDetalleTalonario($oEstadoDetalleTalonario);
                $em->persist($b);
            }
        }

        $evitaAnulacionImed = $em->getRepository("RebsolHermesBundle:Parametro")->obtenerParametro('EVITAR_ANULACION_EN_IMED');
        $evitaAnulacionImed = $evitaAnulacionImed['valor'] === '1';

        if (!$evitaAnulacionImed) {
            $ointerfazImed = $em->getRepository('RebsolHermesBundle:InterfazImed')->findOneBy(array('idPagoCuenta' => $oPagoCuenta->getId()));
            if ($ointerfazImed) {
                $Var = $em->getRepository('RebsolHermesBundle:PagoCuenta')->SetGlobalsVar($this->ObtenerEmpresaLogin(), $this->getUser());
                $arrayUnserialize = unserialize($ointerfazImed->getListaBonos());
                foreach ($arrayUnserialize as $bono) {
                    $client = new \nusoap_client($Var['IMED_WS'], true);
                    $param = array(
                        'CodUsuario' => $ointerfazImed->getCodUsuario(),
                        'CodClave' => $ointerfazImed->getCodClave(),
                        'CodFinanciador' => $ointerfazImed->getCodFinanciador(),
                        'CodLugar' => $ointerfazImed->getCodLugar(),
                        'FolioBono' => $bono['FolioBono'],
                        'RutCajero' => $ointerfazImed->getRutCajero()
                    );
                    $answerResult = $client->call('AnulBonInterfaz', $param);
                    if ($answerResult['CodError'] == 1) {
                        return new Response(json_encode(array(
                            'motive' => $answerResult['GloError'],
                            'done' => 0)));
                    }
                }
                $ointerfazImed->setEstado(1);
                $em->persist($ointerfazImed);
            }
        }

        $arrOrdenes = $em->getRepository('RebsolHermesBundle:Orden')->findBy(
            array(
                'idPagoCuenta' => $oPagoCuenta->getId()
            )
        );

        foreach ($arrOrdenes as $oOrden) {
            $oEntrada = $em->getRepository('RebsolHermesBundle:Entrada')->findOneBy(
                array(
                    'idOrden' => $oOrden->getId()
                )
            );
            if (!is_null($oEntrada)) {
                $oEntrada->setEstadoOrden('CA');
                $oEntrada->setFechaAnulacion($oFecha->format("YmdHis"));
                $oEntrada->setEstadoLecturaOrden(0);
                $oOrden->setFechaAnulacion($oFecha->format("YmdHis"));
                $oOrden->setIdUsuarioAnulacion($this->getUser()->getId());
                $em->persist($oEntrada);
                $em->persist($oOrden);
            }
        }

        try {
            $em->flush();
            return new Response(json_encode(array(
                'motive' => 'Anulado Correctamente',
                'done' => 1)));
        } catch (\Exception $e) {
            return new Response(json_encode(array(
                'motive' => 'Ocurrió un error durante la anulación',
                'error' => $e->getMessage(),
                'done' => 0)));
        }

    }

    private function setCloneObjectRecord($clon, $log)
    {
        $clonReflection = new \ReflectionObject($clon);
        $logReflection = new \ReflectionObject($log);

        foreach ($clonReflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($clon);

            if ($logReflection->hasProperty($property->getName())) {
                $logProp = $logReflection->getProperty($property->getName());
                $logProp->setAccessible(true);
                $logProp->setValue($log, $value);
            }
        }
        return $log;
    }

    private function setDetalleTratamiento($a)
    {
        $oDetalleTratamiento = $a->getIdDetalleTratamiento();
        $oDetalleTratamiento->setCantidadPagada($oDetalleTratamiento->getCantidadPagada() - $a->getCantidad());
        $oDetalleTratamiento->setCantidadRealizada($oDetalleTratamiento->getCantidadRealizada() - $a->getCantidad());
        return $oDetalleTratamiento;
    }

    private function setTratamiento($oTratamiento)
    {
        $oTratamiento->setIdEstado($this->estado('EstadoTratamientoEnProceso'));
        return $oTratamiento;
    }
}
