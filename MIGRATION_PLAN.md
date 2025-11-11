# Plan de Migración a multi_tenancy_bundle

## 🎯 Objetivo
Adoptar features útiles de `hakam/multi-tenancy-bundle` para gestión de tenants, **manteniendo la separación de proyectos melisa_central (Main) y melisa_tenant (Tenant)**.

## 🏗️ Arquitectura Actual (NO CAMBIAR)
```
/var/www/html/
├── melisa_central/          # Proyecto Admin - SOLO tabla tenant
│   └── BD: melisa_central (tabla: tenant)
└── melisa_tenant/           # Proyecto Multi-Tenant
    └── BDs: melisalacolina, melisahospital, etc
           └── Cada una con: member, patient, appointment, etc
```

**IMPORTANTE:** 
- ✅ melisa_central solo tiene tabla `tenant` (registro de clientes)
- ✅ Cada tenant DB tiene su propia tabla `member` con usuarios
- ✅ NO existe `tenant_member` (no se necesita)
- ✅ Login lee `member` de la BD del tenant correspondiente

**NO vamos a fusionar proyectos.** Solo adoptaremos:
- ✅ TenantEntityManager (para gestionar conexión dinámica)
- ✅ SwitchDbEvent (cambio de tenant más limpio)
- ✅ Comandos de migración para tenants
- ✅ DTOs y enums (mejor tipado)
- ❌ NO: Entity Manager Main (no aplica, melisa_central es otro proyecto)
- ❌ NO: TenantConfigProvider (ya tienes TenantResolver)

---

## 📋 FASE 1: PREPARACIÓN Y ANÁLISIS ✅ COMPLETADA
**Duración real:** 30 minutos  
**Objetivo:** Instalar bundle sin romper funcionalidad existente

### ✅ Tareas completadas:
- [x] Crear branch `multitenancy` desde master
- [x] Instalar bundle: `composer require hakam/multi-tenancy-bundle` (v2.9.3)
- [x] Registrar `HakamMultiTenancyBundle` en `config/bundles.php`
- [x] Crear configuración en `config/packages/hakam_multi_tenancy.yaml`
- [x] Crear `src/Entity/TenantDb.php` como stub (requerida por bundle pero no usada)
- [x] Limpiar conflicto con API Platform (removido automáticamente por Composer)
- [x] Eliminar `config/packages/uid.yaml` (incompatibilidad)
- [x] Verificar servicios del bundle disponibles

### 📝 Servicios del bundle registrados:
- ✅ `doctrine.orm.tenant_entity_manager` - TenantEntityManager
- ✅ `doctrine.dbal.tenant_connection` - Conexión dinámica
- ✅ Comandos: `tenant:migrations:migrate`, `tenant:database:create`, `tenant:fixtures:load`

### � Archivos modificados:
- `composer.json` - hakam/multi-tenancy-bundle v2.9.3
- `config/bundles.php` - HakamMultiTenancyBundle registrado
- `config/packages/hakam_multi_tenancy.yaml` - Configuración (ver abajo)
- `src/Entity/TenantDb.php` - Entity stub (NO usada en lógica real)

