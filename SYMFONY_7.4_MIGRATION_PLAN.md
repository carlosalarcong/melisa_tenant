# Plan de Migración Symfony 6.4 → 7.4 LTS

**Fecha de inicio:** 8 de enero 2026  
**Estimación total:** ~4 semanas (1 mes)  
**Branch:** `feature/upgrade-symfony-7.4`

---

## **FASE 1: Preparación y Análisis** (2-3 días)

### 1.1 Crear Branch de Migración

- [x] Crear branch `feature/upgrade-symfony-7.4`
- [x] Verificar que estamos en un punto estable del proyecto

### 1.2 Actualizar PHP

- [x] Verificar versión actual de PHP: `php -v` (PHP 8.3.26 ✅)
- [x] Actualizar servidor a PHP 8.2 o 8.3 (Ya instalado PHP 8.3.26)
- [x] Actualizar `composer.json`: `"php": ">=8.2"`
- [x] Verificar extensiones PHP requeridas instaladas (ctype, iconv, intl, json, mbstring, pdo, tokenizer, xml ✅)

### 1.3 Auditar Deprecaciones

- [x] Habilitar modo estricto en `.env.local`: `SYMFONY_DEPRECATIONS_HELPER=max[direct]=0`
- [x] Ejecutar `php bin/console cache:clear --env=dev`
- [x] Ejecutar tests para detectar deprecaciones: `php bin/phpunit`
- [x] Documentar todas las deprecaciones encontradas

**Deprecaciones encontradas y corregidas:**
- ✅ Anotación `@Route` en SettingsController - corregida
- ✅ Entidades ya usan atributos PHP 8
- ✅ Controllers ya usan atributos PHP 8 (16 archivos)
- ✅ No se encontró uso de `$request->get()` deprecado

### 1.4 Revisar Dependencias

- [x] Ejecutar `composer outdated` y documentar paquetes desactualizados
- [x] Verificar compatibilidad: `composer why-not symfony/framework-bundle:^7.4`
- [x] Verificar compatibilidad de `hakam/multi-tenancy-bundle` (✅ ya confirmado v2.9.3)
- [x] Listar paquetes de terceros que necesiten actualización

**Resumen:** Proyecto en excelente estado, listo para actualizar a Symfony 7.4

---

## **FASE 2: Corrección de Deprecaciones** (3-5 días)

### 2.1 Controllers

- [ ] Buscar uso de `$request->get()` deprecado
- [ ] Reemplazar por `$request->query->get()` o `$request->request->get()`
- [ ] Verificar uso de `AbstractController` en todos los controllers
- [ ] Actualizar type hints en métodos de controllers

### 2.2 Security

- [ ] Revisar configuración en `config/packages/security.yaml`
- [ ] Actualizar firewalls si usan guards antiguos
- [ ] Verificar atributos `@IsGranted` y convertir a `#[IsGranted]`
- [ ] Probar sistema de voters

### 2.3 Forms

- [ ] Convertir anotaciones a atributos PHP 8 en entidades
- [ ] Actualizar constraints de validación: `@Assert\*` → `#[Assert\*]`
- [ ] Verificar FormTypes personalizados
- [ ] Probar forms principales del sistema

### 2.4 Services

- [ ] Verificar que servicios usen autowiring
- [ ] Remover configuraciones obsoletas en `services.yaml`
- [ ] Verificar inyección de dependencias
- [ ] Actualizar servicios a servicios privados si aplica

### 2.5 Routes

- [ ] Convertir anotaciones de rutas a atributos PHP 8: `@Route` → `#[Route]`
- [ ] Verificar configuración de rutas en `config/routes/`
- [ ] Probar generación de URLs

### 2.6 Doctrine

- [ ] Verificar configuración de Entity Managers (default y tenant)
- [ ] Actualizar anotaciones de Doctrine a atributos en entidades
- [ ] Verificar repositorios personalizados

---

## **FASE 3: Actualización de Composer** (1 día)

### 3.1 Backup

- [x] Crear backup: `cp composer.json composer.json.backup`
- [x] Crear backup: `cp composer.lock composer.lock.backup`
- [x] Commit de estado actual antes de actualizar

### 3.2 Actualizar restricción de Symfony

- [x] Actualizar `extra.symfony.require` a `"7.4.*"` en composer.json
- [x] Actualizar `"php": ">=8.2"` en composer.json

### 3.3 Actualizar paquetes Symfony

- [x] Actualizar todos los paquetes `symfony/*` a 7.4.*
- [x] 64 paquetes actualizados exitosamente
- [x] 2 paquetes nuevos instalados (polyfill-php85, type-info)

