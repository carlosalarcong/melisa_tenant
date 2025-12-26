# AdministradorUsuariosBundle - Parte 2: Controladores Principales

## 📂 Ubicación
`src/Rebsol/AdministradorUsuariosBundle/Controller/_Default/DatosMaestrosMedicos/`

---

## 🎯 DatosMaestrosMedicosController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DatosMaestrosMedicosController.php`  
**Extiende:** `AgendaController` (de RebsolAgendaBundle)  
**Rol:** **Controlador base** - Toda la lógica común

### Responsabilidades Principales

1. **Método Central:** `renderViewDMM()` - Renderiza todas las vistas
2. **Gestión de Estados** - Helpers para obtener estados
3. **Validaciones de Seguridad**
4. **Integración con Zoom**
5. **Gestión de Arrays de Usuarios**

---

### Métodos Públicos Clave

#### `IndexAction(Request $request)` 
**Ruta:** `/DatosMaestrosMedicos`  
**Propósito:** Listado de **PROFESIONALES**

```php
return $this->renderViewDMM([
    'from'   => 0,  // Profesional
    'new'    => 3,  // Listado
    'render' => 'render',
    'path'   => '_Default\DatosMaestrosMedicos',
    'source' => 'index'
]);
```

#### `usuarioIndexAction()`
**Ruta:** `/` (ruta principal del bundle)  
**Propósito:** Listado de **USUARIOS ADMINISTRATIVOS**

```php
return $this->renderViewDMM([
    'from'   => 1,  // Usuario
    'new'    => 3,  // Listado
    'render' => 'render',
    'path'   => '_Default',
    'source' => 'index'
]);
```

#### `vincularZoomAction(Request $request)`
**Ruta:** `/VincularZoom`  
**Propósito:** Vincular usuario con Zoom para teleconsulta

**Flujo:**
```
1. Obtiene lista de usuarios Zoom (con paginación)
2. Busca si el correo del usuario existe en Zoom
3. Si existe:
   - Actualiza o valida el zoomUser en BD
4. Si no existe:
   - Crea usuario en Zoom vía API
   - Guarda ID en UsuariosRebsol.zoomUser
   - Envía email de confirmación
```

**Parámetros API Zoom:**
- `ApiZoom.User` - Usuario API
- `ApiZoom.Password` - Password API
- `ApiZoom.Url` - URL API

**Estados retornados:**
- `Activado` - Usuario confirmado
- `Por Confirmar` - Pendiente email
- `ID registrado con otro correo` - Conflicto

---

### Métodos Protegidos/Privados Importantes

#### `renderViewDMM(array $arr)` ⭐ **MÉTODO CLAVE**

**Parámetros del array:**
```php
[
    'from'         => 0|1,        // 0=Profesional, 1=Usuario
    'new'          => 0|1|2|3,    // Operación CRUD
    'render'       => 'render',   // Método renderizado
    'idUser'       => $id,        // ID usuario (opcional)
    'path'         => 'ruta',     // Path vista
    'source'       => 'archivo',  // Archivo twig
    'errorReturn'  => bool,       // Si hay errores
    'mensajeError' => string,     // Mensaje error (opcional)
    'form'         => $form,      // Formulario (opcional)
    'rol'          => $rol,       // Rol (opcional)
    'entity'       => $entity,    // Entidad (opcional)
    'esSelectType' => bool        // Si usa select de documentos
]
```

**Lógica por Operación:**

##### **new = 0 (EDITAR)**
```php
- Obtiene UsuariosRebsol y Persona
- Carga información de perfiles/grupos
- Obtiene especialidades bloqueadas
- Crea formulario DMMType pre-llenado
- Si tiene Zoom: verifica estado
- Retorna vista de edición
```

##### **new = 1 (CREAR)**
```php
- Crea entidades vacías
- Genera formulario DMMType nuevo
- Setea EstadoUsuario activo por defecto
- Retorna vista de creación
```

##### **new = 2 (VER)**
```php
- Obtiene todos los datos del usuario:
  * Especialidades
  * Grupos
  * Perfiles
  * Servicios
  * LoginLog
- Si tiene Zoom: verifica estado
- Retorna vista solo lectura
```

