# Documentación Completa: AdministradorUsuariosBundle

## 📚 Índice General

Esta documentación analiza en detalle el **AdministradorUsuariosBundle** de Symfony 2 para facilitar su migración a Symfony 6.

---

## 📖 Documentos

### [Parte 1: Introducción y Arquitectura](./01-INTRODUCCION-Y-ARQUITECTURA.md)
**Contenido:**
- Propósito general del bundle
- Arquitectura y estructura de directorios
- Conceptos clave del negocio
  - Profesionales vs Usuarios Administrativos
  - Sistema de permisos (Grupos y Perfiles)
  - Gestión de servicios y ubicaciones
  - Estados críticos del sistema
  - Sistema de licencias
  - Seguridad y control de acceso
  - Integración con Zoom
- Flujo de datos principal
- Método central: `renderViewDMM()`
- Dependencias externas

**Lee esto si necesitas:**
- Entender el propósito general del módulo
- Comprender la lógica de negocio
- Conocer las entidades involucradas
- Entender el sistema de permisos

---

### [Parte 2: Controladores Principales](./02-CONTROLADORES-PRINCIPALES.md)
**Contenido:**
- **DatosMaestrosMedicosController** (controlador base)
  - Método `renderViewDMM()` detallado
  - Métodos de seguridad
  - Gestión de arrays de usuarios
  - Integración Zoom
- **DMMNuevoController** (crear usuarios)
  - Flujo de creación
  - Validaciones especiales
  - Persistencia de entidades
- **DMMEditController** (editar usuarios)
  - Flujo de edición
  - Actualización de entidades
  - Gestión de cambios críticos
  - Manejo de foto de perfil

**Lee esto si necesitas:**
- Entender cómo se crean usuarios
- Entender cómo se editan usuarios
- Conocer las validaciones aplicadas
- Ver la integración con Zoom

---

### [Parte 3: Controladores Complementarios](./04-CONTROLADORES-COMPLEMENTARIOS.md)
**Contenido:**
- **DMMVerController** (visualización)
- **DMMAddController** (grupos y perfiles)
  - Lógica de asignación de grupos
  - Lógica de asignación de perfiles
  - Sistema de inclusión/exclusión
- **DMMDellController** (inactivación)
- **DMMActController** (reactivación)
- **DMMUnlockController** (desbloqueo)
  - Tipos de bloqueo
  - Proceso de desbloqueo
- **DMMExportarExcelController**
- **Controladores de dependencias** (AJAX)
  - GrupoPerfilController
  - UnidadporSucursalController
  - ServicioporUnidadController
  - Validadores en tiempo real

**Lee esto si necesitas:**
- Entender gestión de grupos y perfiles
- Conocer el proceso de inactivación/reactivación
- Ver cómo funciona el desbloqueo de cuentas
- Entender las dependencias AJAX

---

### [Parte 4: Repositorios, Formularios y Vistas](./05-REPOSITORIOS-FORMULARIOS-VISTAS.md)
**Contenido:**
- **Repositorios**
  - UsuariosRebsolRepository (métodos principales)
  - PerfilRepository
- **Formularios (FormTypes)**
  - DMMType (formulario principal)
    - Opciones configurables
    - Todos los campos detallados
    - Campos dinámicos
  - addpType, addgType, FotoPnaturalType
- **Vistas Twig**
  - Estructura de vistas
  - Vistas principales (listado, crear, editar, ver)
  - Fragmentos de formularios
  - Macros y componentes reutilizables

**Lee esto si necesitas:**
- Conocer las queries de base de datos
- Entender el formulario principal
- Ver la estructura de vistas
- Conocer los componentes JavaScript

---

### [Parte 5: Archivos No Utilizados y Migración](./06-ARCHIVOS-NO-UTILIZADOS-Y-MIGRACION.md)
**Contenido:**
- **Archivos posiblemente no utilizados**
  - Carpeta Recycle
  - Archivos .OLD
  - Vistas de prueba
  - Duplicados
- **Plan completo de migración a Symfony 6**
  - Fase 1: Preparación ✅
  - Fase 2: Estructura base
  - Fase 3: Migrar controladores
  - Fase 4: Migrar formularios
  - Fase 5: Crear servicios
  - Fase 6: Migrar repositorios
  - Fase 7: Actualizar seguridad
  - Fase 8: Migrar vistas
  - Fase 9: Migrar routing
  - Fase 10: Validaciones modernas
- **Ejemplos de código: Antes vs Después**
- **Prioridades de migración**
- **Checklist completa**
- **Puntos críticos**
- **Estimación de tiempo: 2-4 meses**

**Lee esto si necesitas:**
- Identificar archivos para eliminar
- Planificar la migración a Symfony 6
- Ver ejemplos de código migrado
- Estimar tiempos y recursos

---

## 🎯 Lectura Recomendada Según Tu Objetivo

