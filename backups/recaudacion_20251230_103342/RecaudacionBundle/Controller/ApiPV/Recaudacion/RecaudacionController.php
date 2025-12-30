<?php

namespace Rebsol\RecaudacionBundle\Controller\ApiPV\Recaudacion;

//use Rebsol\RecaudacionBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecaudacionController extends \Rebsol\RecaudacionBundle\Controller\RecaudacionController {
    
    protected function RenderViewInformeCaja($arr) {

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		$id                         = $arr['id'];
		$renderType                 = ($arr['print']==1) ? 'renderView':'render';
		$oDocumentoPagoDetalles     = NULL;
		$arrMediosMonto             = NULL;
		$detalleCaja                = NULL;
		$detalleCajat               = NULL;
		$arrayPagosCuenta           = array();
		$arrayBoletas               = array();
		$EstadoBoletaActiva         = $this->estado('EstadoBoletaActiva');
		$EstadoPagoActiva           = $this->estado('EstadoPagoActiva');
		$EstadoApi                  = ($estadoApi == "core")?1:0;
		$EstadoPagoRegularizada     = $this->estado('EstadoPagoRegularizada');
		$EstadoActivo               = $this->estado('EstadoActivo');
		$EstadoPagoGarantia         = $this->estado('EstadoPagoGarantia');
		$EstadoPagoAnulada          = $this->estado('EstadoPagoAnulada');
		$EstadoReaperturaAbierta    = $this->estado('EstadoReaperturaAbierta');
		$em                         = $this->getDoctrine()->getManager();
		$oCajaDetalles              = $em->getRepository("RebsolHermesBundle:Caja")->find($id);
		$idUser                     = $oCajaDetalles->getIdUsuario();

        /** INFORMES->CORE */
        $oCajaPrincipal            = $this->rCaja()->GetInformacionDetalladaCajaPagoProfesional($id, $idUser);
        $oCajaConBoleta            = $this->rCaja()->GetInformacionDetalladaCajaSecundariaPagoProfesional($id, $idUser, $EstadoBoletaActiva, $EstadoPagoActiva);
        
        /** revisar Informe Garantias */
        $oCajaConGarantia          = $this->rCaja()->GetInformacionDetalladaCajaGarantiaPagoProfesional($id, $idUser, $EstadoPagoRegularizada, $EstadoPagoGarantia);
        $oCajaPagoAnulados         = $this->rCaja()->GetInformacionDetalladaCajaAnuladaPagoProfesional($id, $idUser, $EstadoPagoAnulada);
        $oCajaConGarantiaAnulada   = $this->rCaja()->GetInformacionDetalladaGarantiaCajaAnuladaPagoProfesional($id, $idUser, $EstadoPagoAnulada);


        /** INFORMES->TODOS LOS CASOS */
		$formaPago                   = $this->rCaja()->GetFormasPagoPagoProfesional($id);
        $formaPagoGarantia           = $this->rCaja()->GetFormasPagoGarantiaPagoProfesional($id);
		$documentos                  = $this->rCaja()->GetResultadoDocumentosPagoProfesional($id);
        $documentosGarantia          = $this->rCaja()->GetResultadoDocumentosGarantiaPagoProfesional($id);
		$detallesMontosTipos         = $this->rCaja()->GetResultadosCuadraturaPagoProfesional($id);
		
        $detallesMontosBonosManuales = $this->rCaja()->GetResultadosCuadraturaBonoPagoProfesional($id, $EstadoPagoActiva);
		$detalleMontosDeposito       = $this->rCaja()->GetDetalleCajaDepositoPagoProfesional($id, $EstadoActivo);
        $detalleMontosGarantias      = $this->rCaja()->GetResultadosCuadraturaGarantiaPagoProfesional($id);

		$detalleMontosTiposBonos    =   $this->rCaja()->GetResultadosCuadraturaBonosPagoProfesional($id);

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

		return $this->$renderType('RecaudacionBundle:'.$arr['path'].':'.$arr['source'].'.html.twig', array(
			'caja'                        => $oCajaPrincipal,
			'cajac'                       => $oCajaConBoleta,
			'datosCaja'                   => $this->rCaja()->GetInformacionCaja($id),
			'cajero'                      => $this->rCaja()->GetCajeroInforme($idUser),
			'cajag'                       => $oCajaConGarantia,
			'cajaAnul'                    => $oCajaPagoAnulados,
			'cajaAnulg'                   => $oCajaConGarantiaAnulada,
			'cajaDetalles'                => $oCajaDetalles,
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
			'idcaja'                      => $id,
			'detalleCount'                => $oDocumentoPagoDetalles,
			'copagoCount'                 => $arrMediosMonto,
			'detalleCaja'                 => $detalleCaja,
			'detalleCajaTodo'             => $detalleCajat,
			'coreApi'                     => ($estadoApi === "core") ? 1: 0,
        ));
	}

    public function imprimirMediosDePagoAction(Request $request, $id) {

        return $this->RenderViewInformeCaja(
			array(
				'id'        => $id,
				'print'     => 0,
				'from'      => 0,
				'path'      => 'ApiPV\Recaudacion\GestionCaja\Informes',
				'source'    => 'InformeMedioDePago'
				)
			);
	}
	
	public function imprimirMediosDePagoPDFAction()
	{
		$html = $this->RenderViewInformeCaja(
			array(
				'id'        => $this->request('valoridCaja'),
				'print'     => 1,
				'from'      => 0,
				'path'      => 'ApiPV\Recaudacion\GestionCaja\Informes',
				'source'    => 'InformeMedioDePagoPDF'
				)
			);

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

	public function existeUsuarioIntegracionAction(Request $request) {
		$em                   = $this->getDoctrine()->getManager();
		$oEmpresa             = $this->ObtenerEmpresaLogin();
		$oPersonaIntegracion = $this->getUsuarioIntegracionByEmpresa($oEmpresa->getId());
		if(!empty($oPersonaIntegracion)) {
			return new JsonResponse(
				array(
					'resultado' => true
                )
			);
		}  else {
			return new JsonResponse(
                array(
					'resultado' => false
                )
			);
		}
	}

	public function getUsuarioIntegracionByEmpresa($idEmpresa) {
		$em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("
			SELECT
			ur.id as idUsuarioRebsol,
			p.rutPersona as rut,
			p.digitoVerificador as digito
			FROM
			Rebsol\HermesBundle\Entity\UsuariosRebsol ur
			JOIN ur.idPersona p
			WHERE 
				p.idEmpresa                    = ?2
			AND ur.esProfesionalIntegracion = 1
      	");
        $query->setParameter(2, $idEmpresa);
        return $query->getResult();  
	}

}