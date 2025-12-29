# 🎮 Guía Completa de Stimulus - Melisa Tenant

**Documentación unificada de Stimulus** - Configuración, conceptos y ejemplos prácticos

**Última actualización:** 2025-12-29 | **Stimulus:** 3.2.2 | **Turbo:** 8.0.20

---

## 📋 Tabla de Contenidos

1. [¿Qué es Stimulus?](#qué-es-stimulus)
2. [Configuración Inicial](#configuración-inicial)
3. [Convención de Nombres](#convención-de-nombres)
4. [Crear un Controller](#crear-un-controller)
5. [Conceptos Fundamentales](#conceptos-fundamentales)
6. [Ejemplos Prácticos](#ejemplos-prácticos)
7. [Debugging](#debugging)
8. [Best Practices](#best-practices)

---

## 🎯 ¿Qué es Stimulus?

**Stimulus** es un framework JavaScript modesto diseñado para mejorar HTML existente sin necesidad de Virtual DOM o SPAs complejas.

### Filosofía

- **HTML-first:** El HTML es la fuente de verdad
- **Progressive enhancement:** Mejora páginas existentes sin reescribirlas
- **Convención sobre configuración:** Patrones claros y consistentes
- **Modest JavaScript:** No reemplaza el HTML, lo complementa

### ¿Por qué Stimulus en Melisa Tenant?

✅ **Integración con Symfony** - Funciona perfectamente con Twig templates  
✅ **Sin build complejo** - Solo Webpack Encore  
✅ **Turbo Drive** - Navegación SPA sin recargar la página  
✅ **Mantenible** - Código limpio y organizado por features  
✅ **Progresivo** - Se puede adoptar incrementalmente

---

## ⚙️ Configuración Inicial

### 1. Instalar Dependencias

```bash
npm install @hotwired/stimulus @hotwired/turbo sweetalert2 --save
```

### 2. Configurar Webpack

En `webpack.config.js`, agregar entry point para JavaScript:

En `webpack.config.js`, agregar entry point para JavaScript:

```javascript
Encore
    // ... otras configuraciones
    .addEntry('js/main', './assets/app.js')
    // ... resto del config
```

### 3. Crear bootstrap.js con Auto-registro

**Archivo:** `assets/bootstrap.js`

```javascript
import { Application } from '@hotwired/stimulus';

const app = Application.start();

// 🚀 AUTO-REGISTRO AUTOMÁTICO
// Busca recursivamente todos los *_controller.js en ./controllers/
const controllers = require.context('./controllers', true, /_controller\.js$/);

controllers.keys().forEach((key) => {
    // Convierte la ruta al nombre del controller
    // ./admin_user/username_generator_controller.js → admin-user--username-generator
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

**✅ Ventaja:** Ya no necesitas modificar este archivo cada vez que crees un controller nuevo.

### 4. Configurar app.js

**Archivo:** `assets/app.js`

```javascript
import './bootstrap.js';
import Swal from 'sweetalert2';

// SweetAlert2 disponible globalmente
window.Swal = Swal;
```

### 5. Incluir Scripts en Layout

En `templates/partials/admin_vendor_scripts.html.twig`:

```html
<!-- Runtime de Webpack -->
<script src="/assets/runtime.js"></script>

<!-- JavaScript compilado con Stimulus -->
<script src="/assets/js/main.js"></script>
```

### 6. Compilar Assets

```bash
# Compilación única
npm run build

# Modo desarrollo con watch
npm run dev

# Producción optimizada
npm run build
```

---

## 📁 Convención de Nombres

El sistema de **auto-registro** convierte automáticamente la ruta del archivo al nombre del controller:

| Ruta del Archivo | Nombre en HTML |
|------------------|----------------|
| `controllers/form_validator_controller.js` | `data-controller="form-validator"` |
| `controllers/admin_user/username_generator_controller.js` | `data-controller="admin-user--username-generator"` |
| `controllers/mantenedores/pais/pais_controller.js` | `data-controller="mantenedores--pais--pais"` |
| `controllers/internal/default/patient_controller.js` | `data-controller="internal--default--patient"` |

### Reglas de Conversión

1. **Underscores (`_`) → Guiones (`-`)**
2. **Separadores de carpetas (`/`) → Doble guión (`--`)**
3. **Se elimina el sufijo `_controller.js`**

### Ejemplo Paso a Paso

```
Archivo: assets/controllers/admin_user/username_generator_controller.js

Paso 1: Eliminar ./ → admin_user/username_generator_controller.js
Paso 2: Eliminar _controller.js → admin_user/username_generator
Paso 3: / → -- → admin_user--username_generator
Paso 4: _ → - → admin-user--username-generator

✅ HTML: data-controller="admin-user--username-generator"
```

---

## 🛠️ Crear un Controller

### Paso 1: Crear el Archivo

```bash
# Estructura recomendada por feature
touch assets/controllers/mi_feature/mi_controller.js

# O en raíz si es genérico
touch assets/controllers/modal_controller.js
```

### Paso 2: Estructura Básica

```javascript
import { Controller } from '@hotwired/stimulus';

/**
 * Controller para [descripción breve]
 * 
 * Uso:
 * <div data-controller="mi-feature--mi">
 *   ...
 * </div>
 */
export default class extends Controller {
    // 1. TARGETS - Elementos HTML referenciables
    static targets = ['input', 'output', 'button'];
    
    // 2. VALUES - Datos desde HTML
    static values = {
        url: String,
        enabled: { type: Boolean, default: true },
        count: { type: Number, default: 0 }
    };
    
    // 3. CLASSES - Clases CSS configurables
    static classes = ['loading', 'success', 'error'];
    
    // 4. LIFECYCLE - Se ejecuta al conectar al DOM
    connect() {
        console.log('Controller conectado');
        this.setupEventListeners();
    }
    
    // 5. LIFECYCLE - Se ejecuta al desconectar del DOM
    disconnect() {
        this.cleanup();
    }
    
    // 6. ACTIONS - Métodos públicos llamables desde HTML
    miMetodo(event) {
        event.preventDefault();
        
        // Acceder a targets
        const valor = this.inputTarget.value;
        
        // Acceder a values
        if (this.enabledValue) {
            // Lógica...
        }
        
        // Actualizar output
        this.outputTarget.textContent = valor;
    }
    
    // 7. CALLBACKS - Se ejecutan cuando cambian values
    enabledValueChanged() {
        if (this.enabledValue) {
            this.element.classList.remove('disabled');
        } else {
            this.element.classList.add('disabled');
        }
    }
    
    // 8. MÉTODOS PRIVADOS
    setupEventListeners() {
        // Configuración inicial
    }
    
    cleanup() {
        // Limpieza de recursos
    }
}
```

### Paso 3: Usar en Template Twig

```twig
<div data-controller="mi-feature--mi"
     data-mi-feature--mi-url-value="{{ path('api_endpoint') }}"
     data-mi-feature--mi-enabled-value="true"
     data-mi-feature--mi-count-value="10">
     
    {# Target: input #}
    <input type="text" 
           data-mi-feature--mi-target="input"
           data-action="keyup->mi-feature--mi#miMetodo">
    
    {# Target: output #}
    <div data-mi-feature--mi-target="output"></div>
    
    {# Target: button con action #}
    <button type="button"
            data-mi-feature--mi-target="button"
            data-action="click->mi-feature--mi#miMetodo">
        Ejecutar
    </button>
</div>
```

### Paso 4: Compilar

```bash
npm run build
```

✅ **El controller se registra automáticamente**. No necesitas modificar `bootstrap.js`.

---

## 🎓 Conceptos Fundamentales

### 1. Controllers

**Definición:** Clases JavaScript que agregan comportamiento a elementos HTML.

```javascript
export default class extends Controller {
    connect() {
        // Se ejecuta cuando el elemento con data-controller se monta en el DOM
        this.element.classList.add('ready');
    }
    
    disconnect() {
        // Se ejecuta cuando el elemento se desmonta del DOM
        this.element.classList.remove('ready');
    }
}
```

**Propiedades especiales:**

- `this.element` - El elemento HTML con `data-controller`
- `this.identifier` - El nombre del controller (ej: "admin-user--username-generator")
- `this.application` - La instancia de Stimulus Application

### 2. Targets

**Definición:** Referencias a elementos HTML específicos dentro del scope del controller.

```javascript
export default class extends Controller {
    static targets = ['name', 'email', 'submit'];
    
    connect() {
        // Acceder a targets individuales
        console.log(this.nameTarget);        // Primer elemento encontrado
        console.log(this.emailTarget);
        
        // Verificar existencia
        if (this.hasNameTarget) {
            this.nameTarget.focus();
        }
        
        // Acceder a múltiples targets del mismo tipo
        console.log(this.submitTargets);     // Array de todos los elementos
    }
}
```

**HTML:**

```html
<div data-controller="form">
    <input data-form-target="name">
    <input data-form-target="email">
    <button data-form-target="submit">Enviar</button>
</div>
```

**APIs generadas automáticamente:**

- `this.{name}Target` - Primer elemento (lanza error si no existe)
- `this.{name}Targets` - Array de todos los elementos
- `this.has{Name}Target` - Boolean, verifica existencia

### 3. Actions

**Definición:** Conexiones entre eventos DOM y métodos del controller.

**Sintaxis:** `evento->controller#metodo`

```html
<!-- Evento explícito -->
<button data-action="click->modal#open">Abrir</button>

<!-- Evento por defecto (click en buttons, submit en forms) -->
<button data-action="modal#open">Abrir</button>

<!-- Múltiples actions -->
<input data-action="blur->validator#check focus->validator#clear">

<!-- Con modificadores -->
<form data-action="submit->form#save:prevent">

<!-- Eventos globales -->
<div data-controller="scroll" 
     data-action="scroll@window->scroll#update">
</div>
```

**Modificadores disponibles:**

- `:prevent` - Llama `event.preventDefault()`
- `:stop` - Llama `event.stopPropagation()`
- `:self` - Solo si event.target === element
- `:once` - Se ejecuta solo una vez

**Ejemplo con modificadores:**

```html
<form data-action="submit->form#save:prevent:stop">
    <!-- Previene submit y detiene propagación -->
</form>

<div data-action="click->menu#toggle:self">
    <!-- Solo se activa si haces click directo en el div, no en hijos -->
    <button>No activará el toggle</button>
</div>
```

### 4. Values

**Definición:** Datos que se pasan desde HTML al controller, con tipado y valores por defecto.

```javascript
export default class extends Controller {
    static values = {
        url: String,
        count: Number,
        enabled: Boolean,
        items: Array,
        config: Object,
        // Con valor por defecto
        timeout: { type: Number, default: 5000 }
    };
    
    connect() {
        console.log(this.urlValue);          // "https://api.example.com"
        console.log(this.countValue);        // 42
        console.log(this.enabledValue);      // true
        console.log(this.itemsValue);        // [1, 2, 3]
        console.log(this.configValue);       // {key: "value"}
        console.log(this.timeoutValue);      // 5000 (default)
    }
    
    // Callbacks automáticos cuando cambia un value
    urlValueChanged(newValue, oldValue) {
        console.log(`URL cambió de ${oldValue} a ${newValue}`);
        this.fetchData();
    }
    
    countValueChanged() {
        this.updateDisplay();
    }
}
```

**HTML:**

```html
<div data-controller="api"
     data-api-url-value="https://api.example.com"
     data-api-count-value="42"
     data-api-enabled-value="true"
     data-api-items-value='[1, 2, 3]'
     data-api-config-value='{"key": "value"}'>
</div>
```

**APIs generadas:**

- `this.{name}Value` - Getter/setter del valor
- `this.has{Name}Value` - Boolean, verifica si está definido
- `{name}ValueChanged(newValue, oldValue)` - Callback automático

### 5. Classes

**Definición:** Clases CSS configurables desde HTML para evitar hardcodear estilos.

```javascript
export default class extends Controller {
    static classes = ['loading', 'success', 'error', 'hidden'];
    
    async save() {
        // Agregar clase loading
        this.element.classList.add(this.loadingClass);
        this.element.classList.remove(this.hiddenClass);
        
        try {
            await this.performSave();
            
            // Cambiar a success
            this.element.classList.remove(this.loadingClass);
            this.element.classList.add(this.successClass);
        } catch (error) {
            // Cambiar a error
            this.element.classList.remove(this.loadingClass);
            this.element.classList.add(this.errorClass);
        }
    }
}
```

**HTML:**

```html
<div data-controller="save"
     data-save-loading-class="spinner-border"
     data-save-success-class="alert-success"
     data-save-error-class="alert-danger"
     data-save-hidden-class="d-none">
</div>
```

**Ventaja:** Puedes usar diferentes frameworks CSS sin cambiar el JavaScript.

---

## 💼 Ejemplos Prácticos

### Ejemplo 1: Username Generator (Admin User)

**Archivo:** `assets/controllers/admin_user/username_generator_controller.js`

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
            return; // No sobrescribir
        }

        // Generar: primera letra + apellido
        const primeraLetra = nombre.charAt(0);
        const username = (primeraLetra + apellido)
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Quitar tildes
            .replace(/[^a-z0-9]/g, '');       // Solo alfanuméricos

        // Manejar campo readonly
        const wasReadonly = this.usernameTarget.hasAttribute('readonly');
        if (wasReadonly) {
            this.usernameTarget.removeAttribute('readonly');
        }

        this.usernameTarget.value = username;
        
        if (wasReadonly) {
            this.usernameTarget.setAttribute('readonly', 'readonly');
        }
        
        // Disparar eventos para validaciones
        this.usernameTarget.dispatchEvent(new Event('change', { bubbles: true }));
        this.usernameTarget.dispatchEvent(new Event('input', { bubbles: true }));
    }

    generate() {
        // Método para botón de regenerar
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

**Template:**

```twig
{{ form_start(form, {'attr': {
    'data-controller': 'admin-user--username-generator'
}}) }}

    {{ form_row(form.name, {'attr': {
        'data-admin-user--username-generator-target': 'name'
    }}) }}

    {{ form_row(form.lastName, {'attr': {
        'data-admin-user--username-generator-target': 'lastName'
    }}) }}

    <div class="input-group">
        {{ form_widget(form.username, {'attr': {
            'data-admin-user--username-generator-target': 'username'
        }}) }}
        <button type="button" class="btn btn-outline-secondary"
                data-action="admin-user--username-generator#generate"
                title="Regenerar username">
            <i class="ri-refresh-line"></i>
        </button>
    </div>

{{ form_end(form) }}
```

### Ejemplo 2: Modal Genérico

**Archivo:** `assets/controllers/modal_controller.js`

```javascript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dialog', 'title', 'body', 'closeButton'];
    static classes = ['show', 'backdrop'];
    static values = {
        closable: { type: Boolean, default: true }
    };

    open() {
        this.dialogTarget.classList.add(this.showClass);
        document.body.classList.add(this.backdropClass);
        
        if (this.closableValue) {
            this.setupCloseHandlers();
        }
    }

    close() {
        this.dialogTarget.classList.remove(this.showClass);
        document.body.classList.remove(this.backdropClass);
        this.removeCloseHandlers();
    }

    setupCloseHandlers() {
        // Click fuera del modal
        this.boundClickOutside = this.clickOutside.bind(this);
        document.addEventListener('click', this.boundClickOutside);
        
        // Tecla Escape
        this.boundEscapeKey = this.escapeKey.bind(this);
        document.addEventListener('keydown', this.boundEscapeKey);
    }

    removeCloseHandlers() {
        if (this.boundClickOutside) {
            document.removeEventListener('click', this.boundClickOutside);
        }
        if (this.boundEscapeKey) {
            document.removeEventListener('keydown', this.boundEscapeKey);
        }
    }

    clickOutside(event) {
        if (event.target === this.dialogTarget) {
            this.close();
        }
    }

    escapeKey(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }

    disconnect() {
        this.removeCloseHandlers();
    }
}
```

**Template:**

```twig
<div data-controller="modal"
     data-modal-show-class="show"
     data-modal-backdrop-class="modal-backdrop"
     data-modal-closable-value="true">
     
    <button data-action="modal#open">Abrir Modal</button>
    
    <div class="modal" data-modal-target="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 data-modal-target="title">Título</h5>
                <button data-action="modal#close" 
                        data-modal-target="closeButton">×</button>
            </div>
            <div class="modal-body" data-modal-target="body">
                Contenido del modal
            </div>
        </div>
    </div>
</div>
```

### Ejemplo 3: Auto-save Form

**Archivo:** `assets/controllers/autosave_controller.js`

```javascript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'status'];
    static values = {
        url: String,
        delay: { type: Number, default: 1000 }
    };

    connect() {
        this.timeout = null;
        this.saving = false;
    }

    change() {
        clearTimeout(this.timeout);
        this.showPending();
        
        this.timeout = setTimeout(() => {
            this.save();
        }, this.delayValue);
    }

    async save() {
        if (this.saving) return;
        
        this.saving = true;
        this.showSaving();

        try {
            const formData = new FormData(this.formTarget);
            const response = await fetch(this.urlValue, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                this.showSuccess();
            } else {
                this.showError();
            }
        } catch (error) {
            this.showError();
        } finally {
            this.saving = false;
        }
    }

    showPending() {
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = 'Pendiente...';
            this.statusTarget.className = 'badge bg-warning';
        }
    }

    showSaving() {
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = 'Guardando...';
            this.statusTarget.className = 'badge bg-info';
        }
    }

    showSuccess() {
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = 'Guardado ✓';
            this.statusTarget.className = 'badge bg-success';
        }
    }

    showError() {
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = 'Error ✗';
            this.statusTarget.className = 'badge bg-danger';
        }
    }

    disconnect() {
        clearTimeout(this.timeout);
    }
}
```

**Template:**

```twig
<div data-controller="autosave"
     data-autosave-url-value="{{ path('save_draft') }}"
     data-autosave-delay-value="2000">
     
    <span data-autosave-target="status" class="badge bg-secondary">
        No guardado
    </span>
    
    <form data-autosave-target="form"
          data-action="input->autosave#change">
        <textarea name="content" rows="10"></textarea>
        <input type="text" name="title">
    </form>
</div>
```

---

## 🐛 Debugging

### Ver Controllers Registrados

```javascript
// En la consola del navegador
Object.keys(Stimulus.router.modulesByIdentifier)
// ["admin-user--username-generator", "modal", "form", ...]
```

### Ver Controllers Activos

```javascript
// Controllers en la página actual
document.querySelectorAll('[data-controller]')

// Controller específico
document.querySelector('[data-controller="modal"]')
```

### Habilitar Logs Temporales

En `bootstrap.js` para debug:

```javascript
controllers.keys().forEach((key) => {
    const controllerName = /* ... conversión ... */;
    
    // 🐛 Log temporal
    console.log(`✅ ${controllerName} (${key})`);
    
    app.register(controllerName, controllers(key).default);
});
```

### Stimulus Debug Mode

```javascript
// En assets/bootstrap.js
const app = Application.start();
app.debug = true; // Muestra logs en consola
```

### Problemas Comunes

#### ❌ Controller no se conecta

**Síntomas:** No aparece log de `connect()`, no funciona

**Causas:**
- Nombre incorrecto en `data-controller`
- Archivo no tiene sufijo `_controller.js`
- Error de sintaxis en el controller
- Assets no compilados

**Solución:**
```bash
# 1. Verificar nombre
console.log(Object.keys(Stimulus.router.modulesByIdentifier))

# 2. Recompilar
npm run build

# 3. Hard reload del navegador
Ctrl + Shift + R
```

#### ❌ Targets no se encuentran

**Error:** `Missing target element "name" for "form" controller`

**Causas:**
- Target no definido en `static targets = []`
- Typo en el nombre del target
- Elemento no existe en el DOM cuando se conecta
- Nombre incorrecto en atributo data-*

**Solución:**
```javascript
// Usar hasTarget antes de acceder
if (this.hasNameTarget) {
    this.nameTarget.focus();
}

// O usar targets (plural) que siempre devuelve array
this.nameTargets.forEach(target => {
    // ...
});
```

#### ❌ Actions no se ejecutan

**Síntomas:** Click/evento no hace nada

**Causas:**
- Sintaxis incorrecta en `data-action`
- Método no existe en el controller
- Event.preventDefault() no llamado en submit
- Typo en el nombre del método

**Solución:**
```html
<!-- ✅ Correcto -->
<button data-action="modal#open">Abrir</button>

<!-- ❌ Incorrecto -->
<button data-action="modal.open">Abrir</button>
<button data-action="modal->open">Abrir</button>
```

#### ❌ Values no se actualizan

**Síntomas:** `this.urlValue` es undefined o no cambia

**Causas:**
- Typo en el nombre del value
- JSON mal formado en Array/Object values
- No definido en `static values = {}`

**Solución:**
```javascript
// Verificar con has{Name}Value
if (this.hasUrlValue) {
    fetch(this.urlValue);
}

// JSON debe estar entre comillas simples en HTML
data-config-value='{"key": "value"}'
```

---

## ✨ Best Practices

### 1. Organización de Archivos

```
assets/controllers/
├── shared/              # Controllers reutilizables
│   ├── modal_controller.js
│   ├── dropdown_controller.js
│   └── tooltip_controller.js
├── admin_user/          # Feature-specific
│   ├── username_generator_controller.js
│   ├── password_strength_controller.js
│   └── role_selector_controller.js
├── mantenedores/
│   ├── base_controller.js
│   ├── list_controller.js
│   └── pais/
│       └── pais_controller.js
└── forms/               # Específicos de formularios
    ├── validator_controller.js
    └── autosave_controller.js
```

### 2. Naming Conventions

```javascript
// ✅ Bueno: Nombres descriptivos
export default class extends Controller {
    static targets = ['emailInput', 'submitButton', 'errorMessage'];
    
    validateEmail() { }
    showError() { }
}

// ❌ Malo: Nombres genéricos
export default class extends Controller {
    static targets = ['input1', 'btn', 'msg'];
    
    do() { }
    check() { }
}
```

### 3. Single Responsibility

```javascript
// ✅ Bueno: Un controller, una responsabilidad
class EmailValidatorController extends Controller {
    validateFormat() { }
    checkDomain() { }
}

class FormSubmitterController extends Controller {
    submit() { }
    handleResponse() { }
}

// ❌ Malo: Controller hace demasiado
class FormController extends Controller {
    validateEmail() { }
    validatePassword() { }
    validatePhone() { }
    submit() { }
    uploadFile() { }
    showModal() { }
}
```

### 4. Composición de Controllers

```html
<!-- ✅ Múltiples controllers especializados -->
<form data-controller="validator autosave form-submitter"
      data-validator-url-value="/validate"
      data-autosave-url-value="/draft"
      data-form-submitter-url-value="/submit">
    <!-- Cada controller tiene su responsabilidad -->
</form>
```

### 5. Cleanup en disconnect()

```javascript
export default class extends Controller {
    connect() {
        // Crear listeners
        this.boundResize = this.handleResize.bind(this);
        window.addEventListener('resize', this.boundResize);
        
        // Crear timers
        this.interval = setInterval(() => this.update(), 1000);
    }
    
    disconnect() {
        // ✅ Limpiar recursos
        window.removeEventListener('resize', this.boundResize);
        clearInterval(this.interval);
    }
}
```

### 6. Usar Values para Configuración

```javascript
// ✅ Bueno: Configurable desde HTML
export default class extends Controller {
    static values = {
        delay: { type: Number, default: 300 },
        minLength: { type: Number, default: 3 }
    };
}

// ❌ Malo: Hardcodeado
export default class extends Controller {
    search() {
        const DELAY = 300;
        const MIN_LENGTH = 3;
        // ...
    }
}
```

### 7. Validación de Targets

```javascript
// ✅ Bueno: Verificar existencia
export default class extends Controller {
    static targets = ['optional'];
    
    update() {
        if (this.hasOptionalTarget) {
            this.optionalTarget.textContent = 'Updated';
        }
    }
}

// ❌ Malo: Asumir que existe
export default class extends Controller {
    static targets = ['optional'];
    
    update() {
        this.optionalTarget.textContent = 'Updated'; // Error si no existe
    }
}
```

### 8. Manejo de Errores

```javascript
export default class extends Controller {
    async fetchData() {
        try {
            const response = await fetch(this.urlValue);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            this.updateDisplay(data);
            
        } catch (error) {
            console.error('Error fetching data:', error);
            this.showError(error.message);
            
            // Reportar a servicio de logging si existe
            if (window.errorLogger) {
                window.errorLogger.log(error);
            }
        }
    }
    
    showError(message) {
        if (this.hasErrorTarget) {
            this.errorTarget.textContent = message;
            this.errorTarget.classList.remove('d-none');
        } else {
            // Fallback si no hay target
            alert(message);
        }
    }
}
```

### 9. Documentación

```javascript
/**
 * Controller para auto-guardar formularios
 * 
 * Guarda automáticamente los cambios después de un delay configurable.
 * Muestra el estado del guardado (pending, saving, saved, error).
 * 
 * Uso:
 * <form data-controller="autosave"
 *       data-autosave-url-value="/api/save"
 *       data-autosave-delay-value="2000">
 * </form>
 * 
 * @fires autosave:success - Cuando se guarda exitosamente
 * @fires autosave:error - Cuando falla el guardado
 */
export default class extends Controller {
    static targets = ['form', 'status'];
    static values = {
        url: String,
        delay: { type: Number, default: 1000 }
    };
    
    /**
     * Marca el formulario como pendiente y programa el guardado
     * @param {Event} event - Evento input/change
     */
    change(event) {
        // ...
    }
}
```

### 10. Testing

```javascript
// Exportar controller para testing
export default class extends Controller {
    // ...
    
    // Métodos públicos para testing
    _getFormData() {
        return new FormData(this.formTarget);
    }
    
    _parseResponse(response) {
        return response.json();
    }
}

// En el test
import UsernameGeneratorController from './username_generator_controller';

describe('UsernameGeneratorController', () => {
    it('genera username correctamente', () => {
        const controller = new UsernameGeneratorController();
        // ... setup ...
        
        controller.autoGenerateUsername();
        
        expect(controller.usernameTarget.value).toBe('calarcon');
    });
});
```

---

## 📚 Recursos Adicionales

### Documentación Oficial

- [Stimulus Handbook](https://stimulus.hotwired.dev/handbook/introduction) - Guía oficial completa
- [Stimulus Reference](https://stimulus.hotwired.dev/reference/controllers) - API reference
- [Turbo Documentation](https://turbo.hotwired.dev/) - Framework de navegación SPA
- [Hotwired](https://hotwired.dev/) - Ecosistema completo (Turbo + Stimulus)

### Webpack & Build

- [Webpack require.context](https://webpack.js.org/guides/dependency-management/#requirecontext) - Auto-import
- [Webpack Encore](https://symfony.com/doc/current/frontend.html) - Integración con Symfony

### Ejemplos del Proyecto

Busca en el código:

```bash
# Ver todos los controllers del proyecto
find assets/controllers -name "*_controller.js"

# Buscar ejemplos de uso de targets
grep -r "static targets" assets/controllers/

# Ver uso en templates
grep -r "data-controller" templates/
```

---

## 🔄 Mantener Actualizado

Para actualizar Stimulus:

```bash
# Ver versión actual
npm list @hotwired/stimulus

# Actualizar
npm update @hotwired/stimulus @hotwired/turbo

# Recompilar
npm run build
```

---

## 📝 Changelog

**2025-12-29** - Versión 2.0
- Fusión de STIMULUS_GUIDE.md y STIMULUS_SETUP.md
- Documentación de auto-registro con require.context
- Ejemplos actualizados con controllers del proyecto
- Sección ampliada de debugging
- Best practices consolidadas

**2025-12-29** - Versión 1.0
- Setup inicial de Stimulus
- Configuración de webpack
- Primer controller (username_generator)

---

**¿Preguntas?** Consulta la [documentación oficial](https://stimulus.hotwired.dev/) o busca ejemplos en `assets/controllers/`.
