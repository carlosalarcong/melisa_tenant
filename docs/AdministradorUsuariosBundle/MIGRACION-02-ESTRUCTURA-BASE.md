# 🏗️ Fase 2: Estructura Base del Nuevo Módulo

## 🎯 Objetivo
Definir y crear la estructura moderna de directorios para el módulo de Administración de Usuarios en Symfony 6.

---

## 🤔 Decisión: Bundle vs Estructura Moderna

### Opción A: Estructura Moderna (Recomendada)
```
src/
├── Controller/
│   └── Admin/
│       └── User/
├── Service/
│   └── User/
├── Form/
│   └── Type/
│       └── User/
└── ...
```

**Ventajas:**
- ✅ Sigue convenciones de Symfony 6
- ✅ Más fácil de mantener
- ✅ Mejor autocompletado en IDEs
- ✅ Namespaces más claros

**Desventajas:**
- ⚠️ Cambio más radical
- ⚠️ Requiere actualizar muchos namespaces

---

### Opción B: Mantener como Bundle
```
src/AdministradorUsuarios/
├── Controller/
├── Service/
├── Form/
└── Resources/
```

**Ventajas:**
- ✅ Migración incremental más fácil
- ✅ Aislamiento del módulo
- ✅ Menos cambios iniciales

**Desventajas:**
- ⚠️ No sigue convenciones de Symfony 6
- ⚠️ Puede requerir configuración extra

---

## ✅ Decisión Recomendada: Opción A (Estructura Moderna)

Usar estructura moderna de Symfony 6 con organización por funcionalidad.

---

## 📁 Estructura Completa del Nuevo Módulo

```
src/
├── Controller/
│   └── Admin/
│       └── User/
│           ├── UserController.php              # Listado y operaciones generales
│           ├── UserCreateController.php        # Crear usuario
│           ├── UserEditController.php          # Editar usuario
│           ├── UserViewController.php          # Ver usuario
│           ├── UserDeleteController.php        # Eliminar (inactivar)
│           ├── UserActivateController.php      # Reactivar
│           ├── UserUnlockController.php        # Desbloquear
│           ├── UserGroupController.php         # Gestión grupos/perfiles
│           ├── UserExportController.php        # Exportar Excel
│           ├── UserZoomController.php          # Integración Zoom
│           └── Ajax/
│               ├── GroupProfileController.php  # Grupos por perfil AJAX
│               ├── UnitBranchController.php    # Unidades por sucursal
│               ├── ServiceUnitController.php   # Servicios por unidad
│               ├── ValidateRutController.php   # Validar RUT
│               ├── ValidateUsernameController.php  # Validar username
│               └── ValidateVigenciaController.php  # Validar vigencia
│
├── Entity/
│   ├── Main/                                   # Entidades BD principal (ya existen)
│   │   ├── UsuariosRebsol.php
│   │   ├── Persona.php
│   │   ├── Pnatural.php
│   │   ├── Grupo.php
│   │   ├── Perfil.php
│   │   └── ...
│   └── Tenant/                                 # Entidades BD tenant (ya existen)
│       └── ...
│
├── Repository/
│   ├── UsuariosRebsolRepository.php           # Queries de usuarios
│   ├── PerfilRepository.php                    # Queries de perfiles
│   └── GrupoRepository.php                     # Queries de grupos
│
├── Service/
│   └── User/
│       ├── UserManagementService.php          # CRUD de usuarios
│       ├── ProfileManagementService.php       # Gestión perfiles/grupos
│       ├── LicenseValidationService.php       # Validación licencias
│       ├── ZoomIntegrationService.php         # Integración Zoom
│       ├── PasswordManagementService.php      # Gestión contraseñas
│       ├── UserValidationService.php          # Validaciones negocio
│       ├── UserSpecialtyService.php           # Gestión especialidades
│       └── UserSessionService.php             # Control sesiones
│
├── Form/
│   └── Type/
│       └── User/
│           ├── UserType.php                   # Formulario principal usuario
│           ├── ProfessionalType.php           # Formulario profesional
│           ├── ProfileAssignmentType.php      # Asignar perfiles/grupos
│           ├── GroupAssignmentType.php        # Asignar solo grupos
│           ├── UserPhotoType.php              # Subir foto
│           └── UserServiceType.php            # Asignar servicios
│
├── Validator/
│   └── Constraints/
│       ├── UniqueUsername.php                 # Validador username único
│       ├── UniqueUsernameValidator.php
│       ├── ValidRut.php                       # Validador RUT chileno
│       ├── ValidRutValidator.php
│       ├── AvailableLicense.php               # Validador licencias
│       ├── AvailableLicenseValidator.php
│       ├── ValidSpecialtyDate.php             # Validador fechas especialidad
│       └── ValidSpecialtyDateValidator.php
│
├── EventSubscriber/
│   ├── UserCreatedSubscriber.php              # Evento post-creación
│   ├── UserUpdatedSubscriber.php              # Evento post-actualización
│   ├── UserDeletedSubscriber.php              # Evento post-eliminación
│   └── UserLoginSubscriber.php                # Evento login (validaciones)
│
├── Security/
│   └── Voter/
│       ├── UserVoter.php                      # Permisos sobre usuarios
│       └── ProfileVoter.php                   # Permisos sobre perfiles
│
└── Enum/                                       # PHP 8.1+ Enums
    ├── UserStateEnum.php                      # Estados de usuario
    ├── UserRoleEnum.php                       # Roles de usuario
    └── SpecialtyStateEnum.php                 # Estados de especialidad

templates/
├── admin/
│   └── user/
│       ├── index.html.twig                    # Listado usuarios
│       ├── professional_index.html.twig       # Listado profesionales
│       ├── create.html.twig                   # Crear usuario
│       ├── edit.html.twig                     # Editar usuario
│       ├── view.html.twig                     # Ver usuario
│       ├── assign_profiles.html.twig          # Asignar grupos/perfiles
│       ├── _form.html.twig                    # Fragmento formulario
│       ├── _form_personal.html.twig           # Datos personales
│       ├── _form_professional.html.twig       # Datos profesionales
│       ├── _form_access.html.twig             # Datos acceso
│       ├── _form_services.html.twig           # Servicios
│       ├── _table.html.twig                   # Tabla listado
│       └── _modals.html.twig                  # Modales reutilizables

config/
├── packages/
│   └── user_management.yaml                   # Configuración del módulo
└── routes/
    └── admin_user.yaml                        # Rutas del módulo (opcional)

migrations/
├── Main/
│   └── VersionXXXXXXXXXX.php                 # Migraciones BD principal
└── Tenant/
    └── VersionXXXXXXXXXX.php                 # Migraciones BD tenant

tests/
├── Unit/
│   └── Service/
│       └── User/
│           ├── UserManagementServiceTest.php
│           ├── ProfileManagementServiceTest.php
│           └── LicenseValidationServiceTest.php
└── Functional/
    └── Controller/
        └── Admin/
            └── User/
                ├── UserControllerTest.php
                ├── UserCreateControllerTest.php
                └── UserEditControllerTest.php
```