##### **new = 3 (LISTADO)**
```php
- Llama setArrayUsers() para construir datos
- Calcula licencias:
  * cantidadLicencias (configuración)
  * cantidadUsuariosActivos
  * licenciasDisponibles
  * licenciasUsadas
- Si from=1 (usuarios):
  * Valida existencia de datos maestros:
    - Sexo
    - TipoMédico
    - Cargo
    - Sucursal
    - Unidad
    - Servicio
  * Verifica expiraciones de contraseña/acceso
- Retorna vista con DataTable
```

---

#### `setArrayUsers($from, $new, $arr)`

**Propósito:** Construir array de usuarios con datos relacionados

**Si from = 0 (Profesionales):**
```php
Para cada usuario:
  - Obtiene sus especialidades (rolespecialidades())
  - Agrupa especialidades por usuario
  - Retorna: [idUR => [...datos, 'especialidades' => [...] ]]
```

**Si from = 1 (Usuarios):**
```php
Para cada usuario:
  - Obtiene grupos (grupo3())
  - Obtiene perfiles (perfil33())
  - Obtiene especialidades (rolespecialidades())
  - Obtiene último login (UltimoLoginlog())
  - Retorna: [
      idUR => [
        ...datos,
        'grupos' => [...],
        'perfil' => [...],
        'especialidades' => [...],
        'ultimoLogIn' => [...]
      ]
    ]
```

---

#### Métodos de Seguridad

##### `ExpiracionRestaurarContrasena()` (privado)
```php
Verifica si debe cambiar contraseña:
1. Obtiene parámetro TIEMPO_LIMITE_LOGIN (días)
2. Suma días al campo 'auditoria' de UsuariosRebsol
3. Compara con fecha actual
4. Retorna true si expiró
```

##### `ExpiracionAcceso()` (privado)
```php
Verifica si cuenta expiró:
1. Obtiene parámetro TIEMPO_LIMITE_EXPIRACION (segundos)
2. Suma segundos a fechaCreacion de UsuarioHistorialContrasena
3. Compara con fecha actual
4. Retorna true si expiró
```

##### `obtenerModulosDisponibles()`
```php
Obtiene módulos accesibles por usuario:
1. Query perfiles del usuario (directos)
2. Query perfiles de grupos del usuario
3. Une ambos resultados
4. Retorna módulos únicos disponibles
```

---

#### Métodos Helper

##### `estado($var)` - Obtiene entidades de estado
```php
Parámetros soportados:
- 'Estado.activo'
- 'Estado.inactivo'
- 'EstadoUsuarios.activo'
- 'EstadoUsuarios.inactivo'
- 'EstadoEspecialidadMedica.activo'
- 'EstadoEspecialidadMedica.inactivo'
- 'EstadoEspecialidadMedica.bloqueado'
- 'EstadoRelUsuarioServicio.Activo'
- 'EstadoRelUsuarioServicio.Inactivo'
- 'EstadoRelUsuarioServicio.Bloqueado'
```

##### `parametro($var)` - Obtiene IDs de parámetros
```php
Mismos parámetros que estado()
Retorna el ID en lugar de la entidad
```

##### `rUsuariosRebsol()` - Atajo al repositorio
```php
return $this->get('administradorUsuarios.UsuariosRebsol');
```

---

## 🆕 DMMNuevoController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMNuevoController.php`  
**Extiende:** `DatosMaestrosMedicosController`  
**Rol:** Crear nuevos usuarios y profesionales

### Rutas y Métodos

#### `nuevoProfesionalAction()`
**Ruta:** `/NuevoProfesional`  
**Vista:** MedicoCreate.html.twig

```php
return $this->renderViewDMM([
    'esSelectType' => true,
    'from'         => 0,
    'new'          => 1,
    'path'         => '_Default\DatosMaestrosMedicos/MedicosVigentes',
    'source'       => 'MedicoCreate'
]);
```

#### `crearProfesionalAction(Request $request)`
**Ruta:** `/crearProfesional` [POST]  
**Procesa:** Creación de profesional

#### `nuevoUsuarioAction()`
**Ruta:** `/DatosMaestrosMedicos/NuevoUsuario`  
**Vista:** UsuarioCreate.html.twig

```php
return $this->renderViewDMM([
    'esSelectType' => true,
    'from'         => 1,
    'new'          => 1,
    'path'         => '_Default/MedicosVigentes',
    'source'       => 'UsuarioCreate'
]);
```

#### `crearUsuarioAction(Request $request)`
**Ruta:** `/DatosMaestrosMedicos/crearUsuario` [POST]  
**Procesa:** Creación de usuario

