# 🔐 Fase 6: Seguridad, Routing y Vistas

## 🎯 Objetivo
Actualizar la configuración de seguridad, routing y vistas del módulo.

---

## 🔐 Parte 1: Seguridad

### Migración de security.yml a security.yaml

**ANTES (Symfony 3 - security.yml):**
```yaml
security:
    encoders:
        Rebsol\HermesBundle\Entity\UsuariosRebsol:
            algorithm: bcrypt
            cost: 12

    providers:
        db_provider:
            entity:
                class: RebsolHermesBundle:UsuariosRebsol
                property: nombreUsuario
```

**DESPUÉS (Symfony 6 - config/packages/security.yaml):**
```yaml
security:
    password_hashers:
        App\Entity\Main\UsuariosRebsol:
            algorithm: auto
            # Mantiene compatibilidad con contraseñas bcrypt existentes

    providers:
        app_user_provider:
            entity:
                class: App\Entity\Main\UsuariosRebsol
                property: nombreUsuario

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            lazy: true
            provider: app_user_provider
            custom_authenticator: App\Security\LoginAuthenticator
            logout:
                path: app_logout
                target: app_login
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800

    access_control:
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/admin, roles: ROLE_USER }
        - { path: ^/admin/usuarios, roles: ROLE_ADMIN }
```

---

### Actualizar Código que Usa Encoder/Hasher

**ANTES (Symfony 3):**
```php
$factory = $this->get('security.encoder_factory');
$encoder = $factory->getEncoder($usuario);
$hashedPassword = $encoder->encodePassword($plainPassword, $usuario->getSalt());
$usuario->setContrasena($hashedPassword);
```

**DESPUÉS (Symfony 6):**
```php
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

public function __construct(
    private UserPasswordHasherInterface $passwordHasher
) {}

public function updatePassword(UsuariosRebsol $usuario, string $plainPassword): void
{
    $hashedPassword = $this->passwordHasher->hashPassword($usuario, $plainPassword);
    $usuario->setContrasena($hashedPassword);
}
```

---

### Verificar Contraseña

**ANTES:**
```php
$encoder = $factory->getEncoder($usuario);
$isValid = $encoder->isPasswordValid(
    $usuario->getContrasena(),
    $plainPassword,
    $usuario->getSalt()
);
```

**DESPUÉS:**
```php
$isValid = $this->passwordHasher->isPasswordValid($usuario, $plainPassword);
```

---

## 🛣️ Parte 2: Routing

### Eliminar routing.yml

**ANTES (routing.yml):**
```yaml
AdministradorUsuarios:
    path:  /
    defaults: { _controller: AdministradorUsuariosBundle:_Default/DatosMaestrosMedicos/DatosMaestrosMedicos:usuarioIndex}

AdministradorUsuario_New:
    path:  /DatosMaestrosMedicos/NuevoUsuario
    defaults: { _controller: AdministradorUsuariosBundle:_Default/DatosMaestrosMedicos/DMMNuevo:nuevoUsuario}
```

**DESPUÉS: Usar Atributos PHP 8 en Controladores**

Ya implementado en los controladores con atributos `#[Route]`.

Opcionalmente, agregar en `config/routes.yaml`:
```yaml
# config/routes.yaml

admin_user_controllers:
    resource: '../src/Controller/Admin/User/'
    type: attribute
    prefix: /admin
```

---

## 🎨 Parte 3: Vistas Twig

### Estructura de Vistas

```
templates/admin/user/
├── index.html.twig              # Listado usuarios
├── professional_index.html.twig # Listado profesionales
├── dashboard.html.twig          # Dashboard
├── create.html.twig             # Crear/Editar
├── edit.html.twig               # Editar (alias)
├── view.html.twig               # Ver detalles
├── assign_profiles.html.twig    # Asignar grupos/perfiles
├── _form.html.twig              # Formulario principal
├── _form_personal.html.twig     # Sección datos personales
├── _form_professional.html.twig # Sección datos profesionales
├── _form_access.html.twig       # Sección acceso
├── _form_services.html.twig     # Sección servicios
├── _table.html.twig             # Tabla listado
├── _actions.html.twig           # Botones de acción
└── _modals.html.twig            # Modales reutilizables
```

---

### Ejemplo: index.html.twig

