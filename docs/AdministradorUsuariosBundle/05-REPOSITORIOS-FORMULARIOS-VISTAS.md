# AdministradorUsuariosBundle - Parte 4: Repositorios, Formularios y Vistas

## 📦 Repositorios

### UsuariosRebsolRepository.php

**Ubicación:** `Repository/UsuariosRebsolRepository.php`  
**Extiende:** `DefaultRepository`  
**Propósito:** Queries complejas relacionadas con usuarios

#### Métodos Principales (primeros 200 líneas analizadas)

##### `sexoxempresa($idEmpresaLogin)`
**Retorna:** Array de sexos disponibles en la empresa
```sql
SELECT x.nombreSexo as sexo
FROM Sexo x
WHERE x.idEmpresa = :idEmpresa
AND x.idEstado = activo
```

##### `ObtenerModulosDisponibles($oUsuarioRebsol, $oEstadoAct)`
**Retorna:** Módulos accesibles por el usuario

**Lógica:**
```
1. Query módulos de perfiles directos del usuario:
   UsuariosRebsol → RelUsuarioPerfil → Perfil → ModuloPerfil → Modulo

2. Query módulos de perfiles de grupos:
   UsuariosRebsol → RelUsuarioGrupo → Grupo → RelGrupoPerfil → 
   Perfil → ModuloPerfil → Modulo

3. Une ambos resultados (únicos)
4. Valida que módulo esté activo en la empresa (RelModuloEmpresa)
```

##### `rolespecialidades()`
**Retorna:** Array de especialidades por usuario
```sql
SELECT 
  u1.id as idUR2,
  eme.nombreEspecialidadMedica as Especialidad2,
  r0.id as idEspecialidad2,
  eme.id as idEspecialidadMedica
FROM RelEspecialidadProfesional r0
JOIN UsuariosRebsol u1
JOIN EspecialidadMedica eme
WHERE eme.idEmpresa = :empresa
AND r0.idEstado = 1
ORDER BY eme.nombreEspecialidadMedica ASC
```

##### `grupo($iEmpresa)` y `grupo3()`
**Retorna:** Grupos por usuario

**grupo():** Todos los grupos
```sql
SELECT g.nombre, u.id as idug
FROM RelUsuarioGrupo rg
JOIN UsuariosRebsol u
JOIN Grupo g
WHERE g.idEmpresa = :empresa
```

**grupo3():** Solo grupos activos
```sql
... WHERE g.idEmpresa = :empresa
AND rg.idEstado = activo
AND g.idEstado = activo
```

##### `UltimoLoginlog()`
**Retorna:** Fecha último login por usuario
```sql
SELECT MAX(ull.fechaLogin) as fecha, u.id as idug
FROM UsuarioLoginLog ull
JOIN UsuariosRebsol u
GROUP BY u.id
```

##### Otros métodos importantes (inferidos del uso):

- `DatosMaestrosMedicos()` - Listado completo de usuarios
- `DatosMaestrosMedicos2()` - Variante con filtros
- `perfil33()` - Perfiles por usuario
- `perfil2()` - Perfiles activos
- `grupo2()` - Grupos con filtros
- `ObtParametrosExpLogin()` - Parámetros de expiración
- `ExpRestPass()` - Validación expiración contraseña
- `ExpRestPass3()` - Historial de contraseña
- `ExpRestPass4()` - Último registro historial
- `GetCountServiciosEmpresa()` - Cuenta servicios
- `RelEspecialidadProfesionalFilter()` - Filtro especialidades
- `obtenerespecialidadconfecha()` - Especialidades bloqueadas
- `RelUsuarioServicioFilter()` - Filtro servicios
- `RelUsuarioServicioFilterActual()` - Servicios actuales
- `ValidarRolyDependencia()` - Validación rol-especialidades
- `InputDataForm()` - Pre-llenar formulario edición
- `getDataVistaVer()` - Datos para vista de lectura
- `obtenerCantidadDeLicenciasPorEmpresa()` - Cantidad licencias
- `perfilesPorUsuario()` - Perfiles de usuario

