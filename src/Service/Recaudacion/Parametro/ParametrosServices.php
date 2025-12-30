<?php

namespace App\Service\Recaudacion\Parametro;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;


/**
 * Class  ParametrosServices
 * @package  \Rebsol\CajaBundle\Services\Api\Unab
 * @author sDelgado
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  03/12/15  ]
 * Fecha de Actualización: [ ]
 */
class   ParametrosServices {


	private $container;
	private $em;
	private $sToolsService;

	public function __construct(Container $container, EntityManager $em ) {
		$this->container     = $container;
		$this->em            = $em;
		$this->sToolsService = $this->container->get('hermesTools.Tools');
	}


	public function rPagoCuenta() {
		return $this->em->getRepository('RebsolHermesBundle:PagoCuenta');
	}

	public function rPaciente() {
		return $this->container->get('caja.Paciente');
	}

	public function rCaja() {
		return $this->em->getRepository('RebsolHermesBundle:Caja');
	}

	public function rParametro(){
		return $this->em->getRepository('RebsolHermesBundle:Parametro');
	}

	public function rDiferencia(){
		return $this->em->getRepository('RebsolHermesBundle:Diferencia');
	}

	public function rFormaPago() {
		return $this->em->getRepository('RebsolHermesBundle:FormaPago');
	}


	public function obtenerApiModulo($idModulo, $valorArray = 'rutaApi') {
		return $this->sToolsService->obtenerApiModulo( $idModulo, $valorArray = 'rutaApi' );
	}


	/**
	 * [obtenerParametro description]
	 * @param  string $nombreParametro [description]
	 * @return null                   [description]
	 */
	public function obtenerParametro($nombreParametro) {

		switch ($nombreParametro) {

			case 'Estado.activo':
			return $this->container->getParameter('Estado.activo');
			break;
			case 'Estado.inactivo':
			return $this->container->getParameter('Estado.inactivo');
			break;
			case 'EstadoUsuarios.activo':
			return $this->container->getParameter('EstadoUsuarios.activo');
			break;
			case 'EstadoUsuarios.inactivo':
			return $this->container->getParameter('EstadoUsuarios.inactivo');
			break;
			case 'EstadoEspecialidadMedica.activo':
			return $this->container->getParameter('EstadoEspecialidadMedica.activo');
			break;
			case 'EstadoEspecialidadMedica.inactivo':
			return $this->container->getParameter('EstadoEspecialidadMedica.inactivo');
			break;
			case 'EstadoRelUsuarioServicio.Activo':
			return $this->container->getParameter('EstadoRelUsuarioServicio.Activo');
			break;
			case 'EstadoRelUsuarioServicio.Inactivo':
			return $this->container->getParameter('EstadoRelUsuarioServicio.Inactivo');
			break;
			case 'EstadoRelUsuarioServicio.Bloqueado':
			return $this->container->getParameter('EstadoRelUsuarioServicio.Bloqueado');
			break;
			case 'EstadoPago.garantia':
			return $this->container->getParameter('EstadoPago.garantia');
			break;
			case 'EstadoPago.pagadoNormal':
			return $this->container->getParameter('EstadoPago.pagadoNormal');
			break;
			case 'EstadoPila.inactivo':
			return $this->container->getParameter('EstadoPila.inactivo');
			break;
			case 'FormaPagoTipo.Efectivo':
			return $this->container->getParameter('FormaPagoTipo.Efectivo');
			break;
			case 'FormaPagoTipo.Gratuidad':
			return $this->container->getParameter('FormaPagoTipo.Gratuidad');
			break;
			case 'FormaPagoTipo.BonoElectronico':
			return $this->container->getParameter('FormaPagoTipo.BonoElectronico');
			break;
			case 'FormaPagoTipo.TarjetaCredito':
			return $this->container->getParameter('FormaPagoTipo.TarjetaCredito');
			break;
			case 'FormaPagoTipo.BonoManual':
			return $this->container->getParameter('FormaPagoTipo.BonoManual');
			break;
			case 'FormaPagoTipo.TarjetaDebito':
			return $this->container->getParameter('FormaPagoTipo.TarjetaDebito');
			break;
			case 'FormaPagoTipo.ChequeFecha':
			return $this->container->getParameter('FormaPagoTipo.ChequeFecha');
			break;
			case 'FormaPagoTipo.ChequeDia':
			return $this->container->getParameter('FormaPagoTipo.ChequeDia');
			break;
			case 'FormaPagoTipo.ConvenioLasik':
			return $this->container->getParameter('FormaPagoTipo.ConvenioLasik');
			break;
			case 'FormaPagoTipo.ConvenioImed':
			return $this->container->getParameter('FormaPagoTipo.ConvenioImed');
			break;
			case 'FormaPagoTipo.SeguroComplementario':
			return $this->container->getParameter('FormaPagoTipo.SeguroComplementario');
			break;
			case 'EstadoDetalleTalonario.emitidas':
			return $this->container->getParameter('EstadoDetalleTalonario.emitidas');
			break;
			default:
			return null;

		}
	}


