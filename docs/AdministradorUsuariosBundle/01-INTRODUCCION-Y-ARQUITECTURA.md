# AdministradorUsuariosBundle - Parte 1: Introducción y Arquitectura

## 📋 Información General

**Bundle:** AdministradorUsuariosBundle  
**Namespace:** `Rebsol\AdministradorUsuariosBundle`  
**Versión Symfony:** 2.x  
**Objetivo:** Migración a Symfony 6.x

---

## 🎯 Propósito del Bundle

Este bundle es el **núcleo de administración de usuarios y profesionales** del sistema Melisa. Gestiona:

1. **Usuarios del Sistema (UsuariosRebsol)**
2. **Profesionales Médicos** (con especialidades)
3. **Perfiles y Grupos de Permisos**
4. **Relaciones Usuario-Servicio-Unidad-Sucursal**
5. **Control de Acceso y Seguridad**
6. **Gestión de Licencias de Usuario**

---

## 🏗️ Arquitectura General

### Estructura de Directorios

```
AdministradorUsuariosBundle/
├── Controller/
│   ├── DefaultController.php (vacío, hereda de HermesBundle)
│   └── _Default/
│       └── DatosMaestrosMedicos/
│           ├── DatosMaestrosMedicosController.php (Controlador base)
│           ├── DMMNuevoController.php (Crear)
│           ├── DMMEditController.php (Editar)
│           ├── DMMVerController.php (Ver)
│           ├── DMMAddController.php (Grupos/Perfiles)
│           ├── DMMDellController.php (Eliminar/Inactivar)
│           ├── DMMActController.php (Activar)
│           ├── DMMUnlockController.php (Desbloqueo)
│           ├── DMMExportarExcelController.php (Exportar)
│           └── Dependencias/
│               ├── GrupoPerfilController.php
│               ├── ServicioporUnidadController.php
│               ├── UnidadporSucursalController.php
│               ├── ValrutController.php
│               ├── ValusernameController.php
│               └── VigenciaController.php
│
├── Form/
│   └── Type/
│       └── _Default/
│           └── DatosMaestrosMedicos/
│               ├── DMMType.php (Formulario principal)
│               └── MedicosVigentes/
│                   ├── addgType.php (Grupos)
│                   ├── addpType.php (Perfiles)
│                   └── FotoPnaturalType.php (Foto)
│
├── Repository/
│   ├── DefaultRepository.php (Base)
│   ├── UsuariosRebsolRepository.php (Principal)
│   └── PerfilRepository.php (Perfiles)
│
├── Resources/
│   ├── config/
│   │   ├── routing.yml
│   │   ├── services.yml
│   │   ├── repositories.yml
│   │   └── parameters.yml
│   ├── views/
│   │   ├── _Default/
│   │   │   ├── MedicosVigentes/ (Vistas usuarios)
│   │   │   └── DatosMaestrosMedicos/ (Vistas profesionales)
│   │   └── UI/
│   │       └── Macros/ (Componentes reutilizables)
│   └── translations/
│
├── DependencyInjection/
└── Tests/
```

---

## 🔑 Conceptos Clave del Negocio

### 1. Dualidad: Profesionales vs Usuarios

El sistema maneja **DOS tipos de entidades relacionadas pero diferentes**:

#### **a) Profesionales (from=0)**
- Son médicos y profesionales de la salud
- SIEMPRE tienen especialidades médicas
- Tienen Rol con `profClinico = 1`
- Se gestionan desde: `/DatosMaestrosMedicos`
- Vistas en: `_Default/DatosMaestrosMedicos/MedicosVigentes/`

#### **b) Usuarios Administrativos (from=1)**
- Personal administrativo, técnicos, otros
- NO necesariamente tienen especialidades
- Tienen Rol con `profClinico = 0 o null`
- Se gestionan desde: `/AdministradorUsuarios` (ruta principal)
- Vistas en: `_Default/MedicosVigentes/`

**Variable clave:** `from` en el método `renderViewDMM()`
- `from = 0` → Profesional
- `from = 1` → Usuario Administrativo

---

### 2. Operaciones CRUD (Variable: new)

El sistema usa un **parámetro `new`** para determinar la operación:

```php
'new' => 0  // Editar
'new' => 1  // Crear nuevo
'new' => 2  // Ver (solo lectura)
'new' => 3  // Listado
```

---

### 3. Entidades Principales Involucradas

```
Persona (Datos básicos)
  └── Pnatural (Persona Natural)
      └── UsuariosRebsol (Usuario del sistema)
          ├── RolProfesional (Rol asignado)
          ├── RelUsuarioCargo (Cargo)
          ├── RelUsuarioTipoMedico (Tipo de médico)
          ├── RelEspecialidadProfesional (Especialidades)
          ├── RelUsuarioServicio (Servicios asignados)
          ├── RelUsuarioGrupo (Grupos de permisos)
          ├── RelUsuarioPerfil (Perfiles individuales)
          ├── PrevisionPnatural (Previsiones)
          └── UsuarioHistorialContrasena (Historial passwords)
```

