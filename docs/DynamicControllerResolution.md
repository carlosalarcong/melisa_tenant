# Dynamic Controller Resolution System

## 📋 Resumen

El sistema de resolución dinámica de controladores permite que la aplicación multi-tenant resuelva automáticamente controladores específicos por tenant sin configuración manual. Utiliza un patrón de EventSubscriber + Service para interceptar requests y redirigir a controladores personalizados.

## 🏗️ Arquitectura

### Componentes principales:

1. **`DynamicControllerSubscriber`** - EventSubscriber que intercepta requests
2. **`DynamicControllerResolver`** - Service que contiene la lógica de resolución
3. **`TenantContext`** - Proporciona información del tenant actual

## 🔄 Flujo de ejecución completo

### 1. Request inicial
```
Usuario visita: https://melisahospital.com/dashboard
```

### 2. Symfony resuelve ruta básica
```php
// routes.yaml o anotaciones
#[Route('/dashboard', name: 'app_dashboard')]
// Controlador inicial: App\Controller\Dashboard\Default\DefaultController::index
```

### 3. DynamicControllerSubscriber intercepta
```php
// src/EventSubscriber/DynamicControllerSubscriber.php
public function onKernelRequest(RequestEvent $event): void
{
    $originalController = $request->attributes->get('_controller');
    // "App\Controller\Dashboard\Default\DefaultController::index"
    
    $tenant = $this->tenantContext->getCurrentTenant();
    $tenantSubdomain = $tenant['subdomain']; // "melisahospital"
    
    // Verificar si debe resolverse dinámicamente
    if ($this->shouldResolveDynamically($originalController, $tenantSubdomain)) {
        // Llamar al resolver
        $resolvedController = $this->controllerResolver->resolveControllerFromRoute(
            $originalController,
            $tenantSubdomain
        );
        
        // Actualizar el controlador en el request
        $request->attributes->set('_controller', $resolvedController);
    }
}
```

### 4. DynamicControllerResolver resuelve
```php
// src/Service/DynamicControllerResolver.php
public function resolveControllerFromRoute(string $originalController, string $tenantSubdomain): string
{
    // Analiza el controlador original
    [$originalClass, $method] = explode('::', $originalController);
    $classParts = explode('\\', $originalClass);
    
    // Extrae componentes
    $baseNamespace = "App\\Controller";
    $controllerType = "Dashboard"; 
    $controllerName = "DefaultController";
    $tenantKey = "Melisahospital";
    
    // Genera patrones de búsqueda por prioridad
    $dynamicPatterns = [
        "App\\Controller\\Dashboard\\Melisahospital\\DefaultController", // ✅ Este existe
        "App\\Controller\\Melisahospital\\DefaultController",
        "App\\Controller\\Dashboard\\Default\\DefaultController", 
        $originalClass // Controlador original como fallback
    ];
    
    // Encuentra el primero que exista
    foreach ($dynamicPatterns as $pattern) {
        if (class_exists($pattern) && method_exists($pattern, $method)) {
            return $pattern . '::' . $method;
        }
    }
}
```

### 5. Symfony ejecuta controlador resuelto
```php
// src/Controller/Dashboard/Melisahospital/DefaultController.php
class DefaultController extends AbstractDashboardController
{
    public function index(Request $request): Response
    {
        // Lógica específica del hospital
        return $this->renderDashboard('melisahospital', [...]);
    }
}
```

## 🎯 Lógica de filtrado

### ❌ **NO se resuelven dinámicamente:**
```php
private function shouldResolveDynamically(string $controller, string $tenantSubdomain): bool
{
    // Controladores ya específicos del tenant
    if (str_contains($controller, ucfirst($tenantSubdomain))) {
        return false;
    }
    
    // Controladores de sistema
    $systemControllers = [
        'App\\Controller\\LoginController',
        'App\\Controller\\SecurityController', 
        'App\\Controller\\LocaleController',
        'Symfony\\',
    ];
    
    // Controladores centrales (mantenedores)
    $centralControllers = [
        'App\\Controller\\Mantenedores\\Basico\\',
        'App\\Controller\\Mantenedores\\',
    ];
}
```

