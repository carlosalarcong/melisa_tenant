# Configuración de Stimulus en Melisa Tenant

Este documento describe cómo está configurado Stimulus en el proyecto y cómo crear nuevos controllers.

## Arquitectura

Melisa Tenant usa **Stimulus** como framework JavaScript para controllers reactivos. La configuración incluye:

- **Auto-registro automático** de controllers
- Convención de nombres basada en estructura de carpetas
- Webpack Encore para compilación
- Integración con Turbo para navegación SPA

## Checklist de Configuración Inicial

### 1. Instalar paquetes npm

```bash
npm install @hotwired/stimulus @hotwired/turbo sweetalert2 --save
```

### 2. Configurar webpack.config.js

Agregar entry point para JavaScript:

```javascript
.addEntry('js/main', './assets/app.js')
```

### 3. Crear bootstrap.js

Archivo: `assets/bootstrap.js`

```javascript
import { Application } from '@hotwired/stimulus';

const app = Application.start();

// Auto-registrar todos los controllers en ./controllers/**/*_controller.js
const controllers = require.context('./controllers', true, /_controller\.js$/);

controllers.keys().forEach((key) => {
    const controllerName = key
        .replace('./', '')
        .replace(/_controller\.js$/, '')
        .replace(/\//g, '--')
        .replace(/_/g, '-');
    
    app.register(controllerName, controllers(key).default);
});

window.Stimulus = app;
export { app };
```

### 4. Agregar scripts en layout

En `templates/partials/admin_vendor_scripts.html.twig`:

```html
<script src="/assets/runtime.js"></script>
<script src="/assets/js/main.js"></script>
```

### 5. Compilar assets

```bash
npm run build
# o para desarrollo con watch:
npm run dev
```

## Convención de Nombres

El sistema de auto-registro convierte automáticamente la ruta del archivo al nombre del controller:

| Ruta del archivo | Nombre en HTML |
|------------------|----------------|
| `assets/controllers/form_validator_controller.js` | `data-controller="form-validator"` |
| `assets/controllers/admin_user/username_generator_controller.js` | `data-controller="admin-user--username-generator"` |
| `assets/controllers/mantenedores/pais/pais_controller.js` | `data-controller="mantenedores--pais--pais"` |
| `assets/controllers/internal/default/patient_controller.js` | `data-controller="internal--default--patient"` |

### Reglas de conversión:

1. **Guiones bajos (`_`) → Guiones (`-`)**
2. **Separadores de carpetas (`/`) → Doble guión (`--`)**
3. Se elimina el sufijo `_controller.js`

## Crear un Nuevo Controller

### 1. Crear el archivo

```bash
touch assets/controllers/mi_nuevo_controller.js
```

### 2. Estructura básica del controller

```javascript
import { Controller } from '@hotwired/stimulus';

/**
 * Controller para [descripción]
 */
export default class extends Controller {
    // Definir targets (campos que el controller puede referenciar)
    static targets = ['campo1', 'campo2'];
    
    // Definir values (valores reactivos)
    static values = {
        url: String,
        enabled: Boolean
    };
    
    // Se ejecuta cuando el controller se conecta al DOM
    connect() {
        console.log('Controller conectado');
    }
    
    // Se ejecuta cuando el controller se desconecta del DOM
    disconnect() {
        // Cleanup si es necesario
    }
    
    // Métodos de acción (llamados desde HTML con data-action)
    miMetodo() {
        // Acceder a targets
        const valor = this.campo1Target.value;
        
        // Acceder a values
        const url = this.urlValue;
        
        // Lógica del método
    }
}
```

### 3. Usar en el template

```twig
<form data-controller="mi-nuevo">
    <input type="text" 
           data-mi-nuevo-target="campo1"
           data-action="blur->mi-nuevo#miMetodo">
    
    <input type="text" 
           data-mi-nuevo-target="campo2">
    
    <button type="button" 
            data-action="mi-nuevo#miMetodo">
        Ejecutar
    </button>
</form>
```

## Estructura de Atributos Data

### data-controller

Define qué controller(s) controlan el elemento:

```html
<!-- Un controller -->
<div data-controller="modal">

<!-- Múltiples controllers -->
<div data-controller="modal form-validator">
```

### data-{controller}-target

Marca elementos que el controller puede referenciar:

```html
<input data-usuario--form-target="email">
<input data-usuario--form-target="password">
```

En el controller:

```javascript
static targets = ['email', 'password'];

connect() {
    console.log(this.emailTarget.value);
    console.log(this.passwordTarget.value);
}
```

### data-action

Define qué método ejecutar en respuesta a eventos:

```html
<!-- Sintaxis: evento->controller#metodo -->
<button data-action="click->modal#open">Abrir</button>

<!-- Evento por defecto (click para buttons, submit para forms) -->
<button data-action="modal#open">Abrir</button>

<!-- Múltiples acciones -->
<input data-action="blur->validator#check focus->validator#clear">
```

### data-{controller}-{name}-value

Pasar valores al controller:

```html
<div data-controller="list"
     data-list-url-value="/api/items"
     data-list-per-page-value="10">
</div>
```

En el controller:

```javascript
static values = {
    url: String,
    perPage: Number
};

connect() {
    fetch(this.urlValue)
        .then(/* ... */);
}
```

## Ejemplos de Controllers del Proyecto

### Username Generator (Admin User)

**Archivo:** `assets/controllers/admin_user/username_generator_controller.js`

**Uso en HTML:** `data-controller="admin-user--username-generator"`

**Funcionalidad:** Genera automáticamente el username basado en nombre y apellido.

```javascript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['name', 'lastName', 'username'];

    connect() {
        if (this.hasNameTarget) {
            this.nameTarget.addEventListener('blur', () => this.autoGenerateUsername());
        }
        
        if (this.hasLastNameTarget) {
            this.lastNameTarget.addEventListener('blur', () => this.autoGenerateUsername());
        }
    }

    autoGenerateUsername() {
        if (!this.hasNameTarget || !this.hasLastNameTarget || !this.hasUsernameTarget) {
            return;
        }

        const nombre = this.nameTarget.value.trim();
        const apellido = this.lastNameTarget.value.trim();

        if (!nombre || !apellido) {
            return;
        }

        const currentUsername = this.usernameTarget.value.trim();
        if (currentUsername !== '') {
            return;
        }

        const primeraLetra = nombre.charAt(0);
        const username = (primeraLetra + apellido)
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]/g, '');

        const wasReadonly = this.usernameTarget.hasAttribute('readonly');
        if (wasReadonly) {
            this.usernameTarget.removeAttribute('readonly');
        }

        this.usernameTarget.value = username;
        
        if (wasReadonly) {
            this.usernameTarget.setAttribute('readonly', 'readonly');
        }
        
        this.usernameTarget.dispatchEvent(new Event('change', { bubbles: true }));
        this.usernameTarget.dispatchEvent(new Event('input', { bubbles: true }));
    }

    generate() {
        if (this.hasUsernameTarget) {
            const wasReadonly = this.usernameTarget.hasAttribute('readonly');
            if (wasReadonly) {
                this.usernameTarget.removeAttribute('readonly');
            }
            
            this.usernameTarget.value = '';
            
            if (wasReadonly) {
                this.usernameTarget.setAttribute('readonly', 'readonly');
            }
        }
        
        this.autoGenerateUsername();
    }
}
```

**Uso en template:**

```twig
{{ form_start(form, {'attr': {
    'data-controller': 'admin-user--username-generator'
}}) }}

    {{ form_widget(form.name, {'attr': {
        'data-admin-user--username-generator-target': 'name'
    }}) }}

    {{ form_widget(form.lastName, {'attr': {
        'data-admin-user--username-generator-target': 'lastName'
    }}) }}

    <div class="input-group">
        {{ form_widget(form.username, {'attr': {
            'data-admin-user--username-generator-target': 'username'
        }}) }}
        <button type="button" 
                data-action="admin-user--username-generator#generate">
            <i class="ri-refresh-line"></i>
        </button>
    </div>

{{ form_end(form) }}
```

## Ventajas del Auto-registro

✅ **No necesitas modificar `bootstrap.js`** cada vez que crees un controller

✅ **Convención sobre configuración**: solo sigue la estructura de carpetas

✅ **Mantenimiento simplificado**: elimina un controller borrando su archivo

✅ **Descubrimiento automático**: nuevos controllers funcionan inmediatamente después de compilar

## Debugging

### Ver controllers registrados

En la consola del navegador:

```javascript
// Ver todos los controllers registrados
Object.keys(Stimulus.router.modulesByIdentifier)

// Ver controllers activos en la página
document.querySelectorAll('[data-controller]')
```

### Habilitar logs temporales

Para debug, agrega console.log en `bootstrap.js`:

```javascript
controllers.keys().forEach((key) => {
    const controllerName = /* ... */;
    console.log(`✅ ${controllerName} (${key})`);
    app.register(controllerName, controllers(key).default);
});
```

### Problemas comunes

**Controller no se conecta:**
- Verifica que el nombre en `data-controller` coincida con el auto-generado
- Usa guiones (`-`) no underscores (`_`)
- Revisa la consola por errores de sintaxis

**Targets no se encuentran:**
- Verifica que `static targets = ['nombre']` esté definido
- El nombre del target en HTML debe coincidir: `data-{controller}-target="nombre"`

**Actions no se ejecutan:**
- Verifica la sintaxis: `data-action="evento->controller#metodo"`
- El método debe existir en el controller
- Revisa la consola por errores

## Recursos

- [Stimulus Handbook](https://stimulus.hotwired.dev/handbook/introduction)
- [Stimulus Reference](https://stimulus.hotwired.dev/reference/controllers)
- [Turbo Documentation](https://turbo.hotwired.dev/)
- [Webpack require.context](https://webpack.js.org/guides/dependency-management/#requirecontext)

## Actualización

**Última actualización:** 2025-12-29  
**Versión Stimulus:** 3.2.2  
**Versión Turbo:** 8.0.20
