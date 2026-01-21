<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DashboardExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('include_tenant_component', [$this, 'includeTenantComponent'], [
                'needs_environment' => true,
                'is_safe' => ['html']
            ]),
        ];
    }

    /**
     * Include a component with tenant-specific fallback.
     * 
     * Search order:
     * 1. templates/dashboard/tenants/{tenant}/{component}
     * 2. templates/dashboard/components/{component}
     * 
     * @param Environment $twig
     * @param string $componentName Component filename (e.g., '_welcome_banner.html.twig')
     * @param array $context Variables to pass to the component
     * @return string Rendered HTML
     */
    public function includeTenantComponent(Environment $twig, string $componentName, array $context = []): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $tenantName = $request?->attributes->get('_tenant_name') ?? 'default';

        // Try tenant-specific template first
        $tenantTemplate = sprintf('dashboard/tenants/%s/%s', $tenantName, $componentName);
        
        if ($this->templateExists($twig, $tenantTemplate)) {
            return $twig->render($tenantTemplate, $context);
        }

        // Fallback to default component
        $defaultTemplate = sprintf('dashboard/components/%s', $componentName);
        
        if ($this->templateExists($twig, $defaultTemplate)) {
            return $twig->render($defaultTemplate, $context);
        }

        // Component not found
        return sprintf('<!-- Component not found: %s -->', htmlspecialchars($componentName));
    }

    /**
     * Check if a template exists.
     * 
     * @param Environment $twig
     * @param string $template
     * @return bool
     */
    private function templateExists(Environment $twig, string $template): bool
    {
        $loader = $twig->getLoader();
        return $loader->exists($template);
    }
}