---

## 📝 Namespaces

### Convención de Namespaces

```php
// Controladores
namespace App\Controller\Admin\User;
namespace App\Controller\Admin\User\Ajax;

// Servicios
namespace App\Service\User;

// Formularios
namespace App\Form\Type\User;

// Repositorios
namespace App\Repository;

// Validadores
namespace App\Validator\Constraints;

// EventSubscribers
namespace App\EventSubscriber;

// Security
namespace App\Security\Voter;

// Enums
namespace App\Enum;
```

---

## 🛠️ Comandos para Crear Estructura

```bash
cd /var/www/html/melisa_tenant

# Controladores
mkdir -p src/Controller/Admin/User/Ajax

# Servicios
mkdir -p src/Service/User

# Formularios
mkdir -p src/Form/Type/User

# Validadores
mkdir -p src/Validator/Constraints

# EventSubscribers
mkdir -p src/EventSubscriber

# Security Voters
mkdir -p src/Security/Voter

# Enums
mkdir -p src/Enum

# Templates
mkdir -p templates/admin/user

# Tests
mkdir -p tests/Unit/Service/User
mkdir -p tests/Functional/Controller/Admin/User

# Config
mkdir -p config/routes
```

---

## ⚙️ Archivo de Configuración del Módulo

Crear archivo `config/packages/user_management.yaml`:

```yaml
# config/packages/user_management.yaml

parameters:
    # Licencias
    user_management.license.total: '%env(int:USER_LICENSES_TOTAL)%'
    user_management.license.warning_threshold: 10
    
    # Contraseñas
    user_management.password.expiry_days: '%env(int:PASSWORD_EXPIRY_DAYS)%'
    user_management.password.history_size: 5
    user_management.password.min_length: 8
    
    # Bloqueos
    user_management.lock.max_attempts: 3
    user_management.lock.timeout_minutes: 30
    
    # Zoom
    user_management.zoom.enabled: '%env(bool:ZOOM_ENABLED)%'
    user_management.zoom.api_url: '%env(ZOOM_API_URL)%'
    user_management.zoom.api_user: '%env(ZOOM_API_USER)%'
    user_management.zoom.api_password: '%env(ZOOM_API_PASSWORD)%'
    
    # Estados
    user_management.state.active: 1
    user_management.state.inactive: 0
    user_management.state.blocked: 2

services:
    _defaults:
        autowire: true
        autoconfigure: true

    # Servicios de Usuario
    App\Service\User\:
        resource: '../../src/Service/User/'
        tags: ['app.user_service']
    
    # Event Subscribers
    App\EventSubscriber\:
        resource: '../../src/EventSubscriber/'
        tags: ['kernel.event_subscriber']
    
    # Voters
    App\Security\Voter\:
        resource: '../../src/Security/Voter/'
        tags: ['security.voter']
```

