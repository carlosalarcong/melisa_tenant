<?php

namespace App\Controller\Legacy;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// TODO: Migrar estas entidades de HermesBundle
// use Rebsol\HermesBundle\Entity\UsuariosRebsol;
// use Rebsol\HermesBundle\Entity\UsuarioExcluido;
/*
 * DefaultController:
 * 		Contiene funciones que son heredadas por otros.
 * 		Migrado desde HermesBundle para compatibilidad con código legacy.
 */
class DefaultController extends AbstractController {

	/**
	 * @var array
	 */
	public $aOpcionesEliminar = array(
		"0" => array('cod' => '0', 'msg' => 'Objeto Eliminado')
		,"1" => array('cod' => '1', 'msg' => 'Objeto No existe')
		,"2" => array('cod' => '2', 'msg' => 'No se puede eliminar el objeto (Posee objetos dependientes)')
		,"3" => array('cod' => '3', 'msg' => 'Usuario Inactivo')
		,"4" => array('cod' => '4', 'msg' => 'Usuario No Existe')
		,"5" => array('cod' => '5', 'msg' => 'No se puede Inactivar')
		,"6" => array('cod' => '6', 'msg' => 'Usuario Activado')
		,"7" => array('cod' => '7', 'msg' => 'Usuario No Existe')
		,"8" => array('cod' => '8', 'msg' => 'No es posible activar Usuario')
		,"9" => array('cod' => '9', 'msg' => 'Talonario en uso, no se puede eliminar')
		);

