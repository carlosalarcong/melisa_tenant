<?php

namespace Rebsol\RecaudacionBundle\Controller\ApiPV\Supervisor\ConsolidadoCajaPorProfesional;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Rebsol\RecaudacionBundle\Form\Type\ApiPV\ConsolidadoCajaPorProfesional\ConsolidadoCajaPorProfesionalType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsolidadoCajaPorProfesionalController extends SupervisorController {

    public function indexAction(Request $request){

        $estadoApi = $this->estado('EstadoApi');

        if($estadoApi != 'core'){
            if($estadoApi['rutaApi'] === 'ApiPV'){
                $estadoApi = 'core';
            }
        }

        $em = $this->getDoctrine()->getManager();

            //Obtenemos el login de la empresa
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());
        $form = $this->createForm(ConsolidadoCajaPorProfesionalType::class, null,
            array(
                'estado_activado' => $this->container->getParameter('estado_activo'),
                'idEstado'        => $this->container->getParameter('estado_activo'),
                'idEmpresa'       => $oEmpresa->getId(),
                'idSucursal'      => $SucursalUsuario->getId(),
                'database_default'=> $this->obtenerEntityManagerDefault()
                ));

        $eSucursal        = '';
        $eTipoDocumento   = '';
        $eUbicacionCaja   = '';
        $eUbicacionCajero = '';
        $eUsuariosRebsol  = '';

