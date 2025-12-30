<?php

namespace App\Repository;

use App\Entity\Tenant\DocumentBatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * DocumentBatchRepository - Repositorio para talonarios de documentos
 * Mock temporal hasta conectar base de datos Legacy
 */
class DocumentBatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentBatch::class);
    }

    /**
     * findBy - Busca talonarios por criterios
     * Mock: Retorna array con un talonario simulado
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        // Mock: Simular talonario activo para permitir flujo
        $mockBatch = new DocumentBatch();
        $mockBatch->setSubCompanyId(1);
        $mockBatch->setDocumentTypeId(1);
        $mockBatch->setStartNumber(1);
        $mockBatch->setEndNumber(999999);
        $mockBatch->setCurrentNumber(1);
        $mockBatch->setIsActive(true);
        
        return [$mockBatch];
    }
}
