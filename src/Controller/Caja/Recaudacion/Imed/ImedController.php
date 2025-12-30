<?php

namespace App\Controller\Caja\Recaudacion\Imed;

use Rebsol\HermesBundle\Entity\BonoDetalle;
use Rebsol\HermesBundle\Entity\BonoDetalleBonificacion;
use Rebsol\HermesBundle\Entity\InterfazImed;
use Rebsol\HermesBundle\Entity\PagoCuenta;
use App\Controller\Caja\Recaudacion\Imed\Exception;
use App\Controller\Caja\RecaudacionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 09/05/2013
 * Update 06/06/2014
 */
class ImedController extends RecaudacionController
{

    /////////////////////////////////////////////
    ////////// I-MED GLOBALS VAR //////////
    /////////////////////////////////////////////
    /**
     * Variable de Session para pruebas con Rut Test.
     * @var string
     * @access private
     */
    var $nombreUsuario = '';
    /**
     * Variable de Session para pruebas con Clave.
     * @var string
     * @access private
     */
    var $claveUsuario = '';
    /**
     * Codigo Imed de la Empresa (Servet Encore)
     * @var string
     * @access private
     */
    var $CodImedLugarServet = '';

    /**
     * Ordena todos los Números de transacción.
     * @var string
     * @access private
     */
    var $numTransac = '';

    /**
     * URL del WS de IMED
     * @var string
     * @access private
     */
    var $UrlWS = '';

    /**
     * URL de Test para envío de NroAuditoria y NumTransac
     * @var string
     * @access private
     */
    var $UrlInterfazImed = '';

    /**
     * URL de Producción para envío de NroAuditoria y NumTransac
     * @var string
     * @access private
     */
    var $UrlProd = '';

    /**
     * @var string
     * @access private
     */
    var $puertoIp = '';
    /**
     * @var string
     * @access private
     */
    var $Ip = '';

    /**
     * URL de Test para envío de NroAuditoria y NumTransac
     * @access private
     */
    var $Client = NULL;

    /**
     * sets Globals Variables.
     * @access  private
     */
    function SetGlobalsVar()

    {
        $this->setSession(null, 'ExitoErrorPost');

        $resultadoBool = true;
        $oUsuarioActual = $this->getUser();
        $Var = $this->rPagoCuenta()->SetGlobalsVar($this->ObtenerEmpresaLogin(), $oUsuarioActual);

        ($Var['COD_LUGAR']) ? $this->CodImedLugarServet = $Var['COD_LUGAR'] : $resultadoBool = false;
        ($Var['IMED_WS']) ? $this->UrlWS = $Var['IMED_WS'] : $resultadoBool = false;
        ($Var['IMED_URL_INTERFAZ']) ? $this->UrlInterfazImed = $Var['IMED_URL_INTERFAZ'] : $resultadoBool = false;
        ($Var['IP_PUBLICA_CLIENTE']) ? $this->Ip = $Var['IP_PUBLICA_CLIENTE'] : $resultadoBool = false;
        ($Var['PUERTO_IP_PUBLICA_CLIENTE']) ? $this->puertoIp = $Var['PUERTO_IP_PUBLICA_CLIENTE'] : $resultadoBool = false;
        ($Var['IMED_NOMBRE_USUARIO']) ? $this->nombreUsuario = $Var['IMED_NOMBRE_USUARIO'] : $resultadoBool = false;
        ($Var['IMED_CLAVE_USUARIO']) ? $this->claveUsuario = $Var['IMED_CLAVE_USUARIO'] : $resultadoBool = false;
        return $resultadoBool;
    }

    ////////////////////////////////////////////////////////
    ////////// I-MED PRINCIPALS FUNCTIONS //////////
    ////////////////////////////////////////////////////////
    /**
     * sets Principal Transaction comunication HERMES - I-MED, Recibe los Prametros mediante Ajax de las Prestaciones y los necesarios que requiere I-MED para poder Generar Venta de Bono y respuesta.
     * @access   public
     */
    function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $oUser = $this->getUser();
        if (!$this->SetGlobalsVar()) {
            return new Response(json_encode($this->ReturnError($this->ErrorImedHermes("SetGlobalsVar"))));//Genera Variables Globales y valida si tienen información para continuar con proceso IMED
        }
        $arrPrestaciones = $this->verificarEsImed($this->ajax('arrPrestaciones'));

