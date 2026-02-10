# Implementación de SweetAlert2 para Confirmaciones de Eliminación

## Resumen
Reemplazo del `confirm()` nativo del navegador por **SweetAlert2** del template Velzon (Themesbrand v4.3.0).

## Cambios Realizados

### 1. Stimulus Controller ✅
**Archivo**: `assets/controllers/confirm_delete_controller.js`

Controller reutilizable con soporte para traducciones y configuración personalizada:

```javascript
// Uso básico en cualquier formulario DELETE
<form data-controller="confirm-delete" 
      data-action="submit->confirm-delete#confirm"
      method="post">
  <button type="submit">Eliminar</button>
</form>

// Configuración personalizada con traducciones
<form data-controller="confirm-delete"
      data-confirm-delete-title-value="{{ 'title'|trans }}"
      data-confirm-delete-text-value="{{ 'text'|trans }}"
      data-action="submit->confirm-delete#confirm">
</form>
```

### 2. Templates Actualizados ✅

#### Mantenedores Modernos (con Turbo)
- `templates/maintainers/_table_row.html.twig`
  - Reemplazado: `onsubmit="return confirm('...')"`
  - Por: `data-controller="confirm-delete"` + valores de traducción

#### Mantenedores Legacy
- `templates/maintainers/_base_index.html.twig`
  - Same approach

#### Admin
- `templates/admin/menu_config/index.html.twig`
  - Eliminación de ítems de menú con SweetAlert2

### 3. Traducciones ✅
**Archivo**: `translations/maintainers.es.yaml`

```yaml
maintainers:
  common:
    confirm_delete_title: '¿Está seguro?'
    confirm_delete_text: 'Esta acción no se puede deshacer'
    yes_delete: 'Sí, eliminar'
    cancel: 'Cancelar'
```

## Ventajas de SweetAlert2

### Antes (confirm nativo)
- ❌ Feo, sin estilos
- ❌ No personalizable
- ❌ Inconsistente entre navegadores
- ❌ Bloquea el thread del navegador

### Después (SweetAlert2)
- ✅ Moderno y responsive
- ✅ Totalmente personalizable
- ✅ Consistente en todos los navegadores
- ✅ Prometizado (no bloqueante)
- ✅ Iconos y colores configurables
- ✅ Soporte para traducciones
- ✅ Animaciones suaves
- ✅ Accessible (ARIA)

## Configuración Disponible

```javascript
// Valores configurables por data-attributes:
data-confirm-delete-title-value        // Título del modal
data-confirm-delete-text-value         // Texto descriptivo
data-confirm-delete-confirm-button-text-value  // Botón confirmar
data-confirm-delete-cancel-button-text-value   // Botón cancelar
data-confirm-delete-icon-value         // Icono: warning, error, question

// Colores predeterminados:
confirmButtonColor: '#dc3545'  // danger (rojo)
cancelButtonColor: '#6c757d'   // secondary (gris)
```

## Propagación Automática

Como todos los mantenedores heredan de:
- `AbstractMantenedorController` 
- `modern_index.html.twig` + `_table_row.html.twig`

**Los 132 mantenedores obtienen automáticamente SweetAlert2** sin modificar cada uno.

## Compatibilidad

- ✅ Turbo Frames (Refresh sin reload)
- ✅ Turbo Streams (DELETE con remove())
- ✅ Legacy pages sin Turbo
- ✅ Responsive (mobile/tablet/desktop)
- ✅ RTL support
- ✅ Dark mode ready

## Assets

SweetAlert2 ya estaba incluido en Velzon:
- `assets/libs/sweetalert2/` (librería minificada)
- `assets/app.js` (importado como `window.Swal`)
- No requiere instalación adicional

## Testing

Probar en:
1. Cualquier mantenedor (ej: `/app_servicio/servicio`)
2. Click en botón de eliminar (🗑️)
3. Verificar modal de SweetAlert2 aparece
4. Confirmar → Registro eliminado con Turbo Stream
5. Cancelar → Modal se cierra, sin acción

## Rollback

Si se necesita volver a confirm() nativo:
```twig
{# Remover data-attributes y agregar: #}
onsubmit="return confirm('¿Eliminar?')"
```