	/**
	 * [obtenerEstado description]
	 * @param  string $nombreParametro [description]
	 * @return null                  [description]
	 */
	public function obtenerEstado($nombreParametro){

		switch ($nombreParametro) {
			case 'EstadoPilaActiva':
			return $this->em->getRepository('RebsolHermesBundle:EstadoPila')->find($this->container->getParameter('EstadoPila.activo'));
			break;
			case 'EstadoReaperturaCerrada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoReapertura')->find($this->container->getParameter('EstadoReapertura.cerrada'));
			break;
			case 'EstadoReaperturaAbierta':
			return $this->em->getRepository('RebsolHermesBundle:EstadoReapertura')->find($this->container->getParameter('EstadoReapertura.abierta'));
			break;
			case 'EstadoActivo':
			return $this->em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('Estado.activo'));
			break;
			case 'EstadoInc':
			return $this->em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('Estado.inactivo'));
			break;
			case 'EstadoPagoActiva':
			return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.pagadoNormal'));
			break;
			case ' EstadoPagoAnulada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.anulado'));
			break;
			case 'EstadoPagoGarantia':
			return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.garantia'));
			break;
			case 'EstadoPagoRegularizada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoPago')->find($this->container->getParameter('EstadoPago.garantiaRegularizada'));
			break;
			case 'EstadoCuentaCerradaPagada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaPagada'));
			case 'EstadoCuentaCerradaPagadaTotal':
			return $this->em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaPagadaTotal'));
			break;
			case 'estadoCuentacerradaPagadaConSaldoPendiente':
			return $this->em->getRepository('RebsolHermesBundle:EstadoCuenta')->find($this->container->getParameter('EstadoCuenta.cerradaPagadaConSaldoPendiente'));
			break;
			case 'EstadoBoletaActiva':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find($this->container->getParameter('EstadoDetalleTalonario.emitidas'));
			break;
			case 'EstadoBoletaAnulada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDetalleTalonario')->find($this->container->getParameter('EstadoDetalleTalonario.anulada'));
			break;
			case 'EstadoAccionClinicaSolicitado':
			return $this->em->getRepository('RebsolHermesBundle:EstadoAccionClinica')->find($this->container->getParameter('EstadoAccionClinica.solicitado'));
			break;
			case 'EstadoApi':
			return $this->obtenerApiModulo($this->container->getParameter('modulo_caja'));
			break;
			case 'DiferenciacajeroPideAutorizacion':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.cajeroPideAutorizacion'));
			break;
			case 'Diferenciaautorizada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.autorizada'));
			break;
			case 'DiferenciadescuentoNoRequiereAutorizacion':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'));
			break;
			case 'DiferenciacajeroCancelaSolicitud':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.cajeroCancelaSolicitud'));
			break;
			case 'Diferenciarechazada':
			return $this->em->getRepository('RebsolHermesBundle:EstadoDiferencia')->find($this->container->getParameter('EstadoDiferencia.rechazada'));
			break;
			default:
			return null;
		}

	}


	public function validacionesDocumentosFaltantes($sucursalUsuario, $estado){

		$failsMesages    = array();
		$countPlan       = 0;
		$countProf       = 0;

		$oOrigen         = $this->em->getRepository('RebsolHermesBundle:Origen')->findBy(
			array(
				'idSucursal' => $sucursalUsuario,
				'idEstado'   => $estado
				)
			);

		$oRolProfesional = $this->em->getRepository('RebsolHermesBundle:RolProfesional')->findBy(
			array(
				'idRol'    => $this->container->getParameter('rol_medico'),
				'idEstado' => $estado
				)
			);

		$oPrevision      = $this->em->getRepository('RebsolHermesBundle:Prevision')->findBy(
			array(
				'idEmpresa' => $this->container->getParameter('empresa_activa_id'),
				'idEstado'  => $estado
				)
			);

		$oTipoPrevision  = $this->em->getRepository('RebsolHermesBundle:TipoPrevision')->findBy(
			array(
				'idEmpresa'  => $this->container->getParameter('empresa_activa_id'),
				'esConvenio' => 1,
				'idEstado'   => $estado
				)
			);

		if (!$oOrigen) {
			$failOrigen = 'Origen';
			$failsMesages[] = $failOrigen;
		}

		if ($oRolProfesional) {
			foreach ($oRolProfesional as $c) {
				$countProf = $countProf + 1;
			}
		}

		if ($countProf == 0) {
			$failProfesionales = 'Profesionales';
			$failsMesages[] = $failProfesionales;
		}

		if (!$oPrevision) {
			$failFinanciador = 'Financiador';
			$failsMesages[] = $failFinanciador;
		}

		if (!$oTipoPrevision) {
			$failConvenios = 'Convenios';
			$failsMesages[] = $failConvenios;
		}

		$auxRelSucPre = array();
		foreach ($oPrevision as $pr) {

			$oRelSucursalPrevision = $this->em->getRepository('RebsolHermesBundle:RelSucursalPrevision')->findOneBy(array(
				'idSucursal' => $sucursalUsuario,
				'idPrevision' => $pr->getid(),
				'idEstado' => $estado
				));

			if ($oRelSucursalPrevision) {
				$auxRelSucPre[] = $oRelSucursalPrevision->getid();
			}

		}


		foreach ($auxRelSucPre as $c) {

			$oPrPlan = $this->em->getRepository('RebsolHermesBundle:PrPlan')->findBy(array(
				'idRelSucursalPrevision' => $c,
				'idEstado' => $estado
				));

			if ($oPrPlan) {
				$countPlan = $countPlan + 1;
			}
		}

		return $failsMesages;
	}



	public function errorImedHermes($var) {
		switch ($var){
			case 'VtaBonInterfaz':
			return 'Error en Generar Venta Bono Interfaz';
			break;
			case 'ObtBonInterfaz':
			return 'Error en Obtener Bono por Interfaz';
			break;
			case 'noSubEmpresa':
			return 'Prestaciones no Corresponden a Sub-E,mpresa de Cajero';
			break;
			case 'sinPreciosPrestacion':
			return 'Prestación no cuenta con sus Precios Correctamente';
			break;
			case 'sinParameters':
			return 'No fue posible generar Parametros, reintente';
			break;
			case 'SetGlobalsVar':
			return 'No se han encontrado datos básicos para establecer comunicación con I-MED, debe ponerse en contacto con el Administrador';
			break;
			case ' noSendPostLogin':
			return 'No fue posible establecer Comunicación con I-MED. Error: Envio Post = False';
			break;
			case 'errorCajaRecaudacion':
			return 'El usuario no esta relacionado como Cajero';
			break;
			case 'errorEjecucion':
			return 'No ha sido Posible inicializar Caja, Error Interno';
			break;

			default:
			return null;
		}
	}


	public function tipos($oEmpresa){

		$tipoDocumentoAfecto  = $this->container->getParameter('tipo_documento_afecto');
		$tipoDocumentoExento  = $this->container->getParameter('tipo_documento_Exento');
		$tipoLogRecepcion     = $this->container->getParameter('tipo_log_recepcion');
		$tipoLogPagoReserva   = $this->container->getParameter('tipo_log_pago_Reserva');
		$ambulatoria          = $this->container->getParameter('ambulatoria');

		$oTipoDocumentoAfecto = $this->em->getRepository('RebsolHermesBundle:TipoDocumento')->find($tipoDocumentoAfecto);
		$oTipoDOcumentoExento = $this->em->getRepository('RebsolHermesBundle:TipoDocumento')->find($tipoDocumentoExento);

		return array(
			'TipoAtencionFcAmbulatoria' => $this->em->getRepository('RebsolHermesBundle:TipoAtencionFc')->find($ambulatoria),
			'BoletaAfecta'              => $this->em->getRepository('RebsolHermesBundle:RelEmpresaTipoDocumento')->findOneBy( array(
				'idTipoDocumento'           => $oTipoDocumentoAfecto->getid(),
				'idEmpresa'                 => $oEmpresa->getid()
				)
			),
			'BoletaExenta'              => $this->em->getRepository('RebsolHermesBundle:RelEmpresaTipoDocumento')->findOneBy( array(
				'idTipoDocumento'           => $oTipoDOcumentoExento->getid(),
				'idEmpresa'                 => $oEmpresa->getid()
				)
			),
			'TipoLogRecepcion'          => $this->em->getRepository('RebsolHermesBundle:ReservaAtencionTipoLog')->find($tipoLogRecepcion),
			'TipoLogPagoReserva'        => $this->em->getRepository('RebsolHermesBundle:ReservaAtencionTipoLog')->find($tipoLogPagoReserva)
			);

	}



}