# AdministradorUsuariosBundle - Parte 3: Controladores Complementarios

## 👁️ DMMVerController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMVerController.php`  
**Extiende:** `DatosMaestrosMedicosController`  
**Rol:** Visualización de usuarios (solo lectura)

### Métodos

#### `verProfesionalAction($id)`
**Ruta:** `/{id}/ver`  
**Propósito:** Ver información completa de un profesional

```php
return $this->renderViewDMM([
    'from'   => 0,  // Profesional
    'new'    => 2,  // Ver
    'idUser' => $id,
    'path'   => '_Default\DatosMaestrosMedicos/MedicosVigentes',
    'source' => 'MedicoRead'
]);
```

**Datos mostrados:**
- Datos personales completos
- Especialidades asignadas
- Grupos y perfiles
- Tipos de médico
- Servicios asignados
- Historial de login
- Auditoría completa
- Estado de Zoom (si aplica)

#### `verUsuarioAction($id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/ver`  
**Propósito:** Ver información completa de un usuario

```php
return $this->renderViewDMM([
    'from'   => 1,  // Usuario
    'new'    => 2,  // Ver
    'idUser' => $id,
    'path'   => '_Default\MedicosVigentes',
    'source' => 'UsuarioRead'
]);
```

---

## ➕ DMMAddController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMAddController.php`  
**Extiende:** `DatosMaestrosMedicosController`  
**Rol:** Gestión de **Grupos y Perfiles** de usuarios

### Propósito Principal

Permite modificar grupos y perfiles de un usuario **SIN tocar sus datos personales/profesionales**.

### Métodos Públicos

#### `addAction(Request $request, $id)`
**Ruta:** `/{id}/add`  
**Vista:** GruposPerfilesAdmin.html.twig

**Proceso:**
```
1. Obtiene información del usuario
2. Obtiene grupos actuales
3. Obtiene perfiles actuales (directos + de grupos)
4. Crea formulario addpType con:
   - Grupos pre-seleccionados
   - Perfiles pre-seleccionados (activos)
5. Renderiza modal de edición
```

#### `adderedAction(Request $request, $id)`
**Ruta:** `/{id}/addered` [POST]  
**Procesa:** Actualización de grupos y perfiles

**Flujo:**
```
1. handleRequest()
2. Valida con ValidaForm2():
   - Debe tener al menos 1 perfil O 1 grupo
3. Si válido:
   - agregaGrupoDatos() → actualiza grupos
   - agregaPerfilDatos() → actualiza perfiles
   - Actualiza auditoria del usuario
   - Cierra sesión del usuario (perfiles modificados)
   - Retorna "Agregado"
```

---

### Lógica de Grupos: `agregaGrupoDatos()`

**Algoritmo:**
```
1. Query: Obtiene grupos actuales del usuario (ACTIVOS)

2. Compara formulario vs BD:
   - Grupos nuevos no en BD:
     * Crea RelUsuarioGrupo con estado ACTIVO
   
   - Grupos que ya existían pero estaban INACTIVOS:
     * Cambia estado a ACTIVO
   
   - Grupos en BD no en formulario:
     * Cambia estado a INACTIVO

3. Resultado: grupos sincronizados con selección
```

---

### Lógica de Perfiles: `agregaPerfilDatos()`

**Algoritmo:**
```
1. Query: Obtiene perfiles actuales del usuario (TODOS los estados)

2. Compara formulario vs BD:
   - Perfiles nuevos no en BD:
     * Crea RelUsuarioPerfil con estado ACTIVO
   
   - Perfiles que ya existían pero estaban INACTIVOS:
     * Cambia estado a ACTIVO
   
   - Perfiles en BD no en formulario:
     * Cambia estado a INACTIVO

3. Resultado: perfiles sincronizados con selección
```

**Nota importante:** Un perfil con estado INACTIVO sirve para **EXCLUIR** ese perfil aunque venga de un grupo.

---

### Método Helper

#### `busquedaPerfilActivos($id, $arrgrupo)`

