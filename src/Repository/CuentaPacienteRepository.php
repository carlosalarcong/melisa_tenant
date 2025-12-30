<?php

namespace App\Repository;

use Rebsol\ComercialBundle\Repository\DefaultRepository;


/**
 * Class  CuentaPacienteRepository
 * @package  \Rebsol\RecaudacionBundle\Repository
 * @author   Nombre del Autor
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  05/12/15  ]
 * Fecha de Actualización: [ ]
 */
class  CuentaPacienteRepository extends DefaultRepository {

	/**
	 * [obtenerCuentaPaciente description]
	 * @param  integer $idPaciente [description]
	 * @return array             [description]
	 */
	public function obtenerCuentaPaciente($idPnatural, $idCuentaPaciente = null) {

		$strinCuentaPacientegDql = 'SELECT
		cuentaPaciente.id as idCuentaPaciente,
		paciente.id AS idPaciente,
		
		pc.id  AS idPagoCuenta,
		pc.monto AS saldoCuenta,
		pc.monto AS totalCuenta,
		
		datoIngreso.id AS idDatoIngreso,
		datoIngreso.fechaIngreso AS fechaIngreso,
		ei.id AS idEstadoIngreso,
		
		cuentaPaciente.totalCuentaPaquetizado,
		
		estadoCuenta.id AS idEstadoCuenta,
		estadoCuenta.nombre AS nombreEstadoCuenta,
		
		estadoPago.id AS idEstadoPagoCuenta,
		estadoPago.nombreEstadoPago AS nombreEstadoPago,
		
		tutor.nombrePnatural AS nombreTutor,
		tutor.apellidoPaterno AS apellidoPaternoTutor,
		tutor.apellidoMaterno AS apellidoMaternoTutor,
		tutorPersona.identificacionExtranjero AS rutTutor,
		
		tipoPagoCuenta.nombre as nombreTipoCuenta,
		tipoPagoCuenta.id as idTipoCuenta,
		
		pcd.id, 
		pcd.urlDte, 
		pcd.dataPendiente, 
		pcd.urlProdDte,
		est.id as idEstadoDetalleTalonario,
		c.id as idCaja

		FROM RebsolHermesBundle:Paciente paciente
		LEFT JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
		LEFT JOIN RebsolHermesBundle:DatoIngreso datoIngreso  WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
		JOIN cuentaPaciente.idEstadoCuenta estadoCuenta
		LEFT JOIN paciente.idTutor tutor
		LEFT JOIN tutor.idPersona tutorPersona
		LEFT JOIN RebsolHermesBundle:PagoCuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
		LEFT JOIN pc.idCaja c
		LEFT JOIN RebsolHermesBundle:PagoCuentaDetalle pcd WITH pcd.idPagoCuenta = pc.id
		JOIN pc.idEstadoPago estadoPago
		LEFT JOIN datoIngreso.idTipoCuenta tipoPagoCuenta
		JOIN datoIngreso.idEstadoIngreso ei   
					LEFT JOIN Rebsol\HermesBundle\Entity\DetalleTalonario dt
			WITH paciente.id = dt.idPaciente
			LEFT JOIN dt.idEstadoDetalleTalonario est
		WHERE paciente.idPnatural   = :idPnatural
		AND paciente.idEmpresa      = :idEmpresa';

		if($idCuentaPaciente){
            $strinCuentaPacientegDql .= ' AND cuentaPaciente.id      = :idCuentaPaciente';
        }
		/*
		 * cuentaPaciente.totalCuenta AS totalCuenta,
		cuentaPaciente.saldoCuenta AS saldoCuenta,
		 */

		$query = $this->_em->createQuery($strinCuentaPacientegDql);
		$query->setParameter( 'idPnatural', $idPnatural );
		$query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        if($idCuentaPaciente){
            $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
        }

		$datosCuentaPaciente        = $query->getArrayResult();

        $stringDqlCuentaPacienteLog = 'SELECT paciente.id AS idCuentaPaciente,
		paciente.id AS idPaciente,
		paciente.evento AS evento,
		cuentaPacienteLog.saldoCuenta AS saldoCuentaLog,
		cuentaPacienteLog.fechaEstadoCuenta AS fechaEstadoCuentaLog,
		estadoCuenta.id AS idEstadoCuentaPaciente,
		estadoCuenta.nombre AS nombreEstadoCuentaPaciente,
		paciente.fechaIngreso AS fechaIngreso
		FROM RebsolHermesBundle:Paciente paciente
		JOIN RebsolHermesBundle:CuentaPacienteLog cuentaPacienteLog  WITH ( paciente.id = cuentaPacienteLog.idPaciente )
		JOIN cuentaPacienteLog.idEstadoCuenta estadoCuenta
		WHERE paciente.idPnatural   = :idPnatural
		AND paciente.idEmpresa      = :idEmpresaLog';

		$queryCuentaPacienteLog = $this->_em->createQuery($stringDqlCuentaPacienteLog);
		$queryCuentaPacienteLog->setParameter( 'idPnatural', $idPnatural );
		$queryCuentaPacienteLog->setParameter( 'idEmpresaLog', $this->obtenerParametroSesion('idEmpresaLogin') );
		$datosCuentaPacienteLog = $queryCuentaPacienteLog->getArrayResult();

        $respuestaCuentaPaciente = array();
		foreach ($datosCuentaPacienteLog as $valoresCuentaPacienteLog) {

			foreach ($datosCuentaPaciente as $valoresCuentaPaciente) {
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idCuentaPaciente']              = $valoresCuentaPaciente['idCuentaPaciente'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idPaciente']                    = $valoresCuentaPaciente['idPaciente'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['totalCuenta']                   = $valoresCuentaPaciente['totalCuenta'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['saldoCuenta']                   = $valoresCuentaPaciente['saldoCuenta'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idDatoIngreso']                 = $valoresCuentaPaciente['idDatoIngreso'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoIngreso']                = $valoresCuentaPaciente['idEstadoIngreso'];

				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ][ 'nombreTutor' ]  		  		  = $valoresCuentaPaciente[ 'nombreTutor' ];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ][ 'apellidoPaternoTutor' ] 		  = $valoresCuentaPaciente[ 'apellidoPaternoTutor' ];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ][ 'apellidoMaternoTutor' ] 		  = $valoresCuentaPaciente[ 'apellidoMaternoTutor' ];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ][ 'rutTutor' ]   		  		  = $valoresCuentaPaciente[ 'rutTutor' ];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ][ 'idPagoCuenta' ]   		  	  = $valoresCuentaPaciente[ 'idPagoCuenta' ];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['totalCuentaPaquetizado']        = $valoresCuentaPaciente['totalCuentaPaquetizado'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idTipoCuenta']                  = $valoresCuentaPaciente['idTipoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombreTipoCuenta']              = $valoresCuentaPaciente['nombreTipoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoPagoCuenta']              = $valoresCuentaPaciente['idEstadoPagoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombreEstadoPago']              = $valoresCuentaPaciente['nombreEstadoPago'];

				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['fechaIngreso']                = $valoresCuentaPaciente['fechaIngreso'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoCuentaPaciente']     = $valoresCuentaPacienteLog['idEstadoCuentaPaciente'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombreEstadoCuentaPaciente'] = $valoresCuentaPacienteLog['nombreEstadoCuentaPaciente'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['fechaEstadoCuentaLog']       = $valoresCuentaPacienteLog['fechaEstadoCuentaLog'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['saldoCuentaLog']             = $valoresCuentaPacienteLog['saldoCuentaLog'];

                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['evento']             = $valoresCuentaPacienteLog['evento'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['urlDte']             = $valoresCuentaPaciente['urlDte'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['dataPendiente']             = $valoresCuentaPaciente['dataPendiente'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['urlProdDte']             = $valoresCuentaPaciente['urlProdDte'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idPago']             = $valoresCuentaPaciente['idPagoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoDetalleTalonario']             = $valoresCuentaPaciente['idEstadoDetalleTalonario'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idCaja']             = $valoresCuentaPaciente['idCaja'];

            }

		}

		if(!empty($respuestaCuentaPaciente)){
			foreach ($respuestaCuentaPaciente as $key => $valoresCuentaLog) {
				$respuestaCuentaPacienteLog[] = $valoresCuentaLog;
			}
		}

		return !empty($respuestaCuentaPacienteLog) ? $respuestaCuentaPacienteLog : NULL;

	}

	/**
	 * [obtenerListadoPagoCuentaPorPaciente description]
	 * @param  array $arrayParameters [description]
	 * @return array                  [description]
	 */
	public function obtenerListadoPagoCuentaPorPaciente( $arrayParameters ) {

		$srtingQuery = 'SELECT pagoCuenta.id AS idPagoCuenta,
		pagoCuenta.monto AS montoPagado,
		pagoCuenta.fechaPago AS fechaPago
		FROM RebsolHermesBundle:PagoCuenta pagoCuenta
		WHERE pagoCuenta.idPaciente = :idPaciente
		';

		$createQuery = $this->_em->createQuery($srtingQuery);
		$createQuery->setParameter('idPaciente', $arrayParameters['idPaciente']);

		return $createQuery->getArrayResult();

	}

	public function obtenerIdPnatural( $idPaciente ) {

		$srtingQuery = 'SELECT pnatural.id AS idPnatural
		FROM RebsolHermesBundle:Paciente paciente
		LEFT JOIN paciente.idPnatural pnatural
		WHERE paciente.id = :idPaciente
		';

		$createQuery = $this->_em->createQuery($srtingQuery);
		$createQuery->setParameter('idPaciente', $idPaciente);

		return $createQuery->getOneOrNullResult();

	}

	public function obtenerCuentaPacienteTutor($idPnatural, $idCuentaPaciente = null) {

		$strinCuentaPacientegDql = 'SELECT
		cuentaPaciente.id as idCuentaPaciente,
		paciente.id AS idPaciente,
		
		pc.id  AS idPagoCuenta,
		pc.monto AS saldoCuenta,
		pc.monto AS totalCuenta,
		
		datoIngreso.id AS idDatoIngreso,
		datoIngreso.fechaIngreso AS fechaIngreso,
		ei.id AS idEstadoIngreso,
		
		cuentaPaciente.totalCuentaPaquetizado,
		
		estadoCuenta.id AS idEstadoCuentaPaciente,
		estadoCuenta.nombre AS nombreEstadoCuentaPaciente,
		
		estadoPago.id AS idEstadoPagoCuenta,
		estadoPago.nombreEstadoPago AS nombreEstadoPago,
		
		persona.identificacionExtranjero AS rutPaciente,
		pnatural.nombrePnatural AS nombrePnatural,
		pnatural.apellidoPaterno AS apellidoPaterno,
		pnatural.apellidoMaterno AS apellidoMaterno,
		pnatural.fechaNacimiento AS fechaNacimiento,

		tipoPagoCuenta.nombre as nombreTipoCuenta,
		tipoPagoCuenta.id as idTipoCuenta,
		est.id as idEstadoDetalleTalonario,
		c.id as idCaja,
		tutor.id as idPnaturalTutor 

		FROM RebsolHermesBundle:Paciente paciente
		JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
		JOIN paciente.idPnatural pnatural
		JOIN pnatural.idPersona persona
		LEFT JOIN RebsolHermesBundle:DatoIngreso datoIngreso  WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
		LEFT JOIN paciente.idTutor tutor
        LEFT JOIN tutor.idPersona tutorPersona
        left join RebsolHermesBundle:PagoCuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
        LEFT JOIN pc.idCaja c
        JOIN pc.idEstadoPago estadoPago
        LEFT JOIN Rebsol\HermesBundle\Entity\DetalleTalonario dt
			WITH paciente.id = dt.idPaciente
		LEFT JOIN dt.idEstadoDetalleTalonario est
        LEFT JOIN datoIngreso.idTipoCuenta tipoPagoCuenta   
		JOIN cuentaPaciente.idEstadoCuenta estadoCuenta
		JOIN datoIngreso.idEstadoIngreso ei   
		WHERE tutor.id   = :idPnatural
		AND paciente.idEmpresa      = :idEmpresa';

        if($idCuentaPaciente){
            $strinCuentaPacientegDql .= ' AND cuentaPaciente.id      = :idCuentaPaciente';
        }

		$query = $this->_em->createQuery($strinCuentaPacientegDql);
		$query->setParameter( 'idPnatural', $idPnatural );
		$query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        if($idCuentaPaciente){
            $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
        }
		$datosCuentaPaciente        = $query->getArrayResult();

		$stringDqlCuentaPacienteLog = 'SELECT paciente.id AS idCuentaPaciente,
		paciente.id AS idPaciente,
		cuentaPacienteLog.saldoCuenta AS saldoCuentaLog,
		cuentaPacienteLog.fechaEstadoCuenta AS fechaEstadoCuentaLog,
		estadoCuenta.id AS idEstadoCuentaPaciente,
		estadoCuenta.nombre AS nombreEstadoCuentaPaciente,
		paciente.fechaIngreso AS fechaIngreso
		FROM RebsolHermesBundle:Paciente paciente
		JOIN RebsolHermesBundle:CuentaPacienteLog cuentaPacienteLog  WITH ( paciente.id = cuentaPacienteLog.idPaciente )
		JOIN cuentaPacienteLog.idEstadoCuenta estadoCuenta
		LEFT JOIN paciente.idTutor tutor
		WHERE tutor.id   = :idPnatural
		AND paciente.idEmpresa      = :idEmpresaLog';

		$queryCuentaPacienteLog = $this->_em->createQuery($stringDqlCuentaPacienteLog);
		$queryCuentaPacienteLog->setParameter( 'idPnatural', $idPnatural );
		$queryCuentaPacienteLog->setParameter( 'idEmpresaLog', $this->obtenerParametroSesion('idEmpresaLogin') );
		$datosCuentaPacienteLog = $queryCuentaPacienteLog->getArrayResult($queryCuentaPacienteLog);

		foreach ($datosCuentaPacienteLog as $valoresCuentaPacienteLog) {

			foreach ($datosCuentaPaciente as $valoresCuentaPaciente) {
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idCuentaPaciente']              = $valoresCuentaPaciente['idCuentaPaciente'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idPaciente']                    = $valoresCuentaPaciente['idPaciente'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['totalCuenta']                   = $valoresCuentaPaciente['totalCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['saldoCuenta']                   = $valoresCuentaPaciente['saldoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idDatoIngreso']                 = $valoresCuentaPaciente['idDatoIngreso'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoIngreso']               = $valoresCuentaPaciente['idEstadoIngreso'];

				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombrePnatural']                = $valoresCuentaPaciente['nombrePnatural'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['apellidoPaterno']               = $valoresCuentaPaciente['apellidoPaterno'];
				$respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['apellidoMaterno']               = $valoresCuentaPaciente['apellidoMaterno'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['rutPaciente']                   = $valoresCuentaPaciente['rutPaciente'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ][ 'idPagoCuenta' ]   		  	  = $valoresCuentaPaciente[ 'idPagoCuenta' ];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['totalCuentaPaquetizado']        = $valoresCuentaPaciente['totalCuentaPaquetizado'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idTipoCuenta']                  = $valoresCuentaPaciente['idTipoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombreTipoCuenta']             = $valoresCuentaPaciente['nombreTipoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['fechaNacimiento']              = $valoresCuentaPaciente['fechaNacimiento'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoPagoCuenta']           = $valoresCuentaPaciente['idEstadoPagoCuenta'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombreEstadoPago']              = $valoresCuentaPaciente['nombreEstadoPago'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoDetalleTalonario']       = $valoresCuentaPaciente['idEstadoDetalleTalonario'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idCaja']                         = $valoresCuentaPaciente['idCaja'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idPnaturalTutor']                = $valoresCuentaPaciente['idPnaturalTutor'];

                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['fechaIngreso']               = $valoresCuentaPacienteLog['fechaIngreso'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['idEstadoCuentaPaciente']     = $valoresCuentaPacienteLog['idEstadoCuentaPaciente'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['nombreEstadoCuentaPaciente'] = $valoresCuentaPacienteLog['nombreEstadoCuentaPaciente'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['fechaEstadoCuentaLog']       = $valoresCuentaPacienteLog['fechaEstadoCuentaLog'];
                $respuestaCuentaPaciente[ $valoresCuentaPaciente['idPagoCuenta'] ]['saldoCuentaLog']             = $valoresCuentaPacienteLog['saldoCuentaLog'];

			}

		}

		if(!empty($respuestaCuentaPaciente)){
			foreach ($respuestaCuentaPaciente as $key => $valoresCuentaLog) {
				$respuestaCuentaPacienteLog[] = $valoresCuentaLog;
			}
		}

		return !empty($respuestaCuentaPacienteLog) ? $respuestaCuentaPacienteLog : NULL;

	}

    public function obtenerCuentaPacienteInforme($param) {
        $fechaDesde = preg_split('[-]', $param['fechaDesde']);
        $fechaHasta = preg_split('[-]', $param['fechaHasta']);

        $idTipoTrasladoVivo      = $this->obtenerParametroYML('TipoTraslado.Vivo');
        $idTipoTrasladoFallecido = $this->obtenerParametroYML('TipoTraslado.Fallecido');
        $idTipoTraslado = array($idTipoTrasladoVivo,$idTipoTrasladoFallecido);

        $param['fechaDesde'] = $fechaDesde[2] .'-'. $fechaDesde[1] .'-'. $fechaDesde[0] . ' 00:00:00';
        $param['fechaHasta'] = $fechaHasta[2] .'-'. $fechaHasta[1] .'-'. $fechaHasta[0] . ' 23:59:59';

        $strinCuentaPacientegDql = 'SELECT
		cuentaPaciente.id as id,
		pagocuenta.id as idPagoCuenta,
		paciente.id AS idPaciente,
		cuentaPaciente.saldoCuenta AS saldoCuenta,
		estadoCuenta.id AS idEstadoCuentaPaciente,
		estadoCuenta.nombre AS nombreEstadoCuentaPaciente,
		datoIngreso.id AS idDatoIngreso,
		datoIngreso.fechaIngreso AS fechaIngreso,
		estadoIngreso.nombre AS descripcionEstadoIngreso,
		tutor.nombrePnatural AS nombreTutor,
		tutor.apellidoPaterno AS apellidoPaternoTutor,
		tutor.apellidoMaterno AS apellidoMaternoTutor,
		tutorPersona.identificacionExtranjero AS rutTutor,
		cuentaPaciente.totalCuentaPaquetizado,
		tipoPagoCuenta.nombre as nombreTipoCuenta,
		tipoPagoCuenta.id as idTipoCuenta,
		tie.id AS idTipoIdentificacionExtranjeroPaciente,
		persona.identificacionExtranjero AS rutPaciente,
		pnatural.nombrePnatural AS nombrePnatural,
		pnatural.apellidoPaterno AS apellidoPaterno,
		pnatural.apellidoMaterno AS apellidoMaterno,
		pnatural.fechaNacimiento AS fechaNacimiento,
		servicio.esHparcial,
		(SELECT TIMESTAMPDIFF(DAY, min(rcp.fechaInicio) , IFNULL(max(rcp.fechaFin), CURRENT_DATE() ))
        FROM RebsolHermesBundle:RelCamaPaciente rcp
        WHERE rcp.idPaciente = paciente.id) AS diasTranscurridos,
        pagocuenta.fechaPago, 
        pagocuenta.esCobranza,
        IF(pagocuenta.idEstadoPago = 1, pagocuenta.monto, 0) AS pagado,
        (SELECT pagocuenta01.monto
        FROM RebsolHermesBundle:PagoCuenta pagocuenta01 
        WHERE pagocuenta01.idEstadoPago = 1 AND pagocuenta01.idCuentaPaciente = cuentaPaciente.id 
        and pagocuenta01.id in (SELECT MIN(pagocuenta02.id)
        FROM  RebsolHermesBundle:PagoCuenta pagocuenta02 
        WHERE pagocuenta02.idEstadoPago = 1 and pagocuenta02.idCuentaPaciente = cuentaPaciente.id )) AS pagoInicial,
        (SELECT sum(pagocuenta03.precioDiferencia)
        FROM RebsolHermesBundle:PagoCuenta pagocuenta03 
        WHERE pagocuenta03.idEstadoPago = 1 AND pagocuenta03.idCuentaPaciente = cuentaPaciente.id 
        and pagocuenta03.id BETWEEN (SELECT MIN(pagocuenta04.id)
        FROM  RebsolHermesBundle:PagoCuenta pagocuenta04 
        WHERE pagocuenta04.idEstadoPago = 1 AND pagocuenta04.idCuentaPaciente = cuentaPaciente.id ) and pagocuenta.id) AS montosPagados,
        (SELECT sum(pagocuenta05.monto)
        FROM RebsolHermesBundle:PagoCuenta pagocuenta05 
        WHERE pagocuenta05.idEstadoPago = 4 AND pagocuenta05.idCuentaPaciente = cuentaPaciente.id 
        and pagocuenta05.id BETWEEN (SELECT MIN(pagocuenta06.id)
        FROM  RebsolHermesBundle:PagoCuenta pagocuenta06 
        WHERE pagocuenta06.idEstadoPago = 4 AND pagocuenta06.idCuentaPaciente = cuentaPaciente.id ) and pagocuenta.id) AS montosPendientes,
        pagocuenta.fechaPago,
        estadoPago.id As  idEstadoPago,
		paciente.fechaIngreso AS fechaIngresoPaciente,
		(SELECT MAX(traslado.fechaTraslado)
		FROM RebsolHermesBundle:Traslado traslado
		WHERE traslado.idPaciente = paciente.id 
		AND traslado.idTipoTraslado in (:idTipoTraslado)) AS fechaAlta
		FROM RebsolHermesBundle:Paciente paciente
		JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
		JOIN paciente.idPnatural pnatural
		JOIN pnatural.idPersona persona
		LEFT JOIN RebsolHermesBundle:TipoIdentificacionExtranjero tie WITH persona.idTipoIdentificacionExtranjero = tie.id
		JOIN RebsolHermesBundle:DatoIngreso datoIngreso  WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
		JOIN cuentaPaciente.idEstadoCuenta estadoCuenta
		LEFT JOIN paciente.idTutor tutor
		LEFT JOIN tutor.idPersona tutorPersona
		LEFT JOIN datoIngreso.idTipoCuenta tipoPagoCuenta
		LEFT JOIN datoIngreso.idEstadoIngreso estadoIngreso 
		left join datoIngreso.idServicio servicio
		inner join RebsolHermesBundle:PagoCuenta pagocuenta WITH pagocuenta.idCuentaPaciente = cuentaPaciente.id
		inner JOIN pagocuenta.idEstadoPago estadoPago
		WHERE paciente.idEmpresa      = :idEmpresa  
		      AND pagocuenta.idEstadoPago <> 2 AND ';

        if ($param['estadoCuenta'] != '' and $param['estadoCuenta'] != 0 ) {
            $strinCuentaPacientegDql .= ' estadoCuenta.id = :estadoCuenta AND ';
        }

        $strinCuentaPacientegDql .= ' pagocuenta.fechaPago >= :fechaDesde AND
		pagocuenta.fechaPago <= :fechaHasta 
		ORDER BY cuentaPaciente.id, pagocuenta.id';

        $query = $this->_em->createQuery($strinCuentaPacientegDql);
        $query->setParameter('fechaDesde' ,$param['fechaDesde'] );
        $query->setParameter('fechaHasta' ,$param['fechaHasta'] );
        $query->setParameter('idTipoTraslado' , $idTipoTraslado );
        if ($param['estadoCuenta'] != '' and $param['estadoCuenta'] != 0  ) {
            $query->setParameter('estadoCuenta' ,$param['estadoCuenta'] );
        }

        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );

        $datosCuentaPaciente        = $query->getArrayResult($query);

        $cuenta = -1;
        $oFechaActual        = new \DateTime();
        foreach ($datosCuentaPaciente as $key =>  $cuentaPaciente) {
            if ($cuenta !== $cuentaPaciente['id']) {
                $cuenta = $cuentaPaciente['id'];

            } else {
                $datosCuentaPaciente[$key]['pagoInicial']= $cuentaPaciente['pagoInicial'];

            }
            $montosPendientes =  $cuentaPaciente['montosPendientes']?:0 ;
            $montosPagados =  $cuentaPaciente['montosPagados']?:0 ;
            $datosCuentaPaciente[$key]['totalCuenta'] = $montosPendientes + $montosPagados;
            $datosCuentaPaciente[$key]['montoPendiente'] = $montosPendientes;

            $datosCuentaPaciente[$key]['diasDespues'] = 0;

            if ($cuentaPaciente['idEstadoPago'] === 4) {
                $dias = $cuentaPaciente['fechaPago']->diff($oFechaActual);
                $datosCuentaPaciente[$key]['diasDespues'] = ($dias->days)+1;
            }

        }

        return !empty($datosCuentaPaciente) ? $datosCuentaPaciente : NULL;
    }

    public function obtenerCuentaPacienteAccionClinica($oPaciente, $idCuentaPaciente) {

        $resultado = array();
        $strinCuentaPacientegDql01 = 'SELECT acp.id as idAccionClinicaPaciente
                                    FROM RebsolHermesBundle:AccionClinicaPaciente acp
                                    LEFT JOIN acp.idPagoCuenta pc
                                    LEFT JOIN pc.idCuentaPaciente cp
                                    LEFT JOIN acp.idPaqueteArticulo pa
                                    WHERE acp.idPaciente = :idPaciente
                                    AND acp.idEstadoAccionClinica NOT IN (2,8,9)';

        if($oPaciente->getIdTipoAtencionFc()->getId() === $this->obtenerParametroYML('TipoAtencion.urgencia')){
            $strinCuentaPacientegDql01 .= ' AND pa.id is NULL';
        }

        $query = $this->_em->createQuery($strinCuentaPacientegDql01);
        $query->setParameter( 'idPaciente', $oPaciente->getId() );

        $datosCuentaPacienteAccionClinica = $query->getArrayResult();

        $strinCuentaPacientegDql02 = 'SELECT acp.id AS idAccionClinicaPaciente, ep.id AS idEstadoPago
                                    FROM RebsolHermesBundle:Paciente paciente
                                    JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
                                    JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
                                    JOIN RebsolHermesBundle:PagoCuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
                                    JOIN RebsolHermesBundle:PrePagoCuentaDetalle ppcd WITH pc.id = ppcd.idPagoCuenta
                                    JOIN ppcd.idAccionClinicaPaciente AS acp
                                    JOIN pc.idEstadoPago ep
                                    WHERE paciente.idPnatural   = :idPnatural
                                    AND paciente.idEmpresa      = :idEmpresa
                                    AND ppcd.idEstado           = :idEstado
                                    AND cuentaPaciente.id       = :idCuentaPaciente
                                    AND pc.idEstadoPago not in (0,2,3)';
        $query = $this->_em->createQuery($strinCuentaPacientegDql02);
        $query->setParameter( 'idPnatural', $oPaciente->getIdPnatural()->getId() );
        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
		$query->setParameter('idEstado', $this->obtenerParametroYML('Estado.activo'));

        $datosCuentaPacienteAccionClinica2 = $query->getArrayResult();

        foreach ($datosCuentaPacienteAccionClinica as $item) {

            $foundidAccionClinicaPaciente = array_search($item['idAccionClinicaPaciente'] , array_column($datosCuentaPacienteAccionClinica2, 'idAccionClinicaPaciente'));
            if (!is_numeric($foundidAccionClinicaPaciente)) {
                $resultado[] = $item['idAccionClinicaPaciente'];
            }
        }

        $bPendienteDePago = false;
        foreach ($datosCuentaPacienteAccionClinica2 as $value) {
            if ($value['idEstadoPago'] === $this->obtenerParametroYML('EstadoPago.pendientePago')) {
                $bPendienteDePago = true;
            }
        }

        return $resultado || $bPendienteDePago;
    }

    public function obtenerCuentaPacienteTutorAccionClinica($oPaciente, $idCuentaPaciente) {

        $resultado = array();

        $strinCuentaPacientegDql01 = 'SELECT acp.id as idAccionClinicaPaciente
                                    FROM RebsolHermesBundle:AccionClinicaPaciente acp
                                    JOIN acp.idPaciente paciente
                                    LEFT JOIN acp.idPagoCuenta pc
                                    LEFT JOIN pc.idCuentaPaciente cp
                                    LEFT JOIN paciente.idTutor tutor 
                                    WHERE  tutor.id   = :idPnatural 
                                    AND acp.idPaciente = :idPaciente
                                    AND acp.idEstadoAccionClinica NOT IN (2,8,9)';

        $query = $this->_em->createQuery($strinCuentaPacientegDql01);
        $query->setParameter( 'idPnatural', $oPaciente->getIdPnatural()->getId() );
        $query->setParameter( 'idPaciente', $oPaciente->getId() );

        $datosCuentaPacienteAccionClinica = $query->getArrayResult();

        $strinCuentaPacientegDql02 = 'SELECT acp.id AS idAccionClinicaPaciente, ep.id AS idEstadoPago
                                    FROM RebsolHermesBundle:Paciente paciente
                                    JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
                                    JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
                                    JOIN RebsolHermesBundle:PagoCuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
                                    JOIN RebsolHermesBundle:PrePagoCuentaDetalle ppcd WITH pc.id = ppcd.idPagoCuenta
                                    JOIN ppcd.idAccionClinicaPaciente AS acp
                                    LEFT JOIN paciente.idTutor tutor
                                    JOIN pc.idEstadoPago ep
                                    WHERE tutor.id              = :idPnatural
                                    AND paciente.idEmpresa      = :idEmpresa
                                    AND cuentaPaciente.id       = :idCuentaPaciente
                                    AND ppcd.idEstado           = :idEstado
                                    AND pc.idEstadoPago not in (0,2,3)';
        $query = $this->_em->createQuery($strinCuentaPacientegDql02);
        $query->setParameter( 'idPnatural', $oPaciente->getIdPnatural()->getId() );
        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
		$query->setParameter('idEstado', $this->obtenerParametroYML('Estado.activo'));

        $datosCuentaPacienteAccionClinica2 = $query->getArrayResult();

        foreach ($datosCuentaPacienteAccionClinica as $item) {

            $foundidAccionClinicaPaciente = array_search($item['idAccionClinicaPaciente'] , array_column($datosCuentaPacienteAccionClinica2, 'idAccionClinicaPaciente'));
            if (!is_numeric($foundidAccionClinicaPaciente)) {
                $resultado[] = $item['idAccionClinicaPaciente'];
            }
        }

        $bPendienteDePago = false;
        foreach ($datosCuentaPacienteAccionClinica2 as $value) {
            if ($value['idEstadoPago'] === $this->obtenerParametroYML('EstadoPago.pendientePago')) {
                $bPendienteDePago = true;
            }
        }

        return $resultado || $bPendienteDePago;
    }

    /* *
    ´* el método obtenerCuentaPacienteTutorAccionClinica
    * se le remplazó el $strinCuentaPacientegDql01 para poder traajar con urgencia
    * */
    public function obtenerCuentaPacienteAccionClinicaAnterior($idPnatural, $idCuentaPaciente) {

        $resultado = array();
        $strinCuentaPacientegDql01 = 'SELECT acp.id as idAccionClinicaPaciente
                                    FROM RebsolHermesBundle:Paciente paciente
                                    JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
                                    JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
                                    JOIN RebsolHermesBundle:Pagocuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
                                    JOIN RebsolHermesBundle:AccionClinicaPaciente acp WITH paciente.id =acp.idPaciente
                                    WHERE paciente.idPnatural   = :idPnatural
                                    AND paciente.idEmpresa      = :idEmpresa
                                    AND cuentaPaciente.id       = :idCuentaPaciente
                                    AND pc.idEstadoPago not in (0,2,3)';
        $query = $this->_em->createQuery($strinCuentaPacientegDql01);
        $query->setParameter( 'idPnatural', $idPnatural );
        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
        
        $datosCuentaPacienteAccionClinica = $query->getArrayResult();

        $strinCuentaPacientegDql02 = 'SELECT acp.id AS idAccionClinicaPaciente, ep.id AS idEstadoPago
                                    FROM RebsolHermesBundle:Paciente paciente
                                    JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
                                    JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
                                    JOIN RebsolHermesBundle:PagoCuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
                                    JOIN RebsolHermesBundle:PrePagoCuentaDetalle ppcd WITH pc.id = ppcd.idPagoCuenta
                                    JOIN ppcd.idAccionClinicaPaciente AS acp
                                    JOIN pc.idEstadoPago ep
                                    WHERE paciente.idPnatural   = :idPnatural
                                    AND paciente.idEmpresa      = :idEmpresa
                                    AND cuentaPaciente.id       = :idCuentaPaciente
                                    AND ppcd.idEstado           = :idEstado
                                    AND pc.idEstadoPago not in (0,2,3)';
        $query = $this->_em->createQuery($strinCuentaPacientegDql02);
        $query->setParameter( 'idPnatural', $idPnatural );
        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
        $query->setParameter('idEstado', $this->obtenerParametroYML('Estado.activo'));

        $datosCuentaPacienteAccionClinica2 = $query->getArrayResult();

        foreach ($datosCuentaPacienteAccionClinica as $item) {

            $foundidAccionClinicaPaciente = array_search($item['idAccionClinicaPaciente'] , array_column($datosCuentaPacienteAccionClinica2, 'idAccionClinicaPaciente'));
            if (!is_numeric($foundidAccionClinicaPaciente)) {
                $resultado[] = $item['idAccionClinicaPaciente'];
            }
        }

        $bPendienteDePago = false;
        foreach ($datosCuentaPacienteAccionClinica2 as $value) {
            if ($value['idEstadoPago'] === $this->obtenerParametroYML('EstadoPago.pendientePago')) {
                $bPendienteDePago = true;
            }
        }

        return $resultado || $bPendienteDePago;
    }
    /* *
    ´* el método obtenerCuentaPacienteTutorAccionClinica
    * se le remplazó el $strinCuentaPacientegDql01 para poder traajar con urgencia
    * */
    public function obtenerCuentaPacienteTutorAccionClinicaAnterior($idPnatural, $idCuentaPaciente) {

        $resultado = array();
        $strinCuentaPacientegDql01 = 'SELECT acp.id as idAccionClinicaPaciente
                                    FROM RebsolHermesBundle:Paciente paciente
                                    JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
                                    JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
                                    JOIN RebsolHermesBundle:Pagocuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
                                    JOIN RebsolHermesBundle:AccionClinicaPaciente acp WITH paciente.id =acp.idPaciente
                                    LEFT JOIN paciente.idTutor tutor
                                    WHERE tutor.id   = :idPnatural
                                    AND paciente.idEmpresa      = :idEmpresa
                                    AND cuentaPaciente.id       = :idCuentaPaciente
                                    AND pc.idEstadoPago not in (0,2,3)';
        $query = $this->_em->createQuery($strinCuentaPacientegDql01);
        $query->setParameter( 'idPnatural', $idPnatural );
        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );

        $datosCuentaPacienteAccionClinica = $query->getArrayResult();

        $strinCuentaPacientegDql02 = 'SELECT acp.id AS idAccionClinicaPaciente, ep.id AS idEstadoPago
                                    FROM RebsolHermesBundle:Paciente paciente
                                    JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
                                    JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
                                    JOIN RebsolHermesBundle:PagoCuenta pc WITH cuentaPaciente.idPaciente = pc.idPaciente
                                    JOIN RebsolHermesBundle:PrePagoCuentaDetalle ppcd WITH pc.id = ppcd.idPagoCuenta
                                    JOIN ppcd.idAccionClinicaPaciente AS acp
                                    LEFT JOIN paciente.idTutor tutor
                                    JOIN pc.idEstadoPago ep
                                    WHERE tutor.id              = :idPnatural
                                    AND paciente.idEmpresa      = :idEmpresa
                                    AND cuentaPaciente.id       = :idCuentaPaciente
                                    AND ppcd.idEstado           = :idEstado
                                    AND pc.idEstadoPago not in (0,2,3)';
        $query = $this->_em->createQuery($strinCuentaPacientegDql02);
        $query->setParameter( 'idPnatural', $idPnatural );
        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );
        $query->setParameter( 'idCuentaPaciente', $idCuentaPaciente );
		$query->setParameter('idEstado', $this->obtenerParametroYML('Estado.activo'));

        $datosCuentaPacienteAccionClinica2 = $query->getArrayResult();

        foreach ($datosCuentaPacienteAccionClinica as $item) {

            $foundidAccionClinicaPaciente = array_search($item['idAccionClinicaPaciente'] , array_column($datosCuentaPacienteAccionClinica2, 'idAccionClinicaPaciente'));
            if (!is_numeric($foundidAccionClinicaPaciente)) {
                $resultado[] = $item['idAccionClinicaPaciente'];
            }
        }

        $bPendienteDePago = false;
        foreach ($datosCuentaPacienteAccionClinica2 as $value) {
            if ($value['idEstadoPago'] === $this->obtenerParametroYML('EstadoPago.pendientePago')) {
                $bPendienteDePago = true;
            }
        }

        return $resultado || $bPendienteDePago;
    }

    public function obtenerEstadoCuentaPacienteInforme($param) {
        $fechaDesde = preg_split('[-]', $param['fechaDesde']);
        $fechaHasta = preg_split('[-]', $param['fechaHasta']);

        $idTipoTrasladoVivo      = $this->obtenerParametroYML('TipoTraslado.Vivo');
        $idTipoTrasladoFallecido = $this->obtenerParametroYML('TipoTraslado.Fallecido');
        $idTipoTraslado = array($idTipoTrasladoVivo,$idTipoTrasladoFallecido);

        $param['fechaDesde'] = $fechaDesde[2] .'-'. $fechaDesde[1] .'-'. $fechaDesde[0] . ' 00:00:00';
        $param['fechaHasta'] = $fechaHasta[2] .'-'. $fechaHasta[1] .'-'. $fechaHasta[0] . ' 23:59:59';

        $strinCuentaPacientegDql = "SELECT
		cuentaPaciente.id as idCuentaPaciente,
		paciente.id AS idPaciente,
		cuentaPaciente.saldoCuenta AS saldoCuenta,
		estadoCuenta.id AS idEstadoCuentaPaciente,
		estadoCuenta.nombre AS nombreEstadoCuentaPaciente,
		datoIngreso.numero AS numeroIngreso,
		datoIngreso.id AS idDatoIngreso,
		datoIngreso.fechaIngreso AS fechaIngreso,
		estadoIngreso.nombre AS descripcionEstadoIngreso,
		cuentaPaciente.totalCuentaPaquetizado,
		tipoPagoCuenta.nombre as nombreTipoCuenta,
		tipoPagoCuenta.id as idTipoCuenta,
		tie.id AS idTipoIdentificacionExtranjeroPaciente,
		persona.identificacionExtranjero AS rutPaciente,
		persona.correoElectronico,
		persona.telefonoMovil,
		pnatural.nombrePnatural AS nombrePnatural,
		pnatural.apellidoPaterno AS apellidoPaterno,
		pnatural.apellidoMaterno AS apellidoMaterno,
		pnatural.fechaNacimiento AS fechaNacimiento,
		financiador.nombrePrevision nombreFinanciador,
		pnatProfesional.nombrePnatural AS nombrePnaturalProfesional,
		pnatProfesional.apellidoPaterno AS apellidoPaternoProfesional,
		pnatProfesional.apellidoMaterno AS apellidoMaternoProfesional,
		(SELECT cmfc.detalleConsultaMedica 
		FROM RebsolHermesBundle:ConsultaMedicaFc cmfc
		WHERE cmfc.idPaciente  = cuentaPaciente.idPaciente AND
		      cmfc.idItemAtencion = 2 AND
		      cmfc.idEstado = 1 AND
		      cmfc.idPrecisionDiagnostica = 1 ) AS diagnosticoPrincipal,
		(SELECT IDENTITY(cmfc1.idPatologia)
		FROM RebsolHermesBundle:ConsultaMedicaFc cmfc1
		WHERE cmfc1.idPaciente  = cuentaPaciente.idPaciente AND
		      cmfc1.idItemAtencion = 2 AND
		      cmfc1.idEstado = 1 AND
		      cmfc1.idPrecisionDiagnostica = 1 ) AS esGes,
		paciente.fechaIngreso AS fechaIngresoPaciente,
		(SELECT date_format(max(traslado.fechaTraslado), '%d/%m/%Y %H:%i')
		FROM RebsolHermesBundle:Traslado traslado
		WHERE traslado.idPaciente = paciente.id
		AND traslado.idTipoTraslado in (:idTipoTraslado)) AS fechaEgreso,
		dicu.fechaAlta AS fechaAlta,
		tipoAtencion.nombreTipoAtencionFc AS nombreTipoAtencion
		
		,ce.nombre AS convenioEmpresa
        ,(SELECT date_format(MIN(relCamaPaciente.fechaInicio), '%d/%m/%Y %H:%i') 
          FROM RebsolHermesBundle:RelCamaPaciente relCamaPaciente
          WHERE relCamaPaciente.idPaciente = paciente.id AND relCamaPaciente.fechaInicio IS NOT NULL) AS fechaConfirmacionBox
 
		
		,(SELECT date_format(min(cmfc2.fechaCreacion), '%d/%m/%Y %H:%i') 
		    FROM RebsolHermesBundle:ConsultaMedicaFc cmfc2
		    WHERE cmfc2.idPaciente = paciente.id
		) AS fechaInicionAtencionConsultaMedicaFc
		
		,(SELECT date_format(min(csu.fechaCreacion), '%d/%m/%Y %H:%i') 
		    FROM RebsolHermesBundle:CategorizacionSimpleUrgencia csu
		    WHERE csu.idPaciente = paciente.id
		) AS fechaInicionCategorizacionSimpleUrgencia,
		
		suc.nombreSucursal as sucursal
		
		FROM RebsolHermesBundle:Paciente paciente
		JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
		JOIN paciente.idPnatural pnatural
		JOIN paciente.idTipoAtencionFc tipoAtencion
		JOIN paciente.idFinanciador financiador
		JOIN pnatural.idPersona persona
		LEFT JOIN RebsolHermesBundle:TipoIdentificacionExtranjero tie WITH persona.idTipoIdentificacionExtranjero = tie.id
		JOIN RebsolHermesBundle:DatoIngreso datoIngreso  WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
		LEFT JOIN RebsolHermesBundle:DatoIngresoComplementoUrgencia dicu WITH ( dicu.idDatoIngreso = datoIngreso.id)
		LEFT JOIN dicu.idConvenioEmpresa ce  
		JOIN cuentaPaciente.idEstadoCuenta estadoCuenta
		LEFT JOIN datoIngreso.idTipoCuenta tipoPagoCuenta
		LEFT JOIN datoIngreso.idEstadoIngreso estadoIngreso
		JOIN datoIngreso.idProfesional profesional
		JOIN profesional.idPersona personaProfesional
		JOIN RebsolHermesBundle:Pnatural pnatProfesional WITH personaProfesional.id = pnatProfesional.idPersona
		JOIN datoIngreso.idSucursal suc 
		
		WHERE paciente.idEmpresa      = :idEmpresa AND
		datoIngreso.fechaIngreso >= :fechaDesde AND
		datoIngreso.fechaIngreso <= :fechaHasta
		ORDER BY cuentaPaciente.id desc";

        $query = $this->_em->createQuery($strinCuentaPacientegDql);
        $query->setParameter('fechaDesde' ,$param['fechaDesde'] );
        $query->setParameter('fechaHasta' ,$param['fechaHasta'] );
        $query->setParameter('idTipoTraslado' , $idTipoTraslado );


        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );

        $datosCuentaPaciente        = $query->getArrayResult();

        if (!empty($datosCuentaPaciente)) {
            $arrPacientes = array();
            foreach ($datosCuentaPaciente as $item) {
                $arrPacientes[] = $item['idPaciente'];
            }
            $strinAccionClinicaPacientegDql = "SELECT
            paciente.id AS idPaciente,
            ac.nombreAccionClinica AS prestacion,
            eac.nombreEstadoAccionClinica AS estadoPrestacion,
            estadoPago.nombreEstadoPago,
            ac.codigoFonasa AS codigoPrestacion,
            pagoCuenta.id As idPagoCuenta,
            (SELECT
            GROUP_CONCAT(fp.nombre SEPARATOR ' / ')
            FROM RebsolHermesBundle:PagoCuenta pc
            LEFT JOIN RebsolHermesBundle:DetallePagoCuenta dpc WITH ( pc.id = dpc.idPagoCuenta )
            LEFT JOIN dpc.idFormaPago fp
            WHERE pc.idPaciente = paciente.id AND pc.id = acp.idPagoCuenta
            GROUP BY pc.id) AS formaPago,
            pagoCuenta.fechaPago as fechaPago,
            acp.precioCobrado,
            acp.precioDiferencia,
            acp.totalDescuento,
            tsd.id AS idTipoSentidoDiferencia,
            '0' AS esArticulo,
            pagoCuenta.montoDiferencia as totalDescuentoGlobal
            FROM RebsolHermesBundle:AccionClinicaPaciente acp
            JOIN acp.idEstadoAccionClinica eac
            JOIN acp.idAccionClinica ac
            JOIN acp.idPaciente paciente
            LEFT JOIN acp.idEstadoPago estadoPago 
            LEFT JOIN acp.idPagoCuenta pagoCuenta
            LEFT JOIN acp.idMotivoDiferencia motd
            LEFT JOIN motd.idTipoSentidoDiferencia tsd
            WHERE acp.idPaciente in (:arrPacientes) AND acp.idEstadoAccionClinica != 2";

            $query2 = $this->_em->createQuery($strinAccionClinicaPacientegDql);
            $query2->setParameter('arrPacientes' ,$arrPacientes);


            $accionClinicaPaciente = $query2->getArrayResult($query2);

            $strinArticuloPacientegDql = "SELECT
            paciente.id AS idPaciente,
            a.nombre AS prestacion,
            eac.nombreEstadoAccionClinica AS estadoPrestacion,
            estadoPago.nombreEstadoPago,
            a.codigo AS codigoPrestacion,
            pagoCuenta.id As idPagoCuenta,
                (SELECT
                    GROUP_CONCAT(fp.nombre SEPARATOR ' / ')
                FROM RebsolHermesBundle:PagoCuenta pc
                LEFT JOIN RebsolHermesBundle:DetallePagoCuenta dpc WITH ( pc.id = dpc.idPagoCuenta )
                LEFT JOIN dpc.idFormaPago fp
                WHERE pc.idPaciente = paciente.id AND pc.id = ap.idPagoCuenta
                GROUP BY pc.id) AS formaPago,
            pagoCuenta.fechaPago as fechaPago,
            ap.precioCobrado,
            '' AS precioDiferencia,
            '0' AS totalDescuento,
            '' AS idTipoSentidoDiferencia,
            '1' AS esArticulo,
            pagoCuenta.montoDiferencia as totalDescuentoGlobal
            
            FROM RebsolHermesBundle:ArticuloPaciente ap
            JOIN ap.idEstadoAccionClinica eac
            JOIN ap.idArticulo a
            JOIN ap.idPaciente paciente
            LEFT JOIN ap.idPagoCuenta pagoCuenta
            LEFT JOIN ap.idEstadoPago estadoPago
            WHERE ap.idPaciente in (:arrPacientes) AND ap.idEstadoAccionClinica != 2";

            $query3 = $this->_em->createQuery($strinArticuloPacientegDql);
            $query3->setParameter('arrPacientes' ,$arrPacientes);

            $articuloPaciente = $query3->getArrayResult($query3);

            $resultado = array_merge($accionClinicaPaciente, $articuloPaciente);

            array_multisort(array_column($resultado, 'idPaciente'), SORT_DESC, $resultado);


            $arrayAux = array();
            $index = 0;
            $bRegistro = false;
            foreach($datosCuentaPaciente as $cuentaPaciente) {
                foreach ($resultado as $prestacion) {
                    if ($cuentaPaciente['idPaciente'] === $prestacion['idPaciente']) {
                        $arrayAux[$index] = array_merge(
                            $cuentaPaciente,
                            array(
                                'prestacion' => $prestacion['prestacion'],
                                'estadoPrestacion' => $prestacion['estadoPrestacion'],
                                'codigoPrestacion' => $prestacion['codigoPrestacion'],
                                'formaPago' => $prestacion['formaPago'],
                                'fechaPago' => $prestacion['fechaPago'],
                                'nombreEstadoPago' => $prestacion['nombreEstadoPago'],
                                'precioCobrado' => $prestacion['precioCobrado'],
                                'precioDiferencia' => $prestacion['precioDiferencia'],
                                'totalDescuento' => $prestacion['totalDescuento'],
                                'idTipoSentidoDiferencia' => $prestacion['idTipoSentidoDiferencia'],
                                'esArticulo' => $prestacion['esArticulo'],
                                'idPagoCuenta' => $prestacion['idPagoCuenta'],
                                'totalDescuentoGlobal' => $prestacion['totalDescuentoGlobal']
                            )
                        );
                        $bRegistro = true;
                        $index++;
                    }
                }
                if ($bRegistro === false) {
                    $arrayAux[$index] = array_merge(
                        $cuentaPaciente,
                        array(
                            'prestacion' => null,
                            'estadoPrestacion' => null,
                            'codigoPrestacion' => null,
                            'fechaPago' => null,
                            'nombreEstadoPago' => null,
                            'precioCobrado' => null,
                            'precioDiferencia' => null,
                            'totalDescuento' => null,
                            'idTipoSentidoDiferencia' => null,
                            'esArticulo' => '0',
                            'idPagoCuenta' => null,
                            'totalDescuentoGlobal' => 0
                        )
                    );
                    $index++;
                }

            }

        }
        return !empty($arrayAux) ? $arrayAux : array();
    }

    public function obtenerEstadoCuentaPacienteInformeCustom($param) {
        $fechaDesde = preg_split('[-]', $param['fechaDesde']);
        $fechaHasta = preg_split('[-]', $param['fechaHasta']);

        $idTipoTrasladoVivo      = $this->obtenerParametroYML('TipoTraslado.Vivo');
        $idTipoTrasladoFallecido = $this->obtenerParametroYML('TipoTraslado.Fallecido');
        $idTipoTraslado = array($idTipoTrasladoVivo,$idTipoTrasladoFallecido);

        $param['fechaDesde'] = $fechaDesde[2] .'-'. $fechaDesde[1] .'-'. $fechaDesde[0] . ' 00:00:00';
        $param['fechaHasta'] = $fechaHasta[2] .'-'. $fechaHasta[1] .'-'. $fechaHasta[0] . ' 23:59:59';

        $strinCuentaPacientegDql = "SELECT
		cuentaPaciente.id as idCuentaPaciente,
		paciente.id AS idPaciente,
		cuentaPaciente.saldoCuenta AS saldoCuenta,
		estadoCuenta.id AS idEstadoCuentaPaciente,
		estadoCuenta.nombre AS nombreEstadoCuentaPaciente,
		datoIngreso.id AS idDatoIngreso,
		datoIngreso.numero AS numeroIngreso,
		datoIngreso.fechaIngreso AS fechaIngreso,
		estadoIngreso.nombre AS descripcionEstadoIngreso,
		cuentaPaciente.totalCuentaPaquetizado,
		tipoPagoCuenta.nombre as nombreTipoCuenta,
		tipoPagoCuenta.id as idTipoCuenta,
		tie.id AS idTipoIdentificacionExtranjeroPaciente,
		persona.identificacionExtranjero AS rutPaciente,
		persona.correoElectronico,
		persona.telefonoMovil,
		pnatural.id AS idPnatural,
		pnatural.nombrePnatural AS nombrePnatural,
		pnatural.apellidoPaterno AS apellidoPaterno,
		pnatural.apellidoMaterno AS apellidoMaterno,
		pnatural.fechaNacimiento AS fechaNacimiento,
		
		IF(financiador.id IS NOT NULL, financiador.nombrePrevision, '-') AS nombreFinanciador,
		IF(convenio.id IS NOT NULL, convenio.nombrePrevision, '-') AS nombreConvenio,
		prPlan.nombre AS nombrePlan,
        
		pnatProfesional.nombrePnatural AS nombrePnaturalProfesional,
		pnatProfesional.apellidoPaterno AS apellidoPaternoProfesional,
		pnatProfesional.apellidoMaterno AS apellidoMaternoProfesional,
		(SELECT cmfc.detalleConsultaMedica 
		FROM RebsolHermesBundle:ConsultaMedicaFc cmfc
		WHERE cmfc.idPaciente  = cuentaPaciente.idPaciente AND
		      cmfc.idItemAtencion = 2 AND
		      cmfc.idEstado = 1 AND
		      cmfc.idPrecisionDiagnostica = 1 ) AS diagnosticoPrincipal,
		(SELECT IDENTITY(cmfc1.idPatologia)
		FROM RebsolHermesBundle:ConsultaMedicaFc cmfc1
		WHERE cmfc1.idPaciente  = cuentaPaciente.idPaciente AND
		      cmfc1.idItemAtencion = 2 AND
		      cmfc1.idEstado = 1 AND
		      cmfc1.idPrecisionDiagnostica = 1 ) AS esGes,
		paciente.fechaIngreso AS fechaIngresoPaciente,
		(SELECT date_format(max(traslado.fechaTraslado), '%d/%m/%Y %H:%i')
		FROM RebsolHermesBundle:Traslado traslado
		WHERE traslado.idPaciente = paciente.id
		AND traslado.idTipoTraslado in (:idTipoTraslado)) AS fechaEgreso,
		tipoAtencion.nombreTipoAtencionFc AS nombreTipoAtencion,
		suc.nombreSucursal as sucursal
		,ppn.id as idPrevisionPnatural
		,dicu.fechaAlta AS fechaAlta 
        ,ce.nombre AS convenioEmpresa
        
        ,(SELECT date_format(relCamaPaciente.fechaInicio, '%d/%m/%Y %H:%i') 
		    FROM RebsolHermesBundle:RelCamaPaciente relCamaPaciente
		    WHERE relCamaPaciente.idPaciente = paciente.id
		    AND ( relCamaPaciente.idEstadoRelCamaPaciente = 1 OR relCamaPaciente.idEstadoRelCamaPaciente = 2 )
		) AS fechaConfirmacionBox
		
		,(SELECT date_format(min(cmfc2.fechaCreacion), '%d/%m/%Y %H:%i') 
		    FROM RebsolHermesBundle:ConsultaMedicaFc cmfc2
		    WHERE cmfc2.idPaciente = paciente.id
		) AS fechaInicionAtencionConsultaMedicaFc
		
		,(SELECT date_format(min(csu.fechaCreacion), '%d/%m/%Y %H:%i') 
		    FROM RebsolHermesBundle:CategorizacionSimpleUrgencia csu
		    WHERE csu.idPaciente = paciente.id
		) AS fechaInicionCategorizacionSimpleUrgencia
		
		FROM RebsolHermesBundle:Paciente paciente
		JOIN RebsolHermesBundle:CuentaPaciente cuentaPaciente WITH ( paciente.id = cuentaPaciente.idPaciente )
		JOIN paciente.idPnatural pnatural
		JOIN paciente.idTipoAtencionFc tipoAtencion
		JOIN pnatural.idPersona persona
		LEFT JOIN RebsolHermesBundle:TipoIdentificacionExtranjero tie WITH persona.idTipoIdentificacionExtranjero = tie.id
		JOIN RebsolHermesBundle:DatoIngreso datoIngreso WITH ( cuentaPaciente.idPaciente = datoIngreso.idPaciente )
		LEFT JOIN RebsolHermesBundle:DatoIngresoComplementoUrgencia dicu WITH ( dicu.idDatoIngreso = datoIngreso.id)
		LEFT JOIN dicu.idConvenioEmpresa ce 
		LEFT JOIN RebsolHermesBundle:PrevisionPnatural ppn WITH ( ppn.idPaciente = paciente.id )
		LEFT JOIN ppn.idPrevision financiador
		LEFT JOIN ppn.idConvenio convenio
		
		JOIN cuentaPaciente.idEstadoCuenta estadoCuenta
		LEFT JOIN datoIngreso.idTipoCuenta tipoPagoCuenta
		LEFT JOIN datoIngreso.idEstadoIngreso estadoIngreso
		JOIN datoIngreso.idProfesional profesional
		JOIN profesional.idPersona personaProfesional
		JOIN RebsolHermesBundle:Pnatural pnatProfesional WITH personaProfesional.id = pnatProfesional.idPersona 
		JOIN datoIngreso.idSucursal suc
		JOIN datoIngreso.idPrPlan prPlan
		WHERE paciente.idEmpresa      = :idEmpresa AND
		datoIngreso.fechaIngreso >= :fechaDesde AND
		datoIngreso.fechaIngreso <= :fechaHasta AND
		ppn.id = (SELECT MIN(ppn2.id) FROM RebsolHermesBundle:PrevisionPnatural ppn2 WHERE ppn2.idPaciente = paciente.id)
		ORDER BY cuentaPaciente.id desc";

        $query = $this->_em->createQuery($strinCuentaPacientegDql);
        $query->setParameter('fechaDesde' ,$param['fechaDesde'] );
        $query->setParameter('fechaHasta' ,$param['fechaHasta'] );
        $query->setParameter('idTipoTraslado' , $idTipoTraslado );


        $query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );

        $datosCuentaPaciente        = $query->getArrayResult();

        if (!empty($datosCuentaPaciente)) {
            $arrPacientes = array();
            foreach ($datosCuentaPaciente as $key => $item) {
                $arrPacientes[$key] = $item['idPaciente'];
            }
            $sqlNoBono = "
            SELECT
            acp.id as idAccionClinicaPaciente,
            acp.totalDescuento as totalDescuento,
            acp.porcentajeDescuento as porcentajeDescuento,
            pc.montoDiferencia as totalDescuentoGlobal,
            '0' as porcentajeDescuentoGlobal,
            md.id as idMotivoDiferencia,
            md.nombre as nombreMotivoDiferencia,
            mdg.nombre as nombreMotivoDiferenciaGlobal,
            tsd.nombre as nombreTipoDiferencia,
            
            paciente.id AS idPaciente,
            ac.nombreAccionClinica AS prestacion,
            eac.nombreEstadoAccionClinica AS estadoPrestacion,
            ac.codigoFonasa AS codigoPrestacion,
            estadoPago.nombreEstadoPago,
            
            dpc.montoPagoCuenta AS montoPagadoPagoCuenta,
            dpc.fechaDetallePago AS fechaPago,
            tipoPrestacion.nombreTipoPrestacion,
            fp.id as idFormaPago,
            fp.nombre AS formaPago,
            mg.nombre AS motivoGratuidad,
            
            dpa.montoTotalDocumento AS montoPagadoDocumentoPago,
            dpa.numeroDocumento as numeroBono,
            dpa.numeroVoucher as numeroVoucher,
            
            dpc.id as idDetallePagoCuenta,
            pc.id as idPagoCuenta,
            acp.precioCobrado as montoPrestacion,
            acp.precioDiferencia as montoPagadoPrestacion,
            acp.cantidad as cantidadPrestacion
            
            FROM RebsolHermesBundle:AccionClinicaPaciente acp
            JOIN acp.idEstadoAccionClinica eac
            JOIN acp.idAccionClinica ac
            JOIN acp.idPaciente paciente
            LEFT JOIN acp.idEstadoPago estadoPago
            LEFT JOIN ac.idTipoPrestacion tipoPrestacion 
            LEFT JOIN RebsolHermesBundle:PagoCuenta pc WITH ( pc.id = acp.idPagoCuenta )
            LEFT JOIN RebsolHermesBundle:DetallePagoCuenta dpc WITH ( pc.id = dpc.idPagoCuenta )
            LEFT JOIN dpc.idFormaPago fp
            LEFT JOIN dpc.idMotivoGratuidad mg
            LEFT JOIN RebsolHermesBundle:DocumentoPago dpa WITH dpc.id = dpa.idDetallePagoCuenta
            LEFT JOIN acp.idMotivoDiferencia md
            LEFT JOIN md.idTipoSentidoDiferencia tsd
            LEFT JOIN pc.idMotivoDiferencia mdg
            
            WHERE acp.idPaciente in (:arrPacientes) AND acp.idEstadoAccionClinica != 2 AND fp.id NOT IN (11,14) 
            
            ORDER BY acp.id ASC
            ";

            $query2 = $this->_em->createQuery($sqlNoBono);
            $query2->setParameter('arrPacientes' ,$arrPacientes);
            $accionClinicaPacienteNoBono = $query2->getArrayResult();

            $sqlBono = "
            SELECT
            acp.id as idAccionClinicaPaciente,
            acp.totalDescuento as totalDescuento,
            acp.porcentajeDescuento as porcentajeDescuento,
            pc.montoDiferencia as totalDescuentoGlobal,
            '0' as porcentajeDescuentoGlobal,
            md.id as idMotivoDiferencia,
            md.nombre as nombreMotivoDiferencia,
            mdg.nombre as nombreMotivoDiferenciaGlobal,
            tsd.nombre as nombreTipoDiferencia,
            
            paciente.id AS idPaciente,
            ac.nombreAccionClinica AS prestacion,
            eac.nombreEstadoAccionClinica AS estadoPrestacion,
            ac.codigoFonasa AS codigoPrestacion,
            estadoPago.nombreEstadoPago,
            
            dpc.montoPagoCuenta AS montoPagadoPagoCuenta,
            dpc.fechaDetallePago AS fechaPago,
            tipoPrestacion.nombreTipoPrestacion,
            fp.id as idFormaPago,
            fp.nombre AS formaPago,
            mg.nombre AS motivoGratuidad,
            
            (
            SELECT SUM(bdb.montoBonoAdicional) FROM RebsolHermesBundle:BonoDetalleBonificacion bdb
                LEFT JOIN bdb.idBonoDetalle bd2 WHERE bd2.id = bd.id
            ) as montoPagadoDocumentoPago,
            bd.folioBono as numeroBono,
            '-' as numeroVoucher,
            
            dpc.id as idDetallePagoCuenta,
            pc.id as idPagoCuenta,
            acp.precioCobrado as montoPrestacion,
            acp.precioDiferencia as montoPagadoPrestacion,
            acp.cantidad as cantidadPrestacion
            
            FROM RebsolHermesBundle:AccionClinicaPaciente acp
            JOIN acp.idEstadoAccionClinica eac
            JOIN acp.idAccionClinica ac
            JOIN acp.idPaciente paciente
            LEFT JOIN acp.idEstadoPago estadoPago
            LEFT JOIN ac.idTipoPrestacion tipoPrestacion 
            LEFT JOIN RebsolHermesBundle:PagoCuenta pc WITH ( pc.id = acp.idPagoCuenta )
            LEFT JOIN RebsolHermesBundle:DetallePagoCuenta dpc WITH ( pc.id = dpc.idPagoCuenta )
            LEFT JOIN dpc.idFormaPago fp
            LEFT JOIN dpc.idMotivoGratuidad mg
            
            LEFT JOIN RebsolHermesBundle:BonoDetalle bd WITH ( bd.idPagoCuenta = pc.id )
            
            LEFT JOIN acp.idMotivoDiferencia md
            LEFT JOIN md.idTipoSentidoDiferencia tsd
            LEFT JOIN pc.idMotivoDiferencia mdg
            
            WHERE acp.idPaciente in (:arrPacientes) AND acp.idEstadoAccionClinica != 2 AND fp.id IN (11,14) AND bd.idAccionClinica = ac.id
            
            ORDER BY acp.id ASC
            ";

            $query2 = $this->_em->createQuery($sqlBono);
            $query2->setParameter('arrPacientes' ,$arrPacientes);
            $accionClinicaPacienteBono = $query2->getArrayResult();

            $accionClinicaPaciente = array_merge($accionClinicaPacienteNoBono, $accionClinicaPacienteBono);

            usort($accionClinicaPaciente, function ($a, $b) {
                if ($a['idAccionClinicaPaciente'] == $b['idAccionClinicaPaciente']) {
                    return 0;
                }
                return ($a['idAccionClinicaPaciente'] < $b['idAccionClinicaPaciente']) ? -1 : 1;
            });


            $strinArticuloPacientegDql = "
            SELECT
            
            acp.totalDescuento as totalDescuento,
            acp.porcentajeDescuento as porcentajeDescuento,
            pc.montoDiferencia as totalDescuentoGlobal,
            '0' as porcentajeDescuentoGlobal,
            md.id as idMotivoDiferencia,
            md.nombre as nombreMotivoDiferencia,
            mdg.nombre as nombreMotivoDiferenciaGlobal,
            tsd.nombre as nombreTipoDiferencia,
            
            paciente.id AS idPaciente,
            a.nombre AS prestacion,
            eac.nombreEstadoAccionClinica AS estadoPrestacion,
            a.codigo AS codigoPrestacion,
            estadoPago.nombreEstadoPago,
            pagoCuenta.id,
            dpa.montoTotalDocumento AS montoPagadoDocumentoPago,
            dpc.montoPagoCuenta AS montoPagadoPagoCuenta,
            tipoPrestacion.nombreTipoPrestacion,
            fp.nombre AS formaPago,
            mg.nombre AS motivoGratuidad,
            dpa.numeroDocumento as numeroBono,
            dpa.numeroVoucher as numeroVoucher,
            dpc.id as idDetallePagoCuenta,
            pc.id as idPagoCuenta,
            ap.precioCobrado as montoPrestacion
            
            FROM RebsolHermesBundle:ArticuloPaciente ap
            JOIN ap.idEstadoAccionClinica eac
            JOIN ap.idArticulo a
            JOIN ap.idAccionClinicaPaciente acp
            JOIN acp.idAccionClinica ac
            JOIN ap.idPaciente paciente
            LEFT JOIN ap.idPagoCuenta pagoCuenta
            LEFT JOIN ap.idEstadoPago estadoPago
            LEFT JOIN ac.idTipoPrestacion tipoPrestacion 
            LEFT JOIN RebsolHermesBundle:PagoCuenta pc WITH ( pc.id = ap.idPagoCuenta )
            LEFT JOIN RebsolHermesBundle:DetallePagoCuenta dpc WITH ( pc.id = dpc.idPagoCuenta )
            LEFT JOIN dpc.idFormaPago fp
            LEFT JOIN dpc.idMotivoGratuidad mg
            LEFT JOIN RebsolHermesBundle:DocumentoPago dpa WITH dpc.id = dpa.idDetallePagoCuenta
            LEFT JOIN acp.idMotivoDiferencia md
            LEFT JOIN md.idTipoSentidoDiferencia tsd
            LEFT JOIN pc.idMotivoDiferencia mdg
            
            WHERE ap.idPaciente in (:arrPacientes) AND ap.idEstadoAccionClinica != 2";

            $query3 = $this->_em->createQuery($strinArticuloPacientegDql);
            $query3->setParameter('arrPacientes' ,$arrPacientes);

            $articuloPaciente = $query3->getArrayResult();

            $resultado = array_merge($accionClinicaPaciente, $articuloPaciente);

            array_multisort(array_column($resultado, 'idPaciente'), SORT_DESC, $resultado);

            $arrayAux = array();
            $index = 0;
            $bRegistro = false;
            foreach($datosCuentaPaciente as $cuentaPaciente) {
                foreach ($resultado as $prestacion) {
                    if ($cuentaPaciente['idPaciente'] === $prestacion['idPaciente']) {

                        $descuento = $this->obtenerDatosDescuento($prestacion);

                        $arrayAux[$index] = array_merge(
                            $cuentaPaciente,
                                array(
                                    'prestacion' => $prestacion['prestacion'],
                                    'estadoPrestacion' => $prestacion['estadoPrestacion'],
                                    'codigoPrestacion' => $prestacion['codigoPrestacion'],
                                    'formaPago' => $prestacion['formaPago'],
                                    'fechaPago' => $prestacion['fechaPago'],
                                    'nombreEstadoPago' => $prestacion['nombreEstadoPago'],
                                    'tipoPrestacion' => $prestacion['nombreTipoPrestacion'],
                                    'motivoGratuidad' => $prestacion['motivoGratuidad'],
                                    'numeroBono' => $prestacion['numeroBono'],
                                    'idDetallePagoCuenta' => $prestacion['idDetallePagoCuenta'],
                                    'idPagoCuenta' => $prestacion['idPagoCuenta'],
                                    'montoPrestacion' => $prestacion['montoPrestacion'],
                                    'numeroVoucher' => $prestacion['numeroVoucher'],
                                    'montoPagado' => is_null($prestacion['montoPagadoPagoCuenta']) ? $prestacion['montoPagadoPrestacion'] : $prestacion['montoPagadoPagoCuenta'],

                                    'nombreTipoDiferencia' => $descuento['nombreTipoDiferencia'],
                                    'nombreMotivoDiferencia' => $descuento['nombreMotivoDiferencia'],
                                    'agrupacion' => $descuento['agrupacion'],
                                    'porcentajeDescuento' => $prestacion['porcentajeDescuento'],
                                    'porcentajeDescuentoGlobal' => $prestacion['porcentajeDescuentoGlobal'],
                                    'totalDescuento' => $prestacion['totalDescuento'],
                                    'totalDescuentoGlobal' => $prestacion['totalDescuentoGlobal'],
                                    'cantidadPrestacion' => $prestacion['cantidadPrestacion'],
                                    'montoTotalPrestacion' => intval($prestacion['cantidadPrestacion'])*intval($prestacion['montoPrestacion']),
                                )
                        );
                        $bRegistro = true;
                        $index++;
                    }
                }
                if ($bRegistro === false) {
                    $arrayAux[$index] = array_merge(
                        $cuentaPaciente,
                            array(
                                'prestacion' => null,
                                'codigoPrestacion' => null,
                                'estadoPrestacion' => null,
                                'formaPago' => null,
                                'fechaPago' => null,
                                'nombreEstadoPago' => null,
                                'tipoPrestacion' => null,
                                'motivoGratuidad' => null,
                                'numeroBono' => null,
                                'idDetallePagoCuenta' => null,
                                'idPagoCuenta' => null,
                                'montoPrestacion' => null,
                                'numeroVoucher' => null,
                                'montoPagado' => null,

                                'nombreTipoDiferencia' => null,
                                'nombreMotivoDiferencia' => null,
                                'agrupacion' => null,
                                'porcentajeDescuento' => null,
                                'porcentajeDescuentoGlobal' => null,
                                'totalDescuento' => null,
                                'totalDescuentoGlobal' => null,
                                'cantidadPrestacion' => null,
                                'montoTotalPrestacion' => null,
                            )
                    );
                    $index++;
                }

            }

        }
        return !empty($arrayAux) ? $arrayAux : NULL;
    }

    public function obtenerDatosDescuento($prestacion)
    {
        if($prestacion['totalDescuento']) {
            $aResult['agrupacion'] = 'INDIVIDUAL';
            $aResult['nombreMotivoDiferencia'] = $prestacion['nombreMotivoDiferencia'];
            $aResult['nombreTipoDiferencia'] = $prestacion['nombreTipoDiferencia'];
        } elseif($prestacion['totalDescuentoGlobal']) {
            $aResult['agrupacion'] = 'GLOBAL';
            $aResult['nombreMotivoDiferencia'] = $prestacion['nombreMotivoDiferenciaGlobal'];
            $aResult['nombreTipoDiferencia'] = '-';
        }else{
            $aResult['agrupacion'] = '-';
            $aResult['nombreMotivoDiferencia'] = '-';
            $aResult['nombreTipoDiferencia'] = '-';
        }
        return $aResult;
    }
}
