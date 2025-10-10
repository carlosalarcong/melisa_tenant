<?php

namespace App\Controller;

use App\Service\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private TenantResolver $tenantResolver
    ) {}

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            return $this->handleLogin($request, $session);
        }
        
        // Resolver tenant desde subdomain para mostrar en el formulario
        $tenant = $this->tenantResolver->resolveTenantFromRequest($request);
        
        return $this->render('login/form.html.twig', [
            'error' => null,
            'username' => '',
            'tenant' => $tenant,
            'tenant_name' => $tenant ? $tenant['name'] : 'No se pudo determinar la empresa'
        ]);
    }

    private function handleLogin(Request $request, SessionInterface $session): Response
    {
        $username = trim($request->request->get('username', ''));
        $password = trim($request->request->get('password', ''));
        $rememberMe = $request->request->get('remember_me', false);

        // Validar datos básicos
        if (!$username || !$password) {
            return $this->render('login/form.html.twig', [
                'error' => 'Por favor complete todos los campos',
                'username' => $username,
                'tenant' => null,
                'tenant_name' => 'Error'
            ]);
        }

        try {
            // 1. Resolver tenant desde subdomain
            $tenant = $this->tenantResolver->resolveTenantFromRequest($request);
            
            if (!$tenant) {
                return $this->render('login/form.html.twig', [
                    'error' => 'No se pudo determinar la empresa desde la URL. Verifique el subdomain.',
                    'username' => $username,
                    'tenant' => null,
                    'tenant_name' => 'Error de configuración'
                ]);
            }

            // 2. Conectar a la BD del tenant
            $tenantConnection = $this->tenantResolver->createTenantConnection($tenant);
            
            // 3. Buscar usuario en la BD del tenant
            $userQuery = '
                SELECT id, username, password, first_name, last_name, email, is_active
                FROM member 
                WHERE username = ? AND is_active = true
            ';
            
            $userResult = $tenantConnection->executeQuery($userQuery, [$username]);
            $user = $userResult->fetchAssociative();
            
            if (!$user) {
                return $this->render('login/form.html.twig', [
                    'error' => 'Usuario no encontrado o inactivo en ' . $tenant['name'],
                    'username' => $username,
                    'tenant' => $tenant,
                    'tenant_name' => $tenant['name']
                ]);
            }

            // 4. Verificar contraseña
            if (!password_verify($password, $user['password'])) {
                return $this->render('login/form.html.twig', [
                    'error' => 'Contraseña incorrecta',
                    'username' => $username,
                    'tenant' => $tenant,
                    'tenant_name' => $tenant['name']
                ]);
            }

            // 5. Login exitoso - Crear sesión
            $session->set('logged_in', true);
            $session->set('user_id', $user['id']);
            $session->set('username', $user['username']);
            $session->set('user_name', $user['first_name'] . ' ' . $user['last_name']);
            $session->set('tenant_id', $tenant['id']);
            $session->set('tenant_name', $tenant['name']);
            $session->set('tenant_slug', $tenant['subdomain']);
            $session->set('database_name', $tenant['database_name']);
            $session->set('tenant', $tenant); // Array completo del tenant para TenantController

            // 6. Redirección al dashboard en el mismo servicio
            $response = $this->redirectToRoute('app_dashboard', ['controller' => 'dashboard']);
            
            if ($rememberMe) {
                $this->setRememberMeCookies($response, $tenant, $user);
            }

            return $response;

        } catch (\Exception $e) {
            return $this->render('login/form.html.twig', [
                'error' => 'Error del sistema: ' . $e->getMessage(),
                'username' => $username,
                'tenant' => null,
                'tenant_name' => 'Error de conexión'
            ]);
        }
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        
        $response = $this->redirectToRoute('app_login');
        
        // Limpiar cookies de "Remember Me"
        $response->headers->clearCookie('remember_tenant');
        $response->headers->clearCookie('remember_user');
        $response->headers->clearCookie('remember_token');
        
        return $response;
    }

    private function setRememberMeCookies(Response $response, array $tenant, array $user): void
    {
        // Codificar datos
        $tenantData = base64_encode(json_encode([
            'id' => $tenant['id'],
            'slug' => $tenant['subdomain'],
            'name' => $tenant['name'],
            'database_name' => $tenant['database_name']
        ]));
        
        $userData = base64_encode(json_encode([
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['first_name'] . ' ' . $user['last_name']
        ]));
        
        // Crear token de seguridad
        $token = hash('sha256', $tenantData . $userData . 'melisa_secret_2025');
        
        // Cookies que duran 30 días
        $expiry = time() + (30 * 24 * 60 * 60);
        
        $response->headers->setCookie(new Cookie('remember_tenant', $tenantData, $expiry));
        $response->headers->setCookie(new Cookie('remember_user', $userData, $expiry));
        $response->headers->setCookie(new Cookie('remember_token', $token, $expiry));
    }

    #[Route('/api/tenants', name: 'app_tenants_list', methods: ['GET'])]
    public function tenantsList(): JsonResponse
    {
        try {
            $tenants = $this->tenantResolver->getAllActiveTenants();
            
            // Formatear los tenants para el frontend
            $formattedTenants = array_map(function($tenant) {
                return [
                    'name' => $tenant['name'],
                    'subdomain' => $tenant['subdomain'],
                    'url' => 'http://' . $tenant['subdomain'] . '.melisaupgrade.prod:8081/login'
                ];
            }, $tenants);
            
            return new JsonResponse($formattedTenants);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error al obtener tenants'], 500);
        }
    }
}