---

### 4. Sistema de Permisos: Grupos y Perfiles

#### **Jerarquía de Permisos:**

```
Grupo (conjunto de usuarios)
  └── contiene → Perfiles
                   └── contienen → Módulos (permisos)

Usuario
  ├── puede pertenecer a → Grupos (hereda perfiles del grupo)
  └── puede tener → Perfiles individuales (directos)
```

#### **Lógica de Asignación:**

1. **Usuario en Grupo:** Hereda TODOS los perfiles del grupo (ACTIVOS)
2. **Perfil Individual Activo:** Usuario tiene ese perfil específico
3. **Perfil Individual Inactivo:** EXCLUYE ese perfil (aunque venga del grupo)

**Ejemplo:**
- Grupo "Médicos" tiene perfiles: [A, B, C]
- Usuario "Dr. Pérez" está en grupo "Médicos"
- Usuario tiene perfil B con estado INACTIVO
- **Resultado:** Dr. Pérez tiene perfiles [A, C] (B está excluido)

---

### 5. Sistema de Servicios y Ubicación

```
Empresa
  └── Sucursal (ubicación física)
      └── Unidad (departamento/área)
          └── Servicio (servicio médico específico)
              └── RelUsuarioServicio (usuario asignado)
```

**Reglas importantes:**
- Un usuario puede estar en **MÚLTIPLES servicios**
- Solo **UN servicio puede estar ACTIVO** a la vez
- El servicio activo determina el contexto de trabajo del usuario
- Los servicios inactivos se pueden reactivar

---

### 6. Estados Críticos

#### **EstadoUsuarios:**
- `1` = Activo (puede iniciar sesión)
- `0` = Inactivo (no puede acceder)

#### **Estado (general para relaciones):**
- `1` = Activo
- `0` = Inactivo

#### **EstadoEspecialidadMedica:**
- `1` = Activo
- `0` = Inactivo
- `2` = Bloqueado (usado al desactivar usuario)

#### **EstadoRelUsuarioServicio:**
- `1` = Activo (servicio actualmente en uso)
- `0` = Inactivo (servicio asignado pero no en uso)
- `2` = Bloqueado (eliminado lógicamente)

---

### 7. Gestión de Licencias

El sistema controla la cantidad de **usuarios activos** vs **licencias disponibles**:

```php
cantidadLicencias = Configuración de empresa
cantidadUsuariosActivos = Count de UsuariosRebsol con estado activo
licenciasDisponibles = cantidadLicencias - cantidadUsuariosActivos
```

**Restricción:** No se puede activar un usuario si no hay licencias disponibles.

---

### 8. Seguridad y Control de Acceso

#### **a) Expiración de Contraseña:**
- Parámetro: `TIEMPO_LIMITE_LOGIN` (días)
- Se calcula desde el campo `auditoria` en `UsuariosRebsol`
- Al expirar: el usuario debe cambiar contraseña

#### **b) Expiración de Acceso:**
- Parámetro: `TIEMPO_LIMITE_EXPIRACION` (segundos)
- Se calcula desde `fechaCreacion` en `UsuarioHistorialContrasena`
- Al expirar: cuenta bloqueada, requiere desbloqueo por admin

#### **c) Intentos Fallidos:**
- Parámetro: `NUMERO_INTENTOS_FALLIDOS_LOGIN`
- Campo: `intentosFallidos` en `UsuariosRebsol`
- Al alcanzar el límite: cuenta bloqueada

#### **d) Historial de Contraseñas:**
- Se guarda en `UsuarioHistorialContrasena`
- Usa el mismo encoder que `UsuariosRebsol`
- Permite validar que no se repitan contraseñas

---

### 9. Integración con Zoom (Teleconsulta)

Si la empresa tiene `teleconsulta = true`:

```php
UsuariosRebsol.zoomUser → ID del usuario en Zoom
```

**Funcionalidades:**
- Vincular usuario Melisa con cuenta Zoom
- Crear usuario Zoom automáticamente
- Verificar estado de cuenta Zoom
- Enviar invitación de confirmación

**Estados posibles:**
- "Activado" - Usuario confirmado en Zoom
- "Por Confirmar" - Pendiente de confirmación de email
- "No vinculado" - Sin usuario Zoom
- "Usuario registrado no existe" - Error de sincronización

---

## 🔄 Flujo de Datos Principal

### Método Central: `renderViewDMM()`

Este método es el **CORAZÓN** del bundle. Maneja todas las vistas:

```php
renderViewDMM([
    'from'       => 0 o 1,      // Profesional o Usuario
    'new'        => 0-3,         // Operación CRUD
    'render'     => 'render',    // Método de renderizado
    'idUser'     => $id,         // ID del usuario (si aplica)
    'path'       => 'ruta',      // Path de la vista
    'source'     => 'archivo',   // Archivo twig
    'errorReturn'=> false,       // Si hay errores de validación
    'form'       => $form,       // Formulario (si aplica)
    'rol'        => $rol,        // Rol (si aplica)
    'entity'     => $entity      // Entidad (si aplica)
])
```

