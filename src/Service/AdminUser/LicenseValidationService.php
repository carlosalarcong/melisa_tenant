<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\License;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Psr\Log\LoggerInterface;

/**
 * Servicio para validación de licencias disponibles
 * 
 * IMPORTANTE: Validaciones atómicas para prevenir condiciones de carrera
 */
class LicenseValidationService
{
    public function __construct(
        private TenantEntityManager $em,
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
        // Obtener licencia del tenant
        $license = $this->em->getRepository(License::class)->findOneBy([
            'organization' => $tenant
        ]);
        
        if (!$license) {
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
        
        // Contar usuarios activos
        $usedCount = (int) $this->em->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Member::class, 'm')
            ->where('m.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();
        
        $total = $license->getQuantity();
        $available = max(0, $total - $usedCount);
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
            'used' => $usedCount,
            'available' => $available,
            'needsWarning' => $needsWarning,
            'can_create_user' => $available > 0
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