### ✅ **SÍ se resuelven dinámicamente:**
- Cualquier controlador bajo `App\Controller\` que NO esté en las exclusiones
- Esto hace al sistema escalable: nuevos controladores automáticamente funcionan

## 🔍 Patrones de resolución

### DynamicControllerResolver tiene 3 métodos principales:

#### 1. `resolve()` - Resolución basada en patrones de configuración
```php
public function resolve(Request $request): callable
```
- Usa parámetros como `_controller_pattern`, `_fallback_controller`
- Para rutas con configuración explícita

#### 2. `resolveController()` - Resolución por parámetros
```php
public function resolveController(string $subdomain, string $controller, string $action = 'index'): string
```
- Múltiples patrones de búsqueda jerárquicos
- Para llamadas programáticas

#### 3. `resolveControllerFromRoute()` - Resolución automática (usado por Subscriber)
```php
public function resolveControllerFromRoute(string $originalController, string $tenantSubdomain): string
```
- Analiza automáticamente el controlador original
- Genera patrones dinámicos sin configuración

## 📁 Estructura de controladores soportada

```
src/Controller/
├── Dashboard/
│   ├── Default/
│   │   └── DefaultController.php          # Fallback general
│   ├── Melisahospital/
│   │   └── DefaultController.php          # Hospital específico ✅
│   └── Melisalacolina/
│       └── DefaultController.php          # Clínica específica ✅
├── Mantenedores/
│   └── Basico/
│       ├── PaisController.php             # Central - NO se resuelve ❌
│       └── RegionController.php           # Central - NO se resuelve ❌
├── Reportes/
│   ├── Default/
│   │   └── DefaultController.php          # Fallback
│   └── Melisahospital/
│       └── DefaultController.php          # Hospital específico ✅
└── LoginController.php                    # Sistema - NO se resuelve ❌
```

## 🚀 Ventajas del sistema

### 1. **Automático y escalable**
- Nuevos controladores automáticamente funcionan con multi-tenant
- Zero configuración manual por ruta
- Solo necesitas crear la estructura de carpetas

### 2. **Flexible y robusto**
- Múltiples patrones de fallback
- Logging detallado para debugging
- Manejo de errores graceful

### 3. **Separación de responsabilidades**
- Subscriber: Decide QUÉ resolver
- Resolver: Decide CÓMO resolver
- Cada clase tiene una responsabilidad clara

### 4. **Performance optimizado**
- Solo se ejecuta cuando es necesario
- Caché de resolución implícito (class_exists)
- Patrones ordenados por probabilidad

## 🔧 Configuración

### Registrar el EventSubscriber
```yaml
# config/services.yaml
App\EventSubscriber\DynamicControllerSubscriber:
    tags:
        - { name: kernel.event_subscriber }
```

### Prioridad de ejecución
```php
public static function getSubscribedEvents(): array
{
    return [
        // Ejecutar después del LocaleListener pero antes del controlador
        KernelEvents::REQUEST => [['onKernelRequest', 15]],
    ];
}
```

## 🐛 Debugging

### Ver logs de resolución
```bash
tail -f var/log/dev.log | grep "Controlador resuelto dinámicamente"
```

### Método de debug disponible
```php
$debugInfo = $this->controllerResolver->getDebugInfo('melisahospital');
// Retorna información sobre controladores disponibles, paths, etc.
```

## 📝 Ejemplo práctico

### Crear nuevo controlador específico por tenant:

1. **Crear estructura**:
```bash
mkdir -p src/Controller/Facturas/Melisahospital
```

2. **Crear controlador**:
```php
// src/Controller/Facturas/Melisahospital/DefaultController.php
namespace App\Controller\Facturas\Melisahospital;

class DefaultController 
{
    #[Route('/facturas', name: 'app_facturas')]
    public function index(): Response
    {
        // Lógica específica de facturación para hospital
    }
}
```

3. **¡Listo!** - El sistema automáticamente:
   - Detecta que `App\Controller\Facturas\` debe resolverse
   - Encuentra el controlador específico para melisahospital
   - Redirige automáticamente sin configuración adicional

## ⚠️ Consideraciones importantes

### Templates NO se resuelven dinámicamente
- El DynamicControllerResolver solo maneja controladores
- Cada controlador debe resolver sus propios templates según su lógica
- Esto proporciona mayor flexibilidad y control

### Mantenedores son centrales
- Los controladores bajo `App\Controller\Mantenedores\` NO se resuelven
- Son compartidos entre todos los tenants
- Para funcionalidad específica por tenant, usar otras estructuras

### Orden de prioridad importa
- Los patrones se evalúan en orden de prioridad
- El primer controlador encontrado se usa
- Estructura jerárquica permite overrides específicos