---

### PerfilRepository.php

**Ubicación:** `Repository/PerfilRepository.php`  
**Extiende:** `DefaultRepository`  
**Propósito:** Gestión de perfiles y relaciones

#### Método Principal

##### `busquedaGruposActivos($arrGrupoId)`
**Retorna:** IDs de perfiles de los grupos especificados

```sql
SELECT g.id
FROM RelGrupoPerfil g
WHERE g.idGrupo IN (:arrIdGrupos)
AND g.idEstado = activo
```

**Nota:** Hay una versión comentada más compleja que valida también RelUsuarioPerfil.

#### Otros métodos (inferidos del uso en controladores):

- `busquedaPerfilActivos($idUsuario)` - Perfiles directos activos
- `busquedaPerfilInactivos($idUsuario)` - Perfiles excluidos
- `busquedaPerfilesPorGrupo($arrGrupoId)` - Perfiles de grupos
- `ObtenerInformacionUsuarioPorPerfil($idUsuario)` - Info completa
- `ObtieneIdNombreGrupoPorUsuario($idUsuario)` - Grupos del usuario
- `ObtenerInformacionPerfilPorUsuario($idUsuario)` - Perfiles del usuario
- `busquedaGruposPorUsuario($idUsuario)` - IDs de grupos
- `ObtenerperfilesPorUsuario($arrGrupo)` - Perfiles según grupos

---

## 📝 Formularios (Form Types)

### DMMType.php

**Ubicación:** `Form/Type/_Default/DatosMaestrosMedicos/DMMType.php`  
**Propósito:** Formulario principal para crear/editar usuarios/profesionales

#### Opciones del Formulario

```php
[
    'isNew'              => bool,     // true=crear, false=editar
    'AdminUser'          => bool,     // true=usuario, false=profesional
    'oEmpresa'           => Empresa,  // Empresa actual
    'estado_activado'    => int,      // ID estado activo
    'database_default'   => EntityManager,
    'countServicios'     => int,      // Cantidad de servicios
    'Rol'                => Rol,      // Rol del usuario
    'estado_exclusion'   => int,      // Estado para exclusiones
    'esSelectType'       => bool      // Si muestra select documentos
]
```

#### Campos del Formulario

##### **Si esSelectType = true:**
```php
documento (EntityType)
  - TipoIdentificacionExtranjero
  - Dropdown de tipos de documento
  - Requerido
  - Data: idTipoIdentificacionDefault de empresa
```

##### **Si isNew = false (edición):**
```php
Especialidad2 (EntityType)
  - EspecialidadMedica
  - Multiple select
  - No mapeado
  - Filtrado por empresa
```

##### **Campos Persona:**
```php
telefonoFijo (TextType)
  - Opcional
  - Min: 8, Max: 10 caracteres

telefonoMovil (TextType)
  - Requerido
  - Min: 8, Max: 10 caracteres

correoElectronico (EmailType)
  - Requerido
  - Validación email

correoElectronico2 (EmailType)
  - Opcional
  - Min: 13, Max: 100 caracteres
```

##### **Si isNew = true (creación):**
```php
identificacion (TextType)
  - Requerido
  - Max: 12 caracteres
  - Para RUT o documento

otroDocumento (HiddenType)
  - Para documentos extranjeros
```

##### **Campos Pnatural:**
```php
nombrePnatural (TextType)
  - Requerido
  - Max: 60 caracteres

apellidoPaterno (TextType)
  - Requerido
  - Max: 45 caracteres

apellidoMaterno (TextType)
  - Opcional
  - Max: 45 caracteres

fechaNacimiento (DateType)
  - Requerido
  - Widget: single_text
  - Format: dd-MM-yyyy

idSexo (EntityType)
  - Sexo
  - Requerido
  - Filtrado por empresa
  - Solo sexos para personas (esPersona != 0)
```