        if(!empty($arrPrestaciones)) {
            $param = $this->SetParametersArray($this->ajax('arrDatos'), $arrPrestaciones, $oUser, $em);
            if (isset($param['error'])) {
                return new Response(json_encode($param));
            } else {
                return new Response(json_encode($this->SendGetVtaBonInterfaz($param)));
            }

        }else{
            return new Response(json_encode(array(
                'error' => 2,
                'glosaError' => $this->ErrorImedHermes("errorImedVacio"))));
        }
    }

    /**
     *  "INTEGRACIÓN COMPONENTE BONO ELECTRONICO"
     * Cosume WS VtaBonINterfaz con Parametros solicitados según Manual I-MED
     * @access   private
     */
    function SendGetVtaBonInterfaz($param)
    {
        $oUser = $this->getUser();

        try {
            $client = new \SoapClient($this->UrlWS);

            $answer = $client->__call('VtaBonInterMul', $param);

            if ($answer['CodError'] == 1) {
                return $this->ReturnError($answer['GloError']);
            } else {
                $this->setSession('answer', $answer);
                $this->numTransac = (!isset($this->numTransac)) ?: "";
                $this->numTransac = is_array($answer["NumTransac"]) ? implode(",", $answer["NumTransac"]) : $answer["NumTransac"];
                $this->setSession('NumTransac', $this->numTransac);
                $this->setSession('NroAuditoria', $answer["CodAuditoria"]);

                return array(
                    'NroAuditoria' => $answer["CodAuditoria"],
                    'NumTransac' => $this->numTransac,
                    'rut' => $this->getRutUser($oUser),
                    'lugar' => $param['CodLugar'],
                    'path' => $this->UrlInterfazImed,
                    'error' => 0
                );
            }

        } catch (Exception $e) {
            // $this->setSession("imedProcess", 2);
            return $this->ReturnError('Problemas para conectarse con Imed');
        }
    }


    ///////////////////////////////////
    ////////// I-MED AJAX //////////
    ///////////////////////////////////
    /**
     *"Envio Post Nro. Auditoria, Num Transaccion para completar proceso de pago."
     * Recibe desde Ajax NroAuditoria y NumTransac
     * @access   public
     */
    function sendPostLoginAction()
    {
        $this->setSession("imedProcess", 0);
        $nombre_fichero = $this->get('kernel')->getRootDir() . '/../web/imed/' . $this->getSession('NumTransac');

        if (file_exists($nombre_fichero)) {
            //echo "El fichero $nombre_fichero existe";
            $this->setSession("imedProcess", 2);

            unlink($nombre_fichero);
        }

        $em = $this->getDoctrine()->getManager();

        $oUsuarioActual = $this->getUser();
        $Var = $this->rPagoCuenta()->SetGlobalsVar($this->ObtenerEmpresaLogin(), $oUsuarioActual);

        try {
            $client = new \SoapClient($Var['IMED_WS']);

            $param = array(
                'CodUsuario' => $Var['IMED_NOMBRE_USUARIO'],
                'CodClave' => $Var['IMED_CLAVE_USUARIO'],
                'NumTransac' => $this->getSession('NumTransac'),
                'NroAuditoria' => $this->getSession('NroAuditoria')
            );

            $answerObtBonInterMul = $client->__call('ObtBonInterMul', $param);

            $answerVerResulEmi = $client->__call('VerResulEmi', $param);

            $imedResponse = $this->ImedParserObtBonInterMul($answerObtBonInterMul);

            //Consulta si el estado continua "En Proceso", de no ser así, inicia el IF, de lo cotrario,
            //ELSE que lleva a la vista la respuesta en proceso, para solicitar nuevamente la validacion en el Controlador.

            if (($answerVerResulEmi['CodEstado'] != 1 && $answerVerResulEmi['CodEstado'] != 3) && $imedResponse['CodError'] == 0) {
                $this->setSession("imedProcess", 1);
                $arrImedVntaBono = $this->getSession("parametrosImed");

                $oInterfazImed = new InterfazImed();
                $oInterfazImed->setCodUsuario($param['CodUsuario']); ////// hay que actualizar variable, esta es solo para desarrollo
                $oInterfazImed->setCodClave($param['CodClave']); ////// hay que actualizar variable, esta es solo para desarrollo
                $oInterfazImed->setCodigoTransaccion($param['NumTransac']);
                $oInterfazImed->setNumeroAuditoria($param['NroAuditoria']);
                $oInterfazImed->setRutConvenio($imedResponse['LisVenConv']['RutConvenio']);
                $oInterfazImed->setRutTratante($imedResponse['LisVenConv']['RutTratante']);
                $oInterfazImed->setRutSolic($imedResponse['LisVenConv']['RutSolic']);
                $oInterfazImed->setRutBenef($imedResponse['LisVenConv']['RutBenef']);
                $oInterfazImed->setRutCajero($arrImedVntaBono['ListConvenios']['ListConveniosType']['RutCajero']);
                $oInterfazImed->setIndurgencia($imedResponse['LisVenConv']['IndUrgencia']);
                $oInterfazImed->setUrlRetExito($arrImedVntaBono['UrlRetExito']);
                $oInterfazImed->setUrlRetError($arrImedVntaBono['UrlRetError']);
                $oInterfazImed->setCodFinanciador(intval($imedResponse['LisVenConv']['CodFinanciador']));
                $oInterfazImed->setCodLugar(intval($arrImedVntaBono['CodLugar']));
                $oInterfazImed->setCodTipoTratamiento(intval($arrImedVntaBono['ListConvenios']['ListConveniosType']['CodTipoTratamiento']));
                $oInterfazImed->setCorrConvenio(intval($imedResponse['LisVenConv']['CorrConvenio']));
                $oInterfazImed->setNomSolic($imedResponse['LisVenConv']['NomSolic']);
                $oInterfazImed->setFecIniTratamiento(intval($arrImedVntaBono['ListConvenios']['ListConveniosType']['FecIniTratamiento']));
                $oInterfazImed->setFecTerTratamiento(intval($arrImedVntaBono['ListConvenios']['ListConveniosType']['FecTerTratamiento']));
                $oInterfazImed->setCantDias(intval($arrImedVntaBono['ListConvenios']['ListConveniosType']['CantDias']));
                $oInterfazImed->setFolioAntecedente(intval($arrImedVntaBono['ListConvenios']['ListConveniosType']['FolioAntecedente']));
                //$oInterfazImed->setIdPaciente($answerObtBonInterfaz['']); desde pagarContoller.PHP
                $oInterfazImed->setEstado(intval($imedResponse['CodError']));
                $oInterfazImed->setGloError($imedResponse['GloError']);
                $oInterfazImed->setlisPrestaUt(serialize($arrImedVntaBono['ListConvenios']['ListConveniosType']['LisPrestAut']['LisPrestAutMulType']));
                $oInterfazImed->setlistaBonos(serialize($imedResponse['LisVenConv']['ListaBonosMul']));
                $oInterfazImed->setlistaForPag(serialize($imedResponse['LisVenConv']['ListaForPag']));
                $oInterfazImed->setFechaTransImed(new \DateTime("now"));
                $oInterfazImed->setIdPagoCuenta($imedResponse['oPagoCuenta']);

                $em->persist($oInterfazImed);
                $em->flush();

                $this->setSession("idInterfazImed", $oInterfazImed->getId());

                //Casos de error una vez sea el estado Distinto a "En Proceso"
                if ($answerVerResulEmi['CodEstado'] == 0 || $answerVerResulEmi['CodEstado'] == 4 || $imedResponse['CodError'] == 1) {

                    if ($imedResponse['CodError'] == 1) {

                        $respuestaArray = array(
                            'respuesta' => null,
                            'cod' => $imedResponse['CodError'],
                            'glo' => 'No se logró concluir Proceso.'
                        );
                    } else {

                        $respuestaArray = array(
                            'respuesta' => null,
                            'cod' => $answerVerResulEmi['CodEstado'],
                            'glo' => $answerVerResulEmi['GloError']
                        );
                    }

                    return new Response(json_encode($respuestaArray));
                }

                $arrBonos = array();
                $Exedente = 0;

                foreach ($imedResponse['LisVenConv']['ListaBonosMul'] as $bonos) {

                    $MontoPrest = 0;
                    $MontoBono = 0;
                    $MontoCopago = 0;
                    $MontoSeguro = 0;
                    $FolioBono = 0;

                    foreach ($bonos['LisPrestVta'] as $presVta) {

                        $MontoBono = $presVta['MontoBon'] + $MontoBono;
                        $MontoCopago = $presVta['MontoCopago'] + $MontoCopago;
                        $FolioBono = $bonos['FolioBono'];
                        //Para generar Monto Seguro Complementario
                        if (!empty($presVta['ListaOtrasBon'])) {
                            if(!isset($presVta['ListaOtrasBon'][0])){
                                $MontoSeguro = $presVta['ListaOtrasBon']['MtoBonAdic'] + $MontoSeguro;
                            }else{
                                foreach ($presVta['ListaOtrasBon'] as $value){
                                    $MontoSeguro = $value['MtoBonAdic'] + $MontoSeguro;
                                }
                            }

                        } else if(isset($presVta['ListaOtrasBonType'])){
                            foreach ($presVta['ListaOtrasBonType'] as $value){
                                if(!empty($presVta['ListaOtrasBonType']['ListaOtrasBon'])){
                                    $MontoSeguro = $value['MtoBonAdic'] + $MontoSeguro;
                                }
                            }
                        } else {
                            $MontoSeguro = 0;
                        }

                    }

                    $arrBonos[$bonos['FolioBono']] = array(
                        'MontoBon' => $MontoBono,
                        'MontoCopago' => $MontoCopago,
                        'FolioBono' => $FolioBono,
                        'MontoSeguro' => $MontoSeguro
                    );
                }

                if (!empty($imedResponse['LisVenConv']['ListaForPag'])) {

                    foreach ($imedResponse['LisVenConv']['ListaForPag'] as $forma) {

                        if ($forma->CodForPag == 6) {
                            $Exedente = $forma->MtoTransac + $Exedente;
                        }
                    }
                }

                $arrRespuesta = array(
                    'error' => 0,
                    'glosaAnswer' => $answerVerResulEmi['CodEstado'],
                    'errorObtBon' => $imedResponse['CodError'],
                    'arrayBonos' => $arrBonos,
                    'Exedente' => $Exedente
                );

                $respuestaArray = array(
                    'respuesta' => $arrRespuesta,
                    'cod' => $answerVerResulEmi['CodEstado'],
                    'glo' => $answerVerResulEmi['GloEstado'],
                );

                return new Response(json_encode($respuestaArray));

            } else {

                if ($this->getSession("imedProcess") == 0 || $this->getSession("imedProcess") == null) {
                    $respuestaArray = array('respuesta' => null,
                        'cod' => $answerVerResulEmi['CodEstado'],
                        'glo' => $answerVerResulEmi['GloEstado']
                    );
                    return new Response(json_encode($respuestaArray));
                }
                if ($this->getSession("imedProcess") == 2) {

                    $respuestaArray = array('respuesta' => null,
                        'cod' => 0,
                        'glo' => $answerVerResulEmi['GloEstado']
                    );
                    return new Response(json_encode($respuestaArray));
                }

            }
        } catch (Exception $e) {

            $respuestaArray = array('respuesta' => null,
                'cod' => 0,
                'glo' => 'Problemas para conectarse con Imed'
            );
            return new Response(json_encode($respuestaArray));

        }


    }

    /**
     *  "WEB SERVICES INFORMACION DE SALIDA DE BONOS EMITIDOS POR INTERFAZ"
     * Cosume WS ObtBonInterfaz con Parametros solicitados según Manual I-MED
     * @access  public
     */
    function sendGetObtBonInterfazAction()
    {
        if (!$this->SetGlobalsVar()) {
            return new Response(json_encode($this->ReturnError($this->ErrorImedHermes("SetGlobalsVar"))));
        }
        $answer = $this->getSession('answer');
        $client = new \nusoap_client($this->UrlWS, true);
        $this->numTransac = (!isset($this->numTransac)) ?: "";
        $this->numTransac = is_array($answer["NumTransac"]) ? implode(",", $answer["NumTransac"]) : $answer["NumTransac"];
        $param = array(
            'CodUsuario' => $this->nombreUsuario,
            'CodClave' => $this->claveUsuario,
            'NumTransac' => $this->numTransac
        );

        if ($client->getError()) {
            return new Response(json_encode($this->ReturnError("Imposible Conectar con Web Services, Por Favor, Intentelo Nuevamente")));
        }

        $answerObtBonInterfaz = $client->call('ObtBonInterfaz', $param);

        if ($answerObtBonInterfaz['CodError'] != 0 || $answerObtBonInterfaz['CodError'] != '0') {

            return new Response(json_encode($this->ReturnError($answerObtBonInterfaz['GloError'])));

        } else {
            $answerObtBonInterfaz['error'] = 0;
            return $answerObtBonInterfaz;
        }
    }

    //////////////////////////////////////////////////////////////////
    ////////// I-MED TRANSACTIONS COMPLEMENTARIES FUNCTIONS //////////
    //////////////////////////////////////////////////////////////////
    /**
     * sets DataArrays to I-MED
     * @access   private
     */
    function SetParametersArray($arrDatos, $arrPrestaciones, $oUser, $em)
    {
        $rutTratante = '0000000000-0';
        $rutSolicitante = '0000000000-0';
        $nomSolic = "";
        $codEspecialidad = 0;
        $oReserva = ($arrDatos['idReservaAtencion']) ? $em->getRepository("RebsolHermesBundle:ReservaAtencion")->find($arrDatos['idReservaAtencion']) : null;
        $oTratamiento = ($arrDatos['idTratamiento']) ? $em->getRepository("RebsolHermesBundle:Tratamiento")->find($arrDatos['idTratamiento']) : null;
        $CodFinanciador = ($arrDatos['convenio']) ? $arrDatos['convenio'] : $arrDatos['financiador'];

        if ($oReserva) {
            if ($oReserva->getIdUsuarioSolicita()) {
                $rutUsuarioSolicita = $oReserva->getIdUsuarioSolicita()->getIdPersona()->getRutPersona();
                $dvUsuarioSolicita = $oReserva->getIdUsuarioSolicita()->getIdPersona()->getDifitoVerificador();
                $rutSolicitante = $rutUsuarioSolicita . '-' . $dvUsuarioSolicita;
                //$nomSolic           = $oPnaturalSolicitante->getNombrePnatural().' '.$oPnaturalSolicitante->getApellidoPaterno().' '.$oPnaturalSolicitante->getApellidoMaterno();

            } else {

                if (!empty($arrDatos['derivadoInt'])) {
                    $nomSolic = $this->getCompleteNameFromIdPnatural($arrDatos['derivadoInt']);
                } else if (!empty($arrDatos['derivadoExt'])) {
                    $nomSolic = $arrDatos['derivadoExt'];
                    $rutSolicitante = $arrDatos['derivadoExtRut'];
                } else if (empty($arrDatos['derivadoInt']) && empty($arrDatos['derivadoExt'])) {
                    $nomSolic = "sin nombre";
                }
            }

            if ($oReserva->getIdUsuarioProfesional()) {
                $rutUsuarioProfesional = $oReserva->getIdUsuarioProfesional()->getIdPersona()->getRutPersona();
                $dvUsuarioProfesional = $oReserva->getIdUsuarioProfesional()->getIdPersona()->getDigitoVerificador();
                $rutTratante = $rutUsuarioProfesional . '-' . $dvUsuarioProfesional;
                $oUsuariosRebsol = $em->getRepository("RebsolHermesBundle:UsuariosRebsol")->findOneBy(array("idPersona" => $oReserva->getIdUsuarioProfesional()->getIdPersona()));
                $oRelEspecialidadProfesional = $em->getRepository("RebsolHermesBundle:RelEspecialidadProfesional")->findOneBy(array("idUsuario" => $oUsuariosRebsol->getId(), "idEstado" => 1));
                $codEspecialidad = ($oReserva) ? $oReserva->getIdEspecialidadMedica()->getCodigoImed() :
                    $oRelEspecialidadProfesional->getIdEspecialidadMedica()->getCodigoImed();
            }
        } else {
            if (!empty($arrDatos['derivadoInt'])) {
                $nomSolic = $this->getCompleteNameFromIdPnatural($arrDatos['derivadoInt']);
                $oPnatural = $em->getRepository("RebsolHermesBundle:Pnatural")->find($arrDatos['derivadoInt']);
                $rutSolicitante = $oPnatural->getIdPersona()->getRutPersona() . '-' . $oPnatural->getIdPersona()->getdigitoVerificador();
            } else if (!empty($arrDatos['derivadoExt'])) {
                $nomSolic = $arrDatos['derivadoExt'];
                $rutSolicitante = $arrDatos['derivadoExtRut'];
            } else if (empty($arrDatos['derivadoInt']) && empty($arrDatos['derivadoExt'])) {
                $nomSolic = "sin nombre";
            }
        }

        $rutCajero = $this->getRutUser($oUser);

        if (strlen($this->getRutUser($oUser)) == 9) {
            $rutCajero = '000' . $this->getRutUser($oUser);
        } else if (strlen($this->getRutUser($oUser)) == 10) {
            $rutCajero = '00' . $this->getRutUser($oUser);
        }

        if (!empty($arrDatos['derivadoInt']) && $rutSolicitante != '0000000000-0') {
            if (strlen($rutSolicitante) == 9) {
                $rutSolicitante = '000' . $rutSolicitante;
            } else if (strlen($rutSolicitante) == 10) {
                $rutSolicitante = '00' . $rutSolicitante;
            }
        }

        if (!empty($arrDatos['derivadoExt']) && $rutTratante != '0000000000-0') {
            if (strlen($rutTratante) == 9) {
                $rutTratante = '000' . $rutTratante;
            } else if (strlen($rutTratante) == 10) {
                $rutTratante = '00' . $rutTratante;
            }
        }


        $oPrevision = $em->getRepository("RebsolHermesBundle:Prevision")->find($CodFinanciador);
        $arrRutConvenios = $this->rPagoCuenta()->GetRutConvenio($arrPrestaciones, $this->getSession('idTalonario'));

        $arrDatosParameters = array(

            'CodUsuario' => $this->nombreUsuario, // asignado por I-Med
            'CodClave' => $this->claveUsuario, // asignado por I-Med
            'CodFinanciador' => intval($oPrevision->getIdImed()),
            'CodLugar' => intval($this->CodImedLugarServet), //Código predeterminado para Servet
            'UrlRetExito' => $this->UrlExito(),
            'UrlRetError' => $this->UrlError()

        );

        //Verificar las urls de arriba


        $array_convenios = array(
            'rutTratante' => $rutTratante,
            'rutSolicitante' => $rutSolicitante,
            'nomSolic' => $nomSolic,
            'codEspecialidad' => $codEspecialidad,
            'idPnatural' => $arrDatos['idPnatural'],
            'rutCajero' => $rutCajero,
            'oTratamiento' => $oTratamiento,
            'arrPrestaciones' => $arrPrestaciones,
            'plan' => $arrDatos['plan'],
            'TipoAtencion' => $arrDatos['TipoAtencion'],
            'oUser' => $oUser
        );


        $arrListConvenios = $this->SetConveniosArray($arrRutConvenios, $array_convenios, intval($oPrevision->getId()));
        $arrDatosParameters['ListConvenios'] = $arrListConvenios;
        $this->setSession("parametrosImed", $arrDatosParameters);

        return $arrDatosParameters;
    }

    public function SetConveniosArray($arrRutConvenios, $array_convenios, $idPrevision)
    {
        $i = 0;

        foreach ($arrRutConvenios as $key => $rutConvenio) {
            $convenios['ListConveniosType'] = array(
                'RutConvenio' => $rutConvenio, // asignado por I-Med //ListConvenios
                'CorrConvenio' => 0, // asignado por I-Med //ListConvenios
                'RutTratante' => $array_convenios['rutTratante'], //ListConvenios
                'RutSolic' => $array_convenios['rutSolicitante'], //ListConvenios
                'NomSolic' => $array_convenios['nomSolic'], //ListConvenios
                'CodEspecia' => '0' . $array_convenios['codEspecialidad'], //ListConvenios
                'RutBenef' => $this->getRutPnatural($array_convenios['idPnatural']), //ListConvenios
                'RutCajero' => $array_convenios['rutCajero'], //ListConvenios
                'IndUrgencia' => 'N', //si es Urgencias 'S', si NO es Urgencias 'N' //ListConvenios
                'CodTipoTratamiento' => 1, //ListConvenios
                'FecIniTratamiento' => '19000101', //ListConvenios
                'FecTerTratamiento' => '19000101', //ListConvenios
                'CantDias' => 0, //ListConvenios
                'FolioAntecedente' => 0, //ListConvenios
            );

            if ($array_convenios['oTratamiento'] and $array_convenios['oTratamiento']->getIdTipoTratamiento()->esImed() == 1) {

                $convenios['ListConveniosType'] ['CodTipoTratamiento'] = $array_convenios['oTratamiento']->getIdTipoTratamiento()->getCodigoTratamiento();
                $convenios['ListConveniosType'] ['FecIniTratamiento'] = $array_convenios['oTratamiento']->getFechaCreacion()->format("Ymd");
                $convenios['ListConveniosType'] ['FecTerTratamiento'] = $array_convenios['oTratamiento']->getFechaCreacion()->format("Ymd");
                $dias = (strtotime($array_convenios['oTratamiento']->getFechaCreacion()->format("Y-m-d")) - strtotime($array_convenios['oTratamiento']->getFechaCreacion()->format("Y-m-d"))) / 86400;
                $dias = abs($dias);
                $dias = floor($dias);
                $convenios[$i] ['CantDias'] = $dias;

            }


            /**
             * [$arrDatosParameters description]
             * @var [type]
             */
            $arrPrestacionesTemp = $this->SetPrestacionArray($array_convenios['arrPrestaciones'], $array_convenios['plan'], $array_convenios['TipoAtencion'], $array_convenios['oUser'], $idPrevision);

            if (!empty($arrPrestacionesTemp)) {

                if (!isset($arrPrestacionesTemp['error'])) {

                    $convenios['ListConveniosType'] ['LisPrestAut'] ['LisPrestAutMulType'] = $arrPrestacionesTemp;

                } else {

                    return $arrPrestacionesTemp;
                }

            } else {
                return $this->ReturnError('No existe ninguna Prestación relacionada con I-MED');
            }

        }

        return $convenios;
    }

    /**
     * sets and Builder PrestacionesArrays to send Parameters
     * @access   private
     */
    function SetPrestacionArray($arrPrestaciones, $plan, $tipoAtencion, $oUser, $idPrevision)
    {

        $em = $this->getDoctrine()->getManager();
        $auxCategoria = 0;
        //$lista[] = $this->SetPrestacionesPrestacion($prestacion['0'], $prestacion['1'], $prestacion['2'], $prestacion['3'], $idPrevision);
        foreach ($arrPrestaciones as $prestacion) {

            $fService = $this->get('Caja_valida');
            $idPrestacion = $prestacion['0'];
            $ArrTalonarios = $this->get('session')->get('idTalonario');
            if ($fService->SubEmpresa($idPrestacion, $ArrTalonarios)) {

                $categoria = $fService->CategoriaPrestador($idPrestacion);
                $oAccionCinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($prestacion['0']);
                if ($oAccionCinica->getEsImed() == 1) {

                    if ($categoria == "PRESTACION") {
                        $lista[] = $this->SetPrestacionesPrestacion($prestacion['0'], $prestacion['1'], $prestacion['2'], $prestacion['3'], $idPrevision);
                    }

                    if ($categoria == "HONORARIOS") {

                        $auxCategoria = 1;
                        $auxPrestacion = $prestacion['0'];
                        $lista[] = $this->SetPrestacionesHonorario($prestacion['0'], $prestacion['1'], $prestacion['2'], $prestacion['3'], $plan);
                        $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
                        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($oUser);
                        $oRelTipoAtencion = $em->getRepository('RebsolHermesBundle:RelSucursalTipoAtencion')->findOneBy(array('idSucursal' => $SucursalUsuario->getId(), 'idTipoAtencion' => $tipoAtencion, 'idEstado' => $oEstadoAct->getId()));
                        $TipoAtencion = $oRelTipoAtencion->getId();
                        $lista[] = $this->SetPrestacionesHonorarioPabellon($arrPrestaciones, $auxPrestacion, $plan, $TipoAtencion, $oUser);

                    }


                }
            } else {
                $this->ReturnError("Prestaciones no Corresponden a Sub-Empresa de Cajero");
            }
        }

        if (!empty($lista) || isset($lista)) {

            $arrayLista = array();

            foreach ($lista as $l) {
                if (!empty($l)) {
                    $arrayLista[] = $l;
                }
            }

        } else {
            return;
        }

        return $arrayLista;
    }


    /**
     * Set Prestaciones/Honorarios for SetPrestacionesArray
     * @access   private
     */
    function SetPrestacionesHonorario($idPrestacion, $cantidad, $monto, $tipo, $plan, $em)
    {

        if (intval($tipo) == 1) {

            $fechahoy = new \DateTime();
            $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
            $idAbiertaPrecio = $this->rPagoCuenta()->DatosAbiertaPrecioParaImedPrestacionesHonorarios($oAccionClinica->getid(), $plan, $oEstadoAct, $fechahoy->format("Y-m-d H:i:s"));

            if (!$idAbiertaPrecio) {
                $this->ReturnError("Prestación no cuenta con sus Precios Correctamente");
            } else {

                $oAbiertaDistribucion = $em->getRepository('RebsolHermesBundle:AbiertaDistribucion')->findBy(array("idPrecio" => $idAbiertaPrecio, "idEstado" => $oEstadoAct));
                $preciosArray = array();

                foreach ($oAbiertaDistribucion as $ad) {
                    $preciosArray[] = array(
                        'precio' => $ad->getValor(),
                        'ItemCirugia' => $ad->getIdItemCirugia()->getId(),
                    );
                }

                foreach ($preciosArray as $p) {

                    $monto = str_replace(',', '', $monto);
                    if (intval($p['precio']) == intval($monto)) {
                        switch ($p['ItemCirugia']) {
                            case 1:
                                $CodItem = '8';
                                break;
                            case 3:
                                $CodItem = '1';
                                break;
                            case 4:
                                $CodItem = '2';
                                break;
                            default:
                                $CodItem = '0';
                        }

                        $lista[] = array(
                            'CodPrestacion' => $oAccionClinica->getCodigoAccionClinica() /*. $oAccionClinica->getCodigoFonasa()*/,
                            'CodItem' => $CodItem,
                            'Cantidad' => intval($cantidad),
                            'RecargoHora' => 'N',
                            'MtoTotal' => intval($monto),
                            'InfAdicional' => ($oAccionClinica->getDescripcion()) ? $oAccionClinica->getDescripcion() : '0'
                        );
                        return array_shift($lista);
                    }
                }
            }
        }
    }

    /**
     * Set Normals Prestaciones for SetPrestacionesArray
     * @access   private
     */
    function SetPrestacionesPrestacion($idPrestacion, $cantidad, $monto, $tipo, $idPrevision)
    {
        $em = $this->getDoctrine()->getManager();
        if (intval($tipo) == 1) {
            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion); // 1.- saco id

            $parametroCodMultiple = $em->getRepository('RebsolHermesBundle:Parametro')
                ->obtenerParametro('CODIGO_MULTIPLE_IMED');
            if ($parametroCodMultiple['valor'] === '1') {
                $oRelCodigoImedPrestacion = $em->getRepository('RebsolHermesBundle:RelCodigoImedPrestacion')
                    ->findOneBy(array('idAccionClinica' => $oAccionClinica->getId(), 'idPrevision' => $idPrevision));

                if(is_null($oRelCodigoImedPrestacion)){
                    $codigoImed = null;
                }else{
                    $codigoImed = $oRelCodigoImedPrestacion->getCodigo();
                }

            } else {
                $codigoImed = $oAccionClinica->getCodigoImed();
            }

            $monto = str_replace(',', '', $monto);

            // ELIMINAR PRUEBA PARA HOMOLOGACIÓN IMED
            /*$oRelCodigoImedPrestacion2s = $em->getRepository('RebsolHermesBundle:AccionClinica')->prueba();
            //dump($oRelCodigoImedPrestacion2s); exit();
            foreach ($oRelCodigoImedPrestacion2s as $value)
            {
                $lista[] = array(
                    'CodPrestacion' => $codigo,//$codigoImed,//$oAccionClinica->getCodigoImed()
                    'CodItem'       => '0', //Corresponde a una Prestacion normal(Consulta, Exámenes, etc)
                    'Cantidad'      => intval($cantidad),
                    'RecargoHora'   => 'N',
                    'MontoPrest'      => intval($monto),
                    'InfAdicional'  => ($oAccionClinica->getDescripcion()) ? $oAccionClinica->getDescripcion() : '0'
                );
            }*/

            $lista[] = array(
                'CodPrestacion' => $codigoImed,//$oAccionClinica->getCodigoImed()
                'CodItem' => '0', //Corresponde a una Prestacion normal(Consulta, Exámenes, etc)
                'Cantidad' => intval($cantidad),
                'RecargoHora' => 'N',
                'MontoPrest' => intval($monto),
                'InfAdicional' => ($oAccionClinica->getDescripcion()) ? $oAccionClinica->getDescripcion() : '0'
            );

        }

        return array_shift($lista);
        /*ELIMINAR RETURN PARA PRUEBAS DE HOMOLOGACIÓN IMED*/