### ⚙️ Configuración aplicada:
```yaml
hakam_multi_tenancy:
    tenant_database_className: 'App\Entity\TenantDb'  # Stub
    tenant_database_identifier: 'id'
    tenant_config_provider: null  # No usamos el provider del bundle
    
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

### ⚠️ Punto de verificación PASADO:
```bash
✅ php bin/console cache:clear
✅ php bin/console debug:container | grep tenant
✅ php bin/console list | grep tenant
```

**Estado:** Bundle instalado y funcional. Código existente sin cambios.

---

## 📋 FASE 2: IMPLEMENTAR TENANTENTITYMANAGER ✅ COMPLETADA
**Duración real:** 45 minutos  
**Objetivo:** Integrar TenantEntityManager y SwitchDbEvent del bundle con código existente

### ✅ Tareas completadas:
- [x] Crear `CustomTenantConfigProvider` que usa `TenantResolver`
- [x] Implementar `TenantConfigProviderInterface` del bundle
- [x] Crear `TenantDatabaseSwitchListener` usando `SwitchDbEvent`
- [x] Registrar servicios en `config/services.yaml`
- [x] Configurar bundle para usar `CustomTenantConfigProvider`
- [x] Desactivar `TenantConnectionListener` antiguo (comentado como backup)
- [x] Verificar integración con cache:warmup

### 📝 Servicios implementados:

**CustomTenantConfigProvider** (`src/Service/CustomTenantConfigProvider.php`):
- Implementa `TenantConfigProviderInterface` del bundle
- Usa `TenantResolver` para leer desde `melisa_central`
- Convierte datos a `TenantConnectionConfigDTO`
- Retorna `DriverTypeEnum::MYSQL` y `DatabaseStatusEnum::DATABASE_MIGRATED`

**TenantDatabaseSwitchListener** (`src/EventListener/TenantDatabaseSwitchListener.php`):
- Suscrito a `KernelEvents::REQUEST` con alta prioridad (1000)
- Detecta subdomain y resuelve tenant con `TenantResolver`
- Guarda tenant en `TenantContext` (para controladores)
- Dispara `SwitchDbEvent` del bundle (el bundle hace el cambio de conexión)

### 🔄 Flujo de cambio de BD (nuevo):
```
1. Request → TenantDatabaseSwitchListener
2. Extrae subdomain del host
3. TenantResolver consulta melisa_central
4. Guarda en TenantContext
5. Dispara SwitchDbEvent(tenantId)
6. DbSwitchEventListener (del bundle) escucha
7. Llama CustomTenantConfigProvider.getTenantConnectionConfig(tenantId)
8. TenantEntityManager.clear() + switchConnection(params)
9. ✅ Conexión cambiada a BD del tenant
```

### 📄 Archivos modificados/creados:
- `src/Service/CustomTenantConfigProvider.php` - Nuevo provider
- `src/EventListener/TenantDatabaseSwitchListener.php` - Nuevo listener
- `config/services.yaml` - Registro de servicios
- `config/packages/hakam_multi_tenancy.yaml` - tenant_config_provider configurado

### ⚠️ Punto de verificación PASADO:
```bash
✅ php bin/console cache:warmup
✅ php bin/console debug:container CustomTenantConfigProvider
✅ php bin/console debug:container TenantDatabaseSwitchListener
✅ php bin/console debug:container tenant_entity_manager
```

### 🔧 Cambios en arquitectura:
- ✅ Ahora usa `TenantEntityManager` del bundle (vía autowiring)
- ✅ Cambio de conexión via `SwitchDbEvent` (evento del bundle)
- ✅ Mantiene `TenantResolver` y `TenantContext` (código existente)
- ✅ `TenantConnectionListener` antiguo comentado (backup temporal)

**Estado:** Bundle integrado con lógica existente. TenantEntityManager y eventos funcionando.

---

## 📋 FASE 3: ACTUALIZAR CONTROLADORES Y REPOSITORIOS ✅ COMPLETADA
**Duración real:** 30 minutos  
**Objetivo:** Migrar controladores para usar TenantEntityManager

### ✅ Tareas completadas:
- [x] Actualizar `AbstractMantenedorController` para usar `TenantEntityManager`
- [x] Cambiar `EntityManagerInterface` por `Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager`
- [x] Registrar alias `TenantConfigProviderInterface` → `CustomTenantConfigProvider`
- [x] Crear comando de prueba `app:test-tenant-em`
- [x] Verificar funcionamiento end-to-end

### 📝 Cambios implementados:

**AbstractMantenedorController** (`src/Controller/Mantenedores/AbstractMantenedorController.php`):
```php
// ANTES:
use Doctrine\ORM\EntityManagerInterface;
protected EntityManagerInterface $entityManager;
public function __construct(EntityManagerInterface $entityManager, ...)

