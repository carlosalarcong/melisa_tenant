<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Organization;
use App\Entity\Tenant\License;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Servicio para validación de licencias disponibles
 * 
 * IMPORTANTE: Validaciones atómicas para prevenir condiciones de carrera
 */
class LicenseValidationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private int $warningThreshold = 5
    ) {}

    /**
     * Verificar si hay licencias disponibles para crear/activar usuarios
     * 
     * @param Organization $tenant Tenant a verificar
     * @return bool True si hay licencias disponibles
     */
    public function hasAvailableLicenses(Organization $tenant): bool
    {
        $info = $this->getLicenseInfo($tenant);
        return $info['available'] > 0;
    }

    /**
     * Obtener información completa de licencias
     * 
     * @param Organization $tenant
     * @return array ['total', 'used', 'available', 'needsWarning']
     */
    public function getLicenseInfo(Organization $tenant): array
    {
        $conn = $this->em->getConnection();
        
        // Query atómico con FOR UPDATE para prevenir race conditions
        $sql = "
            SELECT 
                l.cantidad_licencias as total,
                COUNT(DISTINCT u.id_usuario_rebsol) as used
            FROM licencia l
            LEFT JOIN persona p ON p.id_empresa = l.id_empresa
            LEFT JOIN usuarios_rebsol u ON u.id_persona = p.id_persona 
                AND u.id_estado_usuario = (
                    SELECT id_estado FROM estado WHERE nombre = 'ACTIVO' LIMIT 1
                )
            WHERE l.id_empresa = :tenantId
            GROUP BY l.cantidad_licencias
            FOR UPDATE
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['tenantId' => $tenant->getId()]);
        $data = $result->fetchAssociative();
        
        if (!$data) {
            $this->logger->warning('No se encontró información de licencias', [
                'tenantId' => $tenant->getId()
            ]);
            return [
                'total' => 0,
                'used' => 0,
                'available' => 0,
                'needsWarning' => true
            ];
        }
        
        $total = (int) $data['total'];
        $used = (int) $data['used'];
        $available = $total - $used;
        $needsWarning = $available <= $this->warningThreshold;
        
        if ($needsWarning && $available > 0) {
            $this->logger->warning('Pocas licencias disponibles', [
                'tenantId' => $tenant->getId(),
                'available' => $available,
                'threshold' => $this->warningThreshold
            ]);
        }
        
        return [
            'total' => $total,
            'used' => $used,
            'available' => $available,
            'needsWarning' => $needsWarning
        ];
    }

    /**
     * Validar que se puede crear N usuarios
     * 
     * @param Organization $tenant
     * @param int $count Cantidad de usuarios a crear
     * @return bool
     */
    public function canCreateUsers(Organization $tenant, int $count): bool
    {
        $info = $this->getLicenseInfo($tenant);
        return $info['available'] >= $count;
    }

    /**
     * Obtener el límite de licencias del tenant
     * 
     * @param Organization $tenant
     * @return int
     */
    public function getLicenseLimit(Organization $tenant): int
    {
        $license = $this->em->getRepository(License::class)
            ->findOneBy(['organization' => $tenant]);
        
        return $license ? $license->getQuantity() : 0;
    }
}
