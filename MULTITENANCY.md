# Multi-Tenancy con hakam/multi-tenancy-bundle

## 🏗️ Arquitectura

Este proyecto implementa multi-tenancy usando el bundle `hakam/multi-tenancy-bundle` integrado con una arquitectura de 2 proyectos:

```
/var/www/html/
├── melisa_central/          # Proyecto Admin - SOLO tabla tenant
│   └── BD: melisa_central (tabla: tenant)
└── melisa_tenant/           # Proyecto Multi-Tenant (este proyecto)
    └── BDs: melisalacolina, melisahospital, melisawiclinic, etc
           └── Cada una con: member, patient, appointment, invoice, etc
```

## 🔄 Flujo de Cambio de Base de Datos

### 1. Request HTTP
```
http://melisalacolina.melisaupgrade.prod/dashboard
```

### 2. TenantDatabaseSwitchListener (Prioridad 1000)
- Escucha `KernelEvents::REQUEST`
- Extrae subdomain: `"melisalacolina"`
- Llama a `TenantResolver.getTenantBySlug("melisalacolina")`

### 3. TenantResolver
- Conecta a `melisa_central`
- Ejecuta: `SELECT * FROM tenant WHERE subdomain='melisalacolina' AND is_active=1`
- Retorna array con datos del tenant

### 4. TenantContext
- Guarda tenant en contexto
- Disponible en controladores via `$this->getTenant()`

### 5. SwitchDbEvent
- Se dispara evento: `new SwitchDbEvent("melisalacolina")`
- `EventDispatcher` notifica a listeners

### 6. DbSwitchEventListener (del bundle)
- Llama a `CustomTenantConfigProvider.getTenantConnectionConfig("melisalacolina")`
- Obtiene `TenantConnectionConfigDTO`
- Ejecuta `TenantEntityManager.clear()`
- Ejecuta `TenantConnection.switchConnection(['dbname' => 'melisalacolina', ...])`

### 7. Queries Subsecuentes
- Todas las queries usan automáticamente la BD `melisalacolina`
- Los controladores usan `TenantEntityManager` sin preocuparse por la conexión

## 📁 Componentes Principales

### CustomTenantConfigProvider
**Ubicación:** `src/Service/CustomTenantConfigProvider.php`

Implementa `TenantConfigProviderInterface` del bundle.
- Lee tenants desde `melisa_central` vía `TenantResolver`
- Convierte datos a `TenantConnectionConfigDTO`
- Retorna configuración con driver MySQL y status MIGRATED

### TenantDatabaseSwitchListener
**Ubicación:** `src/EventListener/TenantDatabaseSwitchListener.php`

Listener personalizado que integra bundle con lógica existente.
- Detecta subdomain del request
- Resuelve tenant con `TenantResolver`
- Guarda en `TenantContext`
- Dispara `SwitchDbEvent`

### TenantResolver
**Ubicación:** `src/Service/TenantResolver.php`

Servicio que consulta la base de datos central.
- Lee desde `melisa_central.tenant`
- Retorna array con datos del tenant
- Mantiene lógica existente (no modificado por bundle)

### TenantContext
**Ubicación:** `src/Service/TenantContext.php`

Mantiene el tenant activo en memoria.
- Disponible en controladores
- Almacena tenant completo
- Persiste en sesión si es necesario

## 🎯 Uso en Controladores

### AbstractMantenedorController
```php
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;

class MiControlador extends AbstractMantenedorController
{
    public function __construct(TenantEntityManager $entityManager, ...)
    {
        parent::__construct($entityManager, ...);
    }
    
    public function index(): Response
    {
        // $this->entityManager ya está conectado a la BD del tenant correcta
        $repository = $this->entityManager->getRepository(MiEntity::class);
        $entities = $repository->findAll();
        
        // $this->getTenant() retorna datos del tenant actual
        $tenant = $this->getTenant();
        
        return $this->render('template.html.twig', [
            'entities' => $entities,
            'tenant' => $tenant
        ]);
    }
}
```

## ⚙️ Configuración