### 3.4 Actualizar paquetes dev

- [x] Todos los paquetes dev actualizados a 7.4.*

### 3.5 Ejecutar actualización

- [x] `composer update symfony/* --with-all-dependencies`
- [x] Resolver conflictos de dependencias si aparecen
- [x] Verificar que no hay errores de composer

### 3.6 Actualizar otros paquetes

- [x] Doctrine actualizado (3.5.8 → 3.6.0)
- [x] Monolog actualizado (3.9.0 → 3.10.0)
- [x] Twig actualizado (3.22.1 → 3.22.2)

**Breaking changes corregidos:**
- ✅ Command::execute() debe retornar int
- ✅ SettingSyncCommand corregido

**Resultado:** ✅ **Symfony 7.4.3 LTS instalado exitosamente**

---

## **FASE 4: Testing Multi-Tenancy** (4-6 días) 🔥 **CRÍTICO**

### 4.1 Tests Unitarios

- [x] Ejecutar `php bin/phpunit tests/Unit/`
- [x] PHPUnit 12.4.5 instalado correctamente
- [ ] 12 tests fallando - necesitan actualización (TenantResolver constructor)
- [ ] Corregir tests fallidos para SF7.4

### 4.2 Tests de Conexión Multi-Tenant

- [x] Verificar evento `SwitchDbEvent` funciona correctamente
- [x] Probar cambio dinámico de base de datos entre tenants
- [x] Validar `TenantEntityManager` se conecta correctamente
- [x] Verificar aislamiento de datos entre tenants
- [x] TenantResolver operativo - resuelve melisalacolina y template
- [x] Event listeners registrados (TenantDatabaseSwitchListener priority 1000)
- [x] Comando de prueba creado: `test:multi-tenancy`
- [x] Base de datos melisalacolina existe con 52 tablas

### 4.3 Tests de Migraciones

- [x] Verificar migraciones en `migrations/Main/` (vacío - no necesarias)
- [x] Verificar migraciones en `migrations/Tenant/` (2 migraciones existentes)
- [x] Entity Main\TenantDb mapeado correctamente
- [x] Comandos tenant disponibles (tenant:database:create, tenant:migrations:migrate)
- [ ] Probar ejecución de migraciones en tenant nuevo (si se necesita)

### 4.4 Tests de Entidades

- [x] Entidades en `src/Entity/Main/` funcionan (TenantDb)
- [x] Entidades en `src/Entity/Tenant/` gestionadas por bundle hakam
- [x] Repositorios actualizados: CountryRepository, GenderRepository
- [x] Entidades traducidas: Pais→Country, Sexo→Gender
- [ ] Probar relaciones entre entidades
- [ ] Verificar cascadas y eventos de Doctrine

### 4.5 Tests Funcionales

- [x] Cache limpiado exitosamente en modo dev
- [x] Rutas registradas correctamente (login, logout, dashboard, settings)
- [ ] Probar login en diferentes tenants
- [ ] Verificar sistema de traducciones por tenant
- [ ] Validar carga de configuraciones específicas por tenant
- [ ] Probar módulo de Recaudación
- [ ] Probar mantenedores (países, etc.)
- [ ] Verificar Dashboard funciona correctamente

**Resultado parcial:** ✅ **Multi-tenancy funciona correctamente con SF7.4.3**
**Pendiente:** Tests unitarios necesitan actualización, assets con controllers por tenant

---

## **FASE 5: Migraciones y Assets** (2-3 días)

### 5.1 Regenerar Cache

- [x] `php bin/console cache:clear --env=dev`
- [ ] `php bin/console cache:clear --env=prod --no-warmup`
- [ ] `php bin/console cache:warmup --env=prod`
- [ ] Verificar que no hay errores en warmup

### 5.2 Verificar Assets

- [x] `php bin/console importmap:install` - No assets pendientes
- [ ] `php bin/console asset-map:compile` - ⚠️ Error con controllers por tenant
- [ ] Verificar que assets se cargan correctamente en navegador
- [ ] Probar Stimulus controllers en `/assets/controllers/`
- [ ] Verificar Turbo funciona correctamente
- [ ] Probar carga de archivos CSS y JS

**Nota:** Controllers de Stimulus específicos por tenant necesitan revisión (internal/melisalacolina/patient_controller.js)

### 5.3 Migraciones de Base de Datos

- [ ] `php bin/console doctrine:migrations:status`
- [ ] Verificar que no hay cambios de schema no esperados
- [ ] Probar migración en BD de prueba si hay cambios

