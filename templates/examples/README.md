# 📚 Examples - Stimulus Controllers

Esta carpeta contiene **ejemplos interactivos** del sistema de controllers Stimulus multi-tenant implementado en el proyecto Melisa.

## 📂 **Archivos en esta carpeta:**

| Archivo | Descripción | URL |
|---------|-------------|-----|
| `index.html.twig` | Página principal de ejemplos | `/examples` |
| `api_platform_demo.html.twig` | Demo de integración API Platform | `/examples/api-platform` |
| `internal_controllers_demo.html.twig` | Demo de controllers internos | `/examples/internal-controllers` |

## 🎯 **Propósito:**

### ✅ **Ejemplos educativos**
- Mostrar cómo usar la nueva estructura de controllers
- Demonstrar el sistema de fallback por subdomain
- Ejemplos prácticos para el equipo de desarrollo

### ✅ **Testing interactivo**
- Probar funcionalidades en diferentes tenants
- Verificar que el sistema de fallback funciona
- Debug visual de controllers cargados

### ✅ **Documentación viva**
- Complementa la documentación estática
- Muestra código real funcionando
- Permite experimentar con diferentes configuraciones

## 🏗️ **Arquitectura mostrada:**

### **Controllers API Platform:**
```html
<!-- Usa: apiplatform--api-patient -->
<div data-controller="apiplatform--api-patient">
```

**Fallback:**
1. `apiplatform/melisalacolina/api_patient_controller.js`
2. `apiplatform/default/api_patient_controller.js`

### **Controllers Internos:**
```html
<!-- Usa: internal--patient -->
<div data-controller="internal--patient">
```

**Fallback:**
1. `internal/melisalacolina/patient_controller.js`
2. `internal/default/patient_controller.js`

## 🚀 **Cómo usar:**

1. **Visitar:** `http://[subdomain].localhost:8081/examples`
2. **Elegir demo:** API Platform o Internal Controllers
3. **Probar funcionalidades:** En diferentes subdomains
4. **Ver console:** Para debug info del Dynamic Loader

## 🔍 **Testing multi-tenant:**

| URL | Subdomain | Controller específico cargado |
|-----|-----------|-------------------------------|
| `melisahospital.localhost:8081/examples` | `melisahospital` | Default (no específico) |
| `melisalacolina.localhost:8081/examples` | `melisalacolina` | La Colina especializado |
| `melisawiclinic.localhost:8081/examples` | `melisawiclinic` | Wi Clinic especializado |

## 📝 **Nota importante:**

**Estos son EJEMPLOS, no funcionalidad de producción.**

- Para desarrollo y aprendizaje del equipo
- Pueden modificarse sin afectar el sistema real
- Útiles para prototipar nuevas funcionalidades
- Sirven como referencia de implementación

---

*Ejemplos actualizados: Octubre 15, 2025*
*Sistema: Dynamic Controller Loading Multi-tenant*