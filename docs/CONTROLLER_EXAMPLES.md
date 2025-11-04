# 🎯 Ejemplos de Controladores Multi-Tenant Transparentes

## 1️⃣ Controlador de Dashboard (Ya Implementado)

```php
<?php
// src/Controller/Dashboard/Melisahospital/DefaultController.php

namespace App\Controller\Dashboard\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractTenantAwareController
{
    #[Route('/dashboard', name: 'app_dashboard_melisahospital')]
    public function index(Request $request): Response
    {
        // ✨ Acceso automático al tenant - sin constructor
        $tenant = $this->getTenant();
        
        return $this->render('dashboard/melisahospital/index.html.twig', [
            'tenant' => $tenant,
            'tenant_name' => $this->getTenantName()
        ]);
    }
}
```

## 2️⃣ Controlador de Pacientes

```php
<?php
// src/Controller/Pacientes/Melisahospital/PacientesController.php

namespace App\Controller\Pacientes\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PacientesController extends AbstractTenantAwareController
{
    public function __construct(
        private Connection $connection  // ✅ Solo inyecta lo que REALMENTE necesitas
    ) {}

    #[Route('/pacientes', name: 'app_pacientes_melisahospital')]
    public function index(Request $request): Response
    {
        // ✨ Tenant disponible automáticamente
        $pacientes = $this->getPacientes();
        
        return $this->render('pacientes/melisahospital/index.html.twig', [
            'pacientes' => $pacientes,
            'tenant_name' => $this->getTenantName(),
            'subdomain' => $this->getTenantSubdomain()
        ]);
    }
    
    #[Route('/pacientes/{id}', name: 'app_pacientes_view_melisahospital')]
    public function view(int $id, Request $request): Response
    {
        $paciente = $this->getPaciente($id);
        
        return $this->render('pacientes/melisahospital/view.html.twig', [
            'paciente' => $paciente,
            'tenant' => $this->getTenant()  // ✨ Acceso directo
        ]);
    }
    
    private function getPacientes(): array
    {
        // Usar la BD del tenant automáticamente configurada
        $sql = 'SELECT * FROM pacientes WHERE activo = 1 LIMIT 100';
        return $this->connection->fetchAllAssociative($sql);
    }
    
    private function getPaciente(int $id): array
    {
        $sql = 'SELECT * FROM pacientes WHERE id = ?';
        return $this->connection->fetchAssociative($sql, [$id]);
    }
}
```

## 3️⃣ Controlador de Reportes

```php
<?php
// src/Controller/Reportes/Melisahospital/ReportesController.php

namespace App\Controller\Reportes\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportesController extends AbstractTenantAwareController
{
    #[Route('/reportes', name: 'app_reportes_melisahospital')]
    public function index(Request $request): Response
    {
        // ✨ Sin constructor, sin inyecciones especiales
        
        $reportes = [
            'total_pacientes' => $this->getTotalPacientes(),
            'citas_hoy' => $this->getCitasHoy(),
            'ingresos_mes' => $this->getIngresosMes()
        ];
        
        return $this->render('reportes/melisahospital/index.html.twig', [
            'reportes' => $reportes,
            'tenant' => $this->getTenant(),
            'fecha_generacion' => new \DateTime()
        ]);
    }
    
    #[Route('/reportes/pacientes', name: 'app_reportes_pacientes_melisahospital')]
    public function reportePacientes(Request $request): Response
    {
        // Usar el tenant para queries específicas
        $databaseName = $this->tenant['database_name'];
        
        return $this->render('reportes/melisahospital/pacientes.html.twig', [
            'tenant_name' => $this->getTenantName()
        ]);
    }
    
    private function getTotalPacientes(): int
    {
        // Lógica usando $this->tenant si es necesario
        return 1250;
    }
    
    private function getCitasHoy(): int
    {
        return 35;
    }
    
    private function getIngresosMes(): float
    {
        return 45000.00;
    }
}
```

## 4️⃣ API Controller

```php
<?php
// src/Controller/Api/Melisahospital/PacientesApiController.php

namespace App\Controller\Api\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pacientes', name: 'api_pacientes_')]
class PacientesApiController extends AbstractTenantAwareController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // ✨ Tenant disponible para filtrar datos por cliente
        
        $pacientes = [
            ['id' => 1, 'nombre' => 'Juan Pérez', 'rut' => '12345678-9'],
            ['id' => 2, 'nombre' => 'María García', 'rut' => '98765432-1']
        ];
        
        return $this->json([
            'success' => true,
            'tenant' => $this->getTenantSubdomain(),
            'data' => $pacientes
        ]);
    }
    
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(int $id, Request $request): JsonResponse
    {
        // Verificar que hay tenant válido
        if (!$this->hasTenant()) {
            return $this->json([
                'success' => false,
                'error' => 'Tenant no identificado'
            ], 400);
        }
        
        $paciente = ['id' => $id, 'nombre' => 'Juan Pérez', 'tenant' => $this->getTenantName()];
        
        return $this->json([
            'success' => true,
            'data' => $paciente
        ]);
    }
}
```

## 5️⃣ Controlador con Servicios Personalizados

