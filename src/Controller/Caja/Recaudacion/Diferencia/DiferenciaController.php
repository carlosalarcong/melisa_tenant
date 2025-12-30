<?php

namespace App\Controller\Caja\Recaudacion\Diferencia;

use Rebsol\HermesBundle\Entity\Diferencia;
use Rebsol\HermesBundle\Entity\PersonaDomicilio;
use App\Controller\Caja\RecaudacionController;
use App\Form\Recaudacion\Pago\DiferenciaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 11/09/2014
 **/
class DiferenciaController extends RecaudacionController {

			/**
			 * [generarDiferenciaAction description]
			 * @return [type] [description]
			 */
			public function generarDiferenciaAction(){

				$oEmpresa           = $this->ObtenerEmpresaLogin();
				$domiclio           = new PersonaDomicilio();
				$count              = $this->getSession('countPrestacionesArticulos');
				$listaPrestaciones  = $this->getSession('listaPrestaciones');

				foreach ($listaPrestaciones as $key => $prestacion) {
                    $listaPrestaciones[ $key ][ 6 ] = intval($prestacion[ 1 ])*intval( str_replace(',', '', $prestacion[ 2 ]));
				}

				$this->killSession('countPrestacionesArticulos');
				$this->killSession('listaPrestaciones');
				$Diferenciaform     = $this->createForm(DiferenciaType::class, $domiclio, array(
					'iEmpresa'           => $oEmpresa->getId(),
					'estado_activado'    => $this->estado('EstadoActivo')->getId(),
					'database_default'   => $this->obtenerEntityManagerDefault(),
					'count'              => $count
					));

				return $this->render('RecaudacionBundle:Recaudacion/Pago/Pago_Form:Diferencia.html.twig', array(
					'diferencia_form'       => $Diferenciaform->createView(),
					'count'                 => $count,
					'listaPrestaciones'     => $listaPrestaciones
					));
			}

			public function generarDiferenciaSaldoAction(){

				$oEmpresa           = $this->ObtenerEmpresaLogin();
				$domiclio           = new PersonaDomicilio();
				$saldo              = $this->ajax('saldo');
				$count              = 1;
				$Diferenciaform     = $this->createForm(DiferenciaType::class, $domiclio, array(
					'iEmpresa'           => $oEmpresa->getId(),
					'estado_activado'    => $this->estado('EstadoActivo')->getId(),
					'database_default'   => $this->obtenerEntityManagerDefault(),
					'count'              => $count
					));

				return $this->render('RecaudacionBundle:Recaudacion/Pago/Pago_Form:DiferenciaSaldo.html.twig', array(
					'diferencia_form'       => $Diferenciaform->createView(),
					'count'                 => $count,
					'saldo'                 => $saldo
					));
			}

			public function getMotivosDiferenciaAction(){
				$TipoDiferencia     = $this->container->get('request_stack')->getCurrentRequest()->query->get('tipoDiferencia');
				$result             = $this->rDiferencia()->MotivoDiferenciaPorTipoDiferencia($TipoDiferencia);
				return new Response(json_encode($result));
			}

			public function getTipoSentidoAction(){
				$TipoDiferencia     = $this->container->get('request_stack')->getCurrentRequest()->query->get('tipoDiferencia');
				$result             = $this->rDiferencia()->getTipoSentido($TipoDiferencia);
				return new Response(json_encode($result));
			}

            public function getTipoSentidoPorMotivoDiferenciaAction(Request $request){
                $TipoDiferencia     = $request->query->get('motivoDiferencia');
                $result             = $this->rDiferencia()->getTipoSentidoPorMotivoDiferencia($TipoDiferencia);
                return new Response(json_encode($result));
            }


    public function countPrestacionesArticulosAction(){

				$this->setSession('countPrestacionesArticulos', $this->ajax('count'));
				$this->setSession('listaPrestaciones', $this->ajax('listaPrestaciones'));
				return new Response(1);

			}

			public function solicitarDiferenciaAction(){

				$em                   = $this->getDoctrine()->getManager();
				$oUser                = $this->getUser();
				$como                 = $this->ajax('como');
				$agrupacion           = $this->ajax('agrupcion');
				$tipoDiferencia       = $this->ajax('tipoDiferenciaGlobal');
				$motivoDiferencia     = $this->ajax('motivoDiferencia');
				$subTotal             = $this->ajax('subTotal');
				$DifTotal             = $this->ajax('DifTotal');
				$FullTotal            = $this->ajax('FullTotal');
				$oFecha               = new \DateTime("now");
				$arrRespuesta         = array();
				$oDiferencia          = new Diferencia();

				if(isset($como) && isset($agrupacion)){

					$this->setSession('diferenciaSaldo', 0);

				} else {

					$this->setSession('diferenciaSaldo', 1);

				}

				$oDiferencia->setFechaSolicitud($oFecha);
				$oDiferencia->setTotalCuenta($subTotal);
				$oDiferencia->setTotalDescuento($DifTotal);
				$oDiferencia->setTotalCuentaConDescuento($FullTotal);
				$oDiferencia->setIdUsuarioSolicitud($oUser);

				if($tipoDiferencia == 1){

					$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciadescuentoNoRequiereAutorizacion'));
					$arrRespuesta['autorizacion']       = 'NR';

				}

				if($tipoDiferencia == 2){

					$montoMaximoDiferencia = $this->rParametro()->obtenerParametro('MONTO_MAXIMO_DIFERENCIA')['valor'];

					if($DifTotal > $montoMaximoDiferencia){

						$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciacajeroPideAutorizacion'));
						$arrRespuesta['autorizacion']       = 'S';

					} else {

						$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciadescuentoNoRequiereAutorizacion'));
						$arrRespuesta['autorizacion']       = 'NR';

					}
				}

				$oDiferencia->setIdMotivoDiferencia($em->getRepository('RebsolHermesBundle:MotivoDiferencia')->find($motivoDiferencia));

				$em->persist($oDiferencia);
				$em->flush();

				$arrRespuesta['id']  = $oDiferencia->getId();
				$this->setSession('idDiferencia', $arrRespuesta['id']);
				return new Response(json_encode($arrRespuesta));

			}

