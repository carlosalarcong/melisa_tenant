# 🔄 Flujo Completo del Sistema Multi-Tenant Transparente

## 📊 Arquitectura de Componentes

```
┌─────────────────────────────────────────────────────────────────┐
│                    REQUEST ENTRANTE                              │
│         http://melisahospital.melisaupgrade.prod:8081/dashboard │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│  🔧 FASE 1: CONFIGURACIÓN DE BASE DE DATOS                      │
│  TenantConnectionListener (Priority: 1000)                      │
│  - Extrae subdomain del host: "melisahospital"                  │
│  - Cambia conexión DB de default → melisahospital               │
│  - Usa Reflection para modificar Connection::$params            │
│  - Doctrine reconecta automáticamente (lazy connection)         │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│  🎯 FASE 2: RESOLUCIÓN DE CONTROLADOR DINÁMICO                  │
│  DynamicControllerSubscriber (Priority: 15)                     │
│  - Lee _controller del request: "App\Controller\Dashboard..."   │
│  - Verifica si necesita resolución dinámica                     │
│  - Consulta TenantContext para obtener subdomain                │
│  - Llama a DynamicControllerResolver                            │
│  - Resuelve a controlador específico del tenant                 │
│  - Actualiza _controller en request                             │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│  💉 FASE 3: INYECCIÓN AUTOMÁTICA DE CONTEXTO                    │
│  TenantContextInjector (Priority: 10)                           │
│  - Detecta si controlador extends AbstractTenantAwareController │
│  - Obtiene tenant de TenantContext                              │
│  - Usa Reflection para inyectar propiedades:                    │
│    • $this->tenant = [...]                                      │
│    • $this->tenantSubdomain = "melisahospital"                  │
│    • $this->tenantName = "Melisa Hospital"                      │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│  🎬 FASE 4: EJECUCIÓN DEL CONTROLADOR                           │
│  Dashboard\Melisahospital\DefaultController::index()            │
│  - Tiene acceso automático a $this->tenant                      │
│  - Usa métodos helper: getTenant(), getTenantName()             │
│  - Renderiza template específico del tenant                     │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                    RESPONSE AL CLIENTE                           │
│         HTML renderizado con datos del tenant correcto           │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔍 Detalle por Componente

### 1️⃣ TenantConnectionListener
**Archivo**: `/src/EventListener/TenantConnectionListener.php`  
**Priority**: 1000 (ALTA - Ejecuta primero)  
**Tipo**: EventSubscriber  
**Evento**: `KernelEvents::REQUEST`

#### Responsabilidad
Cambiar la conexión de base de datos al tenant correcto **ANTES** de cualquier query.

#### Flujo Interno
```php
onKernelRequest() {
    1. Extraer host: "melisahospital.melisaupgrade.prod"
       ↓
    2. Parsear subdomain: "melisahospital"
       ↓
    3. Verificar si cambió el tenant actual
       ↓
    4. Cerrar conexión existente si está activa
       ↓
    5. Modificar Connection::$params['dbname']
       ↓
    6. Usar Reflection para inyectar nuevo dbname
       ↓
    7. Doctrine reconectará automáticamente en próximo query
}
```

#### Interacción con Otros
- ✅ **Usa**: `TenantResolver` (para validar tenant)
- ✅ **Usa**: `Doctrine\DBAL\Connection` (modifica dbname)
- ✅ **Usa**: `Reflection` para modificar propiedades privadas
- ⚠️ **NO interactúa** con controladores directamente

#### Estado
- Mantiene `$currentTenant` para evitar reconexiones innecesarias
- Usa fallback a "melisahospital" si no detecta tenant

---

### 2️⃣ DynamicControllerSubscriber
**Archivo**: `/src/EventSubscriber/DynamicControllerSubscriber.php`  
**Priority**: 15 (MEDIA - Después de routing, antes de ejecución)  
**Tipo**: EventSubscriber  
**Evento**: `KernelEvents::REQUEST`

#### Responsabilidad
Resolver qué controlador específico del tenant debe ejecutarse basado en patrones de jerarquía.

#### Flujo Interno
```php
onKernelRequest() {
    1. Leer _controller del request
       ↓
    2. Verificar si es string válido
       ↓
    3. Obtener tenant desde TenantContext
       ↓
    4. Verificar shouldResolveDynamically():
       • ¿Ya es específico del tenant? → NO resolver
       • ¿Es controlador de sistema? → NO resolver
       • ¿Es mantenedor central? → NO resolver
       • ¿Es App\Controller\*? → SÍ resolver
       ↓
    5. Llamar DynamicControllerResolver::resolveControllerFromRoute()
       ↓
    6. Si cambió, actualizar _controller en request
}
```

#### Patrones de Resolución
```php
// Ejemplo: Original = App\Controller\Dashboard\Default\DefaultController