        return $this->render('RecaudacionBundle:ApiPV/Supervisor/ConsolidadoCajaPorProfesional:index.html.twig', array(
            'form' => $form->createView(),
            'oSucursal' => $SucursalUsuario->getId(),
            'esucursal' => $eSucursal,
            'eTipoDocumento' => $eTipoDocumento,
            'eUbicacionCaja' => $eUbicacionCaja,
            'eUbicacionCajero' => $eUbicacionCajero,
            'eUsuariosRebsol' => $eUsuariosRebsol,
            'coreApi'          => ($estadoApi === "core") ? 1 : 0
        ));
    }

    public function renderViewInformeCajaPorProfesionalAction($id) {

        $estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
        }

        $fechaIngresada = $this->get('session')->get('fecha');
		$Sucursal       = $this->get('session')->get('sucursal');
        $em             = $this->getDoctrine()->getManager();
        
        $fechaAjaxReformat = new \DateTime(date("Y-m-d", strtotime($fechaIngresada)));
		$Fecha             = $fechaAjaxReformat->format("Y-m-d");

		//obtener todas las cajas disponibles, y obtener todos los valores a partir de eso con IN ARRAY en sql
        $arrCajas = $em->getRepository("RebsolHermesBundle:Caja")->informeCajaIndex($Fecha, $Sucursal);

        $arrIdsCajas = array();
        foreach($arrCajas as $caja) {
            array_push($arrIdsCajas, $caja['idCaja']);
        }

        $idUsuarioRebsol = $id;
        $arrayPagosCuenta           = array();
		$arrayBoletas               = array();
        $oDocumentoPagoDetalles     = NULL;
        $arrMediosMonto             = NULL;
        $detalleCaja                = NULL;
		$detalleCajat               = NULL;
        $EstadoBoletaActiva         = $this->estado('EstadoBoletaActiva');
        $EstadoPagoActiva           = $this->estado('EstadoPagoActiva');
        $EstadoPagoRegularizada     = $this->estado('EstadoPagoRegularizada');
		$EstadoActivo               = $this->estado('EstadoActivo');
        $EstadoPagoGarantia         = $this->estado('EstadoPagoGarantia');
        $EstadoPagoAnulada          = $this->estado('EstadoPagoAnulada');
        $EstadoReaperturaAbierta    = $this->estado('EstadoReaperturaAbierta');

        $oUsuarioRebsol = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($idUsuarioRebsol);
        $oPersona = $oUsuarioRebsol->getIdPersona();
        $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(['idPersona' => $oPersona]);

        if(!$oPersona->getRutPersona() || $oPersona->getRutPersona() == 0) {
            $rut = $oPersona->getIdentificacionExtranjero();
        } else {
            $rut = $oPersona->getRutPersona() . '-' . $oPersona->getDigitoVerificador();
        }

        $arrProfesional = [
            'identificacion' => $oPersona->getIdTipoIdentificacionExtranjero()->getNombre(),
            'rut' => $rut,
            'nombres' => $oPnatural->getNombrePnatural(),
            'apellidoPaterno' => $oPnatural->getApellidoPaterno(),
            'apellidoMaterno' => $oPnatural->getApellidoMAterno()
        ];

        $oCajaPrincipal = $this->rCaja()->GetInformacionDetalladaCajaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        foreach ($oCajaPrincipal as $key => $result) {
            $oCajaPrincipal[$key]['identificacionExtranjero'] = $this->get('CommonServices')->formatearRut($result['identificacionExtranjero']);
        }
        $oCajaConBoleta = $this->rCaja()->GetInformacionDetalladaCajaSecundariaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoBoletaActiva, $EstadoPagoActiva);
        foreach ($oCajaConBoleta as $key => $result) {
            $oCajaConBoleta[$key]['identificacionExtranjero'] = $this->get('CommonServices')->formatearRut($result['identificacionExtranjero']);
        }
        $arrPagosCuentaWeb = $this->getPagoCuentaWebByFechaProfesional($fechaIngresada, $Sucursal, $idUsuarioRebsol);
        foreach($arrPagosCuentaWeb as $oPagoCuentaWeb) {
            $oPaciente = $oPagoCuentaWeb->getIdPaciente();
            $oPnatural = $oPaciente->getIdPnatural();
            $oPersona = $oPnatural->getIdPersona();
            $oUsuarioProfesional = $oPaciente->getIdProfesional();
            $oPersonaProfesional = $oUsuarioProfesional->getIdPersona();

            $oPnaturalProfesional = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(['idPersona' => $oPersonaProfesional]);

            $rut = $oPersona->getRutPersona();
            $d = $oPersona->getDigitoVerificador();

            $arrWeb = [];

            $arrWeb = [
                "rut" => $rut,
                "dv" => $d,
                "identificacionExtranjero" => $oPersona->getIdentificacionExtranjero(),
                "nombreDocumento" => "Rut",
                "nombre" => $oPnatural->getNombrePnatural(),
                "apellidoP" => $oPnatural->getApellidoPaterno(),
                "montoPagoCuenta" => strval($oPagoCuentaWeb->getMonto()),
                "convenio" => null,
                "financiador" => $oPaciente->getIdFinanciador()->getNombrePrevision(),
                "nBono" => null,
                "bonificacion" => null,
                "copago" => null,
                "seguro" => null,
                "idCaja" => null,
                "idPagoCuenta" => $oPagoCuentaWeb->getId(),
                "diferencia" => null,
                "boleta" => null,
                "nombreMedico" => $oPnaturalProfesional->getNombrePnatural(),
                "apellidoPaternoMedico" => $oPnaturalProfesional->getApellidoPaterno(),
                "apellidoMaternoMedico" => $oPnaturalProfesional->getApellidoMaterno(),
                "estadoBoleta" => $oPagoCuentaWeb->getIdEstadoPago()->getId(),
                "idPaciente" => $oPaciente->getId(),
                "esExterno" => 0,
                "profesionalExterno" => null,
                "monto" => intval($oPagoCuentaWeb->getMonto()),
                "idEstadoPago" => $oPagoCuentaWeb->getIdEstadoPago()->getId()
            ];

            array_push($oCajaPrincipal, $arrWeb);
        }
        $oCajaConGarantia          = $this->rCaja()->GetInformacionDetalladaCajaGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoRegularizada, $EstadoPagoGarantia);
        $oCajaPagoAnulados         = $this->rCaja()->GetInformacionDetalladaCajaAnuladaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoAnulada);
        $oCajaConGarantiaAnulada   = $this->rCaja()->GetInformacionDetalladaGarantiaCajaAnuladaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoAnulada);

        $formaPago                   = $this->rCaja()->GetFormasPagoPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $montoPagoWeb = 0;
        foreach($arrPagosCuentaWeb as $oPagoCuentaWeb) {
            $oDetallePagoCuenta = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->findOneBy(['idPagoCuenta' => $oPagoCuentaWeb]);
            $oFormaPagoDetalle = $oDetallePagoCuenta->getIdFormaPago();

            $arrPagoWeb = [];
            $arrPagoWeb = [
                "nombreForma" => $oFormaPagoDetalle->getNombre(),
                "idForma" => $oFormaPagoDetalle->getId(),
                "idFormaPago" => $oFormaPagoDetalle->getIdTipoFormaPago()->getId(),
                "idPagoCuenta" => $oPagoCuentaWeb->getId(),
                "monto" => intval($oPagoCuentaWeb->getMonto()),
                "idPaciente" => $oPagoCuentaWeb->getIdPaciente()->getId(),
                "numeroDocumentoManual" => null,
                "numeroDocumentoElectronico" => null,
                "montoBonoElectronico" => null,
                "copagoImed" => null,
                "seguroComplementario" => null,
                "montoCheques" => null,
                "PorcentajeDiferencia" => null,
            ];
            $montoPagoWeb = $montoPagoWeb + $oPagoCuentaWeb->getMonto();
            array_push($formaPago, $arrPagoWeb);
        }

        $formaPagoGarantia           = $this->rCaja()->GetFormasPagoGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $documentos                  = $this->rCaja()->GetResultadoDocumentosPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $documentosGarantia          = $this->rCaja()->GetResultadoDocumentosGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        //TODO REVISAR DETALLESMONTOSTIPOS
        $detallesMontosTipos         = $this->rCaja()->GetResultadosCuadraturaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);

        if(count($arrPagosCuentaWeb) > 0) {
            $oDetallePagoCuenta = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->findOneBy(['idPagoCuenta' => $arrPagosCuentaWeb[0]]);
            $oFormaPagoDetalle = $oDetallePagoCuenta->getIdFormaPago();

            $arrMontosPagoWeb = [
                "nombreForma" => $oFormaPagoDetalle->getNombre(),
                "idfp" => $oFormaPagoDetalle->getId(),
                "idFormaPago" => $oFormaPagoDetalle->getIdTipoFormaPago()->getId(),
                "monto" => $montoPagoWeb,
                "montoCheque" => 0,
                "montoBono" => 0,
                "montoBonoElectronico" => 0,
                "montoBonoElectronicoSeguro" => 0
            ];

            array_push($detallesMontosTipos, $arrMontosPagoWeb);
        }

        $detallesMontosBonosManuales = $this->rCaja()->GetResultadosCuadraturaBonoPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoActiva);
        $detalleMontosDeposito       = $this->rCaja()->GetDetalleCajaDepositoPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoActivo);
        $detalleMontosGarantias      = $this->rCaja()->GetResultadosCuadraturaGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $detalleMontosTiposBonos    =   $this->rCaja()->GetResultadosCuadraturaBonosPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);

        foreach ($oCajaConBoleta as $c) {
			$arrayPagosCuenta[] = $c['idPagoCuenta'];
        }
        
        foreach ($arrayPagosCuenta as $c) {

			$BoletasQueryResult = $this->rCaja()->GetBoletasPorCaja($c, $EstadoBoletaActiva);

			if ($BoletasQueryResult) {

				foreach ($BoletasQueryResult as $b){
					$arrayBoletas[] = $b;
				}
			}
        }
        
        $arrMediosPagos = array();
		$countAux       = 0;

		foreach ($formaPago as $c) {
			$arrMediosPagos[$countAux] = array('id'=>$c['idForma'],'monto'=>0);
			$countAux                  = $countAux + 1;
        }

        //aqui copiar lo de informe pero filtrando por profesional (recaudacioncontroller)
        

        return $this->render('RecaudacionBundle:ApiPV:consolidadoCajaPorProfesional.html.twig', [
            'arrProfesional' => $arrProfesional,
            'fechaIngresada' => $fechaIngresada,
            'caja'                        => $oCajaPrincipal,
			'cajac'                       => $oCajaConBoleta,
			'cajag'                       => $oCajaConGarantia,
			'cajaAnul'                    => $oCajaPagoAnulados,
			'cajaAnulg'                   => $oCajaConGarantiaAnulada,
			'documentos'                  => $documentos,
			'documentosg'                 => $documentosGarantia,
			'formaPago'                   => $formaPago,
			'formaPagog'                  => $formaPagoGarantia,
			'formasPago'                  => $arrMediosPagos,
			'numerosBoletas'              => $arrayBoletas,
			'estadoActivo'                => $EstadoActivo,
			'estadoReapertura'            => $EstadoReaperturaAbierta->getid(),
			'estadoBoletaActiva'          => $EstadoBoletaActiva->getid(),
			'detallesMontosTipos'         => $detallesMontosTipos,
			'detallesMontosBonosManuales' => $detallesMontosBonosManuales,
			'detalleMontosDeposito'       => $detalleMontosDeposito,
			'detalleMontosTiposBonos'     => $detalleMontosTiposBonos,
			'detalleMontosGarantias'      => $detalleMontosGarantias,
			'idUsuarioRebsol'             => $idUsuarioRebsol,
			'detalleCount'                => $oDocumentoPagoDetalles,
			'copagoCount'                 => $arrMediosMonto,
			'detalleCaja'                 => $detalleCaja,
			'detalleCajaTodo'             => $detalleCajat,
			'coreApi'                     => ($estadoApi === "core") ? 1: 0,
        ]);
    }

    public function renderViewInformeCajaPorProfesionalPDFAction() {
       $estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
        }

        $fechaIngresada = $this->get('session')->get('fecha');
		$Sucursal       = $this->get('session')->get('sucursal');
        $em             = $this->getDoctrine()->getManager();
        
        $fechaAjaxReformat = new \DateTime(date("Y-m-d", strtotime($fechaIngresada)));
		$Fecha             = $fechaAjaxReformat->format("Y-m-d");

		//obtener todas las cajas disponibles, y obtener todos los valores a partir de eso con IN ARRAY en sql
        $arrCajas = $em->getRepository("RebsolHermesBundle:Caja")->informeCajaIndex($Fecha, $Sucursal);

        $arrIdsCajas = array();
        foreach($arrCajas as $caja) {
            array_push($arrIdsCajas, $caja['idCaja']);
        }

        $idUsuarioRebsol = $this->request('valorIdUsuarioRebsol');
        $arrayPagosCuenta           = array();
		$arrayBoletas               = array();
        $oDocumentoPagoDetalles     = NULL;
        $arrMediosMonto             = NULL;
        $detalleCaja                = NULL;
		$detalleCajat               = NULL;
        $EstadoBoletaActiva         = $this->estado('EstadoBoletaActiva');
        $EstadoPagoActiva           = $this->estado('EstadoPagoActiva');
        $EstadoPagoRegularizada     = $this->estado('EstadoPagoRegularizada');
		$EstadoActivo               = $this->estado('EstadoActivo');
        $EstadoPagoGarantia         = $this->estado('EstadoPagoGarantia');
        $EstadoPagoAnulada          = $this->estado('EstadoPagoAnulada');
        $EstadoReaperturaAbierta    = $this->estado('EstadoReaperturaAbierta');

        $oUsuarioRebsol = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($idUsuarioRebsol);
        $oPersona = $oUsuarioRebsol->getIdPersona();
        $oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(['idPersona' => $oPersona]);

        if(!$oPersona->getRutPersona() || $oPersona->getRutPersona() == 0) {
            $rut = $oPersona->getIdentificacionExtranjero();
        } else {
            $rut = $oPersona->getRutPersona() . '-' . $oPersona->getDigitoVerificador();
        }

        $arrProfesional = [
            'identificacion' => $oPersona->getIdTipoIdentificacionExtranjero()->getNombre(),
            'rut' => $rut,
            'nombres' => $oPnatural->getNombrePnatural(),
            'apellidoPaterno' => $oPnatural->getApellidoPaterno(),
            'apellidoMaterno' => $oPnatural->getApellidoMAterno()
        ];

        $oCajaPrincipal = $this->rCaja()->GetInformacionDetalladaCajaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $oCajaConBoleta = $this->rCaja()->GetInformacionDetalladaCajaSecundariaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoBoletaActiva, $EstadoPagoActiva);

        $arrPagosCuentaWeb = $this->getPagoCuentaWebByFechaProfesional($fechaIngresada, $Sucursal, $idUsuarioRebsol);
        foreach($arrPagosCuentaWeb as $oPagoCuentaWeb) {
            $oPaciente = $oPagoCuentaWeb->getIdPaciente();
            $oPnatural = $oPaciente->getIdPnatural();
            $oPersona = $oPnatural->getIdPersona();
            $oUsuarioProfesional = $oPaciente->getIdProfesional();
            $oPersonaProfesional = $oUsuarioProfesional->getIdPersona();

            $oPnaturalProfesional = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(['idPersona' => $oPersonaProfesional]);

            $rut = $oPersona->getRutPersona();
            $d = $oPersona->getDigitoVerificador();

            $arrWeb = [];

            $arrWeb = [
                "rut" => $rut,
                "dv" => $d,
                "identificacionExtranjero" => $oPersona->getIdentificacionExtranjero(),
                "nombreDocumento" => "Rut",
                "nombre" => $oPnatural->getNombrePnatural(),
                "apellidoP" => $oPnatural->getApellidoPaterno(),
                "montoPagoCuenta" => strval($oPagoCuentaWeb->getMonto()),
                "convenio" => null,
                "financiador" => $oPaciente->getIdFinanciador()->getNombrePrevision(),
                "nBono" => null,
                "bonificacion" => null,
                "copago" => null,
                "seguro" => null,
                "idCaja" => null,
                "idPagoCuenta" => $oPagoCuentaWeb->getId(),
                "diferencia" => null,
                "boleta" => null,
                "nombreMedico" => $oPnaturalProfesional->getNombrePnatural(),
                "apellidoPaternoMedico" => $oPnaturalProfesional->getApellidoPaterno(),
                "apellidoMaternoMedico" => $oPnaturalProfesional->getApellidoMaterno(),
                "estadoBoleta" => $oPagoCuentaWeb->getIdEstadoPago()->getId(),
                "idPaciente" => $oPaciente->getId(),
                "esExterno" => 0,
                "profesionalExterno" => null,
                "monto" => intval($oPagoCuentaWeb->getMonto()),
                "idEstadoPago" => $oPagoCuentaWeb->getIdEstadoPago()->getId()
            ];

            array_push($oCajaPrincipal, $arrWeb);
        }

        $oCajaConGarantia          = $this->rCaja()->GetInformacionDetalladaCajaGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoRegularizada, $EstadoPagoGarantia);
        $oCajaPagoAnulados         = $this->rCaja()->GetInformacionDetalladaCajaAnuladaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoAnulada);
        $oCajaConGarantiaAnulada   = $this->rCaja()->GetInformacionDetalladaGarantiaCajaAnuladaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoAnulada);

        $formaPago                   = $this->rCaja()->GetFormasPagoPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $montoPagoWeb = 0;
        foreach($arrPagosCuentaWeb as $oPagoCuentaWeb) {
            $oDetallePagoCuenta = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->findOneBy(['idPagoCuenta' => $oPagoCuentaWeb]);
            $oFormaPagoDetalle = $oDetallePagoCuenta->getIdFormaPago();

            $arrPagoWeb = [];
            $arrPagoWeb = [
                "nombreForma" => $oFormaPagoDetalle->getNombre(),
                "idForma" => $oFormaPagoDetalle->getId(),
                "idFormaPago" => $oFormaPagoDetalle->getIdTipoFormaPago()->getId(),
                "idPagoCuenta" => $oPagoCuentaWeb->getId(),
                "monto" => intval($oPagoCuentaWeb->getMonto()),
                "idPaciente" => $oPagoCuentaWeb->getIdPaciente()->getId(),
                "numeroDocumentoManual" => null,
                "numeroDocumentoElectronico" => null,
                "montoBonoElectronico" => null,
                "copagoImed" => null,
                "seguroComplementario" => null,
                "montoCheques" => null,
                "PorcentajeDiferencia" => null,
            ];
            $montoPagoWeb = $montoPagoWeb + $oPagoCuentaWeb->getMonto();
            array_push($formaPago, $arrPagoWeb);
        }

        $formaPagoGarantia           = $this->rCaja()->GetFormasPagoGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $documentos                  = $this->rCaja()->GetResultadoDocumentosPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $documentosGarantia          = $this->rCaja()->GetResultadoDocumentosGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        //TODO REVISAR DETALLESMONTOSTIPOS
        $detallesMontosTipos         = $this->rCaja()->GetResultadosCuadraturaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        
        if(count($arrPagosCuentaWeb) > 0) {
            $oDetallePagoCuenta = $em->getRepository('RebsolHermesBundle:DetallePagoCuenta')->findOneBy(['idPagoCuenta' => $arrPagosCuentaWeb[0]]);
            $oFormaPagoDetalle = $oDetallePagoCuenta->getIdFormaPago();

            $arrMontosPagoWeb = [
                "nombreForma" => $oFormaPagoDetalle->getNombre(),
                "idfp" => $oFormaPagoDetalle->getId(),
                "idFormaPago" => $oFormaPagoDetalle->getIdTipoFormaPago()->getId(),
                "monto" => $montoPagoWeb,
                "montoCheque" => 0,
                "montoBono" => 0,
                "montoBonoElectronico" => 0,
                "montoBonoElectronicoSeguro" => 0
            ];

            array_push($detallesMontosTipos, $arrMontosPagoWeb);
        } 
        
        $detallesMontosBonosManuales = $this->rCaja()->GetResultadosCuadraturaBonoPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoPagoActiva);
        $detalleMontosDeposito       = $this->rCaja()->GetDetalleCajaDepositoPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas, $EstadoActivo);
        $detalleMontosGarantias      = $this->rCaja()->GetResultadosCuadraturaGarantiaPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);
        $detalleMontosTiposBonos    =   $this->rCaja()->GetResultadosCuadraturaBonosPagoProfesionalPorProfesional($idUsuarioRebsol, $arrIdsCajas);

        foreach ($oCajaConBoleta as $c) {
			$arrayPagosCuenta[] = $c['idPagoCuenta'];
        }
        
        foreach ($arrayPagosCuenta as $c) {

			$BoletasQueryResult = $this->rCaja()->GetBoletasPorCaja($c, $EstadoBoletaActiva);

			if ($BoletasQueryResult) {

				foreach ($BoletasQueryResult as $b){
					$arrayBoletas[] = $b;
				}
			}
        }
        
        $arrMediosPagos = array();
		$countAux       = 0;

		foreach ($formaPago as $c) {
			$arrMediosPagos[$countAux] = array('id'=>$c['idForma'],'monto'=>0);
			$countAux                  = $countAux + 1;
        }

        //aqui copiar lo de informe pero filtrando por profesional (recaudacioncontroller)
        

        $html = $this->renderView('@Recaudacion/ApiPV/consolidadoCajaPorProfesionalPDF.html.twig', array(
            'arrProfesional' => $arrProfesional,
            'fechaIngresada' => $fechaIngresada,
            'caja'                        => $oCajaPrincipal,
			'cajac'                       => $oCajaConBoleta,
			'cajag'                       => $oCajaConGarantia,
			'cajaAnul'                    => $oCajaPagoAnulados,
			'cajaAnulg'                   => $oCajaConGarantiaAnulada,
			'documentos'                  => $documentos,
			'documentosg'                 => $documentosGarantia,
			'formaPago'                   => $formaPago,
			'formaPagog'                  => $formaPagoGarantia,
			'formasPago'                  => $arrMediosPagos,
			'numerosBoletas'              => $arrayBoletas,
			'estadoActivo'                => $EstadoActivo,
			'estadoReapertura'            => $EstadoReaperturaAbierta->getid(),
			'estadoBoletaActiva'          => $EstadoBoletaActiva->getid(),
			'detallesMontosTipos'         => $detallesMontosTipos,
			'detallesMontosBonosManuales' => $detallesMontosBonosManuales,
			'detalleMontosDeposito'       => $detalleMontosDeposito,
			'detalleMontosTiposBonos'     => $detalleMontosTiposBonos,
			'detalleMontosGarantias'      => $detalleMontosGarantias,
			'idUsuarioRebsol'             => $idUsuarioRebsol,
			'detalleCount'                => $oDocumentoPagoDetalles,
			'copagoCount'                 => $arrMediosMonto,
			'detalleCaja'                 => $detalleCaja,
			'detalleCajaTodo'             => $detalleCajat,
			'coreApi'                     => ($estadoApi === "core") ? 1: 0,
        ));

        return new Response( $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
			array(
                    'orientation'  => 'Landscape'
                    ,'lowquality'  => false
                    , 'page-width' => 1000
                    ,'zoom'        => '.6'
                    ,'page-size'   => 'Legal'
                    )
                ), 200,
            array(
                'Content-Type' => 'application/pdf'
                )
            );
    }

    public function getPagoCuentaWebByFechaProfesional($fecha, $idSucursal, $idProfesional) {
        $fechaOb = new \DateTime($fecha);
        $nuevafecha = new \DateTime($fecha);
		$nuevafecha->modify('+1 day');    
		return $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('pc')
            ->from('RebsolHermesBundle:PagoCuenta', 'pc')
            ->join('RebsolHermesBundle:DetallePagoCuenta', 'dpg', 'WITH', 'dpg.idPagoCuenta = pc.id')
            ->innerJoin('dpg.idFormaPago', 'fp')
			->innerJoin('pc.idPaciente', 'paciente')
			->join('RebsolHermesBundle:ReservaAtencion', 'ra', 'WITH', 'ra.idPaciente = paciente.id')
			->innerJoin('ra.idHorarioConsulta', 'hc')
			->where('pc.idPagoWeb IS NOT NULL')
			->andWhere('pc.fechaPago >= :fechaPago')
			->andWhere('hc.idSucursal = :idSucursal')
            ->andWhere('pc.fechaPago < :fechaPagoLast')
            ->andWhere('paciente.idProfesional = :idProfesional')
            ->andWhere('fp.pagoProfesional = 1')
			->setParameter('fechaPago', $fechaOb)
			->setParameter('idSucursal', $idSucursal)
            ->setParameter('fechaPagoLast', $nuevafecha->format('Y-m-d'))
            ->setParameter('idProfesional', $idProfesional)
            ->getQuery()
            ->getResult()
        ;
	}
}