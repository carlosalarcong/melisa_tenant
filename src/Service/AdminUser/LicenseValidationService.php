<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Organization;
use App\Entity\Tenant\License;
use App\Repository\LicenseRepository;
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
        private LicenseRepository $licenseRepository,
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
        $usage = $this->licenseRepository->getLicenseUsage($tenant);
        
        if ($usage['total'] === 0) {
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
        
        $needsWarning = $usage['available'] <= $this->warningThreshold;
        
        if ($needsWarning && $usage['available'] > 0) {
            $this->logger->warning('Pocas licencias disponibles', [
                'tenantId' => $tenant->getId(),
                'available' => $usage['available'],
                'threshold' => $this->warningThreshold
            ]);
        }
        
        return [
            'total' => $usage['total'],
            'used' => $usage['used'],
            'available' => $usage['available'],
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
        $license = $this->licenseRepository->findByOrganization($tenant);
        
        return $license ? $license->getQuantity() : 0;
    }
}
