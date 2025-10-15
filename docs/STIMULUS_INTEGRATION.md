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