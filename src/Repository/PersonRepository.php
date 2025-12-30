<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class  PersonRepository (migrado desde PersonaRepository)
 * @package  App\Repository
 * @author   sDelgado
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  21/12/15  ]
 * Fecha de Actualización: [ ]
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // Mock: No entity mapping yet
        parent::__construct($registry, \stdClass::class);
    }

    // Legacy compatibility
	private $_em;

	private function getManager() {
		return $this->getEntityManager();
	}

	public function setEntityManager($entityManager) {

		$this->_em         = $entityManager;
	}

	/**
	 * [obtenerAvanzadoDirectorioPaciente description]
	 * @param  string $oEmpresa     [description]
	 * @param  string $strNombres   [description]
	 * @param  string $strApPaterno [description]
	 * @param  string $strApMaterno [description]
	 * @param  string $strRut       [description]
	 * @param  string $srtDv        [description]
	 * @param  string $strlike      [description]
	 * @return array                [description]
	 */
	public function obtenerAvanzadoDirectorioPaciente($oEmpresa, $strNombres = null , $strApPaterno = null, $strApMaterno = null, $strRut = null, $srtDv = null, $strlike) {

		$queryStr = 'SELECT pe.id as idPersona
		, pe.rutPersona as rutPaciente
		, pe.digitoVerificador as dvPaciente
		, tie.id as idTipoIdentificacionExtranjero
		, tie.nombre as nombreTipoIdentificacion
		, pe.identificacionExtranjero as documentoExtranjero
		, pn.id as idPnatural
		, pn.nombrePnatural as nombre
		, pn.apellidoPaterno as apellidoPaterno
		, pn.apellidoMaterno as apellidoMaterno
		, se.nombreSexo as sexo
		, pe.telefonoMovil as telefonoMovil
		, pe.telefonoFijo as telefonoFijo
		, pn.fechaNacimiento as fechaNacimiento
		, re.id as idReserva
		FROM RebsolHermesBundle:Persona pe
		INNER JOIN RebsolHermesBundle:Pnatural pn WITH (pn.idPersona = pe.id )
		LEFT JOIN RebsolHermesBundle:Paciente pa WITH (pa.idPnatural = pn.id )
		LEFT JOIN RebsolHermesBundle:ReservaAtencion re WITH (re.idPaciente = pa.id )
		LEFT JOIN RebsolHermesBundle:Sexo se WITH (pn.idSexo = se.id)
		LEFT JOIN RebsolHermesBundle:TipoIdentificacionExtranjero tie WITH (tie.id = pe.idTipoIdentificacionExtranjero)
		WHERE pe.idEmpresa = :idEmpresa';

		if (!empty($strNombres)) $queryStr.=" AND (pn.nombrePnatural ".$strlike." :nPnatural)";
		if (!empty($strApPaterno)) $queryStr.=" AND (pn.apellidoPaterno ".$strlike." :apPaterno)";
		if (!empty($strApMaterno)) $queryStr.=" AND (pn.apellidoMaterno ".$strlike." :apMaterno)";
		if (!empty($strRut)) $queryStr.=" AND (pe.rutPersona ".$strlike." :rutPersona) AND (pe.digitoVerificador ".$strlike." :dv)";

		$query = $this->_em->createQuery($queryStr);

		$query->setParameter('idEmpresa', $oEmpresa->getId());

		if (!empty($strNombres)) $query->setParameter("nPnatural", $strNombres);

		if (!empty($strApPaterno)) $query->setParameter("apPaterno", $strApPaterno);

		if (!empty($strApMaterno)) $query->setParameter("apMaterno", $strApMaterno);

		if (!empty($strRut)) $query->setParameter("rutPersona", $strRut);

		if (!empty($strRut)) $query->setParameter("dv", $srtDv);

		$resultado = $query->getResult();
		return $resultado;
	}

	public function obtenerAvanzadaHSJDD($oEmpresa, $strNombres = null , $strApPaterno = null, $strApMaterno = null, $strlike) {

		$queryStr = 'SELECT pe.id as idPersona
		, pe.rutPersona as rutPaciente
		, pe.digitoVerificador as dvPaciente
		, tie.id as idTipoIdentificacionExtranjero
		, tie.nombre as nombreTipoIdentificacion
		, pe.identificacionExtranjero as documentoExtranjero
		, pn.id as idPnatural
		, pn.nombrePnatural as nombre
		, pn.apellidoPaterno as apellidoPaterno
		, pn.apellidoMaterno as apellidoMaterno
		, se.nombreSexo as sexo
		, pe.telefonoMovil as telefonoMovil
		, pe.telefonoFijo as telefonoFijo
		, pn.fechaNacimiento as fechaNacimiento
		FROM RebsolHermesBundle:Persona pe
		INNER JOIN RebsolHermesBundle:Pnatural pn WITH (pn.idPersona = pe.id )
		LEFT  JOIN RebsolHermesBundle:Sexo se WITH (pn.idSexo = se.id)
		LEFT  JOIN RebsolHermesBundle:TipoIdentificacionExtranjero tie WITH (tie.id = pe.idTipoIdentificacionExtranjero)
		WHERE
		pe.idEmpresa = :idEmpresa';

		if (!empty($strNombres)) $queryStr   .=" AND (pn.nombrePnatural ".$strlike." :nPnatural)";

		if (!empty($strApPaterno)) $queryStr .=" AND (pn.apellidoPaterno ".$strlike." :apPaterno)";

		if (!empty($strApMaterno)) $queryStr .=" AND (pn.apellidoMaterno ".$strlike." :apMaterno)";

		if (!empty($strRut)) $queryStr       .=" AND (pe.rutPersona ".$strlike." :rutPersona) AND (pe.digitoVerificador ".$strlike." :dv)";

		$query = $this->_em->createQuery($queryStr);

		$query->setParameter('idEmpresa', $oEmpresa->getId());

		if (!empty($strNombres)) $query->setParameter("nPnatural", $strNombres);

		if (!empty($strApPaterno)) $query->setParameter("apPaterno", $strApPaterno);

		if (!empty($strApMaterno)) $query->setParameter("apMaterno", $strApMaterno);

		if (!empty($strRut)) $query->setParameter("rutPersona", $strRut);

		if (!empty($strRut)) $query->setParameter("dv", $srtDv);

		$resultado = $query->getResult();

		return $resultado;
	}

	/**
	 * [obtenerDatosPersona description]
	 * @param  array  $opciones [description]
	 * @return [type]           [description]
	 */
	public function obtenerDatosPersona( array $opciones ) {

		$queryStr = 'SELECT p.id
		, p.rutPersona as rut
		, p.digitoVerificador as dv
		, ex.id as extranjero
		, p.identificacionExtranjero as numeroDocumento
		, pn.id as idPnatural
		, pn.nombrePnatural as nombre
		, pn.apellidoPaterno as apep
		, pn.apellidoMaterno as apem
		, pn.fechaNacimiento as fechan
		, x_.id as sexo
		, p.telefonoMovil as celu
		, p.telefonoFijo as fijo
		, p.telefonoTrabajo as trabajo
		, p.correoElectronico as mail1
		, p.correoElectronico2 as mail2
		, ur.id as usuario
		, p.id as idPersona
		, pn.fechaDefuncion as fechad
		, pn.nombreSocial as nombreSocial
		FROM RebsolHermesBundle:Persona p
		JOIN RebsolHermesBundle:Pnatural pn WITH p.id = pn.idPersona
		LEFT JOIN p.idTipoIdentificacionExtranjero ex
		LEFT JOIN pn.idSexo x_
		LEFT JOIN RebsolHermesBundle:UsuariosRebsol ur WITH  p.id = ur.idPersona
		WHERE p.idEmpresa                        = :idEmpresa ';


        if (isset($opciones['nombres']) and !empty($opciones['nombres'])) $queryStr   .=" AND (pn.nombrePnatural ".$opciones['strlike']." :nPnatural)";

        if (isset($opciones['apPaterno']) and !empty($opciones['apPaterno'])) $queryStr .=" AND (pn.apellidoPaterno ".$opciones['strlike']." :apPaterno)";

        if (isset($opciones['apMaterno']) and !empty($opciones['apMaterno'])) $queryStr .=" AND (pn.apellidoMaterno ".$opciones['strlike']." :apMaterno)";

        if (isset($opciones['identificacionExtranjero'])) $queryStr       .=" AND (p.identificacionExtranjero = :identificacionExtranjero) AND (p.idTipoIdentificacionExtranjero = :idTipoIdentificacionExtranjero)";

		$query = $this->_em->createQuery($queryStr);

        $query->setParameter('idEmpresa', $opciones[ 'idEmpresa' ]);

        if (isset($opciones['nombres']) and !empty($opciones['nombres'])) $query->setParameter("nPnatural", $opciones['nombres']);

		if (isset($opciones['apPaterno']) and !empty($opciones['apPaterno'])) $query->setParameter("apPaterno", $opciones['apPaterno']);

		if (isset($opciones['apMaterno']) and !empty($opciones['apMaterno'])) $query->setParameter("apMaterno", $opciones['apMaterno']);

        if (isset($opciones['idTipoIdentificacionExtranjero'])) $query->setParameter("idTipoIdentificacionExtranjero", $opciones['idTipoIdentificacionExtranjero']);

        if (isset($opciones['identificacionExtranjero'])) $query->setParameter("identificacionExtranjero", $opciones['identificacionExtranjero']);


		$query     = current( $query->getArrayResult() );

		if ( $query ) {

			$idPersona = $query[ 'id' ];

			$queryStrPagoCuenta = 'SELECT pc.id as pagoCuenta
			FROM RebsolHermesBundle:Persona p
			LEFT JOIN RebsolHermesBundle:Pnatural pn WITH ( p.id    = pn.idPersona )
			LEFT JOIN p.idTipoIdentificacionExtranjero ex
			LEFT JOIN RebsolHermesBundle:Paciente pa WITH ( pn.id   = pa.idPnatural )
			LEFT JOIN RebsolHermesBundle:PagoCuenta pc WITH ( pa.id = pc.idPaciente )
			WHERE p.id          = :idPersona
			AND p.idEmpresa     = :idEmpresa
			AND pc.idEstadoPago = :idEstadoPago';

			$queryPagoCuenta = $this->_em->createQuery($queryStrPagoCuenta);

			$queryPagoCuenta->setParameters (
				array(
					'idPersona'      => $idPersona
					, 'idEmpresa'    => $opciones[ 'idEmpresa' ]
					, 'idEstadoPago' => 2
					)
				);

			$queryPagoCuenta  = $queryPagoCuenta->getArrayResult();
			$cantidadGarantia = 0;

			if( !empty( $queryPagoCuenta ) ){

				foreach ($queryPagoCuenta as $key => $value) {

					$cantidadGarantia = ++$cantidadGarantia;
				}

				unset($queryPagoCuenta, $key, $value);
				gc_collect_cycles();
			}

			$query['garantias']   = $cantidadGarantia;
			$query['dataReserva'] = false;

		} else {

			$queryStrReservaAtencion = 'SELECT ra.rutPaciente as rut,
			ra.digitoVerificadorPaciente as dv,
			ex.id as extranjero,
			ra.identificacionExtranjero as numeroDocumento,
			ra.nombres as nombre,
			ra.apellidoPaterno as apep,
			ra.apellidoMaterno as apem,
			ra.fechaNacimiento as fechan,
			x_.id as sexo,
			ra.telefonoMovil as celu,
			ra.telefonoFijo as fijo,
			ra.telefonoContacto as trabajo,
			ra.correoElectronico as mail1,
			ra.direccion as direccion,
			ra.restoDireccion as resto,
			ra.numero as numero,
			co.id as idComuna,
			ra.fechaRegistro as fechar
			FROM
			RebsolHermesBundle:ReservaAtencion ra
			LEFT JOIN ra.idComuna co
			LEFT JOIN ra.idTipoIdentificacionExtranjero ex
			LEFT JOIN ra.idSexo x_
			WHERE ra.idTipoIdentificacionExtranjero = :idTipoIdentificacionExtranjero
			AND ra.identificacionExtranjero = :identificacionExtranjero
			ORDER BY
			ra.fechaRegistro DESC';

			$query = $this->_em->createQuery($queryStrReservaAtencion)->setMaxResults('1');

			$query->setParameters (
				array(
					'idTipoIdentificacionExtranjero' => $opciones[ 'idTipoIdentificacionExtranjero' ]
					, 'identificacionExtranjero'     => $opciones[ 'identificacionExtranjero' ]
					)
				);

			$query = current($query->getResult());

			if ( $query ) {

				$query['mail2']         = null;
				$query['usuario']       = null;
				$query['fechad']        = null;
				$query['idPersona']     = null;
				$query['garantias']     = null;
				$query['dataReserva']   = true;
			}
		}

        if (isset($opciones['idTipoIdentificacionExtranjero'])) {
            if ($opciones['idTipoIdentificacionExtranjero'] == 1) {

                $rutPersona = $opciones['identificacionExtranjero'];

                if (strlen($rutPersona) > 1) {

                    if (preg_split('/[-]/', $rutPersona)[0] < 50000000) {

                        if ($this->verificaRutUsuario($rutPersona)) {

                            $query = $query;

                            if ($query !== false) {

                                $query['esRut'] = true;
                            }
                        } else {

                            return 'Rut Inválido';
                        }
                    } else {

                        return 'Rut no pertenece a Empresa';
                    }
                } else {

                    return 'Rut no debe ser vacío';
                }
            } else {

                $query = $query;

                if ($query !== false) {

                    $query['esRut'] = false;
                }
            }
        }

		return $query;
	}

    public function obtenerDatosPersonaCuentaPaciente( array $opciones ) {

        $queryStr = 'SELECT p.id
		, p.rutPersona as rut
		, p.digitoVerificador as dv
		, ex.id as extranjero
		, ex.nombre as tipoDocumento
		, p.identificacionExtranjero as numeroDocumento
		, pn.id as idPnatural
		, pn.nombrePnatural as nombre
		, pn.apellidoPaterno as apep
		, pn.apellidoMaterno as apem
		, pn.fechaNacimiento as fechan
		, x_.id as sexo
		, p.telefonoMovil as celu
		, p.telefonoFijo as fijo
		, p.telefonoTrabajo as trabajo
		, p.correoElectronico as mail1
		, p.correoElectronico2 as mail2
		, ur.id as usuario
		, p.id as idPersona
		, pn.fechaDefuncion as fechad
		, pn.nombreSocial as nombreSocial
		FROM RebsolHermesBundle:Persona p
		JOIN RebsolHermesBundle:Pnatural pn WITH p.id = pn.idPersona
		LEFT JOIN p.idTipoIdentificacionExtranjero ex
		LEFT JOIN pn.idSexo x_
		LEFT JOIN RebsolHermesBundle:UsuariosRebsol ur WITH  p.id = ur.idPersona
		WHERE p.idEmpresa                        = :idEmpresa ';


        if (isset($opciones['nombres']) and !empty($opciones['nombres'])) $queryStr   .=" AND (pn.nombrePnatural ".$opciones['strlike']." :nPnatural)";

        if (isset($opciones['apPaterno']) and !empty($opciones['apPaterno'])) $queryStr .=" AND (pn.apellidoPaterno ".$opciones['strlike']." :apPaterno)";

        if (isset($opciones['apMaterno']) and !empty($opciones['apMaterno'])) $queryStr .=" AND (pn.apellidoMaterno ".$opciones['strlike']." :apMaterno)";

        if (isset($opciones['identificacionExtranjero'])) $queryStr       .=" AND (p.identificacionExtranjero = :identificacionExtranjero) AND (p.idTipoIdentificacionExtranjero = :idTipoIdentificacionExtranjero)";

        $query = $this->_em->createQuery($queryStr);

        $query->setParameter('idEmpresa', $opciones[ 'idEmpresa' ]);

        if (isset($opciones['nombres']) and !empty($opciones['nombres'])) $query->setParameter("nPnatural", $opciones['nombres']);

        if (isset($opciones['apPaterno']) and !empty($opciones['apPaterno'])) $query->setParameter("apPaterno", $opciones['apPaterno']);

        if (isset($opciones['apMaterno']) and !empty($opciones['apMaterno'])) $query->setParameter("apMaterno", $opciones['apMaterno']);

        $query     = current( $query->getArrayResult() );

        if ( $query ) {

            $idPersona = $query[ 'id' ];

            $queryStrPagoCuenta = 'SELECT pc.id as pagoCuenta
			FROM RebsolHermesBundle:Persona p
			LEFT JOIN RebsolHermesBundle:Pnatural pn WITH ( p.id    = pn.idPersona )
			LEFT JOIN p.idTipoIdentificacionExtranjero ex
			LEFT JOIN RebsolHermesBundle:Paciente pa WITH ( pn.id   = pa.idPnatural )
			LEFT JOIN RebsolHermesBundle:PagoCuenta pc WITH ( pa.id = pc.idPaciente )
			WHERE p.id          = :idPersona
			AND p.idEmpresa     = :idEmpresa
			AND pc.idEstadoPago = :idEstadoPago';

            $queryPagoCuenta = $this->_em->createQuery($queryStrPagoCuenta);

            $queryPagoCuenta->setParameters (
                array(
                    'idPersona'      => $idPersona
                , 'idEmpresa'    => $opciones[ 'idEmpresa' ]
                , 'idEstadoPago' => 2
                )
            );

            $queryPagoCuenta  = $queryPagoCuenta->getArrayResult();
            $cantidadGarantia = 0;

            if( !empty( $queryPagoCuenta ) ){

                foreach ($queryPagoCuenta as $key => $value) {

                    $cantidadGarantia = ++$cantidadGarantia;
                }

                unset($queryPagoCuenta, $key, $value);
                gc_collect_cycles();
            }

            $query['garantias']   = $cantidadGarantia;
            $query['dataReserva'] = false;

        }


        return $query;
    }

	private function verificaRutUsuario($rut = '') {

		$sep = array();
		$multi = 2;
		$suma = 0;

		if (empty($rut)) {
			return false;
		}

		$tmpRUT = preg_replace('/[^0-9kK]/', '', $rut);

		if (strlen($tmpRUT) == 8) {
			$tmpRUT = '0' . $tmpRUT;
		}

		if (strlen($tmpRUT) == 7){
			$tmpRUT = '00' . $tmpRUT;
		}

		if (strlen($tmpRUT) == 6) {
			$tmpRUT = '000' . $tmpRUT;
		}

		if (strlen($tmpRUT) == 5) {
			$tmpRUT = '0000' . $tmpRUT;
		}

		if (strlen($tmpRUT) != 9) {
			return false;
		}

		if (strlen($tmpRUT) == 9) {
			$sep['rutPersona'] = substr($tmpRUT, 0, 8);
		}

		if (strlen($tmpRUT) == 8) {
			$sep['rutPersona'] = substr($tmpRUT, 0, 7);
		}

		if (strlen($tmpRUT) == 7) {
			$sep['rutPersona'] = substr($tmpRUT, 0, 6);
		}

		if (strlen($tmpRUT) == 6) {
			$sep['rutPersona'] = substr($tmpRUT, 0, 5);
		}

		$sep['digitoVerifivador'] = substr($tmpRUT, -1);

		if ($sep['digitoVerifivador'] == 'k') {
			$sep['digitoVerifivador'] = 'K';
		}

		if (!is_numeric($sep['rutPersona'])) {
			return false;
		}

		if (empty($sep['rutPersona']) OR $sep['digitoVerifivador'] == '') {
			return false;
		}

		for ($i = strlen($sep['rutPersona']) - 1; $i >= 0; $i--) {

			$suma = $suma + $sep['rutPersona'][$i] * $multi;

			if ($multi == 7) {

				$multi = 2;
			} else {

				$multi++;
			}
		}

		$resto = $suma % 11;

		if ($resto == 1) {

			$sep['digitoVerifivadort'] = 'K';
		} else {

			if ($resto == 0) {

				$sep['digitoVerifivadort'] = '0';
			} else {

				$sep['digitoVerifivadort'] = 11 - $resto;
			}
		}

		if ($sep['digitoVerifivadort'] != $sep['digitoVerifivador']) {

			return false;
		}

		return true;
	}

}
