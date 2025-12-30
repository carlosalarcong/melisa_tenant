<?php

namespace Rebsol\RecaudacionBundle\Controller\Api\Unab\PagoCuenta;

use Rebsol\HermesBundle\Controller\DefaultController;

use Symfony\Component\Process\Process;


use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaKccType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\DirectorioPacienteMascotaChipType;
use Rebsol\HermesBundle\Api\DirectorioPaciente\Api1\Form\Type\ResultadoBusqueda\DirectorioPacienteBusquedaAvanzadaType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\PagoType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\MediosPagoType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\OtrosMediosPagoType;
use Rebsol\CajaBundle\Form\Type\Api\Caja\Recaudacion\Pago\BusquedaAvanzadaDirectorioPacienteType;
use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\PrestacionType;
use Rebsol\HermesBundle\Form\Type\Caja\Recaudacion\Pago\DiferenciaType;

use Rebsol\HermesBundle\Entity\PersonaDomicilio;

class PagoCuentaController extends DefaultController {


	var $arrSessionVarName;
	var $em;

	public function __construct(){

		$arrayVariables = array(
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
			'esTratamiento' );

		$this->arrSessionVarName  =  $arrayVariables;
	}

	/**
	 * [obtenerServicioGlobales Párametros Globales de CajaBundle]
	 * @uses \Rebsol\CajaBundle\Services\Api\Unab\ParametrosServices
	 * @return [type] [description]
	 */
	protected function obtenerServicioGlobales() {
		return $this->container->get('recaudacion.obtenerParametrosGlobales');
	}

	protected function obtenerServicioFormularioPagos() {
		return $this->container->get('admision.obtenerFormualrioPago');
	}

	/**
	 * [indexAction Inicia Sub-Módulo Pago Cuenta]
	 * @return [type] [description]
	 */
	public function indexAction() {

		/**
		 * Variables Obj  - CORE
		 */
		$em       = $this->getDoctrine()->getManager();
		$domiclio = new PersonaDomicilio();
		$fecha    = new \DateTime();
		$fecha    = $fecha->format("Y-m-d");
		$idUser   = $this->getUser();
		$oEmpresa = $this->obtenerEmpresaLogin();

		/**
		 * [$idCantidad variables]
		 * @var integer
		 */
		$idCantidad                     = 20;
		$reserva                        = 0;
		$tipoAtencion                   = 1;

		/**
		 * [$sinReserva Variables Null]
		 * @var null
		 */
		$sinReserva                     = null;
		$extranjero                     = null;

		/**
		 * [$result Variables Array]
		 * @var array
		 */
		$result                         = array();
		$arrayFormasPago                = array();
		$arrayOtrosFormasPago           = array();

		/**
		 * [$arrPrestaciones Variables solo para Reservas]
		 * @var null
		 */
		$arrPrestaciones                = null;

		/**
		 * Limpia Variables de Session
		 */
		$this->setSession('from', 'caja');
		$this->setSession('esTratamiento', 0);

		$process                        = new Process($this->clearSessionsVar(), $this->anularDiferenciasAyer());
		$process->run();

		$session          = $this->container->get('request_stack')->getCurrentRequest()->getSession();
		$sucursalUsuario  = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser()->getId());

