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

## 📋 FASE 1: PREPARACIÓN Y ANÁLISIS
**Duración estimada:** 1-2 días  
**Objetivo:** Preparar el terreno sin romper nada

### ✅ Tareas:
- [x] Crear branch `multitenancy` desde master
- [ ] Instalar bundle sin activar
- [ ] Auditar estructura actual de código
- [ ] Mapear entidades actuales vs estructura bundle
- [ ] Crear backup de base de datos
- [ ] Documentar configuración actual

### 📝 Entregables:
- `composer.json` con bundle instalado
- Documento de mapeo de entidades
- Backup SQL de melisa_central

---

## 📋 FASE 2: IMPLEMENTAR TENANTENTITYMANAGER
**Duración estimada:** 2-3 días  
**Objetivo:** Usar TenantEntityManager del bundle (sin Main EM)

### ✅ Tareas:
- [ ] Configurar `TenantEntityManager` en doctrine.yaml
- [ ] Mantener connection "default" apuntando a tenant dinámico
- [ ] NO crear Entity Manager "default" para Main (no lo necesitas)
- [ ] Actualizar servicios para inyectar `TenantEntityManager`
- [ ] Mantener código actual funcionando en paralelo

### 📝 Entregables:
- `config/packages/doctrine.yaml` con TenantEntityManager
- TenantEntityManager disponible como servicio

### ⚠️ Punto de verificación:
```bash
# Verificar que TenantEntityManager se registró
php bin/console debug:container TenantEntityManager
```

---

## 📋 FASE 3: IMPLEMENTAR SWITCHDBEVENT
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
