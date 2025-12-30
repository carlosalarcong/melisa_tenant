<?php

namespace App\Controller\Caja\Recaudacion\Pago;


use Rebsol\DteBundle\Services\Dte\ConexionAdecom\Constant\ConexionAdecomConstant;
use Rebsol\DteBundle\Services\DteAces\ConexionAces\Constant\ConexionAcesConstant;
use App\Entity\AccionClinicaPaciente;
use App\Entity\CuentaPaciente;
use App\Entity\CuentaPacienteLog;
use App\Entity\DerivadorExterno;
use App\Entity\DetalleDocumentoPago;
use App\Entity\DetallePagoCuenta;
use App\Entity\DetalleTalonario;
use App\Entity\DocumentoPago;
use App\Entity\Paciente;
use App\Entity\PagoCuenta;
use App\Entity\PagoCuentaDetalle;
use App\Entity\PrevisionPnatural;
use App\Entity\ReservaAtencionLog;
use App\Controller\Caja\RecaudacionController;
use App\Form\Recaudacion\Pago\MediosPagoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 17/01/2014
 * participantes: sDelgado
 */
class PagarController extends RecaudacionController
{

    private function clearSesionVar()
    {
        //nuevos
        $this->killSession('idInterfazImed');
        $this->killSession('api');
        $this->killSession('pacienteApi');
        $this->killSession('persona');
        $this->killSession('garantia');
        $this->killSession('ListaTratamiento');
        $this->killSession('ListaDiferencia');
        $this->killSession('ListaDiferenciaSaldo');
        $this->killSession('idSubEmpresaItem');
        $this->killSession('idDiferenciaSaldo');

        $this->killSession('idDiferencia');
        $this->killSession('derivadoRutExt');


        //antiguos
        $this->killSession('sucursal');
        $this->killSession('financiador');
        $this->killSession('convenio');
        $this->killSession('plan');
        $this->killSession('origen');
        $this->killSession('derivadoInt');
        $this->killSession('derivadoExt');
        $this->killSession('ListaPrestacion');
        $this->killSession('caja');
        $this->killSession('vSumaCantidad');
        $this->killSession('idReservaAtencion');
        $this->killSession('idPacienteGarantia');
        $this->killSession('tipoPago');
        $this->killSession('idPrePagoCuenta');

        return true;
    }

    /**
     * @deprecated [<5.*>] [<description>]
     */
    public function personaAction(Request $request)
    {

        $arrayRequest = $request->query->all();
        $arrayRequest['idEmpresa'] = $this->ObtenerEmpresaLogin()->getId();

        if ($arrayRequest['tipoDocumentoExtranjero'] == 1) {
            $arrayRequest['documentoPorDefecto'] = str_replace('.', '', $arrayRequest['documentoPorDefecto']);
        }

        $idPnatural = $this->rPagoCuenta()->GetPNaturalByRut($arrayRequest);

        return new Response(json_encode($idPnatural));
    }

    public function setSessionCajaPagarAction()
    {

        $idPaciente = $this->container->get('request_stack')->getCurrentRequest()->get('idPaciente');
        $pagoCuentaFarmacia = $this->container->get('request_stack')->getCurrentRequest()->get('pagoCuentaFarmacia');
        $formulariotypeCantidadArticulos = $this->container->get('request_stack')->getCurrentRequest()->get('formulariotypeCantidadArticulos');
        $idPrevision = $this->container->get('request_stack')->getCurrentRequest()->get('idPrevision');
        $idConvenio = $this->container->get('request_stack')->getCurrentRequest()->get('idConvenio');
        $inputIdDatoIngreso = $this->container->get('request_stack')->getCurrentRequest()->query->get('inputIdDatoIngreso');
        $inputMonto = $this->container->get('request_stack')->getCurrentRequest()->query->get('inputMonto');
        $tipoPago = $this->container->get('request_stack')->getCurrentRequest()->query->get('tipoPago');
        $idPrePagoCuenta = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPrePagoCuenta');

        if ($inputIdDatoIngreso !== null) {
            $this->setSession('caja.inputIdDatoIngreso', $inputIdDatoIngreso);
        }

        if ($inputMonto !== null) {
            $this->setSession('caja.inputMonto', $inputMonto);
        }

        if ($idPaciente !== null) {
            $this->setSession('caja.idPaciente', $idPaciente);
        }

        if ($pagoCuentaFarmacia !== null) {
            $this->setSession('caja.totalPago', $pagoCuentaFarmacia);
        }

        if ($idPrevision !== null) {
            $this->setSession('caja.idPrevision', $idPrevision);
        }

        if ($idConvenio !== null) {
            $this->setSession('caja.idConvenio', $idConvenio);
        }

        if ($formulariotypeCantidadArticulos !== null) {
            $this->setSession('caja.cantidadArticulos', $formulariotypeCantidadArticulos);
        }

        if ($tipoPago !== null) {
            $this->setSession('tipoPago', $tipoPago);
        }

        if ($idPrePagoCuenta !== null) {
            $this->setSession('idPrePagoCuenta', $idPrePagoCuenta);
        }

        $idPnatural = $this->ajax('idPnatural');
        $this->setSession('persona', $idPnatural);

        $sucursal = $this->ajax('sucursal');
        $this->setSession('sucursal', $sucursal);

        $financiador = $this->ajax('financiador');
        $this->setSession('financiador', $financiador);

        $convenio = $this->ajax('convenio');
        $this->setSession('convenio', $convenio);

        $plan = $this->ajax('plan');
        $this->setSession('plan', $plan);

        $origen = $this->ajax('origen');
        $this->setSession('origen', $origen);

        $derivadoInt = $this->ajax('derivadoInt');
        $this->setSession('derivadoInt', $derivadoInt);

        $derivadoExt = $this->ajax('derivadoExt');
        $this->setSession('derivadoExt', $derivadoExt);
        $derivadoRutExt = $this->ajax('derivadoRutExt');
        $this->setSession('derivadoRutExt', $derivadoRutExt);

        $listaPrestacion = $this->ajax('ListaPrestacion');
        $this->setSession('ListaPrestacion', $listaPrestacion);

        $listaTratamiento = $this->ajax('ListaTratamiento');
        $this->setSession('ListaTratamiento', $listaTratamiento);

        $listaDiferencia = $this->ajax('ListaDiferencia');
        $this->setSession('ListaDiferencia', $listaDiferencia);

        $listaDiferenciaSaldo = $this->ajax('ListaDiferenciaSaldo');
        $this->setSession('ListaDiferenciaSaldo', $listaDiferenciaSaldo);

        $idReservaAtencion = $this->ajax('idReservaAtencion');
        $this->setSession('idReservaAtencion', $idReservaAtencion);

        $idDerivadoExterno = $this->ajax('idDerivadoExterno');
        $this->setSession('idDerivadoExterno', $idDerivadoExterno);

        $caja = $this->ajax('caja');
        $this->setSession('caja', $caja);

        $garantia = $this->ajax('TipoDeMedioPago');

        /////////////FORMULARIO DATOS PROFESIONAL QUE INGRESA EXAMEN
        $formProfesional = $this->ajax('formProfesional');
        $this->setSession('datosProfesional', $formProfesional);
        ////////////////////

        if ($garantia !== null) {
            $this->setSession('garantia', $garantia);
        }

        $idModulo = $this->container->getParameter('modulo_caja');
        $estadoApi = $this->obtenerApiModulo($idModulo);

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        ($estadoApi === 'core') ? $this->setSession('api', null) : $this->setSession('api', 1);

        return new Response("ok");
    }

    public function cajaPagarMediosPagoAction(Request $request)
    {
        //Carga de Parametros Standar
        $em = $this->getDoctrine()->getManager();
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $oUser = $this->getUser();
        $session = $request->getSession();
        $tipos = $this->Tipos($em, $oEmpresa);
        //Fin Carga de Parametros Standar

        /////////// VARIABLES /////////
        $auxAfecta = 0;
        $auxExenta = 0;
        $auxMedioPago = 0;
        $boletaExenta = 0;
        $boletaAfecta = 0;
        $idCantidad = 20;
        $auxSumaBonos = 0;
        $auxBonoCount = 0;
        $montoArticulos = 0;
        $montoPrestaciones = 0;
        $arrAuxiliarPrestaciones = array();
        $arrAuxiliarPrestacionesA = array();
        $arrBoletaExentaId = array();
        $arrBoletaAfectaId = array();
        $arrSubAfecta = array();
        $arrSubExenta = array();
        $arrayOtrosFormasPago = array();
        $arrayFormasPago = array();
        $arrayTodasFormasPago = array();
        $oDetalleTalonario = NULL;
        $oTalonario = NULL;
        $oBoletaAfecta = $tipos['BoletaAfecta'];
        $oBoletaExenta = $tipos['BoletaExenta'];
        $oFecha = new \DateTime("now");
        $esTeleConsulta = 0;
        /*
         * Se eliminan estas líneas ya que la totalización del valor del paquete
         * se agrega en  un registro de accion_clinica_paciente y se registra como una prestación.
         */
        /* $montoTotalCuentaPaquetizado = 0; */

        /////////////// FIN VARIABLES///////////////////////////////

        //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
        // OBTENGO VARIABLES SESSION Y GENERA DE SER NECESARIO OBJETOS
        $oPnatural = ($this->getSession('persona')) ? $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('persona')) : null;
        $oSucursal = ($this->getSession('sucursal')) ? $em->getRepository('RebsolHermesBundle:Sucursal')->find($this->getSession('sucursal')) : null;
//dump($this->getSession('financiador'));exit();