	/**
	 * createDeleteForm:
	 *		Crea un formulario para poder eliminar/desactivar una entidad.
	 *
	 * @param mixed $id The entity id
	 *
	 * @return Symfony\Component\Form\Form The form
	 */
	public function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
		->add('id')
		->getForm()
		;
	}

	public function ValidarFormulario($form) {
		return $form->isValid();
	}

	/**
	 * botarUsuarioRebsol: Función para botar a un oUsuarioRebsol del sistema
	 * @param  UsuariosRebsol|Int $oUsuarioRebsol
	 * @param  String              $mensaje
	 * @deprecated [v4.16.0] [Se eliminará desde la v4.25.0]
	 * @see
	 */
	public function botarUsuarioRebsol($oUsuarioRebsol, $mensaje) {
		$em = $this->getDoctrine()->getManager();

		if ($oUsuarioRebsol instanceof UsuariosRebsol) {

		} else {
			$oUsuarioRebsol = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($oUsuarioRebsol);
		}

		$oUsuarioExcluido = $em->getRepository('RebsolHermesBundle:UsuarioExcluido')->findOneBy(array('idUsuario'=>$oUsuarioRebsol->getId()));
		$oEstadoActivo = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));

		if (is_null($oUsuarioExcluido)) {
			$oUsuarioExcluido = new UsuarioExcluido();
			$oUsuarioExcluido->setIdUsuario($oUsuarioRebsol);
			$oUsuarioExcluido->setMensaje($mensaje);
			$oUsuarioExcluido->setIdEstado($oEstadoActivo);
		} else {
			$oUsuarioExcluido->setMensaje($mensaje);
			$oUsuarioExcluido->setIdEstado($oEstadoActivo);
		}
		$em->persist($oUsuarioExcluido);
		$em->flush();
	}

	/**
	 * @return Empresa
	 * Descripción: ObtenerEmpresaLogin() Obtiene el Objeto Empresa desde la sesión
	 */
	public function ObtenerEmpresaLogin() {
		$oUsuarioRebsol = $this->getUser();
		$oPersona = $oUsuarioRebsol->getIdPersona();
		$oEmpresa = $oPersona->getIdEmpresa();
		return $oEmpresa;
	}

    /**
     * obtenerParametroSesion:
     *      Obtiene el valor del parámetro que se encuentra en sessión
     * @param String $parametro
     * @return String $valorParametro
     */
    public function obtenerParametroSesion($parametro) {
    	if ($this->container === null) {
    		global $kernel;

    		if ('AppCache' == get_class($kernel)) {
    			$kernel = $kernel->getKernel();
    		}

    		$this->container =  $kernel->getContainer();
    	}

    	$session = $this->container->get('session');

    	if ( !$session->has($parametro) ) {
    		return null;
    	}

    	return $session->get($parametro);
    }

    public function obtenerApiModulo($idModulo = 1) {
    	$em = $this->getDoctrine()->getManager();

    	$oModulo = $em->getRepository('RebsolHermesBundle:Modulo')->find($idModulo);
    	$oEmpresa = $this->ObtenerEmpresaLogin();

    	$oApis = array();

    	if (is_null($oModulo)) {
    		$oApis = 'core';
    	} else {
    		$oApisModulo = $em->getRepository('RebsolHermesBundle:Perfil')->obtenerApiModuloEmpresa($oModulo, $oEmpresa, $this->container->getParameter('estado_activo'));

    		if (is_null($oApisModulo)) {
    			$oApis = 'core';
    		} else {
    			if ($oApisModulo->getRuta() != 'Default') {
    				$arrDetalle = array();
    				$arrDetalle['idModulo']     = $oApisModulo->getIdModulo()->getId();
    				$arrDetalle['nombreModulo'] = $oApisModulo->getIdModulo()->getNombre();
    				$arrDetalle['idApi']        = $oApisModulo->getId();
    				$arrDetalle['nombreApi']    = $oApisModulo->getNombre();
    				$arrDetalle['rutaApi']      = $oApisModulo->getRuta();
    				$oApis = $arrDetalle;
    			} else {
    				$oApis = 'core';
    			}
    		}
    	}

    	return $oApis;
    }

    public function obtenerValorApiModulo($idModulo, $valorArray = 'rutaApi')
    {
    	$sToolsService = $this->get('hermesTools.Tools');

    	return $sToolsService->obtenerApiModulo($idModulo, $valorArray);
    }

	/**
	 * @return String
	 * Descripción: ObtenerHoraServidor() Obtiene la hora del servidor.
	 */
	public function ObtenerFechaHoraServidorAction() {
		$fFechaDelServidor = new \DateTime();
		$aFechaDelServidor = json_encode($fFechaDelServidor);
		return new Response($aFechaDelServidor);
	}

	/**
	 * @return String
	 * Descripción: Función para mantener la conexión del servidor con el cliente.
	 */
	public function mantenerConexionServidorAction()
	{
		$this->getDoctrine()->getManager()->getConnection()->close();

		return new Response('true');
	}

	/**
	 * @return String
	 * Descripción: obtenerEntityManagerDefault(): Clarito como el agua
	 */
	public function obtenerEntityManagerDefault() {
		return $this->container->getParameter('database_default');
	}

	public function crearMeetingZoom($em, $oHorarioConsulta, $oReservaAtencion){

		$usuarioZoom = $this->container->getParameter('ApiZoom.User');
		$passZoom = $this->container->getParameter('ApiZoom.Password');
		$urlApi = $this->container->getParameter('ApiZoom.Url');

		$fechaInicio = $oHorarioConsulta->getFechaInicioHorario();
		$fechaInicioString = $fechaInicio->format('Y-m-d');
		$horaString = $fechaInicio->format('H:i:s');
		$fechaFinal = $fechaInicioString . "T" . $horaString . "-03:00";

		$oProfesionalNatural = $em->getRepository("RebsolHermesBundle:Pnatural")->findOneBy(['idPersona' => $oReservaAtencion->getIdUsuarioProfesional()->getIdPersona()->getId()]);

		$params['topic'] = "Su cita con " . $oProfesionalNatural->getNombrePnatural() . " " .$oProfesionalNatural->getApellidoPaterno() . " " . $oProfesionalNatural->getApellidoMaterno() . " para " . $oReservaAtencion->getIdEspecialidadMedica()->getNombreEspecialidadMedica();
		$params['start_time'] = $fechaFinal;
		$params['duration'] = $oHorarioConsulta->getDuracionConsulta();
		$params['user'] = $oReservaAtencion->getIdUsuarioProfesional()->getZoomUser();
		$params['email'] = $oReservaAtencion->getCorreoElectronico();
		$params['send_email'] = true;

		$params_json = json_encode($params);

		$curl = curl_init();
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

		if($response) {
			$responseJson = json_decode($response);
			$oReservaAtencion->setUrlMasterZoom($responseJson->masterUrl);
			$oReservaAtencion->setMeetingId($responseJson->meetingId);
			$em->persist($oReservaAtencion);
			$em->flush();
		} else {
			;
		}
	}

	public function em(){
		return $this->getDoctrine()->getManager();
	}

	/**
	 * Protecteds Estado
	 */
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
			case "FormaPagoTipo.Excedente":
				return $this->container->getParameter('FormaPagoTipo.Excedente');
				break;
			case "FormaPagoTipo.Transbank":
				return $this->container->getParameter('FormaPagoTipo.Transbank');
				break;
			default:
				return null;
		}
	}

	/**
	 * Protecteds Estado
	 * Descripción: Funcion consultar de manera mas agil por estados comunes a utilizar referentes de Caja.
	 */

	protected function estado($var)
	{

		$em = $this->getDoctrine()->getManager();

		switch ($var) {
			case "EstadoPilaActiva":
				return $em->getRepository('RebsolHermesBundle:EstadoPila')->find($this->container->getParameter('EstadoPila.activo'));
				break;
			case "EstadoPilaInaciva":
				return $em->getRepository('RebsolHermesBundle:EstadoPila')->find($this->container->getParameter('EstadoPila.inactivo'));
				break;
			case "EstadoReaperturaCerrada":
				return $em->getRepository('RebsolHermesBundle:EstadoReapertura')->find($this->container->getParameter('EstadoReapertura.cerrada'));
				break;
			case "EstadoReaperturaAbierta":
				return $em->getRepository('RebsolHermesBundle:EstadoReapertura')->find($this->container->getParameter('EstadoReapertura.abierta'));
				break;
			case "EstadoActivo":
				return $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('Estado.activo'));
				break;
			case "EstadoInc":
				return $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('Estado.inactivo'));
				break;
			case "EstadoPagoActiva":
				return $em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.pagadoNormal'));
				break;
			case "EstadoPagoAnulada":
				return $em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.anulado'));
				break;
			case "EstadoPagoGarantia":
				return $em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.garantia'));
				break;
			case "EstadoPagoRegularizada":
				return $em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.garantiaRegularizada'));
				break;
			case "EstadoPagoPendientePago":
				return $em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.pendientePago'));
				break;
			case "EstadoCuentaCerradaPagada":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaPagada'));
				break;
			case "EstadoCuentaAnulada":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.anulado'));
				break;
			case "EstadoCerradaPendientePago":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaPendientePago'));
				break;
			case "EstadoAbiertaPendientePago":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.abiertaPendientePago'));
				break;
			case "EstadoCerradaPagadaTotal":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaPagadaTotal'));
				break;
			case "EstadoAbiertaPagadaTotal":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.abiertaPagadaTotal'));
				break;
			case "EstadoCerradaRevisionInterna":
				return $em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaRevisionInterna'));
				break;
			case "EstadoBoletaActiva":
				return $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find($this->container->getParameter('EstadoDetalleTalonario.emitidas'));
				break;
			case "EstadoBoletaAnulada":
				return $em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find($this->container->getParameter('EstadoDetalleTalonario.anulada'));
				break;
			case "EstadoAccionClinicaSolicitado":
				return $em->getRepository('RebsolHermesBundle:EstadoAccionClinica')->find($this->container->getParameter('EstadoAccionClinica.solicitado'));
				break;
			case "EstadoTratamientoFinalizado":
				return $em->getRepository('RebsolHermesBundle:EstadoTratamiento')->find($this->container->getParameter('EstadoTratamiento.Finalizado'));
				break;
			case "EstadoTratamientoEnProceso":
				return $em->getRepository('RebsolHermesBundle:EstadoTratamiento')->find($this->container->getParameter('EstadoTratamiento.EnProceso'));
				break;
			case "EstadoTratamientoAnulado":
				return $em->getRepository('RebsolHermesBundle:EstadoTratamiento')->find($this->container->getParameter('EstadoTratamiento.Anulado'));
				break;
			case "EstadoApi":
				return $this->obtenerApiModulo($this->container->getParameter("modulo_caja"));
				break;
			case "DiferenciacajeroPideAutorizacion":
				return $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.cajeroPideAutorizacion'));
				break;
			case "Diferenciaautorizada":
				return $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.autorizada'));
				break;
			case "DiferenciadescuentoNoRequiereAutorizacion":
				return $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'));
				break;
			case "DiferenciacajeroCancelaSolicitud":
				return $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.cajeroCancelaSolicitud'));
				break;
			case "Diferenciarechazada":
				return $em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.rechazada'));
				break;
			case "EstadoGarantiaPorEmitir":
				return $em->getRepository('RebsolHermesBundle:EstadoGarantia')->find($this->container->getParameter('EstadoGarantia.porEmitir'));
				break;
			case "EstadoGarantiaEmitida":
				return $em->getRepository('RebsolHermesBundle:EstadoGarantia')->find($this->container->getParameter('EstadoGarantia.emitida'));
				break;
			case "EstadoGarantiaAnulada":
				return $em->getRepository('RebsolHermesBundle:EstadoGarantia')->find($this->container->getParameter('EstadoGarantia.anulada'));
				break;
			default:
				return null;
		}
	}

	public function getErrorMessages(Form $form) {
		$errors = array();
		foreach ($form->getErrors() as $key => $error) {
			$template = $error->getMessageTemplate();
			$parameters = $error->getMessageParameters();

			foreach ($parameters as $var => $value) {
				$template = str_replace($var, $value, $template);
			}

			$errors[$key] = $template;
		}
		if ($form->count()) {
			foreach ($form as $child) {
				if (!$child->isValid()) {
					$errors[$child->getName()] = $this->getErrorMessages($child);
				}
			}
		}
		return $errors;
	}

	public function obtenerIdProfesionalLogueado() {

		$idUsuario = $this->get('session')->get('idUsuarioLogin');

		if ($this->get('session')->has('RCH_idUsuarioProfesional')) {
			$idUsuario = $this->get('session')->get('RCH_idUsuarioProfesional');
		}

		if ($this->get('session')->has('idUsuarioLoginAjax')) {
			$idUsuario = $this->get('session')->get('idUsuarioLoginAjax');
		}

		return $idUsuario;
	}

	public function validarFechaNacimiento($fechaNacimiento)
	{
		$fechaExplode = explode("-", $fechaNacimiento);
		$error = array();
		if(in_array('', $fechaExplode)){
			$error['mensaje'] = 'Se debe ingresar una fecha válida.';
			return $error;
		}
		if((count($fechaExplode) == 3) && !in_array('', $fechaExplode)){
			$checkearFecha = checkdate($fechaExplode[1], $fechaExplode[0], $fechaExplode[2]);
			if(!$checkearFecha){
				$error['mensaje'] = 'Se debe ingresar una fecha válida.';
				return $error;
			}
			$fecha = new \DateTime(date('Y-m-d H:i:s', strtotime($fechaNacimiento)));
			$fecha = $fecha->format("d-m-Y");
			$fechahoy = new \DateTime();
			$fechahoy = $fechahoy->format("d-m-Y");
			if(strtotime($fecha) > strtotime($fechahoy)){
				$error['mensaje'] = 'Fecha no debe ser Superior a hoy.';
				return $error;
			}
		}
		return $error;
	}
}
?>