##### **Campos UsuariosRebsol:**
```php
nombreUsuario (TextType)
  - Requerido (si isNew)
  - Readonly
  - Generado automáticamente

contrasenaMd5 (TextType)
  - Si AdminUser: requerido (si isNew), readonly
  - Si no AdminUser: opcional
  - Generado automáticamente o manual

rcm (TextType)
  - Opcional
  - Max: 45 caracteres
  - Solo para médicos

regsuper (TextType)
  - Registro Superintendencia
  - Opcional
  - Max: 250 caracteres
  - Solo para médicos

fechaTermino (DateType)
  - Opcional
  - Widget: single_text
  - Format: dd-MM-yyyy

EstadoUsuario (EntityType)
  - EstadoUsuarios
  - Requerido
  - Oculto (hidden)
```

##### **Campos Profesionales (si AdminUser = true):**
```php
esProfesionalUrgencia (CheckboxType)
  - Opcional
  - Define si atiende urgencias

esProfesionalIntegracion (CheckboxType)
  - Opcional
  - Integración con sistemas externos

soloModuloPacientes (CheckboxType)
  - Opcional
  - Restringe solo a módulo pacientes

soloPacientesAsignados (CheckboxType)
  - Opcional
  - Solo ve pacientes asignados

verCaja (CheckboxType)
  - Opcional
  - Acceso a módulo de caja
```

##### **Si Rol es Médico:**
```php
Rol (EntityType)
  - Rol
  - Requerido
  - Filtrado por empresa

obs (TextareaType)
  - Comentario
  - Opcional
  - Max: 5000 caracteres

obs2 (TextareaType)
  - Comentario Web
  - Opcional
  - Max: 5000 caracteres

sobrecupo (IntegerType)
  - Cantidad de sobrecupo permitido
  - Requerido
  - Min: 0, Max: 10

Tipomedico (EntityType)
  - TipoMedico
  - Requerido
  - Filtrado por empresa

Cargo (EntityType)
  - Cargo
  - Requerido
  - Filtrado por empresa

Especialidad (EntityType)
  - EspecialidadMedica
  - Multiple select
  - Requerido
  - Filtrado por empresa
```

##### **Campos Sucursal-Unidad-Servicio (Dinámicos):**

El formulario usa **Event Subscribers** para cargar dinámicamente:

```php
AddSucursalFieldSubscriber
  - Carga sucursales de la empresa

AddUnidadFieldSubscriber
  - Carga unidades según sucursal seleccionada

AddServicioFieldSubscriber
  - Carga servicios según unidad seleccionada
```

**Campos generados:**
```php
idSucursal (EntityType)
idUnidad (EntityType)
idServicio_1, idServicio_2, ..., idServicio_N (EntityType)
  - Dinámicamente según countServicios
  - Permite múltiples asignaciones
```

##### **Previsiones:**
```php
Prevision (EntityType)
  - Prevision
  - Multiple select
  - Opcional
  - Filtrado por empresa
```

---

### Otros FormTypes

#### addpType.php
**Ubicación:** `Form/Type/_Default/DatosMaestrosMedicos/MedicosVigentes/addpType.php`  
**Propósito:** Formulario para asignar perfiles

**Campos:**
- `perfil` (EntityType) - Multiple select de Perfil

#### addgType.php
**Ubicación:** `Form/Type/_Default/DatosMaestrosMedicos/MedicosVigentes/addgType.php`  
**Propósito:** Formulario para asignar grupos

**Campos:**
- `grupo` (EntityType) - Multiple select de Grupo

#### FotoPnaturalType.php
**Ubicación:** `Form/Type/_Default/DatosMaestrosMedicos/MedicosVigentes/FotoPnaturalType.php`  
**Propósito:** Subir foto de perfil

**Campos:**
- `foto` (FileType) - Upload de imagen

---

## 🎨 Vistas (Twig)

### Estructura de Vistas