			public function solicitarDiferenciaSaldoAction(){

			///////////GLOSA////////
			/////NR: no requiere, //S: solicitada
			///

				$em                   = $this->getDoctrine()->getManager();
				$oUser                = $this->getUser();
				$tipoDiferencia       = intval($this->ajax('tipoDiferenciaGlobal'));
				$motivoDiferencia     = $this->ajax('motivoDiferencia');
				$subTotal             = $this->ajax('subTotal');
				$DifTotal             = $this->ajax('DifTotal');
				$FullTotal            = $this->ajax('FullTotal');
				$oFecha               = new \DateTime("now");
				$arrRespuesta         = array();

				if(!isset($como) && !isset($agrupacion)){
					$this->setSession('diferenciaSaldo', 1);
				}else{
					$this->setSession('diferenciaSaldo', 0);
				}

				$oDiferencia          = new Diferencia();

				$oDiferencia->setFechaSolicitud($oFecha);
				$oDiferencia->setTotalCuenta($subTotal);
				$oDiferencia->setTotalDescuento($DifTotal);
				$oDiferencia->setTotalCuentaConDescuento($FullTotal);
				$oDiferencia->setIdUsuarioSolicitud($oUser);

				if($tipoDiferencia == 1){
					$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciadescuentoNoRequiereAutorizacion'));
					$arrRespuesta['autorizacion']       = 'NR';
				}
				if($tipoDiferencia == 2){
					$montoMaximoDiferencia = $this->rParametro()->obtenerParametro('MONTO_MAXIMO_DIFERENCIA')['valor'];
					if($DifTotal > $montoMaximoDiferencia){
						$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciacajeroPideAutorizacion'));
						$arrRespuesta['autorizacion']       = 'S';
					}else{
						$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciadescuentoNoRequiereAutorizacion'));
						$arrRespuesta['autorizacion']       = 'NR';
					}
				}

				$oDiferencia->setIdMotivoDiferencia($em->getRepository('RebsolHermesBundle:MotivoDiferencia')
					->find($motivoDiferencia));
				$em->persist($oDiferencia);
				$em->flush();

				$arrRespuesta['id']  = $oDiferencia->getId();
				$this->setSession('idDiferenciaSaldo', $arrRespuesta['id']);
				return new Response(json_encode($arrRespuesta));

			}

			public function anularDiferenciaAction(){

				$em                   = $this->getDoctrine()->getManager();
				$oUser                = $this->getUser();
				$idDiferencia         = $this->ajax('idDiferencia');

				$oFecha               = new \DateTime("now");
				$arrRespuesta         = array();
				$oDiferencia          = $em->getRepository('RebsolHermesBundle:Diferencia')->find($idDiferencia);

				$oDiferencia->setFechaAnulacion($oFecha);
				$oDiferencia->setIdUsuarioAnulacion($oUser);
				$oDiferencia->setIdEstadoDiferencia($this->estado('DiferenciacajeroCancelaSolicitud'));

				$em->persist($oDiferencia);
				$em->flush();
				$this->killSession('idDiferencia');

				return new Response(json_encode(true));

			}

			public function respuestaSupervisorDiferenciaAction(){

				$em           = $this->getDoctrine()->getManager();

				if($this->getSession('diferenciaSaldo') == 1){

					$idDiferenciaTemp = $this->getSession('idDiferenciaSaldo');

				}else if($this->getSession('diferenciaSaldo') == 0){

					$idDiferenciaTemp = $this->getSession('idDiferencia');

				}


				$oDiferencia  = $em->getRepository('RebsolHermesBundle:Diferencia')->find($idDiferenciaTemp);
				$estado       = $oDiferencia->getIdEstadoDiferencia()->getId();

				if($estado !== $this->container->getParameter('EstadoDiferencia.cajeroPideAutorizacion')){

					$resultadoEstado  = $estado;

				}else{

					$resultadoEstado  = 'no';

				}
				return new Response(json_encode($resultadoEstado));
			}

		}

