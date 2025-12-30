<?php

namespace App\Controller\Caja\Recaudacion\Pago;

use Rebsol\CajaBundle\Form\Type\Recaudacion\Pago\DiferenciaType;
use App\Entity\Persona;
use App\Entity\PersonaDomicilio;
use App\Entity\Pnatural;
use App\Controller\Caja\RecaudacionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 * Update 13/11/2013
 */
class PagoController extends RecaudacionController {

		/**
		 * Actualiza Los Datos Actuales de una Persona ( independiente que sea o no sea paciente).
		 */
		public function actualizaPacienteApi1Action() {

		//recibo variables desde AJAX
			$emailAjax     = $this->container->get('request_stack')->getCurrentRequest()->query->get('email');
			$celAjax       = $this->container->get('request_stack')->getCurrentRequest()->query->get('cel');
			$fijoAjax      = $this->container->get('request_stack')->getCurrentRequest()->query->get('fijo');
			$trabajoAjax   = $this->container->get('request_stack')->getCurrentRequest()->query->get('trabajo');
			$direccionAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('direccion');
			$numeroAjax    = $this->container->get('request_stack')->getCurrentRequest()->query->get('numero');
			$restoAjax     = $this->container->get('request_stack')->getCurrentRequest()->query->get('resto');
			$comunaAjax    = $this->container->get('request_stack')->getCurrentRequest()->query->get('comuna');
			$idPersonaAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPersona');
			$em            = $this->getDoctrine()->getManager();
			$fechahoy      = new \DateTime();
			$timestamp     = strtotime($fechahoy->format("Y-m-d H:i:s"));

			if (($celAjax && $fijoAjax && $trabajoAjax) || $direccionAjax || $numeroAjax || $restoAjax || $comunaAjax) {
			//genera los objetos en base a entidades para actualizacion
				$oPersona = $em->getRepository('RebsolHermesBundle:Persona')->findOneBy(array(
					"id" => $idPersonaAjax));
				$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array(
					"idPersona" => $idPersonaAjax));
				$oComuna = $em->getRepository('RebsolHermesBundle:Comuna')->findOneBy(array(
					"id" => $comunaAjax));

			//inicia inyecciones
				$oPersonaDomicilio = new PersonaDomicilio();

			//Crea domicilio
				$oPersonaDomicilio->setIdPersona($oPersona);
				$oPersonaDomicilio->setTimestampFecha($timestamp);
				$oPersonaDomicilio->setFechaDomicilio(new \DateTime("now"));
				$oPersonaDomicilio->setdireccion($direccionAjax);
				$oPersonaDomicilio->setnumero($numeroAjax);
				$oPersonaDomicilio->setrestoDireccion($restoAjax);
				$oPersonaDomicilio->setIdComuna($oComuna);
			//Prepara Datos para Inyección de domicilio
				$em->persist($oPersonaDomicilio);

				$oPersona->settelefonoMovil($celAjax);
				$oPersona->settelefonoTrabajo($trabajoAjax);
				$oPersona->settelefonoFijo($fijoAjax);
				$oPersona->setcorreoElectronico(($emailAjax)?$emailAjax:null);

				$em->persist($oPersona);

				$em->flush();

				$this->get('session')->set('Pnatural', $oPnatural->getid());

				$respuesta = "ok";
			}
			else
			{
				$respuesta = "falta";
			}
			return new Response(json_encode($respuesta));
		}


		public function buscaMascotasPorDuenoAction(Request $request){

			$em        = $this->getDoctrine()->getManager();

			$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array(
				"idPersona" => $request->get('idPersona'))
			);

			$respuesta = $this->rPaciente()->HistoricoPagosPacienteApi1($oPnatural->getId());

			return new Response(json_encode($respuesta));
		}


		public function actualizaPacienteSinRutAction()
		{

		//recibo variables desde AJAX
			$rutAjax                 = $this->container->get('request_stack')->getCurrentRequest()->query->get('rut');
			$dvAjax                  = $this->container->get('request_stack')->getCurrentRequest()->query->get('dv');
			$nombreAjax              = $this->container->get('request_stack')->getCurrentRequest()->query->get('nombre');
			$apepAjax                = $this->container->get('request_stack')->getCurrentRequest()->query->get('apep');
			$apemAjax                = $this->container->get('request_stack')->getCurrentRequest()->query->get('apem');
			$sexoAjax                = $this->container->get('request_stack')->getCurrentRequest()->query->get('sexo');
			$fechaAjax               = $this->container->get('request_stack')->getCurrentRequest()->query->get('fecha');
			$emailAjax               = $this->container->get('request_stack')->getCurrentRequest()->query->get('email');
			$celAjax                 = $this->container->get('request_stack')->getCurrentRequest()->query->get('cel');
			$fijoAjax                = $this->container->get('request_stack')->getCurrentRequest()->query->get('fijo');
			$trabajoAjax             = $this->container->get('request_stack')->getCurrentRequest()->query->get('trabajo');
			$direccionAjax           = $this->container->get('request_stack')->getCurrentRequest()->query->get('direccion');
			$numeroAjax              = $this->container->get('request_stack')->getCurrentRequest()->query->get('numero');
			$restoAjax               = $this->container->get('request_stack')->getCurrentRequest()->query->get('resto');
			$comunaAjax              = $this->container->get('request_stack')->getCurrentRequest()->query->get('comuna');
            $paisAjax              = $this->container->get('request_stack')->getCurrentRequest()->query->get('pais');
			$sinRut                  = $this->container->get('request_stack')->getCurrentRequest()->query->get('sinRut');
			$idReserva               = $this->container->get('request_stack')->getCurrentRequest()->query->get('idReserva');
			$idExtranjero            = $this->container->get('request_stack')->getCurrentRequest()->query->get('extranjero');
			$rRepository             = $this->getDoctrine()->getRepository("RebsolHermesBundle:Paciente");
			$idPersonaAjax           = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPersona');
			$documentoExtranjeroajax = $this->container->get('request_stack')->getCurrentRequest()->query->get('documentoExtranjero');

			$identificacion          = $this->container->get('request_stack')->getCurrentRequest()->query->get('identificacion');
			$tipoDocumentoExtranjero = $this->container->get('request_stack')->getCurrentRequest()->query->get('tipoDocumentoExtranjero');

			$em                      = $this->getDoctrine()->getManager();
			$oEmpresa                = $this->ObtenerEmpresaLogin();
			$fechaAjax               = new \DateTime($fechaAjax);
			$fechahoy                = new \DateTime();
			$timestamp               = strtotime($fechahoy->format("Y-m-d H:i:s"));
			$sFunciones              = $this->get('libreria_funciones');
            $oPais                   = null;
            $oComuna                 = null;

            $arrParametroHabilitarPaisExtranjero = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('HABILITAR_PAIS_NACIONALIDAD_EXTRANJERO');
            $habilitarPaisExtranjero = intval($arrParametroHabilitarPaisExtranjero['valor']);

			if ($rutAjax || $dvAjax || $comunaAjax || $documentoExtranjeroajax|| $tipoDocumentoExtranjero || $nombreAjax || $apepAjax || $apemAjax || $sexoAjax || $fechaAjax || ($celAjax && $fijoAjax && $trabajoAjax))
			{
			//genera los objetos en base a entidades para actualizacion

                $oSexo      = $em->getRepository('RebsolHermesBundle:Sexo')->findOneBy(array(
					"id" => $sexoAjax));
				$aux        = 0;

				$identificacion = str_replace('.', '', $identificacion);

				$oPersona      = $em->getRepository('RebsolHermesBundle:Persona')->findOneBy(
					array(
						"idEmpresa"                      => $oEmpresa,
						"idTipoIdentificacionExtranjero" => $tipoDocumentoExtranjero,
						"identificacionExtranjero"       => $identificacion,
					)
				);

				if ($oPersona) {
					$idPersonaAjax = $oPersona->getId();
				}
			//inicia inyecciones

				if($idPersonaAjax == null){

					$oPersona = new Persona();
				//++++++++++++++++++++++//
				//Crea Persona
					// if($documentoExtranjeroajax){

						// $codigoExtranjero = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->ObtenerUltimoPacienteExtranjero($oEmpresa);
						// $dvCodigoEstranjero = $sFunciones->devuelveDigito($codigoExtranjero);
					// 	$oTipoIdentificacionExtranjero = $em->getRepository('RebsolHermesBundle:TipoIdentificacionExtranjero')->find($tipoDocumentoExtranjero);
					// 	$oPersona->setidTipoIdentificacionExtranjero($oTipoIdentificacionExtranjero);
					// 	$oPersona->setIdentificacionExtranjero($identificacion);
						// $oPersona->setrutPersona($codigoExtranjero);
						// $oPersona->setdigitoVerificador($dvCodigoEstranjero);

					// }else{

						$rutAjax = str_replace(".","", $rutAjax);
						$rutAjax = str_replace("-","", $rutAjax);

						$oTipoIdentificacionExtranjero = $em->getRepository('RebsolHermesBundle:TipoIdentificacionExtranjero')->find($tipoDocumentoExtranjero);
						$oPersona->setidTipoIdentificacionExtranjero($oTipoIdentificacionExtranjero);
						$oPersona->setIdentificacionExtranjero($identificacion);
						$oPersona->setrutPersona($rutAjax);
						$oPersona->setdigitoVerificador($dvAjax);
					// }
					$oPersona->settelefonoMovil($celAjax);
					$oPersona->settelefonoTrabajo($trabajoAjax);
					$oPersona->settelefonoFijo($fijoAjax);
					$oPersona->setcorreoElectronico(($emailAjax)?$emailAjax:null);
					$oPersona->setidEmpresa($oEmpresa);

					$em->persist($oPersona);


					$oPnatural = new Pnatural();

					$oPnatural->setidPersona($oPersona);
					$oPnatural->setnombrePnatural($nombreAjax);
					$oPnatural->setapellidoPaterno($apepAjax);
					$oPnatural->setapellidoMaterno($apemAjax);
					$oPnatural->setidSexo($oSexo);
					$oPnatural->setnumeroHermanoGemelo($aux);
					$oPnatural->setfechaNacimiento($fechaAjax);
					$em->persist($oPnatural);

				}else{

					$oPersona   = $em->getRepository('RebsolHermesBundle:Persona')->find($idPersonaAjax);
					$oPnatural  = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array(
						"idPersona" => $oPersona->getId()));

					$oPersona->settelefonoMovil($celAjax);
					$oPersona->settelefonoTrabajo($trabajoAjax);
					$oPersona->settelefonoFijo($fijoAjax);
					$oPersona->setcorreoElectronico(($emailAjax)?$emailAjax:null);
					$em->persist($oPersona);

					$oPnatural->setidSexo($oSexo);
					$oPnatural->setfechaNacimiento($fechaAjax);
					$em->persist($oPnatural);

				}

			 //inicia inyecciones
				//
				if($direccionAjax || $numeroAjax || $restoAjax){
					$oPersonaDomicilio = new PersonaDomicilio();
				//++++++++++++++++++++++//
				//Crea domicilio
					$oPersonaDomicilio->setIdPersona($oPersona);
					$oPersonaDomicilio->setTimestampFecha($timestamp);
					$oPersonaDomicilio->setFechaDomicilio(new \DateTime("now"));
					$oPersonaDomicilio->setdireccion($direccionAjax);
					$oPersonaDomicilio->setnumero($numeroAjax);
					$oPersonaDomicilio->setrestoDireccion($restoAjax);

                    if ($habilitarPaisExtranjero === 1  && (intval($tipoDocumentoExtranjero) === 2 || intval($tipoDocumentoExtranjero) === 4)) {
                        $oPais    = $em->getRepository('RebsolHermesBundle:Pais')->find($paisAjax);
                        $oPersonaDomicilio->setIdPais($oPais);
                        $oPersonaDomicilio->setIdComuna(null);
                    } else {
                        if($comunaAjax){
                            $oComuna = $em->getRepository('RebsolHermesBundle:Comuna')->findOneBy(array(
                                "id" => $comunaAjax));
                            $oPersonaDomicilio->setIdComuna($oComuna);
                            $oPersonaDomicilio->setIdPais(null);
                        }
                    }

				//Prepara Datos para Inyección de domicilio
					$em->persist($oPersonaDomicilio);
				}

				if($idReserva){
					$oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReserva);

					if($documentoExtranjeroajax){
						$oReservaAtencion->setRutPaciente($codigoExtranjero);
						$oReservaAtencion->setDigitoVerificadorPaciente($dvCodigoEstranjero);
						$oReservaAtencion->setIdentificacionExtranjero($documentoExtranjeroajax);
						$oReservaAtencion->setIdTipoIdentificacionExtranjero($oTipoIdentificacionExtranjero);
					}else{
						$oReservaAtencion->setRutPaciente($rutAjax);
						$oReservaAtencion->setDigitoVerificadorPaciente($dvAjax);
					}

					$oReservaAtencion->setnombres($nombreAjax);
					$oReservaAtencion->setapellidoPaterno($apepAjax);
					$oReservaAtencion->setapellidoMaterno($apemAjax);
					$oReservaAtencion->setidSexo($oSexo);
					$oReservaAtencion->setdireccion($direccionAjax);
					$oReservaAtencion->setnumero($numeroAjax);
					$oReservaAtencion->setrestoDireccion($restoAjax);
					$oReservaAtencion->setIdComuna($oComuna);
					$oReservaAtencion->setfechaNacimiento($fechaAjax);
					$oReservaAtencion->setcorreoElectronico(($emailAjax)?$emailAjax:null);
					$oReservaAtencion->settelefonoMovil($celAjax);
					$oReservaAtencion->settelefonoContacto($trabajoAjax);
					$oReservaAtencion->settelefonoFijo($fijoAjax);
				 //$oReservaAtencion->setIdentificacionExtranjero($identificacion);

					$em->persist($oReservaAtencion);
				}

				$em->flush();

				$respuesta = "ok";
			}
			else
			{
				$respuesta = "falta";
			}
			return new Response(json_encode($respuesta));
		}


		public function ActualizaPacienteAction()
		{
		//recibo variables desde AJAX
			$emailAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('email');
			$celAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('cel');
			$fijoAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('fijo');
			$trabajoAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('trabajo');
			$direccionAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('direccion');
			$numeroAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('numero');
			$restoAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('resto');
			$comunaAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('comuna');
            $paisAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('pais');
			$idPersonaAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPersona');
            $tipoDocumentoExtranjero = $this->container->get('request_stack')->getCurrentRequest()->query->get('tipoDocumentoExtranjero');
			$em = $this->getDoctrine()->getManager();
			$fechahoy = new \DateTime();
			$timestamp = strtotime($fechahoy->format("Y-m-d H:i:s"));
			$idReserva = $this->container->get('request_stack')->getCurrentRequest()->query->get('idReserva');

            $arrParametroHabilitarPaisExtranjero = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('HABILITAR_PAIS_NACIONALIDAD_EXTRANJERO');
            $habilitarPaisExtranjero = intval($arrParametroHabilitarPaisExtranjero['valor']);


			if ($celAjax || $fijoAjax || $trabajoAjax)
			{

			//genera los objetos en base a entidades para actualizacion
				$oPersona = $em->getRepository('RebsolHermesBundle:Persona')->findOneBy(array(
					"id" => $idPersonaAjax));

			//inicia inyecciones
			//
				if($direccionAjax || $numeroAjax || $restoAjax){
					$oPersonaDomicilio = new PersonaDomicilio();
			//++++++++++++++++++++++//
			//Crea domicilio
					$oPersonaDomicilio->setIdPersona($oPersona);
					$oPersonaDomicilio->setTimestampFecha($timestamp);
					$oPersonaDomicilio->setFechaDomicilio(new \DateTime("now"));
					$oPersonaDomicilio->setdireccion($direccionAjax);
					$oPersonaDomicilio->setnumero($numeroAjax);
					$oPersonaDomicilio->setrestoDireccion($restoAjax);

                    if ($habilitarPaisExtranjero === 1  && (intval($tipoDocumentoExtranjero) === 2 || intval($tipoDocumentoExtranjero) === 4)) {
                        $oPais    = $em->getRepository('RebsolHermesBundle:Pais')->find($paisAjax);
                        $oPersonaDomicilio->setIdPais($oPais);
                        $oPersonaDomicilio->setIdComuna(null);
                    } else {
                        if($comunaAjax){
                            $oComuna = $em->getRepository('RebsolHermesBundle:Comuna')->findOneBy(array(
                                "id" => $comunaAjax));
                            $oPersonaDomicilio->setIdComuna($oComuna);
                            $oPersonaDomicilio->setIdPais(null);
                        }
                    }


                    //Prepara Datos para Inyección de domicilio
					$em->persist($oPersonaDomicilio);
				}

				$oPersona->setTelefonoMovil(($celAjax)?$celAjax:null);
				$oPersona->setTelefonoTrabajo($trabajoAjax);
				$oPersona->setTelefonoFijo($fijoAjax);
				$oPersona->setCorreoElectronico(($emailAjax)?$emailAjax:null);
				$em->persist($oPersona);


				if($idReserva){
					$oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReserva);

					$oReservaAtencion->setdireccion($direccionAjax);
					$oReservaAtencion->setnumero($numeroAjax);
					$oReservaAtencion->setrestoDireccion($restoAjax);
					if(isset($oComuna)){
						$oReservaAtencion->setIdComuna($oComuna);
					}

					$oReservaAtencion->setcorreoElectronico(($emailAjax)?$emailAjax:null);
					$oReservaAtencion->setTelefonoMovil($celAjax);
					$oReservaAtencion->setTelefonoContacto($trabajoAjax);
					$oReservaAtencion->setTelefonoFijo($fijoAjax);
			 //$oReservaAtencion->setIdentificacionExtranjero($identificacion);

					$em->persist($oReservaAtencion);
				}

				$em->flush();
				$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array(
					"idPersona" => $oPersona->getId()));
				$this->get('session')->set('Pnatural', $oPnatural->getid());

				$respuesta = "ok";
			}
			else
			{
				$respuesta = "falta";
			}
			return new Response(json_encode($respuesta));
		}

	/**
	 * Crea una Pesona (pero aun no es paciente).
	 */
	public function creaPacienteAction(Request $request) {


		$arrayRequest                = $request->query->all();
		$arrayRequest[ 'idEmpresa' ] = $this->ObtenerEmpresaLogin()->getId();

		if ($this->get('recaudacion.RegistroPersona')->generaRegistroPersonaDB($arrayRequest)) {

			$respuesta = 'ok';
		} else {

			$respuesta = 'falta';
		}

		return new Response(
			json_encode($respuesta)
			);
	}


	/**
	 * 	@deprecated [<5.*>] [<description>]
	 * Crea una Pesona (pero aun no es paciente).
	 */
	public function creaPacienteApiAction()
	{

		//recibo variables desde AJAX
		$rutAjax        = $this->container->get('request_stack')->getCurrentRequest()->query->get('rut');
		$dvAjax         = $this->container->get('request_stack')->getCurrentRequest()->query->get('dv');
		$nombreAjax     = $this->container->get('request_stack')->getCurrentRequest()->query->get('nombre');
		$apepAjax       = $this->container->get('request_stack')->getCurrentRequest()->query->get('apep');
		$apemAjax       = $this->container->get('request_stack')->getCurrentRequest()->query->get('apem');
		$emailAjax      = $this->container->get('request_stack')->getCurrentRequest()->query->get('email');
		$celAjax        = $this->container->get('request_stack')->getCurrentRequest()->query->get('cel');
		$fijoAjax       = $this->container->get('request_stack')->getCurrentRequest()->query->get('fijo');
		$trabajoAjax    = $this->container->get('request_stack')->getCurrentRequest()->query->get('trabajo');
		$direccionAjax  = $this->container->get('request_stack')->getCurrentRequest()->query->get('direccion');
		$numeroAjax     = $this->container->get('request_stack')->getCurrentRequest()->query->get('numero');
		$restoAjax      = $this->container->get('request_stack')->getCurrentRequest()->query->get('resto');
		$comunaAjax     = $this->container->get('request_stack')->getCurrentRequest()->query->get('comuna');

		$em             = $this->getDoctrine()->getManager();
		$oEmpresa       = $this->ObtenerEmpresaLogin();
		$fechahoy       = new \DateTime();
		$timestamp      = strtotime($fechahoy->format("Y-m-d H:i:s"));

		if ($rutAjax || $dvAjax || $nombreAjax || $apepAjax || $apemAjax || ($celAjax && $fijoAjax && $trabajoAjax) || $direccionAjax || $numeroAjax || $restoAjax || $comunaAjax)
		{

			//genera los objetos en base a entidades para actualizacion
			$oComuna = $em->getRepository('RebsolHermesBundle:Comuna')->findOneBy(array(
				"id" => $comunaAjax));
			$aux = 0;
			//inicia inyecciones

			$oPersona = new Persona();
			//++++++++++++++++++++++//
			//Crea Persona
			$oPersona->setrutPersona($rutAjax);
			$oPersona->setdigitoVerificador($dvAjax);
			$oPersona->settelefonoMovil($celAjax);
			$oPersona->settelefonoTrabajo($trabajoAjax);
			$oPersona->settelefonoFijo($fijoAjax);
			$oPersona->setcorreoElectronico(($emailAjax)?$emailAjax:null);
			$oPersona->setidEmpresa($oEmpresa);

			$em->persist($oPersona);

			$oPersonaDomicilio = new PersonaDomicilio();
			//++++++++++++++++++++++//
			//Crea domicilio
			$oPersonaDomicilio->setIdPersona($oPersona);
			$oPersonaDomicilio->setTimestampFecha($timestamp);
			$oPersonaDomicilio->setdireccion($direccionAjax);
			$oPersonaDomicilio->setnumero($numeroAjax);
			$oPersonaDomicilio->setrestoDireccion($restoAjax);
			$oPersonaDomicilio->setIdComuna($oComuna);
			$em->persist($oPersonaDomicilio);

			$oPnatural = new Pnatural();

			$oPnatural->setidPersona($oPersona);
			$oPnatural->setnombrePnatural($nombreAjax);
			$oPnatural->setapellidoPaterno($apepAjax);
			$oPnatural->setapellidoMaterno($apemAjax);
			$oPnatural->setnumeroHermanoGemelo($aux);
			$em->persist($oPnatural);


			$em->flush();

			$respuesta = "ok";
		}
		else
		{
			$respuesta = "falta";
		}
		return new Response(json_encode($respuesta));
	}

	public function consulaPlanesAction()
	{
		$em = $this->getDoctrine()->getManager();
		$oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$sucursalAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
		$datoAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('dato');
		$fService = $this->get('Caja_valida');
		$result = $fService->ConsultaPlan($sucursalAjax, $datoAjax, $oEstadoAct);
		$this->setSession('prevision', $datoAjax);
		return new Response(json_encode($result));
	}

	public function consulaProfesionalesAction() {

		$oEmpresa     = $this->ObtenerEmpresaLogin();
		$idEstadoAct  = $this->container->getParameter('EstadoUsuarios.activo');
		$idRolMedico  = $this->container->getParameter('rol_medico');
		$sucursalAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
		$fService     = $this->get('Caja_valida');
		$result       = $fService->ConsultaProfesionales($idEstadoAct, $idRolMedico, $sucursalAjax, $oEmpresa->getId());

		return new Response(json_encode($result));
	}

	public function consulaOrigenAction()
	{
		$em = $this->getDoctrine()->getManager();
		$oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$sucursalAjax = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
		$fService = $this->get('Caja_valida');
		$result = $fService->ConsultaOrigen($oEstadoAct, $sucursalAjax);

		return new Response(json_encode($result));
	}

	public function consultaPacienteAction(Request $request)
	{
		$em                 = $this->getDoctrine()->getManager();
		$oEmpresa           = $this->ObtenerEmpresaLogin();
		$tipoIdentificacion = $request->query->get('tipoIdentificacion');
		$identificacion     = $request->query->get('identificacion');

		if ( $tipoIdentificacion == 1 ) {
			$identificacion = str_replace('.', '', $identificacion);
		}

		$oPersona           = $em->getRepository('RebsolHermesBundle:Persona')->findOneBy(
			array(
				'idTipoIdentificacionExtranjero' => $tipoIdentificacion,
				'identificacionExtranjero'       => $identificacion,
				'idEmpresa'                      => $oEmpresa->getId()
				)
			);

		$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(array(
			'idPersona' => ( $oPersona ) ?  $oPersona->getId() : null
			)
		);

		$result = false;

		if ( $oPnatural ) {

			$this->get('session')->set('Pnatural', $oPnatural->getid());
			$result = true;
		}

		return new Response( json_encode($result) );
	}


	public function diferenciaAction(Request $request){

		//ELEMENTOS BASICOS
		$em = $this->getDoctrine()->getManager();
		$data = $request->get('data');
		$oEmpresa = $this->ObtenerEmpresaLogin();
		$oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$SC = $this->get('session')->get('vSumaCantidad');
		//CONSTRUCCION ARREGLOS
		$arrAtencion = array();

		foreach ($data as $atencion) {
			$oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($atencion[0]);
			$arrAtencion[] = array(
				'id' => $oAccionClinica->getid(),
				'codigo' => $oAccionClinica->getcodigoAccionClinica(),
				'nombre' => $oAccionClinica->getnombreAccionClinica(),
				'cantidad' => $atencion[1],
				'total' => $atencion[2],
				);
		}

		// FORMULARIOS
		$form = $this->createForm(DiferenciaType::class, NULL, array(
			'iEmpresa' => $oEmpresa->getId()
			, 'estado_activado' => $oEstado
			));

		// return $this->render('RebsolHermesBundle:Caja\Recaudacion\Pago\Diferencia:indexDiferencia.html.twig', array(
		return $this->render('CajaBundle:Recaudacion\Pago\Diferencia:indexDiferencia.html.twig', array(
			'formDiferecia' => $form->createView(),
			'total' => $SC,
			'atenciones' => $arrAtencion));
	}

	public function consultaMotivoPorTipoAction(){
		$em = $this->getDoctrine()->getManager();
		$oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$tipo = $this->container->get('request_stack')->getCurrentRequest()->query->get('tipo');

		$oMotivoDiferencia = $em->getRepository('RebsolHermesBundle:MotivoDiferencia')->findBy(
			array(
				'idTipoDiferencia' => $tipo
				,'idEstado' => $oEstado
				));
		$aMotivos = array();
		foreach ($oMotivoDiferencia as $value) {
			$aMotivos[] = array('id' => $value->getId(), 'nombre' => $value->getNombre());
		}
		return new Response(json_encode($aMotivos));
	}

	public function guardaIdPnaturalMascotaAction(){
		$em = $this->getDoctrine()->getManager();
		$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->ajax('id'));
		$this->setSession('idPnaturalMascota', ($this->ajax('id')));
		$oDueno = ($this->getSession('idPnaturalMascota'))?$em->getRepository("RebsolHermesBundle:Pnatural")->obtenerPadrePnat($this->ajax('id')):null;
		$this->setSession('idPnaturalDueno', $oDueno->getId());
		$arrayDueñoMascota = array(
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
			'rutMascota' => $oPnatural->getIdPersona()->getRutPersona()
			);
		/**
		 * @return Response json_enconde
		 */
		return new Response(json_encode(($arrayDueñoMascota)?$arrayDueñoMascota:null));

	}

	public function buscaMascotasClienteAction(){
		$em = $this->getDoctrine()->getManager();
		$arr = array();
		$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->find($this->getSession('idPnaturalDueno'));
		$oMascotas = $this->rPagoCuenta()->GetMascotasPorDuenoApi1($this->getSession('idPnaturalDueno'));
		foreach($oMascotas as $oMascota){
			$arrayDueñoMascota = array(
				'nombre'        => $oPnatural->getNombrePnatural()." ".$oPnatural->getApellidoPaterno()." ".$oPnatural->getApellidoMaterno(),
			   ///////// MASCOTA ////////
				'id'            => $oMascota->getId(),
				'nombreMascota' => $oMascota->getNombrePnatural(),
				'chip'          => $oMascota->getChip(),
				'kcc'           => $oMascota->getKcc(),
				'sexo'          => $oMascota->getIdSexo()->getNombreSexo(),
				'especie'       => $oMascota->getIdRaza()->getIdEspecie()->getNombre(),
				'raza'          => $oMascota->getIdRaza()->getNombre(),
				'color'         => $oMascota->getColor()
				);
			$arr[] = $arrayDueñoMascota;
		}
		return new Response(json_encode((count($arr)>0)?$arr:null));
	}

	public function busquedaExternoAction() {
		$rut               = $this->container->get('request_stack')->getCurrentRequest()->query->get('rut');
		$em                = $this->getDoctrine()->getManager();
		$arrExternos       = $em->getRepository('RebsolHermesBundle:DerivadorExterno')
		->busquedaExternoPorRut($rut);
		$renderView        = $this->renderView('CajaBundle:Recaudacion\Pago\Pago_Form:verExternos.html.twig',
			array( 'oExternos' => $arrExternos )
			);
		return new Response($renderView);
	}

	/**
	 * @return Response Json
	 * Descripción: ObtieneExternoAction() Obtiene los datos de un derivador externo
	*/
	public function obtieneExternoAction() {
		$em                = $this->getDoctrine()->getManager();
		$id                = $this->container->get('request_stack')->getCurrentRequest()->query->get('id');
		$oDerivadorExterno = $em->getRepository('RebsolHermesBundle:DerivadorExterno')->find($id);
		$respuesta = array(
			'rut'    =>$oDerivadorExterno->getRut(),
			'dv'     =>$oDerivadorExterno->getDigitoVerificador(),
			'nombre' =>$oDerivadorExterno->getNombre()
			);
		return new Response(json_encode($respuesta));
	}

}