```twig
{# templates/admin/user/index.html.twig #}

{% extends 'layout_vertical.html.twig' %}

{% block title %}Administración de Usuarios{% endblock %}

{% block page_css %}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
{% endblock %}

{% block content %}
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Usuarios del Sistema</h4>
                
                <div class="page-title-right">
                    {% if license_info.can_create_user %}
                        <a href="{{ path('admin_user_new') }}" class="btn btn-primary">
                            <i class="ri-add-line"></i> Nuevo Usuario
                        </a>
                    {% else %}
                        <button class="btn btn-secondary" disabled>
                            <i class="ri-lock-line"></i> Sin Licencias
                        </button>
                    {% endif %}
                    
                    <a href="{{ path('admin_user_export') }}" class="btn btn-success">
                        <i class="ri-file-excel-line"></i> Exportar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {# Alerta de licencias #}
    {% if license_info.is_warning %}
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ri-alert-line me-2"></i>
            <strong>Atención:</strong> Solo quedan {{ license_info.available }} licencias disponibles.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    {% endif %}

    {# Información de licencias #}
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Licencias Totales</p>
                            <h4 class="mb-0">{{ license_info.total }}</h4>
                        </div>
                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="ri-user-3-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Licencias Usadas</p>
                            <h4 class="mb-0">{{ license_info.used }}</h4>
                        </div>
                        <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-success">
                                <i class="ri-checkbox-circle-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Disponibles</p>
                            <h4 class="mb-0">{{ license_info.available }}</h4>
                        </div>
                        <div class="avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-info">
                                <i class="ri-add-circle-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Uso</p>
                            <h4 class="mb-0">{{ license_info.percentage }}%</h4>
                        </div>
                        <div class="avatar-sm rounded-circle bg-warning align-self-center mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i class="ri-pie-chart-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {# Tabla de usuarios #}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="users-table" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>RUT</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Último Login</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {% for user in users %}
                                {% set pnatural = user.idPersona.pnatural %}
                                <tr>
                                    <td>{{ user.id }}</td>
                                    <td>
                                        <strong>{{ user.nombreUsuario }}</strong>
                                        {% if user.zoomUser %}
                                            <i class="ri-video-line text-info ms-1" title="Zoom vinculado"></i>
                                        {% endif %}
                                    </td>
                                    <td>{{ pnatural.nombreCompleto }}</td>
                                    <td>{{ pnatural.identificacion }}</td>
                                    <td>{{ user.idPersona.correoElectronico }}</td>
                                    <td>
                                        <span class="badge badge-soft-primary">
                                            {{ user.idRol.nombre }}
                                        </span>
                                    </td>
                                    <td>
                                        {% if user.idEstadoUsuario.nombre == 'ACTIVO' %}
                                            <span class="badge badge-soft-success">Activo</span>
                                        {% elseif user.idEstadoUsuario.nombre == 'BLOQUEADO' %}
                                            <span class="badge badge-soft-danger">Bloqueado</span>
                                        {% else %}
                                            <span class="badge badge-soft-secondary">Inactivo</span>
                                        {% endif %}
                                    </td>
                                    <td>
                                        {% if user.ultimoLogin %}
                                            {{ user.ultimoLogin|date('d/m/Y H:i') }}
                                        {% else %}
                                            <span class="text-muted">Nunca</span>
                                        {% endif %}
                                    </td>
                                    <td>
                                        {% include 'admin/user/_actions.html.twig' with {'user': user} %}
                                    </td>
                                </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}

{% block page_js %}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[2, 'asc']],
                pageLength: 25
            });
        });
    </script>
{% endblock %}
```

---

### Ejemplo: _actions.html.twig (Botones de Acción)

```twig
{# templates/admin/user/_actions.html.twig #}

<div class="btn-group" role="group">
    {# Ver #}
    <a href="{{ path('admin_user_view', {id: user.id}) }}" 
       class="btn btn-sm btn-info" 
       title="Ver">
        <i class="ri-eye-line"></i>
    </a>

    {# Editar #}
    <a href="{{ path('admin_user_edit', {id: user.id}) }}" 
       class="btn btn-sm btn-primary" 
       title="Editar">
        <i class="ri-pencil-line"></i>
    </a>

    {# Grupos/Perfiles #}
    <a href="{{ path('admin_user_profile_assign', {id: user.id}) }}" 
       class="btn btn-sm btn-warning" 
       title="Grupos y Perfiles">
        <i class="ri-shield-user-line"></i>
    </a>

    {# Activar/Desactivar #}
    {% if user.idEstadoUsuario.nombre == 'ACTIVO' %}
        <button type="button" 
                class="btn btn-sm btn-danger" 
                onclick="deleteUser({{ user.id }})" 
                title="Desactivar">
            <i class="ri-user-forbid-line"></i>
        </button>
    {% else %}
        <button type="button" 
                class="btn btn-sm btn-success" 
                onclick="activateUser({{ user.id }})" 
                title="Activar">
            <i class="ri-user-add-line"></i>
        </button>
    {% endif %}

    {# Desbloquear (si está bloqueado) #}
    {% if user.idEstadoUsuario.nombre == 'BLOQUEADO' %}
        <button type="button" 
                class="btn btn-sm btn-secondary" 
                onclick="unlockUser({{ user.id }})" 
                title="Desbloquear">
            <i class="ri-lock-unlock-line"></i>
        </button>
    {% endif %}
</div>
```

---

### JavaScript para Acciones AJAX

Crear archivo: `assets/js/admin/user.js`

```javascript
// assets/js/admin/user.js

/**
 * Eliminar (inactivar) usuario
 */
function deleteUser(userId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'El usuario será desactivado',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/usuarios/${userId}/eliminar`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    _token: getCsrfToken('delete-user-' + userId)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Desactivado!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error al desactivar usuario', 'error');
                console.error(error);
            });
        }
    });
}

/**
 * Activar usuario
 */
function activateUser(userId) {
    fetch(`/admin/usuarios/${userId}/activar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            _token: getCsrfToken('activate-user-' + userId)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('¡Activado!', data.message, 'success')
                .then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Error al activar usuario', 'error');
        console.error(error);
    });
}

/**
 * Desbloquear usuario
 */
function unlockUser(userId) {
    fetch(`/admin/usuarios/${userId}/desbloquear`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            _token: getCsrfToken('unlock-user-' + userId)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('¡Desbloqueado!', data.message, 'success')
                .then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Error al desbloquear usuario', 'error');
        console.error(error);
    });
}

/**
 * Helper: Obtener token CSRF
 */
function getCsrfToken(tokenId) {
    const meta = document.querySelector(`meta[name="csrf-token-${tokenId}"]`);
    return meta ? meta.content : '';
}
```

---

## ⏱️ Tiempo Estimado

- **Configuración de seguridad:** 0.5 días
- **Routing:** 0.5 días
- **Vistas principales:** 2 días
- **JavaScript/Assets:** 1 día
- **Testing:** 1 día
- **Total:** **5 días**

---

## ➡️ Siguiente Paso

[07 - Ejemplos de Código Completos](./MIGRACION-07-EJEMPLOS-CODIGO.md)

---

**Fase:** 6 de 10  
**Prioridad:** 🔴 Alta  
**Riesgo:** 🟡 Medio