### config/packages/hakam_multi_tenancy.yaml
```yaml
hakam_multi_tenancy:
    tenant_database_className: 'App\Entity\TenantDb'  # Stub entity
    tenant_database_identifier: 'id'
    
    tenant_connection:
        url: '%env(DATABASE_URL)%'
        driver: 'pdo_mysql'
        charset: 'utf8mb4'
        server_version: '8.0'
    
    tenant_migration:
        tenant_migration_namespace: 'DoctrineMigrations'
        tenant_migration_path: '%kernel.project_dir%/migrations'
    
    tenant_entity_manager:
        mapping:
            type: 'attribute'
            dir: '%kernel.project_dir%/src/Entity'
            prefix: 'App\Entity'
```

### config/services.yaml
```yaml
services:
    # Provider personalizado
    App\Service\CustomTenantConfigProvider:
        autowire: true

    # Alias para que el bundle use nuestro provider
    Hakam\MultiTenancyBundle\Port\TenantConfigProviderInterface:
        alias: App\Service\CustomTenantConfigProvider
        public: true

    # Listener que integra con TenantResolver
    App\EventListener\TenantDatabaseSwitchListener:
        autowire: true
        tags:
            - { name: kernel.event_subscriber }
```

## 🧪 Pruebas

### Comando de Prueba
```bash
php bin/console app:test-tenant-em
```

Este comando ejecuta 6 pruebas:
1. ✅ Lista tenants activos desde melisa_central
2. ✅ Resuelve tenant específico (melisalacolina)
3. ✅ CustomTenantConfigProvider retorna config correcta
4. ✅ SwitchDbEvent se dispara correctamente
5. ✅ Conexión cambia a melisalacolina
6. ✅ Cambio dinámico entre tenants funciona

## 🎯 Ventajas de esta Implementación

### vs Implementación Anterior (sin bundle)
- ✅ **Tipado fuerte:** `TenantEntityManager` en lugar de `EntityManagerInterface` genérico
- ✅ **DTOs y Enums:** `TenantConnectionConfigDTO`, `DriverTypeEnum`, `DatabaseStatusEnum`
- ✅ **Eventos estándar:** `SwitchDbEvent` en lugar de lógica manual
- ✅ **Clear automático:** El bundle hace `EntityManager->clear()` al cambiar BD
- ✅ **Menos reflexión:** No necesita `ReflectionObject` para cambiar parámetros
- ✅ **Wrapper dedicado:** `TenantConnection` con método `switchConnection()`

### vs Bundle Puro (sin integración)
- ✅ **2 proyectos separados:** melisa_central y melisa_tenant (mejor separación de concerns)
- ✅ **TenantResolver existente:** Reutiliza lógica probada de consulta a melisa_central
- ✅ **TenantContext preservado:** Mantiene compatibilidad con código existente
- ✅ **Sin TenantConfigProvider por defecto:** Usa CustomTenantConfigProvider adaptado

## 📊 Comparación

| Aspecto | Antes (Custom) | Ahora (Bundle) |
|---------|---------------|----------------|
| Cambio de conexión | Manual con Reflection | `switchConnection()` del bundle |
| Entity Manager | Doctrine EM genérico | `TenantEntityManager` (typed) |
| Clear cache | Manual (si se hacía) | Automático en DbSwitchEventListener |
| Eventos | Custom listener | `SwitchDbEvent` (estándar) |
| Config provider | No existía | `CustomTenantConfigProvider` |
| Tipado | Arrays genéricos | `TenantConnectionConfigDTO`, Enums |

## 🚀 Comandos Útiles

```bash
# Limpiar cache
php bin/console cache:clear

# Ver servicios del tenant
php bin/console debug:container | grep -i tenant

# Ver autowiring de TenantEntityManager
php bin/console debug:autowiring TenantEntityManager

# Ver eventos registrados
php bin/console debug:event-dispatcher SwitchDbEvent

# Ejecutar pruebas
php bin/console app:test-tenant-em
```

## 📝 Notas Importantes

1. **melisa_central** solo tiene tabla `tenant` (registro de clientes)
2. Cada tenant DB tiene su propia tabla `member` con usuarios
3. NO existe `tenant_member` en ninguna BD
4. El login lee `member` de la BD del tenant correspondiente
5. `TenantDb` entity es un stub solo para satisfacer dependencias del bundle (no se usa realmente)

## 🔗 Referencias

- Bundle: https://github.com/RamyHakam/multi_tenancy_bundle
- Documentación: Ver `ARCHITECTURE.md` para detalles de la arquitectura de 2 proyectos
- Plan de migración: Ver `MIGRATION_PLAN.md` para el proceso de adopción del bundle