```
Resources/views/
├── _Default/
│   ├── MedicosVigentes/              (Usuarios Administrativos)
│   │   ├── index.vigentes.html.twig  (Listado)
│   │   ├── UsuarioCreate.html.twig   (Crear)
│   │   ├── UsuarioEdit.html.twig     (Editar)
│   │   ├── UsuarioRead.html.twig     (Ver)
│   │   ├── UserUnlock.html.twig      (Desbloqueo)
│   │   ├── SalaEdit.html.twig        (Editar sala)
│   │   ├── UserAdd.html.twig         (Agregar perfil)
│   │   ├── GruposPerfilesAdmin.html.twig (Modal grupos/perfiles)
│   │   ├── tools.html.twig           (Botones acción)
│   │   ├── toolsDisable.html.twig    (Botones deshabilitado)
│   │   ├── toolsSala.html.twig       (Botones sala)
│   │   ├── DMMheaderList.html.twig   (Header listado)
│   │   ├── DMMfooterList.html.twig   (Footer listado)
│   │   └── Form/
│   │       ├── Add/                   (Formularios agregar)
│   │       ├── Crear/                 (Formularios crear)
│   │       ├── Edit/                  (Formularios editar)
│   │       ├── Ver/                   (Formularios ver)
│   │       └── Unlock/                (Formularios desbloqueo)
│   │
│   └── DatosMaestrosMedicos/         (Profesionales)
│       ├── index.html.twig           (Listado profesionales)
│       ├── MedicosVigentes/
│       │   ├── index.vigentes.html.twig
│       │   ├── MedicoCreate.html.twig
│       │   ├── MedicoEdit.html.twig
│       │   ├── MedicoRead.html.twig
│       │   ├── DMMheaderList.html.twig
│       │   ├── DMMfooterList.html.twig
│       │   └── Form/
│       │       ├── Add/
│       │       ├── Crear/
│       │       ├── Edit/
│       │       ├── Ver/
│       │       └── FotoPnatural/
│       └── Recycle/                   (Archivos OLD/backup)
│
└── UI/
    └── Macros/
        └── Agenda/
            ├── WidgetsAgenda.html.twig
            ├── WidgetsAdmUsr.html.twig
            └── validadorDependencias.html.twig
```

---

### Vistas Principales

#### index.vigentes.html.twig
**Propósito:** Listado de usuarios en DataTable

**Características:**
- Muestra información de licencias
- DataTable con filtros
- Columnas:
  * Tipo identificación
  * Identificación
  * Usuario
  * Nombre completo
  * Rol
  * Especialidades
  * Grupos/Perfiles
  * Fecha creación
  * Última conexión
  * Estado
  * Acciones (tools)

**JavaScript incluido:**
- Inicialización DataTable
- Filtros dinámicos
- Acciones AJAX (ver, editar, eliminar, activar)
- Export a Excel

---

#### UsuarioCreate.html.twig / MedicoCreate.html.twig
**Propósito:** Formulario de creación

**Estructura:**
```twig
{% extends 'layoutformulariosajax.html.twig' %}

<form id="form1" action="crear" method="post">
  <div class="widget-box">
    <h4>Datos Personales</h4>
    {% include 'Form/Crear/MedicoCreate_1.html.twig' %}
  </div>
  
  <div class="widget-box">
    <h4>Datos de Usuario</h4>
    {% include 'Form/Crear/MedicoCreate_2.html.twig' %}
  </div>
  
  <div class="widget-box">
    <h4>Datos Institución</h4>
    {% include 'Form/Crear/MedicoCreate_3.html.twig' %}
  </div>
</form>

{{ macros JavaScript }}
```

**JavaScript incluido:**
- Generación automática de usuario/password
- Validación RUT en tiempo real
- Validación username disponible
- Cascada Sucursal → Unidad → Servicio
- Validación fecha nacimiento/término
- Chosen para selects múltiples
- Máscaras de entrada

---

#### UsuarioEdit.html.twig / MedicoEdit.html.twig
**Propósito:** Formulario de edición

**Similar a Create pero:**
- Campos pre-llenados
- Campos no editables (identificación)
- Botón "Grupos y Perfiles" (modal)
- Botón "Cambiar Foto"
- Validación de cambios vs BD
- Especialidades bloqueadas (no editables con fecha)

---

#### UsuarioRead.html.twig / MedicoRead.html.twig
**Propósito:** Vista de solo lectura

