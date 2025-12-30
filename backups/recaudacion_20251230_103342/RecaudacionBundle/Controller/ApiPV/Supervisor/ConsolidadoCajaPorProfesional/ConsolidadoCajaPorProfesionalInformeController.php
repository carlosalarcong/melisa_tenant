<?php

namespace Rebsol\RecaudacionBundle\Controller\ApiPV\Supervisor\ConsolidadoCajaPorProfesional;

use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\Response;

class ConsolidadoCajaPorProfesionalInformeController extends SupervisorController
{

	public function informeAction()
	{
		$fechaIngresada = $this->container->get('request_stack')->getCurrentRequest()->query->get('fechaIngresada');
		$Sucursal = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
		$this->get('session')->set('fecha', $fechaIngresada);
		$this->get('session')->set('sucursal', $Sucursal);

		return new Response("ok");
    }
    
    public function informerAction()
	{
        //Obtenemos la fecha ingresada y la sucursal mediante sesión.
		$fechaIngresada = $this->get('session')->get('fecha');
		$Sucursal       = $this->get('session')->get('sucursal');
		$em             = $this->getDoctrine()->getManager();

		//En caso de que no exista sucursal, tipo documento, ubicación caja, ubicación cajero y usuarios rebsol, enviamos mensajes de error.
		$eSucursal        = '';
		$eTipoDocumento   = '';
		$eUbicacionCaja   = '';
		$eUbicacionCajero = '';
		$eUsuariosRebsol  = '';

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		//Formateamos la fecha
		$fechaAjaxReformat = new \DateTime(date("Y-m-d", strtotime($fechaIngresada)));
		$Fecha             = $fechaAjaxReformat->format("Y-m-d");

		//Repositorio para obtener todas las cajas abiertas, reabiertas y cerradas, filtradas por fecha y sucursal.
		$arrCajas = $em->getRepository("RebsolHermesBundle:Caja")->informeCajaIndex($Fecha, $Sucursal);
		$arrProfesionales = array();
		foreach($arrCajas as $caja) {
			$oCaja = $em->getRepository('RebsolHermesBundle:Caja')->find($caja['idCaja']);
			//por cada caja, debo buscar todas las "PagoCuenta" que pertenzcan a esta caja, obtener el detalle de estas pagoCuenta y 
			//ver si forma de pago es pagoProfesional = 1
			$arrPagoCuentasEnCaja = $em->getRepository('RebsolHermesBundle:PagoCuenta')->findBy(['idCaja' => $oCaja]);
			foreach($arrPagoCuentasEnCaja as $oPagoCuenta) {
				$oPaciente = $oPagoCuenta->getIdPaciente();
				$oProfesional = $oPaciente->getIdProfesional();
				if($oProfesional) {
					if(!in_array($oProfesional, $arrProfesionales)) {
						array_push($arrProfesionales, $oProfesional);
					}
				}
			}
		}

		$arrPagosCuentaWeb = $this->getPagoCuentaWebByFecha($Fecha, $Sucursal);
		foreach($arrPagosCuentaWeb as $oPagoCuentaWeb) {
			$oPaciente = $oPagoCuentaWeb->getIdPaciente();
			$oProfesional = $oPaciente->getIdProfesional();
			if($oProfesional) {
				if(!in_array($oProfesional, $arrProfesionales)) {
					array_push($arrProfesionales, $oProfesional);
				}
			}
		}

		$arrProfesionalesFixed = array();

		foreach($arrProfesionales as $oUsuarioRebsol) {
			$oPersona = $oUsuarioRebsol->getIdPersona();
			$oPNatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(['idPersona' => $oPersona]);
			$profesional = [
				'idUR' => $oUsuarioRebsol->getId(),
				'nombres' => $oPNatural->getNombrePnatural(),
				'apellidoPaterno' => $oPNatural->getApellidoPaterno(),
				'apellidoMaterno' => $oPNatural->getApellidoMaterno()
			];
			array_push($arrProfesionalesFixed, $profesional);
		}
		
		return $this->render('RecaudacionBundle:ApiPV/Supervisor/ConsolidadoCajaPorProfesional:informe.html.twig',
			array(
				'arrProfesionales' => $arrProfesionalesFixed,
				'esucursal'        => $eSucursal,
				'eTipoDocumento'   => $eTipoDocumento,
				'eUsuariosRebsol'  => $eUsuariosRebsol,
				'coreApi'          => ($estadoApi === "core") ? 1 : 0
		));
	}
	
	public function getPagoCuentaWebByFecha($fecha, $idSucursal) {
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
			->andWhere('fp.pagoProfesional = 1')
			->setParameter('fechaPago', $fechaOb)
			->setParameter('idSucursal', $idSucursal)
			->setParameter('fechaPagoLast', $nuevafecha->format('Y-m-d'))
            ->getQuery()
            ->getResult()
        ;
	}
}