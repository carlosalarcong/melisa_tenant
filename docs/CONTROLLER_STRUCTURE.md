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