<?php

namespace App\Controller\Caja\Supervisor;

use App\Controller\Caja\RecaudacionController;
use App\Controller\Caja\Supervisor\render;
use Symfony\Component\HttpFoundation\Request;

class SupervisorController extends RecaudacionController {

	public $aOpcionesEstado = array(
		"1" => array('cod' => '1', 'msg' => 'Diferencia Aprobada Satisfactoriamente'),
		"2" => array('cod' => '2', 'msg' => 'Documento Anulado Satisfactoriamente'),
		"3" => array('cod' => '3', 'msg' => 'Documento Habilitado Satisfactoriamente'),
		"4" => array('cod' => '4', 'msg' => 'Diferencia Rechazada Satisfactoriamente'),
		"5" => array('cod' => '5', 'msg' => 'No se puede abrir caja. Existen otras cajas abiertas'),
		"6" => array('cod' => '6', 'msg' => 'Caja Abierta Satisfactoriamente'),
		"7" => array('cod' => '7', 'msg' => 'Caja Cerrada Satisfactoriamente'),
		"8" => array('cod' => '8', 'msg' => 'No se puede cerrar la caja'),
		"9" => array('cod' => '9', 'msg' => 'Caja Reabierta Satisfactoriamente')
		);


	/**
	 * @return render
	 * Descripción: IndexAction() Muetra el listado mantenedores disponibles del módulo de caja
	 */
	public function indexAction(Request $request) {

		//Código para que en el combobox, muestre el primer elemento si sólo existe ese en la base de datos
		$iIdEstado = $this->container->getParameter('estado_activo');
		$oEmpresa  = $this->ObtenerEmpresaLogin();

		$estadoApi = $this->estado('EstadoApi');

		if($estadoApi != 'core'){
			if($estadoApi['rutaApi'] === 'ApiPV'){
				$estadoApi = 'core';
			}
		}

		$em = $this->getDoctrine()->getManager();

		//==========================================================================================================================================
		// VALIDACIÓN DE EXISTENCIA DE DATOS EN LAS TABLAS DE LA BASE DE DATOS, LA CUAL ARROJA MENSAJE DE ADVETENCIA DE NO CUMPLIRSE LA VALIDACIÓN
		//==========================================================================================================================================

		//=========================
		// VALIDACIÓN DE SUCURSAL
		//=========================

		//Obtenemos el objeto de las sucursales por empresa y estado
		$arrSucursales = $em->getRepository("RebsolHermesBundle:Sucursal")->
		findBy(
			array(
				"idEmpresa" => $oEmpresa->getId(),
				"idEstado"  => $iIdEstado
				)
			);

		//Hacemos un conteo de la cantidad de elementos que vienen de la consulta
		$len_sucursales = count($arrSucursales);

		//Si el conteo nos arroja 0, asignamos una variable de error para mostrarlo en la vista con la etiqueta - Sucursal, de lo contrario no hace nada.
		if($len_sucursales === 0){
			$errorSucursal = "- Sucursal";
		}else{
			$errorSucursal = "";
		}

		//Si la cantidad de objetos es mayor a 1, el objeto queda como falso y en el combobox dirá: "Seleccione algo"
		if(count($arrSucursales)>1){
			$oSucursal = 'false';
		}

		//Si es menor a uno, en el combobox mostrará el primer elemento por defecto y a $errorSucursal también le asignamos el mismo mensaje,
		//de lo contrario mostrará el nombre de la sucursal correspondiente.
		else{
			if($arrSucursales == null){
				$oSucursal     = 'false';
				$errorSucursal = "- Sucursal";
			}else{
				$oSucursal = $arrSucursales[0]->getId();
			}
		}


		//==============================
		// VALIDACIÓN DE TIPO DOCUMENTO
		//==============================

		//Obtenemos el objeto de las sucursales por empresa y estado
		$arrTipoDocumento = $em->getRepository("RebsolHermesBundle:RelEmpresaTipoDocumento")->
		findBy(
			array(
				"idEmpresa" => $oEmpresa->getId(),
										//"idEstado"  => $iIdEstado
				)
			);

		//Hacemos un conteo de la cantidad de elementos que vienen de la consulta
		$len_TipoDocumento = count($arrTipoDocumento);

		//Si el conteo nos arroja 0, asignamos una variable de error para mostrarlo en la vista con la etiqueta - Sucursal, de lo contrario no hace nada.
		if($len_TipoDocumento === 0){
			$errorTipoDocumento = "- Tipo Documento";
		}else{
			$errorTipoDocumento = "";
		}

		//Si la cantidad de objetos es mayor a 1, el objeto queda como falso y en el combobox dirá: "Seleccione algo"
		if(count($arrTipoDocumento)>1){
			$TipoDocumento = 'false';
		}

		//Si es menor a uno, en el combobox mostrará el primer elemento por defecto y a $errorSucursal también le asignamos el mismo mensaje,
		//de lo contrario mostrará el nombre de la sucursal correspondiente.
		else{
			if($arrTipoDocumento == null){
				$TipoDocumento     = 'false';
				$errorTipoDocumento = "- Tipo Documento";
			}else{
				$TipoDocumento = $arrTipoDocumento[0]->getId();
			}
		}

		//==============================
		// VALIDACIÓN DE UBICACIÓN CAJA
		//==============================

//        //Obtenemos el objeto de las sucursales por empresa y estado
//        $arrUbicacionCaja = $em->getRepository("RebsolHermesBundle:UbicacionCaja")->
//                         findBy(
//                                 array(
//                                        "idEstado"  => $iIdEstado
//                                      )
//                               );
//
//        //Hacemos un conteo de la cantidad de elementos que vienen de la consulta
//        $len_UbicacionCaja = count($arrUbicacionCaja);
//
//        //Si el conteo nos arroja 0, asignamos una variable de error para mostrarlo en la vista con la etiqueta - Sucursal, de lo contrario no hace nada.
//        if($len_UbicacionCaja === 0){
//            $errorUbicacionCaja = "- Ubicación Caja";
//        }else{
//            $errorUbicacionCaja = "";
//        }
//
//        //Si la cantidad de objetos es mayor a 1, el objeto queda como falso y en el combobox dirá: "Seleccione algo"
//        if(count($arrUbicacionCaja)>1){
//          $UbicacionCaja = 'false';
//        }
//
//        //Si es menor a uno, en el combobox mostrará el primer elemento por defecto y a $errorSucursal también le asignamos el mismo mensaje,
//        //de lo contrario mostrará el nombre de la sucursal correspondiente.
//        else{
//             if($arrUbicacionCaja == null){
//                  $UbicacionCaja     = 'false';
//                  $errorUbicacionCaja = "- Ubicación Caja";
//             }else{
//                 $UbicacionCaja = $arrUbicacionCaja[0]->getId();
//             }
//        }
//
//        //=========================================
//        // VALIDACIÓN DE RELACIÓN UBICACIÓN CAJERO
//        //=========================================
//
//        //Obtenemos el objeto de las sucursales por empresa y estado
//        $arrUbicacionCajero = $em->getRepository("RebsolHermesBundle:RelUbicacionCajero")->
//                         findBy(
//                                 array(
//                                        "idEstado"  => $iIdEstado
//                                      )
//                               );
//
//        //Hacemos un conteo de la cantidad de elementos que vienen de la consulta
//        $len_UbicacionCajero = count($arrUbicacionCajero);
//
//        //Si el conteo nos arroja 0, asignamos una variable de error para mostrarlo en la vista con la etiqueta - Sucursal, de lo contrario no hace nada.
//        if($len_UbicacionCajero === 0){
//            $errorUbicacionCajero = "- Relación Ubicación Cajero";
//        }else{
//            $errorUbicacionCajero = "";
//        }
//
//        //Si la cantidad de objetos es mayor a 1, el objeto queda como falso y en el combobox dirá: "Seleccione algo"
//        if(count($arrUbicacionCajero)>1){
//          $UbicacionCajero = 'false';
//        }
//
//        //Si es menor a uno, en el combobox mostrará el primer elemento por defecto y a $errorSucursal también le asignamos el mismo mensaje,
//        //de lo contrario mostrará el nombre de la sucursal correspondiente.
//        else{
//             if($arrUbicacionCajero == null){
//                  $UbicacionCajero     = 'false';
//                  $errorUbicacionCajero = "- Relación Ubicación Cajero";
//             }else{
//                 $UbicacionCajero = $arrUbicacionCajero[0]->getId();
//             }
//        }

		//===============================
		// VALIDACIÓN DE USUARIOS REBSOL
		//===============================

		//Obtenemos el objeto de las sucursales por empresa y estado
		$arrUsuariosRebsol = $em->getRepository("RebsolHermesBundle:UsuariosRebsol")->
		findBy(
			array(
				"idEstadoUsuario"  => $iIdEstado
				)
			);

		//Hacemos un conteo de la cantidad de elementos que vienen de la consulta
		$len_UsuariosRebsol = count($arrUsuariosRebsol);

		//Si el conteo nos arroja 0, asignamos una variable de error para mostrarlo en la vista con la etiqueta - Sucursal, de lo contrario no hace nada.
		if($len_UsuariosRebsol === 0){
			$errorUsuariosRebsol = "- Usuarios Rebsol";
		}else{
			$errorUsuariosRebsol = "";
		}

		//Si la cantidad de objetos es mayor a 1, el objeto queda como falso y en el combobox dirá: "Seleccione algo"
		if(count($arrUsuariosRebsol)>1){
			$UsuariosRebsol = 'false';
		}

		//Si es menor a uno, en el combobox mostrará el primer elemento por defecto y a $errorSucursal también le asignamos el mismo mensaje,
		//de lo contrario mostrará el nombre de la sucursal correspondiente.
		else{
			if($arrUsuariosRebsol == null){
				$UsuariosRebsol     = 'false';
				$errorUsuariosRebsol = "- Usuarios Rebsol";
			}else{
				$UsuariosRebsol = $arrUsuariosRebsol[0]->getId();
			}
		}

		return $this->render('RecaudacionBundle:Supervisor:index.html.twig',
			array(
				'esucursal'        => $errorSucursal,
				'eTipoDocumento'   => $errorTipoDocumento,
				'coreApi'          => ($estadoApi === "core")?1:0,
					   //'eUbicacionCaja'   => $errorUbicacionCaja,
					   //'eUbicacionCajero' => $errorUbicacionCajero,
				'eUsuariosRebsol'  => $errorUsuariosRebsol
				));
	}

