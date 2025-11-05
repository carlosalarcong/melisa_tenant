# 🔗 Conexión Dinámica Multi-Tenant

## 📋 Descripción General

Sistema de conexión dinámica que permite a la aplicación conectarse automáticamente a diferentes bases de datos según el subdominio de la URL. Cada tenant (establecimiento médico) tiene su propia base de datos independiente.

## 🏗️ Arquitectura del Sistema

```
Request: http://melisalacolina.melisaupgrade.prod:8081/
                    ↓
    TenantConnectionListener (Priority 1000 - PRIMERO)
                    ↓
        Extrae subdomain de URL → "melisalacolina"
                    ↓
        Modifica params de Doctrine Connection
                    ↓
        Conexión Doctrine apunta a BD del tenant
                    ↓
    TenantResolver (detecta tenant desde URL o Session)
                    ↓
        Consulta melisa_central.tenant table
                    ↓
        Retorna configuración completa del tenant
                    ↓
    TenantContext (almacena tenant en memoria/sesión)
                    ↓
    LocalizationService (configura idioma del tenant)
                    ↓
        Aplicación usa BD correcta + idioma + contexto
```

## 📁 Archivos del Sistema

### 🔌 Event Listener de Conexión (CRÍTICO)
**Archivo:** `/var/www/html/melisa_tenant/src/EventListener/TenantConnectionListener.php`

**Prioridad:** `1000` (se ejecuta ANTES que todo)

```php
/**
 * Event Listener que automáticamente configura la conexión Doctrine
 * para apuntar a la base de datos del tenant basado en el subdomain
 * 
 * Este listener es CRÍTICO porque:
 * 1. Se ejecuta PRIMERO (priority 1000)
 * 2. Modifica la conexión Doctrine ANTES de cualquier query
 * 3. Usa reflexión para cambiar params de Connection
 * 4. Trabaja con lazy connections (no fuerza conexión inmediata)
 */
class TenantConnectionListener implements EventSubscriberInterface
{
    // Extrae tenant del hostname: melisalacolina.domain.com → melisalacolina
    private function extractTenantFromHost(string $host): ?string
    
    // Modifica los parámetros de la conexión Doctrine vía Reflection
    private function configureTenantDatabase(string $tenant): void
    {
        $params = $this->connection->getParams();
        $params['dbname'] = $tenant; // ← Cambia la BD dinámicamente
        
        // Usa reflexión para actualizar params en Connection
        $reflection = new \ReflectionObject($this->connection);
        $paramsProperty = $reflection->getProperty('params');
        $paramsProperty->setValue($this->connection, $params);
    }
}
```

**Importante:** Este listener usa **Lazy Connection**, lo que significa que NO conecta inmediatamente a la BD, solo cambia los parámetros. La conexión real ocurre en el primer query.

---

### 🎯 Servicio Principal de Resolución
**Archivo:** `/var/www/html/melisa_tenant/src/Service/TenantResolver.php`

```php
/**
 * Servicio que resuelve el tenant basado en el subdomain o sesión
 * y proporciona acceso a la configuración del tenant
 */
class TenantResolver
{
    /**
     * Constructor con inyección de DATABASE_URL desde .env
     */
    public function __construct(
        private readonly string $centralDbUrl
    ) {
        // Parsea DATABASE_URL: mysql://user:pass@host:3306/melisa_central
        $this->centralDbConfig = $this->parseDatabaseUrl($centralDbUrl);
    }
    
    // Métodos principales:
    // - resolveTenantFromRequest(): Detecta tenant desde URL
    // - getTenantBySlug(): Obtiene config desde BD central
    // - createTenantConnection(): Crea conexión dinámica adicional (si se necesita)
    // - getAllActiveTenants(): Lista tenants activos para selector
}
```

**Configuración de credenciales:**
- Las credenciales se leen desde la variable de entorno `DATABASE_URL` en `.env`
- Formato: `mysql://usuario:password@host:puerto/melisa_central`
- Se configura en `config/services.yaml`:
  ```yaml
  App\Service\TenantResolver:
      arguments:
          $centralDbUrl: '%env(DATABASE_URL)%'
  ```

**Nota:** Este servicio consulta `melisa_central` que contiene la tabla `tenant` con la configuración de todos los establecimientos.

---

### 🏪 Contexto del Tenant Actual
**Archivo:** `/var/www/html/melisa_tenant/src/Service/TenantContext.php`