---

## 📄 Variables de Entorno

Agregar a `.env`:

```env
###> USER MANAGEMENT MODULE ###
USER_LICENSES_TOTAL=50
PASSWORD_EXPIRY_DAYS=90
ZOOM_ENABLED=true
ZOOM_API_URL=https://api.zoom.us/v2
ZOOM_API_USER=your_api_key
ZOOM_API_PASSWORD=your_api_secret
###< USER MANAGEMENT MODULE ###
```

---

## 🔧 Configuración de Routing (Opcional)

Crear archivo `config/routes/admin_user.yaml`:

```yaml
# config/routes/admin_user.yaml

admin_user:
    resource: '../../src/Controller/Admin/User/'
    type: attribute
    prefix: /admin/usuarios
    name_prefix: admin_user_
```

O en `config/routes.yaml`:

```yaml
# config/routes.yaml

admin_user_controllers:
    resource: '../src/Controller/Admin/User/'
    type: attribute
    prefix: /admin
```

---

## 🎨 Configuración de Webpack Encore

Si aún no está configurado, agregar en `webpack.config.js`:

```javascript
Encore
    // ... configuración existente
    
    // Módulo de administración de usuarios
    .addEntry('admin-user', './assets/js/admin/user.js')
    .addStyleEntry('admin-user-styles', './assets/scss/admin/user.scss')
;
```

Crear archivos de assets:

```bash
mkdir -p assets/js/admin
mkdir -p assets/scss/admin

touch assets/js/admin/user.js
touch assets/scss/admin/user.scss
```

---

## 📋 Checklist de Creación de Estructura

### Directorios
- [ ] Crear `src/Controller/Admin/User/`
- [ ] Crear `src/Controller/Admin/User/Ajax/`
- [ ] Crear `src/Service/User/`
- [ ] Crear `src/Form/Type/User/`
- [ ] Crear `src/Validator/Constraints/`
- [ ] Crear `src/EventSubscriber/`
- [ ] Crear `src/Security/Voter/`
- [ ] Crear `src/Enum/`
- [ ] Crear `templates/admin/user/`
- [ ] Crear `tests/Unit/Service/User/`
- [ ] Crear `tests/Functional/Controller/Admin/User/`

### Configuración
- [ ] Crear `config/packages/user_management.yaml`
- [ ] Agregar variables a `.env`
- [ ] Configurar routing (si se usa YAML)
- [ ] Configurar Webpack Encore para assets

### Documentación
- [ ] Documentar estructura en README
- [ ] Crear diagrama de arquitectura
- [ ] Documentar convenciones de namespaces

---

## 🧪 Validación de Estructura

Verificar que la estructura está correcta:

```bash
# Verificar directorios creados
tree -L 4 src/Controller/Admin/
tree -L 3 src/Service/
tree -L 4 src/Form/

# Verificar autoload de Composer
composer dump-autoload

# Limpiar caché de Symfony
php bin/console cache:clear
```

---

## ⏱️ Tiempo Estimado de Esta Fase

- **Crear estructura de directorios:** 1 hora
- **Configuración inicial:** 2 horas
- **Configurar routing y assets:** 1 hora
- **Documentación:** 1 hora
- **Total:** **5 horas** (~1 día)

---

## 🎯 Criterios de Éxito

✅ Fase completada cuando:
1. Todos los directorios creados
2. Configuración inicial lista
3. Variables de entorno definidas
4. Routing configurado
5. Webpack Encore preparado para assets del módulo
6. Autoload de Composer actualizado
7. Caché de Symfony limpia

---

## ➡️ Siguiente Paso

Una vez completada esta fase, continuar con:
[03 - Servicios de Negocio](./MIGRACION-03-SERVICIOS.md)

---

**Fase:** 2 de 10  
**Prioridad:** 🔴 Alta - Fundacional  
**Riesgo:** 🟢 Bajo - Solo estructura