		$oUbicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
			"idUsuario" => $idUser,
			"idEstado"  =>  $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo')
			));


		if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()){

			$session->getFlashBag()->add('errorCajaRecaudacion', $this->obtenerServicioGlobales()->ErrorImedHermes('errorCajaRecaudacion'));
            return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->container->getParameter("caja.idModulo"))));

		}


		$idFormaspago = $this->obtenerServicioGlobales()->rFormaPago()->ObtieneFormaPago();

		if ($idFormaspago){

			foreach ($idFormaspago as $id){

				$arrayFormasPago[] = $id['id'];
			}
		}

		/**
		 * @todo Seguir aqui importar función protegida
		 */
		$listadoMediosPago  = $this->obtenerServicioGlobales()->rFormaPago()->ListadoFormasDePagoParaMediosPago();
		$listadoOtrosMedios = $this->obtenerServicioGlobales()->rFormaPago()->ListadoFormasDePagoParaOtrosMedios();


		if ($listadoOtrosMedios){
			foreach ($listadoOtrosMedios as $id){
				$arrayOtrosFormasPago[] = $id['id'];
			}
		}

		/**
		* [$validacion Validaciones]
		* @var [type]
		*/

		$validacion     = $this->validacionComplementariaCaja($idUser->getid(), $fecha, $session, $reserva, null, $arrPrestaciones);

		/**
		* [$form Formularios]
		* @var [type]
		*/

		$form           = $this->formularioPago(   $arrayFormasPago, $arrayOtrosFormasPago, $idCantidad, $sucursalUsuario->getId(), $domiclio );


		$datosCompletos = 0;


		/*
		 * Redirección a  _Default ( SERVET ) Sub - Módulo Pago Cuenta
		 */
		if($this->obtenerServicioGlobales()->obtenerEstado('EstadoApi') !== 'Api1'){
			return $this->redirect($this->generateUrl('Caja_PagoCuenta_Default_Servet_Inicio'));
		}

		$estadoApi = $this->obtenerServicioGlobales()->obtenerEstado('EstadoApi');
		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		$parametrosArray = [
		/** Formularios Directorio Paciente */
		'form'                  => $form['form1'],
		'form2'                 => $form['form2'],
		'form3'                 => $form['form3'],
		'form4'                 => $form['form4'],
		'form5'                 => $form['form5'],
		/** Formularios Caja */
		'pago_form'             => $form['Pago'],
		'mediospago_form'       => $form['MediosPago'],
		'prestacion_form'       => $form['Prestacion'],

		/** Estados desde 'validacionComplementariaCaja' */
		'sincerrar'             => $validacion['sincerrar'],
		'sintalonario'          => $validacion['sintalonario'],
		'sintalonarioAE'        => $validacion['sintalonarioAE'],
		'abierta'               => $validacion['open'],
		'cerrada'               => $validacion['close'],
		'pagoTodosLosDias'      => $validacion['pagoTodosLosDias'],

		/** Estados desde 'EstadosCaja' */
		'estadoReapertura'      => $this->obtenerServicioGlobales()->obtenerEstado('EstadoReaperturaAbierta'),
		'coreApi'               => ($estadoApi === 'core') ? 1 : 0,
		'from'                  => $this->getSession('from'),
		/** Estados Caja  */
		'idReservaAtencion'     => $sinReserva,
		'extranjero'            => $extranjero,
		'cantidad'              => $idCantidad,
		/** Arrays desde 'validacionComplementariaCaja' */
		'talonarios'            => $validacion['oTalonario'],
		'TalonarioNumeroActual' => $validacion['TalonarioNumeroActual'],
		'subEmpresa'            => $validacion['subEmpresa'],
		'caja'                  => $validacion['caja'],
		'getPrestacionesCaja'   => $validacion['getPrestacionesCaja'],
		/** Arrays Caja */
		'listadoMediosPagos'    => $listadoMediosPago,
		'listadoOtrosMedios'    => $listadoOtrosMedios,
		'resultados'            => $result,
		/** Arrays desde Funciones o Repositorios */
		'errores'               => $this->obtenerServicioGlobales()->validacionesDocumentosFaltantes( $sucursalUsuario->getId(), $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo')),
		/** IDs Caja */
		'sucursal'              => $sucursalUsuario->getId(),
		'tipoAtencion'          => $tipoAtencion,
		'reserva'               => $reserva,
		/** IDs desde Funciones o Repositorio */
		'banco'                 => $this->obtenerServicioGlobales()->rFormaPago()->ObtieneBancoCaja( $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'), $oEmpresa),
		'cajero'                => $this->obtenerServicioGlobales()->rPagoCuenta()->GetCajeroByUser($idUser->getId()),
		'datosCompletos'        => $datosCompletos
		];

		return $this->render('CajaBundle:Api/Unab:index.html.twig', $parametrosArray);

	}

	protected function setSession($nombreVariable, $variableSesion) {
		return $this->get('session')->set($nombreVariable, $variableSesion);
	}

	private function clearSessionsVar() {
		foreach($this->arrSessionVarName as $variablesSesion){
			$this->killSession($variablesSesion);
		}
	}

	private function anularDiferenciasAyer() {

		$em     = $this->getDoctrine()->getManager();
		$oUser        = $this->getUser();
		$oFecha       = new \DateTime("now");
		$oDiferencias = $this->obtenerServicioGlobales()->rDiferencia()->anularDiferenciasAyer($oFecha);
		if(count($oDiferencias)>0){
			foreach($oDiferencias as $d){
				$oDiferencia = $em->getRepository('RebsolHermesBundle:Diferencia')->find($d);
				$oDiferencia->setFechaAnulacion($oFecha);
				$oDiferencia->setIdUsuarioAnulacion($oUser);
				$oDiferencia->setIdEstadoDiferencia($this->obtenerServicioGlobales()->obtenerEstado('DiferenciacajeroCancelaSolicitud'));
				$em->persist($oDiferencia);
			}
			$em->flush();
		}

	}

	protected function getSession($variableSesion) {
		return $this->get('session')->get($variableSesion);
	}

	protected function killSession($eliminarSesion) {
		return $this->get('session')->remove($eliminarSesion);
	}

	protected function validacionComplementariaCaja($idUser, $fecha, $session, $reserva, $idHorarioConsulta, $arrPrestaciones){

		$em     = $this->getDoctrine()->getManager();

		$subEmpresa[]                  = array();
		$arrTalonario[]                = array();

		$valorUno                      = 0;
		$valorDos                      = 0;
		$valorTres                     = 0;
		$auxReaperturaCount            = 0;
		$noCajero                      = 0;
		$subEmpresaTalonarioPrestacion = 0;
		$fechaPago                     = null;
		$talonarioNumeroActual         = null;
		$oCajaFindByUser               = $em->getRepository('RebsolHermesBundle:Caja')->findBy(array("idUsuario" => $idUser));

		if ($oCajaFindByUser){
			foreach ($oCajaFindByUser as $c){
				$estadoTemp = (!is_null($c->getIdEstadoReapertura()))?$c->getIdEstadoReapertura()->getId():null;
				if ($estadoTemp && $estadoTemp == $this->container->getParameter('EstadoReapertura.abierta')){

					$auxReaperturaCount = $auxReaperturaCount + 1;
					$oCajaTemp          = $c;
					$fechaReapertura    = $c->getFechaReapertura();

					break;
				} else {
					$auxReaperturaCount = $auxReaperturaCount + 0;
				}
			}
		}

		if ($auxReaperturaCount > 0){
			$fechaReapertura = $fechaReapertura->format("Y-m-d");
			if (strtotime($fecha) > strtotime($fechaReapertura)){
				$sincerrar = 0;
			}else{
				$sincerrar = 1;
			}
			$open = 1;
			$close = 1;
			$oCaja = $oCajaTemp;


			$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->findOneBy(array(
				"idUbicacionCaja"   => $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid(),
				"idEstado"          => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
				"idEstadoPila"      => $$this->container->getParameter('EstadoPila.activo')
				));

		} else {

			$oCaja = $this->obtenerServicioGlobales()->rPagoCuenta()->GetCajaByUser($idUser, $fecha);


			if ($oCaja){
				$this->get('session')->set('VarCajaHoy', $oCaja->getId());

				$sincerrar   = 1;
				$fechaCierre = $oCaja->getfechaCierre();
				$open        = 1;

				if ($fechaCierre){
					$fechaCierre = $fechaCierre->format("Y-m-d");
					if (strtotime($fechaCierre) == strtotime($fecha)){
						$close = 0;
					}else{
						$close = 1;
					}
				}else{
					$close = 1;
				}
			}else{
				$open      = 0;
				$close     = 0;
				$sincerrar = 0;
				$oCaja     = NULL;
				foreach ($oCajaFindByUser as $csc){
					$fechaApertura = $csc->getfechaApertura();
					$fechaCierre   = $csc->getfechaCierre();
					$fechaApertura = $fechaApertura->format("Y-m-d");
					if (strtotime($fecha) > strtotime($fechaApertura)){
						if ($fechaCierre === NULL){

							$sincerrar = 0;
							$oCaja = $csc;
						}else{
							$sincerrar = 1;
							$oCaja = $csc;
						}
					}else{
						$sincerrar = 1;
						$oCaja = $csc;
					}

					if ($sincerrar == 0){
						$oCaja = $csc;
						break;
					}
				}
			}
		}
        $folioGlobal = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');
		if ($oCaja){

			/*$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->findBy(array(
				"idUbicacionCaja"   => $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid(),
				"idEstado"          => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
				"idEstadoPila"      => $this->container->getParameter('EstadoPila.activo')
				));*/

            $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->findBy(
                array(
                    'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $oCaja->getidUbicacionCajero()->getidUbicacionCaja()->getid() : null,
                    'idEstado' => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
                    'idEstadoPila' => $this->container->getParameter('EstadoPila.activo')
                )
            );

			if ($oTalonario){
				$arrTalonario = array();
				foreach ($oTalonario as $t){

					$arrTalonario[] = $t->getId();

				}
				$this->get('session')->set('idTalonario', $arrTalonario);
			}else{
				$session->getFlashBag()->add('errorCajaRecaudacion', 'No se ha asignado Boleta a ésta Caja');
                return $this->redirect($this->generateUrl('Dashboard_ingresar', array('idModulo' => $this->container->getParameter("caja.idModulo"))));
			}
		} else {
			$ubicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
				"idEstado"          => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
				"idUsuario"         => $idUser
				));

            $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->findBy(
                array(
                    'idUbicacionCaja' => $folioGlobal['valor'] === '0' ? $ubicacionCajero->getIdUbicacionCaja()->getid() : null,
                    'idEstado' => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
                    'idEstadoPila' => $this->container->getParameter('EstadoPila.activo')
                )
            );

			/*$oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->findBy(array(
				"idUbicacionCaja"   => $ubicacionCajero->getIdUbicacionCaja()->getid(),
				"idEstado"          => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
				"idEstadoPila"      => $this->container->getParameter('EstadoPila.activo')
				));*/
		}

		if ($oTalonario){
			$arrTalonarioId = array();
			foreach($oTalonario as $t){
				$arrAux = array();
				$arrAux['id']               = $t->getId();
				$arrAux['idNombreArray']    =  $t->getIdSubEmpresa()->getId().$t->getid().$t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
				$arrAux['idSubEmpresa']     = $t->getIdSubEmpresa()->getId();
				$arrAux['idTipoDocumento']  =  $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid();
				$arrAux['actual']           = $t->getNumeroActual();
				$arrTalonarioId[]           = $arrAux;
			}

			$talonarioNumeroActual = $this->obtenerServicioGlobales()->rCaja()->GetNumeroActualSinAnulacionTalonario(  $arrTalonarioId,
				$this->container->getParameter('EstadoDetalleTalonario.anulada'),
				$em);

			foreach ($oTalonario as $t){

				if (array_key_exists($t->getIdSubEmpresa()->getId(), $subEmpresa)){
					$subEmpresa[$t->getIdSubEmpresa()->getId()] = $t->getIdSubEmpresa()->getId();
				}else{
					$subEmpresa[$t->getIdSubEmpresa()->getId()] = $t->getIdSubEmpresa()->getId();
				}
				if ($t->getnumeroActual() >= $t->getnumeroTermino()){
					$valorUno = $valorUno + 1;
				}else{
					if ($t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 1 || $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 3){
						$valorDos = $valorDos + 1;
						/** genero requisitos minimos para la generacion de boletas. al menos 2 Boletas afectas y  exentas (por sus distintas subempresas) */
						if ($t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 1 || $t->getIdRelEmpresaTipoDocumento()->getIdTipoDocumento()->getid() == 3){
							$valorTres = $valorTres + 1;
						}
					}

				}
			}
		}else{
			$sintalonario = 0;
		}

		if ($valorUno > 0){
			$sintalonario = 0;
			if ($valorDos >= 1){
				$sintalonarioAE = 1;
			}else{
				$sintalonario = 0;
				$sintalonarioAE = 0;
			}
		}else{
			$sintalonario = 1;
			if ($valorDos >= 1){
				if ($valorTres >= 1){
					$sintalonarioAE = 1;
				}else{
					$sintalonario = 0;
					$sintalonarioAE = 0;
				}
			}else{
				$sintalonario = 0;
				$sintalonarioAE = 0;
			}
		}

		/** DATOS DE RESERVA */

		if($reserva == 1){

			/** valida fecha de registro */
			$oHorario = $em->getRepository("RebsolHermesBundle:HorarioConsulta")->find($idHorarioConsulta);
			$oHorario = $oHorario->getFechaInicioHorario();
			$fechaHorario = $oHorario->format("Y-m-d");

			if (strtotime($fechaHorario) >= strtotime($fecha)){
				$fechaPago = 1;
			//si es 1 puede pagar HOY y MAÑANA desde Agenda
			}else{
				$fechaPago = 0;
			//si es 0 NO puede pagar HOY desde Agenda
			}

			if (!$oCaja){

				$oUbicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
					"idUsuario"     => $idUser,
					"idEstado"      => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo')
					));

				if (!$oUbicacionCajero || !$this->getUser()->getVerCaja()){
					$noCajero = 1;
				} else {
					$noCajero = 0;
				}

				$oCaja = null;

			} else {
				$noCajero = 0;
			}

			/** Valida SubEmpresa para Talonarios */
			if ($noCajero != 1){
				if ($this->obtenerServicioGlobales()->rCaja()->SubEmpresaDesdeCaja($arrTalonario, $arrPrestaciones)) {
					$subEmpresaTalonarioPrestacion = 1;
				}
			}
		}

		$parametroPagoTodosLosDias = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('SOLO_PAGOS_DEL_DIA');
		$getPrestacionesCaja       = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('BUSQUEDA_PRESTACION_CAJA');

		$validacionCajaParametrosArray  = [
		'pagoTodosLosDias'              => $parametroPagoTodosLosDias,
		'sincerrar'                     => $sincerrar,
		'sintalonario'                  => $sintalonario,
		'sintalonarioAE'                => $sintalonarioAE,
		'open'                          => $open,
		'close'                         => $close,
		'subEmpresa'                    => $subEmpresa,
		'oTalonario'                    => $oTalonario,
		'TalonarioNumeroActual'         => $talonarioNumeroActual,
		'caja'                          => $oCaja,
		/** RESERVA */
		'noCajero'                      => $noCajero,
		'fechaPago'                     => $fechaPago,
		'subEmpresaTalonarioPrestacion' => $subEmpresaTalonarioPrestacion,
		'getPrestacionesCaja'           => $getPrestacionesCaja
		];

		return $validacionCajaParametrosArray;
	}


	protected function formularioPago($arrayFormasPago, $arrayOtrosFormasPago, $idCantidad, $sucursalUsuario, $domiclio){

		$form1 = $this->createForm(DirectorioPacienteType::class, null); //type para la seccion Buscar rut
		$form2 = $this->createForm(DirectorioPacienteMascotaType::class, null); // type buscar por
		$form3 = $this->createForm(DirectorioPacienteMascotaKccType::class, null); // type para busqueda por kcc
		$form4 = $this->createForm(DirectorioPacienteMascotaChipType::class, null); // type par busqueda por chip

		$form5 = $this->createForm(DirectorioPacienteBusquedaAvanzadaType::class, null, array(
			'oEmpresa'    => $this->getSession('idEmpresaLogin'),
			'estado_activado' => $this->obtenerServicioGlobales()->obtenerEstado('EstadoActivo')->getId()
			));

		$form = $this->createForm(BusquedaAvanzadaDirectorioPacienteType::class, null);

		$mediosPagoform = $this->createForm(MediosPagoType::class, null, array(
			'validaform'       => null,
			'idFrom'           => $arrayFormasPago,
			'idFromOtros'      => $arrayOtrosFormasPago,
			'idCantidad'       => $idCantidad,
			'clone'            => false,
			'nuevo'            => true,
			'iEmpresa'         => $this->getSession('idEmpresaLogin'),
			'sucursal'         => $sucursalUsuario,
			'estado_activado'  => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
			));

		$pagoform = $this->createForm(PagoType::class, $domiclio, array(
			'validaform'       => null,
			'iEmpresa'         => $this->getSession('idEmpresaLogin'),
			'estado_activado'  => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
			'database_default' => $this->obtenerEntityManagerDefault()
			));

		$prestacionform = $this->createForm(PrestacionType::class, $domiclio, array(
			'validaform'       => null,
			'iEmpresa'         => $this->getSession('idEmpresaLogin'),
			'estado_activado'  => $this->obtenerServicioGlobales()->obtenerParametro('Estado.activo'),
			'sucursal'         => $sucursalUsuario,
			'database_default' => $this->obtenerEntityManagerDefault()
			));

		$formularioPagoParametrosArray = [
		/* Directorio Paciente */
		'form1'                         => $form1->createView(),
		'form2'                         => $form2->createView(),
		'form3'                         => $form3->createView(),
		'form4'                         => $form4->createView(),
		'form5'                         => $form5->createView(),
		/* Caja */
		'BusquedaPaciente'              => $form->createView(),
		'MediosPago'                    => $mediosPagoform->createView(),
		'Pago'                          => $pagoform->createView(),
		'Prestacion'                    => $prestacionform->createView()
		];


		return $formularioPagoParametrosArray;
	}





}