	/**
	 * @param Request $request.
	 * @param String $router Módulo desde el que se está haciendo la petición.
	 * @return |RedirectResponse()
	 * Descripción: ValidadPeticionAjax() Valida que la petición se esté realizando por AJAX.
	 */
	public function obtenerAuditoriaDeEntidadAction($oEntidad, $iEntidadId = null) {
		if (!$oEntidad) {
			return false;
		}
		if ($iEntidadId !== null) {
			$em = $this->getDoctrine();
			$entity = $em->getRepository('RebsolHermesBundle:' . $oEntidad . '')->find($iEntidadId);
			$oEntidad = $entity;
		}

		$em = $this->getDoctrine()->getManager();
		$repoLogEntry = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
		$repoEntity   = $repoLogEntry->getLogEntries($oEntidad);
		$arrAuditoria = array();
		// Obtener la auditoría de Creación
		$arrAuditoria['creado'] = null;
		foreach ($repoEntity as $value) {
			if($value->getAction() == 'create'){
				$arrAuditoria['creado']['username'] = $value->getUsername();
				$arrAuditoria['creado']['fecha'] = $value->getLoggedAt()->format('d-m-Y H:i:s');
				break;
			}
		}
		if(!$arrAuditoria['creado']){
			$oUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->find($this->container->getParameter('usuario_default_creaciones'));
			$arrAuditoria['creado']['username'] = $oUsuario->getUsername();
			$arrAuditoria['creado']['fecha'] = $this->container->getParameter('fecha_default_creaciones');
		}
		// Obtener la auditoría de modificación
		$arrAuditoria['modificado'] = null;
		foreach ($repoEntity as $value) {
			if($value->getAction() == 'update' AND $value->getUsername() != NULL){
				$arrAuditoria['modificado']['username'] = $value->getUsername();
				$arrAuditoria['modificado']['fecha'] = $value->getLoggedAt()->format('d-m-Y H:i:s');
				break;
			}
		}
		if(!$arrAuditoria['modificado']){
			$arrAuditoria['modificado'] = false;
		}
		return $this->render('RecaudacionBundle:Supervisor:auditoria.html.twig', array('arrAuditoria' => $arrAuditoria));

	}

