<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * Class  PatientRepository (migrado desde PacienteRepository)
 * @package  App\Repository
 * @author   sDelgado
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  05/12/15  ]
 * Fecha de Actualización: [ ]
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // Mock: No entity mapping yet
        parent::__construct($registry, \stdClass::class);
    }

	/**
	 * [obtenerRecetasPorPacienteNoValidadas description]
	 * @param  integer $idPaciente [description]
	 * @return array             [description]
	 */
	public function obtenerRecetasPorPacienteNoValidadas($datosArrayPaciente) {

		$idPnatural = null;


		$stringDql = 'SELECT persona.id AS idPersona,
		pnatural.id                     AS idPnatural,
		persona.rutPersona              AS rutPersona
		FROM RebsolHermesBundle:Persona persona
		JOIN RebsolHermesBundle:Pnatural pnatural WITH   ( pnatural.idPersona  = persona.id  )
		WHERE 1 = 1';

		if(trim($datosArrayPaciente['rutPersona']) !== ''){
			$stringDql .= 'AND persona.identificacionExtranjero =  :rutPersona ';
		}

		if(trim($datosArrayPaciente['otroTipoDocumento']) !== ''){
			$stringDql .= 'AND persona.identificacionExtranjero =  :otroTipoDocumentos ';
		}

		$stringDql .= 'AND persona.idEmpresa  = :idEmpresa';
		$query     = $this->_em->createQuery($stringDql);

		if(trim($datosArrayPaciente['rutPersona']) !== ''){
			$query->setParameter('rutPersona', str_ireplace('.', '', $datosArrayPaciente['rutPersona']));
		}

		if(trim($datosArrayPaciente['otroTipoDocumento']) !== ''){
			$query->setParameter('otroTipoDocumentos', $datosArrayPaciente['otroTipoDocumento']);
		}

		$query->setParameter( 'idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin') );

		$datosPersona = $query->getArrayResult($query);


		if(!empty($datosPersona)){
			foreach ($datosPersona as $valor) {
				$idPnatural = $valor['idPnatural'];
			}
		}

		/**
		 * [$stringDqlDos Consultando a la tabla PACIENTE por id ($idPnatural)]
		 * @var string
		 */
		$stringDqlDos = 'SELECT paciente.id AS idPaciente
		FROM RebsolHermesBundle:Paciente paciente
		WHERE paciente.idPnatural = :idPnatural
		AND   paciente.idEmpresa  = :idEmpresa';

		$queryDos = $this->_em->createQuery($stringDqlDos);
		$queryDos->setParameter('idPnatural', $idPnatural);
		$queryDos->setParameter('idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin'));


		$datosPaciente  = $queryDos->getArrayResult($queryDos);
		$arrIdsPaciente = array();


		if(!empty($datosPaciente)){
			foreach ($datosPaciente as $key => $value) {
				$arrIdsPaciente[]  = $value['idPaciente'];
			}
		}

		/**
		 * [$stringDqlTres Consultando a la tabla RCH_RECETA por ids ($idPaciente)]
		 * @var string
		 */
		$stringDqlTres = 'SELECT rol.nombre AS nombreRolProfesional,
		paciente.id                         AS idPaciente,
		rChReceta.id                        AS idReceta,
		rChReceta.fechaAtencion             AS fechaAtencion,
		rChReceta.fechaComplemento          AS rchRecetaFechaComplemento,
		rChReceta.fechaCreacion             AS fechaCreacionReceta,
		rChReceta.folio                     AS folioReceta,
		recetaDispensacion.id               AS idDispensacionReceta,
		recetaDispensacion.nombre           AS nombreDispensacionReceta,
		profesional.id                      AS idUsuarioCreacion,
		pnatural.nombrePnatural             AS nombreProfesional,
		pnatural.apellidoPaterno            AS apellidoPaternoProfesional,
		pnatural.apellidoMaterno            AS apellidoMaternoProfesional,
		convenio.nombrePrevision            AS nombreConvenio,
		financiador.nombrePrevision         AS nombreFinanciador

		FROM RebsolHermesBundle:RchReceta rChReceta
		LEFT JOIN RebsolHermesBundle:Paciente paciente WITH (rChReceta.idPaciente = paciente.id)
		LEFT JOIN paciente.idConvenio convenio
		LEFT JOIN paciente.idFinanciador financiador
		LEFT JOIN rChReceta.idRchRecetaDispensacion recetaDispensacion
		LEFT JOIN rChReceta.idUsuarioCreacion profesional
		LEFT JOIN RebsolHermesBundle:RolProfesional rolProfesional WITH(rolProfesional.idUsuario = profesional.id   )
		LEFT JOIN RebsolHermesBundle:Estado estado WITH( rolProfesional.idEstado = estado.id  )
		LEFT JOIN rolProfesional.idRol rol
		LEFT JOIN profesional.idPersona persona
		LEFT JOIN RebsolHermesBundle:Pnatural pnatural WITH ( pnatural.idPersona = persona.id )
		WHERE 1             = 1
		AND paciente.id IN (:idPaciente)
		AND rChReceta.esGes = :esGes
		AND estado.id       = :idEstado';

		$queryTres = $this->_em->createQuery($stringDqlTres);
		$queryTres->setParameter('esGes', 0);
		$queryTres->setParameter('idPaciente', $arrIdsPaciente);
		$queryTres->setParameter('idEstado', $this->obtenerParametroYML('EstadoRelUsuarioServicio.Activo'));

		return $queryTres->getArrayResult($queryTres);

	}


	public function obtenerRelUsuarioServicio($oSucursal){

		$arr    = array();
		$arrAux = array();
		$query  = $this->_em->createQuery('SELECT
			un.id as uni
			FROM
			RebsolHermesBundle:Unidad un
			WHERE 1=1
			AND un.idSucursal     = ?3
			AND un.idEstado       = ?1
			');

		$query->setParameter(1, $this->obtenerParametroYML('Estado.activo'));
		$query->setParameter(3, $oSucursal->getId());
		$arrUnidades = $query->getResult();


		foreach ($arrUnidades as $id) {

			$queryse = $this->_em->createQuery('SELECT
				se.id as id
				FROM RebsolHermesBundle:Servicio se
				WHERE 1=1
				AND se.idUnidad          = ?1
				');

			$queryse->setParameter(1, $id['uni']);
			$oServicio = $queryse->getResult();

			foreach ($oServicio as $id) {
				$arr[] = $id['id'];
			}

		}

		return $arr;

	}



}
