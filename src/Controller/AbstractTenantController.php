<?php

namespace App\Controller;

use App\Service\TenantContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractTenantController extends AbstractController
{
    protected TenantContext $tenantContext;

    public function __construct(TenantContext $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    protected function getCurrentTenant(): ?array
    {
        return $this->tenantContext->getCurrentTenant();
    }
    
    protected function getTenantTemplateDirectory(): string
    {
        $tenant = $this->getCurrentTenant();
        return $tenant ? strtolower($tenant['subdomain']) : 'default';
    }
    
    protected function renderTenantTemplate(string $template, array $parameters = []): Response
    {
        $tenantDir = $this->getTenantTemplateDirectory();
        $tenantTemplate = $tenantDir . '/' . $template;
        
        // Si existe plantilla personalizada, usarla
        if ($this->container->get('twig')->getLoader()->exists($tenantTemplate)) {
            return $this->render($tenantTemplate, $parameters);
        }
        
        // Fallback a plantilla por defecto
        return $this->render('default/' . $template, $parameters);
    }
    
    protected function addTenantToParameters(array $parameters = []): array
    {
        $tenant = $this->getCurrentTenant();
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();
        
        $parameters['tenant'] = $tenant;
        $parameters['tenant_name'] = $tenant['name'] ?? null;
        $parameters['subdomain'] = $tenant['subdomain'] ?? null;
        $parameters['logged_user'] = $session->get('member');
        
        return $parameters;
    }
    
    protected function renderWithTenant(string $template, array $parameters = []): Response
    {
        return $this->renderTenantTemplate($template, $this->addTenantToParameters($parameters));
    }
}