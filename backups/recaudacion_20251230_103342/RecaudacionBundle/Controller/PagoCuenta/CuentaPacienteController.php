<?php

namespace Rebsol\RecaudacionBundle\Controller\PagoCuenta;


use Rebsol\HermesBundle\Entity\Caja;
use Rebsol\RecaudacionBundle\Form\Type\Recaudacion\Pago\CerrarCajaType;
use Rebsol\RecaudacionBundle\Form\Type\Recaudacion\Pago\PrestacionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class  CuentaPacienteController
 * @package  \Rebsol\CajaBundle\Controller\Api\Unab\PagoCuenta
 * @author   Nombre del Autor
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  04/12/15  ]
 * Fecha de Actualización: [ ]
 */
class CuentaPacienteController extends PagoCuentaController {


	public function mostrarCuentaAction() {
        $this->em = $this->getDoctrine()->getManager();
		$datosCliente = array();
		$idPaciente   = $this->container->get('request_stack')->getCurrentRequest()->get('idPaciente');
        $idPnatural   = $this->container->get('request_stack')->getCurrentRequest()->get('idPnatural');
        if ($idPnatural === null) {
            $idPnatural   = $this->get('session')->get('Pnatural');
        }
		if ($idPaciente !== '') {

			$idPnatural    = $this->rCajaCuentaPaciente()->obtenerIdPnatural($idPaciente)[ 'idPnatural' ];
		}

		$datosCuentaPagoPaciente = $this->rCajaCuentaPaciente()->obtenerCuentaPaciente($idPnatural);
		$datosCuentaPagoPacienteTutor = $this->rCajaCuentaPaciente()->obtenerCuentaPacienteTutor($idPnatural);

		return $this->render('RecaudacionBundle:Recaudacion/DatosCuentaPaciente:mostrarDatos.html.twig', array(
			'datosCuentaPagoPaciente' => $datosCuentaPagoPaciente,
			'datosCuentaPagoPacienteTutor' => $datosCuentaPagoPacienteTutor,
			)
		);
	}

    public function mostrarCuentaNoEncontradoAction() {
        return $this->render('CajaBundle:Api/Caja/PagoCuenta:PacienteNoEncontrado.html.twig' );
    }

	public function mostrarFinanciadorPacienteAction() {
        $this->em = $this->getDoctrine()->getManager();
        $idUser = $this->getUser()->getId();
        $sucursalUsuario = $this->em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerIdSucursalPorIdUsuario($idUser);
        $prestacionform = $this->createForm(PrestacionType::class, null, array(
            'validaform' => null,
            'iEmpresa' => $this->getSession('idEmpresaLogin'),
            'estado_activado' =>  $this->obtenerServicioGlobales()->obtenerEstado('EstadoActivo'),
            'sucursal' => $sucursalUsuario['id'],
            'database_default' => $this->obtenerEntityManagerDefault()
        ));

        return $this->render('RecaudacionBundle:Api/DatosCuentaPaciente:DatosFinanciador.html.twig', array(
                'IdFinanciador' => null,
                'prestacion_form' => $prestacionform->createView(),
            )
        );
    }

	protected function rCuentaPaciente(){
		return $this->get('cuentaPaciente.cuentaPaciente');
	}

	protected function rCajaCuentaPaciente(){
		return $this->get('recaudacion.CuentaPaciente');
	}

	public function insertarReguardoFinancieroAction() {

		$arrGarantias = null;

		$arrAdmision       = $this->rCuentaPaciente()->obtieneDatosAdmisionPorId($this->container->get('request_stack')->getCurrentRequest()->get('idDatoIngreso'));

		if(!empty($arrAdmision['arrPagoCuenta'])) {

			foreach( $arrAdmision['arrPagoCuenta'] as $arrPagoCuenta ) {

				$arrIdPagoCuenta[ $arrPagoCuenta['id'] ]  = array(
					'id' => $arrPagoCuenta['id']
					);
			}

			$arrGarantias =  $this->rCuentaPaciente()->obtieneGarantiasCuentaPaciente($arrIdPagoCuenta);
		}

		return $this->render('CuentaPacienteBundle:_Default/CuentaPaciente/DetalleCuentaPaciente:ResguardoFinanciero.html.twig', array(
			'datosGarantia'                => $arrGarantias,
			));
	}