#### **📋 Función Principal**
Mantiene el contexto del tenant actual durante toda la sesión del usuario, evitando múltiples consultas a la base de datos central y proporcionando acceso rápido a la información del tenant activo.

#### **🎯 Características Técnicas**

##### **Almacenamiento en Memoria + Sesión**
```php
class TenantContext
{
    private ?array $currentTenant = null;      // Cache en memoria
    private ?string $currentSubdomain = null;  // Subdomain activo
    private RequestStack $requestStack;        // Stack de requests Symfony
}
```

##### **Persistencia Dual**
- **Memoria**: Para acceso ultrarrápido dentro del request actual
- **Sesión**: Para persistir entre requests del mismo usuario
- **Recuperación automática**: Si está en sesión pero no en memoria, lo restaura

##### **Cache Inteligente**
```php
public function getCurrentTenant(): ?array
{
    // 1. ¿Está en memoria? → Retornar inmediatamente (0ms)
    if ($this->currentTenant) {
        return $this->currentTenant;
    }
    
    // 2. ¿Está en sesión? → Restaurar a memoria
    $session = $request->getSession();
    $tenantData = $session->get('tenant');
    
    if ($tenantData && is_array($tenantData)) {
        $this->setCurrentTenant($tenantData);
        return $this->currentTenant;
    }
    
    // 3. No encontrado → Null (requiere resolver desde BD central)
    return null;
}
```

#### **🔧 Métodos Principales**

```php
// Establecer tenant (guarda en memoria + sesión)
public function setCurrentTenant(?array $tenant): void

// Obtener tenant completo
public function getCurrentTenant(): ?array

// Obtener datos específicos
public function getCurrentTenantName(): ?string        // "Clínica La Colina"
public function getCurrentDatabaseName(): ?string     // "melisalacolina"
public function getCurrentSubdomain(): ?string        // "melisalacolina"

// Verificar si hay tenant activo
public function hasCurrentTenant(): bool

// Limpiar contexto (útil para testing o cambio de tenant)
public function clearCurrentTenant(): void
```

#### **⚡ Optimización de Performance**

```php
// ❌ Sin TenantContext (ineficiente - consulta BD cada vez)
public function page1(TenantResolver $resolver, Request $request) {
    $tenant = $resolver->resolveTenantFromRequest($request); // Query a melisa_central
}

public function page2(TenantResolver $resolver, Request $request) {
    $tenant = $resolver->resolveTenantFromRequest($request); // Query a melisa_central OTRA VEZ
}

// ✅ Con TenantContext (eficiente - 1 query por sesión)
public function page1(TenantContext $context) {
    $tenant = $context->getCurrentTenant(); // Desde cache en memoria (0ms)
}

public function page2(TenantContext $context) {
    $tenant = $context->getCurrentTenant(); // Desde cache en memoria (0ms)
}
```

**Ahorro:** Hasta 90% menos consultas a BD central.

---

### 🌐 Servicio de Localización
**Archivo:** `/var/www/html/melisa_tenant/src/Service/LocalizationService.php`

```php
/**
 * Maneja idiomas por tenant y por usuario
 * Soporta traducciones específicas por establecimiento
 */
class LocalizationService
{
    private array $supportedLocales = ['es', 'en'];
    private string $defaultLocale = 'es';
    
    // Prioridades para detectar idioma:
    // 1. Sesión del usuario ($_SESSION['_locale'])
    // 2. Configuración del tenant (tenant.locale)
    // 3. Header Accept-Language del navegador
    // 4. Idioma por defecto ('es')
    public function getCurrentLocale(): string
    
    // Establece idioma del usuario en sesión
    public function setUserLocale(string $locale): bool
    
    // Traduce usando dominios específicos del tenant
    // Busca en: [tenant_subdomain].es.yaml → default.es.yaml → messages.es.yaml
    public function trans(string $id, array $parameters = [], string $domain = 'messages'): string
    
    // Obtiene dominio de traducción basado en tenant
    // Ejemplo: melisalacolina → dominio "melisalacolina"
    private function getTenantDomain(): string
}
```

**Estructura de traducciones:**
```
translations/
├── melisalacolina/
│   ├── messages.es.yaml    # Traducciones específicas Clínica La Colina
│   └── messages.en.yaml
├── melisahospital/
│   ├── messages.es.yaml    # Traducciones específicas Hospital
│   └── messages.en.yaml
└── default/
    ├── messages.es.yaml    # Traducciones comunes/fallback
    └── messages.en.yaml
```