// AHORA:
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
protected TenantEntityManager $entityManager;
public function __construct(TenantEntityManager $entityManager, ...)
```

**config/services.yaml**:
- Añadido alias: `Hakam\MultiTenancyBundle\Port\TenantConfigProviderInterface` → `App\Service\CustomTenantConfigProvider`
- Permite que el bundle use nuestro provider personalizado

### 🧪 Pruebas realizadas:

Comando `app:test-tenant-em` ejecuta 6 pruebas:
1. ✅ Lista tenants activos desde melisa_central
2. ✅ Resuelve tenant específico (melisalacolina)
3. ✅ CustomTenantConfigProvider retorna config correcta
4. ✅ SwitchDbEvent se dispara sin errores
5. ✅ Conexión cambia a melisalacolina (SELECT DATABASE())
6. ✅ Cambio dinámico a melisa_template funciona

**Resultado:** Todas las pruebas pasaron exitosamente.

### 🔄 Flujo completo funcionando:
```
1. SwitchDbEvent('melisalacolina')
2. DbSwitchEventListener escucha
3. CustomTenantConfigProvider.getTenantConnectionConfig('melisalacolina')
4. TenantResolver.getTenantBySlug('melisalacolina')
5. Query a melisa_central: SELECT * FROM tenant WHERE subdomain='melisalacolina'
6. Retorna TenantConnectionConfigDTO(dbname='melisalacolina', ...)
7. TenantConnection.switchConnection(['dbname' => 'melisalacolina', ...])
8. ✅ SELECT DATABASE() retorna 'melisalacolina'
```

### 📄 Archivos modificados:
- `src/Controller/Mantenedores/AbstractMantenedorController.php` - Usa TenantEntityManager
- `config/services.yaml` - Alias TenantConfigProviderInterface
- `config/packages/hakam_multi_tenancy.yaml` - Config provider comentado
- `src/Command/TestTenantEntityManagerCommand.php` - Comando de prueba

### ⚠️ Punto de verificación PASADO:
```bash
✅ php bin/console cache:warmup
✅ php bin/console debug:autowiring TenantEntityManager
✅ php bin/console app:test-tenant-em
✅ Cambio dinámico de BD funciona correctamente
```

**Estado:** TenantEntityManager totalmente funcional. Controladores actualizados. Sistema probado end-to-end.

---

## 📋 FASE 4: LIMPIEZA Y OPTIMIZACIÓN (PRÓXIMA)
**Duración estimada:** 2-3 días  
**Objetivo:** Cambiar conexión con evento en lugar de TenantResolver manual

### ✅ Tareas:
- [ ] Crear listener para `SwitchDbEvent`
- [ ] Integrar con TenantContext existente
- [ ] Mantener TenantResolver para consultas a melisa_central
- [ ] Actualizar EventSubscriber para usar SwitchDbEvent
- [ ] Testear cambio de conexión con evento

### 📝 Entregables:
- Listener funcionando
- TenantResolver y SwitchDbEvent coexistiendo

### ⚠️ Punto de verificación:
```bash
# Debe cambiar conexión correctamente
curl http://melisalacolina.melisaupgrade.prod/dashboard
```

---

## 📋 FASE 4: CONFIGURAR BUNDLE (Solo para Tenants)
**Duración estimada:** 1-2 días  
**Objetivo:** Activar bundle solo para gestión de tenant DBs

### ✅ Tareas:
- [ ] Habilitar bundle en `config/bundles.php`
- [ ] Crear `config/packages/hakam_multi_tenancy.yaml`
- [ ] Configurar tenant_connection (dinámico)
- [ ] Configurar tenant_migration paths
- [ ] OMITIR configuración de Main (no aplica)
- [ ] Verificar servicios del bundle

### 📝 Entregables:
- `config/packages/hakam_multi_tenancy.yaml` (solo tenant config)
- Servicios del bundle disponibles

### ⚠️ Punto de verificación:
```bash
# Verificar servicios registrados (solo tenant)
php bin/console debug:container | grep -i tenant
```

---

## 📋 FASE 5: MIGRAR A SWITCHDBEVENT COMPLETO
**Duración estimada:** 3-4 días  
**Objetivo:** Reemplazar TenantResolver con SwitchDbEvent del bundle

### ✅ Tareas:
- [ ] Actualizar controladores para usar `SwitchDbEvent`
- [ ] Mantener TenantResolver solo para consultas a melisa_central (via HTTP/API)
- [ ] Crear adapter si necesitas consultar melisa_central desde melisa_tenant
- [ ] Agregar logging para monitorear cambios de tenant
- [ ] Testear con todos los tenants activos

### 📝 Entregables:
- Controllers usando `SwitchDbEvent`
- Adapter para comunicación con melisa_central (si necesario)
- Logs de cambios de tenant

### ⚠️ Punto de verificación:
```bash
# Eventos deben dispararse correctamente
curl http://melisalacolina.melisaupgrade.prod/dashboard
```

---

## 📋 FASE 6: MIGRAR COMANDOS DE CONSOLA
**Duración estimada:** 3-4 días  
**Objetivo:** Usar comandos del bundle

### ✅ Tareas:
- [ ] Mapear `app:migrate-tenant` → `tenant:migration:migrate`
- [ ] Mapear `app:migrations-tenant` → `tenant:migration:diff`
- [ ] Crear aliases temporales para comandos antiguos
- [ ] Migrar lógica custom a comandos del bundle
- [ ] Actualizar documentación de comandos
- [ ] Actualizar scripts de deploy

### 📝 Entregables:
- Comandos del bundle funcionando
- Aliases de compatibilidad
- Documentación actualizada

### ⚠️ Punto de verificación:
```bash
# Comandos nuevos deben funcionar
php bin/console tenant:migration:diff --dbid=1
php bin/console tenant:migration:migrate update --all
```

---

## 📋 FASE 7: ELIMINAR CÓDIGO LEGACY
**Duración estimada:** 2-3 días  
**Objetivo:** Limpiar implementación antigua

### ✅ Tareas:
- [ ] Eliminar `TenantSubscriber` antiguo
- [ ] Eliminar `app:migrate-tenant` command
- [ ] Eliminar `app:migrations-tenant` command
- [ ] Eliminar métodos deprecados de `Tenant` entity
- [ ] Eliminar `TenantResolver` si ya no se usa
- [ ] Actualizar todos los imports
- [ ] Ejecutar PHPStan/Psalm para detectar código muerto

### 📝 Entregables:
- Código legacy eliminado
- Tests pasando
- No hay código muerto

### ⚠️ Punto de verificación:
```bash
# Sin errores
vendor/bin/phpstan analyse src/
php bin/console lint:container
```

---

## 📋 FASE 8: OPTIMIZACIÓN Y FIXTURES
**Duración estimada:** 2-3 días  
**Objetivo:** Aprovechar features avanzados del bundle

### ✅ Tareas:
- [ ] Crear fixtures con `#[TenantFixture]`
- [ ] Implementar `TenantConfigProviderInterface` custom si necesario
- [ ] Optimizar queries con nuevo TenantEntityManager
- [ ] Agregar tests de integración
- [ ] Documentar arquitectura final
- [ ] Crear guía de desarrollo para equipo