### 5.4 Sistema de Traducciones

- [ ] Verificar traducciones en `translations/messages.es.yaml`
- [ ] Verificar traducciones en `translations/messages.en.yaml`
- [ ] Probar sistema de traducciones por tenant en `translations/demo/`
- [ ] Verificar carga dinámica de traducciones

---

## **FASE 6: Nuevas Features de SF7.4** (Opcional, 2-3 días)

### 6.1 Aprovechar Atributos PHP 8

- [ ] Convertir routes a atributos modernos con opciones avanzadas
- [ ] Usar `#[IsGranted]` en controllers donde aplique
- [ ] Implementar atributos en services si aplica

### 6.2 MapRequestPayload (nuevo en SF7)

- [ ] Identificar endpoints API que puedan usar `#[MapRequestPayload]`
- [ ] Implementar DTOs con MapRequestPayload
- [ ] Probar serialización automática

### 6.3 Mejoras de Performance

- [ ] Verificar mejoras de AssetMapper
- [ ] Revisar configuración de HTTP client
- [ ] Verificar mejoras de serialización

### 6.4 Explorar Nuevas Features

- [ ] Revisar changelog de Symfony 7.4
- [ ] Documentar features útiles para el proyecto
- [ ] Implementar features prioritarias

---

## **FASE 7: Testing Integral** (3-4 días)

### 7.1 Testing Manual Completo

- [ ] Login en tenant principal
- [ ] Login en tenant secundario/demo
- [ ] Cambio entre diferentes tenants
- [ ] CRUD de módulo Recaudación
- [ ] Mantenedor de países
- [ ] Configuraciones del sistema
- [ ] Gestión de usuarios y permisos
- [ ] Traducciones dinámicas funcionando
- [ ] Assets y Stimulus controllers funcionando
- [ ] Navegación Turbo sin errores

### 7.2 Testing Automatizado

- [ ] `php bin/phpunit` - todos los tests
- [ ] Verificar coverage: `php bin/phpunit --coverage-html var/coverage`
- [ ] Revisar reporte de coverage
- [ ] Corregir tests fallidos

### 7.3 Performance Testing

- [ ] Usar Web Profiler para verificar performance: `/_profiler`
- [ ] Comparar tiempos de respuesta vs Symfony 6.4
- [ ] Verificar queries de base de datos optimizadas
- [ ] Verificar uso de memoria

### 7.4 Revisión de Logs

- [ ] Revisar `var/log/dev.log` sin errores críticos
- [ ] Revisar `var/log/prod.log` sin errores críticos
- [ ] Verificar logs de deprecaciones resueltas
- [ ] Configurar monitoreo de logs

---

## **FASE 8: Deploy a Staging** (1-2 días)

### 8.1 Preparar Entorno Staging

- [ ] Actualizar PHP a 8.2+ en servidor staging
- [ ] Verificar extensiones PHP necesarias instaladas
- [ ] Backup completo de BD staging
- [ ] Backup de archivos de staging

### 8.2 Deploy a Staging

