<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TenantContext
{
    private ?array $currentTenant = null;
    private ?string $currentSubdomain = null;
    private RequestStack $requestStack;
    
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    public function setCurrentTenant(?array $tenant): void
    {
        $this->currentTenant = $tenant;
        $this->currentSubdomain = $tenant['subdomain'] ?? null;
    }
    
    public function getCurrentTenant(): ?array
    {
        if ($this->currentTenant) {
            return $this->currentTenant;
        }
        
        // Intentar obtener de la sesión
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->hasSession()) {
            $session = $request->getSession();
            
            // Primero intentar obtener el array completo del tenant
            $tenantData = $session->get('tenant');
            
            if ($tenantData && is_array($tenantData)) {
                $this->setCurrentTenant($tenantData);
                return $this->currentTenant;
            }
            
            // Si no existe como array, intentar reconstruir desde claves individuales
            if ($session->has('tenant_id') || $session->has('tenant_name') || $session->has('tenant_slug')) {
                $reconstructedTenant = [
                    'id' => $session->get('tenant_id'),
                    'name' => $session->get('tenant_name', 'Melisa Clinic'),
                    'subdomain' => $session->get('tenant_slug', 'default'),
                    'database_name' => $session->get('database_name', ''),
                    'rut_empresa' => null, // Datos adicionales pueden ser null
                    'host' => 'localhost',
                    'host_port' => 3306,
                    'db_user' => 'melisa',
                    'db_password' => 'melisamelisa'
                ];
                
                $this->setCurrentTenant($reconstructedTenant);
                return $this->currentTenant;
            }
        }
        
        return null;
    }
    
    public function getCurrentSubdomain(): ?string
    {
        if ($this->currentSubdomain) {
            return $this->currentSubdomain;
        }
        
        $tenantData = $this->getCurrentTenant();
        return $tenantData['subdomain'] ?? null;
    }
    
    public function hasCurrentTenant(): bool
    {
        return $this->getCurrentTenant() !== null;
    }
    
    public function getCurrentTenantName(): ?string
    {
        $tenantData = $this->getCurrentTenant();
        return $tenantData['name'] ?? null;
    }
    
    public function getCurrentDatabaseName(): ?string
    {
        $tenantData = $this->getCurrentTenant();
        return $tenantData['database_name'] ?? null;
    }
}