**Secciones:**
1. **Datos Personales** (ver_1.html.twig)
2. **Datos de Usuario** (ver_2.html.twig)
3. **Datos Institución** (ver_3.html.twig)
4. **Grupos y Perfiles** (ver_4.html.twig)
5. **Auditoría** (render desde MantenedoresBundle)
6. **Registro Ingreso** (ver_5.html.twig - LoginLog)

**Todos los campos en modo lectura:**
```twig
<span class="text-unedited">{{ valor }}</span>
```

---

#### GruposPerfilesAdmin.html.twig
**Propósito:** Modal para editar grupos y perfiles

**Características:**
- Select múltiple de grupos
- Select múltiple de perfiles
- Sincronización grupos → perfiles (AJAX)
- Opciones para activar/desactivar individual
- Submit guarda cambios y cierra sesión del usuario

**JavaScript:**
```javascript
// Al cambiar grupos, actualiza perfiles disponibles
$('#grupo').on('change', function() {
  $.ajax({
    url: '/grupoperfil',
    data: { grp: grupos_seleccionados },
    success: function(perfiles) {
      // Actualiza select de perfiles
    }
  });
});
```

---

#### UserUnlock.html.twig
**Propósito:** Formulario de desbloqueo

**Muestra:**
- Motivo(s) de bloqueo:
  * Expiración contraseña
  * Expiración acceso
  * Intentos fallidos
- Botón para desbloquear cada motivo
- Parámetros configurados en empresa

---

### Fragmentos de Formulario (Parciales)

#### Form/Crear/MedicoCreate_1.html.twig
**Campos:** Datos personales
- Documento, Identificación
- Nombre, Apellidos
- Fecha nacimiento
- Sexo
- Teléfonos, correos

#### Form/Crear/MedicoCreate_2.html.twig
**Campos:** Datos usuario
- Usuario (auto-generado)
- Contraseña (auto-generada)
- RCM, Registro Superintendencia
- Fecha término
- Checkboxes profesionales

#### Form/Crear/MedicoCreate_3.html.twig
**Campos:** Datos institución
- Rol
- Tipo Médico
- Cargo
- Especialidades
- Previsiones
- Comentarios, sobrecupo
- Sucursal → Unidad → Servicios (dinámico)

---

## 🎭 Macros y Componentes Reutilizables

### WidgetsAgenda.html.twig / WidgetsAdmUsr.html.twig
**Ubicación:** `Resources/views/UI/Macros/Agenda/`

**Macros JavaScript incluidos:**

```twig
{{ macroWidgetAgenda.jsmodal() }}
  - Manejo de modales

{{ macroWidgetAgenda.keygenerate() }}
  - Generación usuario/password

{{ macroWidgetAgenda.validakeyup() }}
  - Validación en tiempo real

{{ macroWidgetAgenda.eventofechatermino() }}
  - Validación fecha término

{{ macroWidgetAgenda.nocutcopypaste() }}
  - Bloquea copiar/pegar en campos sensibles

{{ macroWidgetAgenda.rolismedic() }}
  - Muestra/oculta campos según rol

{{ macroWidgetAgenda.choisestyle_especialidad() }}
  - Estilo para select especialidades

{{ macroWidgetAgenda.choisestyle_prevision() }}
  - Estilo para select previsiones

{{ macroWidgetAgenda.botonesadicionalesform() }}
  - Botones guardar/cancelar

{{ macroWidgetAgenda.tipped() }}
  - Tooltips

{{ macroWidgetAgenda.RutDv() }}
  - Validador RUT chileno
```

### validadorDependencias.html.twig
**Macros de validación:**

```twig
{{ validadorDependencias.ValidaSucursalUnidadServicio() }}
  - Cascada de selects dinámicos
  - Carga Unidades según Sucursal
  - Carga Servicios según Unidad
```

---

**Continúa en:** [05-ARCHIVOS-NO-UTILIZADOS-Y-MIGRACION.md](./05-ARCHIVOS-NO-UTILIZADOS-Y-MIGRACION.md)