        $oFinanciador = ($this->getSession('financiador')) ? $em->getRepository('RebsolHermesBundle:Prevision')->find($this->getSession('financiador')) : null;
        $oPlan = ($this->getSession('plan')) ? $em->getRepository('RebsolHermesBundle:PrPlan')->find($this->getSession('plan')) : null;
        $oOrigen = ($this->getSession('origen')) ? $em->getRepository('RebsolHermesBundle:Origen')->find($this->getSession('origen')) : null;
        $PacienteApi = ($this->getSession('pacienteApi')) ? $this->getSession('pacienteApi') : null;
        $PacienteGarantia = ($this->getSession('idPacienteGarantia')) ? $this->getSession('idPacienteGarantia') : null;
        ($this->getSession('auXidPnaturalMascota')) ? $this->setSession('idPnaturalMascota', $this->getSession('auXidPnaturalMascota')) : null;
        $oPnaturalMascota = ($this->getSession('idPnaturalMascota')) ? $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('idPnaturalMascota')) : null;
        ($oPnaturalMascota) ? $this->setSession('auXidPnaturalMascota', $this->getSession('idPnaturalMascota')) : null;
        $oConvenio = ($this->getSession('convenio')) ? $em->getRepository('RebsolHermesBundle:Prevision')->find($this->getSession('convenio')) : null;
        $oCaja = ($this->getSession('caja')) ? $em->getRepository('RebsolHermesBundle:Caja')->find($this->getSession('caja')) : null;
        $oInterfazImed = ($this->getSession('idInterfazImed') != null) ? $em->getRepository('RebsolHermesBundle:InterfazImed')->find($this->getSession('idInterfazImed')) : null;
        $arrayUnserialize = ($this->getSession('idInterfazImed') != null) ? unserialize($oInterfazImed->getListaBonos()) : null;
        $arrayUnserializeFP = ($this->getSession('idInterfazImed') != null) ? unserialize($oInterfazImed->getListaForPag()) : null;
        $folioGlobal = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        $derivadoInt = $this->getSession('derivadoInt');
        $derivadoExt = $this->getSession('derivadoExt');
        $derivadoRutExt = $this->getSession('derivadoRutExt');
        $ListaPrestacion = $this->getSession('ListaPrestacion');
        $ListaTratamiento = $this->getSession('ListaTratamiento');
        $ListaDiferencia = $this->getSession('ListaDiferencia');
        $ListaDiferenciaSaldo = $this->getSession('ListaDiferenciaSaldo');
        $datosProfesional = $this->getSession('datosProfesional');
        $tipoPago = $this->getSession('tipoPago');
        $idPrePagoCuenta = $this->getSession('idPrePagoCuenta');

        $requierePagoAnticipadoUrgencia = $em->getRepository('RebsolHermesBundle:Parametro')
            ->obtenerParametro('URGENCIA_REQUIERE_PAGO_ANTICIPADO_PARA_EJECUTAR_PRESTACION');
        $bRequierePagoAnticipadoUrgencia = $requierePagoAnticipadoUrgencia['valor'] === '1';
        $oEstadoAccionClinicaPlanificado = $em->getRepository('RebsolHermesBundle:EstadoAccionClinica')->find($this->container->getParameter('EstadoAccionClinica.planificado'));

        $accionesClinicaFaltantes = [];
        $examenPacienteRevisado = [];
        $accionesClinicaFaltantes = [];

        if(empty($ListaPrestacion)){
            return new JsonResponse(array('mensaje' => 'sinPrestaciones', 'data' => null));
        }

        foreach ($ListaPrestacion as $prestacion) {
            if ($prestacion[4] != null && $prestacion[4] != "") {
                $arrAccionesClinicas = $em->getRepository('RebsolHermesBundle:ExamenPacienteFc')->obtenerAccionesClinicasExamen($prestacion[4]);
                $arrIdPaquete = $em->getRepository('RebsolHermesBundle:AccionClinica')->esPaquete($prestacion[0]);

                if (!array_key_exists($prestacion[4], $accionesClinicaFaltantes)) {
                    $accionesClinicaFaltantes[$prestacion[4]] = $arrAccionesClinicas;
                }

                // arreglo caja pago procedimiento
                foreach ($arrAccionesClinicas as $key => $idAccionClinica) {
                    if ($idAccionClinica == $prestacion[0]) {
                        $oExamenPacienteDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findOneBy(['idExamenPacienteFc' => $prestacion[4], 'idAccionClinica' => $idAccionClinica]);

                        $oExamenPacienteDetalle->setIdEstadoPago($em->getRepository('RebsolHermesBundle:EstadoPago')->find(1));
                    }
                }
                //

                if (is_null($arrIdPaquete)) {

                    //Se revisan las acciones clinicas que quedan en la lista
                    $arrAccionClinicasRevisar = $accionesClinicaFaltantes[$prestacion[4]];


                    foreach ($arrAccionClinicasRevisar as $key => $idAccionClinica) {
                        if ($idAccionClinica == $prestacion[0]) {
                            unset($accionesClinicaFaltantes[$prestacion[4]][$key]);
                        }
                    }
                }
            }
        }

        if ($ListaTratamiento !== "" && !is_null($ListaTratamiento)) {

            $esTratamiento = true;

        } else {
            $esTratamiento = false;
        }

        if ($ListaDiferencia !== "") {
            $esDiferencia = true;

        } else {
            $esDiferencia = false;

        }


        if ($ListaDiferenciaSaldo !== "") {
            $esDiferenciaSaldo = true;

        } else {
            $esDiferenciaSaldo = false;
        }

        $Api = $this->getSession('api');
        $idReservaAtencion = $this->getSession('idReservaAtencion');
        $idDerivadoExterno = $this->getSession('idDerivadoExterno');
        $garantia = $this->getSession('garantia');
        $TotalCuenta = $this->getSession('vSumaCantidad');
        if ($esDiferenciaSaldo) {
            $TotalCuenta = intval($ListaDiferenciaSaldo[3]);
        }
        //FIN OBTENGO VARIABLES SESSION
        //ESTADOS//
        $EstadoActivo = $this->estado("EstadoActivo");
        $EstadoPagoActiva = $this->estado("EstadoPagoActiva");
        $EstadoCuentaCerradaPagada = $this->estado("EstadoCuentaCerradaPagada");
        $EstadoPagoGarantia = $this->estado("EstadoPagoGarantia");
        $EstadoAccionClinicaSolicitado = ($idReservaAtencion) ? $this->estado("EstadoAccionClinicaSolicitado") : null;
        $EstadoPilaActiva = $this->estado("EstadoPilaActiva");
        $EstadoBoletaAnulada = $this->estado("EstadoBoletaAnulada");
        $EstadoBoletaActiva = $this->estado("EstadoBoletaActiva");
        $EstadoTratamientoFinalizado = $this->estado("EstadoTratamientoFinalizado");
        $EstadoAbiertaPendientePago = $this->estado("EstadoAbiertaPendientePago");
        $EstadoAbiertaPagadaTotal = $this->estado("EstadoAbiertaPagadaTotal");
        $EstadoCerradaPendientePago = $this->estado("EstadoCerradaPendientePago");
        $EstadoCerradaPagadaTotal = $this->estado("EstadoCerradaPagadaTotal");
        $EstadoCerradaRevisionInterna = $this->estado("EstadoCerradaRevisionInterna");

        $nuevoPaciente = false;

        if ($idReservaAtencion) {

            $oReservaAtencionAux = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);

            if ($oReservaAtencionAux->getIdPagoCuenta()) {
                return new JsonResponse(array('mensaje' => "pagado", 'data' => null));
            }
        }

        // FIN ESTADOS //
        //
        //USO REPOSITORIO
        //
        $oServicio = $this->rPaciente()->obtenerRelUsuarioServicio($EstadoActivo, $oSucursal);
        $evento = ($oPnaturalMascota) ? $this->rPaciente()->obtenerEventos($oPnaturalMascota) : $this->rPaciente()->obtenerEventos($oPnatural);
        $numeroAtencion = $this->rPaciente()->obtenerNumeroAtencion();
        $idFormaspago = $this->rPagoCuenta()->Formaspago($EstadoActivo, $oEmpresa);

        //FIN USO REPOSITORIO


        foreach ($oServicio as $id) {
            $oServicio = $em->getRepository("RebsolHermesBundle:Servicio")->find($id);
            $oRelUsuarioServicio = $em->getRepository("RebsolHermesBundle:RelUsuarioServicio")->findOneBy(array("idUsuario" => $oUser, "idServicio" => $oServicio, "idEstado" => $EstadoActivo));
            if ($oRelUsuarioServicio) {
                break;
            }
        }

        foreach ($idFormaspago as $id) {
            if ($id['verEnCaja']) {
                ($id['gr'] == 0) ? $arrayFormasPago[] = $id['id'] : $arrayOtrosFormasPago[] = $id['id'];
                $arrayTodasFormasPago[] = $id['id'];
            }
        }

        $idCantidad = count($arrayTodasFormasPago);

        $oUbicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
            "idUsuario" => $oUser,
            "idEstado" => $EstadoActivo
        ));

        // FIN GENERA OBJETOS
        //GENERA BIND A FORM
        $MediosPagoform = $this->createForm(MediosPagoType::class, null
            , array(
                'validaform' => null,
                'idFrom' => $arrayTodasFormasPago,
                'idFromOtros' => $arrayOtrosFormasPago,
                'idCantidad' => $idCantidad,
                'clone' => false,
                'nuevo' => true,
                'sucursal' => $oSucursal->getId(),
                'iEmpresa' => $oEmpresa->getId(),
                'estado_activado' => $EstadoActivo->getId(),
            )
        );

        $MediosPagoform->handleRequest($request);

        $auxxx = $request->request->get('rebsol_hermesbundle_MediosPagoType');

        // FIN GENERA BIND A FORM
        //INYECCION EN BASE DE DATOS
        if ($PacienteGarantia) {
            $oPaciente = $em->getRepository('RebsolHermesBundle:Paciente')->find($PacienteGarantia);
            /* Se comenta las dos líneas siguientes:
             * de acuerdo al req. https://nuevoredmine.rayensalud.com/issues/54367
             * por que actualiza la fecha de ingreso real del paciente,  dato_ingreso.fecha_ingreso debe ser igual a paciente.fecha_ingreso
             */
            // $oPaciente->setFechaIngreso($oFecha);
            // $em->persist($oPaciente);

            $oCuentaPaciente = $em->getRepository('RebsolHermesBundle:CuentaPaciente')->findOneBy(array('idPaciente' => $PacienteGarantia));
            if(!$idPrePagoCuenta){
                $oCuentaPaciente->setIdEstadoCuenta($EstadoCuentaCerradaPagada);
                $em->persist($oCuentaPaciente);
            }
        } else {
            if ($Api) {

                $oPaciente = ($PacienteApi) ? $em->getRepository('RebsolHermesBundle:Paciente')->find($PacienteApi) : null;

                if ($oPnaturalMascota && !$idReservaAtencion && !$oPaciente) {
                    $oEmpresa = $this->ObtenerEmpresaLogin();
                    $oPaciente = new Paciente();
                    $oPaciente->setIdPnatural($oPnaturalMascota);
                    $oPaciente->setEvento($evento);
                    $oPaciente->setNumeroAtencion($numeroAtencion);
                    $oPaciente->setIdTipoAtencionFc($tipos['TipoAtencionFcAmbulatoria']);
                    $oPaciente->setFechaIngreso($oFecha);
                    $oPaciente->setIdEmpresa($oEmpresa);

                    if ($derivadoExt != null || $derivadoInt != null) {

                        if ($derivadoExt) {
                            if ($idDerivadoExterno) {
                                $oDerivadorExterno = $em->getRepository("RebsolHermesBundle:DerivadorExterno")->find($idDerivadoExterno);
                            } else if (!empty($derivadoRutExt) && $derivadoInt == null) {
                                $derivadoRutExt[0] = str_replace('.', '', $derivadoRutExt[0]);

                                $oDerivadorExterno = new DerivadorExterno();
                                $oDerivadorExterno->setRut($derivadoRutExt[0]);
                                $oDerivadorExterno->setDigitoVerificador($derivadoRutExt[1]);
                                $oDerivadorExterno->setNombre($derivadoExt);
                                $oDerivadorExterno->setIdEstado($EstadoActivo);
                                $oDerivadorExterno->setIdEmpresa($oEmpresa);
                                $oDerivadorExterno->setIdUsuarioCreacion($oUser);
                                $oDerivadorExterno->setFechaCreacion($oFecha);
                                $em->persist($oDerivadorExterno);
                            }
                            $oPaciente->setEsExterno(1);
                            $oPaciente->setIdDerivadorExterno($oDerivadorExterno);
                        } else {
                            $oProfesional = $em->getRepository('RebsolHermesBundle:Pnatural')->find($derivadoInt);
                            $oUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->findOneBy(array("idPersona" => $oProfesional->getidPersona()));
                            $oPaciente->setEsExterno(0);
                            $oPaciente->setIdProfesional($oUsuario);
                        }
                    }

                    $oPaciente->setIdOrigen($oOrigen);
                    $oPaciente->setIdPlan($oPlan);
                    (!$oConvenio) ? $oPaciente->setIdConvenio(NULL) : $oPaciente->setIdConvenio($oConvenio);
                    (!$oFinanciador) ? $oPaciente->setIdFinanciador(NULL) : $oPaciente->setIdFinanciador($oFinanciador);

                    $em->persist($oPaciente);

                }

                if ($idReservaAtencion && !$oPaciente) {
                    $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
                    $oPaciente = $em->getRepository('RebsolHermesBundle:Paciente')->find($oReservaAtencion->getIdPaciente());
                    $oPaciente->setIdOrigen($oOrigen);
                    $oPaciente->setIdPlan($oPlan);
                    (!$oConvenio) ? $oPaciente->setIdConvenio(NULL) : $oPaciente->setIdConvenio($oConvenio);
                    $em->persist($oPaciente);
                }
                if ($idReservaAtencion && $PacienteApi) {
                    $oPaciente->setIdOrigen($oOrigen);
                    $oPaciente->setIdPlan($oPlan);
                    (!$oConvenio) ? $oPaciente->setIdConvenio(NULL) : $oPaciente->setIdConvenio($oConvenio);
                    $em->persist($oPaciente);
                }

            } else {

                $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
                $oEmpresa = $this->ObtenerEmpresaLogin();
                $oPaciente = new Paciente();
                $oPaciente->setIdPnatural($oPnatural);
                $oPaciente->setEvento($evento);
                $oPaciente->setNumeroAtencion($numeroAtencion);
                $oPaciente->setIdTipoAtencionFc($tipos['TipoAtencionFcAmbulatoria']);
                $oPaciente->setFechaIngreso($oFecha);
                $oPaciente->setIdEmpresa($oEmpresa);
                if (!is_null($oReservaAtencion) && $oReservaAtencion->getIdEmpresaSolicitante()) {
                    $oPaciente->setIdEmpresaSolicitante($oReservaAtencion->getIdEmpresaSolicitante());
                }

                if ($derivadoExt != null || $derivadoInt != null) {
                    if ($derivadoExt) {
                        if ($idDerivadoExterno) {
                            $oDerivadorExterno = $em->getRepository("RebsolHermesBundle:DerivadorExterno")->find($idDerivadoExterno);
                        } else if (!empty($derivadoRutExt) && $derivadoInt == null) {
                            $derivadoRutExt[0] = str_replace('.', '', $derivadoRutExt[0]);

                            $oDerivadorExterno = new DerivadorExterno();
                            $oDerivadorExterno->setRut($derivadoRutExt[0]);
                            $oDerivadorExterno->setDigitoVerificador($derivadoRutExt[1]);
                            $oDerivadorExterno->setNombre($derivadoExt);
                            $oDerivadorExterno->setIdEstado($EstadoActivo);
                            $oDerivadorExterno->setIdEmpresa($oEmpresa);
                            $oDerivadorExterno->setIdUsuarioCreacion($oUser);
                            $oDerivadorExterno->setFechaCreacion($oFecha);
                            $em->persist($oDerivadorExterno);
                        }
                        $oPaciente->setEsExterno(1);
                        $oPaciente->setIdDerivadorExterno($oDerivadorExterno);
                    } else {
                        $oProfesional = $em->getRepository('RebsolHermesBundle:Pnatural')->find($derivadoInt);
                        $oUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->findOneBy(array("idPersona" => $oProfesional->getIdPersona()));
                        $oPaciente->setEsExterno(0);
                        $oPaciente->setIdProfesional($oUsuario);
                    }
                }

                if ($idReservaAtencion) {
                    $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);

                    // Preguntar si el usuario es SALA
                    $esSalaReservaAtencion = $oReservaAtencion->getIdUsuarioProfesional()->getEsSala();

                    // El profesional sólo debe ser sobreescrito cuando NO es sala (agenda procedimiento)
                    if ($esSalaReservaAtencion === false) {
                        $oPaciente->setEsExterno(0);
                        $oPaciente->setIdProfesional($oReservaAtencion->getIdUsuarioProfesional());
                    }

                    // Si fue seleccionado un derivador externo, no hay problemas en guardar el profesional de la reserva
                    if ($oPaciente->getIdDerivadorExterno() !== null) {
                        $oPaciente->setEsExterno(0);
                        $oPaciente->setIdProfesional($oReservaAtencion->getIdUsuarioProfesional());
                    }
                }

                $oPaciente->setIdOrigen($oOrigen);
                $oPaciente->setIdPlan($oPlan);
                (!$oConvenio) ? $oPaciente->setIdConvenio(NULL) : $oPaciente->setIdConvenio($oConvenio);
                (!$oFinanciador) ? $oPaciente->setIdFinanciador(NULL) : $oPaciente->setIdFinanciador($oFinanciador);

                $em->persist($oPaciente);

            }

            $oCuentaPaciente = new CuentaPaciente();

            $oCuentaPaciente->setIdEstadoCuenta($EstadoCuentaCerradaPagada);
            $oCuentaPaciente->setIdPaciente($oPaciente);
            $oCuentaPaciente->setTotalCuenta($TotalCuenta);

            $em->persist($oCuentaPaciente);

        }

        $oPagoCuenta = !$idPrePagoCuenta ? new PagoCuenta() : $em->getRepository('RebsolHermesBundle:PagoCuenta')->find($idPrePagoCuenta);

        $oPagoCuenta->setIdPaciente($oPaciente);
        ($garantia == 0) ? $oPagoCuenta->setIdEstadoPago($EstadoPagoActiva) : $oPagoCuenta->setIdEstadoPago($EstadoPagoGarantia);
        $oPagoCuenta->setIdCuentaPaciente($oCuentaPaciente);
        $oPagoCuenta->setIdCaja($oCaja);
        $oPagoCuenta->setIdUsuario($oUser);
        $oPagoCuenta->setFechaPago($oFecha);
        $oPagoCuenta->setNumeroDocumento(NULL);
        $oPagoCuenta->setImpuesto(NULL);
        $oPagoCuenta->setMonto($TotalCuenta);
        $oPagoCuenta->setEsCobranza(0);

        // Verifica si es una diferencia global y almacena la información en pago_cuenta
        $oPagoCuenta->setPrecioDiferencia($TotalCuenta);
        if ($esDiferencia) {
            if ($ListaDiferencia['1'] == 'true') {
                $oPagoCuenta->setMontoDiferencia($ListaDiferencia['4']);
                $oPagoCuenta->setPrecioDiferencia($ListaDiferencia['5']);
                $oPagoCuenta->setIdMotivoDiferencia($em->getRepository('RebsolHermesBundle:MotivoDiferencia')->find($ListaDiferencia['2']));
                $oPagoCuenta->setMonto($TotalCuenta);
            }
        } elseif ($esDiferenciaSaldo) {
            $oPagoCuenta->setMontoDiferencia($ListaDiferenciaSaldo['2']);
            $oPagoCuenta->setPrecioDiferencia($ListaDiferenciaSaldo['3']);
            $oPagoCuenta->setIdMotivoDiferencia($em->getRepository('RebsolHermesBundle:MotivoDiferencia')->find($ListaDiferenciaSaldo['4']));
            $oPagoCuenta->setMonto($TotalCuenta);

        }

        $oPagoCuenta->setIdSubEmpresa($em->getRepository('RebsolHermesBundle:SubEmpresa')->find($this->getsession('idSubEmpresaItem')));

        $em->persist($oPagoCuenta);

        $oPrevisionPnatural = new PrevisionPnatural();

        $oPrevisionPnatural->setIdPnatural($oPnatural);
        $oPrevisionPnatural->setIdPaciente($oPaciente);
        $oPrevisionPnatural->setFechaPrevision($oFecha);
        (!$oConvenio) ? $oPrevisionPnatural->setIdConvenio(NULL) : $oPrevisionPnatural->setIdConvenio($oConvenio);
        (!$oFinanciador ) ? $oPrevisionPnatural->setIdPrevision(null) : $oPrevisionPnatural->setIdPrevision($oFinanciador);

        $em->persist($oPrevisionPnatural);


        // Verifica si se trata de Boleta Afecta o Exenta o Ambas
        if ($idReservaAtencion) {
            if ($Api) {

                $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
                $oReservaAtencion->setIdPlan($oPlan->getid());
                $oReservaAtencion->setIdPagoCuenta($oPagoCuenta);
                ($oReservaAtencion->getFechaRecepcion()) ? null : $oReservaAtencion->setFechaRecepcion($oFecha);
                $oReservaAtencion->setRecepcionado(1);
                $oReservaAtencion->setidPrevision($oFinanciador);
                ($oConvenio) ? $oReservaAtencion->setIdConvenio($oConvenio) : null;

            } else {

                $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
                $oReservaAtencion->setIdPaciente($oPaciente);
                $oReservaAtencion->setIdPlan($oPlan->getid());
                $oReservaAtencion->setIdPagoCuenta($oPagoCuenta);
                $oReservaAtencion->setFechaRecepcion($oFecha);
                $oReservaAtencion->setRecepcionado(1);
                $oReservaAtencion->setidPrevision($oFinanciador);
                ($oConvenio) ? $oReservaAtencion->setIdConvenio($oConvenio) : null;

            }

            $em->persist($oReservaAtencion);

            $oReservaAtencionLog = new ReservaAtencionLog();

            $oReservaAtencionLog->setIdReservaAtencion($oReservaAtencion);
            $oReservaAtencionLog->setIdHorarioConsultaNuevo($oReservaAtencion->getIdHorarioConsulta());
            $oReservaAtencionLog->setFechaRegistro($oFecha);
            $oReservaAtencionLog->setIdReservaTipoLog($tipos['TipoLogRecepcion']);
            $oReservaAtencionLog->setIdUsuarioModifica($oUser);

            $em->persist($oReservaAtencionLog);

            $oReservaAtencionLog2 = new ReservaAtencionLog();

            $oReservaAtencionLog2->setIdReservaAtencion($oReservaAtencion);
            $oReservaAtencionLog2->setIdHorarioConsultaNuevo($oReservaAtencion->getIdHorarioConsulta());
            $oReservaAtencionLog2->setFechaRegistro($oFecha);
            $oReservaAtencionLog2->setIdReservaTipoLog($tipos['TipoLogPagoReserva']);
            $oReservaAtencionLog2->setIdUsuarioModifica($oUser);

            $em->persist($oReservaAtencionLog2);

        }

        if (!$Api) {
            if ($esTratamiento) {

                $oTratamiento = null;
                $tratamientoCompletado = $pagadoCompletado = false;
                $CantidadTotalTratamiento = false;
                $arrTratamientos = array();
                $arrDetalleTratamiento = array();

                $cantidadDelPago = 0;
                foreach ($ListaTratamiento as $tratamiento) {
                    $cantidadDelPago = $cantidadDelPago + intval($tratamiento['1']);
                    $oDetalleTratamiento = $em->getRepository('RebsolHermesBundle:DetalleTratamiento')
                        ->find(intval($tratamiento['2']));
                    $CantidadTotal = $oDetalleTratamiento->getCantidadTotal();
                    $CantidadPagada = $oDetalleTratamiento->getCantidadPagada();
                    $CantidadRealizada = $oDetalleTratamiento->getCantidadRealizada();
                    $CantidadTotalTratamiento = $CantidadTotal;
                    $tratamientoCompletado = $CantidadRealizada;
                    $cantidadActualRealizada = intval($CantidadRealizada) + intval($tratamiento['1']);
                    $cantidadActualPagada = (intval($tratamiento['5']) == 0) ?
                        $CantidadPagada :
                        $CantidadPagada + intval($tratamiento['1']);


                    $oDetalleTratamiento->setCantidadPagada($cantidadActualPagada);
                    $oDetalleTratamiento->setCantidadRealizada($cantidadActualRealizada);
                    $em->persist($oDetalleTratamiento);

                    $oDetalleTratamientoTotal = $em->getRepository('RebsolHermesBundle:DetalleTratamiento')
                        ->obtenerInformacionDetalleTratamiento(intval($this->getSession('idTratamiento')));

                    $arrTratamientos = array('idDetalleTratamiento' => intval($tratamiento['2']),
                        'CantidadPagada' => $oDetalleTratamientoTotal['cantidadPagada']+$cantidadDelPago,
                        'CantidadRealizada' => $oDetalleTratamientoTotal['cantidadRealizada']+$cantidadDelPago,
                        'CantidadTotal' => $oDetalleTratamientoTotal['cantidadTotal']);

                    $arrDetalleTratamiento[] = $arrTratamientos;
                }

                foreach ($arrDetalleTratamiento as $a) {
                    if ($a['CantidadRealizada'] == $a['CantidadTotal']) {
                        $tratamientoCompletado = true;
                    } else {
                        $tratamientoCompletado = false;
                    }

                    if ($a['CantidadPagada'] == $a['CantidadTotal']) {
                        $pagadoCompletado = true;
                    } else {
                        $pagadoCompletado = false;
                    }

                }


                if ($tratamientoCompletado && $pagadoCompletado) {
                    $oTratamiento = $em->getRepository('RebsolHermesBundle:Tratamiento')
                        ->find($this->getSession('idTratamiento'));

                    $oTratamiento->setIdEstado($EstadoTratamientoFinalizado);
                    $em->persist($oTratamiento);
                }
            }

            if ($esDiferencia) {
                $oDiferencia = $em->getRepository('RebsolHermesBundle:Diferencia')
                    ->find($this->getSession('idDiferencia'));
                $oDiferencia->setIdPagoCuenta($oPagoCuenta);
                $oDiferencia->setIdPaciente($oPaciente);
                $em->persist($oDiferencia);
                $em->flush();

            }
        }

        if ($esDiferenciaSaldo) {
            $oDiferenciaSaldo = $em->getRepository('RebsolHermesBundle:Diferencia')->find($this->getSession('idDiferenciaSaldo'));
            $oDiferenciaSaldo->setIdPagoCuenta($oPagoCuenta);
            $oDiferenciaSaldo->setIdPaciente($oPaciente);
            $em->persist($oDiferenciaSaldo);
            $em->flush();

        }

        if ($esDiferencia == 1) {
            $arrAuxDiferencias = array();
            if ($ListaDiferencia['1'] == 'false') {
                foreach ($ListaDiferencia['6'] as $e) {
                    $arrAuxDiferencias[$e['1']] = $e;
                }
            }
        }

        $ii = 0;
        $arrTempIdAccionClinicaPaciente = array();

        foreach ($ListaPrestacion as $prestacion) {

            if (intval($prestacion['3']) == 2) {
                $oArticuloPaciente = $em->getRepository('RebsolHermesBundle:ArticuloPaciente')->find(intval($prestacion['0']) );
                $oArticuloPaciente->setIdEstadoPago($EstadoPagoActiva);
                $oArticuloPaciente->setIdPagoCuenta($oPagoCuenta);
                if ($bRequierePagoAnticipadoUrgencia && $oPaciente->getIdTipoAtencionFc()->getId() === intval($this->container->getParameter('TipoAtencion.urgencia'))) {
                    $oArticuloPaciente->setIdEstadoAccionClinica($oEstadoAccionClinicaPlanificado);
                }
                /*
                 * Se comentan las siguientes líneas que tienen relación con tratamiento
                 * solucionado el issues https://nuevoredmine.rayensalud.com/issues/70513
                 * */
                /*if ($prestacion['7'] !==''){
                    $oArticuloPaciente->setIdDetalleTratamiento($em->getRepository("RebsolHermesBundle:DetalleTratamiento")->find($prestacion['7']));
                }*/
                $oArticulo = $oArticuloPaciente->getIdArticulo();

                $oSubEmpresa = $em->getRepository('RebsolHermesBundle:SubEmpresa')->find($oArticulo->getIdSubEmpresaFacturadora()); //AGREGAR SUBEMPRESA FACTURADORA
                $auxAfecta = $auxAfecta + (intval($prestacion['2']) * intval($prestacion['1']));

                if (count($arrSubAfecta) == 0) {

                    if (array_key_exists($oSubEmpresa->getId(), $arrAuxiliarPrestacionesA)) {
                        $arrAuxiliarPrestacionesA[$oSubEmpresa->getId()] = $arrAuxiliarPrestacionesA[$oSubEmpresa->getId()] + $auxAfecta;
                    } else {
                        $arrAuxiliarPrestacionesA[$oSubEmpresa->getId()] = $auxAfecta;
                    }
                    $arrSubAfecta[] = array("se" => $oSubEmpresa->getId(), "monto" => $auxAfecta);

                } else {
                    if (array_key_exists($oSubEmpresa->getId(), $arrAuxiliarPrestacionesA)) {
                        $arrAuxiliarPrestacionesA[$oSubEmpresa->getId()] = $arrAuxiliarPrestacionesA[$oSubEmpresa->getId()] + $auxAfecta;
                    } else {
                        $arrAuxiliarPrestacionesA[$oSubEmpresa->getId()] = $auxAfecta;
                    }
                }
                $montoArticulos = $montoArticulos + $auxAfecta;
                $auxAfecta = 0;
                $em->persist($oArticuloPaciente);
            }
            if (intval($prestacion['3']) == 1) {

                if(!empty($prestacion['5']) && $prestacion['5'] != 'undefined' ){
                    $oAccionClinicaPaciente = $em->getRepository('RebsolHermesBundle:AccionClinicaPaciente')->find($prestacion['6']);
                }else{
                    $oAccionClinicaPaciente = new AccionClinicaPaciente();
                }

                $oAccionClinicaPaciente->setIdPaciente($oPaciente);
                $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($prestacion['0']);
                $oAccionClinicaPaciente->setIdAccionClinica($oAccionClinica);
                $oAccionClinicaPaciente->setIdServicioRealizacion(NULL);
                $oAccionClinicaPaciente->setIdServicioSolicitud($oRelUsuarioServicio->getidServicio());
                $oAccionClinicaPaciente->setFechaSolicitud($oFecha);
                $oAccionClinicaPaciente->setFechaAgenda($oFecha);
//                $oAccionClinicaPaciente->setFechaRealizacion(NULL);
                $oAccionClinicaPaciente->setIdUsuarioServicioSolicitante($oRelUsuarioServicio);
                $oAccionClinicaPaciente->setCantidad($prestacion['1']);
                (!$oConvenio) ? $oAccionClinicaPaciente->setIdPrevision($oFinanciador) : $oAccionClinicaPaciente->setIdPrevision($oConvenio);

                if ($bRequierePagoAnticipadoUrgencia && $oPaciente->getIdTipoAtencionFc()->getId() === intval($this->container->getParameter('TipoAtencion.urgencia'))) {
                    $oAccionClinicaPaciente->setIdEstadoAccionClinica($oEstadoAccionClinicaPlanificado);
                } else {
                    if(!$oAccionClinicaPaciente->getIdEstadoAccionClinica()){
                        $oAccionClinicaPaciente->setIdEstadoAccionClinica($EstadoAccionClinicaSolicitado);
                    }
                }
                $oAccionClinicaPaciente->setPrecioCobrado($prestacion['2']);
                $oAccionClinicaPaciente->setGlosaSolicitud(NULL);
                $oAccionClinicaPaciente->setIdEstadoPago($EstadoPagoActiva);
                $oAccionClinicaPaciente->setIdPagoCuenta($oPagoCuenta);
                $oAccionClinicaPaciente->setEsGes(0);
                if ($prestacion['7'] !==''){
                    $oAccionClinicaPaciente->setIdDetalleTratamiento($em->getRepository("RebsolHermesBundle:DetalleTratamiento")->find($prestacion['7']));
                }

                $total = 0;
                $idMotivoDiferencia = 0;
                $idSentidoDiferencia = 0;
                if ($esDiferencia == 1) {
                    if ($ListaDiferencia['1'] == 'false') {
                        $arrDiferencias = $ListaDiferencia['6'];
                        if (array_key_exists(intval($oAccionClinica->getId()), $arrAuxDiferencias)) {
                            if (intval($arrAuxDiferencias[$oAccionClinica->getId()]['0']) == intval($prestacion['3'])) {
                                if (intval($ListaDiferencia['0']) == 1) {
                                    $montoDiferencia = str_replace(',', '', $arrAuxDiferencias[$oAccionClinica->getId()]['2']);
                                    $oAccionClinicaPaciente->setPorcentajeDescuento(intval($arrAuxDiferencias[$oAccionClinica->getId()]['3']));
                                    $total = round((intval($montoDiferencia) * intval($arrAuxDiferencias[$oAccionClinica->getId()]['3'])) / 100);
                                } else {
                                    $total = intval($arrAuxDiferencias[$oAccionClinica->getId()]['4']);
                                }
                                $idMotivoDiferencia = $arrAuxDiferencias[$oAccionClinica->getId()]['5'];
                                $oMotivoDifrencia = $em->getRepository('RebsolHermesBundle:MotivoDiferencia')->find($idMotivoDiferencia);
                                $idSentidoDiferencia = $oMotivoDifrencia->getIdTipoSentidoDiferencia()->getId();

                                $oAccionClinicaPaciente->setTotalDescuento($total);
                                $oAccionClinicaPaciente->setIdMotivoDiferencia($oMotivoDifrencia);
                            }
                        }
                    }

                }

                $montoPrestacion = intval($prestacion['1']) * intval($prestacion['2']);
                if ($idSentidoDiferencia == 2) {
                    $montoPrestacion = (intval($prestacion['1']) * intval($prestacion['2'])) - round($total);
                } else if ($idSentidoDiferencia == 1) {
                    $montoPrestacion = (intval($prestacion['1']) * intval($prestacion['2'])) + round($total);
                }
                $oAccionClinicaPaciente->setPrecioDiferencia($montoPrestacion);

                if ($oFinanciador->getTipoPrestacion() === 2) {
                    /* TODO: SE DEBE AVERIGUAR PORQUE SE LIBERABAN LOS FOLIOS
                    if ($folioGlobal['valor'] === '1') {
                        $this->liberarFolios();
                    }*/

                    $oAccionClinicaPaciente->setMontoExento($montoPrestacion);
                } else if ($oFinanciador->getTipoPrestacion() === 1) {
                    if ($oAccionClinica->getEsAfecto() === true) {
                        $iva = $em->getRepository("RebsolHermesBundle:Parametro")->obtenerParametro('PORCENTAJE_IVA');
                        $montoAfectoConIva = $montoPrestacion;
                        $montoAfectoSinIvaSinRedondeo = $montoAfectoConIva / ((intval($iva['valor']) + 100) / 100);
                        $montoAfectoSinIva = round($montoAfectoSinIvaSinRedondeo);
                        $montoIva = round($montoAfectoSinIvaSinRedondeo * (intval($iva['valor']) / 100));
                        $oAccionClinicaPaciente->setMontoAfecto(intval($montoAfectoConIva));
                        $oAccionClinicaPaciente->setIva(intval($iva['valor']));
                        $oAccionClinicaPaciente->setMontoAfectoSinIva(intval($montoAfectoSinIva));
                        $oAccionClinicaPaciente->setMontoIva(intval($montoIva));
                    } else {
                        $oAccionClinicaPaciente->setMontoExento($montoPrestacion);
                    }
                } else if ($oFinanciador->getTipoPrestacion() === 3) {
                    /* TODO: SE DEBE AVERIGUAR PORQUE SE LIBERABAN LOS FOLIOS
                    if ($folioGlobal['valor'] === '1') {
                        $this->liberarFolios();
                    }*/

                    //Agregar Nivel Fonasa
                    $oAccionClinicaPaciente->setMontoExento($montoPrestacion);
                    if ($oAccionClinica->getIdNivelFonasa()) {
                        $oPrecioArancelMle = $em->getRepository('RebsolHermesBundle:PrecioArancelMle')
                            ->obtenerArancelFonasaVigentePorAccionClinica($oAccionClinica);
                        if ($oPrecioArancelMle) {
                            $iva = $em->getRepository("RebsolHermesBundle:Parametro")->obtenerParametro('PORCENTAJE_IVA');
                            $montoAfectoConIvaSinRedondeo = $montoPrestacion - intval($oPrecioArancelMle['montoNivelFonasa']);
                            $montoAfectoConIva = round($montoAfectoConIvaSinRedondeo);
                            $montoAfectoSinIvaSinRedondeo = $montoAfectoConIvaSinRedondeo / ((intval($iva['valor']) + 100) / 100);
                            $montoAfectoSinIva = round($montoAfectoSinIvaSinRedondeo);
                            $montoIva = round($montoAfectoSinIvaSinRedondeo * (intval($iva['valor']) / 100));
                            $oNivelFonasa = $em->getRepository('RebsolHermesBundle:NivelFonasa')->find($oAccionClinica->getIdNivelFonasa()->getId());
                            $oAccionClinicaPaciente->setMontoNivelFonasa($oPrecioArancelMle['montoNivelFonasa']);
                            $oAccionClinicaPaciente->setMontoAfecto(intval($montoAfectoConIva));
                            $oAccionClinicaPaciente->setMontoExento($oPrecioArancelMle['montoNivelFonasa']);
                            $oAccionClinicaPaciente->setIva(intval($iva['valor']));
                            $oAccionClinicaPaciente->setMontoAfectoSinIva(intval($montoAfectoSinIva));
                            $oAccionClinicaPaciente->setMontoIva(intval($montoIva));
                            $oAccionClinicaPaciente->setIdNivelFonasa($oNivelFonasa);
                        }
                    }

                }

                $oAccionClinicaPaciente->setMontoNc(NULL);

                if (isset($oReservaAtencion)) {
                    if (isset($oInterfazImed)) {
                        foreach ($arrayUnserialize as $a) {
                            if(isset($a['LisPrestVta'])){
                                foreach ($a['LisPrestVta'] as $prest) {
                                    if (intval($oAccionClinica->getCodigoAccionClinica()) == intval($prest['CodPrestacion'])) {
                                        if ($prest['EsGes'] == "S") {
                                            $oAccionClinicaPaciente->setEsGes(1);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($folioGlobal['valor'] === '1') {
                    $oEmpresa = $em->getRepository('RebsolHermesBundle:Empresa')->find($oAccionClinica->getIdEmpresa());
                    //getIdSubEmpresaFacturadora

                    $auxExenta = $auxExenta + (intval($prestacion['2']) * intval($prestacion['1']));

                    if (count($arrSubExenta) == 0) {
                        if (!is_null($oEmpresa)) {
                            if (array_key_exists($oEmpresa->getId(), $arrAuxiliarPrestaciones)) {
                                $arrAuxiliarPrestaciones[$oEmpresa->getId()] = $arrAuxiliarPrestaciones[$oEmpresa->getId()] + $auxExenta;
                            } else {
                                $arrAuxiliarPrestaciones[$oEmpresa->getId()] = $auxExenta;
                            }
                            $arrSubExenta[] = array("se" => $oEmpresa->getId(), "monto" => $auxExenta);
                        }

                    } else {
                        if (!is_null($oEmpresa)) {
                            if (array_key_exists($oEmpresa->getId(), $arrAuxiliarPrestaciones)) {
                                $arrAuxiliarPrestaciones[$oEmpresa->getId()] = $arrAuxiliarPrestaciones[$oEmpresa->getId()] + $auxExenta;
                            } else {
                                $arrAuxiliarPrestaciones[$oEmpresa->getId()] = $auxExenta;
                            }
                        }
                    }
                } else {
                    $oSubEmpresa = $em->getRepository('RebsolHermesBundle:SubEmpresa')->find($oAccionClinica->getIdSubEmpresa());
                    //getIdSubEmpresaFacturadora

                    $auxExenta = $auxExenta + (intval($prestacion['2']) * intval($prestacion['1']));

                    if (count($arrSubExenta) == 0) {
                        if (!is_null($oSubEmpresa)) {
                            if (array_key_exists($oSubEmpresa->getId(), $arrAuxiliarPrestaciones)) {
                                $arrAuxiliarPrestaciones[$oSubEmpresa->getId()] = $arrAuxiliarPrestaciones[$oSubEmpresa->getId()] + $auxExenta;
                            } else {
                                $arrAuxiliarPrestaciones[$oSubEmpresa->getId()] = $auxExenta;
                            }
                            $arrSubExenta[] = array("se" => $oSubEmpresa->getId(), "monto" => $auxExenta);
                        }

                    } else {
                        if (!is_null($oSubEmpresa)) {
                            if (array_key_exists($oSubEmpresa->getId(), $arrAuxiliarPrestaciones)) {
                                $arrAuxiliarPrestaciones[$oSubEmpresa->getId()] = $arrAuxiliarPrestaciones[$oSubEmpresa->getId()] + $auxExenta;
                            } else {
                                $arrAuxiliarPrestaciones[$oSubEmpresa->getId()] = $auxExenta;
                            }
                        }
                    }
                }

                $montoPrestaciones = $montoPrestaciones + $auxExenta;
                $auxExenta = 0;
                $em->persist($oAccionClinicaPaciente);
                $arrTempIdAccionClinicaPaciente[$ii] = $oAccionClinicaPaciente->getId();
                $ii = $ii + 1;
            }
        }
        if($oFinanciador->getTipoPrestacion() === 2 || $oFinanciador->getTipoPrestacion() === 3){
//            $this->killSession('folioReservados');
        }

        $auxExenta = count($arrAuxiliarPrestaciones);
        $auxAfecta = count($arrAuxiliarPrestacionesA);

        // Fin Verifica si se trata de Boleta Afecta o Exenta o Ambas

        $oCuentaPacienteLog = new CuentaPacienteLog();

        $oCuentaPacienteLog->setSaldoCuenta(00.00);
        $oCuentaPacienteLog->setFechaEstadoCuenta($oFecha);
        $oCuentaPacienteLog->setNumeroAccion($evento);
        $oCuentaPacienteLog->setIdCuenta($oCuentaPaciente);

        !$idPrePagoCuenta ? $oCuentaPacienteLog->setIdEstadoCuenta($EstadoCuentaCerradaPagada) : $oCuentaPacienteLog->setIdEstadoCuenta($EstadoAbiertaPendientePago);

//        $oCuentaPacienteLog->setIdEstadoCuenta($EstadoCuentaCerradaPagada);
        $oCuentaPacienteLog->setIdUsuario($oUser);
        $oCuentaPacienteLog->setIdPaciente($oPaciente);

        $em->persist($oCuentaPacienteLog);

        ////////////////////////////////////////////////////////////////////////////////////
        //FORMAS DE PAGO
        ////////////////////////////////////////////////////////////////////////////////////
        // Verifica si se trata de Garantía o Sin Garantía
        $arrMediosPagoBoleta = array();
        $auxEmisionBoleta = 0;
        $countEmisionBoleta = 0;
        $montoRestaBoletaMedioPagoNoEmiteBoleta = 0;
        $idCantidad = 0;
        if ($garantia == 1) {
            // ES GARANTIA
            foreach ($arrayOtrosFormasPago as $idForm) {

                if ($MediosPagoform['medioPago_' . $idForm]->getData()) {

                    $oFormasPago = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);

                    $oFormasPagoTipo = $oFormasPago->getIdTipoFormaPago()->getId();
                    if ($oFormasPagoTipo !== $this->parametro('FormaPagoTipo.BonoElectronico') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.BonoManual') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.ChequeFecha') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.ChequeDia') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.Transbank')) {
                        // FORMA DE PAGO SENSILLA Y QUE NO ES DINAMICA EN MULTIPLES FORMULARIOS
                        //EFECTIVO
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.Efectivo')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);
                        }
                        //GRATUIDAD
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.Gratuidad')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////Obtiene Objeto de Gratuidad////////////////////////////////
                            $oTipoGratuidad = $em->getRepository('RebsolHermesBundle:TipoGratuidad')->find($MediosPagoform['idGratuidad']->getData());
                            $oMotivoGratuidad = $em->getRepository('RebsolHermesBundle:MotivoGratuidad')->findOneBy(array("idTipoGratuidad" => $oTipoGratuidad));
                            ////////////////////////Obtiene Objeto de Gratuidad////////////////////////////////
                            $oDetallePagoCuenta->setIdMotivoGratuidad($oMotivoGratuidad);
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);
                        }
                        //CREDITO
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.TarjetaCredito')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            //////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ////////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')->find($MediosPagoform['TarjetaCredito']->getData());
                            $oDocumentoPago->setIdTarjetaCredito($oTarjetaCredito);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }
                        //DEBITO
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.TarjetaDebito')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0);
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ///////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($MediosPagoform['TarjetaDebito__' . $idForm . '_0']->getData());
                            $oDocumentoPago->setIdBanco($oBanco);
                            $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ///////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }

                        //LASIK
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ConvenioLasik')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            //////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ////////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral($MediosPagoform['folio_' . $idForm]->getData());
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm]->getData());
                            $oDocumentoPago->setNumeroVoucher(NULL);
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }
                        //IMED
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ConvenioImed')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            //////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ////////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral($MediosPagoform['folio_' . $idForm]->getData());
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm]->getData());
                            //$oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setNumeroVoucher(NULL);
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }

                    }
                    if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoElectronico') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoManual') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeFecha') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeDia') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.Transbank')) {
                        // DOCUMENTOS QUE PUEDEN TENER VARIOS PAGOS EN UNA SOLA FORMA DE PAGO
                        //CHEQUE FECHA
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeFecha')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $oDocumentoPago = new DocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $MediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
                                $oDocumentoPago->setIdBanco($oBanco);
                                $s = $MediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
                                $s = str_replace('.', '', $s);
                                $s = str_replace('-', '', $s);
                                $oDocumentoPago->setRutPropietario($s);
                                $oDocumentoPago->setNombrePropietario($MediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroDocumento($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                ////////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);


                                $oDetalleDocumentoPago = new DetalleDocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
                                $oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDetalleDocumentoPago->setMontoDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
                                $oDetalleDocumentoPago->setNumeroDocumentoDetalle($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());


                                $oCondicionPago = $em->getRepository('RebsolHermesBundle:CondicionPago')->find($auxxx['condicion_' . $idForm . '_' . $cant]);
                                $oDetalleDocumentoPago->setidCondicionPago($oCondicionPago);
                                $em->persist($oDetalleDocumentoPago);
                            }
                        }
                        //CHEQUE DIA
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeDia')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $oDocumentoPago = new DocumentoPago();
                                ///////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $MediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
                                $oDocumentoPago->setIdBanco($oBanco);
                                $s = $MediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
                                $s = str_replace('.', '', $s);
                                $s = str_replace('-', '', $s);
                                $oDocumentoPago->setRutPropietario($s);
                                $oDocumentoPago->setNombrePropietario($MediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroDocumento($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                //////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);

                                $oDetalleDocumentoPago = new DetalleDocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
                                $oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDetalleDocumentoPago->setMontoDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
                                $oDetalleDocumentoPago->setNumeroDocumentoDetalle($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
                                $oCondicionPagoAlDia = $em->getRepository('RebsolHermesBundle:CondicionPago')->findOneBy(array('codigoInterfaz' => "AL_DIA", 'idEmpresa' => $oEmpresa, 'idEstado' => $EstadoActivo));
                                $oDetalleDocumentoPago->setidCondicionPago($oCondicionPagoAlDia);
                                ///////////////////////////////////////////////////////////////////////////
                                $em->persist($oDetalleDocumentoPago);
                            }
                        }
                        //BONO ELECTRONICO
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoElectronico')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
                            $oDetallePagoCuenta->setIdMoneda(NULL); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setNombreEmpresa(NULL);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            $oDetallePagoCuenta->setIdMotivoGratuidad(NULL);
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();
                            $aNumeroBono = array();
                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $oDocumentoPago = new DocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData()); //Numero bono
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setCopagoImed($MediosPagoform['copago_' . $idForm . '_' . $idCantidad]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                $oDocumentoPago->setSeguroComplementario($MediosPagoform['Seguro_' . $idForm . '_' . $idCantidad]->getData());
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);
                                $aNumeroBono[] = intval($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
                            }
                        }
                        //BONO MANUAL
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoManual')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $oDocumentoPago = new DocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroDocumento($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);
                            }
                        }

                        //TRANSBANK
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.Transbank')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;

                                $oDetallePagoCuenta = new DetallePagoCuenta();
                                //////////////////////////////////////////////////////////////////////////////////////////////
                                $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                                $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                                $oDetallePagoCuenta->setGarantia($garantia);
                                $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                                $oDetallePagoCuenta->setIdConvenio($oConvenio);
                                $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                                $oDetallePagoCuenta->setCodigoControlFacturacion(0);

                                $em->persist($oDetallePagoCuenta);

                                $oDocumentoPago = new DocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral(0); //Numero bono
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')
                                    ->findOneBy(array('abreviacion' => $MediosPagoform['nombreTarjeta_' . $idForm . '_' . $cant]->getData()));
                                $oDocumentoPago->setIdTarjetaCredito($oTarjetaCredito);
                                $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setCodAutorizacion($MediosPagoform['codAutorizacion_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setUltimos4Numeros($MediosPagoform['ultimos4Numeros_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setTarjetaTipo($MediosPagoform['tarjetaTipo_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);
                            }
                        }
                    }
                }
            }
        } else {

            foreach ($arrayFormasPago as $idForm) {
                if ($MediosPagoform['medioPago_' . $idForm]->getData()) {

                    $oFormasPago = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);
                    if ($oFormasPago->getEmiteBoleta() == 0) {
                        $auxEmisionBoleta = $auxEmisionBoleta + 1;
                        $montoRestaBoletaMedioPagoNoEmiteBoleta = $montoRestaBoletaMedioPagoNoEmiteBoleta +
                            intval($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                    } else {
                        $countEmisionBoleta = $countEmisionBoleta + 1;
                    }

                    $oFormasPagoTipo = $oFormasPago->getIdTipoFormaPago()->getId();

                    if ($oFormasPagoTipo !== $this->parametro('FormaPagoTipo.BonoElectronico') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.BonoManual') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.ChequeFecha') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.ChequeDia') ||
                        $oFormasPagoTipo !== $this->parametro('FormaPagoTipo.Transbank')) {
                        // FORMA DE PAGO SENSILLA Y QUE NO ES DINAMICA EN MULTIPLES FORMULARIOS
                        //EFECTIVO
                        //if ($oFormasPagoTipo === 1)
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.Efectivo')) {
                            $auxMedioPago = $auxMedioPago + 0;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);
                        }
                        //GRATUIDAD
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.Gratuidad')) {
                            $auxMedioPago = $auxMedioPago + 0;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////Obtiene Objeto de Gratuidad////////////////////////////////
                            // $oTipoGratuidad = $em->getRepository('RebsolHermesBundle:TipoGratuidad')->find();
                            $oMotivoGratuidad = $em->getRepository('RebsolHermesBundle:MotivoGratuidad')->find($MediosPagoform['idGratuidad_' . $idForm]->getData());
                            ////////////////////////Obtiene Objeto de Gratuidad////////////////////////////////
                            $oDetallePagoCuenta->setIdMotivoGratuidad($oMotivoGratuidad);
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);
                        }
                        //CREDITO
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.TarjetaCredito')) {
                            $auxMedioPago = $auxMedioPago + 0;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            //////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ////////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            // echo'<pre>';var_dump($MediosPagoform['TarjetaCredito_13_0']->getData());exit;
                            $oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')->find($MediosPagoform['TarjetaCredito_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setIdTarjetaCredito($oTarjetaCredito);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);

                        }
                        //DEBITO
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.TarjetaDebito')) {
                            $auxMedioPago = $auxMedioPago + 0;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            /////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);

                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            ////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ///////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $idCantidad]->getData());


                            $oBanco = $em->getRepository('RebsolHermesBundle:Banco')->find($MediosPagoform['TarjetaDebito__' . $idForm . '_0']->getData());
                            $oDocumentoPago->setIdBanco($oBanco);
                            $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $idCantidad]->getData());
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ///////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }
                        //LASIK
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ConvenioLasik')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            //////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ////////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral($MediosPagoform['folio_' . $idForm]->getData());
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm]->getData());
                            $oDocumentoPago->setNumeroVoucher(NULL);
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }
                        //IMED
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ConvenioImed')) {
                            $auxMedioPago = $auxMedioPago + 1;
                            $oDetallePagoCuenta = new DetallePagoCuenta();
                            //////////////////////////////////////////////////////////////////////////////////////////////
                            $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            $oDetallePagoCuenta->setGarantia($garantia);
                            $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm]->getData());
                            $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            $oDetallePagoCuenta->setCodigoControlFacturacion(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                            /////////////////////////////////////////////////////////////////////
                            $em->persist($oDetallePagoCuenta);

                            $oDocumentoPago = new DocumentoPago();
                            ////////////////////////////////////////////////////////////////////////////////////
                            $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                            $oDocumentoPago->setIdPaciente($oPaciente);
                            $oDocumentoPago->setIdCaja($oCaja);
                            $oDocumentoPago->setGarantia($garantia);
                            $oDocumentoPago->setIdFormaPago($oFormasPago);
                            $oDocumentoPago->setNumeroDocumentoGeneral($MediosPagoform['folio_' . $idForm]->getData());
                            $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                            $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm]->getData());
                            $oDocumentoPago->setNumeroVoucher(NULL);
                            $oDocumentoPago->setIdSucursal($oSucursal);
                            ////////////////////////////////////////////////////////////////////////////////////////
                            $em->persist($oDocumentoPago);
                        }

                    }
                    if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoElectronico') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoManual') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeFecha') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeDia') ||
                        $oFormasPagoTipo === $this->parametro('FormaPagoTipo.Transbank')) {
                        // DOCUMENTOS QUE PUEDEN TENER VARIOS PAGOS EN UNA SOLA FORMA DE PAGO
                        //CHEQUE FECHA
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeFecha')) {
                            $auxMedioPago = $auxMedioPago + 0;
                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            // $oDetallePagoCuenta = new DetallePagoCuenta();
                            // $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                            // $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                            // $oDetallePagoCuenta->setGarantia($garantia);
                            // $oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
                            // $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                            // $oDetallePagoCuenta->setIdConvenio($oConvenio);
                            // $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                            // $oDetallePagoCuenta->setCodigoControlFacturacion(0);
                            // $em->persist($oDetallePagoCuenta);

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $oDetallePagoCuenta = new DetallePagoCuenta();
                                $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                                $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                                $oDetallePagoCuenta->setGarantia($garantia);
                                $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                                $oDetallePagoCuenta->setIdConvenio($oConvenio);
                                $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                                $oDetallePagoCuenta->setCodigoControlFacturacion(0);
                                $em->persist($oDetallePagoCuenta);

                                $oDocumentoPago = new DocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $MediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
                                $oDocumentoPago->setIdBanco($oBanco);
                                $s = $MediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
                                $s = str_replace('.', '', $s);
                                $s = str_replace('-', '', $s);
                                $oDocumentoPago->setRutPropietario($s);
                                $oDocumentoPago->setNombrePropietario($MediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroDocumento($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                ////////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);


                                $oDetalleDocumentoPago = new DetalleDocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
                                $oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDetalleDocumentoPago->setMontoDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
                                $oDetalleDocumentoPago->setNumeroDocumentoDetalle($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());

                                $oCondicionPago = $em->getRepository('RebsolHermesBundle:CondicionPago')->find($auxxx['condicion_' . $idForm . '_' . $cant]);
                                $oDetalleDocumentoPago->setidCondicionPago($oCondicionPago);
                                $em->persist($oDetalleDocumentoPago);
                            }
                        }
                        //CHEQUE DIA
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.ChequeDia')) {
                            $auxMedioPago = $auxMedioPago + 0;

                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $oDetallePagoCuenta = new DetallePagoCuenta();
                                $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                                $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                                $oDetallePagoCuenta->setGarantia($garantia);
                                $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                                $oDetallePagoCuenta->setIdConvenio($oConvenio);
                                $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                                $oDetallePagoCuenta->setCodigoControlFacturacion(0);
                                $em->persist($oDetallePagoCuenta);

                                $oDocumentoPago = new DocumentoPago();
                                ///////////////////////////////////////////////////////////////////////////////////
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral(0); ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DATO ENDURO
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oBanco = $em->getRepository('RebsolHermesBundle:Banco')->findOneBy(array('id' => $MediosPagoform['banco_' . $idForm . '_' . $cant]->getData()));
                                $oDocumentoPago->setIdBanco($oBanco);
                                $s = $MediosPagoform['rut_' . $idForm . '_' . $cant]->getData();
                                $s = str_replace('.', '', $s);
                                $s = str_replace('-', '', $s);
                                $oDocumentoPago->setRutPropietario($s);
                                $oDocumentoPago->setNombrePropietario($MediosPagoform['nombre_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroDocumento($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);
                                //////////////////////////////////////////////////////////////
                                $em->persist($oDocumentoPago);

                                $oDetalleDocumentoPago = new DetalleDocumentoPago();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $oDetalleDocumentoPago->setIdDocumentoPago($oDocumentoPago);
                                $oDetalleDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDetalleDocumentoPago->setMontoDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetalleDocumentoPago->setFechaPagoDocumento($oFecha);
                                $oDetalleDocumentoPago->setNumeroDocumentoDetalle($MediosPagoform['cheque_' . $idForm . '_' . $cant]->getData());

                                $oCondicionPagoAlDia = $em->getRepository('RebsolHermesBundle:CondicionPago')->findOneBy(
                                    array(
                                        'codigoInterfaz' => "AL_DIA",
                                        'idEmpresa' => $oEmpresa,
                                        'idEstado' => $EstadoActivo
                                    )
                                );

                                $oDetalleDocumentoPago->setidCondicionPago($oCondicionPagoAlDia);

                                $em->persist($oDetalleDocumentoPago);
                            }
                        }
                        //BONO ELECTRONICO

                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoElectronico')) {
                            $aNumeroBono = array();

                            if ($auxAfecta || $auxExenta) {
                                $auxMedioPago = $auxMedioPago + 0;
                                $auxBonoCount = $auxBonoCount + 1;
                            } else {
                                $auxMedioPago = $auxMedioPago + 1;
                            }
                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();
                            $MontoTotalBonificacion = 0;
                            $MontoTotalseguroCom = 0;
                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $MontoTotalBonificacion = $MontoTotalBonificacion +
                                    $MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData();
                                $MontoTotalseguroCom = $MontoTotalseguroCom +
                                    $MediosPagoform['Seguro_' . $idForm . '_' . $cant]->getData();;


                            }

                            if (!empty($arrayUnserializeFP)) {
                                $iii = 3;
                            } else {
                                $iii = 2;
                            }

                            for ($i = 1; $i <= $iii; $i++) {

                                ${"oDetallePagoCuenta" . $i} = new DetallePagoCuenta();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                switch ($i) {
                                    case 1:
                                        $oFormasPagoForBonoElecronico = $oFormasPago;
                                        ${"oDetallePagoCuenta" . $i}->setMontoPagoCuenta($MontoTotalBonificacion);
                                        break;
                                    case 2:
                                        if ($MontoTotalseguroCom <= 0) {
                                            continue 2;
                                        }
                                        $oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
                                            ->findOneBy(array(
                                                'idTipoFormaPago' => $this->parametro('FormaPagoTipo.SeguroComplementario'),
                                                'idEmpresa' => $oEmpresa->getId()));
                                        ${"oDetallePagoCuenta" . $i}->setMontoPagoCuenta($MontoTotalseguroCom);
                                        break;
                                    case 3:
                                        $Exedente = 0;
                                        if ($oInterfazImed) {
                                            if (!empty($arrayUnserializeFP)) {
                                                foreach ($arrayUnserializeFP as $forma) {
                                                    if (intval($forma->CodForPag) == 6) {

                                                        $Exedente = $forma->MtoTransac + $Exedente;
                                                        $oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
                                                            ->findOneBy(array(
                                                                'idTipoFormaPago' => $this->parametro('FormaPagoTipo.Excedente'),
                                                                'idEmpresa' => $oEmpresa->getId()));
                                                        ${"oDetallePagoCuenta" . $i}->setMontoPagoCuenta($Exedente);
                                                    }
                                                }
                                            }

                                        }
                                        break;
                                    case 4:
                                        //pendiente EFECTIVO
                                        $oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);
                                        break;
                                }
                                ${"oDetallePagoCuenta" . $i}->setIdPagoCuenta($oPagoCuenta);
                                ${"oDetallePagoCuenta" . $i}->setIdFormaPago($oFormasPagoForBonoElecronico);
                                ${"oDetallePagoCuenta" . $i}->setGarantia($garantia);
                                ${"oDetallePagoCuenta" . $i}->setIdMoneda(NULL);
                                ${"oDetallePagoCuenta" . $i}->setIdPrevision($oFinanciador);
                                ${"oDetallePagoCuenta" . $i}->setIdConvenio($oConvenio);
                                ${"oDetallePagoCuenta" . $i}->setFechaDetallePago($oFecha);
                                ${"oDetallePagoCuenta" . $i}->setNombreEmpresa(NULL);
                                ${"oDetallePagoCuenta" . $i}->setCodigoControlFacturacion(0);
                                ${"oDetallePagoCuenta" . $i}->setIdMotivoGratuidad(NULL);
                                $em->persist(${"oDetallePagoCuenta" . $i});

                            }

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                if (!empty($arrayUnserializeFP)) {
                                    $eee = 3;
                                } else {
                                    $eee = 2;
                                }

                                for ($e = 1; $e <= $eee; $e++) {
                                    ${"oDocumentoPago" . $e} = new DocumentoPago();
                                    switch ($e) {
                                        case 1:
                                            $oFormasPagoForBonoElecronico = $oFormasPago;
                                            break;
                                        case 2:
                                            if ($MontoTotalseguroCom <= 0) {
                                                continue 2;
                                            }
                                            $oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
                                                ->findOneBy(array(
                                                    'idTipoFormaPago' => $this->parametro('FormaPagoTipo.SeguroComplementario'),
                                                    'idEmpresa' => $oEmpresa->getId()));
                                            break;
                                        case 3:

                                            if ($oInterfazImed) {
                                                if (!empty($arrayUnserializeFP)) {
                                                    foreach ($arrayUnserializeFP as $forma) {
                                                        if (intval($forma->CodForPag) == 6) {
                                                            $oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
                                                                ->findOneBy(array(
                                                                    'idTipoFormaPago' => $this->parametro('FormaPagoTipo.Excedente'),
                                                                    'idEmpresa' => $oEmpresa->getId()));
                                                        }
                                                    }
                                                }
                                            }
                                            break;
                                    }

                                    ${"oDocumentoPago" . $e}->setIdDetallePagoCuenta(${"oDetallePagoCuenta" . $e});
                                    ${"oDocumentoPago" . $e}->setIdPaciente($oPaciente);
                                    ${"oDocumentoPago" . $e}->setIdCaja($oCaja);
                                    ${"oDocumentoPago" . $e}->setGarantia($garantia);
                                    ${"oDocumentoPago" . $e}->setIdFormaPago($oFormasPagoForBonoElecronico);
                                    ${"oDocumentoPago" . $e}->setNumeroDocumentoGeneral($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData()); //Numero bono
                                    ${"oDocumentoPago" . $e}->setNumeroDocumento($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData()); //Numero bono
                                    ${"oDocumentoPago" . $e}->setFechaRecepcionDocumento($oFecha);
                                    ${"oDocumentoPago" . $e}->setIdSucursal($oSucursal);

                                    $aNumeroBono[] =  intval($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData());

                                    if ($auxAfecta || $auxExenta) {
                                        $auxSumaBonos = $auxSumaBonos +
                                            $MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData() +
                                            $MediosPagoform['copago_' . $idForm . '_' . $cant]->getData();
                                    }
                                    switch ($e) {
                                        case 1:
                                            ${"oDocumentoPago" . $e}->setMontoTotalDocumento($MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData());
                                            ${"oDocumentoPago" . $e}->setCopagoImed($MediosPagoform['copago_' . $idForm . '_' . $cant]->getData());
                                            break;
                                        case 2:
                                            ${"oDocumentoPago" . $e}->setMontoTotalDocumento($MediosPagoform['Seguro_' . $idForm . '_' . $cant]->getData());
                                            ${"oDocumentoPago" . $e}->setCopagoImed(0);
                                            break;
                                        case 3:
                                            ${"oDocumentoPago" . $e}->setMontoTotalDocumento($MediosPagoform['exedente_' . $idForm]->getData());
                                            ${"oDocumentoPago" . $e}->setCopagoImed(0);
                                            break;
                                    }
                                    $em->persist(${"oDocumentoPago" . $e});

                                }
                            }

                        }
                        //BONO MANUAL
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.BonoManual')) {
                            if ($auxAfecta || $auxExenta) {
                                $auxMedioPago = $auxMedioPago + 0;
                                $auxBonoCount = $auxBonoCount + 1;
                            } else {
                                $auxMedioPago = $auxMedioPago + 1;
                            }
                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();
                            $MontoTotalBonificacion = 0;
                            $MontoTotalseguroCom = 0;
                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                $MontoTotalBonificacion = $MontoTotalBonificacion +
                                    $MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData();
                                $MontoTotalseguroCom = $MontoTotalseguroCom +
                                    $MediosPagoform['Seguro_' . $idForm . '_' . $cant]->getData();;


                            }

                            if (!empty($arrayUnserializeFP)) {
                                $iii = 3;
                            } else {
                                $iii = 2;
                            }

                            for ($i = 1; $i <= $iii; $i++) {

                                ${"oDetallePagoCuenta" . $i} = new DetallePagoCuenta();
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////
                                switch ($i) {
                                    case 1:
                                        $oFormasPagoForBonoManual = $oFormasPago;
                                        ${"oDetallePagoCuenta" . $i}->setMontoPagoCuenta($MontoTotalBonificacion);
                                        break;
                                    case 2:
                                        if ($MontoTotalseguroCom <= 0) {
                                            continue 2;
                                        }
                                        $oFormasPagoForBonoManual = $em->getRepository('RebsolHermesBundle:FormaPago')
                                            ->findOneBy(array(
                                                'idTipoFormaPago' => $this->parametro('FormaPagoTipo.SeguroComplementario'),
                                                'idEmpresa' => $oEmpresa->getId()));
                                        ${"oDetallePagoCuenta" . $i}->setMontoPagoCuenta($MontoTotalseguroCom);
                                        break;
                                    case 3:
                                        $Exedente = 0;
//                                        if ($oInterfazImed) {
//                                            if (!empty($arrayUnserializeFP)) {
//                                                foreach ($arrayUnserializeFP as $forma) {
//                                                    if (intval($forma->CodForPag) == 6) {
//
//                                                        $Exedente = $forma->MtoTransac + $Exedente;
//                                                        $oFormasPagoForBonoElecronico = $em->getRepository('RebsolHermesBundle:FormaPago')
//                                                            ->findOneBy(array(
//                                                                'idTipoFormaPago' => $this->parametro('FormaPagoTipo.Excedente'),
//                                                                'idEmpresa' => $oEmpresa->getId()));
//                                                        ${"oDetallePagoCuenta" . $i}->setMontoPagoCuenta($Exedente);
//                                                    }
//                                                }
//                                            }
//
//                                        }
                                        break;
                                    case 4:
                                        //pendiente EFECTIVO
                                        $oFormasPagoForBonoManual = $em->getRepository('RebsolHermesBundle:FormaPago')->find($idForm);
                                        break;
                                }
                                ${"oDetallePagoCuenta" . $i}->setIdPagoCuenta($oPagoCuenta);
                                ${"oDetallePagoCuenta" . $i}->setIdFormaPago($oFormasPagoForBonoManual);
                                ${"oDetallePagoCuenta" . $i}->setGarantia($garantia);
                                ${"oDetallePagoCuenta" . $i}->setIdMoneda(NULL);
                                ${"oDetallePagoCuenta" . $i}->setIdPrevision($oFinanciador);
                                ${"oDetallePagoCuenta" . $i}->setIdConvenio($oConvenio);
                                ${"oDetallePagoCuenta" . $i}->setFechaDetallePago($oFecha);
                                ${"oDetallePagoCuenta" . $i}->setNombreEmpresa(NULL);
                                ${"oDetallePagoCuenta" . $i}->setCodigoControlFacturacion(0);
                                ${"oDetallePagoCuenta" . $i}->setIdMotivoGratuidad(NULL);
                                $em->persist(${"oDetallePagoCuenta" . $i});

                            }

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;
                                if (!empty($arrayUnserializeFP)) {
                                    $eee = 3;
                                } else {
                                    $eee = 2;
                                }

                                for ($e = 1; $e <= $eee; $e++) {
                                    ${"oDocumentoPago" . $e} = new DocumentoPago();
                                    switch ($e) {
                                        case 1:
                                            $oFormasPagoForBonoManual = $oFormasPago;
                                            break;
                                        case 2:
                                            if ($MontoTotalseguroCom <= 0) {
                                                continue 2;
                                            }
                                            $oFormasPagoForBonoManual = $em->getRepository('RebsolHermesBundle:FormaPago')
                                                ->findOneBy(array(
                                                    'idTipoFormaPago' => $this->parametro('FormaPagoTipo.SeguroComplementario'),
                                                    'idEmpresa' => $oEmpresa->getId()));
                                            break;
                                        case 3:

                                            if ($oInterfazImed) {
                                                if (!empty($arrayUnserializeFP)) {
                                                    foreach ($arrayUnserializeFP as $forma) {
                                                        if (intval($forma->CodForPag) == 6) {
                                                            $oFormasPagoForBonoManual = $em->getRepository('RebsolHermesBundle:FormaPago')
                                                                ->findOneBy(array(
                                                                    'idTipoFormaPago' => $this->parametro('FormaPagoTipo.Excedente'),
                                                                    'idEmpresa' => $oEmpresa->getId()));
                                                        }
                                                    }
                                                }
                                            }
                                            break;
                                    }

                                    ${"oDocumentoPago" . $e}->setIdDetallePagoCuenta(${"oDetallePagoCuenta" . $e});
                                    ${"oDocumentoPago" . $e}->setIdPaciente($oPaciente);
                                    ${"oDocumentoPago" . $e}->setIdCaja($oCaja);
                                    ${"oDocumentoPago" . $e}->setGarantia($garantia);
                                    ${"oDocumentoPago" . $e}->setIdFormaPago($oFormasPagoForBonoManual);
                                    ${"oDocumentoPago" . $e}->setNumeroDocumentoGeneral($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData()); //Numero bono
                                    ${"oDocumentoPago" . $e}->setNumeroDocumento($MediosPagoform['bono_' . $idForm . '_' . $cant]->getData()); //Numero bono
                                    ${"oDocumentoPago" . $e}->setFechaRecepcionDocumento($oFecha);
                                    ${"oDocumentoPago" . $e}->setIdSucursal($oSucursal);

                                    if ($auxAfecta || $auxExenta) {
                                        $auxSumaBonos = $auxSumaBonos +
                                            $MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData() +
                                            $MediosPagoform['copago_' . $idForm . '_' . $cant]->getData();
                                    }
                                    switch ($e) {
                                        case 1:
                                            ${"oDocumentoPago" . $e}->setMontoTotalDocumento($MediosPagoform['Bonificacion_' . $idForm . '_' . $cant]->getData());
                                            ${"oDocumentoPago" . $e}->setCopagoImed($MediosPagoform['copago_' . $idForm . '_' . $cant]->getData());
                                            break;
                                        case 2:
                                            ${"oDocumentoPago" . $e}->setMontoTotalDocumento($MediosPagoform['Seguro_' . $idForm . '_' . $cant]->getData());
                                            ${"oDocumentoPago" . $e}->setCopagoImed(0);
                                            break;
//                                        case 3:
//                                            ${"oDocumentoPago" . $e}->setMontoTotalDocumento($MediosPagoform['exedente_' . $idForm]->getData());
//                                            ${"oDocumentoPago" . $e}->setCopagoImed(0);
//                                            break;
                                    }
                                    $em->persist(${"oDocumentoPago" . $e});

                                }
                            }
                        }

                        //TRANSBANK
                        if ($oFormasPagoTipo === $this->parametro('FormaPagoTipo.Transbank')) {
                            $auxMedioPago = $auxMedioPago + 0;
                            $maxCantidad = $MediosPagoform['dinamico_' . $idForm]->getData();

                            for ($i = 1; $i <= $maxCantidad; $i++) {
                                $cant = $i - 1;

                                $oDetallePagoCuenta = new DetallePagoCuenta();

                                $oDetallePagoCuenta->setIdPagoCuenta($oPagoCuenta);
                                $oDetallePagoCuenta->setIdFormaPago($oFormasPago);
                                $oDetallePagoCuenta->setGarantia($garantia);
                                //$oDetallePagoCuenta->setMontoPagoCuenta($oPagoCuenta->getMonto());
                                $oDetallePagoCuenta->setMontoPagoCuenta($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oDetallePagoCuenta->setIdPrevision($oFinanciador);
                                $oDetallePagoCuenta->setIdConvenio($oConvenio);
                                $oDetallePagoCuenta->setFechaDetallePago($oFecha);
                                $oDetallePagoCuenta->setCodigoControlFacturacion(0);

                                $em->persist($oDetallePagoCuenta);

                                $oDocumentoPago = new DocumentoPago();
                                $oDocumentoPago->setIdDetallePagoCuenta($oDetallePagoCuenta);
                                $oDocumentoPago->setIdPaciente($oPaciente);
                                $oDocumentoPago->setIdCaja($oCaja);
                                $oDocumentoPago->setGarantia($garantia);
                                $oDocumentoPago->setIdFormaPago($oFormasPago);
                                $oDocumentoPago->setNumeroDocumentoGeneral(0); //Numero bono
                                $oDocumentoPago->setFechaRecepcionDocumento($oFecha);
                                $oDocumentoPago->setMontoTotalDocumento($MediosPagoform['monto_' . $idForm . '_' . $cant]->getData());
                                $oTarjetaCredito = $em->getRepository('RebsolHermesBundle:TarjetaCredito')
                                    ->findOneBy(array('abreviacion' => $MediosPagoform['nombreTarjeta_' . $idForm . '_' . $cant]->getData()));
                                $oDocumentoPago->setIdTarjetaCredito($oTarjetaCredito);
                                $oDocumentoPago->setNumeroVoucher($MediosPagoform['voucher_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setCodAutorizacion($MediosPagoform['codAutorizacion_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setUltimos4Numeros($MediosPagoform['ultimos4Numeros_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setTarjetaTipo($MediosPagoform['tarjetaTipo_' . $idForm . '_' . $cant]->getData());
                                $oDocumentoPago->setIdSucursal($oSucursal);

                                $em->persist($oDocumentoPago);
                            }
                        }

                    }
                }

            }
        }

        //buscar interfaz imed
        if($oInterfazImed){
            $oInterfazImed->setIdPagoCuenta($oPagoCuenta);
            $em->persist($oInterfazImed);

            $aBonoDetalle = $em->getRepository('RebsolHermesBundle:BonoDetalle')->findBy(array('folioBono' => $aNumeroBono));
            foreach ($aBonoDetalle as $oBonoDetalle) {
                $oBonoDetalle->setIdPagoCuenta($oPagoCuenta);
                $em->persist($oBonoDetalle);
            }
        }

        $em->flush();

        ////////////////////////////////////////////////////////////////////////////////////
        //BOLETAS
        ////////////////////////////////////////////////////////////////////////////////////

        $this->killSession('detalletalonario');

        if ($countEmisionBoleta > 0) {
            $emiteBoletaTrueFalse = true;
        } else if ($countEmisionBoleta == 0 and $auxEmisionBoleta > 0) {
            $emiteBoletaTrueFalse = false;
        } else if ($countEmisionBoleta == 0 and $auxEmisionBoleta == 0) {
            $emiteBoletaTrueFalse = false;
        }

        /*
         * Se eliminan estas líneas ya que la totalización del valor del paquete
         * se agrega en  un registro de accion_clinica_paciente y se registra como una prestación.
         */
        /*if ($PacienteGarantia) {
            $montoTotalCuentaPaquetizado = $oCuentaPaciente->getTotalCuentaPaquetizado();
        }*/

        $folioGlobal = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');
        $folio = null;

        if ($auxMedioPago == 0 and $emiteBoletaTrueFalse == true) {
            if ($auxAfecta && $montoArticulos > 0) {
                if (count($arrAuxiliarPrestacionesA) > 0) {
                    if ($folioGlobal['valor'] === '0') {
                        foreach ($arrAuxiliarPrestacionesA as $key => $value) {

                            $sTalonario = $this->rPagoCuenta()->RelUbicacionCajero($oSucursal,
                                $oEmpresa,
                                $oUbicacionCajero,
                                $EstadoActivo,
                                $EstadoPilaActiva,
                                $oBoletaAfecta,
                                $oBoletaExenta,
                                $key);

                            if ($sTalonario) {
                                $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($sTalonario['talonario']);
                                $arrTalonarioId = array();
                                $arrAux = array();
                                $arrAux['id'] = $oTalonario->getId();
                                $arrAux['idNombreArray'] = $oTalonario->getIdSubEmpresa()->getId() .
                                    $oTalonario->getid() .
                                    $oTalonario->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
                                $arrAux['idSubEmpresa'] = $oTalonario->getIdSubEmpresa()->getId();
                                $arrAux['idTipoDocumento'] = $oTalonario->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
                                $arrAux['actual'] = $oTalonario->getNumeroActual();
                                $arrTalonarioId[] = $arrAux;
                                $TalonarioNumeroActual = $this->rCaja()->GetNumeroActualSinAnulacionTalonario($arrTalonarioId,
                                    $this->container->getParameter('EstadoDetalleTalonario.anulada'),
                                    $em);


                                foreach ($TalonarioNumeroActual as $nt) {
                                    $correlativo = ($nt['idTalonario'] == $oTalonario->getId()) ? $nt['numeroDocumento'] : null;
                                }

                                $oDetalleTalonario = new DetalleTalonario();
                                $valorConArregloEmisionBoletaResta = intval($value) - intval($montoRestaBoletaMedioPagoNoEmiteBoleta);
                                $oDetalleTalonario->setNumeroDocumento($correlativo);
                                $oDetalleTalonario->setMonto($valorConArregloEmisionBoletaResta);
                                $oDetalleTalonario->setFechaDetalleBoleta($oFecha);
                                $oDetalleTalonario->setIdPaciente($oPaciente);
                                $oDetalleTalonario->setIdCaja($oCaja);
                                $oDetalleTalonario->setIdTalonario($oTalonario);
                                $oDetalleTalonario->setIdEstadoDetalleTalonario($EstadoBoletaActiva);
                                $oDetalleTalonario->setIdUsuarioDetalleBoleta($oUser);
                                $oDetalleTalonario->setIdPagoCuenta($oPagoCuenta);


                                $em->persist($oDetalleTalonario);

                                $correlativo = $correlativo + 1;


                                $oTalonario->setNumeroActual($correlativo);

                                $em->persist($oTalonario);

                                $em->flush();

                                if ($oTalonario->getNumeroActual() >= $oTalonario->getNumeroTermino()) {
                                    $oTalonario->setIdEstadoPila($this->estado('EstadoPilaInaciva'));

                                    $em->persist($oTalonario);

                                    $em->flush();
                                }


                                $boletaAfecta = $oDetalleTalonario->getid();

                            }


                        }
                    } else {
                        $folioReservados = $this->getSession('folioReservados');
                        if (!empty($folioReservados)) {
                            $estadoFolioOcupada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                                ->find($this->container->getParameter('EstadoTalonarioDetalle.ocupada'));

                            foreach ($folioReservados as $key => $value) {
                                $folio = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')->find($value);
                                $folio->setIdEstadoTalonarioDetalle($estadoFolioOcupada);
                                $folio->setIdPaciente($oPaciente);
                                $folio->setIdPagoCuenta($oPagoCuenta);

                                //monto se busca comparando la subempresa de folio ingresada al momento de ingresar la prestacion
                                //y la subempresa al momento del pago
                                foreach ($arrAuxiliarPrestacionesA as $key2 => $value2) {
                                    if ($folio->getIdSubEmpresaFacturadora() === $key2) {
                                        $folio->setMonto($arrAuxiliarPrestacionesA[$key2]);
                                    }
                                }

                                $folio->setIdUsuarioDetalleBoleta($oUser);
                                $folio->setFechaDetalleBoleta($oFecha);
                                $em->persist($folio);

                                //Revisar si existen están todos los folios ocupados dejar el talonario como inactivo
                                $folioSinOcupar = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                                    ->obtenerFoliosSinOcupar($folio->getIdTalonario(), $estadoFolioOcupada);
                                // es 1 ya que aún no pasa a estado ocupado hasta que se haga el flush
                                if (count($folioSinOcupar) === 1) {
                                    $talonario = $em->getRepository('RebsolHermesBundle:Talonario')
                                        ->find($folio->getIdTalonario());
                                    $estadoInactivo = $em->getRepository('RebsolHermesBundle:EstadoPila')
                                        ->find($this->container->getParameter('estado_inactivo'));
                                    $talonario->setIdEstadoPila($estadoInactivo);
                                    $em->persist($talonario);
                                }
                            }

                            $em->flush();
                            $this->killSession('folioReservados');
                        }
                    }
                }
            }
            if ($auxExenta && $auxBonoCount == 0 && $montoPrestaciones > 0) {
                if (count($arrAuxiliarPrestaciones) > 0) {
                    if ($folioGlobal['valor'] === '0') {
                        foreach ($arrAuxiliarPrestaciones as $key => $value) {

                            $sTalonario = $this->rPagoCuenta()->RelUbicacionCajero($oSucursal,
                                $oEmpresa,
                                $oUbicacionCajero,
                                $EstadoActivo,
                                $EstadoPilaActiva,
                                $oBoletaAfecta,
                                $oBoletaExenta,
                                $key);


                            if ($sTalonario) {
                                $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($sTalonario['talonario']);
                                $arrTalonarioId = array();
                                $arrAux = array();
                                $arrAux['id'] = $oTalonario->getId();
                                $arrAux['idNombreArray'] = $oTalonario->getIdSubEmpresa()->getId() .
                                    $oTalonario->getid() .
                                    $oTalonario->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
                                $arrAux['idSubEmpresa'] = $oTalonario->getIdSubEmpresa()->getId();
                                $arrAux['idTipoDocumento'] = $oTalonario->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
                                $arrAux['actual'] = $oTalonario->getNumeroActual();
                                $arrTalonarioId[] = $arrAux;

                                $TalonarioNumeroActual = $this->rCaja()->GetNumeroActualSinAnulacionTalonario($arrTalonarioId,
                                    $this->container->getParameter('EstadoDetalleTalonario.anulada'),
                                    $em);

                                foreach ($TalonarioNumeroActual as $nt) {
                                    $correlativo = ($nt['idTalonario'] == $oTalonario->getId()) ? $nt['numeroDocumento'] : null;
                                }
                                $oDetalleTalonario = new DetalleTalonario();
                                $valorConArregloEmisionBoletaResta = intval($value) - intval($montoRestaBoletaMedioPagoNoEmiteBoleta);
                                $oDetalleTalonario->setNumeroDocumento($correlativo);
                                $oDetalleTalonario->setMonto($valorConArregloEmisionBoletaResta);
                                $oDetalleTalonario->setFechaDetalleBoleta($oFecha);
                                $oDetalleTalonario->setIdPaciente($oPaciente);
                                $oDetalleTalonario->setIdCaja($oCaja);
                                $oDetalleTalonario->setIdTalonario($oTalonario);
                                $oDetalleTalonario->setIdEstadoDetalleTalonario($EstadoBoletaActiva);
                                $oDetalleTalonario->setIdUsuarioDetalleBoleta($oUser);
                                $oDetalleTalonario->setIdPagoCuenta($oPagoCuenta);

                                $em->persist($oDetalleTalonario);

                                $correlativo = $correlativo + 1;
                                $oTalonario->setNumeroActual($correlativo);
                                $em->persist($oTalonario);

                                $em->flush();

                                if ($oTalonario->getNumeroActual() >= $oTalonario->getNumeroTermino()) {
                                    $oTalonario->setIdEstadoPila($this->estado('EstadoPilaInaciva'));

                                    $em->persist($oTalonario);

                                    $em->flush();
                                }

                                $boletaExenta = $oDetalleTalonario->getid();
                            }
                        }
                    } else {

                        $folioReservados = $this->getSession('folioReservados');
                        $estadoFolioOcupada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                            ->find($this->container->getParameter('EstadoTalonarioDetalle.ocupada'));
                        $estadoDisponible = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                            ->find($this->container->getParameter('EstadoTalonarioDetalle.disponible'));

                        if (is_array($folioReservados)) {
                            $folioAfecto = array();
                            $folioExento = array();
                            foreach ($folioReservados as $folioReservado) {
                                switch ($folioReservado['codigoSii']) {
                                    case '39':
                                        $folioAfecto = array($folioReservado['codigoSii'] => $folioReservado['folioDisponible']);
                                        break;
                                    case '41':
                                        $folioExento = array($folioReservado['codigoSii'] => $folioReservado['folioDisponible']);
                                        break;
                                }
                            }
                            $foliosAOcupar = $folioAfecto + $folioExento;

                            if ($oFinanciador->getTipoPrestacion() !== null) {
                                if (!empty($foliosAOcupar)) {

                                    $foliosOcupados = array();
                                    $i = 0;
                                    $montoAfecto = 0;
                                    $montoExento = 0;
                                    $esAfecta = $esExenta = false;
                                    //Se separan las totales de prestaciones afectas y exentas
                                    foreach ($ListaPrestacion as $prestacion) {

                                        $montoPrestacion = isset($arrDiferencias) ? $this->obtenerDiferencias($arrDiferencias, $prestacion) : (intval($prestacion['2'])*intval($prestacion['1']));
                                        $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($prestacion['0']);
                                        if ($oAccionClinica->getEsAfecto() === true) {
                                            $montoAfecto = $montoAfecto + $montoPrestacion;
                                            $esAfecta = true;
                                        } else if ($oAccionClinica->getEsAfecto() === false) {
                                            $montoExento = $montoExento + $montoPrestacion;
                                            $esExenta = true;
                                        }
                                    }

                                    foreach ($foliosAOcupar as $key => $value) {
                                        $folio = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')->find($value);
                                        $folio->setIdEstadoTalonarioDetalle($estadoFolioOcupada);
                                        $folio->setIdPaciente($oPaciente);
                                        $folio->setIdPagoCuenta($oPagoCuenta);

                                        //si $esAfecta ó $esExenta = true entonces se ocupa el folio
                                        $tieneMonto = false;
                                        if ($esAfecta === true && $key === 39) {
                                            $folio->setMonto($montoAfecto);
                                            $tieneMonto = true;
                                        } else if ($esExenta === true && $key === 41) {
                                            $folio->setMonto($montoExento);
                                            $tieneMonto = true;
                                        }


                                        if ($tieneMonto === true) {
                                            $folio->setIdUsuarioDetalleBoleta($oUser);
                                            $folio->setFechaDetalleBoleta($oFecha);

                                            $foliosOcupados[$i]['folio'] = $folio;
                                            $foliosOcupados[$i]['codigoSii'] = $key;
                                            $i++;

                                            $em->persist($folio);

                                            //Revisar si existen están todos los folios ocupados dejar el talonario como inactivo
                                            $folioSinOcupar = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                                                ->obtenerFoliosSinOcupar($folio->getIdTalonario(), $estadoFolioOcupada);
                                            // es 1 ya que aún no pasa a estado ocupado hasta que se haga el flush
                                            if (count($folioSinOcupar) === 1) {
                                                $talonario = $em->getRepository('RebsolHermesBundle:Talonario')
                                                    ->find($folio->getIdTalonario());
                                                $estadoInactivo = $em->getRepository('RebsolHermesBundle:EstadoPila')
                                                    ->find($this->container->getParameter('estado_inactivo'));
                                                $talonario->setIdEstadoPila($estadoInactivo);
                                                $em->persist($talonario);
                                            }
                                        } else {
                                            //Se libera el folio
                                            $folio->setMonto(null);
                                            $folio->setIdSubEmpresaFacturadora(null);
                                            $folio->setIdPaciente(null);
                                            $folio->setIdEstadoTalonarioDetalle($estadoDisponible);
                                            $folio->setIdUsuarioDetalleBoleta(null);
                                            $folio->setIdPagoCuenta(null);
                                            $em->persist($folio);
                                        }
                                    }
                                    $em->flush();
                                    $this->killSession('folioReservados');

//                                    return new JsonResponse(array($foliosAOcupar));

                                }
                            }
                        }
                    }
                }
            }
        }


        ////////////////////////////////////////////////////////////////////////////////////
        //FIN BOLETAS
        ////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////////////////////
        //CONCLUSION PAGO
        ////////////////////////////////////////////////////////////////////////////////////

        if ($oDetalleTalonario) {


            if (count($arrAuxiliarPrestaciones) < 2 && count($arrAuxiliarPrestacionesA) < 2) {
                if ($boletaExenta && !$boletaAfecta) {
                    $this->setSession('detalletalonario', $boletaExenta);
                    $session->getFlashBag()->add('detalletalonarioMensaje', $boletaExenta);
                    $this->setSession('PacienteBloteas', $oPaciente->getId());
                    $respuesta = 1;
                }
                if ($boletaAfecta && !$boletaExenta) {
                    $this->setSession('detalletalonario', $boletaAfecta);
                    $session->getFlashBag()->add('detalletalonarioMensaje', $boletaAfecta);
                    $this->setSession('PacienteBloteas', $oPaciente->getId());
                    $respuesta = 1;
                }

                if ($boletaExenta && $boletaAfecta) {
                    $this->setSession('detalletalonario', "no");
                    $session->getFlashBag()->add('detalletalonarioMensaje', 'no');
                    $this->setSession('PacienteBloteas', $oPaciente->getId());
                    $respuesta = "varios";
                }
            } else {
                if ((count($arrAuxiliarPrestacionesA) > 1 && count($arrAuxiliarPrestaciones) > 1) || (count($arrAuxiliarPrestacionesA) > 1 && count($arrAuxiliarPrestaciones) <= 1) || (count($arrAuxiliarPrestaciones) > 1 && count($arrAuxiliarPrestacionesA) <= 1) || ($boletaAfecta && $boletaExenta)) {
                    $this->setSession('detalletalonario', "no");
                    $session->getFlashBag()->add('detalletalonarioMensaje', 'no');
                    $this->setSession('PacienteBloteas', $oPaciente->getId());
                    $respuesta = "varios";
                }

            }
        }

        if (!$oDetalleTalonario) {
            $this->setSession('detalletalonario', "no");
            $session->getFlashBag()->add('detalletalonarioMensaje', 'no');
            $respuesta = "pagado";
        }
        //ACTUALIZA TABLA IMED

        if ($oInterfazImed) {
            $oInterfazImed->setIdPaciente($oPaciente->getId());
            $em->persist($oInterfazImed);
            $em->flush();
        }

        /**
         * CREAR MEETING UNA VEZ CONFIRMADO EL PAGO
         */
        $idModulo = $this->container->getParameter('modulo_caja');
        $estadoApi = $this->obtenerApiModulo($idModulo);

        if (isset($estadoApi['rutaApi']) && $estadoApi['rutaApi'] === 'ApiPV') {

            if ($idReservaAtencion) {
                $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
                $oHorarioConsulta = $oReservaAtencion->getIdHorarioConsulta();
                $esTeleConsulta = $oHorarioConsulta->getEsTeleconsulta();
                if ($oHorarioConsulta->getEsTeleconsulta()) {
                    $fechaInicio = $oHorarioConsulta->getFechaInicioHorario();
                    $fechaInicioString = $fechaInicio->format('Y-m-d');
                    $horaString = $fechaInicio->format('H:i:s');
                    $fechaFinal = $fechaInicioString . "T" . $horaString . "-03:00";
                    $oUsuarioRebsol = $oPaciente->getIdProfesional();
                    $oPersona = $oUsuarioRebsol->getIdPersona();
                    $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(['idPersona' => $oPersona]);
                    $curl = curl_init();

                    $usuarioZoom = $this->container->getParameter('ApiZoom.User');
                    $passZoom = $this->container->getParameter('ApiZoom.Password');
                    $urlApi = $this->container->getParameter('ApiZoom.Url');
                    $params['topic'] = "Su cita con " . $oPnatural->getNombrePnatural() . " " . $oPnatural->getApellidoPaterno() . " " . $oPnatural->getApellidoMaterno() . " para " . $oReservaAtencion->getIdEspecialidadMedica()->getNombreEspecialidadMedica();
                    $params['start_time'] = $fechaFinal;
                    $params['duration'] = $oHorarioConsulta->getDuracionConsulta();
                    $params['user'] = $oUsuarioRebsol->getZoomUser();
                    $params['email'] = $oReservaAtencion->getCorreoElectronico();
                    $params['send_email'] = true;

                    $params_json = json_encode($params);
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $urlApi . "Meeting/CreateMeeting",
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

                    if ($response) {
                        $responseJson = json_decode($response);
                        $oReservaAtencion->setUrlMasterZoom($responseJson->masterUrl);
                        $oReservaAtencion->setMeetingId($responseJson->meetingId);
                        $em->persist($oReservaAtencion);
                        $em->flush();
                    } else {

                    }
                }
            }
        }
        /** */


        ////////////////////////////////////////////////////////////////////////////////////
        //FIN CONCLUSION PAGO
        ////////////////////////////////////////////////////////////////////////////////////

        $idUsuarioLogin = $this->get('session')->get('idUsuarioLogin');

        $arrPrestacionesRis = [];
        $arrPrestacionesLis = [];
        foreach ($ListaPrestacion as $prestacion) {
            if (intval($prestacion['3']) == 1 && ($prestacion['4'] === "" || is_null($prestacion['4']))) {

                $idPrestacion = intval($prestacion['0']);
                $cantidad = intval($prestacion['1']);
                $cobro = intval($prestacion['2']);
                $oPrestacionBD = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
                $oTipoPrestacion = $oPrestacionBD->getIdTipoPrestacion();

                if ($oTipoPrestacion->getTipoTipoPrestacion() === 'RIS') {
                    $arrPrestacionesRis[] = $prestacion['0'];
                }

                if ($oTipoPrestacion->getTipoTipoPrestacion() === 'LIS') {
                    $arrPrestacionesLis[] = $prestacion['0'];
                }
            }
        }

        //$actualizaOrden  = $this->get('agenda.DacionHoras.ReservaAtencionLog')->actualizarOrdenDesdeCaja($idReservaAtencion, $em, $idUsuarioLogin);

        if ($idReservaAtencion) {

            $oReserva = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
            $oHorarioConsulta = $oReserva->getIdHorarioConsulta();
            $esTeleConsulta = $oHorarioConsulta->getEsTeleconsulta();
            $oUsuarioRebsol = $oHorarioConsulta->getIdUsuario();

            $this->get('Ris_Lis')->procesarRisLisCaja(
                [
                    'listaPrestaciones' => $ListaPrestacion,
                    'idPersona' => $oPnatural->getIdPersona()->getId(),
                    'idUser' => $this->getUser(),
                    'idPagoCuenta' => $oPagoCuenta->getId(),
                    'accionesClinicaFaltantes' => $accionesClinicaFaltantes,
                    'idUsuarioAgendamiento' => $oReserva->getIdUsuarioFuncionario()->getId(),
                    'nombreSala' => $oUsuarioRebsol->getNombreUsuario(),
                    'idReservaAtencion' => $idReservaAtencion
                ]
            );

            $parametrosProcedimientos = [
                'idReservaAtencion' => $idReservaAtencion,
                'idUsuarioLogin' => $idUsuarioLogin,
                'idPaciente' => $oPaciente->getId(),
                'idPagoCuenta' => $oPagoCuenta->getId(),
                'datosProfesional' => $datosProfesional
            ];

            if (count($arrPrestacionesLis) > 0) {
                //CASO RIS
                $parametrosProcedimientos['ListaPrestaciones'] = $arrPrestacionesLis;
                $parametrosProcedimientos['tipoPrestacion'] = 'LIS';
                $this->get('Ris_Lis')->ingresarOrdenProcedimientos($parametrosProcedimientos);
            }

            if (count($arrPrestacionesRis) > 0) {
                //CASO RIS
                $parametrosProcedimientos['ListaPrestaciones'] = $arrPrestacionesRis;
                $parametrosProcedimientos['tipoPrestacion'] = 'RIS';
                $this->get('Ris_Lis')->ingresarOrdenProcedimientos($parametrosProcedimientos);
            }

        } else {
            $this->get('Ris_Lis')->procesarRisLisCaja(
                [
                    'listaPrestaciones' => $ListaPrestacion,
                    'idPersona' => $oPnatural->getIdPersona()->getId(),
                    'idUser' => $this->getUser(),
                    'idPagoCuenta' => $oPagoCuenta->getId(),
                    'accionesClinicaFaltantes' => $accionesClinicaFaltantes
                ]
            );

            $oUsuarioIntegracion = $this->getUsuarioIntegracionByEmpresa($oEmpresa->getId());
            $parametrosExterno = [
                'idPersona' => $oPnatural->getIdPersona()->getId(),
                'idPaciente' => $oPaciente->getId(),
                'usuarioTransaccion' => $this->getUser(),
                'idPagoCuenta' => $oPagoCuenta->getId(),
                'idEmpresa' => $oEmpresa->getId(),
                'nombreSala' => null,
                'idReservaAtencion' => null,
                'datosProfesional' => $datosProfesional,
                'usuarioIntegracion' => $oUsuarioIntegracion
            ];

            if (count($arrPrestacionesLis) > 0) {
                //CASO LIS
                $parametrosExterno['listaPrestaciones'] = $arrPrestacionesLis;
                $parametrosExterno['tipoPrestacion'] = 'LIS';
                $this->get('Ris_Lis')->crearOrdenExterna($parametrosExterno);
            }

            if (count($arrPrestacionesRis) > 0) {
                //CASO RIS
                $parametrosExterno['listaPrestaciones'] = $arrPrestacionesRis;
                $parametrosExterno['tipoPrestacion'] = 'RIS';
                $this->get('Ris_Lis')->crearOrdenExterna($parametrosExterno);
            }


        }


        $this->clearSesionVar();
        ////////////////////////////////////////////////////////////////////////////////////
        //LIMPIA VARIABLES DE SESSION
        ////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////////////////////
        //FIN LIMPIA VARIABLES DE SESSION
        ////////////////////////////////////////////////////////////////////////////////////

        /*if ($oTalonario) {
            $arrTalonario = array();
            foreach ($oTalonario as $t) {
                $arrTalonario[] = $t->getId();
            }
            $this->setSession('idTalonario', $arrTalonario);
        }*/

        if ($oTalonario) {
            if (is_array($oTalonario)) {
                $arrTalonario = array();
                foreach ($oTalonario as $t) {
                    $arrTalonario[] = $t->getId();
                }
                $this->setSession('idTalonario', $arrTalonario);
            } else {
                $arrTalonario = array();
                $arrTalonario[] = $oTalonario->getId();
                $this->setSession('idTalonario', $arrTalonario);

            }
        }

        $idUsuarioLogin = $this->get('session')->get('idUsuarioLogin');
        $actualizaOrden = $this->get('agenda.DacionHoras.ReservaAtencionLog')->actualizarOrdenDesdeCaja($idReservaAtencion, $em, $idUsuarioLogin);

        $adecom = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametrosAdecom($this->ObtenerEmpresaLogin());
        if (isset($adecom[ConexionAdecomConstant::ADECOM_WS]) && $adecom[ConexionAdecomConstant::ADECOM_WS] !== '0') {
            //Array de Prestaciones para el select where IN
            $listaPrestaciones = array();
            foreach ($ListaPrestacion as $value) {
                $listaPrestaciones[] = intval($value[0]);
            }
            $listaSubEmpresaFacturadora = explode(",", $adecom[ConexionAdecomConstant::SUB_EMPRESA_FACTURA]);
            //Obtener lista de prestaciones con subempresas facturadoras
            $listaPrestacionesFacturadoras = $em->getRepository('RebsolHermesBundle:AccionClinica')
                ->obtenerSubEmpresaFacturadora($listaPrestaciones, $listaSubEmpresaFacturadora, $ListaPrestacion);
            $enviarDTE = null;
            if (!empty($listaPrestacionesFacturadoras) && $listaPrestacionesFacturadoras !== null) {
                $dteService = $this->get('dteservice');
                $enviarDTE = $dteService->enviarDTE($listaPrestacionesFacturadoras);
            }
            if ($enviarDTE) $this->actualizarPagoCuentaDetalle($enviarDTE['boleta'], $oPagoCuenta, $oPaciente);
        }

        $aces = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametrosAces($this->ObtenerEmpresaLogin());

        if (isset($aces[ConexionAcesConstant::ACES_WS]) && $aces[ConexionAcesConstant::ACES_WS] !== '0' && isset($foliosOcupados)
            /*&& $oFinanciador->getTipoPrestacion() === 1*/) {
            //Array de Prestaciones para el select where IN
            $listaPrestaciones = array();
            foreach ($ListaPrestacion as $value) {
                $listaPrestaciones[] = intval($value[0]);
            }
            $listaSubEmpresaFacturadora = explode(",", $aces[ConexionAcesConstant::SUB_EMPRESA_FACTURA]);
            //Obtener lista de prestaciones con subempresas facturadoras
            $listaSubEmpresaFacturadora = array_map('intval', $listaSubEmpresaFacturadora);

            $listaPrestacionesFacturadoras = $em->getRepository('RebsolHermesBundle:AccionClinica')
                ->obtenerSubEmpresaFacturadora($listaPrestaciones, $listaSubEmpresaFacturadora, $ListaPrestacion);

            if (!empty($listaPrestacionesFacturadoras) && $listaPrestacionesFacturadoras !== null) {

                $dteService = $this->get('dteAcesService');
                $aDiferencias = isset($arrDiferencias) ? $arrDiferencias : null;

                $dataEnviarDte = array(
                    'listaPrestaciones' => $listaPrestacionesFacturadoras,
                    'oPaciente' => $oPaciente,
                    'idCaja' => $oCaja->getId(),
                    'foliosOcupados' => $foliosOcupados,
                    'oEmpresa' => $oEmpresa,
                    'aDiferencias' => $aDiferencias
                );

                $enviarDTE = $dteService->enviarDTE($dataEnviarDte);

                $boletasDTEs = $this->actualizarPagoCuentaDetalle($enviarDTE['boleta'], $oPagoCuenta, $oPaciente);

                if (!$boletasDTEs['respuesta']) {
                    if (isset($oHorarioConsulta) && $oHorarioConsulta->getEsTeleconsulta()){
                        $this->crearMeetingZoom($em, $oReservaAtencion->getIdHorarioConsulta(), $oReservaAtencion);
                    }
                    return new JsonResponse(array('mensaje' => 'pagadoConErroresGeneracionDte', 'data' => $boletasDTEs['data']));
                }
            }
        }
        if ($respuesta == "pagado"  && isset($oHorarioConsulta) && $oHorarioConsulta->getEsTeleconsulta()) {
            $this->crearMeetingZoom($em, $oReservaAtencion->getIdHorarioConsulta(), $oReservaAtencion);
        }

        $esCuentaAbierta = $this->verificarCompletitudPrePagos($oPnatural->getId(), $oCuentaPaciente->getId());
        $oCuentaPacienteLog = $em->getRepository('RebsolHermesBundle:CuentaPacienteLog')->findBy(
            array(
                'idPaciente' => $PacienteGarantia,
                'idEstadoCuenta' =>  $this->container->getParameter('EstadoCuenta.cerradaRevisionInterna')
            )
        );

        if($esCuentaAbierta){
            if($oCuentaPacienteLog){
                $oCuentaPaciente->setIdEstadoCuenta($EstadoCerradaPendientePago);
                if ($this->verificarCompletitudAccionClinicaPaciente($oPaciente, $oCuentaPaciente->getId())) {
                    $oCuentaPaciente->setIdEstadoCuenta($EstadoCerradaRevisionInterna);
                }
            }else{
                $oCuentaPaciente->setIdEstadoCuenta($EstadoAbiertaPendientePago);
                if (!$this->verificarCompletitudAccionClinicaPaciente($oPaciente, $oCuentaPaciente->getId())) {
                    $oCuentaPaciente->setIdEstadoCuenta($EstadoAbiertaPagadaTotal);
                }
            }
        }else{
            //Buscar si ha sido cerradaRevisionInterna
            if($oCuentaPacienteLog){
                $oCuentaPaciente->setIdEstadoCuenta($EstadoCerradaPagadaTotal);
                if ($this->verificarCompletitudAccionClinicaPaciente($oPaciente, $oCuentaPaciente->getId())) {
                    $oCuentaPaciente->setIdEstadoCuenta($EstadoCerradaRevisionInterna);
                }
            }else{
                $oCuentaPaciente->setIdEstadoCuenta($EstadoAbiertaPagadaTotal);
            }
        }

        $em->persist($oCuentaPaciente);
        $em->flush();

        return new JsonResponse(array('mensaje' => $respuesta, 'data' => isset($boletasDTEs['data']) ? $boletasDTEs['data'] : null));
        ////////////////////////////////////////////////////////////////////////////////////
        // FIN PAGO
        ////////////////////////////////////////////////////////////////////////////////////
    }

    /**
     * @param $dteBoletaExenta
     * Función que actualiza el Pago de Cuenta una vez enviado el pago a ADECOM
     */
    public function actualizarPagoCuentaDetalle($boletas, $oPagoCuenta, $oPaciente)
    {
        $em = $this->getDoctrine()->getManager();
        $aPagosRealizados = array();
        $sinerrores = true;
        foreach ($boletas as $boleta) {
            $oPagoCuentaDetalle = new PagoCuentaDetalle();
            if ($boleta['detalle']['error'] !== '') {

                $oPagoCuentaDetalle->setEnviadoDte(false);
                $oPagoCuentaDetalle->setDetalleDte($boleta['detalle']);
                $oPagoCuentaDetalle->setIdPagoCuenta($oPagoCuenta);
                $oPagoCuentaDetalle->setConsultaUrlProd(false);

                ($boleta['dataPendiente'] !== '') ? $oPagoCuentaDetalle->setDataPendiente(serialize($boleta['dataPendiente'])) : $oPagoCuentaDetalle->setDataPendiente('');

                $em->persist($oPagoCuentaDetalle);
                $em->flush();

                $this->generarLog($boleta);

                //agrego dataPendiente a $aPagosRealizados
                $aPagosRealizados['dataPendiente'][] = $oPagoCuentaDetalle->getId();
                $aPagosRealizados['urlDte'][] = null;
                $aPagosRealizados['urlProdDte'][] = null;

                $sinerrores = false;
            } else {
                $oPagoCuentaDetalle->setEnviadoDte(true);
                $oPagoCuentaDetalle->setUrlDte($boleta['urlDte']);
                $oPagoCuentaDetalle->setUrlProdDte($boleta['urlProdDte']);
                $oPagoCuentaDetalle->setIdPagoCuenta($oPagoCuenta);
                $oPagoCuentaDetalle->setConsultaUrlProd(false);
                $em->persist($oPagoCuentaDetalle);
                $em->flush();

                $this->generarLog($boleta);

                //agrego url a $aPagosRealizados
                $aPagosRealizados['urlDte'][] = $boleta['urlDte'];
                $aPagosRealizados['urlProdDte'][] = $boleta['urlProdDte'];
                $aPagosRealizados['dataPendiente'][] = null;

            }
        }
        $aPagosRealizados['idPago'] = $oPagoCuenta->getId();
        $aPagosRealizados['evento'] = $oPaciente->getEvento();
        $aResultPagosRealizados = array('respuesta' => $sinerrores, 'data' => $aPagosRealizados);

        return $aResultPagosRealizados;

    }

    /**
     * caja Pagar Api
     */
    public function cajaPagarApiAction()
    {
        $idReservaAtencion = $this->ajax('idReservaAtencion');
        return new Response($this->guardarPago($idReservaAtencion));
    }

    public function guardarPago($id)
    {
        $em = $this->getDoctrine()->getManager();

        $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($id);

        if (null != $oReservaAtencion->getIdPaciente()) {
            if (null === $oReservaAtencion->getIdPagoCuenta()) {

                $oPagoCuenta = new PagoCuenta();
                $oPaciente = $em->getRepository('RebsolHermesBundle:Paciente')->find($oReservaAtencion->getIdPaciente()->getId());

                $oPagoCuenta->setIdPaciente($oPaciente);
                $oFecha = new \Datetime();
                $oPagoCuenta->setFechaPago($oFecha);
                $oPagoCuenta->setFechaAPago($oFecha);
                $oPagoCuenta->setFechaAnulacion($oFecha);
                $oPagoCuenta->setCuota(3);
                $oPagoCuenta->setRutAnulacion(175340777);

                $em->persist($oPagoCuenta);
                $em->flush();

                $oReservaAtencion->setIdPagoCuenta($oPagoCuenta);

                $em->persist($oReservaAtencion);
                $em->flush();

                return "Pagado";
            }
        }
        return "Error";
    }

    public function ValidarFormulario($form)
    {
        return $form->isValid();
    }

    public function getUsuarioIntegracionByEmpresa($idEmpresa)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("
			SELECT
			ur.id as idUsuarioRebsol,
			p.rutPersona as rut,
			p.digitoVerificador as digito,
			ur.nombreUsuario as nombreUsuario
			FROM
			Rebsol\HermesBundle\Entity\UsuariosRebsol ur
			JOIN ur.idPersona p
			WHERE
				p.idEmpresa                    = ?2
			AND ur.esProfesionalIntegracion = 1
      	");
        $query->setParameter(2, $idEmpresa);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }

    /**
     * Funcíón que libera los folios y los deja en estado disponible
     */
    public function liberarFolios()
    {
        $em = $this->getDoctrine()->getManager();
        $folioReservados = $this->getSession('folioReservados');
        $estadoDisponible = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
            ->find($this->container->getParameter('EstadoTalonarioDetalle.disponible'));
        foreach ($folioReservados as $folioReservado) {
            $folioADisponible = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')->find($folioReservado['folioDisponible']);
            $folioADisponible->setMonto(null);
            $folioADisponible->setIdSubEmpresaFacturadora(null);
            $folioADisponible->setIdPaciente(null);
            $folioADisponible->setIdEstadoTalonarioDetalle($estadoDisponible);
            $folioADisponible->setIdUsuarioDetalleBoleta(null);
            $folioADisponible->setIdPagoCuenta(null);
            $em->persist($folioADisponible);
        }
    }

    public function generarLog($mensaje)
    {
        $archivo = $this->get('kernel')->getRootDir() . '/../web/uploads/incidencias/aces.txt';
        $strTextoIncidencia = '=============== ';
        $strTextoIncidencia .= date("Y-m-d H:i:s") . ' ===============' . PHP_EOL;
        if ($mensaje['detalle']['error'] !== '') {
            $strTextoIncidencia .= 'ERROR :: ' . $mensaje['detalle']['error'] . PHP_EOL;
            $strTextoIncidencia .= 'ERROR :: ' . serialize($mensaje['dataPendiente']) . PHP_EOL;
        } else {
            $strTextoIncidencia .= 'ÉXITO :: ' . $mensaje['urlDte'] . PHP_EOL;
        }
        $strTextoIncidencia .= PHP_EOL;
        $strTextoIncidencia .= '===============';
        file_put_contents($archivo, $strTextoIncidencia, FILE_APPEND | LOCK_EX);
    }


    public function obtenerDiferencias($arrDiferencias, $prestacion)
    {
        foreach ((array)$arrDiferencias as $diferencia) {
            if (intval($diferencia['1']) === intval($prestacion['0'])) {
                if (!empty($diferencia['4'])) { //existe un monto
                    return $this->get('dteAcesService')
                        ->montoTotalItem(intval($prestacion['2'])*intval($prestacion['1']), intval($diferencia['4']), intval($this->get('dteAcesService')->obtenerTipoDiferencia($diferencia['5'])));
                } elseif (!empty($diferencia['3'])) { //existe un porcentaje
                    $monto = $this->get('dteAcesService')->calcularMontoConPorcentaje(intval($prestacion['2'])*intval($prestacion['1']), intval($diferencia['3']));
                    $return = $this->get('dteAcesService')
                        ->montoTotalItem(intval($prestacion['2'])*intval($prestacion['1']), $monto, intval($this->get('dteAcesService')->obtenerTipoDiferencia($diferencia['5'])));
                    return $return;
                }
            }
        }
        return 0;
    }

    public function verificarCompletitudPrePagos($idPnatural, $idCuentaPaciente){
        $this->em = $this->getDoctrine()->getManager();

        $estados = array(
            $this->container->getParameter('EstadoPago.pendientePago'),
        );

        $estadoIngreso = array(
            $this->container->getParameter('EstadoIngreso.Hospitalizado'),
        );

        $datosCuentaPagoPaciente = $this->rCajaCuentaPaciente()->obtenerCuentaPaciente($idPnatural, $idCuentaPaciente);
        $datosCuentaPagoPacienteTutor = $this->rCajaCuentaPaciente()->obtenerCuentaPacienteTutor($idPnatural, $idCuentaPaciente);

        $hayPagosPendientes = false;
        if($datosCuentaPagoPaciente) {
            foreach ($datosCuentaPagoPaciente as $paciente) {
                if(in_array($paciente['idEstadoPagoCuenta'], $estados) || in_array($paciente['idEstadoIngreso'], $estadoIngreso)){
                    $hayPagosPendientes = true;
                }
            }
        }

        if($datosCuentaPagoPacienteTutor) {
            foreach ($datosCuentaPagoPacienteTutor as $tutor) {
                if(in_array($tutor['idEstadoPagoCuenta'], $estados)  || in_array($tutor['idEstadoIngreso'], $estadoIngreso)){
                    $hayPagosPendientes = true;
                }
            }
        }

        return $hayPagosPendientes;
    }

    protected function rCajaCuentaPaciente(){
        return $this->get('recaudacion.CuentaPaciente');
    }

    public function verificarCompletitudAccionClinicaPaciente($oPaciente, $idCuentaPaciente){

        $datosCuentaPagoPacienteAccionClinica = $this->rCajaCuentaPaciente()->obtenerCuentaPacienteAccionClinica($oPaciente, $idCuentaPaciente);
        $datosCuentaPagoTutorAccionClinica = $this->rCajaCuentaPaciente()->obtenerCuentaPacienteTutorAccionClinica($oPaciente, $idCuentaPaciente);

        $hayAccionClinicaPacientePendientes = false;
        if ($datosCuentaPagoPacienteAccionClinica) {
            $hayAccionClinicaPacientePendientes = true;
        }

        if ($datosCuentaPagoTutorAccionClinica) {
            $hayAccionClinicaPacientePendientes = true;
        }

        return $hayAccionClinicaPacientePendientes;
    }
}