	/**
	 * @param Request $request.
	 * @param String $router Módulo desde el que se está haciendo la petición.
	 * @return |RedirectResponse()
	 * Descripción: ValidadPeticionAjax() Valida que la petición se esté realizando por AJAX.
	 */
	public function obtenerAuditoriaDeDiferenciasAction($oEntidad) {
		$em = $this->getDoctrine()->getManager();
		$arrAuditoria                       = array();
		$arrAuditoria['solicitud']          = null;
		$arrAuditoria['anulacion']          = null;
		$arrAuditoria['autorizacion']       = null;
		$arrAuditoria['sinautorizacion']    = null;
		$estadoDiferencia                   = $oEntidad->getidEstadoDiferencia()->getId();
		$fechaSolicitud                     = $oEntidad->getFechaSolicitud();
		$fechaAnulacion                     = $oEntidad->getFechaAnulacion();
		$fechaAutorizacion                  = $oEntidad->getFechaAutorizacion();

		if($fechaSolicitud != null){
			$arrAux = array();
			$arrAux['nombre'] = $oEntidad->getIdUsuarioSolicitud()->getNombreUsuario();
			$arrAux['fecha']  = $oEntidad->getFechaSolicitud();
			$arrAux['titulo'] = 'Solicitado por';
			$arrAuditoria['solicitud'] = $arrAux;
		}

		if($fechaAnulacion != null){
			$arrAux = array();
			$arrAux['nombre'] = $oEntidad->getIdUsuarioAnulacion()->getNombreUsuario();
			$arrAux['fecha']  = $oEntidad->getFechaAnulacion();
			$arrAux['titulo'] = 'Rechazado por';
			$arrAuditoria['anulacion'] = $arrAux;
		}

		if($fechaAutorizacion != null){
			$arrAux = array();
			$arrAux['nombre'] = $oEntidad->getIdUsuarioAutorizacion()->getNombreUsuario();
			$arrAux['fecha']  = $oEntidad->getFechaAutorizacion();
			$arrAux['titulo'] = 'Aprobado por';
			$arrAuditoria['autorizacion'] = $arrAux;
		}

		if($fechaSolicitud != null && $estadoDiferencia == $this->container->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion')){
			$arrAux = array();
			$arrAux['nombre'] = $oEntidad->getIdUsuarioSolicitud()->getNombreUsuario();
			$arrAux['fecha']  = $oEntidad->getFechaSolicitud();
			$arrAux['titulo'] = 'Generada por';
			$arrAuditoria['sinautorizacion'] = $arrAux;
		}

		return $this->render('RecaudacionBundle:Supervisor/AutorizacionDescuentos:auditoria.html.twig', array('arrAuditoria' => $arrAuditoria));
	}

	/**
	 * @param Request $request.
	 * @param String $router Módulo desde el que se está haciendo la petición.
	 * @return |RedirectResponse()
	 * Descripción: ValidadPeticionAjax() Valida que la petición se esté realizando por AJAX.
	 */

	public function obtenerAuditoriaDeFoliosAction($oEntidad) {

		$em = $this->getDoctrine()->getManager();

		$fechaAnulacion = $oEntidad[0]->getFechaAnulacion();


		$arrAuditoria['anulacion'] = null;

		if($fechaAnulacion != null){
			$arrAux = array();
			$arrAux['nombre'] = $oEntidad[0]->getIdUsuarioAnulacion()->getNombreUsuario();
			$arrAux['fecha']  = $oEntidad[0]->getFechaAnulacion();
			$arrAuditoria['anulacion'] = $arrAux;
		}

		return $this->render('RecaudacionBundle:Supervisor/MantenedorFolios:auditoria.html.twig', array('arrAuditoria' => $arrAuditoria));
	}
}