```php
<?php
// src/Controller/Citas/Melisahospital/CitasController.php

namespace App\Controller\Citas\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use App\Service\CitasService;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CitasController extends AbstractTenantAwareController
{
    public function __construct(
        private CitasService $citasService,          // ✅ Servicios de negocio
        private NotificationService $notificationService  // ✅ Solo lo necesario
        // ❌ NO necesitas TenantContext ni DynamicControllerResolver
    ) {}

    #[Route('/citas', name: 'app_citas_melisahospital')]
    public function index(Request $request): Response
    {
        // ✨ Tenant inyectado automáticamente
        $citas = $this->citasService->getCitasPorTenant(
            $this->getTenantSubdomain()
        );
        
        return $this->render('citas/melisahospital/index.html.twig', [
            'citas' => $citas,
            'tenant' => $this->getTenant()
        ]);
    }
    
    #[Route('/citas/agendar', name: 'app_citas_agendar_melisahospital', methods: ['POST'])]
    public function agendar(Request $request): Response
    {
        $data = $request->request->all();
        
        // Incluir información del tenant automáticamente
        $data['tenant_id'] = $this->tenant['id'];
        $data['tenant_subdomain'] = $this->getTenantSubdomain();
        
        $cita = $this->citasService->crearCita($data);
        
        // Notificar usando el tenant correcto
        $this->notificationService->notificarCita(
            $cita,
            $this->getTenantName()
        );
        
        return $this->redirectToRoute('app_citas_melisahospital');
    }
}
```

## 6️⃣ Controlador con Formularios

```php
<?php
// src/Controller/Mantenedores/Melisahospital/MedicosController.php

namespace App\Controller\Mantenedores\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use App\Form\MedicoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractTenantAwareController
{
    #[Route('/mantenedores/medicos', name: 'app_medicos_melisahospital')]
    public function index(Request $request): Response
    {
        // ✨ Completamente transparente
        
        return $this->render('mantenedores/medicos/index.html.twig', [
            'tenant_name' => $this->getTenantName()
        ]);
    }
    
    #[Route('/mantenedores/medicos/nuevo', name: 'app_medicos_nuevo_melisahospital')]
    public function nuevo(Request $request): Response
    {
        $form = $this->createForm(MedicoType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Agregar tenant_id automáticamente
            $data['tenant_id'] = $this->tenant['id'];
            
            // Guardar médico...
            
            return $this->redirectToRoute('app_medicos_melisahospital');
        }
        
        return $this->render('mantenedores/medicos/nuevo.html.twig', [
            'form' => $form->createView(),
            'tenant' => $this->getTenant()
        ]);
    }
}
```

## 7️⃣ Controlador Fallback (Default)

```php
<?php
// src/Controller/Dashboard/Default/DefaultController.php

namespace App\Controller\Dashboard\Default;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Este controlador se usa automáticamente cuando no existe
 * un controlador específico para el tenant
 */
class DefaultController extends AbstractTenantAwareController
{
    #[Route('/dashboard', name: 'app_dashboard_default')]
    public function index(Request $request): Response
    {
        // ✨ Funciona para CUALQUIER tenant sin modificación
        
        $menuRoutes = [
            'dashboard' => ['url' => '/dashboard', 'label' => 'Dashboard'],
            'pacientes' => ['url' => '/pacientes', 'label' => 'Pacientes'],
            'citas' => ['url' => '/citas', 'label' => 'Citas'],
        ];
        
        return $this->render('dashboard/default.html.twig', [
            'tenant' => $this->getTenant(),
            'tenant_name' => $this->getTenantName(),
            'subdomain' => $this->getTenantSubdomain(),
            'menu_routes' => $menuRoutes
        ]);
    }
}
```

## 🎯 Patrón de Nomenclatura

```
Estructura recomendada:
src/Controller/
    ├── {Módulo}/                    # Pacientes, Citas, Reportes, etc.
    │   ├── {TenantName}/           # Melisahospital, Melisalacolina, etc.
    │   │   └── {Controller}.php    # Lógica específica del tenant
    │   └── Default/                # Fallback si no existe específico
    │       └── {Controller}.php
    └── AbstractTenantAwareController.php  # Base para todos

Ejemplo concreto:
src/Controller/
    ├── Pacientes/
    │   ├── Melisahospital/
    │   │   └── PacientesController.php  # Hospital tiene quirófanos
    │   ├── Melisalacolina/
    │   │   └── PacientesController.php  # Clínica no tiene quirófanos
    │   └── Default/
    │       └── PacientesController.php  # Funcionalidad básica
```

## ✅ Checklist para Nuevo Controlador

- [ ] Extender `AbstractTenantAwareController`
- [ ] Definir rutas con `#[Route()]`
- [ ] NO inyectar `TenantContext` en constructor
- [ ] NO inyectar `DynamicControllerResolver` en constructor
- [ ] Usar `$this->getTenant()` para acceder al tenant
- [ ] Usar `$this->getTenantName()` para el nombre
- [ ] Usar `$this->getTenantSubdomain()` para el subdomain
- [ ] Solo inyectar servicios de negocio necesarios

## 🚀 Ventajas

| Antes | Ahora |
|-------|-------|
| 15-20 líneas constructor | 0-5 líneas (solo servicios necesarios) |
| Inyectar 3-4 servicios framework | Solo servicios de negocio |
| Complejidad alta | Simplicidad máxima |
| Propenso a errores | Casi imposible equivocarse |
| Onboarding 2-3 días | Onboarding 30 minutos |
