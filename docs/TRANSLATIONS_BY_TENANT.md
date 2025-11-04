# 🌍 Sistema de Traducciones por Tenant

## Arquitectura Multi-Tenant con Dominios de Traducción Separados

Este sistema permite que **cada tenant tenga su propia terminología médica** completamente personalizada, usando dominios de traducción separados de Symfony.

---

## 📂 Estructura de Archivos

```
translations/
├── messages.es.yaml                           ← Traducciones base (fallback global)
├── messages.en.yaml
├── default/
│   ├── default.es.yaml                       ← Tenant: default (genérico)
│   └── default.en.yaml
├── melisahospital/
│   ├── melisahospital.es.yaml                ← Tenant: Hospital (urgencias/turnos)
│   └── melisahospital.en.yaml
└── melisalacolina/
    ├── melisalacolina.es.yaml                ← Tenant: Clínica (privado/consultas)
    └── melisalacolina.en.yaml
```

---

## 🎯 Ejemplo: Misma Clave, Diferentes Significados

### `auth.logout`

| Tenant | Traducción | Contexto |
|--------|-----------|----------|
| **default** | "Salir del Sistema" | Término genérico |
| **melisahospital** | "Finalizar Turno Médico" | Enfoque en turnos de guardia |
| **melisalacolina** | "Cerrar Mi Sesión" | Personalizado y amigable |

### `nav.dashboard`

| Tenant | Traducción | Contexto |
|--------|-----------|----------|
| **default** | "Panel Principal" | Neutro |
| **melisahospital** | "Puesto de Mando Central" | Terminología militar/urgencias |
| **melisalacolina** | "Mi Escritorio Digital" | Personal y moderno |

### `dashboard.title`

| Tenant | Traducción | Contexto |
|--------|-----------|----------|
| **default** | "Panel de Control" | Estándar |
| **melisahospital** | "Centro de Comando Hospitalario" | Alta complejidad |
| **melisalacolina** | "Mi Espacio de Trabajo" | Práctica privada |

### `patients.title`

| Tenant | Traducción | Contexto |
|--------|-----------|----------|
| **default** | "Gestión de Pacientes" | Genérico |
| **melisahospital** | "Gestión de Pacientes Internados" | Solo hospitalizados |
| **melisalacolina** | "Mis Pacientes Privados" | Cartera personal |

---

## 🔄 Flujo de Traducción

```
1. Request llega → melisahospital.melisaupgrade.prod/dashboard
    ↓
2. TenantTranslationListener (Priority 25)
   - Detecta tenant: melisahospital
   - Establece atributos en request
    ↓
3. LocaleListener (Priority 20)
   - Detecta locale: 'es'
    ↓
4. Controller ejecuta
    ↓
5. Template usa: {{ 'auth.logout'|trans }}
    ↓
6. LocalizationService::trans()
   - Obtiene tenant domain: 'melisahospital'
   - Llama a TranslatorInterface con dominio 'melisahospital'
    ↓
7. TranslatorInterface busca en:
   - translations/melisahospital/melisahospital.es.yaml
   - Encuentra: auth.logout: 'Finalizar Turno Médico'
    ↓
8. Si NO encuentra en tenant, hace fallback a:
   - translations/messages.es.yaml
    ↓
9. Retorna: "Finalizar Turno Médico"
```

---

## 💻 Uso en Código

### En Controllers

```php
class MiController extends AbstractTenantAwareController
{
    public function __construct(
        private LocalizationService $localizationService
    ) {}
    
    public function index(): Response
    {
        // ✅ Automáticamente usa dominio del tenant
        $message = $this->localizationService->trans('auth.logout');
        
        // Hospital:  "Finalizar Turno Médico"
        // Clínica:   "Cerrar Mi Sesión"
        // Default:   "Salir del Sistema"
        
        return $this->render('template.html.twig');
    }
}
```

### En Templates Twig

```twig
{# Traducción automática por tenant #}
<button>{{ 'auth.logout'|trans }}</button>

{# melisahospital → "Finalizar Turno Médico" #}
{# melisalacolina → "Cerrar Mi Sesión" #}
{# default       → "Salir del Sistema" #}
```

### Con TranslatorInterface Directo

```php
// Usar LocalizationService (RECOMENDADO)
$translation = $this->localizationService->trans('auth.logout');
// ✅ Automáticamente usa dominio correcto del tenant

// Usar TranslatorInterface directo (NO recomendado)
$translation = $this->translator->trans('auth.logout', [], 'messages', 'es');
// ❌ Siempre usa dominio 'messages', ignora tenant
```