---

### 🔐 Servicio de Autenticación
**Archivo:** `/var/www/html/melisa_tenant/src/Service/AuthenticationService.php`

```php
/**
 * Servicio de autenticación que valida credenciales
 * usando DQL (Doctrine Query Language)
 */
class AuthenticationService
{
    public function __construct(
        private MemberRepository $memberRepository
    ) {}
    
    // Autentica usuario y retorna datos (sin password)
    public function authenticateUser(string $username, string $password): ?array
    {
        $user = $this->memberRepository->findActiveUserByUsername($username);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        
        unset($user['password']); // Seguridad: remover password
        return $user;
    }
}
```

**Repository asociado:** `src/Repository/MemberRepository.php`
- Usa **DQL** (no SQL nativo) para queries
- Mapea automáticamente entre camelCase (PHP) y snake_case (BD)

---

### 🎮 Controlador de Resolución Dinámica
**Archivo:** `/var/www/html/melisa_tenant/src/Service/DynamicControllerResolver.php`
```php
/**
 * Resuelve automáticamente controladores específicos por tenant
 * Permite tener lógica personalizada por establecimiento con fallback
 * 
 * Busca en este orden:
 * 1. App\Tenant\[Subdomain]\Controller\[Controller]
 * 2. App\Tenant\Default\Controller\[Controller]
 * 3. App\Controller\[Controller]
 */
class DynamicControllerResolver
{
    // Resuelve controller dinámicamente
    public function resolveController(string $subdomain, string $baseController): string
    
    // Genera nombre de ruta dinámico
    public function generateRedirectRoute(string $subdomain, string $baseRoute): string
}
```

**Ejemplo:**
- Subdomain: `melisalacolina`
- Controller base: `DashboardController`
- Busca: `App\Tenant\Melisalacolina\Controller\DashboardController`
- Si no existe: `App\Tenant\Default\Controller\DashboardController`
- Si no existe: `App\Controller\DashboardController`

---

### 🎧 Event Listener de Traducciones
**Archivo:** `/var/www/html/melisa_tenant/src/EventListener/TenantTranslationListener.php`

```php
/**
 * Establece el dominio de traducción basado en el tenant
 * Priority: 25 (ejecuta ANTES de LocaleListener que tiene priority 20)
 */
class TenantTranslationListener implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $tenantSubdomain = $this->tenantContext->getCurrentTenant()['subdomain'] ?? 'default';
        
        // Establece atributos en request para uso posterior
        $request->attributes->set('_tenant_subdomain', $tenantSubdomain);
        $request->attributes->set('_tenant_translation_path', $this->getTenantTranslationPath($tenantSubdomain));
    }
}
```

---

### � Event Listener de Locale
**Archivo:** `/var/www/html/melisa_tenant/src/EventListener/LocaleListener.php`

```php
/**
 * Establece el locale en cada request basado en sesión o tenant
 * Priority: 20 (ejecuta DESPUÉS de TenantTranslationListener)
 */
class LocaleListener implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $this->localizationService->getCurrentLocale();
        $request->setLocale($locale);
    }
}
```

## 🔄 Flujo de Conexión Dinámica (Detallado)

### **Paso 0: Request Inicial**
```
Usuario ingresa URL: http://melisalacolina.melisaupgrade.prod:8081/login
```

### **Paso 1: TenantConnectionListener (Priority 1000)**
```php
// Se ejecuta PRIMERO (antes de cualquier controller)
public function onKernelRequest(RequestEvent $event): void
{
    $host = $request->getHost(); // "melisalacolina.melisaupgrade.prod"
    $tenant = $this->extractTenantFromHost($host); // "melisalacolina"
    
    // Modificar parámetros de Doctrine Connection con reflexión
    $params = $this->connection->getParams();
    $params['dbname'] = 'melisalacolina'; // ← Cambio dinámico
    
    // Actualizar connection (siguiente query usará esta BD)
    $paramsProperty->setValue($this->connection, $params);
}
```

**Resultado:** La conexión Doctrine principal ahora apunta a `melisalacolina` en lugar de la BD configurada en `.env`

---

