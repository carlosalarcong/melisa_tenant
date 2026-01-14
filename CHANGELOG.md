# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-14

### Added

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

### Changed

- **BREAKING**: Migración de Symfony 6.4.29 a Symfony 7.4.3 LTS
- **BREAKING**: Requerimiento mínimo de PHP 8.2+
- Actualización de todas las dependencias de Symfony a versión 7.4.*
- Refactorización de entidades: `Pais` → `Country`, `Sexo` → `Gender`
- Actualización de repositorios para compatibilidad con Symfony 7.4
- Mejora en README.md con instrucciones actualizadas
- Optimización de composer.json eliminando scripts inexistentes

### Fixed

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
