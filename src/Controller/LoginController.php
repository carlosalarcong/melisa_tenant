<?php

namespace App\Controller;

use App\Service\TenantContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, TenantContext $tenantContext): Response
    {
        // Si el usuario ya está autenticado, redirigir al dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard_default');
        }

        // Obtener error de login si existe
        $error = $authenticationUtils->getLastAuthenticationError();

        // Obtener último username ingresado
        $lastUsername = $authenticationUtils->getLastUsername();

        // Obtener información del tenant actual
        $tenant = $tenantContext->getCurrentTenant();
        
        // Convertir el array a objeto para la plantilla
        $tenantObj = null;
        if ($tenant) {
            $tenantObj = (object) [
                'name' => $tenant['name'] ?? 'Unknown',
                'subdomain' => $tenant['subdomain'] ?? 'unknown'
            ];
        }

        return $this->render('login/form.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error ? $error->getMessageKey() : null,
            'tenant' => $tenantObj,
            'tenant_name' => $tenant ? $tenant['name'] : 'Tenant no identificado'
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Este método puede estar vacío - Symfony intercepta esta ruta
        // y maneja el logout automáticamente según security.yaml
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}