---

### Flujo de Creación: `prepareDataInsertRegister($arr)`

**Entrada:**
```php
[
    'request' => Request,
    'from'    => 0|1,
    'new'     => 1,
    'path'    => string,
    'source'  => string
]
```

**Proceso:**
```
1. Valida petición POST
2. Crea entidad Persona vacía
3. Obtiene Rol del request
4. Crea formulario DMMType
5. handleRequest($request)
6. Valida formulario con validarFormularioNuevo():
   - Si tipo documento = 1 (RUT):
     * Valida formato RUT
     * Valida dígito verificador
   - Busca si persona ya existe (por identificación)
   - Si existe, verifica que no sea funcionario
7. Si válido:
   - Llama dataInsertRegister() para persistir
   - Retorna "Creado"
8. Si inválido:
   - Re-renderiza vista con errores
```

---

### Persistencia: `dataInsertRegister($arr)`

**Entidades creadas en orden:**

```php
1. Persona
   - identificacionExtranjero
   - rutPersona / digitoVerificador (si es RUT)
   - idTipoIdentificacionExtranjero
   - telefonos, correos
   - idEmpresa

2. Pnatural
   - nombrePnatural, apellidos
   - fechaNacimiento
   - idSexo
   - rutaFotoPnatural (imagen por defecto)
   - idPersona → Persona

3. UsuariosRebsol
   - nombreUsuario
   - contrasenaMd5 (con encoder)
   - rcm, registroSuperintendencia
   - fechaCreacion, auditoria
   - fechaTermino
   - intentosFallidos = 0
   - idEstadoUsuario
   - esSistema = 0, esSala = 0
   - esProfesionalUrgencia, esProfesionalIntegracion
   - soloModuloPacientes, soloPacientesAsignados
   - verCaja
   - idPersona → Persona

4. UsuarioHistorialContrasena
   - idUsuario → UsuariosRebsol
   - contrasena (con encoder)
   - fechaCreacion
   - idUsuarioCreacion (usuario logueado)

5. RolProfesional
   - idUsuario → UsuariosRebsol
   - idRol
   - comentario, comentarioWeb
   - cantidadSobrecupo
   - idEstado

6. RelUsuarioCargo
   - idUsuario → UsuariosRebsol
   - idCargo
   - idEstado

7. RelUsuarioServicio (loop por cada servicio)
   - idUsuario → UsuariosRebsol
   - idServicio
   - idEstado: ACTIVO si solo hay 1, INACTIVO si hay más

8. RelUsuarioTipoMedico
   - idUsuario → UsuariosRebsol
   - idTipoMedico
   - idEstado

9. RelEspecialidadProfesional (foreach especialidad)
   - idUsuario → UsuariosRebsol
   - idEspecialidadMedica
   - idEstado

10. PrevisionPnatural (foreach prevision)
    - idPnatural → Pnatural
    - idPrevision
    - fechaPrevision
```

**Nota crítica:** Si tipo documento = 1 (RUT chileno):
```php
$arrRut     = explode("-", $documentoPorDefecto);
$arrRut[0]  = str_replace(".", "", $arrRut[0]);
$rutPersona = intval($arrRut[0]);

// Guarda en ambos formatos:
$oPersona->setRutPersona($rutPersona);
$oPersona->setDigitoVerificador($arrRut[1]);
$oPersona->setIdentificacionExtranjero($rutPersona.'-'.$arrRut[1]);
```

---

### Validaciones Especiales

#### `usuarioConsultaFechaTerminoAction()`
**Ruta:** `/UsuarioConsultaFechaTermino`  
**Retorna:** "true" si fecha < hoy, "false" si fecha >= hoy

#### `usuarioConsultaFechaNacimientoAction()`
**Ruta:** `/UsuarioConsultaFechaNacimiento`  
**Valida:**
- Formato de fecha correcto
- Fecha válida (checkdate)
- Fecha no mayor a hoy
**Retorna:** "true"/"false"/"errorValidarFecha"

---

## ✏️ DMMEditController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMEditController.php`  
**Extiende:** `DatosMaestrosMedicosController`  
**Rol:** Editar usuarios y profesionales existentes

### Rutas y Métodos

#### `editUsuarioAction($id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/editarUsuario`  
**Vista:** UsuarioEdit.html.twig