**Este método:**
1. Determina qué tipo de vista mostrar (crear/editar/ver/listar)
2. Carga los datos necesarios según el contexto
3. Prepara el formulario con las opciones correctas
4. Renderiza la vista apropiada con los datos

---

## 📊 Flujo de Operaciones

### Crear Usuario/Profesional
```
1. Usuario accede a ruta crear
2. renderViewDMM(new=1) prepara formulario vacío
3. Usuario completa formulario
4. Submit → crearUsuario/crearProfesional
5. prepareDataInsertRegister() valida
6. dataInsertRegister() persiste entidades:
   - Persona
   - Pnatural
   - UsuariosRebsol
   - UsuarioHistorialContrasena
   - RolProfesional
   - RelUsuarioCargo
   - RelUsuarioServicio
   - RelUsuarioTipoMedico
   - RelEspecialidadProfesional
   - PrevisionPnatural
7. Redirect a listado
```

### Editar Usuario/Profesional
```
1. Usuario accede a ruta editar/{id}
2. renderViewDMM(new=0) carga datos existentes
3. Formulario se llena con datos actuales
4. Usuario modifica campos
5. Submit → editedUsuario/editedProfesional
6. prepareDataInsertRegister() valida
7. dataInsertRegister() actualiza entidades
8. Manejo especial de:
   - Especialidades (bloquear/activar)
   - Servicios (cambiar activo)
   - Grupos/Perfiles (activar/inactivar)
   - Previsiones (agregar/eliminar)
9. Si cambió contraseña o servicio activo:
   → botarUsuarioRebsol() (cierra sesión del usuario)
```

### Ver Usuario/Profesional
```
1. Usuario accede a ruta ver/{id}
2. renderViewDMM(new=2) carga datos completos
3. Muestra vista de solo lectura con:
   - Datos personales
   - Datos de usuario
   - Especialidades
   - Grupos y perfiles
   - Servicios asignados
   - Auditoría
   - Historial de login
```

### Listar Usuarios/Profesionales
```
1. Usuario accede a ruta índice
2. renderViewDMM(new=3) prepara listado
3. Carga todos los usuarios con:
   - setArrayUsers() construye array con:
     * Datos básicos
     * Especialidades
     * Grupos
     * Perfiles
     * Último login
4. Calcula información de licencias
5. Valida existencia de datos maestros
6. Renderiza DataTable con acciones
```

---

## 🔧 Componentes Técnicos Importantes

### Repositorios Personalizados

- **UsuariosRebsolRepository:** Queries complejas para usuarios
- **PerfilRepository:** Gestión de perfiles y grupos

### Servicios Registrados

```yaml
services:
    administradorUsuarios.UsuariosRebsol:
        class: Rebsol\AdministradorUsuariosBundle\Repository\UsuariosRebsolRepository
        
    administradorUsuarios.Perfil:
        class: Rebsol\AdministradorUsuariosBundle\Repository\PerfilRepository
        
    DMM_val:
        class: Rebsol\HermesBundle\Services\DMM (validaciones)
```

### Parámetros Configurables

```yaml
parameters:
    administradorUsuarios.idModulo: 4
    administradorUsuarios.codigoModulo: AdministradorUsuarios
    administradorUsuarios.nombreModulo: AdministradorUsuarios
```

---

## ⚠️ Dependencias Externas

Este bundle depende fuertemente de:

1. **RebsolHermesBundle** - Entidades principales
2. **RebsolAgendaBundle** - Controlador base AgendaController
3. **MantenedoresBundle** - Auditoría
4. **Servicios:**
   - `Buscar_Paciente_Service` - Buscar personas
   - `Buscar_Funcionario_Service` - Validar funcionarios
   - `hermesTools.ServidorArchivos` - Subir archivos

---

## 📝 Notas para Migración a Symfony 6

### Cambios Críticos Necesarios:

1. **Formularios:** `AbstractType` → usar nuevos tipos
2. **Routing:** Convertir `routing.yml` a anotaciones/atributos
3. **Services:** Inyección de dependencias moderna
4. **EntityManager:** Actualizar sintaxis Doctrine
5. **Validaciones:** Namespace actualizado
6. **Seguridad:** Nuevo sistema de security
7. **Encoder:** Usar `PasswordHasherInterface`

### Recomendaciones:

- Separar lógica de controladores pesados
- Crear servicios para lógica de negocio
- Implementar DTOs para formularios
- Usar eventos de Symfony para acciones post-modificación
- Refactorizar queries a QueryBuilders o DQL optimizado

---

**Continúa en:** [02-CONTROLADORES-PRINCIPALES.md](./02-CONTROLADORES-PRINCIPALES.md)
