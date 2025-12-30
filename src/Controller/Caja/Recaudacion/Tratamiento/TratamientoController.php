<?php

namespace App\Controller\Caja\Recaudacion\Tratamiento;

use App\Controller\Caja\RecaudacionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
use Rebsol\HermesBundle\Entity\Tratamiento;
use Rebsol\HermesBundle\Entity\DetalleTratamiento;
// use Rebsol\HermesBundle\Controller\Caja\Recaudacion\RecaudacionController;

//use App\Controller\Caja\RecaudacionController;
//use Rebsol\HermesBundle\Entity\Tratamiento;
//use Rebsol\HermesBundle\Entity\DetalleTratamiento;

 /**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 19/08/2014
 **/
 class TratamientoController extends RecaudacionController {

		/**
		 * [crearTratamientoAction description]
		 */
		public function crearTratamientoAction(){

			$em                 = $this->getDoctrine()->getManager();
			$oUser              = $this->getUser();
			$idPnatural         = $this->ajax('idPersona');
			$prestaciones       = $this->ajax('prestaciones');
			$nombreTratamiento  = $this->ajax('nombreTratamiento');
			$tipoTratamiento    = $this->ajax('tipoTratamiento');
			$oFecha             = new \DateTime("now");
			$intNuevo           = 0;
			$arrRespuesta       = array();

			$oTratamiento       = new Tratamiento();

			$oTratamiento->setNombreTratamiento($nombreTratamiento);
			$oTratamiento->setIdTipoTratamiento($em->getRepository('RebsolHermesBundle:TipoTratamiento')
				->find($tipoTratamiento));
			$oTratamiento->setFechaCreacion($oFecha);
			$oTratamiento->setIdPnatural($em->getRepository('RebsolHermesBundle:Pnatural')
				->find($idPnatural));
			$oTratamiento->setIdEstado($this->estado('EstadoTratamientoEnProceso'));
			$oTratamiento->setIdUsuarioCreacion($oUser);

			$em->persist($oTratamiento);

			$i = 0;
			foreach ($prestaciones as $prestacion){

				${"oDetalleTratamiento".$i} = new DetalleTratamiento();

				${"oDetalleTratamiento".$i}->setCantidadTotal($prestacion['1']);
				${"oDetalleTratamiento".$i}->setCantidadPagada($intNuevo);
				${"oDetalleTratamiento".$i}->setCantidadRealizada($intNuevo);
				${"oDetalleTratamiento".$i}->setIdTratamiento($oTratamiento);
				${"oDetalleTratamiento".$i}->setIdEstado($this->estado('EstadoActivo'));
				${"oDetalleTratamiento".$i}->setIdAccionClinica((intval($prestacion['3']) == 1)?
					$em->getRepository('RebsolHermesBundle:AccionClinica')
					->find((intval($prestacion['0']))):
					NULL);
				${"oDetalleTratamiento".$i}->setIdArticulo((intval($prestacion['3']) == 2)?
					$em->getRepository('RebsolHermesBundle:Articulo')
					->find((intval($prestacion['0']))):
					NULL);
				$em->persist(${"oDetalleTratamiento".$i});
				$i++;
			}

			$em->flush();

			$arrRespuesta['validate']       = true;
			$arrRespuesta['id']  = $oTratamiento->getId();
			$this->setSession('idTratamiento', $arrRespuesta['id']);
			return new Response(json_encode($arrRespuesta));
		}

		public function crearTratamientoBuscarPnatural(){
		}

		public function anularTratamientoAction(Request $request, $id){
			$em                         = $this->getDoctrine()->getManager();
			$oTratamiento               = $em->getRepository('RebsolHermesBundle:Tratamiento')->find($id);
			$oEstadoAnulado             = $this->estado('EstadoTratamientoAnulado');

			$oTratamiento->setIdEstado($oEstadoAnulado);
			$em->persist($oTratamiento);

			$em->flush();

			return new Response(json_encode(1));
		}

		public function glosaTratamientoAction(){
			$em                         = $this->getDoctrine()->getManager();
			$historicoTratamientos      = $this->rPagoCuenta()->GetTratamientoGlosa($this->ajax('id'));
			$oTratamiento               = $em->getRepository('RebsolHermesBundle:Tratamiento')->find($this->ajax('id'));
			$this->setSession('idTratamiento', $this->ajax('id'));
			return $this->render('RecaudacionBundle:Recaudacion/Pago/Pago_Form:TratamientoGlosa.html.twig', array(
				'Tratamiento'           => $historicoTratamientos,
				'nombreTratamiento'     => $oTratamiento->getNombreTratamiento()
				));
		}
		public function getEditartratamientoAction(){
			$em                         = $this->getDoctrine()->getManager();
			$historicoTratamientos      = $this->rPagoCuenta()->GetTratamientoGlosa($this->ajax('id'));
			$oTratamiento               = $em->getRepository('RebsolHermesBundle:Tratamiento')->find($this->ajax('id'));
			$this->setSession('idTratamiento', $this->ajax('id'));
			return new Response(json_encode(array(
				'Tratamiento'           => $historicoTratamientos,
				'nombreTratamiento'     => $oTratamiento->getNombreTratamiento(),
				'tipoTratamiento'     => $oTratamiento->getIdTipoTratamiento()->getId()
				)));
		}
		public function editarTratamientoAction(){

			$em                = $this->getDoctrine()->getManager();
			$oUser             = $this->getUser();
			$prestaciones      = $this->ajax('prestaciones');
			$nombreTratamiento = $this->ajax('nombreTratamiento');
			$idTratamiento     = $this->ajax('idTratamiento');
			$tipoTratamiento   = $this->ajax('tipoTratamiento');
			$oFecha            = new \DateTime("now");
			$intNuevo          = 0;
			$arrRespuesta      = array();
			$arrActuales       = array();
			$arrNuevos         = array();

			$oTratamiento      = $em->getRepository('RebsolHermesBundle:Tratamiento')
			->find($idTratamiento);

			$oTratamiento->setIdTipoTratamiento($em->getRepository('RebsolHermesBundle:TipoTratamiento')
				->find($tipoTratamiento));

			$oTratamiento->setNombreTratamiento($nombreTratamiento);

			$em->persist($oTratamiento);

			$oDetalleTratamientoArr = $em->getRepository('RebsolHermesBundle:DetalleTratamiento')->findBy(array('idTratamiento' => $idTratamiento, 'idEstado'=>$this->estado('EstadoActivo')->getId()));

			foreach ($oDetalleTratamientoArr as $detalles){
				$arrActuales[$detalles->getId()] = $detalles->getId();
			}
			foreach ($prestaciones as $d){
				if($d['4'] != "NOIDDT"){
					$arrNuevos[$d['4']] = $d['4'];
				}
			}

			$i = 0;

			foreach ($prestaciones as $prestacion){

				if($prestacion['4'] == "NOIDDT"){

					${"oDetalleTratamiento".$i} = new DetalleTratamiento();

					${"oDetalleTratamiento".$i}->setCantidadTotal($prestacion['1']);
					${"oDetalleTratamiento".$i}->setCantidadPagada($intNuevo);
					${"oDetalleTratamiento".$i}->setCantidadRealizada($intNuevo);
					${"oDetalleTratamiento".$i}->setIdTratamiento($oTratamiento);
					${"oDetalleTratamiento".$i}->setIdEstado($this->estado('EstadoActivo'));
					${"oDetalleTratamiento".$i}->setIdAccionClinica((intval($prestacion['3']) == 1)?
						$em->getRepository('RebsolHermesBundle:AccionClinica')
						->find((intval($prestacion['0']))):
						NULL);
					${"oDetalleTratamiento".$i}->setIdArticulo((intval($prestacion['3']) == 2)?
						$em->getRepository('RebsolHermesBundle:Articulo')
						->find((intval($prestacion['0']))):
						NULL);
					$em->persist(${"oDetalleTratamiento".$i});

				}else{

					if(in_array($prestacion['4'],$arrActuales)){
						${"oDetalleTratamiento".$i}   = $em->getRepository('RebsolHermesBundle:DetalleTratamiento')
						->find($prestacion['4']);
						${"oDetalleTratamiento".$i}->setCantidadTotal(intval($prestacion['1']));
						$em->persist(${"oDetalleTratamiento".$i});
					}
				}
				$i++;
			}

			$i = 0;
			foreach ($oDetalleTratamientoArr as $prestacion){

				if(!in_array($prestacion->getId(),$arrNuevos)){
					${"oDetalleTratamiento".$i}   = $em->getRepository('RebsolHermesBundle:DetalleTratamiento')
					->find($prestacion->getId());
					${"oDetalleTratamiento".$i}->setIdEstado($this->estado('EstadoInc'));
					$em->persist(${"oDetalleTratamiento".$i});

				}
				$i++;
			}

			$em->flush();
			$arrRespuesta['validate'] = true;
			$arrRespuesta['id']       = $oTratamiento->getId();
			$this->setSession('idTratamiento', $arrRespuesta['id']);
			return new Response(json_encode($arrRespuesta));
		}
	}