	/**
	 * [insertarListadoPagoCuentaPorPacienteAction description]
	 * @param  Request $request [description]
	 * @return view           [description]
	 */
	public function insertarListadoPagoCuentaPorPacienteAction(Request $request) {

		$arrayParameters               = array();
		$idPaciente                    = $request->request->get('idPaciente');
		$arrayParameters['idPaciente'] = $idPaciente;

		$listadoPagoCuentaPorPaciente = $this->rCajaCuentaPaciente()->obtenerListadoPagoCuentaPorPaciente($arrayParameters);


		return $this->render('CajaBundle:Api/Unab/DatosCuentaPaciente:listadoPagoCuentaPorPaciente.html.twig', array(
			'listadoPagoCuentaPorPaciente'                => $listadoPagoCuentaPorPaciente,
			)
		);

	}

	public function gestionAbrirCajaFarmaciaAction() {

		$em               = $this->getDoctrine()->getManager();
		$oUser            = $this->getUser();
		$oSucursal        = $this->rCaja()->GetSucursalAperturaCaja($oUser->getId());

		$oUbicacionCajero = $em->getRepository('RebsolHermesBundle:RelUbicacionCajero')->findOneBy(array(
			"idUsuario" => $oUser,
			"idEstado"  => $this->obtenerServicioGlobales()->obtenerEstado('EstadoActivo')
			));

		$oCaja = new Caja();

		$oCaja->setIdUsuario($oUser);
		$oCaja->setFechaApertura(new \DateTime('now'));
		$oCaja->setMontoInicial($oUbicacionCajero->getmontoInicial());
		$oCaja->setIdSucursal($oSucursal);
		$oCaja->setIdUbicacionCajero($oUbicacionCajero);

		$em->persist($oCaja);

		$em->flush();

		return $this->redirect($this->generateUrl('Caja_PagoCuenta_Inicio'));
	}

	public function gestionCerrarCajaFarmaciaAction(request $request, $id){

		$em              = $this->getDoctrine()->getManager();
		$oCaja           = $em->getRepository("RebsolHermesBundle:Caja")->find($id);
		$oTiposFormaPago = $em->getRepository("RebsolHermesBundle:FormaPagoTipo")->findBy(
			array(
				'idEstado' => $this->obtenerServicioGlobales()->obtenerEstado('EstadoActivo')
				)
			);

		foreach ($oTiposFormaPago as $tipo) {
			$arrayTiposFormasPago[] = $tipo->getid();
		}

		$cerrarCaja = $this->createForm(CerrarCajaType::class, null, array(
			'idFrom' => $arrayTiposFormasPago,
			'estado_activado' => $this->obtenerServicioGlobales()->obtenerEstado('EstadoActivo')
			));



		return $this->render('CajaBundle:Recaudacion\GestionCaja\Form_OpenClose:CloseForm.html.twig', array(
			'form'  => $cerrarCaja->createView(),
			'items' => $this->getArrayItemsCierreCaja($oCaja->getId(), $oTiposFormaPago, $this->obtenerServicioGlobales()->obtenerEstado('EstadoActivo'), $em),
			'caja'  => $oCaja
			));
	}



