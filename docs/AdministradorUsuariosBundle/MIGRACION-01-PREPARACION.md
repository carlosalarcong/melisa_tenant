# 📋 Fase 1: Preparación y Limpieza

## 🎯 Objetivo
Preparar el código legacy para la migración, eliminando archivos obsoletos y configurando el entorno de trabajo.

---

## 🗑️ Archivos a Eliminar (Definitivamente)

### 1. Carpeta Recycle Completa
```
Resources/views/_Default/DatosMaestrosMedicos/MedicosVigentes/Recycle/
├── MedicoCreate_3.html.OLD.twig
├── MedicoCreate_3.html.twig
├── MedicoCreate_3_SUB.html.twig
└── Dinamico_SucursalUnidadServicio.html.twig
```

**Razón:** ✅ Versiones antiguas y respaldos obsoletos

**Comando:**
```bash
rm -rf Resources/views/_Default/DatosMaestrosMedicos/MedicosVigentes/Recycle/
```

---

### 2. Archivos .OLD
```
Form/Edit/Edit_3.html.OLD.twig
Form/Crear/MedicoCreate_3.html.OLD.twig
```

**Razón:** ✅ Respaldos antiguos de archivos ya editados

**Comando:**
```bash
find Resources/views/ -name "*.OLD.twig" -delete
```

---

### 3. Controladores de Backup
```
Controller/_Default/DatosMaestrosMedicos/DMMNuevoController.php.bckup
```

**Razón:** ✅ Backup del controlador activo, ya no necesario

**Comando:**
```bash
find Controller/ -name "*.bckup" -delete
find Controller/ -name "*.backup" -delete
```

---

### 4. Vistas de Prueba
```
Resources/views/_Default/indexTest.html.twig
Resources/views/_Default/MedicosVigentes/index.vigentesTest.html.twig
```

**Razón:** ⚠️ Archivos de testing no usados en producción

**Verificar primero si están en routing:**
```bash
grep -r "indexTest" Resources/config/routing.yml
grep -r "vigentesTest" Resources/config/routing.yml
```

**Si no están referenciados:**
```bash
find Resources/views/ -name "*Test.html.twig" -delete
```

---

### 5. Traducciones No Utilizadas
```
Resources/translations/messages.fr.xlf
```

**Razón:** ❓ Si no hay soporte para francés

**Verificar idiomas soportados en config y eliminar si no se usa:**
```bash
rm Resources/translations/messages.fr.xlf
```

---

## ⚠️ Archivos a Revisar (No Eliminar Aún)

### 1. DefaultController.php Vacío
```php
// Controller/DefaultController.php
namespace Rebsol\AdministradorUsuariosBundle\Controller;

class DefaultController extends \Rebsol\HermesBundle\Controller\DefaultController {
}
```

**Revisar:** Puede ser requerido por estructura de bundles Symfony 2
**Acción:** Mantener hasta verificar que no se usa en DI o routing
**Después de migración:** Eliminar

---

### 2. DefaultRepository.php
```php
// Repository/DefaultRepository.php
```

**Revisar:** Base para otros repositorios
**Acción:** ✅ **MANTENER** - Extendido por UsuariosRebsolRepository y PerfilRepository
**En migración:** Verificar qué métodos heredados se usan

---

### 3. Vistas Duplicadas

Existen dos carpetas con vistas muy similares:
- `_Default/MedicosVigentes/` (usuarios administrativos)
- `_Default/DatosMaestrosMedicos/MedicosVigentes/` (profesionales)

**Ejemplos:**
```
_Default/MedicosVigentes/Form/Add/UserAdd_*.html.twig
_Default/DatosMaestrosMedicos/MedicosVigentes/Form/Add/UserAdd_*.html.twig
```

**Análisis necesario:**
1. ¿Son realmente diferentes o solo varían parámetros?
2. ¿Se pueden consolidar en una sola vista con variables?

**Acción:** Durante migración, evaluar consolidación con parámetros Twig

---

### 4. Layouts Específicos
```
Resources/views/_Default/layoutMantenedorInfo.html.twig
Resources/views/_Default/layoutformulariosajax.html.twig
Resources/views/_Default/sublayout.html.twig
```

**Revisar:** Si se usan activamente
**Comando de verificación:**
```bash
grep -r "layoutMantenedorInfo" Resources/views/
grep -r "layoutformulariosajax" Resources/views/
grep -r "sublayout" Resources/views/
```

**Acción:** 
- Si se usan → Migrar a layouts modernos de Symfony 6
- Si no → Eliminar

---

## 📦 Preparación del Entorno

### 1. Backup Completo

**Base de datos:**
```bash
# Backup de BD principal
mysqldump -u user -p database_name > backup_$(date +%Y%m%d).sql

# Backup de BD tenant (si aplica)
mysqldump -u user -p tenant_database > backup_tenant_$(date +%Y%m%d).sql
```

**Código:**
```bash
# Crear branch de backup
cd /path/to/melisa_prod
git checkout -b backup-before-migration
git add .
git commit -m "Backup completo antes de migración de AdministradorUsuarios"
git push origin backup-before-migration

# Tag de versión
git tag -a v1.0-pre-migration -m "Versión antes de migración"
git push origin v1.0-pre-migration
```

---

### 2. Configurar Entorno de Desarrollo

**Crear workspace separado:**
```bash
# En melisa_tenant (Symfony 6)
cd /var/www/html/melisa_tenant
git checkout -b feature/user-management-migration
```

**Verificar versiones:**
```bash
php -v          # Debe ser PHP 8.1 o superior
composer --version
symfony -v      # Symfony CLI
```