### **Paso 2: LoginController (Detección de Tenant)**
```php
public function login(Request $request, TenantResolver $tenantResolver): Response
{
    // Resolver tenant desde URL para obtener configuración completa
    $tenant = $tenantResolver->resolveTenantFromRequest($request);
    // Retorna: ['id' => 1, 'name' => 'Clínica La Colina', 'subdomain' => 'melisalacolina', ...]
    
    return $this->render('login/form.html.twig', [
        'tenant' => $tenant,
        'tenant_name' => $tenant['name']
    ]);
}
```

---

### **Paso 3: TenantResolver - Consulta a BD Central**
```php
// Consulta en melisa_central.tenant (conexión separada)
public function getTenantBySlug(string $slug): ?array
{
    // Crea conexión temporal a melisa_central
    $connection = DriverManager::getConnection($this->centralDbConfig);
    
    $query = '
        SELECT id, name, subdomain, database_name, rut_empresa,
               domain, host_port, db_user, db_password, is_active, locale
        FROM tenant 
        WHERE subdomain = ? AND is_active = 1
    ';
    
    $result = $connection->executeQuery($query, ['melisalacolina']);
    return $result->fetchAssociative();
}
```

**Retorna:**
```php
[
    'id' => 1,
    'name' => 'Clínica La Colina',
    'subdomain' => 'melisalacolina',
    'database_name' => 'melisalacolina',
    'locale' => 'es',
    'is_active' => 1,
    // ... más campos
]
```

---

### **Paso 4: Login Exitoso - Establecer Contexto**
```php
public function handleLogin(...): Response
{
    // Autenticar usuario (usa BD del tenant ya configurada por Listener)
    $user = $authService->authenticateUser($username, $password);
    
    // Guardar tenant en sesión
    $session->set('tenant', $tenant);
    $session->set('tenant_id', $tenant['id']);
    $session->set('tenant_slug', $tenant['subdomain']);
    
    // Establecer en TenantContext (cache en memoria)
    $tenantContext->setCurrentTenant($tenant);
    
    // Configurar idioma del tenant
    $localizationService->setUserLocale($tenant['locale'] ?? 'es');
    
    return $this->redirectToRoute('app_dashboard');
}
```

---

### **Paso 5: Navegación Posterior - Dashboard**
```php
public function dashboard(TenantContext $context): Response
{
    // NO consulta BD central, usa cache de TenantContext
    $tenantName = $context->getCurrentTenantName(); // "Clínica La Colina"
    $dbName = $context->getCurrentDatabaseName();   // "melisalacolina"
    
    // Todas las queries usan automáticamente la BD del tenant
    // (ya configurada por TenantConnectionListener)
    $members = $memberRepository->findAll(); // SELECT * FROM melisalacolina.member
    
    return $this->render('dashboard/index.html.twig', [
        'tenant_name' => $tenantName,
        'members' => $members
    ]);
}
```

---

### **Diagrama de Flujo Completo**

```mermaid
sequenceDiagram
    participant U as Usuario
    participant TL as TenantConnectionListener
    participant TR as TenantResolver
    participant TC as TenantContext
    participant LS as LocalizationService
    participant DC as Doctrine Connection
    participant DB as Base de Datos
    
    U->>TL: GET /login (melisalacolina.domain.com)
    TL->>TL: Extraer "melisalacolina" de hostname
    TL->>DC: Modificar params['dbname'] = 'melisalacolina'
    
    U->>TR: Resolver tenant completo
    TR->>DB: Query melisa_central.tenant WHERE subdomain='melisalacolina'
    DB->>TR: Return tenant config
    TR->>TC: setCurrentTenant($tenant)
    TC->>TC: Guardar en memoria + sesión
    
    U->>LS: POST /login (submit credentials)
    LS->>DC: SELECT * FROM melisalacolina.member (usa BD ya configurada)
    DC->>DB: Query a melisalacolina DB
    DB->>DC: Return user data
    DC->>LS: Authenticated
    
    U->>U: Navega a /dashboard
    U->>TC: getCurrentTenant()
    TC->>TC: Return desde cache (sin query)
    U->>DC: Query cualquier entidad
    DC->>DB: Usa melisalacolina DB automáticamente
```

## 🎯 Uso en Controladores

### **Patrón Recomendado (Con TenantContext)**
**Archivo:** `/var/www/html/melisa_tenant/src/Controller/ExampleController.php`