// Busca en orden:
1. App\Controller\Dashboard\Melisahospital\DefaultController  ← ✅ EXISTE
2. App\Controller\Melisahospital\DashboardController
3. App\Controller\Dashboard\Default\DefaultController         ← FALLBACK

// El sistema elige el primero que exista
```

#### Interacción con Otros
- ✅ **Usa**: `TenantContext::getCurrentTenant()`
- ✅ **Usa**: `DynamicControllerResolver::resolveControllerFromRoute()`
- ✅ **Modifica**: `Request::attributes['_controller']`
- ⚠️ **NO modifica** la base de datos

#### Controladores Excluidos
```php
- LoginController        → Sistema central
- SecurityController     → Autenticación
- LocaleController       → Internacionalización
- Mantenedores\*         → Centralizados para todos
```

---

### 3️⃣ DynamicControllerResolver
**Archivo**: `/src/Service/DynamicControllerResolver.php`  
**Priority**: N/A (Es un servicio, no EventSubscriber)  
**Tipo**: Service  

#### Responsabilidad
Lógica centralizada de resolución de controladores con patrones de fallback complejos.

#### Métodos Principales

##### `resolveControllerFromRoute(string $original, string $tenant): string`
```php
// Input:  "App\Controller\Dashboard\Default\DefaultController::index", "melisahospital"
// Output: "App\Controller\Dashboard\Melisahospital\DefaultController::index"

Algoritmo:
1. Descomponer controller string:
   • Namespace base: App\Controller
   • Tipo: Dashboard
   • Tenant original: Default
   • Clase: DefaultController
   • Método: index

2. Generar patrones dinámicos:
   [
     "App\Controller\Dashboard\Melisahospital\DefaultController",
     "App\Controller\Melisahospital\DefaultController",
     "App\Controller\Dashboard\Default\DefaultController"
   ]

3. Verificar cada patrón:
   • class_exists(patrón)?
   • method_exists(patrón, método)?
   
4. Retornar primer match o fallback
```

##### `buildSearchPatterns(string $tenant, string $type, string $action): array`
```php
// Para tenant="melisahospital", type="Dashboard", action="index"

return [
    "App\Controller\Dashboard\Melisahospital\DefaultController",      // Específico jerárquico
    "App\Controller\Dashboard\Melisahospital\DashboardController",    // Con nombre
    "App\Controller\Melisahospital\DashboardController",              // Directo tenant
    "App\Controller\Melisahospital\DefaultController",                // Default tenant
    "App\Controller\Dashboard\Default\DefaultController",             // Fallback jerárquico
    "App\Controller\Dashboard\Default\DashboardController",           // Fallback con nombre
    "App\Controller\DashboardController",                             // Base directo
    "App\Controller\DefaultController"                                // Fallback absoluto
];
```

#### Interacción con Otros
- ✅ **Usa**: `TenantContext` para obtener tenant actual
- ✅ **Usa**: PHP `class_exists()` y `method_exists()`
- ✅ **Usado por**: `DynamicControllerSubscriber`
- ✅ **Usado por**: `LoginController` (para redirección)

#### Métodos Auxiliares
```php
getGuaranteedTenant()       → Nunca retorna null, siempre fallback
getCurrentTenantWithFallback() → Intentos de obtener tenant
generateRedirectRoute()     → Genera nombres de rutas dinámicamente
```

---

### 4️⃣ TenantContextInjector
**Archivo**: `/src/EventSubscriber/TenantContextInjector.php`  
**Priority**: 10 (BAJA - Justo antes de ejecutar controlador)  
**Tipo**: EventSubscriber  
**Evento**: `KernelEvents::CONTROLLER`

#### Responsabilidad
Inyectar automáticamente el contexto del tenant en controladores que extiendan `AbstractTenantAwareController`.

#### Flujo Interno
```php
onKernelController(ControllerEvent $event) {
    1. Obtener controlador del evento
       ↓
    2. Verificar si es array [objeto, 'método']
       ↓
    3. Extraer objeto del controlador
       ↓
    4. Verificar: instanceof AbstractTenantAwareController?
       ↓ NO → Salir (no aplica inyección)
       ↓ SÍ
    5. Obtener tenant desde TenantContext
       ↓
    6. Usar Reflection para inyectar propiedades:
       
       $reflection = new ReflectionClass($controller);
       
       // Inyectar $tenant
       $tenantProperty = $reflection->getProperty('tenant');
       $tenantProperty->setAccessible(true);
       $tenantProperty->setValue($controller, $tenant);
       
       // Inyectar $tenantSubdomain
       $subdomainProperty = $reflection->getProperty('tenantSubdomain');
       $subdomainProperty->setAccessible(true);
       $subdomainProperty->setValue($controller, $tenant['subdomain']);
       
       // Inyectar $tenantName
       $nameProperty = $reflection->getProperty('tenantName');
       $nameProperty->setAccessible(true);
       $nameProperty->setValue($controller, $tenant['name']);
       ↓
    7. Log de confirmación
}
```

#### Interacción con Otros
- ✅ **Usa**: `TenantContext::getCurrentTenant()`
- ✅ **Usa**: `Reflection` para modificar propiedades protected
- ✅ **Afecta**: Todos los `AbstractTenantAwareController`
- ⚠️ **NO afecta**: Controladores que no extiendan la clase base

#### Por qué Reflection?
```php
// Las propiedades son protected:
protected ?array $tenant;
protected ?string $tenantSubdomain;
protected ?string $tenantName;