#### `editedUsuarioAction(Request $request, $id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/editadoUsuario` [POST]  
**Procesa:** Actualización de usuario

#### `editSalaAction($id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/editarSala`  
**Vista:** SalaEdit.html.twig  
**Propósito:** Edición rápida solo de contraseña para usuarios "sala"

#### `editedSalaAction(Request $request, $id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/editadoSala` [POST]  
**Procesa:** Solo actualiza contraseña

---

### Flujo de Edición: `prepareDataInsertRegister($arr)`

```
1. Obtiene UsuariosRebsol existente
2. Obtiene Persona relacionada
3. Crea formulario DMMType pre-llenado
4. handleRequest($request)
5. Valida:
   - Rol vs Dependencias (ValidarRolyDependencia)
   - Unidades y servicios activos
   - Fecha de nacimiento válida
   - Al menos un servicio asignado
6. Si válido:
   - Llama dataInsertRegister() para actualizar
   - Retorna "Editado"
7. Si cambió password o servicios:
   - Cierra sesión del usuario (botarUsuarioRebsol)
```

---

### Actualización: `dataInsertRegister($arr)`

**Entidades actualizadas:**

```php
1. UsuariosRebsol
   - fechaTermino
   - rcm, registroSuperintendencia (si es médico)
   - auditoria = now()
   - esSistema = 0, esSala = 0
   - esProfesionalUrgencia, esProfesionalIntegracion
   - soloModuloPacientes, soloPacientesAsignados, verCaja
   - contrasenaMd5 (si cambió)
   - intentosFallidos = 0 (si cambió password)

2. UsuarioHistorialContrasena (si cambió password)
   - Nueva entrada con contraseña encriptada

3. Persona
   - telefonoMovil, telefonoFijo
   - correoElectronico, correoElectronico2

4. Pnatural
   - nombrePnatural, apellidos
   - fechaNacimiento
   - idSexo

5. RolProfesional
   - idRol
   - comentario, comentarioWeb (si es médico)
   - cantidadSobrecupo (si es médico)

6. RelUsuarioTipoMedico
   - Si cambió a médico: crea/actualiza
   - Si cambió a no-médico: inactiva

7. RelUsuarioCargo
   - idCargo

8. RelEspecialidadProfesional
   - Si es médico:
     * Nuevas especialidades: crea con estado ACTIVO
     * Especialidades bloqueadas: cambia a ACTIVO
     * Especialidades eliminadas: cambia a BLOQUEADO
   - Si no es médico:
     * Todas pasan a BLOQUEADO

9. PrevisionPnatural
   - Elimina previsiones no seleccionadas
   - Agrega nuevas previsiones seleccionadas

10. RelUsuarioServicio
    - Servicios nuevos: crea con estado INACTIVO
    - Servicios existentes: mantiene/actualiza
    - Servicios eliminados: cambia a BLOQUEADO
    - **Solo el primero queda ACTIVO**
```

**Lógica especial de servicios:**
```php
// Si solo hay 1 servicio: queda ACTIVO
if (count($servicios) == 1) {
    $servicio->setIdEstado(ACTIVO);
}

// Si hay múltiples: todos INACTIVO excepto el primero
if (count($servicios) > 1) {
    $servicios[0]->setIdEstado(ACTIVO);
    // resto en INACTIVO
}
```

---

### Métodos Adicionales

#### `verFotoPnaturalAction(Request $request)`
**Ruta:** `/verFotoPnatural` [POST]  
**Propósito:** Modal para ver/actualizar foto de perfil

#### `actualizarFotoPnaturalAction(Request $request)`
**Ruta:** `/DatosMaestrosMedicos/actualizarFotoUsuario` [POST]  
**Proceso:**
```
1. Recibe archivo de foto
2. Genera nombre único: user_{idPnatural}_{uniqid}.{ext}
3. Sube a servidor de archivos
4. Actualiza Pnatural.rutaFotoPnatural
```

#### `identificaRolAction(Request $request)`
**Ruta:** `/identificaRol`  
**Retorna:** "1" si rol es profesional clínico, "0" si no

#### `obtenerPrevisionPorPersona($idPersona)`
**Query:** Obtiene todas las previsiones de un Pnatural

---

**Continúa en:** [03-CONTROLADORES-COMPLEMENTARIOS.md](./03-CONTROLADORES-COMPLEMENTARIOS.md)
