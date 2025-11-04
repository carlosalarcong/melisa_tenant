# 🎮 Stimulus - Guía Completa

**Documentación consolidada de Stimulus para Melisa Tenant**

**Fusiona**: STIMULUS_CONCEPTS.md + STIMULUS_INTEGRATION.md + CONTROLLER_STRUCTURE.md

---

## 📋 Tabla de Contenidos

1. [Conceptos Fundamentales](#conceptos-fundamentales)
2. [Arquitectura de Controladores](#arquitectura-de-controladores)
3. [Estructura de Archivos](#estructura-de-archivos)
4. [Integración con API Platform](#integración-con-api-platform)
5. [Ejemplos Prácticos](#ejemplos-prácticos)
6. [Best Practices](#best-practices)

---



# 📚 PARTE 1: CONCEPTOS FUNDAMENTALES

# 🎮 Stimulus Concepts & Examples - Guía Completa

## 📚 **¿Qué es Stimulus?**

**Stimulus** es un framework JavaScript modesto que permite agregar comportamiento dinámico a HTML existente sin necesidad de Virtual DOM o Single Page Applications complejas.

### 🎯 **Filosofía de Stimulus:**
- **HTML-first:** El HTML es la fuente de verdad
- **Progressive enhancement:** Mejora páginas existentes
- **Convención sobre configuración:** Patrones claros y consistentes
- **Modest JavaScript:** No reemplaza el HTML, lo mejora

---

## 🏗️ **Arquitectura Stimulus**

### 🔧 **Componentes principales:**

```javascript
// 1. CONTROLLER - Clase JavaScript
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    // 2. TARGETS - Elementos HTML que el controller puede referenciar
    static targets = ["name", "output"]
    
    // 3. VALUES - Datos que se pasan desde HTML
    static values = { url: String, count: Number }
    
    // 4. CLASSES - Clases CSS configurables
    static classes = ["loading", "success"]
    
    // 5. ACTIONS - Métodos que responden a eventos
    greet() {
        this.outputTarget.textContent = `Hello ${this.nameTarget.value}!`
    }
}
```

### 🎨 **Conexión HTML:**
```html
<div data-controller="hello" 
     data-hello-url-value="/api/greet"
     data-hello-count-value="5">
     
    <input data-hello-target="name" 
           data-action="keyup->hello#greet">
    
    <span data-hello-target="output"></span>
</div>
```

---

## 🎯 **Conceptos Clave de Stimulus**

### 1. **🎮 Controllers (Controladores)**

**Definición:** Clases JavaScript que agregan comportamiento a elementos HTML.

**Ejemplo básico:**
```javascript
// assets/controllers/counter_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static values = { count: Number }
    
    connect() {
        console.log("Counter connected!")
        this.countValue = this.countValue || 0
    }
    
    increment() {
        this.countValue++
        this.updateDisplay()
    }
    
    decrement() {
        this.countValue--
        this.updateDisplay()
    }
    
    updateDisplay() {
        this.element.textContent = this.countValue
    }
    
    countValueChanged() {
        this.updateDisplay()
    }
}
```

**HTML correspondiente:**
```html
<div data-controller="counter" data-counter-count-value="0">
    <button data-action="click->counter#decrement">-</button>
    <span data-counter-target="display">0</span>
    <button data-action="click->counter#increment">+</button>
</div>
```

### 2. **🎯 Targets (Objetivos)**

**Definición:** Referencias a elementos HTML específicos dentro del controller.

**Ejemplo de targets múltiples:**
```javascript
// assets/controllers/form_controller.js
export default class extends Controller {
    static targets = ["form", "submit", "error", "success"]
    
    connect() {
        this.hideMessages()
    }
    
    async submitForm(event) {
        event.preventDefault()
        this.showLoading()
        
        try {
            const response = await fetch(this.formTarget.action, {
                method: 'POST',
                body: new FormData(this.formTarget)
            })
            
            if (response.ok) {
                this.showSuccess("¡Formulario enviado!")
            } else {
                this.showError("Error al enviar")
            }
        } catch (error) {
            this.showError("Error de conexión")
        }
    }
    
    showLoading() {
        this.submitTarget.disabled = true
        this.submitTarget.textContent = "Enviando..."
    }
    
    showSuccess(message) {
        this.hideMessages()
        this.successTarget.textContent = message
        this.successTarget.classList.remove("hidden")
    }
    
    showError(message) {
        this.hideMessages()
        this.errorTarget.textContent = message
        this.errorTarget.classList.remove("hidden")
    }
    
    hideMessages() {
        this.errorTarget.classList.add("hidden")
        this.successTarget.classList.add("hidden")
    }
}
```

**HTML:**
```html
<div data-controller="form">
    <form data-form-target="form" data-action="submit->form#submitForm">
        <input type="text" name="name" placeholder="Nombre">
        <input type="email" name="email" placeholder="Email">
        
        <button type="submit" data-form-target="submit">Enviar</button>
    </form>
    
    <div data-form-target="error" class="alert alert-danger hidden"></div>
    <div data-form-target="success" class="alert alert-success hidden"></div>
</div>
```

### 3. **💾 Values (Valores)**

**Definición:** Datos que se pasan desde HTML al JavaScript controller.

**Ejemplo con diferentes tipos de values:**
```javascript
// assets/controllers/api_controller.js
export default class extends Controller {
    static values = { 
        url: String,           // URL del API
        method: String,        // GET, POST, etc.
        autoLoad: Boolean,     // Cargar automáticamente
        interval: Number,      // Intervalo de actualización
        headers: Object        // Headers personalizados
    }
    
    connect() {
        console.log("API Controller connected")
        console.log("URL:", this.urlValue)
        console.log("Method:", this.methodValue)
        console.log("Auto load:", this.autoLoadValue)
        
        if (this.autoLoadValue) {
            this.loadData()
        }
        
        if (this.intervalValue > 0) {
            this.startPolling()
        }
    }
    
    async loadData() {
        try {
            const response = await fetch(this.urlValue, {
                method: this.methodValue,
                headers: {
                    'Content-Type': 'application/json',
                    ...this.headersValue
                }
            })
            
            const data = await response.json()
            this.displayData(data)
        } catch (error) {
            console.error("Error loading data:", error)
        }
    }
    
    startPolling() {
        setInterval(() => {
            this.loadData()
        }, this.intervalValue * 1000)
    }
    
    // Callbacks cuando cambian los values
    urlValueChanged(newUrl, oldUrl) {
        console.log(`URL changed from ${oldUrl} to ${newUrl}`)
        if (this.autoLoadValue) {
            this.loadData()
        }
    }
    
    intervalValueChanged(newInterval) {
        if (newInterval > 0) {
            this.startPolling()
        }
    }
}
```

**HTML:**
```html
<div data-controller="api"
     data-api-url-value="/api/patients"
     data-api-method-value="GET"
     data-api-auto-load-value="true"
     data-api-interval-value="30"
     data-api-headers-value='{"Authorization": "Bearer token123"}'>
     
    <div data-api-target="content">Cargando...</div>
    <button data-action="click->api#loadData">Actualizar</button>
</div>
```

### 4. **🎨 Classes (Clases CSS)**

**Definición:** Clases CSS configurables que permiten personalizar la apariencia.

**Ejemplo con classes dinámicas:**
```javascript
// assets/controllers/modal_controller.js
export default class extends Controller {
    static classes = ["open", "closed", "backdrop", "loading"]
    static targets = ["dialog", "backdrop"]
    
    open() {
        // Mostrar backdrop
        this.backdropTarget.classList.add(this.backdropClass)
        
        // Abrir modal
        this.dialogTarget.classList.remove(this.closedClass)
        this.dialogTarget.classList.add(this.openClass)
        
        // Prevenir scroll del body
        document.body.style.overflow = 'hidden'
    }
    
    close() {
        this.dialogTarget.classList.remove(this.openClass)
        this.dialogTarget.classList.add(this.closedClass)
        
        setTimeout(() => {
            this.backdropTarget.classList.remove(this.backdropClass)
            document.body.style.overflow = ''
        }, 300)
    }
    
    showLoading() {
        this.dialogTarget.classList.add(this.loadingClass)
    }
    
    hideLoading() {
        this.dialogTarget.classList.remove(this.loadingClass)
    }
}
```

**HTML:**
```html
<div data-controller="modal"
     data-modal-open-class="modal-open"
     data-modal-closed-class="modal-closed"
     data-modal-backdrop-class="modal-backdrop"
     data-modal-loading-class="modal-loading">
     
    <button data-action="click->modal#open">Abrir Modal</button>
    
    <div data-modal-target="backdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden">
        <div data-modal-target="dialog" class="modal-closed">
            <h2>Mi Modal</h2>
            <p>Contenido del modal</p>
            <button data-action="click->modal#close">Cerrar</button>
        </div>
    </div>
</div>
```

### 5. **⚡ Actions (Acciones)**

**Definición:** Conexiones entre eventos DOM y métodos del controller.

**Sintaxis de actions:**
```html
<!-- Evento -> Controller#Método -->
<button data-action="click->counter#increment">+</button>

<!-- Múltiples actions -->
<input data-action="keyup->search#filter input->search#filter">

<!-- Action con parámetros -->
<button data-action="click->modal#open" data-modal-size-param="large">Abrir</button>

<!-- Action con modificadores -->
<form data-action="submit->form#save:prevent">
<input data-action="keydown.enter->search#submit">
<input data-action="keydown.esc->modal#close">
```

**Ejemplo completo de actions:**
```javascript
// assets/controllers/search_controller.js
export default class extends Controller {
    static targets = ["input", "results", "count"]
    static values = { minLength: Number }
    
    // Action básica
    search() {
        const query = this.inputTarget.value
        if (query.length >= this.minLengthValue) {
            this.performSearch(query)
        } else {
            this.clearResults()
        }
    }
    
    // Action con parámetros
    selectResult(event) {
        const resultId = event.params.id
        const resultText = event.target.textContent
        
        console.log(`Selected: ${resultText} (ID: ${resultId})`)
        this.inputTarget.value = resultText
        this.clearResults()
    }
    
    // Action para limpiar
    clear() {
        this.inputTarget.value = ""
        this.clearResults()
    }
    
    async performSearch(query) {
        try {
            const response = await fetch(`/search?q=${encodeURIComponent(query)}`)
            const results = await response.json()
            
            this.displayResults(results)
        } catch (error) {
            console.error("Search error:", error)
        }
    }
    
    displayResults(results) {
        this.resultsTarget.innerHTML = results.map(result => 
            `<div data-action="click->search#selectResult" 
                  data-search-id-param="${result.id}">
                ${result.name}
             </div>`
        ).join('')
        
        this.countTarget.textContent = `${results.length} resultados`
    }
    
    clearResults() {
        this.resultsTarget.innerHTML = ""
        this.countTarget.textContent = ""
    }
}
```

**HTML:**
```html
<div data-controller="search" data-search-min-length-value="2">
    <div class="search-box">
        <input data-search-target="input" 
               data-action="keyup->search#search keydown.esc->search#clear"
               placeholder="Buscar...">
        <button data-action="click->search#clear">✕</button>
    </div>
    
    <div data-search-target="count" class="search-count"></div>
    <div data-search-target="results" class="search-results"></div>
</div>
```

---

## 🎯 **Ejemplos Prácticos Aplicados en Melisa**

### 1. **🏥 Controller Base Médico**
```javascript
// assets/controllers/patient_controller.js
export default class extends Controller {
    static targets = ["name", "cedula", "phone", "info"]
    static values = { patientId: String }
    static classes = ["loading", "error", "success"]
    
    connect() {
        console.log("Patient controller connected")
        if (this.patientIdValue) {
            this.loadPatient()
        }
    }
    
    async loadPatient() {
        this.showLoading()
        
        try {
            const response = await fetch(`/patients/${this.patientIdValue}`)
            const patient = await response.json()
            
            this.displayPatient(patient)
            this.showSuccess()
        } catch (error) {
            this.showError("Error cargando paciente")
        }
    }
    
    displayPatient(patient) {
        this.nameTarget.textContent = patient.name
        this.cedulaTarget.textContent = patient.cedula
        this.phoneTarget.textContent = patient.phone
    }
    
    showLoading() {
        this.element.classList.add(this.loadingClass)
    }
    
    showSuccess() {
        this.element.classList.remove(this.loadingClass, this.errorClass)
        this.element.classList.add(this.successClass)
    }
    
    showError(message) {
        this.element.classList.remove(this.loadingClass, this.successClass)
        this.element.classList.add(this.errorClass)
        console.error(message)
    }
}
```

### 2. **🏢 Controller Multi-tenant**
```javascript
// assets/controllers/tenant_controller.js
export default class extends Controller {
    static values = { 
        tenant: String,
        theme: String,
        features: Array
    }
    
    connect() {
        this.applyTenantTheme()
        this.enableFeatures()
    }
    
    applyTenantTheme() {
        document.body.className = `tenant-${this.tenantValue}`
        
        if (this.themeValue) {
            document.documentElement.style.setProperty('--primary-color', this.themeValue)
        }
    }
    
    enableFeatures() {
        this.featuresValue.forEach(feature => {
            document.body.classList.add(`feature-${feature}`)
        })
    }
    
    tenantValueChanged(newTenant, oldTenant) {
        if (oldTenant) {
            document.body.classList.remove(`tenant-${oldTenant}`)
        }
        document.body.classList.add(`tenant-${newTenant}`)
    }
}
```

### 3. **📱 Controller Responsive**
```javascript
// assets/controllers/responsive_controller.js
export default class extends Controller {
    static targets = ["mobile", "desktop"]
    static classes = ["hidden"]
    
    connect() {
        this.updateLayout()
        window.addEventListener('resize', this.updateLayout.bind(this))
    }
    
    disconnect() {
        window.removeEventListener('resize', this.updateLayout.bind(this))
    }
    
    updateLayout() {
        const isMobile = window.innerWidth < 768
        
        if (isMobile) {
            this.mobileTarget.classList.remove(this.hiddenClass)
            this.desktopTarget.classList.add(this.hiddenClass)
        } else {
            this.mobileTarget.classList.add(this.hiddenClass)
            this.desktopTarget.classList.remove(this.hiddenClass)
        }
    }
}
```

---

## 🔧 **Patrones Avanzados**

### 1. **🎭 Herencia de Controllers**
```javascript
// Base controller
// assets/controllers/base_medical_controller.js
export default class extends Controller {
    static targets = ["loading", "error"]
    static classes = ["loading", "error", "success"]
    
    showLoading() {
        this.loadingTarget.classList.remove("hidden")
        this.element.classList.add(this.loadingClass)
    }
    
    hideLoading() {
        this.loadingTarget.classList.add("hidden")
        this.element.classList.remove(this.loadingClass)
    }
    
    showError(message) {
        this.hideLoading()
        this.errorTarget.textContent = message
        this.element.classList.add(this.errorClass)
    }
}

// Controller específico
// assets/controllers/specialized_patient_controller.js
import BaseMedicalController from "./base_medical_controller"

export default class extends BaseMedicalController {
    static targets = [...super.targets, "specialty", "insurance"]
    
    connect() {
        super.connect?.()
        this.detectSpecialty()
    }
    
    detectSpecialty() {
        // Lógica específica del controller especializado
    }
}
```

### 2. **📡 Communication entre Controllers**
```javascript
// Publisher controller
export default class extends Controller {
    notify(data) {
        this.dispatch("patient-updated", { 
            detail: data,
            bubbles: true 
        })
    }
}

// Subscriber controller  
export default class extends Controller {
    connect() {
        this.element.addEventListener("patient-updated", this.handlePatientUpdate.bind(this))
    }
    
    handlePatientUpdate(event) {
        console.log("Patient updated:", event.detail)
        this.refresh()
    }
}
```

---

## 🚀 **Best Practices**

### ✅ **DO (Hacer):**
- Usar nombres descriptivos para targets y actions
- Mantener controllers pequeños y enfocados
- Usar values para configuración
- Implementar cleanup en disconnect()
- Usar eventos personalizados para comunicación

### ❌ **DON'T (No hacer):**
- Manipular DOM fuera del controller
- Crear controllers gigantes con muchas responsabilidades  
- Hardcodear valores que deberían ser configurables
- Olvidar remover event listeners en disconnect()
- Acceder directamente a otros controllers

---

## 📚 **Recursos y Referencias**

### 🔗 **Enlaces útiles:**
- **Documentación oficial:** https://stimulus.hotwired.dev/
- **Handbook:** https://stimulus.hotwired.dev/handbook/introduction
- **Examples:** https://github.com/hotwired/stimulus/tree/main/examples

### 🎯 **Archivos del proyecto:**
- **Controllers:** `assets/controllers/`
- **Configuración:** `assets/controllers.json`
- **Import map:** `importmap.php`

---

*Documentación actualizada: Octubre 15, 2025*
*Framework: Stimulus 3.2.2 con Symfony 6.4*

---

# 📁 PARTE 2: ESTRUCTURA DE CONTROLADORES

# 🎮 Nueva Estructura de Controllers Stimulus

## 📁 **Estructura de Carpetas**

```
assets/controllers/
├── dynamic_loader.js                    # Sistema de carga dinámica
├── controllers.json                     # Configuración
│
├── internal/                           # Controllers internos (formularios, UI)
│   ├── default/
│   │   └── patient_controller.js       # Controller base interno
│   ├── melisahospital/
│   │   └── patient_controller.js       # Hospital específico
│   ├── melisalacolina/
│   │   └── patient_controller.js       # La Colina específico  
│   └── melisawiclinic/
│       └── patient_controller.js       # Wi Clinic específico
│
└── apiplatform/                        # Controllers API Platform
    ├── default/
    │   └── api_patient_controller.js   # Controller base API Platform
    ├── melisahospital/
    │   └── api_patient_controller.js   # Hospital API específico
    ├── melisalacolina/
    │   └── api_patient_controller.js   # La Colina API específico
    └── melisawiclinic/
        └── api_patient_controller.js   # Wi Clinic API específico
```

## 🎯 **Uso en Templates**

### **Controllers Internos (formularios, UI)**
```html
<!-- Busca: internal/[subdomain]/patient_controller.js → internal/default/patient_controller.js -->
<div data-controller="internal--patient">
    <input data-internal--patient-target="name">
    <button data-action="click->internal--patient#save">Guardar</button>
</div>
```

### **Controllers API Platform (APIs externas)**
```html
<!-- Busca: apiplatform/[subdomain]/api_patient_controller.js → apiplatform/default/api_patient_controller.js -->
<div data-controller="apiplatform--api-patient">
    <div data-apiplatform--api-patient-target="patientList"></div>
    <button data-action="click->apiplatform--api-patient#loadPatients">Cargar</button>
</div>
```

## ⚙️ **Sistema de Fallback**

### **Algoritmo de búsqueda:**
1. **Detectar subdomain:** `melisalacolina.localhost` → `melisalacolina`
2. **Buscar específico:** `internal/melisalacolina/patient_controller.js`
3. **Fallback a default:** Si no existe → `internal/default/patient_controller.js`
4. **Error si no existe:** Ni específico ni default encontrados

### **Ejemplos:**

| URL | Subdomain | Controller buscado | Fallback |
|-----|-----------|-------------------|----------|
| `melisalacolina.localhost` | `melisalacolina` | `internal/melisalacolina/patient_controller.js` | `internal/default/patient_controller.js` |
| `melisawiclinic.localhost` | `melisawiclinic` | `apiplatform/melisawiclinic/api_patient_controller.js` | `apiplatform/default/api_patient_controller.js` |
| `melisahospital.localhost` | `melisahospital` | `internal/melisahospital/patient_controller.js` | `internal/default/patient_controller.js` |

## 🔧 **Configuración Automática**

### **Dynamic Loader**
- Se carga automáticamente en `assets/app.js`
- Detecta subdomain actual
- Registra controllers encontrados en el DOM
- Maneja cache para rendimiento

### **Debug**
```javascript
// En consola del navegador:
console.log(DynamicControllerLoader.getDebugInfo())
// Output:
// {
//   subdomain: "melisalacolina",
//   loadedControllers: ["internal--patient", "apiplatform--api-patient"],
//   hostname: "melisalacolina.localhost"
// }
```

## 📝 **Convenciones de Nombres**

### **Archivos:**
- `patient_controller.js` (underscore)
- `api_patient_controller.js` (underscore)

### **Data Controllers:**
- `internal--patient` (double dash)
- `apiplatform--api-patient` (double dash)

### **Targets:**
- `data-internal--patient-target="name"`
- `data-apiplatform--api-patient-target="patientList"`

### **Actions:**
- `data-action="click->internal--patient#save"`
- `data-action="click->apiplatform--api-patient#loadPatients"`

## 🏥 **Ejemplos Específicos por Tenant**

### **La Colina (melisalacolina)**
```javascript
// internal/melisalacolina/patient_controller.js
import PatientController from "../default/patient_controller.js"

export default class extends PatientController {
    static targets = [...PatientController.targets, "specialty", "insurance"]
    
    connect() {
        super.connect()
        this.setupClinicTheme() // Verde La Colina
        this.populateSpecialties() // Especialidades médicas
    }
}
```

### **Wi Clinic (melisawiclinic)**
```javascript
// apiplatform/melisawiclinic/api_patient_controller.js  
import ApiPatientController from "../default/api_patient_controller.js"

export default class extends ApiPatientController {
    static targets = [...ApiPatientController.targets, "techDevices", "telemetry"]
    
    connect() {
        super.connect()
        this.enableTechFeatures() // IoT, AI, Blockchain
        this.startTelemetry() // Métricas en tiempo real
    }
}
```

## 🚀 **URLs de Examples**

| Funcionalidad | URL |
|---------------|-----|
| **Examples Index** | `/examples` |
| **API Platform Demo** | `/examples/api-platform` |
| **Internal Controllers Demo** | `/examples/internal-controllers` |
| **API Docs** | `/api/docs` |

## 📚 **Ventajas del Sistema**

### ✅ **Organización Clara**
- Separación entre lógica interna y API Platform
- Controllers específicos por tenant
- Fallback automático a default

### ✅ **Mantenibilidad**
- Herencia clara desde controllers base
- Código compartido en default
- Especialización por subdomain

### ✅ **Escalabilidad**
- Fácil agregar nuevos tenants
- Controllers se cargan dinámicamente
- Cache automático para rendimiento

### ✅ **Desarrollo**
- Convenciones claras de nombres
- Debug info integrado
- Hot reload en desarrollo

## 🔍 **Troubleshooting**

### **Controller no encontrado**
```
🎮 [Dynamic Loader] ❌ Controller no encontrado: internal--patient
```
**Solución:** Verificar que exista `internal/[subdomain]/patient_controller.js` o `internal/default/patient_controller.js`

### **Error de import**
```
🎮 [Dynamic Loader] ❌ Error en import: ./controllers/internal/melisalacolina/patient_controller.js
```
**Solución:** Verificar sintaxis del archivo y que herede correctamente del default

### **Subdomain mal detectado**
```javascript
// Forzar subdomain para testing:
DynamicControllerLoader.currentSubdomain = 'melisalacolina'
```

---

*Actualizado: Octubre 15, 2025*
*Sistema: Dynamic Controller Loading con Multi-tenant*

---

# 🔌 PARTE 3: INTEGRACIÓN CON API PLATFORM

# 🎯 Stimulus + API Platform Integration Guide

## 📋 Controladores Stimulus Creados

### 🏗️ **Estructura de Controladores**

```
assets/controllers/
├── patient_controller.js                    # ← Controlador original (legacy)
└── apiplatform/
    ├── patient_controller.js                # ← Controlador base API Platform
    └── tenants/
        ├── lacolina_patient_controller.js   # ← Especialización La Colina
        └── wiclinic_patient_controller.js   # ← Especialización Wi Clinic
```

### 🚀 **Controlador Base: apiplatform/patient_controller.js**

**Características principales:**
- ✅ Integración completa con API Platform
- ✅ Headers multi-tenant automáticos
- ✅ Cache local para optimización
- ✅ Búsqueda en tiempo real
- ✅ Manejo de errores robusto
- ✅ UI/UX responsiva

**Targets disponibles:**
```javascript
static targets = [
    // UI General
    "loading", "error", "searchResults", "patientList", "info",
    
    // Datos del Paciente
    "name", "cedula", "email", "phone", "address", "gender", "birthDate",
    
    // Información Médica
    "bloodType", "allergies", "medications", 
    
    // Contacto de Emergencia
    "emergencyContact", "emergencyPhone",
    
    // Metadatos
    "patientId", "tenant", "createdAt", "updatedAt"
]
```

**Values de configuración:**
```javascript
static values = { 
    patientId: String,          // ID del paciente
    apiUrl: String,             // URL base: "/api/patients"
    tenant: String,             // Tenant actual
    autoLoad: Boolean,          // Auto-cargar al conectar
    cacheEnabled: Boolean,      // Habilitar cache local
    debugMode: Boolean          // Modo debug
}
```

---

## 🏥 **Controladores Especializados**

### 1. 🏥 **La Colina Clinic (lacolina_patient_controller.js)**

**Extensiones específicas:**
- ✅ Badges de especialidades médicas
- ✅ Información de seguros simulada
- ✅ Filtros por especialidad en búsqueda
- ✅ Estilos visuales personalizados

**Targets adicionales:**
```javascript
"specialty", "referringDoctor", "insuranceProvider",
"appointmentHistory", "treatmentPlan"
```

**Especialidades detectadas:**
- Cardiología
- Neurología 
- Ginecología
- Dermatología
- Traumatología

### 2. 💻 **Wi Clinic Tech (wiclinic_patient_controller.js)**

**Extensiones tecnológicas:**
- ✅ Indicadores de dispositivos IoT
- ✅ Telemetría en tiempo real
- ✅ Hash blockchain simulado
- ✅ Diagnósticos de IA
- ✅ Métricas de wearables

**Targets adicionales:**
```javascript
"techDevices", "telemetryData", "aiDiagnosis",
"vrSessions", "blockchainHash", "iotMetrics"
```

**Características tech detectadas:**
- 📡 Telemedicina
- ⌚ Wearables 
- 🌐 IoT
- 🤖 IA
- 🥽 VR
- 🔗 Blockchain

---

## 🎨 **Uso en Templates**

### 📄 **Template Base (/dashboard/patients-api)**

```twig
<div data-controller="apiplatform--patient"
     data-apiplatform--patient-api-url-value="/api/patients"
     data-apiplatform--patient-tenant-value="{{ tenant_info.subdomain }}"
     data-apiplatform--patient-auto-load-value="true"
     data-apiplatform--patient-cache-enabled-value="true"
     data-apiplatform--patient-debug-mode-value="true">

    <!-- Lista de pacientes -->
    <div data-apiplatform--patient-target="patientList"></div>
    
    <!-- Información del paciente -->
    <div data-apiplatform--patient-target="info">
        <h2 data-apiplatform--patient-target="name"></h2>
        <p data-apiplatform--patient-target="email"></p>
        <!-- Más campos... -->
    </div>
    
    <!-- Búsqueda -->
    <input data-action="input->apiplatform--patient#searchPatients">
    
    <!-- Botones -->
    <button data-action="click->apiplatform--patient#refresh">Actualizar</button>
</div>
```

### 🏥 **Para usar controlador específico de La Colina:**

```twig
<!-- Cambiar el data-controller -->
<div data-controller="apiplatform--tenants--lacolina-patient">
    <!-- Targets adicionales -->
    <span data-apiplatform--tenants--lacolina-patient-target="specialty"></span>
    <span data-apiplatform--tenants--lacolina-patient-target="insuranceProvider"></span>
</div>
```

### 💻 **Para usar controlador específico de Wi Clinic:**

```twig
<!-- Cambiar el data-controller -->
<div data-controller="apiplatform--tenants--wiclinic-patient">
    <!-- Targets tecnológicos -->
    <div data-apiplatform--tenants--wiclinic-patient-target="telemetryData"></div>
    <span data-apiplatform--tenants--wiclinic-patient-target="blockchainHash"></span>
</div>
```

---

## 🚀 **Métodos Principales**

### 📋 **Controlador Base**

```javascript
// Cargar lista completa
await controller.loadPatientsList()

// Cargar paciente específico
await controller.loadPatientInfo('HSP001')

// Búsqueda
await controller.searchPatients(event)

// Seleccionar de lista
controller.selectPatient(event)

// Actualizar datos
await controller.refresh()

// Limpiar
controller.clearInfo()
```

### 🏥 **La Colina - Métodos Específicos**

```javascript
// Crear badge de especialidad
controller.createSpecialtyBadge(patient)

// Extraer especialidades
controller.extractSpecialtyFromName("Paciente Cardiología")

// Información de seguros
controller.generateInsuranceInfo(patient)
```

### 💻 **Wi Clinic - Métodos Tecnológicos**

```javascript
// Detectar características tech
controller.detectTechFeatures(patient)

// Generar datos de telemetría
controller.generateTelemetryData()

// Hash blockchain
controller.generateBlockchainHash(patientId)

// Diagnóstico IA
controller.generateAIDiagnosis(patient)
```

---

## 🔧 **Configuración y Personalización**

### 🎨 **Estilos CSS Automáticos**

**La Colina:**
```css
.lacolina-patient-card {
    border-left: 4px solid #059669;
}
.lacolina-specialty-badge {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
}
```

**Wi Clinic:**
```css
.wiclinic-patient-card {
    border-left: 4px solid #7c3aed;
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}
.tech-indicator {
    animation: pulse 2s infinite;
}
```

### ⚙️ **Variables CSS Dinámicas**

Los controladores especializados configuran automáticamente:
```css
:root {
    --clinic-primary: #059669;    /* La Colina */
    --tech-primary: #7c3aed;      /* Wi Clinic */
}
```

---

## 🧪 **Testing y Debug**

### 🔍 **Debug Mode**

Habilitar con `data-apiplatform--patient-debug-mode-value="true"`:

```javascript
// Logs automáticos
🏥 [API Platform Patient] Controlador conectado
🏥 [La Colina] Controlador especializado conectado  
💻 [Wi Clinic] Tech Controller conectado
```

### 🧪 **Comandos de Testing**

```bash
# Limpiar caché
php bin/console cache:clear

# Verificar rutas
php bin/console debug:router | grep api

# Probar endpoint
curl -H "X-Tenant-Context: melisahospital" \
     "http://melisahospital.localhost:8081/api/patients"
```

### 🔧 **Atajos de Teclado**

- `Ctrl + R` - Refresh de datos
- Click en card - Seleccionar paciente
- Typing en search - Búsqueda automática

---

## 🎯 **URLs de Acceso**

| Tenant | URL | Controlador |
|--------|-----|-------------|
| Hospital | http://melisahospital.localhost:8081/dashboard/patients-api | Base |
| La Colina | http://melisalacolina.localhost:8081/dashboard/patients-api | Especializado |
| Wi Clinic | http://melisawiclinic.localhost:8081/dashboard/patients-api | Tech |

---

## ✅ **Checklist de Implementación**

- [x] Controlador base API Platform
- [x] Especializaciones por tenant
- [x] Template responsive
- [x] Rutas configuradas
- [x] Cache optimizado
- [x] Búsqueda en tiempo real
- [x] Manejo de errores
- [x] Debug logging
- [x] Estilos personalizados
- [x] Telemetría simulada (Wi Clinic)

---

*Documentación actualizada: Octubre 15, 2025*
*Versión: Stimulus 3.2.2 + API Platform 4.2*