<?php

namespace App\Controller\Caja\Recaudacion\Pago\Dependencias;


use App\Controller\Caja\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @author ovaldenegro
 * @version 0.1.0
 * Fecha Creación: 05/11/2013
 * Participantes:
 *
 */
class ValrutController extends DefaultController {

	public function indexRutAction(Request $request) {
		$idTipoIdentificacionExtranjero = $request->query->get('idTipoIdentificacionExtranjero');
		$tipoDocumentoExtranjero        = $request->query->get('tipoDocumentoExtranjero');
		$busquedaAvanzada        = $request->query->get('busquedaAvanzada');

		if ($request->query->has('tipoIdentificacion')) {
			$idTipoIdentificacionExtranjero = $request->query->get('tipoIdentificacion');
		}

		if ($request->query->has('identificacion')) {
			$tipoDocumentoExtranjero = $request->query->get('identificacion');
		}

		if ( $idTipoIdentificacionExtranjero == 1 ) {
			$tipoDocumentoExtranjero = str_replace('.', '', $tipoDocumentoExtranjero);
		} else {
			$tipoDocumentoExtranjero = $tipoDocumentoExtranjero;
		}

        /* *
         * Estamos ocupando el método
         * 'validarIdentificadorUnicoPorEmpresa'
         * del servicio 'Buscar_Paciente_Service'
         * * */
        $sBuscarPaciente = $this->get('Buscar_Paciente_Service');
        $arrayOpciones = array(
            'idTipoIdentificacionExtranjero' => $idTipoIdentificacionExtranjero,
            'identificacionExtranjero'      => $tipoDocumentoExtranjero,
            'idEmpresa'                     => $this->ObtenerEmpresaLogin()->getId(),
            'busquedaAvanzada'              => $busquedaAvanzada,
        );

        $datosPersona = $sBuscarPaciente->validarIdentificadorUnicoPorEmpresa($arrayOpciones, 1);

		if (is_array($datosPersona) && isset($datosPersona['nombreSocial']) && $datosPersona['nombreSocial'] != null) {
			$datosPersona['nombre'] = $datosPersona['nombre']." (".$datosPersona['nombreSocial'].")";
		}
		return new Response(
			json_encode($datosPersona)
			);
	}

	public function indexExtranjeroAction() {

		$em        = $this->getDoctrine()->getManager();
		$r         = $this->container->get('request_stack')->getCurrentRequest()->query->get('r');
		$oEmpresa  = $this->ObtenerEmpresaLogin();
		$Resultado = '';
		$garan     = 0;
		$lenrut    = strlen($r);

		if ($lenrut > 1) {
			$oPersona = $em->getRepository('RebsolHermesBundle:Persona')->findOneBy(array('identificacionExtranjero' => $r));
			if (!$oPersona) {
				$Resultado = "no";
			} else {

				$idPnaturalINT = $oPersona->getId();

				$query         = $em->createQuery("SELECT p.rutPersona as rut,
					p.digitoVerificador as dv,
					ex.id as extranjero,
					p.identificacionExtranjero as numeroDocumento,
					pn.nombrePnatural as nombre,
					pn.apellidoPaterno as apep,
					pn.apellidoMaterno as apem,
					pn.fechaNacimiento as fechan,
					x_.id as sexo,
					p.telefonoMovil as celu,
					p.telefonoFijo as fijo,
					p.correoElectronico as mail1,
					p.correoElectronico2 as mail2,
					ur.id as usuario,
					p.id as idPersona,
					pn.fechaDefuncion as fechad
					FROM
					RebsolHermesBundle:Persona p
					JOIN RebsolHermesBundle:Pnatural pn
					WITH p.id = pn.idPersona
					LEFT JOIN p.idTipoIdentificacionExtranjero ex
					JOIN pn.idSexo x_
					LEFT JOIN Rebsol\HermesBundle\Entity\UsuariosRebsol ur
					WITH  p.id = ur.idPersona
					WHERE
					1=1
					AND p.id = $idPnaturalINT
					AND p.idEmpresa = ?1
					");
				$query->setParameter(1, $oEmpresa->getid());

				$stringresultado = $query->getSingleResult();
				$r               = $stringresultado;

				$queryGarantias  = $em->createQuery("
					SELECT
					pc.id as pagoCuenta
					FROM
					RebsolHermesBundle:Persona p
					JOIN RebsolHermesBundle:Pnatural pn
					WITH p.id = pn.idPersona
					LEFT JOIN p.idTipoIdentificacionExtranjero ex
					LEFT JOIN Rebsol\HermesBundle\Entity\Paciente pa
					WITH  pn.id = pa.idPnatural
					LEFT JOIN Rebsol\HermesBundle\Entity\PagoCuenta pc
					WITH  pa.id = pc.idPaciente
					WHERE
					1=1
					AND p.id = $idPnaturalINT
					AND p.idEmpresa = ?1
					AND pc.idEstadoPago = 2

					");
				$queryGarantias->setParameter(1, $oEmpresa->getid());
				$resultadoGarantias = $queryGarantias->getResult();



				if($resultadoGarantias)
				{
					foreach($resultadoGarantias as $g){
						$garan = $garan + 1;
					}
					$r['garantias'] = $garan;
				}else{
					$r['garantias'] = $garan;
				}

				$Resultado = $r;

			}
		} else {
			$Resultado = "vacio";
		}
		return new Response(json_encode($Resultado));
	}

	public function VerificaGarantiasRut(){
		$DatosPagoGarantia = $this->repoPagoCuenta()->ObtenerDatosPagoGarantia($this->container->get('request_stack')->getCurrentRequest()->query->get('id'));
		return new Response(json_encode($DatosPagoGarantia));
	}
}
