<?php

namespace Rebsol\RecaudacionBundle\Repository;

use Rebsol\ComercialBundle\Repository\DefaultRepository;


/**
 * Class  RchRecetaRepository
 * @package  \Rebsol\CajaBundle\Repository
 * @author   sDelgado
 * Participantes: [ sDelgado ]
 * Fechas de Creación: [  17/12/15  ]
 * Fecha de Actualización: [ ]
 */
class RchRecetaRepository extends DefaultRepository{

	public function verDetalleArticulos($idReceta) {

		/**
		 * [$stringDql Consultando a la tabla RCH_RECETA por rut ($idReceta)]
		 * @var int $idReceta
		 */
		$stringDql = 'SELECT paciente.id      AS idPaciente,
		rChReceta.id                          AS idReceta,
		bodega.id                             AS idBodega,
		rChRecetaDetalle.id                   AS idrChRecetaDetalle,
		rChRecetaDetalle.cantidadSolicitado   AS cantidadSolicitado,
		rChReceta.fechaAtencion               AS fechaAtencion,
		articulo.nombre                       AS nombreArticulo,
		articulo.id                           AS idArticulo,
		articulo.codigo                       AS codigoArticulo,
		prevision.id                          AS idConvenio,
		prevision.copago                      AS copagoConvenio,
		previsionDos.id                       AS idFinanciador,
		previsionDos.copago                   AS copagoFinanciador,
		sucursal.id                           AS idSucursal
		FROM RebsolHermesBundle:RchReceta rChReceta
		LEFT JOIN rChReceta.idBodega as bodega
		LEFT JOIN RebsolHermesBundle:RchRecetaDetalle rChRecetaDetalle WITH ( rChRecetaDetalle.idRchReceta = rChReceta.id )
		LEFT JOIN RebsolHermesBundle:Empresa empresa        WITH ( rChReceta.idEmpresa                     = empresa.id   )
		LEFT JOIN RebsolHermesBundle:Articulo articulo      WITH ( rChRecetaDetalle.idArticulo             = articulo.id  )
		LEFT JOIN RebsolHermesBundle:Paciente paciente      WITH ( rChReceta.idPaciente                    = paciente.id  )
		LEFT JOIN RebsolHermesBundle:Prevision prevision    WITH ( paciente.idConvenio                     = prevision.id )
		JOIN RebsolHermesBundle:Prevision previsionDos      WITH ( paciente.idFinanciador                  = previsionDos.id )
		LEFT JOIN RebsolHermesBundle:Servicio servicio      WITH ( rChReceta.idServicioSolicitud           = servicio.id )
		LEFT JOIN RebsolHermesBundle:Unidad unidad          WITH ( servicio.idUnidad                       = unidad.id   )
		LEFT JOIN RebsolHermesBundle:Sucursal sucursal      WITH ( unidad.idSucursal                       = sucursal.id )
		WHERE 1             = 1
		AND rChReceta.id    = :idReceta
		AND rChReceta.esGes = :esGes
		AND empresa.id      = :idEmpresa';


		$query          = $this->_em->createQuery($stringDql);
		$query->setParameter('idReceta', $idReceta);
		$query->setParameter('esGes', 0);
		$query->setParameter('idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin'));

		$datosRchReceta = $query->getArrayResult($query);

		return $datosRchReceta;

	}

	/**
	 * [$stringDql Consultando a la tabla PRECIO_ARTICULO_SUCURSAL
	 * por idArticulo ($arrayOpciones['idArticulo'])]
	 * @var int $idReceta
	 */
	public function precioArticuloSucursal($idArticulo) {

		$stringDql = 'SELECT articulo.id AS idArticulo,
		precioArticuloSucursal.valorPrivado AS valorPrivado
		FROM RebsolHermesBundle:PrecioArticuloSucursal precioArticuloSucursal
		LEFT JOIN precioArticuloSucursal.idArticulo articulo
		LEFT JOIN RebsolHermesBundle:Sucursal sucursal  WITH ( precioArticuloSucursal.idSucursal = sucursal.id )
		LEFT JOIN sucursal.idEmpresa empresa
		WHERE 1         = 1
		AND articulo.id = :idArticulo
		AND empresa.id  = :idEmpresa';

		$query          = $this->_em->createQuery($stringDql);
		$query->setParameter('idArticulo', $idArticulo);
		$query->setParameter('idEmpresa', $this->obtenerParametroSesion('idEmpresaLogin'));

		$datosArrayPrecioArtSucursal = $query->getArrayResult($query);

		return $datosArrayPrecioArtSucursal;

	}


}