**Propósito:** Construir lista de perfiles activos del usuario

**Algoritmo:**
```
1. Obtiene perfiles individuales ACTIVOS del usuario

2. Obtiene perfiles INACTIVOS (exclusiones explícitas)
   → Los guarda en $arrPerfilExcluidos

3. Si tiene grupos:
   - Obtiene perfiles de esos grupos
   - Solo agrega si NO está en exclusiones

4. Agrega perfiles individuales activos

5. Retorna array único de IDs de perfiles activos
```

**Ejemplo práctico:**
```
Usuario tiene:
- Grupo "Médicos" con perfiles: [1, 2, 3]
- Perfil individual 4 ACTIVO
- Perfil individual 2 INACTIVO (exclusión)

Resultado: [1, 3, 4]  (2 excluido aunque esté en grupo)
```

---

## 🗑️ DMMDellController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMDellController.php`  
**Extiende:** `DatosMaestrosMedicosController`  
**Rol:** **Eliminación lógica** (inactivación) de usuarios

### Método Principal

#### `dellAction(Request $request, $id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/dell`  
**Método:** POST/AJAX

**Proceso de Inactivación:**
```
1. Valida petición AJAX

2. Obtiene estados:
   - Estado.inactivo (0)
   - EstadoUsuarios.inactivo
   - EstadoEspecialidadMedica.inactivo

3. Inactiva entidades en orden:
   
   a) UsuariosRebsol:
      - idEstadoUsuario = INACTIVO
      - auditoria = now()
   
   b) RelUsuarioCargo:
      - idEstado = INACTIVO
   
   c) RolProfesional:
      - idEstado = INACTIVO
   
   d) RelUsuarioTipoMedico:
      - idEstado = INACTIVO
   
   e) RelEspecialidadProfesional (todas):
      - idEstado = INACTIVO

4. Cierra sesión del usuario (botarUsuarioRebsol)
   - Mensaje: "Has sido desactivado por Administrador"

5. Retorna JSON: "ok" o mensaje de error
```

**⚠️ NO elimina:**
- RelUsuarioServicio (mantiene historial)
- RelUsuarioGrupo (mantiene relaciones)
- RelUsuarioPerfil (mantiene relaciones)
- UsuarioHistorialContrasena (mantiene auditoría)

---

## ✅ DMMActController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMActController.php`  
**Extiende:** `AgendaController`  
**Rol:** **Reactivación** de usuarios inactivos

### Método Principal

#### `actAction($id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/act` [POST]  
**Parámetros:** `generarNuevaContrasenia` (nueva contraseña)

**Proceso de Reactivación:**
```
1. Recibe nueva contraseña (obligatoria)

2. Obtiene estados:
   - Estado.activo (1)
   - EstadoUsuarios.activo
   - EstadoRelUsuarioServicio.Activo
   - EstadoEspecialidadMedica.activo

3. Actualiza UsuariosRebsol:
   - contrasenaMd5 = hash(nueva contraseña)
   - idEstadoUsuario = ACTIVO
   - auditoria = now()

4. Reactiva entidades:
   
   a) RelUsuarioCargo:
      - idEstado = ACTIVO (si existe)
   
   b) RolProfesional:
      - idEstado = ACTIVO (si existe)
   
   c) RelUsuarioServicio (primero encontrado):
      - idEstado = ACTIVO
   
   d) RelUsuarioTipoMedico:
      - idEstado = ACTIVO (si existe)
   
   e) RelEspecialidadProfesional (todas):
      - Cambia de INACTIVO a ACTIVO

5. Retorna JSON: mensaje de éxito/error
```

**Nota:** Al reactivar, el usuario **debe cambiar su contraseña** en el primer login.

---

## 🔓 DMMUnlockController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMUnlockController.php`  
**Extiende:** `AgendaController`  
**Rol:** Desbloqueo de cuentas bloqueadas

### Casos de Bloqueo