```php
<?php
namespace App\Controller;

use App\Service\TenantContext;
use App\Service\LocalizationService;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController extends AbstractController
{
    #[Route('/members', name: 'app_members_list')]
    public function list(
        TenantContext $tenantContext,
        LocalizationService $localization,
        MemberRepository $memberRepository
    ): Response {
        
        // 1. Obtener datos del tenant desde cache (0ms)
        $tenantName = $tenantContext->getCurrentTenantName();
        $tenantDb = $tenantContext->getCurrentDatabaseName();
        
        // 2. Las queries usan automáticamente la BD del tenant
        //    (ya configurada por TenantConnectionListener)
        $members = $memberRepository->findAll();
        // Ejecuta: SELECT * FROM melisalacolina.member
        
        // 3. Traducir textos con dominio del tenant
        $title = $localization->trans('members.list.title');
        // Busca en: melisalacolina/messages.es.yaml → default/messages.es.yaml
        
        return $this->render('members/list.html.twig', [
            'tenant_name' => $tenantName,
            'tenant_database' => $tenantDb,
            'members' => $members,
            'title' => $title
        ]);
    }
}
```

---

### **Patrón Completo (Login/Setup Inicial)**
**Archivo:** `/var/www/html/melisa_tenant/src/Controller/LoginController.php`

```php
public function login(
    Request $request,
    TenantResolver $tenantResolver,
    TenantContext $tenantContext,
    LocalizationService $localization
): Response {
    
    // 1. Resolver tenant desde URL (consulta melisa_central)
    $tenant = $tenantResolver->resolveTenantFromRequest($request);
    
    if (!$tenant) {
        throw new NotFoundHttpException('Tenant no encontrado');
    }
    
    // 2. Establecer contexto del tenant (guarda en memoria + sesión)
    $tenantContext->setCurrentTenant($tenant);
    
    // 3. Configurar idioma del tenant
    $locale = $tenant['locale'] ?? 'es';
    $localization->setUserLocale($locale);
    
    // 4. Mostrar formulario
    return $this->render('login/form.html.twig', [
        'tenant_name' => $tenant['name'],
        'error' => null
    ]);
}
```

---

### **Uso Avanzado: Query Manual en BD del Tenant**

```php
use Doctrine\DBAL\Connection;

public function customQuery(Connection $connection): Response
{
    // La conexión YA está configurada para usar BD del tenant
    // gracias a TenantConnectionListener
    
    $query = "SELECT * FROM member WHERE activo = 1";
    $result = $connection->executeQuery($query);
    $members = $result->fetchAllAssociative();
    
    return $this->json($members);
}
```

---

### **⚠️ Patrón NO Recomendado (crear conexión manual)**

```php
// ❌ NO HACER ESTO (innecesario y menos eficiente)
public function badExample(TenantResolver $resolver, Request $request): Response
{
    $tenant = $resolver->resolveTenantFromRequest($request);
    $connection = $resolver->createTenantConnection($tenant); // Conexión extra
    
    $result = $connection->executeQuery('SELECT * FROM member');
    // ...
}

// ✅ HACER ESTO (usa conexión ya configurada)
public function goodExample(MemberRepository $repo): Response
{
    $members = $repo->findAll(); // Usa BD del tenant automáticamente
    // ...
}
```

## 🗄️ Estructura de Base de Datos

### Base de Datos Central
**Ubicación:** `melisa_central.tenant`
```sql
-- Tabla que contiene configuración de todos los tenants
CREATE TABLE tenant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- "Clínica La Colina"
    subdomain VARCHAR(100) NOT NULL,               -- "melisalacolina"
    database_name VARCHAR(100) NOT NULL,           -- "melisalacolina"
    rut_empresa VARCHAR(20),                       -- "12.345.678-9"
    host VARCHAR(255) DEFAULT 'localhost',         -- Host BD del tenant
    host_port INT DEFAULT 3306,                    -- Puerto BD del tenant
    db_user VARCHAR(100) DEFAULT 'melisa',         -- Usuario BD del tenant
    db_password VARCHAR(255) DEFAULT 'melisamelisa', -- Password BD del tenant
    driver VARCHAR(50) DEFAULT 'mysql',            -- Driver de BD
    is_active TINYINT(1) DEFAULT 1,               -- Estado activo
    language VARCHAR(10) DEFAULT 'es',             -- Idioma del tenant
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Bases de Datos de Tenants
Cada tenant tiene su propia base de datos:
- `melisalacolina` - Clínica La Colina
- `melisawiclinic` - Clínica Wi Clinic  
- `melisahospital` - Hospital Central

## 🌐 URLs y Mapeo

| URL | Subdominio | Base de Datos | Descripción |
|-----|------------|---------------|-------------|
| `http://melisalacolina.melisaupgrade.prod:8081/` | `melisalacolina` | `melisalacolina` | Clínica La Colina |
| `http://melisawiclinic.melisaupgrade.prod:8081/` | `melisawiclinic` | `melisawiclinic` | Clínica Wi Clinic |
| `http://melisahospital.melisaupgrade.prod:8081/` | `melisahospital` | `melisahospital` | Hospital Central |