//        return ($lista);
    }

    /**
     * Set Prestaciones/Honorarios if Pabellon for SetPrestacionesArray
     * @access   private
     */
    function SetPrestacionesHonorarioPabellon($arrPrestaciones, $auxPrestacion, $plan, $tipoAtencion, $oUser, $em)
    {
        $fechahoy = new \DateTime();
        $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
        $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($auxPrestacion);
        $idAbiertaPrecio = $this->rPagoCuenta()->DatosAbiertaPrecioParaImedPrestacionesHonorarios($oAccionClinica->getid(), $plan, $oEstadoAct, $fechahoy->format("Y-m-d H:i:s"));
        $iPabellon = $oAccionClinica->getidGuarismo()->getId();
        $oSucursal = $em->getRepository("RebsolHermesBundle:UsuariosRebsol")->obtenerSucursalUsuario($oUser);
        $oRelTipoAtencion = $em->getRepository('RebsolHermesBundle:RelSucursalTipoAtencion')->findOneBy(array('idSucursal' => $oSucursal->getId(), 'idTipoAtencion' => $tipoAtencion, 'idEstado' => $oEstadoAct->getId()));
        $TipoAtencion = $oRelTipoAtencion->getId();
        $Pabellon = $this->rPagoCuenta()->PabellonConHonorario($auxPrestacion, $idAbiertaPrecio, $plan, $fechahoy, $oEstadoAct, $iPabellon, $oSucursal->getId(), $this->getSession('prevision'));

        $cantidad = 0;
        if ($Pabellon) {
            foreach ($arrPrestaciones as $prestacion) {
                $monto = str_replace(',', '', $prestacion['2']);
                $cantidad = ((int)$monto == (int)$Pabellon['precio']) ? $prestacion['1'] : $cantidad = $cantidad;
            }

            $lista[] = array(
                'CodPrestacion' => $oAccionClinica->getCodigoAccionClinica(),
                'CodItem' => '8', //Corresponde a una Prestacion normal(Consulta, Exámenes, etc)
                'Cantidad' => intval($cantidad),
                'RecargoHora' => 'N',
                'MtoTotal' => intval($Pabellon['precio']),
                'InfAdicional' => ($oAccionClinica->getDescripcion()) ? $oAccionClinica->getDescripcion() : '0'
            );
            return array_shift($lista);
        }
    }

    /**
     * URL exito
     * @access   private
     */
    function UrlExito()
    {
        $this->setSession(1, 'ExitoErrorPost');
        //donde tenemos que redirigir si es OK o NO
        return $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost(). '/imed/imedexito.php';
    }

    /**
     * URL fracaso
     * @access   private
     */
    function UrlError()
    {
        $this->setSession(0, 'ExitoErrorPost');
        //donde tenemos que redirigir si es OK o NO
        return $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost(). '/imed/imederror.php';
    }


    /**
     * sets errors I-MED to send Ajax Hermes
     * @access   private
     */
    function ReturnError($error)
    {
        return array(
            'error' => 1,
            'glosaError' => $error
        );
    }

    public function cajaImedValidaPrevisionEsImedAction()
    {

        $em = $this->getDoctrine()->getManager();
        $oPrevision = $em->getRepository('RebsolHermesBundle:Prevision')->find($this->ajax('prevision'));

        if ($oPrevision->getIdImed() != null) {
            return new Response(1);
        } else {
            return new Response(0);
        }

    }

    public function CajaAnulaPagoEsImedAction()
    {

        $em = $this->getDoctrine()->getManager();
        $ointerfazImed = $em->getRepository('RebsolHermesBundle:InterfazImed')->findOneBy(array('idPaciente' => $this->ajax('paciente')));

        if ($ointerfazImed) {
            $evitaAnulacionImed = $em->getRepository("RebsolHermesBundle:Parametro")->obtenerParametro('EVITAR_ANULACION_EN_IMED');
            $evitaAnulacionImed = $evitaAnulacionImed['valor'] === '1';
            if ($evitaAnulacionImed) {
                return new Response(0);
            }
            return new Response(1);
        } else {
            return new Response(0);
        }
    }

    public function CajaCierreAnulaPagoImedAction()
    {

        $em = $this->getDoctrine()->getManager();
        $arr = $this->getSession("parametrosImed");

        $ointerfazImed = $em->getRepository('RebsolHermesBundle:InterfazImed')->findOneBy(array('codLugar' => $arr['CodLugar'],
            'codFinanciador' => $arr['CodFinanciador'],
            'codigoTransaccion' => $this->getSession('NumTransac'),
            'numeroAuditoria' => $this->getSession('NroAuditoria')
        ));

        if ($ointerfazImed) {
            $arrayUnserialize = unserialize($ointerfazImed->getListaBonos());
            foreach ($arrayUnserialize as $bono) {

                $client = new \nusoap_client($this->UrlWS, true);

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
        $em->flush();
    }

    function ImedParserObtBonInterMul($ObtBonInterMul)
    {
        $ImedResponse = array();

        $ImedResponse['CodError'] = $ObtBonInterMul['CodError'];
        $ImedResponse['GloError'] = $ObtBonInterMul['GloError'];

        $LisVenConvType = $ObtBonInterMul['LisVenConv']->LisVenConvType;

        $ImedResponse['LisVenConv']['CodFinanciador'] = $LisVenConvType->CodFinanciador;
        $ImedResponse['LisVenConv']['RutConvenio'] = $LisVenConvType->RutConvenio;
        $ImedResponse['LisVenConv']['CorrConvenio'] = $LisVenConvType->CorrConvenio;
        $ImedResponse['LisVenConv']['RutTratante'] = $LisVenConvType->RutTratante;
        $ImedResponse['LisVenConv']['RutSolic'] = $LisVenConvType->RutSolic;
        $ImedResponse['LisVenConv']['NomSolic'] = $LisVenConvType->NomSolic;
        $ImedResponse['LisVenConv']['CodEspecia'] = $LisVenConvType->CodEspecia;
        $ImedResponse['LisVenConv']['RutBenef'] = $LisVenConvType->RutBenef;
        $ImedResponse['LisVenConv']['IndUrgencia'] = $LisVenConvType->IndUrgencia;

        if (isset($ObtBonInterMul['LisVenConv']->LisVenConvType->ListaBonosMul->ListaBonosMulType)) {

            if (is_array($ObtBonInterMul['LisVenConv']->LisVenConvType->ListaBonosMul->ListaBonosMulType)) {

                $aImedResponse = $this->multipleArrayListaBonosMulType($ObtBonInterMul);
                $ImedResponse['LisVenConv']['ListaBonosMul'] = $aImedResponse['ListaBonosMul'];
                $ImedResponse['oPagoCuenta'] = $aImedResponse['oPagoCuenta'];


            } else {
                //only works if ListaBonosMulType has a single index array
                $aImedResponse = $this->singleArrayListaBonosMulType($ObtBonInterMul);
                $ImedResponse['LisVenConv']['ListaBonosMul'] = $aImedResponse['ListaBonosMul'];
                $ImedResponse['LisVenConv']['ListaBonosMul'][0]['LisPrestVta'] = $aImedResponse['LisPrestVta'];
                $ImedResponse['oPagoCuenta'] = $aImedResponse['oPagoCuenta'];
            }

            $ImedResponse['LisVenConv']['ListaForPag'] = $aImedResponse['ListaForPag'];

        } else {
            $ImedResponse['LisVenConv']['ListaBonosMul'] = null;
            $ImedResponse['LisVenConv']['ListaForPag'] = null;
            $ImedResponse['oPagoCuenta'] = null;
        }

        return $ImedResponse;

    }

    function singleArrayListaBonosMulType($ObtBonInterMul)
    {

        $ListaBonosMulType = $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaBonosMul->ListaBonosMulType;

        $ListaBonosMul[0]['FolioBono'] = $ListaBonosMulType->FolioBono;
        $ListaBonosMul[0]['FecEmi'] = $ListaBonosMulType->FecEmi;
        $ListaBonosMul[0]['NumPrestBon'] = $ListaBonosMulType->NumPrestBon;
        $ListaBonosMul[0]['NumBoleta'] = $ListaBonosMulType->NumBoleta;
        $ListaBonosMul[0]['MontoAfecto'] = $ListaBonosMulType->MontoAfecto;
        $ListaBonosMul[0]['MontoExento'] = $ListaBonosMulType->MontoExento;
        $ListaBonosMul[0]['MontoTotal'] = $ListaBonosMulType->MontoTotal;

        //$ImedResponse['LisVenConv']['ListaBonosMul'] = $ListaBonosMul;
        $em = $this->getDoctrine()->getManager();

        if ($ListaBonosMulType->NumPrestBon == 1) {

            $LisPrestVtaType = $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaBonosMul->ListaBonosMulType->LisPrestVta->LisPrestVtaType;

            $LisPrestVta[0]['CodPrestacion'] = $LisPrestVtaType->CodPrestacion;
            $LisPrestVta[0]['CodItem'] = $LisPrestVtaType->CodItem;
            $LisPrestVta[0]['Cantidad'] = $LisPrestVtaType->Cantidad;
            $LisPrestVta[0]['RecargoHora'] = $LisPrestVtaType->RecargoHora;
            $LisPrestVta[0]['MontoPrest'] = $LisPrestVtaType->MontoPrest;
            $LisPrestVta[0]['MontoBon'] = $LisPrestVtaType->MontoBon;
            $LisPrestVta[0]['MontoCopago'] = $LisPrestVtaType->MontoCopago;
            $LisPrestVta[0]['EsGes'] = $LisPrestVtaType->EsGes;
            $LisPrestVta[0]['CodPatologia'] = $LisPrestVtaType->CodPatologia;
            $LisPrestVta[0]['CodIntSanitaria'] = $LisPrestVtaType->CodIntSanitaria;
            $LisPrestVta[0]['CodCanasta'] = $LisPrestVtaType->CodCanasta;
            $LisPrestVta[0]['NumPieza'] = $LisPrestVtaType->NumPieza;

            $oBonoDetalle = new BonoDetalle();
            $oBonoDetalle->setFolioBono($ListaBonosMulType->FolioBono);
            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->findOneBy(array('codigoImed' => $LisPrestVtaType->CodPrestacion));
            $oBonoDetalle->setIdAccionClinica($oAccionClinica);
            $oBonoDetalle->setIdPagoCuenta(null);
            $em->persist($oBonoDetalle);

            if (!is_null($LisPrestVtaType->ListaOtrasBon)) {
                $ListaOtrasBon = $LisPrestVtaType->ListaOtrasBon->ListaOtrasBonType;

                if(!is_array($ListaOtrasBon)){
                    $LisPrestVta[0]['ListaOtrasBon']['CodBonAdic'] = $ListaOtrasBon->CodBonAdic;
                    $LisPrestVta[0]['ListaOtrasBon']['GloBonAdic'] = $ListaOtrasBon->GloBonAdic;
                    $LisPrestVta[0]['ListaOtrasBon']['MtoBonAdic'] = $ListaOtrasBon->MtoBonAdic;
                    $oBonoDetalleBonificacion = new BonoDetalleBonificacion();
                    $oBonoDetalleBonificacion->setIdBonoDetalle($oBonoDetalle);
                    $oBonoDetalleBonificacion->setCodigoBonoAdicional($ListaOtrasBon->CodBonAdic);
                    $oBonoDetalleBonificacion->setGlosaBonoAdicional($ListaOtrasBon->GloBonAdic);
                    $oBonoDetalleBonificacion->setMontoBonoAdicional($ListaOtrasBon->MtoBonAdic);
                    $em->persist($oBonoDetalleBonificacion);
                }else{
                    foreach ($ListaOtrasBon as $key  => $value){
                        $LisPrestVta[0]['ListaOtrasBon'][$key]['CodBonAdic'] = $value->CodBonAdic;
                        $LisPrestVta[0]['ListaOtrasBon'][$key]['GloBonAdic'] = $value->GloBonAdic;
                        $LisPrestVta[0]['ListaOtrasBon'][$key]['MtoBonAdic'] = $value->MtoBonAdic;
                        $oBonoDetalleBonificacion = new BonoDetalleBonificacion();
                        $oBonoDetalleBonificacion->setIdBonoDetalle($oBonoDetalle);
                        $oBonoDetalleBonificacion->setCodigoBonoAdicional($value->CodBonAdic);
                        $oBonoDetalleBonificacion->setGlosaBonoAdicional($value->GloBonAdic);
                        $oBonoDetalleBonificacion->setMontoBonoAdicional($value->MtoBonAdic);
                        $em->persist($oBonoDetalleBonificacion);
                    }
                }

            } else {
                $LisPrestVta[0]['ListaOtrasBon'] = null;
            }

            $em->flush();

        } else {

            $LisPrestVtaType = $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaBonosMul->ListaBonosMulType->LisPrestVta->LisPrestVtaType;

            foreach ($LisPrestVtaType as $LisPrestVtaKey => $LisPrestVtaBon) {
                $LisPrestVta[$LisPrestVtaKey]['CodPrestacion'] = $LisPrestVtaBon->CodPrestacion;
                $LisPrestVta[$LisPrestVtaKey]['CodItem'] = $LisPrestVtaBon->CodItem;
                $LisPrestVta[$LisPrestVtaKey]['Cantidad'] = $LisPrestVtaBon->Cantidad;
                $LisPrestVta[$LisPrestVtaKey]['RecargoHora'] = $LisPrestVtaBon->RecargoHora;
                $LisPrestVta[$LisPrestVtaKey]['MontoPrest'] = $LisPrestVtaBon->MontoPrest;
                $LisPrestVta[$LisPrestVtaKey]['MontoBon'] = $LisPrestVtaBon->MontoBon;
                $LisPrestVta[$LisPrestVtaKey]['MontoCopago'] = $LisPrestVtaBon->MontoCopago;
                $LisPrestVta[$LisPrestVtaKey]['EsGes'] = $LisPrestVtaBon->EsGes;
                $LisPrestVta[$LisPrestVtaKey]['CodPatologia'] = $LisPrestVtaBon->CodPatologia;
                $LisPrestVta[$LisPrestVtaKey]['CodIntSanitaria'] = $LisPrestVtaBon->CodIntSanitaria;
                $LisPrestVta[$LisPrestVtaKey]['CodCanasta'] = $LisPrestVtaBon->CodCanasta;
                $LisPrestVta[$LisPrestVtaKey]['NumPieza'] = $LisPrestVtaBon->NumPieza;

                $oBonoDetalle = new BonoDetalle();
                $oBonoDetalle->setFolioBono($ListaBonosMulType->FolioBono);
                $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->findOneBy(array('codigoImed' => $LisPrestVtaBon->CodPrestacion));
                $oBonoDetalle->setIdAccionClinica($oAccionClinica);
                $oBonoDetalle->setIdPagoCuenta(null);
                $em->persist($oBonoDetalle);

                $ListaOtrasBonType = $LisPrestVtaBon->ListaOtrasBon;

                if ($ListaOtrasBonType !== null) {

                    foreach ($ListaOtrasBonType as $ListaOtrasBon) {
                        $LisPrestVta[$LisPrestVtaKey]['ListaOtrasBon']['CodBonAdic'] = $ListaOtrasBon->CodBonAdic;
                        $LisPrestVta[$LisPrestVtaKey]['ListaOtrasBon']['GloBonAdic'] = $ListaOtrasBon->GloBonAdic;
                        $LisPrestVta[$LisPrestVtaKey]['ListaOtrasBon']['MtoBonAdic'] = $ListaOtrasBon->MtoBonAdic;
                        $oBonoDetalleBonificacion = new BonoDetalleBonificacion();
                        $oBonoDetalleBonificacion->setIdBonoDetalle($oBonoDetalle);
                        $oBonoDetalleBonificacion->setCodigoBonoAdicional($ListaOtrasBon->CodBonAdic);
                        $oBonoDetalleBonificacion->setGlosaBonoAdicional($ListaOtrasBon->GloBonAdic);
                        $oBonoDetalleBonificacion->setMontoBonoAdicional($ListaOtrasBon->MtoBonAdic);
                        $em->persist($oBonoDetalleBonificacion);
                    }
                }

            }
            $em->flush();

        }
        return array(
            'ListaBonosMul' => $ListaBonosMul,
            'LisPrestVta' => $LisPrestVta,
            'ListaForPag' => $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaForPag,
            'oPagoCuenta' => null

        );

        //$ImedResponse['LisVenConv']['ListaBonosMul'][0]['LisPrestVta'] = $LisPrestVta;
        //$ImedResponse['LisVenConv']['ListaForPag'] = $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaForPag;
    }

    public function multipleArrayListaBonosMulType($ObtBonInterMul)
    {

        $ListaBonosMulTypes = $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaBonosMul->ListaBonosMulType;

        $em = $this->getDoctrine()->getManager();

        foreach ($ListaBonosMulTypes as $ListaBonosMulTypeKey => $ListaBonosMulType) {

            $ListaBonosMul[$ListaBonosMulTypeKey]['FolioBono'] = $ListaBonosMulType->FolioBono;
            $ListaBonosMul[$ListaBonosMulTypeKey]['FecEmi'] = $ListaBonosMulType->FecEmi;
            $ListaBonosMul[$ListaBonosMulTypeKey]['NumPrestBon'] = $ListaBonosMulType->NumPrestBon;
            $ListaBonosMul[$ListaBonosMulTypeKey]['NumBoleta'] = $ListaBonosMulType->NumBoleta;
            $ListaBonosMul[$ListaBonosMulTypeKey]['MontoAfecto'] = $ListaBonosMulType->MontoAfecto;
            $ListaBonosMul[$ListaBonosMulTypeKey]['MontoExento'] = $ListaBonosMulType->MontoExento;
            $ListaBonosMul[$ListaBonosMulTypeKey]['MontoTotal'] = $ListaBonosMulType->MontoTotal;

            $LisPrestVta = array();
            if ($ListaBonosMulType->NumPrestBon === 1) {

                $LisPrestVtaType = $ListaBonosMulType->LisPrestVta->LisPrestVtaType;

                $LisPrestVta[0]['CodPrestacion'] = $LisPrestVtaType->CodPrestacion;
                $LisPrestVta[0]['CodItem'] = $LisPrestVtaType->CodItem;
                $LisPrestVta[0]['Cantidad'] = $LisPrestVtaType->Cantidad;
                $LisPrestVta[0]['RecargoHora'] = $LisPrestVtaType->RecargoHora;
                $LisPrestVta[0]['MontoPrest'] = $LisPrestVtaType->MontoPrest;
                $LisPrestVta[0]['MontoBon'] = $LisPrestVtaType->MontoBon;
                $LisPrestVta[0]['MontoCopago'] = $LisPrestVtaType->MontoCopago;
                $LisPrestVta[0]['EsGes'] = $LisPrestVtaType->EsGes;
                $LisPrestVta[0]['CodPatologia'] = $LisPrestVtaType->CodPatologia;
                $LisPrestVta[0]['CodIntSanitaria'] = $LisPrestVtaType->CodIntSanitaria;
                $LisPrestVta[0]['CodCanasta'] = $LisPrestVtaType->CodCanasta;
                $LisPrestVta[0]['NumPieza'] = $LisPrestVtaType->NumPieza;

                $oBonoDetalle = new BonoDetalle();
                $oBonoDetalle->setFolioBono($ListaBonosMulType->FolioBono);
                $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->findOneBy(array('codigoImed' => $LisPrestVtaType->CodPrestacion));
                $oBonoDetalle->setIdAccionClinica($oAccionClinica);
                $oBonoDetalle->setIdPagoCuenta(null);
                $em->persist($oBonoDetalle);

                if (!is_null($LisPrestVtaType->ListaOtrasBon)) {
                    $ListaOtrasBon = $LisPrestVtaType->ListaOtrasBon->ListaOtrasBonType;
                    if(!is_array($ListaOtrasBon)){
                        $LisPrestVta[0]['ListaOtrasBon']['CodBonAdic'] = $ListaOtrasBon->CodBonAdic;
                        $LisPrestVta[0]['ListaOtrasBon']['GloBonAdic'] = $ListaOtrasBon->GloBonAdic;
                        $LisPrestVta[0]['ListaOtrasBon']['MtoBonAdic'] = $ListaOtrasBon->MtoBonAdic;

                        $oBonoDetalleBonificacion = new BonoDetalleBonificacion();
                        $oBonoDetalleBonificacion->setIdBonoDetalle($oBonoDetalle);
                        $oBonoDetalleBonificacion->setCodigoBonoAdicional($ListaOtrasBon->CodBonAdic);
                        $oBonoDetalleBonificacion->setGlosaBonoAdicional($ListaOtrasBon->GloBonAdic);
                        $oBonoDetalleBonificacion->setMontoBonoAdicional($ListaOtrasBon->MtoBonAdic);
                        $em->persist($oBonoDetalleBonificacion);
                    }else{
                        foreach ($ListaOtrasBon as $key  => $value){
                            $LisPrestVta[0]['ListaOtrasBon'][$key]['CodBonAdic'] = $value->CodBonAdic;
                            $LisPrestVta[0]['ListaOtrasBon'][$key]['GloBonAdic'] = $value->GloBonAdic;
                            $LisPrestVta[0]['ListaOtrasBon'][$key]['MtoBonAdic'] = $value->MtoBonAdic;

                            $oBonoDetalleBonificacion = new BonoDetalleBonificacion();
                            $oBonoDetalleBonificacion->setIdBonoDetalle($oBonoDetalle);
                            $oBonoDetalleBonificacion->setCodigoBonoAdicional($value->CodBonAdic);
                            $oBonoDetalleBonificacion->setGlosaBonoAdicional($value->GloBonAdic);
                            $oBonoDetalleBonificacion->setMontoBonoAdicional($value->MtoBonAdic);
                            $em->persist($oBonoDetalleBonificacion);
                        }
                    }

                } else {
                    $LisPrestVta[0]['ListaOtrasBon'] = null;
                }

            } else {

                $LisPrestVtaType = $ListaBonosMulType->LisPrestVta->LisPrestVtaType;

                foreach ($LisPrestVtaType as $LisPrestVtaKey => $LisPrestVtaBon) {
                    $LisPrestVta[$LisPrestVtaKey]['CodPrestacion'] = $LisPrestVtaBon->CodPrestacion;
                    $LisPrestVta[$LisPrestVtaKey]['CodItem'] = $LisPrestVtaBon->CodItem;
                    $LisPrestVta[$LisPrestVtaKey]['Cantidad'] = $LisPrestVtaBon->Cantidad;
                    $LisPrestVta[$LisPrestVtaKey]['RecargoHora'] = $LisPrestVtaBon->RecargoHora;
                    $LisPrestVta[$LisPrestVtaKey]['MontoPrest'] = $LisPrestVtaBon->MontoPrest;
                    $LisPrestVta[$LisPrestVtaKey]['MontoBon'] = $LisPrestVtaBon->MontoBon;
                    $LisPrestVta[$LisPrestVtaKey]['MontoCopago'] = $LisPrestVtaBon->MontoCopago;
                    $LisPrestVta[$LisPrestVtaKey]['EsGes'] = $LisPrestVtaBon->EsGes;
                    $LisPrestVta[$LisPrestVtaKey]['CodPatologia'] = $LisPrestVtaBon->CodPatologia;
                    $LisPrestVta[$LisPrestVtaKey]['CodIntSanitaria'] = $LisPrestVtaBon->CodIntSanitaria;
                    $LisPrestVta[$LisPrestVtaKey]['CodCanasta'] = $LisPrestVtaBon->CodCanasta;
                    $LisPrestVta[$LisPrestVtaKey]['NumPieza'] = $LisPrestVtaBon->NumPieza;

                    $oBonoDetalle = new BonoDetalle();
                    $oBonoDetalle->setFolioBono($ListaBonosMulType->FolioBono);
                    $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->findOneBy(array('codigoImed' => $LisPrestVtaBon->CodPrestacion));
                    $oBonoDetalle->setIdAccionClinica($oAccionClinica);
                    $oBonoDetalle->setIdPagoCuenta(null);
                    $em->persist($oBonoDetalle);

                    $ListaOtrasBonType = $LisPrestVtaBon->ListaOtrasBon;

                    if ($ListaOtrasBonType !== null) {
                        foreach ($ListaOtrasBonType as $ListaOtrasBonKey => $ListaOtrasBon) {
                            $LisPrestVta[$LisPrestVtaKey][$ListaOtrasBonKey]['ListaOtrasBon']['CodBonAdic'] = $ListaOtrasBon->CodBonAdic;
                            $LisPrestVta[$LisPrestVtaKey][$ListaOtrasBonKey]['ListaOtrasBon']['GloBonAdic'] = $ListaOtrasBon->GloBonAdic;
                            $LisPrestVta[$LisPrestVtaKey][$ListaOtrasBonKey]['ListaOtrasBon']['MtoBonAdic'] = $ListaOtrasBon->MtoBonAdic;
                            $oBonoDetalleBonificacion = new BonoDetalleBonificacion();
                            $oBonoDetalleBonificacion->setIdBonoDetalle($oBonoDetalle);
                            $oBonoDetalleBonificacion->setCodigoBonoAdicional($ListaOtrasBon->CodBonAdic);
                            $oBonoDetalleBonificacion->setGlosaBonoAdicional($ListaOtrasBon->GloBonAdic);
                            $oBonoDetalleBonificacion->setMontoBonoAdicional($ListaOtrasBon->MtoBonAdic);
                            $em->persist($oBonoDetalleBonificacion);
                        }
                    }
                }
            }
            $ListaBonosMul[$ListaBonosMulTypeKey]['LisPrestVta'] = $LisPrestVta;

            $em->flush();
        }

        return array(
            'ListaBonosMul' => $ListaBonosMul,
            //'LisPrestVta' => $aLisPrestVta,
            'ListaForPag' => $ObtBonInterMul['LisVenConv']->LisVenConvType->ListaForPag,
            'oPagoCuenta' => null
        );
    }

    public function verificarEsImed($arrPrestaciones)
    {
        $em = $this->getDoctrine()->getManager();
        $aPrestaciones = array();
        foreach ($arrPrestaciones as $prestacion) {
            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($prestacion['0']);
            if ($oAccionClinica->getEsImed()) {
                $aPrestaciones[] = $prestacion;
            }
        }
        return $aPrestaciones;
    }
}