---

## 🏥 Terminología por Tenant

### melisahospital (Hospital de Alta Complejidad)

**Enfoque:** Urgencias, emergencias, turnos médicos, código de colores

```yaml
# Terminología específica
auth:
  logout: 'Finalizar Turno Médico'  # No "cerrar sesión"
  username: 'Credencial Médica'     # No "usuario"

nav:
  dashboard: 'Puesto de Mando Central'  # Estilo comando
  patients: 'Pacientes Hospitalizados'  # Solo internados
  appointments: 'Guardias y Turnos'     # Sistema de guardias

emergency:
  critical: 'CÓDIGO ROJO - CRÍTICO'
  urgent: 'CÓDIGO AMARILLO - URGENTE'
```

### melisalacolina (Clínica Privada de Especialidades)

**Enfoque:** Atención personalizada, práctica privada, relación cercana

```yaml
# Terminología específica
auth:
  logout: 'Cerrar Mi Sesión'  # Personal
  username: 'Nombre de Usuario'  # Simple

nav:
  dashboard: 'Mi Escritorio Digital'  # Personal
  patients: 'Mis Pacientes'  # Posesivo
  appointments: 'Agenda de Consultas'  # Agenda personal

consultation:
  first_time: 'Primera Consulta'
  executive: 'Consulta Ejecutiva'
  telemedicine: 'Telemedicina'
```

### default (Genérico)

**Enfoque:** Términos neutrales y estándar

```yaml
# Terminología genérica
auth:
  logout: 'Salir del Sistema'
  username: 'Usuario'

nav:
  dashboard: 'Panel Principal'
  patients: 'Pacientes'
  appointments: 'Consultas Agendadas'
```

---

## 🎨 Beneficios del Sistema

### ✅ Personalización Total
Cada establecimiento puede tener su propia "voz" y terminología específica

### ✅ Fallback Automático
Si una traducción no existe en el tenant, usa la versión global

### ✅ Multi-idioma por Tenant
Cada tenant puede tener español E inglés con terminología específica

### ✅ Sin Código Hardcodeado
Todo configurable en archivos YAML

### ✅ Caché Automático
Symfony cachea las traducciones por dominio (performance)

### ✅ Fácil Mantenimiento
Agregar nuevo tenant = crear carpeta + 2 archivos YAML

---

## 🛠️ Cómo Agregar un Nuevo Tenant

### 1. Crear carpeta

```bash
mkdir translations/nuevo_tenant
```

### 2. Crear archivos de traducción

**translations/nuevo_tenant/nuevo_tenant.es.yaml**
```yaml
auth:
  login: 'Traducción específica del nuevo tenant'
  logout: 'Otra traducción específica'

nav:
  dashboard: 'Mi Panel Personalizado'
  # ... etc
```

**translations/nuevo_tenant/nuevo_tenant.en.yaml**
```yaml
auth:
  login: 'Tenant-specific translation'
  logout: 'Another specific translation'
```

### 3. Agregar path en translation.yaml

```yaml
# config/packages/translation.yaml
framework:
    translator:
        paths:
            - '%kernel.project_dir%/translations/nuevo_tenant'
```

### 4. Limpiar caché

```bash
php bin/console cache:clear
```

### 5. ¡Listo! El sistema automáticamente usa las traducciones del nuevo tenant

---

## 🔍 Debugging

### Ver traducciones de un tenant específico

```bash
# Hospital
php bin/console debug:translation es --domain=melisahospital

# Clínica
php bin/console debug:translation es --domain=melisalacolina

# Default
php bin/console debug:translation es --domain=default
```

### Ver todas las traducciones disponibles

```bash
php bin/console debug:translation es
```

### Probar traducciones en navegador

Visita: `https://tenant.melisaupgrade.prod/test/translations`

---

## 📊 Estadísticas

| Tenant | Claves ES | Claves EN | Términos Específicos |
|--------|-----------|-----------|---------------------|
| **default** | ~20 | ~20 | Genérico |
| **melisahospital** | ~40 | ~40 | Urgencias, guardias, códigos |
| **melisalacolina** | ~45 | ~45 | Consultas, privado, wellness |
| **messages (global)** | ~200+ | ~200+ | Fallback |

---

## 🚀 Performance

- **Primera carga:** Lee YAML, parsea, cachea (~5ms)
- **Cargas posteriores:** Lee desde caché compilado (<1ms)
- **Fallback:** Solo busca si no encuentra en tenant (~2ms adicional)

---

**Desarrollado para Melisa Tenant Multi-Platform** 🏥 🌍
