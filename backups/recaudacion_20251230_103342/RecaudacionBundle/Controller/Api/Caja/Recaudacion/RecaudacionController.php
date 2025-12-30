<?php

namespace Rebsol\RecaudacionBundle\Controller\Api\Caja\Recaudacion;

use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\BusquedaAvanzadaDirectorioPacienteType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\OtrosMediosPagoType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\PagoType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\MediosPagoType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\PrestacionType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaChipType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaKccType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\ResultadoBusqueda\DirectorioPacienteBusquedaAvanzadaType;
use Rebsol\HermesBundle\Controller\DefaultController;
use Rebsol\HermesBundle\Entity\PersonaDomicilio;
use Rebsol\HermesBundle\Form\Type\Caja\Recaudacion\Pago\DiferenciaType;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Process\Process;


/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 */
class RecaudacionController extends DefaultController
{

    var $arrSessionVarName;
    var $em;

    public function __construct()
    {

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
            'idSubEmpresaItem',
            'esTratamiento');
    }

    /**
     * @return render
     * Descripción: recaudacionIndex() Genera Vista de Recaudacion para exponer Pago y Apertura y Cierre de Caja
     */

    public function recaudacionIndexAction()
    {

        /////// Variables Obj  - CORE/////////////////////

        $this->em = $this->getDoctrine()->getManager();
        $domiclio = new PersonaDomicilio();
        $fecha = new \DateTime();
        $fecha = $fecha->format("Y-m-d");
        $idUser = $this->getUser();
        $oEmpresa = $this->obtenerEmpresaLogin();
        /////// Variables Int /////////////////////
        $idCantidad = 0;
        $reserva = 0;
        $tipoAtencion = 1;
        /////// Variables Null ////////////////////
        $sinReserva = null;
        $extranjero = null;
        /////// Variables Array ///////////////////
        $result = array();
        $arrayFormasPago = array();
        $arrayOtrosFormasPago = array();
        ////// Variables solo para Reservas //////
        // $oReservaAtencion               = null;
        $arrPrestaciones = null;
        /////// Limpia Variables de Session ///////
        $this->setSession('from', 'caja');
        $this->setSession('esTratamiento', 0);
        $process = new Process($this->clearSessionsVar(), $this->anularDiferenciasAyer());
        $process->run();


        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();

        $sucursalUsuario = $this->em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser()->getId());

        $oUbicacionCajero = $this->em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
            "idUsuario" => $idUser,
            "idEstado" => $this->parametro('Estado.activo')
        ));


        if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()) {

            $session->getFlashBag()->add('errorCajaRecaudacion', $this->ErrorImedHermes('errorCajaRecaudacion'));
            return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->container->getParameter("caja.idModulo"))));
        }


        $idFormaspago = $this->rFormaPago()->ObtieneFormaPago();

        if ($idFormaspago) {

            foreach ($idFormaspago as $id) {

                $arrayFormasPago[] = $id['id'];
            }
        }

        /**
         * @todo Seguir aqui importar función protegida
         */
        $listadoMediosPago = $this->rFormaPago()->ListadoFormasDePagoParaMediosPago();
        $listadoOtrosMedios = $this->rFormaPago()->ListadoFormasDePagoParaOtrosMedios();


        if ($listadoOtrosMedios) {
            foreach ($listadoOtrosMedios as $id) {
                $arrayOtrosFormasPago[] = $id['id'];
            }
        }

        //////// Validaciones ////////
        $validacion = $this->validacionComplementariaCaja($idUser->getid(),
            $fecha,
            $session,
            $reserva,
            null,
            $arrPrestaciones);

        //////// Formularios ////////
        $form = $this->forms($arrayFormasPago,
            $arrayOtrosFormasPago,
            $idCantidad,
            $sucursalUsuario->getId(),
            $domiclio);

        $datosCompletos = 0;

        $estadoApi = $this->estado('EstadoApi');
        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        // @apis/Caja/Api1/Resources/views/Caja/Recaudacion/Base.html.twig
        return $this->render('RecaudacionBundle:Recaudacion:Base.html.twig', array(
            /////////////////Formularios Directorio Paciente////////////
            'form' => $form['form1'],
            'form2' => $form['form2'],
            'form3' => $form['form3'],
            'form4' => $form['form4'],
            'form5' => $form['form5'],
            /////////////////Formularios Caja///////////////////////////
            'pago_form' => $form['Pago'],
            'mediospago_form' => $form['MediosPago'],
            'prestacion_form' => $form['Prestacion'],

            //////////////Estados desde 'validacionComplementariaCaja'/
            'sincerrar' => $validacion['sincerrar'],
            'sintalonario' => $validacion['sintalonario'],
            'sintalonarioAE' => $validacion['sintalonarioAE'],
            'abierta' => $validacion['open'],
            'cerrada' => $validacion['close'],
            'pagoTodosLosDias' => $validacion['pagoTodosLosDias'],

            ///////////////Estados desde 'EstadosCaja'//////////////////
            'estadoReapertura' => $this->estado('EstadoReaperturaAbierta'),
            'coreApi' => ($estadoApi === "core") ? 1 : 0,
            'from' => $this->getSession('from'),
            ///////////////Estados Caja ////////////////////////////////
            'idReservaAtencion' => $sinReserva,
            'extranjero' => $extranjero,
            'cantidad' => $idCantidad,
            //////////////Arrays desde 'validacionComplementariaCaja'//
            'talonarios' => $validacion['oTalonario'],
            'TalonarioNumeroActual' => $validacion['TalonarioNumeroActual'],
            'subEmpresa' => $validacion['subEmpresa'],
            'caja' => $validacion['caja'],
            'getPrestacionesCaja' => $validacion['getPrestacionesCaja'],
            ///////////////Arrays Caja//////////////////////////////////
            'listadoMediosPagos' => $listadoMediosPago,
            'listadoOtrosMedios' => $listadoOtrosMedios,
            'resultados' => $result,
            ///////////////Arrays desde Funciones o Repositorios////////
            'errores' => $this->validacionesDocumentosFaltantes($sucursalUsuario->getId(), $this->parametro('Estado.activo')),
            //////////////IDs Caja//////////////////////////////////////
            'sucursal' => $sucursalUsuario->getId(),
            'tipoAtencion' => $tipoAtencion,
            'reserva' => $reserva,
            //////////////IDs desde Funciones o Repositorios////////////
            'banco' => $this->rFormaPago()->ObtieneBancoCaja($this->parametro('Estado.activo'), $oEmpresa),
            'cajero' => $this->rPagoCuenta()->GetCajeroByUser($idUser->getId()),
            'datosCompletos' => $datosCompletos
        ));
    }


    /**
     * @return render
     * Descripción: indexAction() Genera Vista de Recaudacion para exponer Pago y Apertura y Cierre de Caja
     */

    public function indexAction($idReservaAtencion)
    {
        /////// Variables Obj /////////////////////
        $this->em = $this->getDoctrine()->getManager();
        $domiclio = null;
        $fecha = new \DateTime();
        $fecha = $fecha->format("Y-m-d");
        $idUser = $this->getUser()->getId();
        // $oEmpresa                       = $this->obtenerEmpresaLogin();
        /////// Variables Int /////////////////////
        $idCantidad = 0;
        $pagoefectuado = 0;
        $pagoesGarantia = 0;
        $garan = 0;
        $tipoAtencion = 1;
        $reserva = 1;
        /////// Variables Null ////////////////////
        $datosPago = null;
        $dhp = null;
        $oPaciente = null;
        // $datosPacienteVistaG            = null;
        $estadoPago = null;
        /////// Variables Array ///////////////////
        $result = array();
        $arrPrestaciones = array();
        $arrayFormasPago = array();
        $arrayOtrosFormasPago = array();

        $estadoApi = $this->estado('EstadoApi');
        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        /////// Limpia Variables de Session ///////
        $process = new Process($this->clearSessionsVar(), $this->anularDiferenciasAyer());
        $process->run();
        $this->setSession('from', 'agenda');
        $this->setSession('esTratamiento', 0);

        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();

        $oUbicacionCajero = $this->em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
            "idUsuario" => $idUser,
            "idEstado" => $this->parametro('Estado.activo')));
        $oReservaAtencion = $this->em->getRepository("RebsolHermesBundle:ReservaAtencion")->find($idReservaAtencion);

        $sucursalUsuario = $this->em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerIdSucursalPorIdUsuario($idUser);
        $oPaciente = $oReservaAtencion->getIdPaciente();
        $this->setSession('pacienteApi', $oPaciente->getId());
        if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()) {
            echo "<div class='alert alert-warning'>
			<button type='button' class='close' data-dismiss='alert'>
				<i class='icon-remove'></i>
			</button>
			<strong>Información:</strong>
			No se ha asignado una Ubicación como Cajero, debe ponerse en contacto con el Supervisor.
			<br>
		</div>";
            exit;
        }

        //////PAGO GENERADO///////////////////////////////////
        if ($oReservaAtencion->getIdPagoCuenta()) {
            $estadoPago = $oReservaAtencion->getIdPagoCuenta()->getIdEstadoPago()->getId();

            if ($estadoPago == 1) {

                return $this->pagoGenerado($oReservaAtencion, $pagoesGarantia, $fecha);
            } else {
                $pagoefectuado = 1;
            }
            if ($estadoPago == 2) {
                $datosPago = array('fechaPago' => $oReservaAtencion->getIdPagoCuenta()->getFechaPago());
                $oPaciente = $this->em->getRepository("RebsolHermesBundle:Paciente")
                    ->find($oReservaAtencion->getIdPaciente());
                $dhp = $this->rPaciente()->HistoricoPagosIdPacienteGarantia($oPaciente->getId());
                $estadoPago = 1;
            }
        } else {
            $estadoPago = 0;
            $pagoefectuado = 1;
        }

        $arrDueno = $this->duenoDataArray($idReservaAtencion, $garan);

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
        $listadoMediosPago = $this->rFormaPago()->ListadoFormasDePagoParaMediosPago();
        $listadoOtrosMedios = $this->rFormaPago()->ListadoFormasDePagoParaOtrosMedios();

        if ($listadoOtrosMedios) {

            foreach ($listadoOtrosMedios as $id) {

                $arrayOtrosFormasPago[] = $id['id'];
            }
        }

        //////// Validaciones y Avisos de errores en Vista////////
        if ($oReservaAtencion->getidHorarioConsulta()->getIdTipoConsulta()->getEsTratamiento() == 0) {
            $this->setSession('esTratamiento', 1);
        }

        $validacion = $this->validacionComplementariaCaja($idUser,
            $fecha,
            $session,
            $reserva,
            $oReservaAtencion->getidHorarioConsulta()->getid(),
            $arrPrestaciones);

        /**
         * mensaje
         */
        if (intval($validacion['pagoTodosLosDias']) == 0 && $validacion['fechaPago'] == 0) {

            /**
             * @todo revisar funcionamiento
             */
            echo "<div class='alert alert-warning'>
				<button type='button' class='close' data-dismiss='alert'>
					<i class='icon-remove'></i>
				</button>
				<strong>Información:</strong>
				Solo está autorizado a pagar Reservas a partir de <strong>Hoy en adelante.</strong>
				<br>
			</div>";
            exit;

        }

        /**
         * @var $validation
         */
        if ($validacion['sincerrar'] == 0 ||
            $validacion['sintalonario'] == 0 ||
            $validacion['open'] == 0 ||
            $validacion['subEmpresaTalonarioPrestacion'] == 0 ||
            $validacion['close'] == 0 ||
            $validacion['noCajero'] == 1) {
dump(111111111); exit();
            return $this->render('RecaudacionBundle:Recaudacion:indexReserva.html.twig', array(
                /*Estados desde 'validacionComplementariaCaja*/
                'coreApi' => ($estadoApi === "core") ? 1 : 0,//
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
                'FechaPagoAgenda' => $validacion['fechaPago']
            ));

        }


        /**
         * Formularios
         */
        $form = $this->forms($arrayFormasPago, $arrayOtrosFormasPago, $idCantidad, $sucursalUsuario['id'], $domiclio);
        $datosMascotas = $this->mascotaDataArray($idReservaAtencion);
        $datosCompletos = 0;

        if (!empty($datosMascotas) && !empty($arrDueno)) {
            $datosCompletos = 1;
        }

        $arrEnviar = array(
            /** Formularios Caja */
            'form' => $form['BusquedaPaciente'],
            'pago_form' => $form['Pago'],
            'mediospago_form' => $form['MediosPago'],
            'prestacion_form' => $form['Prestacion'],
            /** Estados desde 'validacionComplementariaCaja' */
            'sincerrar' => $validacion['sincerrar'],
            'sintalonario' => $validacion['sintalonario'],
            'sintalonarioAE' => $validacion['sintalonarioAE'],
            'abierta' => $validacion['open'],
            'cerrada' => $validacion['close'],
            'noCajero' => $validacion['noCajero'],
            'pagoTodosLosDias' => $validacion['pagoTodosLosDias'],
            // 'estadoT'                    => $this->estado('EstadoBoletaActiva'),
            'getPrestacionesCaja' => $validacion['getPrestacionesCaja'],
            'subEmpresaTalonarioPrestacion' => $validacion['subEmpresaTalonarioPrestacion'],
            /** Estados desde 'EstadosCaja' */
            'estadoReapertura' => $this->container->getParameter('EstadoReapertura.abierta'),
            'coreApi' => ($estadoApi === "core") ? 1 : 0,
            'from' => $this->getSession('from'),
            /** Estados Caja */
            'idReservaAtencion' => $id,
            'cantidad' => $idCantidad,
            'PagoesGarantia' => $estadoPago,
            'PagoEfectuado' => $pagoefectuado,
            /** Arrays desde 'validacionComplementariaCaja' */
            'talonarios' => $validacion['oTalonario'],
            /** Arrays Caja */
            'listadoMediosPagos' => $listadoMediosPago,
            'listadoOtrosMedios' => $listadoOtrosMedios,
            'resultados' => $result,
            'prestaciones' => $arrPrestaciones,
            'datosPacienteVista' => $arrDueno,
            // 'extranjero'                 => $arrDueno['extranjero'],
            'datosMascotaVista' => $datosMascotas,
            /** Arrays desde Funciones o Repositorios */
            'errores' => $this->validacionesDocumentosFaltantes($sucursalUsuario, $this->parametro('Estado.activo')),
            /** IDs y/o Fecha Caja */
            'sucursal' => $sucursalUsuario['id'],
            'tipoAtencion' => $tipoAtencion,
            'caja' => $validacion['caja'],
            'reserva' => $reserva,
            'IdFinanciador' => $arrDueno['financiador'],
            'FechaPagoAgenda' => $validacion['fechaPago'],
            /** IDs desde Funciones o Repositorios */
            'banco' => $this->rFormaPago()->ObtieneBancoPorCaja(),
            'cajero' => $this->rPagoCuenta()->GetCajeroByUser($idUser),
            /** si tiene garantia pendiente */
            'datosPago' => $datosPago,
            'historico' => $dhp,
            'paciente' => $oPaciente,
            'datosPacienteVistaG' => ($estadoPago == 1) ? $this->ClienteDataGarantiaArray($oReservaAtencion) : null,
            'datosCompletos' => $datosCompletos
        );
        dump(222222222222); exit();
        /**
         * @return render view
         */
        return $this->render('RecaudacionBundle:Api/Caja/Recaudacion:indexReserva.html.twig', $arrEnviar);

    }

    ///Secundary Function (Private)////
    private function pagoGenerado($oReservaAtencion, $pagoesGarantia, $fecha)
    {
        $arrHistorico = array();
        $oPago = $oReservaAtencion->getIdPagoCuenta();
        $oPaciente = $oReservaAtencion->getIdPaciente();
        $this->setSession('pacienteApi', $oPaciente->getId());
        $oDueno = $this->rPnatural()->obtenerPadrePnat($oPaciente->getidPnatural()->getId());
        $datosPagosHistorico = $this->rPagoCuenta()->GetPagosHistoricosApi1($oDueno->getId(), $this->estado('EstadoActivo'));
        foreach ($datosPagosHistorico as $valoresDatos) {
            $oPacienteAux = $this->em->getRepository("RebsolHermesBundle:Paciente")->find($valoresDatos['idPaciente']);
            if ($oPaciente->getIdPnatural()->getId() == $oPacienteAux->getIdPnatural()->getId()) {
                $arrHistorico[] = $valoresDatos;
            }
        }
        $oHorario = $oReservaAtencion->getidHorarioConsulta()->getFechaInicioHorario();
        $fechaHorario = $oHorario->format("Y-m-d");
        $datosPago = array('fechaPago' => $oPago->getFechaPago());
        $esFechaPago = (strtotime($fecha) >= strtotime($fechaHorario)) ? 1 : 0;
        //si es 1 puede pagar HOY desde Agenda, si es 0 NO puede pagar HOY desde Agenda

        if ($oPago->getIdEstadoPago()->getId() == 1 && $esFechaPago == 1) {
            return $this->render('CajaBundle:Api/Caja/Recaudacion/PostPago/Exitoso.html.twig', array(
                'datosPago' => $datosPago,
                'historico' => $arrHistorico,
                'datosPacienteVista' => $this->duenoDataGarantiaArray($oReservaAtencion, $oPaciente),
                'estadoT' => $this->estado('EstadoBoletaActiva'),
                'from' => 'agenda'
            ));
        }

        if ($oPago->getIdEstadoPago()->getId() == 1 && $esFechaPago == 0) {
            return $this->render('CajaBundle:Api/Caja/Recaudacion/Pago/ExitosoInfoAnterior.html.twig', array(
                'datosPago' => $datosPago,
                'historico' => $hp,
                'paciente' => $oPaciente,
                'datosPacienteVistaG' => $this->duenoDataGarantiaArray($oReservaAtencion),
                'estadoT' => $this->estado('EstadoBoletaActiva'),
                'from' => 'agenda'
            ));
        }

    }

    private function mascotaDataArray($idReservaAtencion)
    {

        $arrMascota = $this->rPaciente()->obtieneDatosMascota($idReservaAtencion);

        return array(
            'nombre' => $arrMascota['nombre'],
            'chip' => $arrMascota['chip'],
            'kcc' => $arrMascota['kcc'],
            'estadoReproductivo' => $arrMascota['estadoReproductivo'],
            'fechaNacimiento' => $arrMascota['fechaNacimiento'],
            'sexo' => $arrMascota['sexo'],
            'especie' => $arrMascota['especie'],
            'raza' => $arrMascota['raza'],
            'color' => $arrMascota['color'],
            'rut' => $arrMascota['rut'],
        );

    }

    private function duenoDataArray($idReservaAtencion, $garan)
    {

        $arrDueno = $this->rPaciente()->obtieneDatosDueno($idReservaAtencion);

        $datosPacienteVista = array(
            'id' => $arrDueno['idReserva'],
            'idPersona' => $arrDueno['idPersona'],
            'nombre' => $arrDueno['nombrePnatural'],
            'ApellidoPaterno' => $arrDueno['apellidoPaterno'],
            'ApellidoMaterno' => $arrDueno['apellidoMaterno'],
            'rut' => $arrDueno['rutPersona'],
            'dv' => $arrDueno['digitoVerificador'],
            //DATOS QUE PUEDEN CAMBIAR
            'correoElectronico' => $arrDueno['correoElectronico'],
            'telefonoMovil' => $arrDueno['telefonoMovil'],
            'telefonoFijo' => $arrDueno['telefonoFijo'],
            'telefonoTrabajo' => $arrDueno['telefonoTrabajo'],
            'direccion' => $arrDueno['direccion'],
            'numero' => $arrDueno['numero'],
            'restoDireccion' => $arrDueno['restoDireccion'],
            //FIN DATOS QUE PUEDEN CAMBIAR//
            'financiador' => $arrDueno['idPrevision'],
            'usuario' => $arrDueno['idUsuarioFuncionario'],
            'funcionario' => $arrDueno['idUsuarioFuncionario'],
            'covenio' => $arrDueno['idConvenio'],
            'profesional' => $arrDueno['idUsuarioProfesional'],
            'profesionalExterno' => $arrDueno['idUsuarioExterno'],
            'idComuna' => $arrDueno['idComuna']
        );
        /**
         * @return $datosPacienteVista
         */
        $datosPacienteVista['garantias'] = $this->getNumGarantias($arrDueno['idPersona'], $garan);
        return $datosPacienteVista;
    }

    private function getNumGarantias($idNumGarantias, $garan)
    {
        if ($idNumGarantias) {
            $resultadoGarantias = $this->rPagoCuenta()->GetPagoCuentaByIdPersona($idNumGarantias);
            if ($resultadoGarantias) {
                // foreach ($resultadoGarantias as $g){
                $garan = $garan + 1;
                // }
            }
        }
        return $garan;
    }

    private function duenoDataGarantiaArray($oReservaAtencion, $oPaciente)

    {
        $oDueno = $this->em->getRepository("RebsolHermesBundle:Pnatural")->obtenerPadrePnat($oPaciente->getIdPnatural()->getId());
        $oPnatural = $oPaciente->getIdPnatural();

        return array(
            'id' => $oReservaAtencion->getId(),
            'idPagoCuenta' => $oReservaAtencion->getIdPagoCuenta()->getId(),
            'idPersona' => $oDueno->getIdPersona()->getId(),
            'nombre' => $oDueno->getNombrePnatural(),
            'ApellidoPaterno' => $oDueno->getApellidoPaterno(),
            'ApellidoMaterno' => $oDueno->getApellidoMaterno(),
            'rut' => $oDueno->getIdPersona()->getRutPersona(),
            'dv' => $oDueno->getIdPersona()->getDigitoVerificador(),
            'correoElectronico' => $oDueno->getIdPersona()->getcorreoElectronico(),
            'telefonoMovil' => $oDueno->getIdPersona()->gettelefonoMovil(),
            'telefonoFijo' => $oDueno->getIdPersona()->gettelefonoFijo(),
            'telefonoTrabajo' => $oDueno->getIdPersona()->gettelefonoTrabajo(),
            'fechaR' => $oReservaAtencion->getFechaRecepcion(),
            //////////////////MASCOTA///////////////////
            'nombreMascota' => $oPnatural->getNombrePnatural(),
            'chip' => $oPnatural->getChip(),
            'kcc' => $oPnatural->getKcc(),
            'estadoReproductivo' => $oPnatural->getIdEstadoReproductivo()->getNombre(),
            'fechaNacimiento' => $oPnatural->getFechaNacimiento(),
            'sexo' => $oPnatural->getIdSexo()->getNombreSexo(),

            'especie' => $oPnatural->getIdRaza()->getIdEspecie()->getNombre(),
            'raza' => $oPnatural->getIdRaza()->getNombre(),
            'color' => $oPnatural->getColor(),
            'rutMascota' => $oPnatural->getIdPersona()->getRutPersona());
    }

    /**
     * [validacionesDocumentosFaltantes description]
     * @param int $sucursalUsuario [description]
     * @param int $estado [description]
     */
    private function validacionesDocumentosFaltantes($sucursalUsuario, $estado)
    {

        $failsMesages = array();
        $countPlan = 0;
        $countProf = 0;

        $oOrigen = $this->em->getRepository('RebsolHermesBundle:Origen')->findBy(array(
            "idSucursal" => $sucursalUsuario,
            "idEstado" => $estado));
        $oRolProfesional = $this->em->getRepository('RebsolHermesBundle:RolProfesional')->findBy(array(
            "idRol" => $this->container->getParameter('rol_medico'),
            "idEstado" => $estado));
        $oPrevision = $this->em->getRepository('RebsolHermesBundle:Prevision')->findBy(array(
            "idEmpresa" => $this->container->getParameter('empresa_activa_id'),
            "idEstado" => $estado));
        $oTipoPrevision = $this->em->getRepository('RebsolHermesBundle:TipoPrevision')->findBy(array(
            "idEmpresa" => $this->container->getParameter('empresa_activa_id'),
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

            $oRelSucursalPrevision = $this->em->getRepository('RebsolHermesBundle:RelSucursalPrevision')->findOneBy(array(
                "idSucursal" => $sucursalUsuario,
                "idPrevision" => $pr->getid(),
                "idEstado" => $estado
            ));

            if ($oRelSucursalPrevision) {
                $auxRelSucPre[] = $oRelSucursalPrevision->getid();
            }
        }


        foreach ($auxRelSucPre as $c) {
            $oPrPlan = $this->em->getRepository('RebsolHermesBundle:PrPlan')->findBy(array(
                "idRelSucursalPrevision" => $c,
                "idEstado" => $estado
            ));
            if ($oPrPlan) {
                $countPlan = $countPlan + 1;
            }
        }
        // DESCOMENTAR Y VERIFICAR URGENTEMENTE
        // if ($countPlan == 0)
        // {
        //     $failPlanes = "Planes";
        //     $failsMesages[] = $failPlanes;
        // }
        return $failsMesages;
    }

    /**
     * [validacionComplementariaCaja description]
     * @param integer $idUser
     * @param date $fecha
     * @param session $session
     * @param string $reserva
     * @param integer $idHorarioConsulta
     * @param array $arrPrestaciones
     */
    private function validacionComplementariaCaja($idUser, $fecha, $session, $reserva, $idHorarioConsulta, $arrPrestaciones)
    {

        $valorUno = 0;
        $valorDos = 0;
        $valorTres = 0;
        $auxReaperturaCount = 0;
        $noCajero = 0;
        $subEmpresaTalonarioPrestacion = 0;
        $fechaPago = null;
        $talonarioNumeroActual = null;
        $arrTalonario[] = array();
        $subEmpresa[] = array();
        // $arrTalonarioNumeroActual      = array();
        $oCajaFindByUser = $this->em->getRepository('RebsolHermesBundle:Caja')->findBy(array("idUsuario" => $idUser));

        if ($oCajaFindByUser) {
            foreach ($oCajaFindByUser as $c) {
                $estadoTemp = (!is_null($c->getIdEstadoReapertura()) ) ? $c->getIdEstadoReapertura()->getId() : null;
                if ($estadoTemp && $estadoTemp == $this->container->getParameter('EstadoReapertura.abierta')) {


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
            if (strtotime($fecha) > strtotime($fechaReapertura)) {
                $sincerrar = 0;
            } else {
                $sincerrar = 1;
            }
            $open = 1;
            $close = 1;
            $oCaja = $oCajaTemp;


            $oTalonario = $this->em->getRepository('RebsolHermesBundle:Talonario')->findOneBy(array(
                "idUbicacionCaja" => $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid(),
                "idEstado" => $this->parametro('Estado.activo'),
                "idEstadoPila" => $$this->container->getParameter('EstadoPila.activo')
            ));

        } else {

            $oCaja = $this->rPagoCuenta()->GetCajaByUser($idUser, $fecha);


            if ($oCaja) {
                $this->get('session')->set('VarCajaHoy', $oCaja->getId());

                $sincerrar = 1;
                $fechaCierre = $oCaja->getfechaCierre();
                $open = 1;

                if ($fechaCierre) {
                    $fechaCierre = $fechaCierre->format("Y-m-d");
                    if (strtotime($fechaCierre) == strtotime($fecha)) {
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
                    if (strtotime($fecha) > strtotime($fechaApertura)) {
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
        $folioGlobal = $this->em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');
        if ($oCaja) {

            /*$oTalonario = $this->em->getRepository('RebsolHermesBundle:Talonario')->findBy(array(
                "idUbicacionCaja"   => $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid(),
                "idEstado"          => $this->parametro('Estado.activo'),
                "idEstadoPila"      => $this->container->getParameter('EstadoPila.activo')
                ));*/


            $oTalonario = $this->em->getRepository('RebsolHermesBundle:Talonario')->findBy(
                array(
                    'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid() : null,
                    'idEstado' => $this->parametro('Estado.activo'),
                    'idEstadoPila' => $this->container->getParameter('EstadoPila.activo')
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
                return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->container->getParameter("caja.idModulo"))));
            }
        } else {
            $ubicacionCajero = $this->em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
                "idEstado" => $this->parametro('Estado.activo'),
                "idUsuario" => $idUser
            ));


            $oTalonario = $this->em->getRepository('RebsolHermesBundle:Talonario')->findBy(
                array(
                    'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $ubicacionCajero->getIdUbicacionCaja()->getid() : null,
                    'idEstado' => $this->parametro('Estado.activo'),
                    'idEstadoPila' => $this->container->getParameter('EstadoPila.activo')
                )
            );

            /*$oTalonario = $this->em->getRepository('RebsolHermesBundle:Talonario')->findBy(array(
                "idUbicacionCaja"   => $ubicacionCajero->getIdUbicacionCaja()->getid(),
                "idEstado"          => $this->parametro('Estado.activo'),
                "idEstadoPila"      => $this->container->getParameter('EstadoPila.activo')
                ));*/
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

            $talonarioNumeroActual = $this->rCaja()->GetNumeroActualSinAnulacionTalonario($arrTalonarioId,
                $this->container->getParameter('EstadoDetalleTalonario.anulada'),
                $this->em);

            foreach ($oTalonario as $t) {

                if (array_key_exists($t->getIdSubEmpresa()->getId(), $subEmpresa)) {
                    $subEmpresa[$t->getIdSubEmpresa()->getId()] = $t->getIdSubEmpresa()->getId();
                } else {
                    $subEmpresa[$t->getIdSubEmpresa()->getId()] = $t->getIdSubEmpresa()->getId();
                }
                if ($t->getnumeroActual() >= $t->getnumeroTermino()) {
                    $valorUno = $valorUno + 1;
                } else {
                    if ($t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 1 || $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 3) {
                        $valorDos = $valorDos + 1;
                        /** genero requisitos minimos para la generacion de boletas. al menos 2 Boletas afectas y  exentas (por sus distintas subempresas) */
                        if ($t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 1 || $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 3) {
                            $valorTres = $valorTres + 1;
                        }
                    }

                }
            }
        } else {
            $sintalonario = 0;
        }

        if ($valorUno > 0) {
            $sintalonario = 0;
            if ($valorDos >= 1) {
                $sintalonarioAE = 1;
            } else {
                $sintalonario = 0;
                $sintalonarioAE = 0;
            }
        } else {
            $sintalonario = 1;
            if ($valorDos >= 1) {
                if ($valorTres >= 1) {
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
            $oHorario = $this->em->getRepository("RebsolHermesBundle:HorarioConsulta")->find($idHorarioConsulta);
            $oHorario = $oHorario->getFechaInicioHorario();
            $fechaHorario = $oHorario->format("Y-m-d");

            if (strtotime($fechaHorario) >= strtotime($fecha)) {
                $fechaPago = 1;
                //si es 1 puede pagar HOY y MAÑANA desde Agenda
            } else {
                $fechaPago = 0;
                //si es 0 NO puede pagar HOY desde Agenda
            }

            if (!$oCaja) {
                $oUbicacionCajero = $this->em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
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
        $getPrestacionesCaja = $this->em->getRepository("RebsolHermesBundle:Parametro")
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
            'TalonarioNumeroActual' => $talonarioNumeroActual,
            'caja' => $oCaja,
            //////////////RESERVA/////////////////
            'noCajero' => $noCajero,
            'fechaPago' => $fechaPago,
            'subEmpresaTalonarioPrestacion' => $subEmpresaTalonarioPrestacion,
            'getPrestacionesCaja' => $getPrestacionesCaja
        );
    }


    private function forms($arrayFormasPago, $arrayOtrosFormasPago, $idCantidad, $sucursalUsuario, $domiclio)
    {

        $form1 = $this->createForm(DirectorioPacienteType::class, null); //type para la seccion Buscar rut
        $form2 = $this->createForm(DirectorioPacienteMascotaType::class, null); // type buscar por
        $form3 = $this->createForm(DirectorioPacienteMascotaKccType::class, null); // type para busqueda por kcc
        $form4 = $this->createForm(DirectorioPacienteMascotaChipType::class, null); // type par busqueda por chip
        $form5 = $this->createForm(DirectorioPacienteBusquedaAvanzadaType::class, null, array(
            'oEmpresa' => $this->getSession('idEmpresaLogin'),
            'estado_activado' => $this->estado('EstadoActivo')->getId()
        ));
        $form = $this->createForm(BusquedaAvanzadaDirectorioPacienteType::class, null);
        $mediosPagoform = $this->createForm(MediosPagoType::class, null, array(
            'validaform' => null,
            'idFrom' => $arrayFormasPago,
            'idFromOtros' => $arrayOtrosFormasPago,
            'idCantidad' => $idCantidad,
            'clone' => false,
            'nuevo' => true,
            'iEmpresa' => $this->getSession('idEmpresaLogin'),
            'sucursal' => $sucursalUsuario,
            'estado_activado' => $this->parametro('Estado.activo'),
        ));
        $pagoform = $this->createForm(PagoType::class, $domiclio, array(
            'validaform' => null,
            'iEmpresa' => $this->getSession('idEmpresaLogin'),
            'estado_activado' => $this->parametro('Estado.activo'),
            'database_default' => $this->obtenerEntityManagerDefault()
        ));
        $prestacionform = $this->createForm(PrestacionType::class, $domiclio, array(
            'validaform' => null,
            'iEmpresa' => $this->getSession('idEmpresaLogin'),
            'estado_activado' => $this->parametro('Estado.activo'),
            'sucursal' => $sucursalUsuario,
            'database_default' => $this->obtenerEntityManagerDefault()
        ));
        return array(

            ////////////// Directorio Paciente /////////////////////
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'form3' => $form3->createView(),
            'form4' => $form4->createView(),
            'form5' => $form5->createView(),
            ////////////// Caja ////////////////////////////////////
            'BusquedaPaciente' => $form->createView(),
            'MediosPago' => $mediosPagoform->createView(),
            'Pago' => $pagoform->createView(),
            'Prestacion' => $prestacionform->createView()

        );
    }

    private function clearSessionsVar()
    {
        foreach ($this->arrSessionVarName as $x) {
            $this->killSession($x);
        }
    }
    /**
     * Descripción: Functiones Protegidas para utilizarlas solo en las subClases de Caja.
     */
    /// Public Generales///
    public function obtenerEmpresaLogin()
    {
        $oUsuarioRebsol = $this->getUser();
        $oPersona = $oUsuarioRebsol->getIdPersona();
        $oEmpresa = $oPersona->getIdEmpresa();
        return $oEmpresa;
    }

    /**
     * @param String $srtNombreEntidad .
     * @param Objeto $oEmpresa .
     * @return |RedirectResponse()
     * Descripción: ResetearValorDefault() Resetea a 0 el valor_default de las demás entidades.
     */
    public function buscarValorDefault($oEmpresa, $strNombreEntidad)
    {
        $this->em = $this->getDoctrine()->getManager();
        $entities = $this->em->getRepository('RebsolHermesBundle:' . $strNombreEntidad . '')->findBy(array("idEmpresa" => $oEmpresa->getId()));
        foreach ($entities as $entity) {
            if ($entity->getValorDefault() == 1) {
                return $entity;
            }
        }
        return false;
    }

    /**
     * @param Request $request .
     * @param String $router Módulo desde el que se está haciendo la petición.
     * @return |RedirectResponse()
     * Descripción: validadPeticionPost() Valida que la petición se esté realizando por POST.
     */
    public function validadPeticionPost(Request $request, $router)
    {
        if ($request->getMethod() !== 'POST') {
            $urlGenerate = 'Caja_' . $router . '';
            $urlResponse = $this->generateUrl($urlGenerate);
            $redirectResponse = new RedirectResponse($urlResponse);
            $redirectResponse->send();
        }
    }

    /**
     * @param Request $request .
     * @param String $router Módulo desde el que se está haciendo la petición.
     * @return |RedirectResponse()
     * Descripción: validadPeticionAjax() Valida que la petición se esté realizando por AJAX.
     */
    public function validadPeticionAjax(Request $request, $router)
    {
        if (!$request->isXmlHttpRequest()) {
            $urlGenerate = 'Caja_' . $router . '';
            $urlResponse = $this->generateUrl($urlGenerate);
            $redirectResponse = new RedirectResponse($urlResponse);
            $redirectResponse->send();
        }
    }

    /// Protecteds Repositorio ///

    protected function rFormaPago()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:FormaPago");
    }

    protected function rPaciente()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:Paciente");
    }

    protected function rPnatural()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:Pnatural");
    }

    protected function rPagoCuenta()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:PagoCuenta");
    }

    protected function rCaja()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:Caja");
    }

    protected function rDetalleCaja()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:DetalleCaja");
    }

    protected function rDocumentoPago()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:DocumentoPago");
    }

    protected function rDiferencia()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:Diferencia");
    }

    protected function rParametro()
    {
        return $this->getDoctrine()->getRepository("RebsolHermesBundle:Parametro");
    }

    /// Protecteds AJAX ///
    protected function ajax($nombreVariable)
    {
        return $this->container->get('request_stack')->getCurrentRequest()->query->get($nombreVariable);
    }

    protected function getSession($variableSesion)
    {
        return $this->get('session')->get($variableSesion);
    }

    protected function setSession($nombreVariable, $variableSesion)
    {
        return $this->get('session')->set($nombreVariable, $variableSesion);
    }

    protected function killSession($eliminarSesion)
    {
        return $this->get('session')->remove($eliminarSesion);
    }

    /// Utilización de Servicios ///
    protected function getRutUser($obtenerIdRutUsuario)
    {
        return $this->get('libreria_funciones')->getRutUser($obtenerIdRutUsuario);
    }

    protected function subEmpresaPorPrestacionTalonario($datoSubEmpresa1, $datoSubEmpresa2)
    {
        return $this->get('Caja_Valida')->SubEmpresa($datoSubEmpresa1, $datoSubEmpresa2);
    }

    // protected function obtenerHonarioGarantia($honorario1, $honorario2, $honorario3, $honorario4, $honorario5, $honorario7, $honorario8, $honorario9, $honorario10, $honorario11){
    // 	return $this->get('Caja_Valida')->obtenerHonarioGarantia($honorario1, $honorario2, $honorario3, $honorario4, $honorario5, $honorario7, $honorario8, $honorario9, $honorario10, $honorario11);
    // }

    protected function obtenerHonarioGarantia($obtenerParametrosHonorario)
    {

        $arrayParametro[] = $obtenerParametrosHonorario['honorario1'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario2'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario3'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario4'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario5'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario6'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario7'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario8'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario9'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario10'];
        $arrayParametro[] = $obtenerParametrosHonorario['honorario11'];

        return $this->get('Caja_Valida')->obtenerHonarioGarantia($arrayParametro);

    }

    protected function getRutPnatural($idRutPnatural)
    {
        $parametroRut = $this->get('libreria_funciones')->getRutPnatural($idRutPnatural);
        return (strlen($parametroRut) == 10) ? '00' . $parametroRut :
            (strlen($parametroRut) == 9) ? '000' . $parametroRut :
                (strlen($parametroRut) == 8) ? '0000' . $parametroRut :
                    (strlen($parametroRut) == 7) ? '00000' . $parametroRut :
                        (strlen($parametroRut) == 6) ? '000000' . $parametroRut :
                            null;
    }

    protected function getUserFromPnatural($idPnatural)
    {
        $parametroRut = $this->get('libreria_funciones')->getUserFromPnatural($idPnatural);
        return ($parametroRut) ? (strlen($this->getRutUser($parametroRut)) == 9) ? '000' . $this->getRutUser($parametroRut) : (strlen($this->getRutUser($parametroRut)) == 10) ? '00' . $this->getRutUser($parametroRut) : null : '0000000000-0';
    }

    protected function getCompleteNameFromIdPnatural($nombreDesdePnatural)
    {
        return ($nombreDesdePnatural) ? $this->get('libreria_funciones')->getCompleteNameFromIdPnatural($nombreDesdePnatural) : null;
    }

    protected function estado($var)
    {
        $this->em = $this->getDoctrine()->getManager();
        switch ($var) {
            case "EstadoPilaActiva":
                return $this->em->getRepository('RebsolHermesBundle:EstadoPila')->find($this->container->getParameter('estado_pila_activo'));
                break;
            case "EstadoReaperturaCerrada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoReapertura')->find($this->container->getParameter('estado_reapertura_cerrada'));
                break;
            case "EstadoReaperturaAbierta":
                return $this->em->getRepository('RebsolHermesBundle:EstadoReapertura')->find($this->container->getParameter('estado_reapertura_abierta'));
                break;
            case "EstadoActivo":
                return $this->em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
                break;
            case "EstadoInc":
                return $this->em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_inactivo'));
                break;
            case "EstadoPagoActiva":
                return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('estado_pagado'));
                break;
            case " EstadoPagoAnulada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('estado_anulado'));
                break;
            case "EstadoPagoGarantia":
                return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('estado_garantia'));
                break;
            case "EstadoPagoRegularizada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('estado_regularizada'));
                break;
            case "EstadoCuentaCerradaPagada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('estado_cerrada_pagada'));
                break;
            case "EstadoBoletaActiva":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find($this->container->getParameter('estado_detalle_talonario_emitidas'));
                break;
            case "EstadoBoletaAnulada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find($this->container->getParameter('estado_detalle_talonario_anulada'));
                break;
            case "EstadoAccionClinicaSolicitado":
                return $this->em->getRepository('RebsolHermesBundle:EstadoAccionClinica')->find($this->container->getParameter('estado_solicitado'));
                break;
            case "EstadoApi":
                return $this->obtenerApiModulo($this->container->getParameter("modulo_caja"));
                break;
            case "DiferenciacajeroPideAutorizacion":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')
                    ->find($this->container->getParameter('EstadoDiferencia.cajeroPideAutorizacion'));
                break;
            case "Diferenciaautorizada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')
                    ->find($this->container->getParameter('EstadoDiferencia.autorizada'));
                break;
            case "DiferenciadescuentoNoRequiereAutorizacion":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')
                    ->find($this->container->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'));
                break;
            case "DiferenciacajeroCancelaSolicitud":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')
                    ->find($this->container->getParameter('EstadoDiferencia.cajeroCancelaSolicitud'));
                break;
            case "Diferenciarechazada":
                return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')
                    ->find($this->container->getParameter('EstadoDiferencia.rechazada'));
                break;
            default:
                return null;
        }
    }

    protected function Tipos($oEmpresa)
    {
        $oTipoDocumentoAfecto = $this->em->getRepository('RebsolHermesBundle:TipoDocumento')->find($this->container->getParameter('tipo_documento_afecto'));
        $oTipoDOcumentoExento = $this->em->getRepository('RebsolHermesBundle:TipoDocumento')->find($this->container->getParameter('tipo_documento_Exento'));
        return array(
            "TipoAtencionFcAmbulatoria" => $this->em->getRepository('RebsolHermesBundle:TipoAtencionFc')->find($this->container->getParameter('ambulatoria')),
            "BoletaAfecta" => $this->em->getRepository('RebsolHermesBundle:RelEmpresaTipoDocumento')->findOneBy(array("idTipoDocumento" => $oTipoDocumentoAfecto->getid(), "idEmpresa" => $oEmpresa->getid())),
            "BoletaExenta" => $this->em->getRepository('RebsolHermesBundle:RelEmpresaTipoDocumento')->findOneBy(array("idTipoDocumento" => $oTipoDOcumentoExento->getid(), "idEmpresa" => $oEmpresa->getid())),
            "TipoLogRecepcion" => $this->em->getRepository('RebsolHermesBundle:ReservaAtencionTipoLog')->find($this->container->getParameter('tipo_log_recepcion')),
            "TipoLogPagoReserva" => $this->em->getRepository('RebsolHermesBundle:ReservaAtencionTipoLog')->find($this->container->getParameter('tipo_log_pago_Reserva'))
        );
    }

    /// functiones varias ///
    protected function cuentaDigito($numero)
    {
        return strlen($numero);
    }

    protected function errorImedHermes($var)
    {
        switch ($var) {
            case "VtaBonInterfaz":
                return "Error en Generar Venta Bono Interfaz";
                break;
            case "ObtBonInterfaz":
                return "Error en Obtener Bono por Interfaz";
                break;
            case "noSubEmpresa":
                return 'Prestaciones no Corresponden a Sub-E,mpresa de Cajero';
                break;
            case "sinPreciosPrestacion":
                return 'Prestación no cuenta con sus Precios Correctamente';
                break;
            case "sinParameters":
                return 'No fue posible generar Parametros, reintente';
                break;
            case "SetGlobalsVar":
                return 'No se han encontrado datos básicos para establecer comunicación con I-MED, debe ponerse en contacto con el Administrador';
                break;
            case " noSendPostLogin":
                return 'No fue posible establecer Comunicación con I-MED. Error: Envio Post = False';
                break;
            case "errorCajaRecaudacion":
                return 'El usuario no esta relacionado como Cajero';
                break;
            case "errorEjecucion":
                return 'No ha sido Posible inicializar Caja, Error Interno';
                break;

            default:
                return null;
        }
    }

    private function anularDiferenciasAyer()
    {

        $this->em = $this->getDoctrine()->getManager();
        $oUser = $this->getUser();
        $oFecha = new \DateTime("now");
        $oDiferencias = $this->rDiferencia()->anularDiferenciasAyer($oFecha);
        if (count($oDiferencias) > 0) {
            foreach ($oDiferencias as $d) {
                $oDiferencia = $this->em->getRepository('RebsolHermesBundle:Diferencia')->find($d);
                $oDiferencia->setFechaAnulacion($oFecha);
                $oDiferencia->setIdUsuarioAnulacion($oUser);
                $oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciacajeroCancelaSolicitud'));
                $this->em->persist($oDiferencia);
            }
            $this->em->flush();
        }

    }

