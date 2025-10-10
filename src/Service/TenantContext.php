<?php

namespace App\Service;

use App\Entity\Tenant;

class TenantContext
{
    private ?Tenant $currentTenant = null;
    private ?string $currentSubdomain = null;
    
    public function setCurrentTenant(?Tenant $tenant): void
    {
        $this->currentTenant = $tenant;
        $this->currentSubdomain = $tenant?->getSubdomain();
    }
    
    public function getCurrentTenant(): ?Tenant
    {
        return $this->currentTenant;
    }
    
    public function getCurrentSubdomain(): ?string
    {
        return $this->currentSubdomain;
    }
    
    public function hasCurrentTenant(): bool
    {
        return $this->currentTenant !== null;
    }
    
    public function extractSubdomain(string $host): ?string
    {
        // Extraer subdominio de URLs como: clinica1.melisa.com, hospital1.melisa
        $parts = explode('.', $host);
        
        // Si tiene al menos 2 partes (subdomain.domain)
        if (count($parts) >= 2) {
            return $parts[0];
        }
        
        return null;
    }
    
    public function isValidTenantSubdomain(string $subdomain): bool
    {
        // Validar que el subdominio tenga formato válido
        return preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]{0,61}[a-zA-Z0-9]$/', $subdomain);
    }
}