	private function getArrayItemsCierreCaja($id, $oTiposFormaPago, $oEstadoAct, $em) {

		$arrayTipos         = array();
		$arrayTiposC        = array();
		$arrTipoMontoAux    = array();
		$oEmpresa           = $this->ObtenerEmpresaLogin();

		foreach ($oTiposFormaPago as $tipos) {
			($tipos->getId() == 8) ? $arrayTiposC[$tipos->getid()] = $this->rCaja()->obtenerMontoCierreCaja($id, $tipos->getId(), $this->container->getParameter('EstadoPago.pagadoNormal')) :
			$arrayTipos[$tipos->getid()] = $this->rCaja()->obtenerMontoCierreCaja($id, $tipos->getId(), $this->container->getParameter('EstadoPago.pagadoNormal'));
		}


		/// Array con formas de pago Efectivo (1) y Cheque al día (8)
		foreach ($arrayTipos as $key => $tipos)
		{
			if ($tipos && ($key == 1 || $key == 8))
			{
				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPago")->findOneBy(
					array(
						'idTipoFormaPago' => $key,
						'idEstado'        => $oEstadoAct,
						'idEmpresa'       => $oEmpresa
						)
					);

				$arrTipoMontoAux[$key] = array(
					"suma"   => $tipos,
					"nombre" => "Monto " . $oFormaPago->getnombre(),
					"idtipo" => $key,
					"cc"     => 0
					);
			}
		}


		foreach ($arrayTiposC as $key => $tipos) {

			if ($tipos != NULL) {

				$oFormaPago = $em->getRepository("RebsolHermesBundle:FormaPago")->findOneBy(
					array(
						'idTipoFormaPago' => $key,
						'idEstado'        => $oEstadoAct,
						'idEmpresa'       => $oEmpresa
						)
					);

				$ArrPagosCuentaCorrienteEmpresa = $this->rCaja()->ObtenerItemsPagosCuentaCorrienteEmpresa($oFormaPago->getid(), $id, $oEstadoAct->getId(), $oEmpresa->getId());

				if(!empty($ArrPagosCuentaCorrienteEmpresa)){
					foreach($ArrPagosCuentaCorrienteEmpresa as  $a){

						$monto = (!$a) ?"Monto " . $oFormaPago->getnombre() . " " . $a['banco']:"Monto " . $oFormaPago->getnombre();
						$cc    = (!$a) ? 0 : 1;
						$suma  = (!$a) ? $tipos:$a['montoTotalForma'];

						$arrTipoMontoAux[$key] = array(
							"suma"   => $suma,
							"nombre" => $monto,
							"cc"     => $cc,
							"idtipo" => $key
							);

					}
				}

			}
		}

		return $arrTipoMontoAux;

	}


	protected function rCaja() {
		return $this->getDoctrine()->getRepository('RebsolHermesBundle:Caja');
	}

    public function resultadoBusquedaAvanzadaPacienteCuentaPacienteAction(){

        $postData       = $this->container->get('request_stack')->getCurrentRequest()->get('busquedaAvanzadaPaciente');

        $nombre         = $postData["nombres"];
        $apPaterno      = $postData["apPaterno"];
        $apMaterno      = $postData["apMaterno"];
        $opcionBusqueda = $postData["opcionBusqueda"];


        if($opcionBusqueda == 0){
            $strlike      = "=";
            $strNombres   = $nombre;
            $strApPaterno = $apPaterno;
            $strApMaterno = $apMaterno;
        }

        if($opcionBusqueda == 1){
            $strlike      = "LIKE";
            $strNombres   = $nombre."%";
            $strApPaterno = $apPaterno."%";
            $strApMaterno = $apMaterno."%";
        }

        if($opcionBusqueda == 2){
            $strlike      = "LIKE";
            $strNombres   = "%".$nombre."%";
            $strApPaterno = "%".$apPaterno."%";
            $strApMaterno = "%".$apMaterno."%";
        }

        $oEmpresa                                        = $this->ObtenerEmpresaLogin();

        $datosPersona = $this->get('caja.Persona')->obtenerDatosPersonaCuentaPaciente(
            array(
                'idEmpresa' => $oEmpresa->getId(),
                'nombres' => $strNombres,
                'apPaterno' => $strApPaterno,
                'apMaterno' => $strApMaterno,
                'strlike' => $strlike
            )
        );

        if (is_array($datosPersona) && isset($datosPersona['nombreSocial']) && $datosPersona['nombreSocial'] != null) {
            $datosPersona['nombre'] = $datosPersona['nombre']." (".$datosPersona['nombreSocial'].")";
        }
        return new Response(json_encode($datosPersona));
    }

}