// Reflection permite modificarlas desde fuera de la clase
// Sin necesidad de constructor o setters públicos
// Mantiene encapsulación pero permite inyección automática
```

---

### 5️⃣ AbstractTenantAwareController
**Archivo**: `/src/Controller/AbstractTenantAwareController.php`  
**Priority**: N/A (Clase base)  
**Tipo**: Abstract Class  

#### Responsabilidad
Proporcionar API uniforme para acceder al contexto del tenant en todos los controladores.

#### Propiedades Inyectadas (por TenantContextInjector)
```php
protected ?array $tenant = null;           // Inyectado automáticamente
protected ?string $tenantSubdomain = null; // Inyectado automáticamente
protected ?string $tenantName = null;      // Inyectado automáticamente
```

#### Métodos Públicos
```php
// Acceso a datos del tenant
getTenant(): array                    → Retorna tenant con fallback
getTenantSubdomain(): string          → Retorna subdomain o 'default'
getTenantName(): string               → Retorna nombre o 'Default Tenant'
hasTenant(): bool                     → Verifica si hay tenant válido

// Utilidades de templates
getTenantTemplateDirectory(): string  → Directorio del tenant para templates
renderTenantTemplate($tpl, $params)   → Render con fallback automático
renderWithTenant($tpl, $params)       → Render + datos tenant inyectados
```

#### Ejemplo de Uso
```php
class MiController extends AbstractTenantAwareController
{
    public function index()
    {
        // ✨ Propiedades ya inyectadas por TenantContextInjector
        $tenant = $this->getTenant();          // ['id' => 1, 'name' => ...]
        $name = $this->getTenantName();        // "Melisa Hospital"
        $subdomain = $this->getTenantSubdomain(); // "melisahospital"
        
        // Render automático con fallback
        return $this->renderWithTenant('dashboard/index.html.twig', [
            'data' => $this->getData()
            // tenant, tenant_name, subdomain se agregan automáticamente
        ]);
    }
}
```

#### Interacción con Otros
- ✅ **Inyectado por**: `TenantContextInjector`
- ✅ **Extendido por**: Todos los controladores del sistema
- ✅ **Proporciona**: API estándar de acceso al tenant

---

## 🔄 Secuencia Temporal Completa

```
TIEMPO  | COMPONENTE                    | ACCIÓN
--------|-------------------------------|----------------------------------------
T+0ms   | Symfony Kernel                | Request recibido
T+1ms   | TenantConnectionListener      | Priority 1000 - EJECUTA PRIMERO
        |   ↳ extractTenantFromHost()   | Parse "melisahospital"
        |   ↳ configureTenantDatabase() | Cambia DB connection
        |   ↳ Reflection                | Modifica Connection::$params
T+5ms   | RouterListener                | Priority 32 - Resuelve ruta
        |                               | Define _controller en request
T+10ms  | DynamicControllerSubscriber   | Priority 15 - EJECUTA SEGUNDO
        |   ↳ shouldResolveDynamically()| Verifica si necesita resolución
        |   ↳ TenantContext             | Obtiene subdomain actual
        |   ↳ DynamicControllerResolver | Resuelve controlador específico
        |   ↳ buildSearchPatterns()     | Genera 8 patrones de búsqueda
        |   ↳ class_exists()            | Verifica cada patrón
        |   ↳ Request::setAttribute()   | Actualiza _controller