- [ ] Merge branch a staging: `git checkout staging && git merge feature/upgrade-symfony-7.4`
- [ ] Push a staging
- [ ] SSH al servidor staging
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php bin/console cache:clear --env=prod`
- [ ] `php bin/console cache:warmup --env=prod`
- [ ] `php bin/console asset-map:compile`
- [ ] Verificar permisos de directorios

### 8.3 Smoke Testing en Staging

- [ ] Verificar que la aplicación levanta sin errores
- [ ] Probar login
- [ ] Probar funcionalidades críticas
- [ ] Probar multi-tenancy en staging
- [ ] Verificar traducciones
- [ ] Verificar assets cargan correctamente

### 8.4 Monitoreo Staging

- [ ] Monitorear logs durante 24 horas
- [ ] Verificar performance es aceptable
- [ ] Recopilar feedback de QA/usuarios de prueba
- [ ] Documentar issues encontrados
- [ ] Resolver issues críticos antes de producción

---

## **FASE 9: Deploy a Producción** (1 día)

### 9.1 Preparativos Pre-Deploy

- [ ] Definir ventana de mantenimiento (horario bajo tráfico)
- [ ] Notificar a usuarios sobre mantenimiento
- [ ] Preparar comunicados
- [ ] Coordinar equipo para deploy

### 9.2 Backups Producción

- [ ] Backup completo BD: `mysqldump -u root -p --all-databases > backup_pre_sf74.sql`
- [ ] Backup de código: `tar -czf backup_code.tar.gz /var/www/html/melisa_tenant`
- [ ] Verificar backups se crearon correctamente
- [ ] Almacenar backups en ubicación segura

### 9.3 Deploy Producción

- [ ] Activar modo mantenimiento
- [ ] Merge branch a main: `git checkout main && git merge feature/upgrade-symfony-7.4`
- [ ] Tag de versión: `git tag v2.0.0-sf74`
- [ ] Push a producción
- [ ] SSH al servidor producción
- [ ] `composer install --no-dev --optimize-autoloader --no-scripts`
- [ ] `php bin/console doctrine:migrations:migrate --no-interaction`
- [ ] `php bin/console cache:clear --env=prod`
- [ ] `php bin/console cache:warmup --env=prod`
- [ ] `php bin/console asset-map:compile`
- [ ] Verificar permisos
- [ ] Desactivar modo mantenimiento

### 9.4 Verificación Post-Deploy

- [ ] Verificar aplicación levanta sin errores (5 min)
- [ ] Probar login (5 min)
- [ ] Probar funcionalidades críticas (15 min)
- [ ] Verificar multi-tenancy funciona (10 min)
- [ ] Monitoreo activo durante 1 hora
- [ ] Revisar logs en tiempo real

### 9.5 Rollback Plan

- [ ] Script de rollback preparado y probado
- [ ] Procedimiento documentado para restaurar SF 6.4
- [ ] Tiempo estimado de rollback: < 15 minutos
- [ ] Equipo en standby para rollback si es necesario

---

## **FASE 10: Post-Deploy** (1-2 días)

### 10.1 Monitoreo Intensivo

- [ ] Revisar logs cada hora durante primer día
- [ ] Monitorear performance y tiempos de respuesta
- [ ] Verificar que no hay errores 500
- [ ] Escuchar feedback de usuarios
- [ ] Monitorear uso de recursos del servidor

### 10.2 Optimización

- [ ] Ajustar configuraciones de cache si es necesario
- [ ] Optimizar queries lentas identificadas
- [ ] Ajustar configuración de Doctrine
- [ ] Optimizar assets si es necesario

### 10.3 Documentación

- [ ] Actualizar README.md con versión Symfony 7.4
- [ ] Documentar nuevas features implementadas
- [ ] Actualizar guías de desarrollo
- [ ] Documentar proceso de migración y lecciones aprendidas
- [ ] Actualizar requisitos de sistema (PHP 8.2+)

### 10.4 Cierre del Proyecto

- [ ] Retrospectiva del equipo
- [ ] Documentar issues y soluciones
- [ ] Archivar backups
- [ ] Celebrar migración exitosa! 🎉

---

## **Checklist de Verificación Final** ✅

### Pre-Migración

- [ ] PHP 8.2+ instalado en todos los entornos
- [ ] Backup de BD y código completo
- [ ] Branch de migración creado
- [ ] Deprecaciones documentadas y corregidas

### Durante Migración

- [ ] Composer actualizado sin errores
- [ ] Tests unitarios pasando 100%
- [ ] Tests funcionales pasando 100%
- [ ] Multi-tenancy funcionando correctamente
- [ ] Assets compilando correctamente
- [ ] Sin deprecations warnings

### Post-Migración

- [ ] Deploy exitoso en staging sin rollback
- [ ] Testing manual completo sin issues críticos
- [ ] Performance igual o mejor que SF 6.4
- [ ] Logs sin errores críticos
- [ ] Deploy a producción exitoso
- [ ] Usuarios usando sistema sin problemas
- [ ] Documentación actualizada

---

## **Métricas de Éxito** 📊

- [ ] 0 errores críticos en producción
- [ ] Performance igual o mejor (< 10% diferencia)
- [ ] 100% de funcionalidades operativas
- [ ] 0 rollbacks necesarios
- [ ] Feedback positivo de usuarios
- [ ] Tests pasando al 100%

---

## **Contactos y Recursos** 📞

- **Responsable técnico:** _[Nombre]_
- **Backup técnico:** _[Nombre]_
- **Documentación Symfony 7.4:** <https://symfony.com/doc/7.4/>
- **Upgrade Guide:** <https://github.com/symfony/symfony/blob/7.4/UPGRADE-7.4.md>
- **Multi-tenancy Bundle:** <https://github.com/RamyHakam/multi_tenancy_bundle>

---

## **Notas y Observaciones** 📝

_Agregar aquí notas importantes durante el proceso de migración..._

---

**Última actualización:** 8 de enero 2026  
**Estado:** 🔴 No iniciado | 🟡 En progreso | 🟢 Completado