## ⚙️ Configuración de Servicios

### **Archivo: `/var/www/html/melisa_tenant/config/services.yaml`**

```yaml
services:
    # Configuración por defecto
    _defaults:
        autowire: true
        autoconfigure: true

    # Event Listener de conexión (CRÍTICO)
    App\EventListener\TenantConnectionListener:
        arguments:
            $connection: '@doctrine.dbal.default_connection'
        tags:
            - { name: kernel.event_subscriber }

    # Event Listener de traducciones
    App\EventListener\TenantTranslationListener:
        tags:
            - { name: kernel.event_subscriber }

    # Event Listener de locale
    App\EventListener\LocaleListener:
        tags:
            - { name: kernel.event_subscriber }

    # Servicios de Tenant
    App\Service\TenantResolver:
        autowire: true

    App\Service\TenantContext:
        autowire: true

    App\Service\DynamicControllerResolver:
        arguments:
            $projectDir: '%kernel.project_dir%'

    # Servicios de localización
    App\Service\LocalizationService:
        autowire: true

    # Servicios de autenticación
    App\Service\AuthenticationService:
        autowire: true

    # Repositorios
    App\Repository\:
        resource: '../src/Repository'
        tags: ['doctrine.repository_service']
```

### **Orden de Ejecución de Event Listeners**

```
Priority 1000: TenantConnectionListener (configura BD del tenant)
       ↓
Priority 25: TenantTranslationListener (configura domain de traducciones)
       ↓
Priority 20: LocaleListener (establece locale en request)
       ↓
Priority 0: Controllers (lógica de negocio)
```

## 🚀 Ventajas del Sistema

### ✅ **Automático y Transparente**
- **Sin configuración manual**: Detección automática del tenant por URL
- **Conexión lazy**: La BD se conecta solo cuando se necesita (primera query)
- **Cache inteligente**: TenantContext evita consultas repetidas a melisa_central
- **Event-driven**: Listeners configuran todo antes que los controllers ejecuten

### ✅ **Escalable y Mantenible**
- **Agregar nuevo tenant**: Solo insertar fila en `melisa_central.tenant`, zero código
- **Sin cambios en controllers**: Los controllers NO necesitan saber de multi-tenancy
- **Repositorios normales**: Doctrine repositories funcionan sin modificaciones
- **Testing fácil**: Mock TenantContext para tests unitarios

### ✅ **Seguro y Aislado**
- **Aislamiento completo**: Cada tenant tiene su propia base de datos física
- **Sin queries cruzados**: Imposible acceder a datos de otro tenant
- **Validación automática**: Solo tenants activos (`is_active = 1`) son accesibles
- **Sin SQL injection**: Usa Doctrine DBAL con prepared statements

### ✅ **Eficiente en Performance**
- **Cache en memoria**: TenantContext evita N queries a melisa_central
- **Cache en sesión**: Persiste entre requests del mismo usuario
- **Lazy connection**: Doctrine no conecta hasta el primer query real
- **Connection pooling**: Doctrine reutiliza conexiones cuando es posible

### ✅ **Flexible y Personalizable**
- **Configuración por tenant**: Cada establecimiento puede tener diferentes:
  - Host/puerto de BD
  - Credenciales de BD
  - Idioma predeterminado
  - Traducciones específicas
- **Controllers específicos**: Sistema de fallback para lógica personalizada
- **Multi-idioma**: Soporte de traducciones por tenant y por usuario

### ✅ **Compatible con Doctrine ORM/DBAL**
- **Doctrine ORM**: Entities y Repositories funcionan normalmente
- **Doctrine DBAL**: Queries manuales usan BD del tenant automáticamente
- **Migrations**: Comando personalizado aplica a todos los tenants
- **QueryBuilder**: DQL queries funcionan sin cambios

## 🛠️ Comandos y Herramientas

### **Migración Automática a Todos los Tenants**
**Archivo:** `/var/www/html/melisa_tenant/src/Command/MigrateTenantCommand.php`