### "Necesito entender el bundle rápidamente"
1. [Parte 1](./01-INTRODUCCION-Y-ARQUITECTURA.md) → Sección "Conceptos Clave del Negocio"
2. [Parte 2](./02-CONTROLADORES-PRINCIPALES.md) → `renderViewDMM()` y flujos de operaciones
3. [Parte 4](./05-REPOSITORIOS-FORMULARIOS-VISTAS.md) → DMMType (formulario principal)

### "Voy a empezar la migración a Symfony 6"
1. [Parte 5](./06-ARCHIVOS-NO-UTILIZADOS-Y-MIGRACION.md) → Completa
2. [Parte 1](./01-INTRODUCCION-Y-ARQUITECTURA.md) → Dependencias y arquitectura
3. [Parte 2](./02-CONTROLADORES-PRINCIPALES.md) → Controladores a migrar
4. [Parte 3](./04-CONTROLADORES-COMPLEMENTARIOS.md) → Funcionalidades complementarias

### "Necesito modificar/arreglar algo específico"
- **Crear usuarios:** [Parte 2](./02-CONTROLADORES-PRINCIPALES.md) → DMMNuevoController
- **Editar usuarios:** [Parte 2](./02-CONTROLADORES-PRINCIPALES.md) → DMMEditController
- **Grupos/Perfiles:** [Parte 3](./04-CONTROLADORES-COMPLEMENTARIOS.md) → DMMAddController
- **Desbloqueo:** [Parte 3](./04-CONTROLADORES-COMPLEMENTARIOS.md) → DMMUnlockController
- **Vistas/Formularios:** [Parte 4](./05-REPOSITORIOS-FORMULARIOS-VISTAS.md)
- **Zoom:** [Parte 2](./02-CONTROLADORES-PRINCIPALES.md) → vincularZoomAction

### "Necesito entender una funcionalidad específica"
- **Licencias:** [Parte 1](./01-INTRODUCCION-Y-ARQUITECTURA.md) → Sección 7
- **Permisos (Grupos y Perfiles):** [Parte 1](./01-INTRODUCCION-Y-ARQUITECTURA.md) → Sección 4
- **Servicios:** [Parte 1](./01-INTRODUCCION-Y-ARQUITECTURA.md) → Sección 5
- **Seguridad:** [Parte 1](./01-INTRODUCCION-Y-ARQUITECTURA.md) → Sección 8
- **Especialidades:** [Parte 2](./02-CONTROLADORES-PRINCIPALES.md) → dataInsertRegister

---

## 📊 Estadísticas del Bundle

- **Controladores:** 12+ archivos
- **Vistas Twig:** 80+ archivos
- **Repositorios:** 3 archivos
- **FormTypes:** 4 archivos
- **Rutas configuradas:** 30+
- **Entidades relacionadas:** 15+
- **Líneas de código:** ~15,000+

---

## ⚠️ Advertencias Importantes

### 🔴 Lógica Crítica (NO cambiar sin entender completamente)
1. Sistema de perfiles: `Estado INACTIVO = EXCLUSIÓN`
2. Servicios activos: Solo uno puede estar activo
3. Especialidades bloqueadas: Con fecha no se pueden desasignar
4. Licencias: Validación atómica necesaria
5. Contraseñas: Mantener compatibilidad con existentes

### 🟡 Archivos Obsoletos (Candidatos a eliminar)
- Carpeta `Recycle/`
- Archivos `.OLD`
- `DMMNuevoController.php.bckup`
- Vistas `*Test.html.twig`

### 🟢 Archivos Core (NO eliminar)
- Todos los controladores principales
- DMMType.php
- UsuariosRebsolRepository.php
- Vistas activas de formularios
- Macros en UI/

---

## 📅 Historial de Documentación

| Versión | Fecha | Descripción |
|---------|-------|-------------|
| 1.0 | Diciembre 2025 | Documentación inicial completa del bundle |

---

## 🤝 Contribuciones

Si encuentras errores o deseas agregar información:
1. Revisa el código fuente actualizado
2. Actualiza el documento correspondiente
3. Mantén el mismo formato y estructura
4. Actualiza este índice si es necesario

---

## 📞 Soporte

Para dudas específicas durante la migración, consulta:
1. Esta documentación primero
2. Código fuente comentado
3. Equipo de arquitectura

---

**Documentación creada:** Diciembre 26, 2025  
**Autor:** GitHub Copilot (Claude Sonnet 4.5)  
**Versión:** 1.0  
**Estado:** ✅ Completo

---

## 🎉 Conclusión

Esta documentación proporciona una **guía completa** para:
- ✅ Entender la lógica de negocio del bundle
- ✅ Conocer cada componente en detalle
- ✅ Identificar archivos obsoletos
- ✅ Planificar la migración a Symfony 6
- ✅ Evitar errores comunes durante la migración

**¡Éxito en tu migración!** 🚀
