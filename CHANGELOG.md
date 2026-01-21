# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-01-21

### Agregado

#### Sistema de Permisos Híbrido Multi-Tenant

- **Nuevas Entidades:**
  - `TenantPermissionProfile`: Almacena el perfil de permisos del tenant (collaborative/restrictive/custom)
  - `TenantModulePermissionOverride`: Sobrescribe permisos a nivel de módulo con roles requeridos en JSON

- **Patrón Strategy para Permisos:**
  - `CollaborativePermissionStrategy`: Acceso multi-rol con permisos por defecto
  - `RestrictivePermissionStrategy`: Acceso solo para administradores
  - `CustomPermissionStrategy`: Permisos basados en base de datos con overrides
  - `PermissionStrategyFactory`: Factory para crear estrategias según perfil del tenant

- **NavbarBuilder Service:**
  - Sistema de construcción de menú de 3+ niveles jerárquicos
  - Filtrado dinámico basado en roles y permisos del tenant
  - Soporte para hasta 4 niveles de profundidad (Mantenedores → Básico → Antecedentes → Tipo)
  - 15 items en nivel Básico, estructura completa en Clínico y Geográficos
  - Integración con MenuItem value object para recursión

- **Sistema de Componentes Dashboard:**
  - `DashboardExtension`: Extensión Twig con función `include_tenant_component()`
  - Sistema de fallback automático: `tenants/{tenant}/{component}` → `components/{component}`
  - 6 componentes base reutilizables:
    * `_welcome_banner.html.twig`
    * `_stats_cards.html.twig`
    * `_appointments_table.html.twig`
    * `_quick_actions.html.twig`
    * `_daily_summary.html.twig`
    * `_dashboard_styles.html.twig`
  - 3 componentes personalizados para WiClinic:
    * `_welcome_banner.html.twig` (tema azul médico)
    * `_quick_actions.html.twig` (5 botones especializados)
    * `_appointments_table.html.twig` (estilos personalizados)
  - Dashboard modularizado de 470 líneas a 27 líneas

