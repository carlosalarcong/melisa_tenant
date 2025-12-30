<?php

namespace Rebsol\RecaudacionBundle\Repository;

use Doctrine\ORM\EntityManager;
use Rebsol\HermesBundle\Entity\Persona;
use Rebsol\HermesBundle\Entity\PersonaDomicilio;
use Rebsol\HermesBundle\Entity\Pnatural;
use Rebsol\HermesBundle\Entity\ReservaAtencion;
use Rebsol\HermesBundle\Services\BuscarPacienteService;

/**
 * Class  RegistroPersonaRepository
 * @package  \Rebsol\CajaBundle\Repository
 * @author   sDelgado
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  21/12/15  ]
 * Fecha de Actualización: [ ]
 */
class RegistroPersonaRepository {

	private $em;
	private $buscarPacienteService;

	public function __construct(EntityManager $entityManager, BuscarPacienteService $buscarPacienteService) {

		$this->em                    = $entityManager;
		$this->buscarPacienteService = $buscarPacienteService;
	}

	/**
	 * [generaRegistroPersonaDB description]
	 * @param  array  $opciones [description]
	 * @return [type]           [description]
	 */
	public function  generaRegistroPersonaDB( array $opciones ) {

		// echo "<pre>"; \Doctrine\Common\Util\Debug::dump( $opciones ); exit(-1);

		$em                             = $this->em;
        $arrParametroHabilitarPaisExtranjero = $this->em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('HABILITAR_PAIS_NACIONALIDAD_EXTRANJERO');
        $habilitarPaisExtranjero = intval($arrParametroHabilitarPaisExtranjero['valor']);
		$idTipoIdentificacionExtranjero = $opciones[ 'tipoIdentificacion' ];
		$opciones[ 'identificacion' ]   = str_replace(".","", $opciones[ 'identificacion' ]);
		$identificacionExtranjero       = $opciones[ 'identificacion' ];
		$idEmpresa                      = $opciones[ 'idEmpresa' ];
		$esEditarPnatural               = false;
		$fechaAjax                      = new \DateTime($opciones[ 'fecha' ]);

        $arrayOpciones = array(
            'idTipoIdentificacionExtranjero' => $idTipoIdentificacionExtranjero,
            'identificacionExtranjero' => $identificacionExtranjero,
            'idEmpresa' => $idEmpresa,
        );

		/**
		 * Verifica documento en la base de datos, si la respuesta es true no
		 * genera una nueva entidad en la tabla Persona de caso contrario actualiza
		 * los datos.
		 */
		if ($this->buscarPacienteService->validarIdentificadorUnicoPorEmpresa($arrayOpciones)) {
            $oPersona = $this->buscarPacienteService->findObjectPersona($arrayOpciones);
			$esEditarPnatural = true;
		} else {

			$oPersona = new Persona();

			$esEditarPnatural = $esEditarPnatural;
		}

		if ( $idTipoIdentificacionExtranjero == 1 ) {

			$oPersona->setrutPersona($opciones[ 'rut' ]);
			$oPersona->setdigitoVerificador($opciones[ 'dv' ]);
		} else {

			$oPersona->setrutPersona(0);
			$oPersona->setdigitoVerificador(0);
		}

		$idTipoIdentificacionExtranjero = $em->getReference('RebsolHermesBundle:TipoIdentificacionExtranjero', $idTipoIdentificacionExtranjero);

		$oPersona->setidTipoIdentificacionExtranjero($idTipoIdentificacionExtranjero);
		$oPersona->setIdentificacionExtranjero($identificacionExtranjero);
		$oPersona->settelefonoMovil($opciones[ 'cel' ]);
		$oPersona->settelefonoTrabajo($opciones[ 'trabajo' ]);
		$oPersona->settelefonoFijo($opciones[ 'fijo' ]);
		$oPersona->setcorreoElectronico( $opciones[ 'email' ] ? $opciones[ 'email' ] : NULL );

		$oEmpresa = $em->getReference('RebsolHermesBundle:Empresa', $idEmpresa);
		$oPersona->setidEmpresa($oEmpresa);
		$em->persist($oPersona);

		if ( $esEditarPnatural ) {

			$oPnatural = $em->getRepository('RebsolHermesBundle:Pnatural')->findOneBy(
				array(
					'idPersona' => $oPersona->getId()
					)
				);
		} else {

			$oPnatural = new Pnatural();
		}

		$oComuna = null;


		if ($opciones[ 'direccion' ] || $opciones[ 'numero' ] || $opciones[ 'resto' ] || $opciones[ 'pais' ]) {
			$fechahoy          = new \DateTime();
			$timestamp         = strtotime($fechahoy->format('Y-m-d H:i:s'));
			$oPersonaDomicilio = new PersonaDomicilio();

			$oPersonaDomicilio->setIdPersona($oPersona);
			$oPersonaDomicilio->setTimestampFecha($timestamp);
			$oPersonaDomicilio->setFechaDomicilio(new \DateTime('now'));
			$oPersonaDomicilio->setdireccion($opciones[ 'direccion' ]);
			$oPersonaDomicilio->setnumero($opciones[ 'numero' ]);
			$oPersonaDomicilio->setrestoDireccion($opciones[ 'resto' ]);

            if ($habilitarPaisExtranjero === 1 && ($idTipoIdentificacionExtranjero->getId() === 2) || $idTipoIdentificacionExtranjero->getId() === 4) {
                if ($opciones[ 'pais' ]) {

                    $oPais = $em->getRepository('RebsolHermesBundle:Pais')->findOneBy( array(
                            'id' => $opciones[ 'pais' ]
                        )
                    );

                    $oPersonaDomicilio->setIdPais($oPais);
                }

            } else {
                if ($opciones[ 'comuna' ]) {

                    $oComuna = $em->getRepository('RebsolHermesBundle:Comuna')->findOneBy( array(
                            'id' => $opciones[ 'comuna' ]
                        )
                    );

                    $oPersonaDomicilio->setIdComuna($oComuna);
                }
            }

            $em->persist($oPersonaDomicilio);
		}

		$oPnatural->setidPersona($oPersona);
		$oPnatural->setnombrePnatural($opciones[ 'nombre' ]);
		$oPnatural->setapellidoPaterno($opciones[ 'apep' ]);
		$oPnatural->setapellidoMaterno($opciones[ 'apem' ]);

		$oSexo = $em->getReference('RebsolHermesBundle:Sexo', $opciones[ 'sexo' ]);
		$oPnatural->setidSexo($oSexo);

		$oPnatural->setnumeroHermanoGemelo(0);
		$oPnatural->setfechaNacimiento($fechaAjax);
		$em->persist($oPnatural);

		if($opciones[ 'idReserva' ]){

			$oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($opciones[ 'idReserva' ]);
		} else {

			$oReservaAtencion = new ReservaAtencion();
		}

		if ( $idTipoIdentificacionExtranjero->getId() == 1 ) {

			$oReservaAtencion->setRutPaciente($opciones[ 'rut' ]);
			$oReservaAtencion->setDigitoVerificadorPaciente($opciones[ 'dv' ]);
		} else {

			$oReservaAtencion->setRutPaciente(0);
			$oReservaAtencion->setDigitoVerificadorPaciente(0);
		}

		$oReservaAtencion->setnombres($opciones[ 'nombre' ]);
		$oReservaAtencion->setapellidoPaterno($opciones[ 'apep' ]);
		$oReservaAtencion->setapellidoMaterno($opciones[ 'apem' ]);
		$oReservaAtencion->setidSexo($oSexo);
		$oReservaAtencion->setdireccion($opciones[ 'direccion' ]);
		$oReservaAtencion->setnumero($opciones[ 'numero' ]);
		$oReservaAtencion->setrestoDireccion($opciones[ 'resto' ]);
        if ($habilitarPaisExtranjero === 1 && ($idTipoIdentificacionExtranjero === 2 || $idTipoIdentificacionExtranjero === 4)) {
            $oReservaAtencion->setIdPais($oPais);
        } else {
            $oReservaAtencion->setIdComuna($oComuna);
        }
		$oReservaAtencion->setfechaNacimiento($fechaAjax);
		$oReservaAtencion->setcorreoElectronico($opciones[ 'email' ] ? $opciones[ 'email' ] : NULL );
		$oReservaAtencion->settelefonoMovil($opciones[ 'cel' ]);
		$oReservaAtencion->settelefonoContacto($opciones[ 'trabajo' ]);
		$oReservaAtencion->settelefonoFijo($opciones[ 'fijo' ]);
		$oReservaAtencion->setidTipoIdentificacionExtranjero($idTipoIdentificacionExtranjero);
		$oReservaAtencion->setIdentificacionExtranjero($identificacionExtranjero);
		$em->persist($oReservaAtencion);

		try {

			$em->flush();

			return true;
		} catch (\Exception $e) {

			return false;
		}
	}

	/**
	 * [verificarDocumento description]
	 * @param  array  $opciones [description]
	 * @return [type]           [description]
	 */
	protected function verificarDocumento( array $opciones ) {

		$queryString = 'SELECT persona.id 
		FROM RebsolHermesBundle:Persona persona
		WHERE persona.idEmpresa                    = :idEmpresa
		AND persona.idTipoIdentificacionExtranjero = :idTipoIdentificacionExtranjero
		AND persona.identificacionExtranjero       = :identificacionExtranjero';

		$query = $this->em->createQuery($queryString);

		$query->setParameters(
			array(
				'idEmpresa'                        => $opciones[ 'idEmpresa' ]
				, 'idTipoIdentificacionExtranjero' => $opciones[ 'extranjero' ]
				, 'identificacionExtranjero'       => $opciones[ 'identificacion' ]
				)
			);

		if (count($query->getArrayResult()) > 0) {

			return true;
		}

		return false;
	}
}