**Instalar dependencias si faltan:**
```bash
composer require symfony/security-bundle
composer require symfony/form
composer require doctrine/orm
composer require symfony/validator
composer require symfony/mailer  # Para emails de Zoom
```

---

### 3. Configurar Base de Datos de Prueba

**Copiar estructura de BD legacy:**
```bash
# Crear BD de prueba
mysql -u root -p -e "CREATE DATABASE melisa_tenant_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar estructura (sin datos sensibles)
mysqldump -u root -p --no-data melisa_prod | mysql -u root -p melisa_tenant_test

# Importar datos de prueba seleccionados
mysqldump -u root -p melisa_prod usuarios_rebsol persona pnatural rol_profesional --where="id <= 10" | mysql -u root -p melisa_tenant_test
```

**Configurar en .env.test:**
```env
DATABASE_URL="mysql://user:pass@127.0.0.1:3306/melisa_tenant_test?serverVersion=8.0"
```

---

### 4. Documentar Configuración Actual

**Crear archivo de documentación:**
```bash
touch docs/AdministradorUsuariosBundle/CONFIG-LEGACY.md
```

**Documentar:**
1. **Parámetros importantes:**
   - Cantidad de licencias
   - Tiempo límite de login
   - Configuración de Zoom (ApiZoom.*)
   - Estados activos/inactivos

2. **Rutas actuales:**
   ```bash
   grep -r "AdministradorUsuarios" app/config/routing.yml > docs/legacy-routes.txt
   ```

3. **Servicios configurados:**
   ```bash
   cat src/Rebsol/AdministradorUsuariosBundle/Resources/config/services.yml
   ```

4. **Dependencias:**
   - Listar bundles/componentes que usa
   - Listar entidades relacionadas

---

## 📋 Checklist de Preparación

### Limpieza
- [ ] Eliminar carpeta Recycle/
- [ ] Eliminar archivos .OLD
- [ ] Eliminar backups de controladores (.bckup)
- [ ] Verificar y eliminar vistas de test
- [ ] Eliminar traducciones no usadas

### Análisis
- [ ] Identificar vistas duplicadas
- [ ] Revisar uso de layouts específicos
- [ ] Documentar DefaultController vacío
- [ ] Analizar DefaultRepository

### Backups
- [ ] Backup de base de datos principal
- [ ] Backup de base de datos tenant
- [ ] Crear branch de backup en git
- [ ] Crear tag de versión pre-migración

### Entorno
- [ ] Verificar PHP 8.1+
- [ ] Verificar Symfony 6 instalado
- [ ] Crear BD de prueba
- [ ] Configurar .env.test
- [ ] Instalar dependencias necesarias

### Documentación
- [ ] Documentar parámetros actuales
- [ ] Exportar configuración de routing
- [ ] Listar servicios configurados
- [ ] Documentar dependencias

---

## 🚨 Validaciones Antes de Continuar

### 1. Código Legacy Funcional
```bash
# En melisa_prod, verificar que todo funciona
cd /var/www/html/melisa_prod
php bin/console cache:clear --env=prod
php bin/console doctrine:schema:validate
```

**Verificar en browser:**
- Login funciona
- Listado de usuarios funciona
- Crear usuario funciona
- Editar usuario funciona

---

### 2. Backups Verificados
```bash
# Verificar que el backup se puede restaurar
mysql -u root -p -e "CREATE DATABASE test_restore;"
mysql -u root -p test_restore < backup_20251226.sql

# Verificar tablas principales
mysql -u root -p test_restore -e "SHOW TABLES LIKE '%usuario%';"
mysql -u root -p test_restore -e "SELECT COUNT(*) FROM usuarios_rebsol;"

# Eliminar BD de prueba
mysql -u root -p -e "DROP DATABASE test_restore;"
```

---

### 3. Git en Estado Limpio
```bash
git status  # No debe haber cambios sin commitear
git log -1  # Verificar último commit
git branch  # Verificar branch actual
```

---

## 📊 Resumen de Archivos

### ✅ Archivos a Eliminar (estimado)
- Carpeta Recycle: ~10 archivos
- Archivos .OLD: ~5 archivos
- Backups: ~3 archivos
- Tests: ~2 archivos
- **Total:** ~20 archivos a eliminar

### 📁 Archivos a Mantener (core)
- Controladores principales: 12 archivos
- Repositorios: 3 archivos
- FormTypes: 4 archivos
- Vistas activas: ~60 archivos
- Configuración: 4 archivos YAML
- **Total:** ~83 archivos a migrar

---

## ⏱️ Tiempo Estimado de Esta Fase

- **Limpieza de archivos:** 2 horas
- **Backups y configuración:** 3 horas
- **Documentación:** 2 horas
- **Validaciones:** 1 hora
- **Total:** **1 día** (8 horas)

---

## 🎯 Criterios de Éxito

✅ Fase completada cuando:
1. Todos los archivos obsoletos eliminados
2. Backups creados y verificados
3. Entorno de desarrollo configurado
4. Base de datos de prueba lista
5. Documentación de configuración legacy completa
6. Sistema legacy funcionando correctamente

---

## ➡️ Siguiente Paso

Una vez completada esta fase, continuar con:
[02 - Estructura Base del Nuevo Módulo](./MIGRACION-02-ESTRUCTURA-BASE.md)

---

**Fase:** 1 de 10  
**Prioridad:** 🔴 Alta - Fundacional  
**Riesgo:** 🟢 Bajo - Solo preparación
