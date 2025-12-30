<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Servicios;

use Rebsol\RecaudacionBundle\Controller\RecaudacionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Rebsol\HermesBundle\Controller\Caja\CajaController;

use Rebsol\CajaBundle\Controller\CajaController;


/**
 * @author sthomson
 * @version 1.0.0
 * Fecha Creación: 21/08/2013
 * Participantes:
 *
 */
class BuscarInsumosController extends RecaudacionController {

	/**
	 * @return Response Json
	 * Descripción: obtenerPrestacionJsonAction() Obtiene los datos de una prestación
	 */
	public function obtenerInsumosJsonAction() {
		$em               = $this->getDoctrine()->getManager();
		$fService = $this->get('Caja_valida');
		$idInsumo = $this->container->get('request_stack')->getCurrentRequest()->query->get('idInsumo');
        $idPacientePagoCuenta = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPaciente');
		$ArrTalonarios = $this->get('session')->get('idTalonario');

								// if($fService->SubEmpresaA($idInsumo, $ArrTalonarios)){

		$oArticuloAux = $em->getRepository('RebsolHermesBundle:Articulo')->find($idInsumo);

		if($this->getsession('idSubEmpresaItem') == null or $this->getsession('idSubEmpresaItem') == $oArticuloAux->getIdSubEmpresa()->getId()){

											// if($this->getsession('idSubEmpresaItem') == null){
											//   $this->setsession('idSubEmpresaItem', $oArticuloAux->getIdSubEmpresa()->getId());
											// }
            $this->setsession('idSubEmpresaItem', $oArticuloAux->getIdSubEmpresa()->getId());

			$em = $this->getDoctrine()->getManager();
			$idPrevision = $this->container->get('request_stack')->getCurrentRequest()->query->get('prevision');
			$sucursal = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
			$fechahoy = new \DateTime();
			$fechahoy = $fechahoy->format("Y-m-d H:i:s");
			$oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
			$oRelSucursalPrevision = $em->getRepository('RebsolHermesBundle:RelSucursalPrevision')
			->findOneBy(
				array(
					'idSucursal'  => $sucursal,
					'idPrevision' => $idPrevision,
					'idEstado'    => $this->container->getParameter('estado_activo')
					));
            $bPagoCuenta = $this->container->get('request_stack')->getCurrentRequest()->query->get('bPagoCuenta');
            $idArticuloPaciente = $this->container->get('request_stack')->getCurrentRequest()->query->get('idArticuloPaciente');
			$aArticulo = $fService->ObtenerInsumo($idInsumo, $fechahoy, $oEstadoAct, $oRelSucursalPrevision, $bPagoCuenta, $idPacientePagoCuenta, $idArticuloPaciente);
			if(!$aArticulo){
				$valorNulo = "0";
				$oArticulo = $em->getRepository('RebsolHermesBundle:Articulo')->find($idInsumo);
				$aArticulo = array(
					'id'            => $oArticulo->getId(),
					'id'            => $oArticulo->getCodigo(),
					'nombre'        => $oArticulo->getNombre(),
					'codigo'        => $oArticulo->getCodigo(),
					'precio' => $valorNulo
					);
			}
			return new Response(json_encode($aArticulo));
		} else {

			return new Response("nosubempresa");

		}


	}

}
