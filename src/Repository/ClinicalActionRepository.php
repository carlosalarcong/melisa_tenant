<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ClinicalActionRepository (migrado desde AccionClinicaRepository)
 */
class ClinicalActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // Mock: No entity mapping yet
        parent::__construct($registry, \stdClass::class);
    }

	public function obtenerRecargoHorario($dia, $hora, $idRelSucursalPrevision)
	{
		$fecha    = new \DateTime();
		$em       = $this->getManager();
		$query    = $em->createQuery("
			SELECT 
			rh.id          AS id,
			rh.horaInicio  AS horaInicio,
			rh.horaTermino AS horaTermino,
			rh.porcentaje  AS porcentaje

			FROM Rebsol\HermesBundle\Entity\RecargaHorario rh

			WHERE 1 = 1
			AND rh.idRelSucursalPrevision = :idRelSucursalPrevision
			AND rh.dia                    = :dia
			AND rh.idEstado               = 1
			");
		$query->setParameter('idRelSucursalPrevision', $idRelSucursalPrevision);
		$query->setParameter('dia', $dia);

		$respuesta = $query->getOneOrNullResult();

		if ($respuesta == null) {
			return false;
		}

		$arrReturn    = array();
		$mayorInicio  = $respuesta['horaInicio']->format('His') < $hora->format('His');
		$menorTermino = $respuesta['horaTermino']->format('His') > $hora->format('His');

		if ($mayorInicio and $menorTermino) {
			return false;
		}

		return $respuesta[ 'porcentaje' ];
	}

}