```bash
# Aplicar migraciones a todos los tenants automáticamente
php bin/console app:migrate-tenant

# Modo simulación (dry-run) - solo muestra qué haría
php bin/console app:migrate-tenant --dry-run

# Forzar sin confirmación (útil en scripts CI/CD)
php bin/console app:migrate-tenant --force

# Migrar solo un tenant específico
php bin/console app:migrate-tenant --tenant=melisalacolina
```

**Funcionamiento:**
1. Lee todos los tenants activos desde `melisa_central.tenant`
2. Para cada tenant:
   - Modifica temporalmente la conexión Doctrine
   - Ejecuta `doctrine:migrations:migrate`
   - Restaura la conexión
3. Reporta éxitos y errores

---

### **Verificar Tenant desde CLI**

```bash
# Listar todos los tenants
php bin/console dbal:run-sql "SELECT subdomain, name, is_active FROM tenant" --connection=central

# Ver configuración de un tenant específico
php bin/console dbal:run-sql "SELECT * FROM tenant WHERE subdomain='melisalacolina'" --connection=central
```

---

### **Testing de Conexión**

```php
// En cualquier command o controller de testing
use App\Service\TenantResolver;
use Symfony\Component\HttpFoundation\Request;

public function testTenantConnection(TenantResolver $resolver): void
{
    // Simular request con hostname
    $request = Request::create('http://melisalacolina.melisaupgrade.prod:8081/');
    
    // Resolver tenant
    $tenant = $resolver->resolveTenantFromRequest($request);
    
    if ($tenant) {
        echo "✅ Tenant encontrado: " . $tenant['name'] . "\n";
        echo "   Database: " . $tenant['database_name'] . "\n";
        
        // Probar conexión
        try {
            $connection = $resolver->createTenantConnection($tenant);
            $result = $connection->executeQuery('SELECT 1 as test');
            echo "✅ Conexión exitosa\n";
        } catch (\Exception $e) {
            echo "❌ Error de conexión: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Tenant no encontrado\n";
    }
}
```

## 🔍 Debugging y Troubleshooting

### **Verificar qué BD está usando Doctrine**

```php
use Doctrine\DBAL\Connection;

public function debugConnection(Connection $connection): void
{
    $params = $connection->getParams();
    
    dump([
        'database' => $params['dbname'] ?? 'unknown',
        'host' => $params['host'] ?? 'unknown',
        'user' => $params['user'] ?? 'unknown',
        'connected' => $connection->isConnected()
    ]);
}
```

---

### **Verificar Tenant en TenantContext**

```php
use App\Service\TenantContext;

public function debugTenant(TenantContext $context): void
{
    if ($context->hasCurrentTenant()) {
        $tenant = $context->getCurrentTenant();
        
        dump([
            'tenant_name' => $tenant['name'],
            'subdomain' => $tenant['subdomain'],
            'database' => $tenant['database_name'],
            'locale' => $tenant['locale'] ?? 'not set',
            'is_active' => $tenant['is_active']
        ]);
    } else {
        dump('❌ No tenant in context');
    }
}
```

---

### **Verificar Traducciones del Tenant**

```php
use App\Service\LocalizationService;

public function debugTranslations(LocalizationService $localization): void
{
    dump([
        'current_locale' => $localization->getCurrentLocale(),
        'supported_locales' => $localization->getSupportedLocales(),
        'tenant_domain' => $localization->getTenantDomain(),
        'tenant_translations' => $localization->getTenantSpecificTranslations()
    ]);
    
    // Probar traducción
    $translated = $localization->trans('auth.login', [], 'messages');
    dump(['translation' => $translated]);
}
```

---

### **Logs Útiles**

```bash
# Ver logs de la aplicación
tail -f /var/www/html/melisa_tenant/var/log/dev.log | grep -i tenant

# Ver logs de Doctrine queries
tail -f /var/www/html/melisa_tenant/var/log/dev.log | grep "SELECT"

# Verificar conexiones MySQL activas
mysql -u melisa -pmelisamelisa -e "SHOW PROCESSLIST;"

# Ver qué base de datos está usando una conexión específica
mysql -u melisa -pmelisamelisa -e "SELECT DATABASE();"
```

---

### **Problemas Comunes y Soluciones**

