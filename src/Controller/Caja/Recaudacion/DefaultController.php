<?php

namespace App\Controller\Caja\Recaudacion;


use Rebsol\AdmisionBundle\Form\Type\IdentificacionType;
use App\Entity\Legacy\PersonaDomicilio;
use App\Controller\Caja\Recaudacion\render;
use App\Controller\Caja\RecaudacionController;
use App\Form\Recaudacion\Pago\BusquedaAvanzadaDirectorioPacienteType;
use App\Form\Recaudacion\Pago\DiferenciaType;
use App\Form\Recaudacion\Pago\MediosPagoType;
use App\Form\Recaudacion\Pago\PagoType;
use App\Form\Recaudacion\Pago\PrestacionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Process\Process;


/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 */
class DefaultController extends RecaudacionController
{

    var $arrSessionVarName;
    var $em;

    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);
        
        $this->arrSessionVarName = array(
            'idPacienteGarantia',
            'idPnaturalMascota',
            'idPnaturalCliente',
            'api',
            'pacienteApi',
            'persona',
            'garantia',
            'idReservaAtencion',
            'sucursal',
            'financiador',
            'convenio',
            'plan',
            'origen',
            'derivadoInt',
            'derivadoExt',
            'ListaPrestacion',
            'caja',
            'vSumaCantidad',
            'countPrestacionesArticulos',
            'idDiferencia',
            'idDiferenciaSaldo',
            'idSubEmpresaItem',
            'esTratamiento',
            'ListaDiferenciaSaldo',
            'ListaDiferencia');
    }

    /**
     * @return render
     * Descripción: IndexAction() Genera Vista de Recaudacion para exponer Pago y Apertura y Cierre de Caja
     */

    public function indexAction(Request $request)
    {
        $tipoPago =  $request->query->get('tipoPago');

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        $this->em = $this->getDoctrine()->getManager();
        $domiclio = new PersonaDomicilio();
        $Fecha = new \DateTime();
        $Fecha = $Fecha->format('Y-m-d');
        $idUser = $this->getUser();
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $idCantidad = 0;
        $reserva = 0;
        $TipoAtencion = 1;
        $sinReserva = null;
        $extranjero = null;
        $result = array();
        $arrayFormasPago = array();
        $arrayOtrosFormasPago = array();
        $oReservaAtencion = null;
        $arrPrestaciones = null;
        $this->setSession('from', 'caja');
        $this->setSession('esTratamiento', 0);
        $process = new Process($this->clearSessionsVar(), $this->AnularDiferenciasAyer());
        $process->run();

        $session = $request->getSession();

        $SucursalUsuario = $this->em->getRepository('App\Entity\Legacy\UsuariosRebsol')->obtenerSucursalUsuario($this->getUser()->getId());

        $oUbicacionCajero = $this->em->getRepository('App\Entity\Legacy\RelUbicacionCajero')->findOneBy(array(
                'idUsuario' => $idUser,
                'idEstado' => $this->parametro('Estado.activo')
            )
        );

        $arrParametroHabilitarPaisExtranjero = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('HABILITAR_PAIS_NACIONALIDAD_EXTRANJERO');
        $habilitarPaisExtranjero = intval($arrParametroHabilitarPaisExtranjero['valor']);


        /*
         * Redirige a recaudacionIndexAction en el controlador \Rebsol\CajaBundle\Controller\Api\Caja\Recaudacion\RecaudacionController
         */
        if ($estadoApi !== 'core') {

            return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->getParameter("caja.idModulo"))));
        }

//        dump('3');exit;
        if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()) {

            $session->getFlashBag()->add('errorCajaRecaudacion', $this->ErrorImedHermes('errorCajaRecaudacion'));

            return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->getParameter("caja.idModulo"))));
        }

        $folioGlobal = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('FOLIO_GLOBAL');
        $oTalonario = $this->em->getRepository('App\Entity\Legacy\Talonario')->findBy(
            array(
                'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $oUbicacionCajero->getidUbicacionCaja()->getid() : null,
                'idEstado' => $this->parametro('Estado.activo'),
                'idEstadoPila' => $this->getParameter('EstadoPila.activo')
            )
        );

        if (!$oTalonario) {

            $session->getFlashBag()->add('errorCajaRecaudacionSinDOcumento', 'No se ha asignado Boleta a esta Caja');

            return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->getParameter("caja.idModulo"))));
        }


        $idFormaspago = $this->rFormaPago()->ObtieneFormaPago();

        if ($idFormaspago) {

            foreach ($idFormaspago as $id) {

                $arrayFormasPago[] = $id['id'];
            }
        }

        $ListadoMediosPago = $this->rFormaPago()->ListadoFormasDePagoParaMediosPago();
        $ListadoOtrosMedios = $this->rFormaPago()->ListadoFormasDePagoParaOtrosMedios();

        if ($ListadoOtrosMedios) {
            foreach ($ListadoOtrosMedios as $id) {

                $arrayOtrosFormasPago[] = $id['id'];
            }
        }

        $Validacion = $this->ValidacionComplementariaCaja($idUser->getid(), $Fecha, $session, $reserva, null, $arrPrestaciones);
        $arrayParams = array(
            'oEmpresa'                => $oEmpresa->getId(),
            'arrayFormasPago'         => $arrayFormasPago,
            'arrayOtrosFormasPago'    => $arrayOtrosFormasPago,
            'idCantidad'              => $idCantidad,
            'SucursalUsuario'         => $SucursalUsuario->getId(),
            'domiclio'                => $domiclio,
            'habilitarPaisExtranjero' => $habilitarPaisExtranjero
        );

        $form = $this->Forms($arrayParams);
        $datosCompletos = 0;

        $habilitaAplicarDiferenciaIndividual = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('APLICAR_DIFERENCIA_INDIVIDUAL');
        $habilitaAplicarDiferenciaSaldo = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('APLICAR_DIFERENCIA_SALDO');
        $habilitaRestriccionesDePago = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('HABILITAR_RESTRICCIONES_DE_PAGO');
        $imedUrl = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('IMED_URL_INTERFAZ_PROD');
        $formasPagoBono = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('TIPO_FORMAS_PAGO_BONOS');
        $formasPagoBono = explode(',', $formasPagoBono['valor']);

        $arrayBonosFormasPago = array();
        if ($ListadoMediosPago) {
            foreach ($ListadoMediosPago as $medioPago) {
                if (in_array($medioPago['tipo'],$formasPagoBono)) {
                    $arrayBonosFormasPago[] = $medioPago;
                }
            }
        }