### 📝 Entregables:
- Fixtures de tenant
- Tests de integración
- Documentación completa

---

## 📋 FASE 9: TESTING Y VALIDACIÓN
**Duración estimada:** 2-3 días  
**Objetivo:** Validar todo funciona correctamente

### ✅ Tareas:
- [ ] Tests unitarios al 80% cobertura
- [ ] Tests de integración para multi-tenancy
- [ ] Tests end-to-end en melisalacolina
- [ ] Performance testing (comparar con versión antigua)
- [ ] Security audit
- [ ] Load testing con múltiples tenants

### 📝 Entregables:
- Suite de tests completa
- Reporte de performance
- Reporte de seguridad

---

## 📋 FASE 10: DEPLOY A PRODUCCIÓN
**Duración estimada:** 1 día  
**Objetivo:** Llevar a producción de forma segura

### ✅ Tareas:
- [ ] Merge de `multitenancy` a `master`
- [ ] Tag de versión (v2.0.0)
- [ ] Deploy en staging primero
- [ ] Validación en staging
- [ ] Deploy a producción
- [ ] Monitoring post-deploy
- [ ] Rollback plan preparado

### 📝 Entregables:
- Código en producción
- Monitoring activo
- Documentación de rollback

---

## 📊 RESUMEN

| Fase | Duración | Riesgo | Prioridad |
|------|----------|--------|-----------|
| 1. Preparación | 1-2 días | Bajo | Alta |
| 2. Interfaces | 2-3 días | Bajo | Alta |
| 3. Entity Managers | 2-3 días | Medio | Alta |
| 4. Activar Bundle | 1-2 días | Bajo | Alta |
| 5. SwitchDbEvent | 3-4 días | Alto | Alta |
| 6. Comandos | 3-4 días | Medio | Media |
| 7. Cleanup | 2-3 días | Bajo | Media |
| 8. Optimización | 2-3 días | Bajo | Baja |
| 9. Testing | 2-3 días | Medio | Alta |
| 10. Deploy | 1 día | Alto | Alta |

**Duración Total Estimada:** 19-30 días (~4-6 semanas)

---

## 🚨 PUNTOS DE NO RETORNO

### Checkpoint 1: Después de Fase 3
- Si algo falla aquí, todavía puedes volver fácilmente
- Código legacy sigue funcionando

### Checkpoint 2: Después de Fase 5
- Ambos sistemas funcionan en paralelo
- Rollback más complejo pero posible

### Checkpoint 3: Después de Fase 7
- Ya no hay vuelta atrás fácil
- Debes tener tests pasando 100%

---

## 📞 CONTACTO Y SOPORTE

- **Bundle Issues:** https://github.com/RamyHakam/multi_tenancy_bundle/issues
- **Documentation:** https://ramyhakam.github.io/multi_tenancy_bundle/

---

## 🔄 ESTADO ACTUAL

**Fase Actual:** FASE 1 - PREPARACIÓN  
**Última Actualización:** 2025-11-11  
**Branch:** multitenancy  
**Progreso:** 10% (1/10 fases)