#### **1. "Tenant no encontrado"**
```bash
# Verificar que el tenant existe en melisa_central
mysql -u melisa -pmelisamelisa melisa_central \
  -e "SELECT * FROM tenant WHERE subdomain='melisalacolina';"

# Verificar que está activo
# is_active debe ser 1
```

#### **2. "Access denied for user"**
```bash
# Verificar credenciales del tenant en melisa_central
mysql -u melisa -pmelisamelisa melisa_central \
  -e "SELECT db_user, db_password FROM tenant WHERE subdomain='melisalacolina';"

# Probar conexión manual
mysql -u melisa -pmelisamelisa melisalacolina -e "SELECT 1;"
```

#### **3. "Unknown database"**
```bash
# Verificar que la BD del tenant existe
mysql -u melisa -pmelisamelisa -e "SHOW DATABASES LIKE 'melisalacolina';"

# Si no existe, crearla
mysql -u melisa -pmelisamelisa -e "CREATE DATABASE melisalacolina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate --connection=melisalacolina
```

#### **4. "TenantConnectionListener no se ejecuta"**
```yaml
# Verificar en config/services.yaml que el listener está registrado
App\EventListener\TenantConnectionListener:
    tags:
        - { name: kernel.event_subscriber }
```

#### **5. "Traducciones no aparecen"**
```bash
# Verificar que existen los archivos de traducción
ls -la translations/melisalacolina/messages.es.yaml
ls -la translations/default/messages.es.yaml

# Limpiar cache de traducciones
php bin/console cache:clear
php bin/console translation:extract --force es
```

---

### **Debug en Producción (con cuidado)**

```php
// Agregar temporalmente en un controller para debugging
use Psr\Log\LoggerInterface;

public function debug(LoggerInterface $logger, Connection $connection, TenantContext $context): Response
{
    $logger->info('DEBUG: Tenant Info', [
        'has_tenant' => $context->hasCurrentTenant(),
        'tenant_name' => $context->getCurrentTenantName(),
        'database' => $connection->getParams()['dbname'] ?? 'unknown',
        'session_tenant' => $request->getSession()->get('tenant')
    ]);
    
    return $this->json(['status' => 'logged']);
}
```

## 📚 Referencias y Documentación

### **Symfony**
- [Doctrine Database Connections](https://symfony.com/doc/current/doctrine.html)
- [Event Listeners and Subscribers](https://symfony.com/doc/current/event_dispatcher.html)
- [Service Container](https://symfony.com/doc/current/service_container.html)
- [Sessions](https://symfony.com/doc/current/components/http_foundation/sessions.html)

### **Doctrine**
- [DBAL Connection Management](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/connections.html)
- [Schema Manager](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/schema-manager.html)
- [DQL (Doctrine Query Language)](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html)

### **Multi-tenancy Patterns**
- [Tenant per Database Pattern](https://docs.microsoft.com/en-us/azure/sql-database/saas-tenancy-app-design-patterns)
- [Multi-tenant Architecture](https://martinfowler.com/bliki/MultiTenancy.html)

### **Archivos Clave del Proyecto**
```
src/
├── Service/
│   ├── TenantResolver.php           # Resolución de tenants
│   ├── TenantContext.php            # Cache del tenant actual
│   ├── LocalizationService.php      # Gestión de idiomas
│   ├── AuthenticationService.php    # Autenticación de usuarios
│   └── DynamicControllerResolver.php # Controllers por tenant
├── EventListener/
│   ├── TenantConnectionListener.php # Configura BD automáticamente (Priority 1000)
│   ├── TenantTranslationListener.php # Configura traducciones (Priority 25)
│   └── LocaleListener.php           # Establece locale (Priority 20)
├── Controller/
│   ├── LoginController.php          # Login multi-tenant
│   ├── LocaleController.php         # Cambio de idioma
│   └── TenantController.php         # Base para controllers por tenant
└── Repository/
    ├── MemberRepository.php         # Queries con DQL
    └── TenantRepository.php         # Acceso a tenant central

config/
├── services.yaml                    # Configuración de servicios
└── packages/
    └── doctrine.yaml                # Configuración de Doctrine

translations/
├── melisalacolina/                  # Traducciones Clínica La Colina
├── melisahospital/                  # Traducciones Hospital
└── default/                         # Traducciones comunes/fallback
```

---

**Última actualización:** Noviembre 5, 2025  
**Versión:** 2.0.0  
**Stack Técnico:** Symfony 6+ | Doctrine DBAL 3+ | PHP 8.4 | Multi-tenant | Multi-idioma