1. **Expiración de Contraseña** (TIEMPO_LIMITE_LOGIN)
2. **Expiración de Acceso** (TIEMPO_LIMITE_EXPIRACION)
3. **Intentos Fallidos** (NUMERO_INTENTOS_FALLIDOS_LOGIN)

---

### Métodos

#### `unlockAction($id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/unlock`  
**Vista:** UserUnlock.html.twig

**Proceso:**
```
1. Obtiene parámetros de empresa
2. Valida cada tipo de bloqueo:
   - expiracionRestaurarContrasena()
   - expiracionAcceso()
   - intentosFallidos()
3. Calcula suma de bloqueos activos
4. Renderiza vista con opciones de desbloqueo
```

#### `unlockedAction($id)`
**Ruta:** `/DatosMaestrosMedicos/{id}/unlocked` [POST]  
**Procesa:** Desbloqueo efectivo

**Proceso según tipo:**
```
Si expiracionRestaurarContrasena = true:
  - UsuariosRebsol.auditoria = now()
  - Reinicia contador de días

Si expiracionAcceso = true:
  - UsuarioHistorialContrasena.fechaCreacion = now()
  - Reinicia contador de tiempo

Si intentosFallidos alcanzó límite:
  - UsuariosRebsol.intentosFallidos = 0
  - Resetea contador

Retorna: "activado"
```

---

### Validadores Privados

#### `expiracionRestaurarContrasena($idUser, $iEmpresa)`
```
1. Obtiene TIEMPO_LIMITE_LOGIN de parámetros
2. Obtiene UsuariosRebsol.auditoria
3. Suma días: auditoria + TIEMPO_LIMITE_LOGIN
4. Compara con hoy
5. Retorna true si hoy > fecha límite
```

#### `expiracionAcceso($idUser, $iEmpresa)`
```
1. Obtiene TIEMPO_LIMITE_EXPIRACION (segundos)
2. Obtiene última entrada de UsuarioHistorialContrasena
3. Suma segundos a fechaCreacion
4. Compara con hoy
5. Retorna true si hoy > fecha límite
```

#### `intentosFallidos($idUser, $iEmpresa)`
```
1. Obtiene NUMERO_INTENTOS_FALLIDOS_LOGIN
2. Obtiene UsuariosRebsol.intentosFallidos
3. Compara valores
4. Retorna true si son iguales (alcanzó límite)
```

---

## 📊 DMMExportarExcelController.php

**Ruta:** `Controller/_Default/DatosMaestrosMedicos/DMMExportarExcelController.php`  
**Rol:** Exportación de listado a Excel

### Método Principal

#### `exportExcelAction()`
**Ruta:** `/DatosMaestrosMedicos/ExportarExcel`  
**Retorna:** Archivo Excel (.xls)

**Columnas exportadas:**
- Tipo de Identificación
- Identificación
- Usuario
- Nombre Completo
- Rol
- Especialidades Médicas
- Grupos
- Perfiles
- Fecha Creación
- Última Conexión
- Estado

**Formato:** Usa PHPExcel para generar el archivo.

---

## 🔧 Controladores de Dependencias

**Ubicación:** `Controller/_Default/DatosMaestrosMedicos/Dependencias/`

Estos controladores manejan **peticiones AJAX** para cargar datos dinámicos en formularios.

---

### GrupoPerfilController.php

#### `IndexgrupoperfilAction()`
**Ruta:** `/DatosMaestrosMedicos/grupoperfil`  
**Parámetros:** `grp[]` (array de IDs de grupos)  
**Retorna:** JSON con IDs de perfiles de esos grupos

**Uso:** Al seleccionar grupos, carga automáticamente sus perfiles.

---

#### `IndexUsuarioperfilAction()`
**Ruta:** `/DatosMaestrosMedicos/Usuarioperfil`  
**Parámetros:** `idUser`  
**Retorna:** JSON con IDs de perfiles activos del usuario

**Lógica:**
```
1. Obtiene grupos del usuario
2. Obtiene perfiles de esos grupos (ACTIVOS)
3. Obtiene perfiles individuales ACTIVOS
4. Une ambos y retorna array único
```