//dump($arrayBonosFormasPago);
//        exit;
        return $this->render('RecaudacionBundle:Recaudacion:Base.html.twig', array(
                /** Formularios Directorio Paciente */
                'form' => $form['form1'],
                'tipoIdentificacionExtranjeroForm' => $form['tipoIdentificacionExtranjeroForm']->createView(),
                'habilitarPaisExtranjero' => $habilitarPaisExtranjero,
                /** Formularios Caja */
                'pago_form' => $form['Pago'],
                'mediospago_form' => $form['MediosPago'],
                'prestacion_form' => $form['Prestacion'],
                'diferencia_form' => $form['Diferencia'],
                /** Estados desde 'ValidacionComplementariaCaja */
                'sincerrar' => $Validacion['sincerrar'],
                'sintalonario' => $Validacion['sintalonario'],
                'sintalonarioAE' => $Validacion['sintalonarioAE'],
                'abierta' => $Validacion['open'],
                'cerrada' => $Validacion['close'],
                'pagoTodosLosDias' => $Validacion['pagoTodosLosDias'],
                /** Estados desde 'EstadosCaja' */
                'estadoReapertura' => $this->estado('EstadoReaperturaAbierta'),
                'coreApi' => ($estadoApi === "core") ? 1 : 0,
                'from' => $this->getSession('from'),
                /** Estados Caja */
                'idReservaAtencion' => $sinReserva,
                'extranjero' => $extranjero,
                'cantidad' => $idCantidad,
                /** Arrays desde 'ValidacionComplementariaCaja' */
                'talonarios' => $Validacion['oTalonario'],
                'TalonarioNumeroActual' => $Validacion['TalonarioNumeroActual'],
                'subEmpresa' => $Validacion['subEmpresa'],
                'caja' => $Validacion['caja'],
                'getPrestacionesCaja' => $Validacion['getPrestacionesCaja'],
                /** Arrays Caja */
                'listadoMediosPagos' => $ListadoMediosPago,
                'listadoOtrosMedios' => $ListadoOtrosMedios,
                'arrayBonosFormasPago' => $arrayBonosFormasPago,
                'resultados' => $result,
                'habilitaRestriccionesDePago' => $habilitaRestriccionesDePago['valor'],
                'habilitaAplicarDiferenciaIndividual' => $habilitaAplicarDiferenciaIndividual['valor'],
                'habilitaAplicarDiferenciaSaldo' => $habilitaAplicarDiferenciaSaldo['valor'],
                /** Arrays desde Funciones o Repositorios */
                'errores' => $this->ValidacionesDocumentosFaltantes($SucursalUsuario, $this->parametro('Estado.activo'), $oEmpresa->getId()),
                /**  IDs Caja */
                'sucursal' => $SucursalUsuario->getId(),
                'tipoAtencion' => $TipoAtencion,
                'reserva' => $reserva,
                /** IDs desde Funciones o Repositorios */
                'banco' => $this->rFormaPago()->ObtieneBancoCaja($this->parametro('Estado.activo'), $oEmpresa),
                'cajero' => $this->rPagoCuenta()->GetCajeroByUser($idUser->getId()),
                'datosCompletos' => $datosCompletos,
                'imedUrl' => $imedUrl['valor'],
                'tipoPago' => $tipoPago
            )
        );
    }

    /**
     * INGRESO DESDE AGENDAMIENTO WEB
     */
    public function indexPagoAction(request $request, $id, $tipoPago)
    {

        $this->em = $this->getDoctrine()->getManager();
        $domiclio = new PersonaDomicilio();
        $Fecha = new \DateTime();
        $Fecha = $Fecha->format("Y-m-d");
        $idUser = $this->getUser();
        $oEmpresa = $this->ObtenerEmpresaLogin();

        $idCantidad = 0;
        $pagoefectuado = 0;
        $PagoesGarantia = 0;
        $garan = 0;
        $TipoAtencion = 1;
        $reserva = 1;

        $datosPago = null;
        $dhp = null;
        $oPaciente = null;
        $datosPacienteVistaG = null;
        $estadoPago = null;

        $result = array();
        $arrPrestaciones = array();
        $arrayFormasPago = array();
        $arrayOtrosFormasPago = array();

        $arrParametroHabilitarPaisExtranjero = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('HABILITAR_PAIS_NACIONALIDAD_EXTRANJERO');
        $habilitarPaisExtranjero = intval($arrParametroHabilitarPaisExtranjero['valor']);

        /** Informacion API */
        $idModulo = $this->getParameter('modulo_caja');
        $estadoApi = $this->obtenerApiModulo($idModulo);

        $idReserva = $id;

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }
        if ($estadoApi !== 'core') {
            return $this->forward('RecaudacionBundle:Api\Caja\Recaudacion\Recaudacion:index', array(
                    'idReservaAtencion' => $id
                )
            );
        }

        /** Limpia Variables de Session */
        $process = new Process($this->clearSessionsVar(), $this->AnularDiferenciasAyer());
        $process->run();
        $this->setSession('from', 'agenda');
        $this->setSession('esTratamiento', 0);

        $session = $request->getSession();

        $oUbicacionCajero = $this->em->getRepository('App\Entity\Legacy\RelUbicacionCajero')->findOneBy(
            array(
                'idUsuario' => $idUser->getId(),
                'idEstado' => $this->parametro('Estado.activo')
            )
        );

        $oReservaAtencion = $this->em->getRepository('App\Entity\Legacy\ReservaAtencion')->find($id);
        $SucursalUsuario = $this->em->getRepository('App\Entity\Legacy\UsuariosRebsol')->obtenerSucursalUsuario($idUser->getId());

        if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()) {
            echo "<div class='alert alert-warning'> <button type='button' class='close' data-dismiss='alert'> <i class='icon-remove'></i> </button>
			<strong>Información: </strong> No se ha asignado una Ubicación como Cajero, debe ponerse en contacto con el Supervisor. <br></div>";
            exit(-1);
        }

        $folioGlobal = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('FOLIO_GLOBAL');

        $oTalonario = $this->em->getRepository('App\Entity\Legacy\Talonario')->findBy(
            array(
                'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $oUbicacionCajero->getidUbicacionCaja()->getid(): null,
                'idEstado' => $this->parametro('Estado.activo'),
                'idEstadoPila' => $this->getParameter('EstadoPila.activo')
            )
        );

        if (!$oTalonario) {

            echo "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'> <i class='icon-remove'></i> </button>
			<strong>Información: </strong> No se ha asignado Boleta a esta Caja. <br></div>";
            exit(-1);
        }

        /** PAGO GENERADO */
        if ($oReservaAtencion->getIdPagoCuenta()) {

            $estadoPago = $oReservaAtencion->getIdPagoCuenta()->getIdEstadoPago()->getId();

            if ($estadoPago == 1) {

                return $this->PagoGenerado($oReservaAtencion, $PagoesGarantia, $Fecha);
            } else {

                $pagoefectuado = 1;
            }

            if ($estadoPago == 2) {
                $datosPago = array('fechaPago' => $oReservaAtencion->getIdPagoCuenta()->getFechaPago());
                $oPaciente = $this->em->getRepository("App\Entity\Legacy\Paciente")
                    ->find($oReservaAtencion->getIdPaciente());
                $dhp = $this->rPaciente()->HistoricoPagosIdPacienteGarantia($oPaciente->getId());
                $estadoPago = 1;
            }
        } else {

            $estadoPago = 0;
            $pagoefectuado = 1;
        }
