<?php

namespace App\Security;

use App\Service\TenantResolver;
use App\Service\TenantContext;
use Hakam\MultiTenancyBundle\Event\SwitchDbEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Autenticador para el formulario de login
 * 
 * Maneja:
 * 1. Detección del tenant desde subdomain
 * 2. Cambio de BD al tenant correspondiente
 * 3. Autenticación del usuario
 * 4. Redirección post-login
 */
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private RouterInterface $router,
        private TenantResolver $tenantResolver,
        private TenantContext $tenantContext,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger
    ) {}

    /**
     * URL del formulario de login
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }

    /**
     * Procesa la autenticación
     */
    public function authenticate(Request $request): Passport
    {
        // Symfony Security espera campos con guion bajo
        $username = $request->request->get('_username', '');
        $password = $request->request->get('_password', '');
        $csrfToken = $request->request->get('_csrf_token');

        $this->logger->info('🔐 Intento de login', [
            'username' => $username,
            'host' => $request->getHost()
        ]);

        // 1. Resolver tenant desde subdomain
        $tenant = $this->resolveTenantFromRequest($request);
        
        if (!$tenant) {
            $this->logger->error('❌ Tenant no encontrado para el host', [
                'host' => $request->getHost()
            ]);
            
            throw new AuthenticationException('Tenant no encontrado para este dominio');
        }

        // 2. Guardar tenant en contexto
        $this->tenantContext->setCurrentTenant($tenant);

        // 3. Disparar evento para cambiar BD
        $this->switchToTenantDatabase($tenant);

        // 4. Guardar username en sesión para recordar
        $request->getSession()->set('_security.last_username', $username);
        $request->getSession()->set('tenant_slug', $tenant['subdomain']);
        $request->getSession()->set('tenant_id', $tenant['id']);

        // 5. Crear Passport con las credenciales
        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Resuelve el tenant desde el request
     */
    private function resolveTenantFromRequest(Request $request): ?array
    {
        try {
            $tenant = $this->tenantResolver->resolveTenantFromRequest($request);
            
            if ($tenant) {
                $this->logger->info('✅ Tenant resuelto', [
                    'id' => $tenant['id'],
                    'subdomain' => $tenant['subdomain'],
                    'database' => $tenant['database_name']
                ]);
            }
            
            return $tenant;
        } catch (\Exception $e) {
            $this->logger->error('❌ Error resolviendo tenant', [
                'error' => $e->getMessage(),
                'host' => $request->getHost()
            ]);
            return null;
        }
    }

    /**
     * Cambia la conexión a la BD del tenant
     */
    private function switchToTenantDatabase(array $tenant): void
    {
        $this->logger->info('🔄 Cambiando a BD del tenant', [
            'tenant_id' => $tenant['id'],
            'database' => $tenant['database_name']
        ]);

        $switchEvent = new SwitchDbEvent((string)$tenant['id']);
        $this->dispatcher->dispatch($switchEvent);

        $this->logger->info('✅ BD cambiada exitosamente');
    }

    /**
     * Redirección tras login exitoso
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var Member $user */
        $user = $token->getUser();
        
        $this->logger->info('🎉 Login exitoso', [
            'user' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);

        // Guardar información adicional en sesión
        $session = $request->getSession();
        $session->set('logged_in', true);
        
        if ($user instanceof \App\Entity\Member) {
            $session->set('user_id', $user->getId());
            $session->set('username', $user->getUsername());
        }

        // Si hay una URL objetivo guardada, redirigir ahí
        if ($targetPath = $this->getTargetPath($session, $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Sino, redirigir al dashboard
        return new RedirectResponse(
            $this->router->generate('app_dashboard_default')
        );
    }
}