///Protecteds Estado
    protected function parametro($var)
    {
        switch ($var) {
            case "Estado.activo":
                return $this->container->getParameter('Estado.activo');
                break;
            case "Estado.inactivo":
                return $this->container->getParameter('Estado.inactivo');
                break;
            case "EstadoUsuarios.activo":
                return $this->container->getParameter('EstadoUsuarios.activo');
                break;
            case "EstadoUsuarios.inactivo":
                return $this->container->getParameter('EstadoUsuarios.inactivo');
                break;
            case "EstadoEspecialidadMedica.activo":
                return $this->container->getParameter('EstadoEspecialidadMedica.activo');
                break;
            case "EstadoEspecialidadMedica.inactivo":
                return $this->container->getParameter('EstadoEspecialidadMedica.inactivo');
                break;
            case "EstadoRelUsuarioServicio.Activo":
                return $this->container->getParameter('EstadoRelUsuarioServicio.Activo');
                break;
            case "EstadoRelUsuarioServicio.Inactivo":
                return $this->container->getParameter('EstadoRelUsuarioServicio.Inactivo');
                break;
            case "EstadoRelUsuarioServicio.Bloqueado":
                return $this->container->getParameter('EstadoRelUsuarioServicio.Bloqueado');
                break;
            case "EstadoPago.garantia":
                return $this->container->getParameter('EstadoPago.garantia');
                break;
            case "EstadoPago.pagadoNormal":
                return $this->container->getParameter('EstadoPago.pagadoNormal');
                break;
            case "EstadoPila.inactivo":
                return $this->container->getParameter('EstadoPila.inactivo');
                break;

            case "FormaPagoTipo.Efectivo":
                return $this->container->getParameter('FormaPagoTipo.Efectivo');
                break;
            case "FormaPagoTipo.Gratuidad":
                return $this->container->getParameter('FormaPagoTipo.Gratuidad');
                break;
            case "FormaPagoTipo.BonoElectronico":
                return $this->container->getParameter('FormaPagoTipo.BonoElectronico');
                break;
            case "FormaPagoTipo.TarjetaCredito":
                return $this->container->getParameter('FormaPagoTipo.TarjetaCredito');
                break;
            case "FormaPagoTipo.BonoManual":
                return $this->container->getParameter('FormaPagoTipo.BonoManual');
                break;
            case "FormaPagoTipo.TarjetaDebito":
                return $this->container->getParameter('FormaPagoTipo.TarjetaDebito');
                break;
            case "FormaPagoTipo.ChequeFecha":
                return $this->container->getParameter('FormaPagoTipo.ChequeFecha');
                break;
            case "FormaPagoTipo.ChequeDia":
                return $this->container->getParameter('FormaPagoTipo.ChequeDia');
                break;
            case "FormaPagoTipo.ConvenioLasik":
                return $this->container->getParameter('FormaPagoTipo.ConvenioLasik');
                break;
            case "FormaPagoTipo.ConvenioImed":
                return $this->container->getParameter('FormaPagoTipo.ConvenioImed');
                break;
            case "FormaPagoTipo.SeguroComplementario":
                return $this->container->getParameter('FormaPagoTipo.SeguroComplementario');
                break;
            case "EstadoDetalleTalonario.emitidas":
                return $this->container->getParameter('EstadoDetalleTalonario.emitidas');
                break;
            default:
                return null;
        }
    }

}