---

#### `IndexUsuarioperfilOkAction()`
**Ruta:** `/DatosMaestrosMedicos/UsuarioperfilOk`  
**Parámetros:** `user`  
**Retorna:** JSON con IDs de perfiles ACTIVOS del usuario

**Diferencia con anterior:** Solo perfiles directos, no de grupos.

---

#### `IndexUsuarioperfilUpdateAction()`
**Ruta:** `/DatosMaestrosMedicos/UsuarioperfilUpdate`  
**Parámetros:** 
- `perfil` - ID del perfil
- `idUser` - ID del usuario
- `tipoActualizar` - 0 (desactivar) o 1 (activar)

**Proceso:**
```
Si tipoActualizar = 0 (desactivar):
  - Si RelUsuarioPerfil existe:
    * Estado = INACTIVO
  - Si no existe (viene de grupo):
    * Crea RelUsuarioPerfil con estado INACTIVO
    * (esto EXCLUYE el perfil del grupo)

Si tipoActualizar = 1 (activar):
  - Si RelUsuarioPerfil no existe:
    * Crea con estado ACTIVO
  - Si existe:
    * Cambia estado a ACTIVO
```

**Uso:** Toggle de perfiles en formulario sin submit completo.

---

#### `IndexUsuarioGrupoUpdateAction()`
**Ruta:** `/DatosMaestrosMedicos/UsuarioGrupoUpdate`  
**Similar a perfiles pero para grupos.**

---

#### `finalizarCambiosPerfilAction()`
**Ruta:** `/DatosMaestrosMedicos/FinalizarCambiosPerfil`  
**Propósito:** Cierra sesión del usuario tras cambios de perfil

---

### UnidadporSucursalController.php

#### `IndexUpSAction()`
**Ruta:** `/DatosMaestrosMedicos/Duniporsuc`  
**Parámetros:** `Sucursal` (ID)  
**Retorna:** JSON con unidades de esa sucursal

**Uso:** Cascada: Sucursal → Unidades

---

### ServicioporUnidadController.php

#### `IndexSpUAction()`
**Ruta:** `/DatosMaestrosMedicos/serporuni`  
**Parámetros:** `Unidad` (ID)  
**Retorna:** JSON con servicios de esa unidad

**Uso:** Cascada: Unidad → Servicios

---

### ValusernameController.php

#### `IndexValAction()`
**Ruta:** `/DatosMaestrosMedicos/valusername`  
**Parámetros:** `username`  
**Retorna:** JSON indicando si username ya existe

**Validación en tiempo real.**

---

### ValrutController.php

#### `IndexRutAction()`
**Ruta:** `/DatosMaestrosMedicos/valRut`  
**Parámetros:** `rut`  
**Retorna:** JSON indicando si RUT ya existe

**Validación en tiempo real.**

---

### VigenciaController.php

#### `validaVigenciaAction()`
**Ruta:** `/DatosMaestrosMedicos/ValidaVigentes`  
**Parámetros:** Varios datos del profesional  
**Retorna:** JSON con validación de vigencia

**Valida si profesional está vigente según criterios.**

---

## 🔄 Flujo Completo de Edición con Grupos/Perfiles

```
1. Usuario accede a editar usuario
   → renderViewDMM(new=0)

2. Usuario hace cambios básicos
   → Submit a editedUsuario

3. Usuario quiere cambiar grupos/perfiles
   → Click botón "Grupos y Perfiles"
   → AJAX a addAction($id)
   → Modal con formulario addpType

4. Usuario modifica grupos/perfiles
   → Submit a adderedAction($id)
   → agregaGrupoDatos()
   → agregaPerfilDatos()
   → botarUsuarioRebsol()

5. Usuario debe volver a iniciar sesión
   → Nuevos permisos aplicados
```

---

**Continúa en:** [04-REPOSITORIOS-Y-FORMULARIOS.md](./04-REPOSITORIOS-Y-FORMULARIOS.md)