T+15ms  | TenantContextInjector         | Priority 10 - EJECUTA TERCERO
        |   ↳ instanceof check          | ¿Es AbstractTenantAwareController?
        |   ↳ TenantContext             | Obtiene datos completos del tenant
        |   ↳ Reflection                | Inyecta $tenant, $tenantSubdomain, $tenantName
        |   ↳ setAccessible(true)       | Permite modificar protected properties
T+20ms  | DefaultController             | EJECUTA CUARTO
        |   ↳ index()                   | Método del controlador
        |   ↳ $this->getTenant()        | Accede a propiedades inyectadas
        |   ↳ renderWithTenant()        | Renderiza template
T+50ms  | Twig                          | Renderiza HTML
T+60ms  | Response                      | Envía al cliente
```

---

## 🔗 Dependencias entre Componentes

```
TenantConnectionListener
    ├─ TenantResolver (valida tenant)
    └─ Connection (modifica dbname)

DynamicControllerSubscriber
    ├─ TenantContext (obtiene subdomain)
    └─ DynamicControllerResolver (resuelve controlador)

DynamicControllerResolver
    ├─ TenantContext (obtiene tenant)
    └─ Logger (debug)

TenantContextInjector
    ├─ TenantContext (obtiene datos completos)
    └─ Logger (debug)

AbstractTenantAwareController
    └─ (independiente - solo define API)
```

---

## 💡 Casos de Uso Reales

### Caso 1: Login en Melisa Hospital
```
1. Request: http://melisahospital.melisaupgrade.prod:8081/login
2. TenantConnectionListener → DB = melisahospital
3. DynamicControllerSubscriber → NO resuelve (LoginController es sistema)
4. TenantContextInjector → NO inyecta (LoginController no usa AbstractTenantAwareController)
5. LoginController ejecuta normalmente
```

### Caso 2: Dashboard de Melisa Hospital
```
1. Request: http://melisahospital.melisaupgrade.prod:8081/dashboard
2. TenantConnectionListener → DB = melisahospital
3. DynamicControllerSubscriber → Resuelve a Dashboard\Melisahospital\DefaultController
4. TenantContextInjector → Inyecta $tenant = ['id'=>1, 'name'=>'Melisa Hospital', ...]
5. DefaultController::index() → Usa $this->getTenant() automáticamente
```

### Caso 3: Mantenedor de Regiones
```
1. Request: http://melisahospital.melisaupgrade.prod:8081/mantenedores/basico/region
2. TenantConnectionListener → DB = melisahospital
3. DynamicControllerSubscriber → NO resuelve (Mantenedores son centrales)
4. TenantContextInjector → Inyecta en AbstractMantenedorController
5. RegionController → Usa $this->getTenant() para filtrar datos
```

---

## 🎯 Ventajas de Esta Arquitectura

### ✅ Separación de Responsabilidades
- **TenantConnectionListener**: Solo maneja DB
- **DynamicControllerSubscriber**: Solo resuelve rutas
- **TenantContextInjector**: Solo inyecta contexto
- **DynamicControllerResolver**: Solo lógica de patrones
- **AbstractTenantAwareController**: Solo API de acceso

### ✅ Punto Único de Configuración
- Cambiar prioridad de listeners: 1 lugar
- Cambiar patrones de resolución: 1 método
- Cambiar API de tenant: 1 clase

### ✅ Escalabilidad
- Agregar nuevo tenant: crear carpeta de controladores
- Nuevo tipo de módulo: automáticamente soportado
- Nuevo patrón de resolución: agregar a buildSearchPatterns()

### ✅ Testeable
- Cada componente puede testearse independientemente
- Mocks fáciles de crear
- Sin dependencias circulares

---

## ⚠️ Puntos Críticos

### 1. Orden de Prioridades
```
DEBE SER:
TenantConnectionListener (1000)  ← MÁS ALTA
    ↓
DynamicControllerSubscriber (15)
    ↓
TenantContextInjector (10)       ← MÁS BAJA

Si cambian, el sistema puede fallar
```

### 2. Reflection Performance
- Usar Reflection tiene overhead mínimo
- Solo se ejecuta 1 vez por request
- Cacheado por OpCache en producción

### 3. Lazy Connection
- Doctrine no reconecta hasta el primer query
- TenantConnectionListener solo prepara parámetros
- Primera consulta puede tener latency

---

**Fecha**: 4 de Noviembre, 2025  
**Sistema**: Multi-Tenant Transparente v1.0  
**Estado**: ✅ Completamente Documentado