- **Navegación Mejorada:**
  - Navbar con dropdown de usuario (avatar, perfil, logout)
  - Sidebar con sistema recursivo de 3+ niveles usando macros Twig
  - Sistema de iconos por nivel con Font Awesome 6 y Boxicons
  - Colores distintivos por nivel: Azul (#5e72e4), Morado (#8b5cf6), Naranja (#f59e0b)
  - Tooltips CSS para textos largos con ellipsis
  - Animaciones suaves de collapse con Bootstrap 5

- **Herramientas CLI:**
  - `TenantPermissionProfileCommand`: Gestión de perfiles de permisos por CLI
  - Uso de DBAL para queries directas a base de datos de tenant
  - Comandos: `show-profile`, `set-profile {type}`

- **Documentación:**
  - `docs/SISTEMA_PERMISOS_MULTI_TENANT.md`: Documentación completa del sistema híbrido
  - Ejemplos de uso y configuración
  - Guía de estrategias y casos de uso

### Modificado

- **Dashboard Componentizado:**
  - Reducción de código de 470 líneas a 27 líneas en `dashboard/default.html.twig`
  - Componentes reutilizables sin necesidad de tablas en base de datos
  - Soporte para personalización por tenant sin modificar código base

- **Optimización Visual del Sidebar:**
  - Ancho optimizado de 260px → 350px para mejor legibilidad
  - Jerarquía visual ultra clara con sistema de colores por nivel
  - Tamaños de fuente graduales: 0.95rem → 0.87rem → 0.8rem → 0.75rem
  - Paddings incrementales: 1rem → 1.5rem → 2rem → 2.3rem
  - Bordes laterales distintivos: 5px → 4px → 3px según nivel
  - Sistema de ellipsis + tooltips para textos largos

- **Arquitectura de Permisos:**
  - Sistema de dos capas:
    * NavbarBuilder: Visibilidad de elementos en UI
    * PermissionVoter: Autorización a nivel de controlador (existente)
  - Separación de responsabilidades entre UI y lógica de negocio

### Corregido

- Rutas inexistentes configuradas como `null` con comentarios TODO:
  - `app_patients`
  - `app_appointments`
  - Rutas de mantenimiento sin implementar
- EntityManager apuntando a DB central en comandos (solucionado con DBAL)
- Problema de visibilidad en niveles 3 y 4 del sidebar (5 iteraciones de refinamiento)
- Truncamiento de texto en elementos de menú profundos (ellipsis + tooltips)

### Detalles Técnicos

- **43 archivos modificados**: +6,283 líneas, -874 líneas
- **Commits**: 14 commits en feature/dashboard
- **Merge commits**: 
  - b06b8e5 (develop)
  - 13993cf (master)
- **Migraciones**: `Version20260121124317.php` (tablas de permisos)
- **Branch eliminada**: feature/dashboard (local y remota)

## [1.1.0] - 2026-01-14

### Agregado

- Sistema de permisos a nivel de campo (FieldAccess)
- Voter personalizado (PermissionVoter) con lógica de cascada de permisos
- Extensión Twig para verificación de permisos en templates
- Tests unitarios: SecurityExtensionTest (9 tests) y FieldAccessTest (10 tests)
- Documentación completa de migraciones Hakam en `docs/MIGRACIONES_HAKAM.md`
- Cache in-memory para optimización de permisos
- Implementación de SecuredResourceInterface en entidades
- Controlador y vistas de testing para sistema de permisos

### Modificado

- Total de tests aumentado a 41 con 118 assertions

## [1.0.0] - 2026-01-14

### Agregado

- Script de deploy automatizado (`scripts/deploy.sh`) con 10 pasos
- Ejecución de tests unitarios en proceso de deploy
- Detección automática de entorno (dev/prod) para instalación de dependencias
- 12 tests unitarios para TenantResolver
- Documentación completa de Git Flow en `GIT_WORKFLOW.md`
- Documentación de proceso de migración en `SYMFONY_7.4_MIGRATION_PLAN.md`
- Sistema multi-tenancy con hakam/multi-tenancy-bundle v2.9.3
- Comando de prueba de multi-tenancy: `TestMultiTenancyCommand`
- Backups automáticos en cada deploy
- Configuración de CSRF y Property Info

### Modificado

- **BREAKING**: Migración de Symfony 6.4.29 a Symfony 7.4.3 LTS
- **BREAKING**: Requerimiento mínimo de PHP 8.2+
- Actualización de todas las dependencias de Symfony a versión 7.4.*
- Refactorización de entidades: `Pais` → `Country`, `Sexo` → `Gender`
- Actualización de repositorios para compatibilidad con Symfony 7.4
- Mejora en README.md con instrucciones actualizadas
- Optimización de composer.json eliminando scripts inexistentes

### Corregido

- Eliminación de animación particles.js que causaba error en página de login
- Corrección de comandos symfony-cmd inexistentes en composer auto-scripts
- Ajuste de clases CSS en template de login para evitar errores JavaScript
- Corrección de formato Markdown en toda la documentación

### Removed

- Entidades obsoletas: `Pais.php`, `Sexo.php`
- Repositorios obsoletos: `PaisRepository.php`, `SexoRepository.php`
- Scripts particles.js y particles.app.js del template de login
- Dependencias de desarrollo en builds de producción (--no-dev)

### Security

- Actualización a Symfony 7.4.3 LTS con soporte hasta 2029
- Mejoras de seguridad incluidas en nueva versión de framework

---

## [Unreleased]

### Planeado

- Deploy a servidor de staging
- Monitoreo de logs post-deploy
- Optimización de performance
- Documentación de API endpoints

---

**Formato de versiones:**

- **MAJOR** (X.0.0): Cambios incompatibles con versiones anteriores
- **MINOR** (0.X.0): Nueva funcionalidad compatible con versión anterior
- **PATCH** (0.0.X): Correcciones de bugs compatibles

[1.0.0]: https://github.com/carlosalarcong/melisa_tenant/releases/tag/v1.0.0