///aqui
        $arrCliente = $this->ClienteDataArray($oReservaAtencion, $oEmpresa->getId(), $garan);
        $oAccionClinica = $this->rPagoCuenta()->GetAccionesClinicas($oReservaAtencion->getId());

        if ($oAccionClinica) {

            foreach ($oAccionClinica as $ac) {

                $arrPrestaciones[] = $ac['id'];
            }
        }

        $idFormaspago = $this->rFormaPago()->ObtieneFormaPago();

        if ($idFormaspago) {

            foreach ($idFormaspago as $id) {

                $arrayFormasPago[] = $id['id'];
            }
        }

        $ListadoMediosPago = $this->rFormaPago()->ListadoFormasDePagoParaMediosPago();
        $ListadoOtrosMedios = $this->rFormaPago()->ListadoFormasDePagoParaOtrosMedios();

        if ($ListadoOtrosMedios) {
            foreach ($ListadoOtrosMedios as $id) {
                $arrayOtrosFormasPago[] = $id['id'];
            }
        }

        /** Validaciones y Avisos de errores en Vista */
        if ($oReservaAtencion->getidHorarioConsulta()->getIdTipoConsulta()->getEsTratamiento() == 0) {
            $this->setSession('esTratamiento', 1);
        }

        $validacion = $this->ValidacionComplementariaCaja($idUser->getid(), $Fecha, $session, $reserva, $oReservaAtencion->getidHorarioConsulta()->getid(), $arrPrestaciones);


        if (intval($validacion['pagoTodosLosDias']) == 0 && $validacion['fechaPago'] == 0) {

            echo "<div class='alert alert-warning'> <button type='button' class='close' data-dismiss='alert'> <i class='icon-remove'></i> </button>
			<strong>Información: </strong> Solo está autorizado a pagar Reservas a partir de <strong>Hoy en adelante.</strong> <br></div>";
            exit(-1);
        }

        $sincerrar = ($validacion['sincerrar'] == 0);
        $sintalonario = ($validacion['sintalonario'] == 0);
        $open = ($validacion['open'] == 0);
        $subEmpresaTalonarioPrestacion = ($validacion['subEmpresaTalonarioPrestacion'] == 0);
        $close = ($validacion['close'] == 0);
        $noCajero = ($validacion['noCajero'] == 1);


        if ($sincerrar || $sintalonario || $open || $subEmpresaTalonarioPrestacion || $close || $noCajero) {

            return $this->render('RecaudacionBundle:Recaudacion:indexReserva.html.twig', array(
                    /** Estados desde 'validacionComplementariaCaja */
                    'coreApi' => ($estadoApi === "core") ? 1 : 0,
                    'sincerrar' => $validacion['sincerrar'],
                    'sintalonario' => $validacion['sintalonario'],
                    'sintalonarioAE' => $validacion['sintalonarioAE'],
                    'abierta' => $validacion['open'],
                    'cerrada' => $validacion['close'],
                    'noCajero' => $validacion['noCajero'],
                    'pagoTodosLosDias' => $validacion['pagoTodosLosDias'],
                    'subEmpresaTalonarioPrestacion' => $validacion['subEmpresaTalonarioPrestacion'],
                    'PagoesGarantia' => $estadoPago,
                    'PagoEfectuado' => $pagoefectuado,
                    'talonarios' => $validacion['oTalonario'],
                    'caja' => $validacion['caja'],
                    'FechaPagoAgenda' => $validacion['fechaPago'],
                    'habilitarPaisExtranjero' => $habilitarPaisExtranjero,
                    'tipoPago'  => $tipoPago
                )
            );

        }

        $arrayParams = array(
            'oEmpresa'                => $oEmpresa->getId(),
            'arrayFormasPago'         => $arrayFormasPago,
            'arrayOtrosFormasPago'    => $arrayOtrosFormasPago,
            'idCantidad'              => $idCantidad,
            'SucursalUsuario'         => $SucursalUsuario->getId(),
            'domiclio'                => $domiclio,
            'habilitarPaisExtranjero' => $habilitarPaisExtranjero
        );

        $form = $this->Forms($arrayParams);
        $datosCompletos = 0;

        if (!empty($arrCliente)) {

            $datosCompletos = 1;
        }

        if ($arrCliente['idTipoIdentificacionExtranjero'] !== null) {
            $form['tipoIdentificacionExtranjeroForm']['tipoIdentificacion']->setData($arrCliente['idTipoIdentificacionExtranjero']);
        }

        // echo "<pre>"; \Doctrine\Common\Util\Debug::dump( $arrCliente['idTipoIdentificacionExtranjero'] ); exit(-1);

        $datosProfesionalDefecto = null;
        if ($idReserva != null) {
            $oReserva = $this->em->getRepository("App\Entity\Legacy\ReservaAtencion")->find($idReserva);

            if (!is_null($oReserva) && !is_null($oReserva->getIdHorarioConsulta())
                && !is_null($oReserva->getIdHorarioConsulta()->getIdHorarioConsultaSala()) && !is_null($oReserva->getIdHorarioConsulta()->getIdHorarioConsultaSala()->getIdUsuario())) {
                $oPersona = $oReserva->getIdHorarioConsulta()->getIdHorarioConsultaSala()->getIdUsuario()->getIdPersona();

                $oPnatural = $this->em->getRepository("App\Entity\Legacy\Pnatural")->findOneBy(['idPersona' => $oPersona]);

                $datosProfesionalDefecto = [
                    'rutDefecto' => $oPersona->getIdentificacionExtranjero(),
                    'observacionDefecto' => 'Examen de procedimientos',
                    'nombreDefecto' => $oPnatural->getNombrePnatural(),
                    'apellidoPaternoDefecto' => $oPnatural->getApellidoPaterno(),
                    'apellidoMaternoDefecto' => $oPnatural->getApellidoMaterno()
                ];
            }
        }
        $habilitaRestriccionesDePago = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('HABILITAR_RESTRICCIONES_DE_PAGO');
        $habilitaAplicarDiferenciaIndividual = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('APLICAR_DIFERENCIA_INDIVIDUAL');
        $habilitaAplicarDiferenciaSaldo = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('APLICAR_DIFERENCIA_SALDO');
        $imedUrl = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('IMED_URL_INTERFAZ_PROD');
        $formasPagoBono = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('TIPO_FORMAS_PAGO_BONOS');
        $formasPagoBono = explode(',', $formasPagoBono['valor']);

        $arrayBonosFormasPago = array();
        if ($ListadoMediosPago) {
            foreach ($ListadoMediosPago as $medioPago) {
                if (in_array($medioPago['tipo'],$formasPagoBono)) {
                    $arrayBonosFormasPago[] = $medioPago;
                }
            }
        }

        return $this->render('RecaudacionBundle:Recaudacion:indexReserva.html.twig', array(
                /** Formularios Caja */
                'form' => $form['form1'],
                'tipoIdentificacionExtranjeroForm' => $form['tipoIdentificacionExtranjeroForm']->createView(),
                'habilitarPaisExtranjero' => $habilitarPaisExtranjero,
                'pago_form' => $form['Pago'],
                'mediospago_form' => $form['MediosPago'],
                'prestacion_form' => $form['Prestacion'],
                'diferencia_form' => $form['Diferencia'],
                /** Estados desde 'ValidacionComplementariaCaja */
                'sincerrar' => $validacion['sincerrar'],
                'sintalonario' => $validacion['sintalonario'],
                'sintalonarioAE' => $validacion['sintalonarioAE'],
                'abierta' => $validacion['open'],
                'cerrada' => $validacion['close'],
                'noCajero' => $validacion['noCajero'],
                'pagoTodosLosDias' => $validacion['pagoTodosLosDias'],
                'getPrestacionesCaja' => $validacion['getPrestacionesCaja'],
                'subEmpresaTalonarioPrestacion' => $validacion['subEmpresaTalonarioPrestacion'],
                /** Estados desde 'EstadosCaja' */
                'estadoReapertura' => $this->getParameter('EstadoReapertura.abierta'),
                'coreApi' => ($estadoApi === "core") ? 1 : 0,
                'from' => $this->getSession('from'),
                /** Estados Caja */
                'idReservaAtencion' => $id,
                'cantidad' => $idCantidad,
                'PagoesGarantia' => $estadoPago,
                'PagoEfectuado' => $pagoefectuado,
                /** Arrays desde 'ValidacionComplementariaCaja' */
                'talonarios' => $validacion['oTalonario'],
                /** Arrays Caja */
                'listadoMediosPagos' => $ListadoMediosPago,
                'listadoOtrosMedios' => $ListadoOtrosMedios,
                'arrayBonosFormasPago' => $arrayBonosFormasPago,
                'habilitaRestriccionesDePago' => $habilitaRestriccionesDePago['valor'],
                'habilitaAplicarDiferenciaIndividual' => $habilitaAplicarDiferenciaIndividual['valor'],
                'habilitaAplicarDiferenciaSaldo' => $habilitaAplicarDiferenciaSaldo['valor'],
                'resultados' => $result,
                'prestaciones' => $arrPrestaciones,
                'datosPacienteVista' => $arrCliente,
                'extranjero' => $arrCliente['extranjero'],
                /** Arrays desde Funciones o Repositorios */
                'errores' => $this->ValidacionesDocumentosFaltantes($SucursalUsuario, $this->parametro('Estado.activo'), $oEmpresa->getId()),
                /** IDs y/o Fecha Caja */
                'sucursal' => $SucursalUsuario->getId(),
                'tipoAtencion' => $TipoAtencion,
                'caja' => $validacion['caja'],
                'reserva' => $reserva,
                'IdFinanciador' => $oReservaAtencion->getIdPrevision()->getId(),
                'FechaPagoAgenda' => $validacion['fechaPago'],
                /** IDs desde Funciones o Repositorios */
                'banco' => $this->rFormaPago()->ObtieneBancoCaja($this->parametro('Estado.activo'), $oEmpresa),
                'cajero' => $this->rPagoCuenta()->GetCajeroByUser($idUser->getId()),
                /** si tiene garantia pendiente */
                'datosPago' => $datosPago,
                'historico' => $dhp,
                'paciente' => $oPaciente,
                'datosPacienteVistaG' => ($estadoPago == 1) ? $this->ClienteDataGarantiaArray($oReservaAtencion) : null,
                'datosCompletos' => $datosCompletos,
                'datosProfesionalDefecto' => $datosProfesionalDefecto,
                'imedUrl' => $imedUrl['valor'],
                'tipoPago'  => $tipoPago
            )
        );

    }

    /** Secundary Function (Private) */
    protected function PagoGenerado($oReservaAtencion, $PagoesGarantia, $Fecha)
    {

        $oPago = $this->em->getRepository('App\Entity\Legacy\PagoCuenta')->find($oReservaAtencion->getIdPagoCuenta()->getId());
        $datosPago = array('fechaPago' => $oPago->getFechaPago());
        $oPaciente = $this->em->getRepository('App\Entity\Legacy\Paciente')->find($oReservaAtencion->getIdPaciente());

        $dhp = $this->rPaciente()->HistoricoPagosIdPaciente($oPaciente->getId());
        $oEstadoTalonarioEmitida = $this->estado('EstadoBoletaActiva');
        $oHorario = $this->em->getRepository("App\Entity\Legacy\HorarioConsulta")->find($oReservaAtencion->getidHorarioConsulta()->getid());
        $oHorario = $oHorario->getFechaInicioHorario();
        $FechaHorario = $oHorario->format("Y-m-d");

        $fechaPago = (strtotime($Fecha) == strtotime($FechaHorario)) ? 1 : 0;
        //si es 1 puede pagar HOY desde Agenda
        //si es 0 NO puede pagar HOY desde Agenda
        $PagoesGarantia = ($oPago->getIdEstadoPago()->getId() == 2) ? 1 : 0;

        if ($oPago->getIdEstadoPago()->getId() == 1 && $fechaPago == 1) {

            return $this->render('RecaudacionBundle:Recaudacion/PostPago:Exitoso.html.twig', array(
                    'datosPago' => $datosPago,
                    'historico' => $dhp,
                    'paciente' => $oPaciente,
                    'datosPacienteVista' => $this->ClienteDataGarantiaArray($oReservaAtencion),
                    'estadoT' => $oEstadoTalonarioEmitida->getId(),
                    'from' => $this->getSession('from')
                )
            );
        }

        if ($oPago->getIdEstadoPago()->getId() == 1 && $fechaPago == 0) {

            // echo "<pre>"; \Doctrine\Common\Util\Debug::dump($this->ClienteDataGarantiaArray($oReservaAtencion)); exit(-1);
            return $this->render('RecaudacionBundle:Recaudacion/PostPago:ExitosoInfoAnterior.html.twig', array(
                'datosPago' => $datosPago,
                'historico' => $dhp,
                'paciente' => $oPaciente,
                'datosPacienteVistaG' => $this->ClienteDataGarantiaArray($oReservaAtencion),
                'estadoT' => $this->estado('EstadoBoletaActiva')
            ));
        }

    }

    protected function ClienteDataArray($oReservaAtencion, $idEmpresa, $garan)
    {

        $oPersona = $this->em->getRepository('App\Entity\Legacy\Persona')->findOneBy(array(
                'idTipoIdentificacionExtranjero' => $oReservaAtencion->getIdTipoIdentificacionExtranjero()
            , 'identificacionExtranjero' => $oReservaAtencion->getIdentificacionExtranjero()
            )
        );

        $datosPacienteVista = array(
            'idTipoIdentificacionExtranjero' => $oReservaAtencion->getIdTipoIdentificacionExtranjero(),
            'identificacionExtranjero' => $oReservaAtencion->getIdentificacionExtranjero(),
            'id' => $oReservaAtencion->getId(),
            'nombre' => $oReservaAtencion->getNombres(),
            'ApellidoPaterno' => $oReservaAtencion->getApellidoPaterno(),
            'ApellidoMaterno' => $oReservaAtencion->getApellidoMaterno(),
            'rut' => $oReservaAtencion->getRutPaciente(),
            'dv' => $oReservaAtencion->getDigitoVerificadorPaciente(),
            'fechaNacimiento' => $oReservaAtencion->getFechaNacimiento(),
            'sexoId' => $oReservaAtencion->getIdSexo()->getId(),
            'sexo' => $oReservaAtencion->getIdSexo()->getNombreSexo(),
            'correoElectronico' => $oReservaAtencion->getcorreoElectronico(),
            'telefonoMovil' => $oReservaAtencion->gettelefonoMovil(),
            'telefonoFijo' => $oReservaAtencion->gettelefonoFijo(),
            'telefonoContacto' => $oReservaAtencion->gettelefonoContacto(),
            'direccion' => $oReservaAtencion->getDireccion(),
            'numero' => $oReservaAtencion->getNumero(),
            'restoDireccion' => $oReservaAtencion->getrestoDireccion(),
            'financiador' => $oReservaAtencion->getidPrevision()->getid(),
            'usuario' => ($oReservaAtencion->getidUsuarioFuncionario()) ? $oReservaAtencion->getidUsuarioFuncionario()->getid() : null,
            'funcionario' => ($oReservaAtencion->getidUsuarioFuncionario()) ? $oReservaAtencion->getidUsuarioFuncionario()->getid() : null,
            'esTratamiento' => $oReservaAtencion->getidHorarioConsulta()->getIdTipoConsulta()->getEsTratamiento(),
            'empresaSolicitante' => $oReservaAtencion->getIdEmpresaSolicitante()? $oReservaAtencion->getIdEmpresaSolicitante()->getNombre() : '',
            'idPersona' =>  $oPersona->getId()
        );

        if ($oPersona) {

            $datosPacienteVista['garantias'] = $this->GetNumGarantias($oPersona->getId(), $garan);
        } else {

            $datosPacienteVista['garantias'] = 0;
        }

        $datosPacienteVista['idPais'] = ($oReservaAtencion->getidPais()) ? $oReservaAtencion->getidPais()->getId() : null;
        $datosPacienteVista['covenio'] = ($oReservaAtencion->getidConvenio() != null) ? $oReservaAtencion->getidConvenio()->getid() : null;
        $datosPacienteVista['idComuna'] = ($oReservaAtencion->getidComuna()) ? $oReservaAtencion->getidComuna()->getId() : null;
        $datosPacienteVista['profesionalExterno'] = ($oReservaAtencion->getidUsuarioExterno()) ? $oReservaAtencion->getidUsuarioExterno()->getid() : null;
        $datosPacienteVista['profesional'] = ($oReservaAtencion->getIdUsuarioProfesional() != null) ? $this->GetIdPnaturalProfesional($oReservaAtencion->getIdUsuarioProfesional()->getIdPersona()->getId()) : null;

        if ($oReservaAtencion->getidTipoIdentificacionExtranjero() != null) {

            $datosPacienteVista['extranjero'] = $oReservaAtencion->getidTipoIdentificacionExtranjero()->getid();

            $oTipoDocumento = $oReservaAtencion->getidTipoIdentificacionExtranjero();
            $datosPacienteVista['identificacion'] = $oReservaAtencion->getIdentificacionExtranjero();
            $datosPacienteVista['nombreDocumento'] = $oTipoDocumento->getNombre();
        } else {

            $datosPacienteVista['extranjero'] = null;
            $datosPacienteVista['identificacion'] = null;
            $datosPacienteVista['nombreDocumento'] = null;
        }

        return $datosPacienteVista;
    }

    protected function GetNumGarantias($idPersona, $garan)
    {
        if ($idPersona) {
            $resultadoGarantias = $this->rPagoCuenta()->GetPagoCuentaByIdPersona($idPersona);
            if ($resultadoGarantias) {
                foreach ($resultadoGarantias as $g) {
                    $garan = $garan + 1;
                }
            }
        }
        return $garan;
    }

    protected function ClienteDataGarantiaArray($oReservaAtencion)
    {

        return array(
            'id' => $oReservaAtencion->getId(),
            'identificacionExtranjero' => $oReservaAtencion->getIdentificacionExtranjero(),
            'tipoIdentificacionExtranjero' => ($oReservaAtencion->getidTipoIdentificacionExtranjero() !== null) ? $oReservaAtencion->getidTipoIdentificacionExtranjero()->getNombre() : null,
            'rut' => $oReservaAtencion->getRutPaciente(),
            'dv' => $oReservaAtencion->getDigitoVerificadorPaciente(),
            'nombre' => $oReservaAtencion->getNombres(),
            'ApellidoPaterno' => $oReservaAtencion->getApellidoPaterno(),
            'ApellidoMaterno' => $oReservaAtencion->getApellidoMaterno(),
            'fechaNacimiento' => $oReservaAtencion->getFechaNacimiento(),
            'sexo' => $oReservaAtencion->getIdSexo()->getNombreSexo(),
            'fechaR' => $oReservaAtencion->getFechaRecepcion(),
            'fechaAtencion' => $oReservaAtencion->getFechaAtencion()
        );
    }

    protected function ValidacionesDocumentosFaltantes($SucursalUsuario, $estado, $oEmpresa)
    {

        $failsMesages = array();
        $countPlan = 0;
        $countProf = 0;

        $oOrigen = $this->em->getRepository('App\Entity\Legacy\Origen')->findBy(array(
            "idSucursal" => $SucursalUsuario,
            "idEstado" => $estado));
        $oRolProfesional = $this->em->getRepository('App\Entity\Legacy\RolProfesional')->findBy(array(
            "idRol" => $this->getParameter('rol_medico'),
            "idEstado" => $estado));
        $oPrevision = $this->em->getRepository('App\Entity\Legacy\Prevision')->findBy(array(
            "idEmpresa" => $oEmpresa,
            "idEstado" => $estado));
        $oTipoPrevision = $this->em->getRepository('App\Entity\Legacy\TipoPrevision')->findBy(array(
            "idEmpresa" => $oEmpresa,
            "esConvenio" => 1,
            "idEstado" => $estado));

        if (!$oOrigen) {
            $failOrigen = "Origen";
            $failsMesages[] = $failOrigen;
        }

        if ($oRolProfesional) {
            foreach ($oRolProfesional as $c) {
                $countProf = $countProf + 1;
            }
        }

        if ($countProf == 0) {
            $failProfesionales = "Profesionales";
            $failsMesages[] = $failProfesionales;
        }

        if (!$oPrevision) {
            $failFinanciador = "Financiador";
            $failsMesages[] = $failFinanciador;
        }

        if (!$oTipoPrevision) {
            $failConvenios = "Convenios";
            $failsMesages[] = $failConvenios;
        }

        $auxRelSucPre = array();
        foreach ($oPrevision as $pr) {
            $oRelSucursalPrevision = $this->em->getRepository('App\Entity\Legacy\RelSucursalPrevision')->findOneBy(array(
                "idSucursal" => $SucursalUsuario,
                "idPrevision" => $pr->getid(),
                "idEstado" => $estado
            ));

            if ($oRelSucursalPrevision) {
                $auxRelSucPre[] = $oRelSucursalPrevision->getid();
            }
        }

        foreach ($auxRelSucPre as $c) {

            $oPrPlan = $this->em->getRepository('App\Entity\Legacy\PrPlan')->findBy(array(
                "idRelSucursalPrevision" => $c,
                "idEstado" => $estado
            ));
            if ($oPrPlan) {
                $countPlan = $countPlan + 1;
            }
        }

        if ($countPlan == 0) {
            $failPlanes = "Planes";
            $failsMesages[] = $failPlanes;
        }

        return $failsMesages;
    }

    protected function ValidacionComplementariaCaja($idUser, $Fecha, $session, $reserva, $idHorarioConsulta, $arrPrestaciones)
    {

        $x = 0;
        $y = 0;
        $af = 0;
        $auxReaperturaCount = 0;
        $noCajero = 0;
        $subEmpresaTalonarioPrestacion = 0;
        $fechaPago = null;
        $TalonarioNumeroActual = null;
        $arrTalonario[] = array();
        $subEmpresa[] = array();
        $arrTalonarioNumeroActual = array();
        $oCajaFindByUser = $this->em->getRepository('App\Entity\Legacy\Caja')->findBy(array("idUsuario" => $idUser));

        if ($oCajaFindByUser) {
            foreach ($oCajaFindByUser as $c) {
                $estadoTemp = (!is_null($c->getIdEstadoReapertura())) ? $c->getIdEstadoReapertura()->getId() : null;
                if ($estadoTemp && $estadoTemp == $this->getParameter('EstadoReapertura.abierta')) {
                    $auxReaperturaCount = $auxReaperturaCount + 1;
                    $oCajaTemp = $c;
                    $fechaReapertura = $c->getFechaReapertura();
                    break;
                } else {
                    $auxReaperturaCount = $auxReaperturaCount + 0;
                }
            }
        }

        if ($auxReaperturaCount > 0) {
            $fechaReapertura = $fechaReapertura->format("Y-m-d");
            if (strtotime($Fecha) > strtotime($fechaReapertura)) {
                $sincerrar = 0;
            } else {
                $sincerrar = 1;
            }
            $open = 1;
            $close = 1;
            $oCaja = $oCajaTemp;
            $oTalonario = $this->em->getRepository('App\Entity\Legacy\Talonario')->findOneBy(array(
                "idUbicacionCaja" => $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid(),
                "idEstado" => $this->parametro('Estado.activo'),
                "idEstadoPila" => $this->getParameter('EstadoPila.activo')
            ));
        } else {

            $oCaja = $this->rPagoCuenta()->GetCajaByUser($idUser, $Fecha);

            if ($oCaja) {
                $this->get('session')->set('VarCajaHoy', $oCaja->getId());

                $sincerrar = 1;
                $fechaCierre = $oCaja->getfechaCierre();
                $open = 1;

                if ($fechaCierre) {
                    $fechaCierre = $fechaCierre->format("Y-m-d");
                    if (strtotime($fechaCierre) == strtotime($Fecha)) {
                        $close = 0;
                    } else {
                        $close = 1;
                    }
                } else {
                    $close = 1;
                }
            } else {
                $open = 0;
                $close = 0;
                $sincerrar = 0;
                $oCaja = NULL;
                foreach ($oCajaFindByUser as $csc) {
                    $fechaApertura = $csc->getfechaApertura();
                    $fechaCierre = $csc->getfechaCierre();
                    $fechaApertura = $fechaApertura->format("Y-m-d");
                    if (strtotime($Fecha) > strtotime($fechaApertura)) {
                        if ($fechaCierre === NULL) {

                            $sincerrar = 0;
                            $oCaja = $csc;
                        } else {
                            $sincerrar = 1;
                            $oCaja = $csc;
                        }
                    } else {
                        $sincerrar = 1;
                        $oCaja = $csc;
                    }

                    if ($sincerrar == 0) {
                        $oCaja = $csc;
                        break;
                    }
                }
            }
        }
        $folioGlobal = $this->em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('FOLIO_GLOBAL');

        if ($oCaja) {

            $oTalonario = $this->em->getRepository('App\Entity\Legacy\Talonario')->findBy(
                array(
                    'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid() : null,
                    'idEstado' => $this->parametro('Estado.activo'),
                    'idEstadoPila' => $this->getParameter('EstadoPila.activo')
                )
            );

            if ($oTalonario) {
                $arrTalonario = array();
                foreach ($oTalonario as $t) {

                    $arrTalonario[] = $t->getId();

                }
                $this->get('session')->set('idTalonario', $arrTalonario);
            } else {
                $session->getFlashBag()->add('errorCajaRecaudacion', 'No se ha asignado Boleta a ésta Caja');
                return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->getParameter("caja.idModulo"))));
            }
        } else {
            $UbicacionCajero = $this->em->getRepository('App\Entity\Legacy\RelUbicacionCajero')->findOneBy(array(
                "idEstado" => $this->parametro('Estado.activo'),
                "idUsuario" => $idUser
            ));

            $oTalonario = $this->em->getRepository('App\Entity\Legacy\Talonario')->findBy(
                array(
                    'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $UbicacionCajero->getIdUbicacionCaja()->getid() : null,
                    'idEstado' => $this->parametro('Estado.activo'),
                    'idEstadoPila' => $this->getParameter('EstadoPila.activo')
                )
            );
        }

        if ($oTalonario) {
            $arrTalonarioId = array();
            foreach ($oTalonario as $t) {
                $arrAux = array();
                $arrAux['id'] = $t->getId();
                $arrAux['idNombreArray'] = $t->getIdSubEmpresa()->getId() . $t->getid() . $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
                $arrAux['idSubEmpresa'] = $t->getIdSubEmpresa()->getId();
                $arrAux['idTipoDocumento'] = $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
                $arrAux['actual'] = $t->getNumeroActual();
                $arrTalonarioId[] = $arrAux;
            }

            $TalonarioNumeroActual = $this->rCaja()->GetNumeroActualSinAnulacionTalonario($arrTalonarioId,
                $this->getParameter('EstadoDetalleTalonario.anulada'),
                $this->em);

            foreach ($oTalonario as $t) {

                if (array_key_exists($t->getIdSubEmpresa()->getId(), $subEmpresa)) {
                    $subEmpresa[$t->getIdSubEmpresa()->getId()] = $t->getIdSubEmpresa()->getId();
                } else {
                    $subEmpresa[$t->getIdSubEmpresa()->getId()] = $t->getIdSubEmpresa()->getId();
                }
                if ($t->getnumeroActual() >= $t->getnumeroTermino()) {
                    $x = $x + 1;
                } else {
                    if ($t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 1 || $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 3) {
                        $y = $y + 1;
                        /** genero requisitos minimos para la generacion de boletas. al menos 2 Boletas afectas y  exentas (por sus distintas subempresas) */
                        if ($t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 1 || $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 3) {
                            $af = $af + 1;
                        }
                    }

                }
            }
        } else {
            $sintalonario = 0;
        }

        if ($x > 0) {
            $sintalonario = 0;
            if ($y >= 1) {
                $sintalonarioAE = 1;
            } else {
                $sintalonario = 0;
                $sintalonarioAE = 0;
            }
        } else {
            $sintalonario = 1;
            if ($y >= 1) {
                if ($af >= 1) {
                    $sintalonarioAE = 1;
                } else {
                    $sintalonario = 0;
                    $sintalonarioAE = 0;
                }
            } else {
                $sintalonario = 0;
                $sintalonarioAE = 0;
            }
        }

        ///////////// DATOS DE RESERVA ////////////

        if ($reserva == 1) {

            //////// valida fecha de registro ////////
            $oHorario = $this->em->getRepository("App\Entity\Legacy\HorarioConsulta")->find($idHorarioConsulta);
            $oHorario = $oHorario->getFechaInicioHorario();
            $FechaHorario = $oHorario->format("Y-m-d");

            if (strtotime($FechaHorario) >= strtotime($Fecha)) {
                $fechaPago = 1;
                //si es 1 puede pagar HOY y MAÑANA desde Agenda
            } else {
                $fechaPago = 0;
                //si es 0 NO puede pagar HOY desde Agenda
            }


            if (!$oCaja) {
                $oUbicacionCajero = $this->em->getRepository('App\Entity\Legacy\RelUbicacionCajero')->findOneBy(array(
                    "idUsuario" => $idUser,
                    "idEstado" => $this->parametro('Estado.activo')
                ));
                if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()) {
                    $noCajero = 1;
                } else {
                    $noCajero = 0;
                }

                $oCaja = null;
            } else {
                $noCajero = 0;
            }

            //////// Valida SubEmpresa para Talonarios ////////
            if ($noCajero != 1) {
                if ($this->rCaja()->SubEmpresaDesdeCaja($arrTalonario, $arrPrestaciones)) {
                    $subEmpresaTalonarioPrestacion = 1;
                }
            }
        }

        $parametroPagoTodosLosDias = $this->rParametro()->obtenerParametro('SOLO_PAGOS_DEL_DIA')['valor'];
        $getPrestacionesCaja = $this->em->getRepository("App\Entity\Legacy\Parametro")
            ->obtenerParametro('BUSQUEDA_PRESTACION_CAJA');
        return array(
            'pagoTodosLosDias' => $parametroPagoTodosLosDias,
            'sincerrar' => $sincerrar,
            'sintalonario' => $sintalonario,
            'sintalonarioAE' => $sintalonarioAE,
            'open' => $open,
            'close' => $close,
            'subEmpresa' => $subEmpresa,
            'oTalonario' => $oTalonario,
            'TalonarioNumeroActual' => $TalonarioNumeroActual,
            'caja' => $oCaja,
            //////////////RESERVA/////////////////
            'noCajero' => $noCajero,
            'fechaPago' => $fechaPago,
            'subEmpresaTalonarioPrestacion' => $subEmpresaTalonarioPrestacion,
            'getPrestacionesCaja' => $getPrestacionesCaja
        );
    }

    protected function Forms($arrayParams)
    {
        $oEmpresa = isset($arrayParams['oEmpresa']) ? $arrayParams['oEmpresa'] : null;
        $arrayFormasPago = isset($arrayParams['arrayFormasPago']) ? $arrayParams['arrayFormasPago'] : null;
        $arrayOtrosFormasPago = isset($arrayParams['arrayOtrosFormasPago']) ? $arrayParams['arrayOtrosFormasPago'] : null;
        $idCantidad = isset($arrayParams['idCantidad']) ? $arrayParams['idCantidad'] : null;
        $SucursalUsuario = isset($arrayParams['SucursalUsuario']) ? $arrayParams['SucursalUsuario'] : null;
        $domiclio = isset($arrayParams['domiclio']) ? $arrayParams['domiclio'] : null;
        $habilitarPaisExtranjero = isset($arrayParams['habilitarPaisExtranjero']) ? $arrayParams['habilitarPaisExtranjero'] : null;

        $em = $this->getDoctrine()->getManager();
        $idTipoIdentificacionDefault = $em->getReference('App\Entity\Legacy\Empresa', $oEmpresa)->getIdTipoIdentificacionDefault();
        $form = $this->createForm(BusquedaAvanzadaDirectorioPacienteType::class, null);

        $mediosPagoform = $this->createForm(MediosPagoType::class, null,
            array(
                'validaform' => null,
                'idFrom' => $arrayFormasPago,
                'idFromOtros' => $arrayOtrosFormasPago,
                'idCantidad' => $idCantidad,
                'clone' => false,
                'nuevo' => true,
                'iEmpresa' => $oEmpresa,
                'sucursal' => $SucursalUsuario,
                'estado_activado' => $this->parametro('Estado.activo')
            )
        );

        $pagoform = $this->createForm(PagoType::class, $domiclio,
            array(
                'validaform' => null,
                'iEmpresa' => $oEmpresa,
                'estado_activado' => $this->parametro('Estado.activo'),
                'database_default' => $this->obtenerEntityManagerDefault(),
                'habilitarPaisExtranjero' => $habilitarPaisExtranjero
            )
        );

        $diferenciaform = $this->createForm(DiferenciaType::class, $domiclio,
            array(
                'iEmpresa' => $oEmpresa,
                'estado_activado' => $this->parametro('Estado.activo'),
                'database_default' => $this->obtenerEntityManagerDefault()
            )
        );

        $tipoIdentificacionExtranjeroForm = $this->createForm(IdentificacionType::class, null,
            array(
                'idTipoIdentificacionDefault' =>  $idTipoIdentificacionDefault
            )
        );

        $prestacionform = $this->createForm(PrestacionType::class, $domiclio,
            array(
                'validaform' => null,
                'iEmpresa' => $oEmpresa,
                'estado_activado' => $this->parametro('Estado.activo'),
                'sucursal' => $SucursalUsuario,
                'database_default' => $this->obtenerEntityManagerDefault()
            )
        );

        return array(
            'tipoIdentificacionExtranjeroForm' => $tipoIdentificacionExtranjeroForm,
            'form1' => $form->createView(),
            'MediosPago' => $mediosPagoform->createView(),
            'Pago' => $pagoform->createView(),
            'Diferencia' => $diferenciaform->createView(),
            'Prestacion' => $prestacionform->createView()
        );
    }

    protected function GetIdPnaturalProfesional($oR)
    {
        $Pnatural = $this->em->getRepository("App\Entity\Legacy\Pnatural")->findOneBy(array('idPersona' => $oR));
        return $Pnatural->getId();
    }

    protected function clearSessionsVar()
    {
        foreach ($this->arrSessionVarName as $x) {
            $this->killSession($x);
        }
    }

    protected function AnularDiferenciasAyer()
    {

        $em = $this->getDoctrine()->getManager();
        $oUser = $this->getUser();
        $oFecha = new \DateTime("now");

        $oDiferencias = $this->rDiferencia()->anularDiferenciasAyer($oFecha);

        if (count($oDiferencias) > 0) {
            foreach ($oDiferencias as $d) {
                $oDiferencia = $em->getRepository('App\Entity\Legacy\Diferencia')->find($d);
                $oDiferencia->setFechaAnulacion($oFecha);
                $oDiferencia->setIdUsuarioAnulacion($oUser);
                $oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciacajeroCancelaSolicitud'));
                $em->persist($oDiferencia);
            }

            $em->flush();

        }

    }

}
