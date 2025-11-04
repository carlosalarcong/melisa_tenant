# 🌍 Sistema de Localización y Traducciones - Comparativa

## 📊 melisa_base vs melisa_tenant

---

## 1️⃣ melisa_base (Sistema Simple)

### Arquitectura Básica

```
melisa_base/
├── src/EventSubscriber/
│   └── RedirectToPreferredLocaleSubscriber.php  ← Redirección automática
├── translations/
│   ├── messages+intl-icu.en.yaml                ← Traducciones inglés
│   └── messages+intl-icu.es.yaml                ← Traducciones español
└── config/packages/
    └── translation.yaml                          ← Configuración Symfony
```

### Características

| Característica | Implementación |
|----------------|----------------|
| **Idiomas soportados** | Español, Inglés (configurados en services.yaml) |
| **Formato de archivos** | ICU Message Format (`messages+intl-icu.{locale}.yaml`) |
| **Detección automática** | Por navegador (header Accept-Language) |
| **Redirección** | Automática en homepage según preferencias del navegador |
| **Scope** | Global - mismo idioma para toda la aplicación |
| **Persistencia** | No tiene - se detecta cada vez |
| **Multi-tenant** | ❌ No aplica |

### RedirectToPreferredLocaleSubscriber

```php
class RedirectToPreferredLocaleSubscriber implements EventSubscriberInterface
{
    private array $locales;              // ['es', 'en', 'fr', ...]
    private string $defaultLocale;       // 'es'
    
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        string $locales,                 // 'es|en|fr|de'
        string $defaultLocale = null
    ) {
        $this->locales = explode('|', trim($locales));
        $this->defaultLocale = $defaultLocale ?: $this->locales[0];
    }
    
    public function onKernelRequest(RequestEvent $event): void
    {
        // SOLO ejecuta en homepage '/'
        if ('/' !== $request->getPathInfo()) {
            return;
        }
        
        // No redirigir si viene de un referrer interno
        $referrer = $request->headers->get('referer');
        if (/* es interno */) {
            return;
        }
        
        // Obtener idioma preferido del navegador
        $preferredLanguage = $request->getPreferredLanguage($this->locales);
        
        // Redirigir si no es el idioma por defecto
        if ($preferredLanguage !== $this->defaultLocale) {
            $response = new RedirectResponse(
                $this->urlGenerator->generate('homepage', [
                    '_locale' => $preferredLanguage
                ])
            );
            $event->setResponse($response);
        }
    }
}
```

### Configuración

**config/packages/translation.yaml**
```yaml
framework:
    default_locale: '%locale%'
    translator:
        default_path: '%kernel.project_dir%/translations'
        fallbacks:
            - '%locale%'
```

**config/services.yaml**
```yaml
parameters:
    locale: 'es'
    app_locales: ar|en|fr|de|es|cs|nl|ru|uk|ro|pt_BR|pl|it|ja|id|ca|sl|hr|zh_CN|bg|tr|lt|bs|sr_Cyrl|sr_Latn|eu

services:
    _defaults:
        bind:
            string $locales: '%app_locales%'
            string $defaultLocale: '%locale%'
```

### Uso en Templates

```twig
{# melisa_base/templates/login/index.html.twig #}

<h3>{{ 'title.login'|trans }}</h3>
<label>{{ 'label.username'|trans }}</label>
<input name="_username" />
<label>{{ 'label.password'|trans }}</label>
<input name="_password" />
<button>{{ 'action.sign_in'|trans }}</button>
```

### Archivos de Traducción

**translations/messages+intl-icu.es.yaml**
```yaml
title:
  login: Acceso seguro
  welcome: Bienvenido
label:
  username: Nombre de usuario
  password: Contraseña
  remember_me: Mantenme conectado
action:
  sign_in: Iniciar sesión
```

**translations/messages+intl-icu.en.yaml**
```yaml
title:
  login: Secure Sign in
  welcome: Welcome
label:
  username: Username
  password: Password
  remember_me: Keep me logged in
action:
  sign_in: Sign in
```

---

## 2️⃣ melisa_tenant (Sistema Avanzado Multi-Tenant)

### Arquitectura Completa

```
melisa_tenant/
├── src/
│   ├── Service/
│   │   └── LocalizationService.php               ← ⭐ Servicio centralizado
│   ├── EventListener/
│   │   └── LocaleListener.php                    ← ⭐ Listener customizado
│   ├── Twig/
│   │   └── LocalizationExtension.php             ← Extensiones Twig
│   └── Controller/
│       └── LocaleController.php                  ← Cambio manual de idioma
├── translations/
│   ├── messages.es.yaml                          ← Traducciones español (10k líneas)
│   └── messages.en.yaml                          ← Traducciones inglés (9k líneas)
├── config/packages/
│   └── translation.yaml                          ← Configuración Symfony
└── vendor/symfony/translation/                   ← ⭐ TranslatorInterface
    └── Translator.php                            ← Motor de traducciones
```

### Características Avanzadas

| Característica | Implementación |
|----------------|----------------|
| **Idiomas soportados** | Español, Inglés (extensible) |
| **Formato de archivos** | YAML estándar (`messages.{locale}.yaml`) |
| **Detección automática** | Multi-nivel (sesión → tenant → navegador → default) |
| **Persistencia** | Sesión del usuario |
| **Scope** | Por usuario + por tenant |
| **Multi-tenant** | ✅ Traducciones específicas por tenant |
| **API Support** | ✅ Stateless para API (header Accept-Language) |
| **Cambio manual** | ✅ LocaleController |

### 🎯 Componentes Clave del Sistema

#### 1. **TranslatorInterface** (Symfony Core)
Motor principal de traducciones proporcionado por Symfony.

```php
namespace Symfony\Contracts\Translation;

interface TranslatorInterface
{
    /**
     * Traduce un mensaje
     * 
     * @param string $id         Clave de traducción (ej: 'auth.login')
     * @param array $parameters  Parámetros para reemplazar (ej: ['%name%' => 'Juan'])
     * @param string $domain     Dominio de traducción (ej: 'messages', 'validators')
     * @param string $locale     Idioma específico (ej: 'es', 'en')
     */
    public function trans(
        string $id, 
        array $parameters = [], 
        string $domain = null, 
        string $locale = null
    ): string;
}
```

**Características:**
- ✅ Lee archivos YAML/PHP/JSON de `translations/`
- ✅ Caché automático de traducciones
- ✅ Fallback a idioma por defecto si no encuentra clave
- ✅ Soporta parámetros con `%variable%`
- ✅ Pluralización automática
- ✅ Dominios de traducción (`messages`, `validators`, etc.)

---

#### 2. **LocalizationService** (Servicio Customizado Multi-Tenant)
Capa de abstracción que encapsula TranslatorInterface + lógica multi-tenant.

```php
class LocalizationService
{
    private TranslatorInterface $translator;  // ← Inyección del Translator de Symfony
    private RequestStack $requestStack;
    private TenantContext $tenantContext;
    
    private array $supportedLocales = ['es', 'en'];
    private string $defaultLocale = 'es';
    
    /**
     * Obtiene el idioma actual con lógica multi-nivel
     */
    public function getCurrentLocale(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        
        // Para API → usar header Accept-Language (stateless)
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            return $request->getPreferredLanguage($this->supportedLocales) 
                ?? $this->defaultLocale;
        }
        
        // PRIORIDAD 1: Sesión del usuario
        if ($request->hasSession()) {
            $session = $request->getSession();
            if ($session->has('_locale')) {
                $locale = $session->get('_locale');
                if (in_array($locale, $this->supportedLocales)) {
                    return $locale;
                }
            }
        }
        
        // PRIORIDAD 2: Configuración del tenant
        if ($this->tenantContext->hasCurrentTenant()) {
            $tenant = $this->tenantContext->getCurrentTenant();
            if (isset($tenant['locale'])) {
                return $tenant['locale'];
            }
        }
        
        // PRIORIDAD 3: Header Accept-Language del navegador
        $preferredLanguage = $request->getPreferredLanguage($this->supportedLocales);
        if ($preferredLanguage) {
            return $preferredLanguage;
        }
        
        // PRIORIDAD 4: Fallback al idioma por defecto
        return $this->defaultLocale;
    }
    
    /**
     * Traducciones específicas del tenant
     */
    public function getTenantSpecificTranslations(): array
    {
        $tenant = $this->tenantContext->getCurrentTenant();
        $tenantName = $tenant['subdomain'] ?? 'default';
        $currentLocale = $this->getCurrentLocale();
        
        $tenantTranslations = [
            'melisahospital' => [
                'es' => [
                    'establishment_type' => 'Hospital',
                    'welcome_message' => 'Bienvenido al Sistema Hospitalario',
                    'main_service' => 'Atención Hospitalaria'
                ],
                'en' => [
                    'establishment_type' => 'Hospital',
                    'welcome_message' => 'Welcome to the Hospital System',
                    'main_service' => 'Hospital Care'
                ]
            ],
            'melisalacolina' => [
                'es' => [
                    'establishment_type' => 'Clínica',
                    'welcome_message' => 'Bienvenido a La Colina',
                    'main_service' => 'Atención Clínica Especializada'
                ],
                'en' => [
                    'establishment_type' => 'Clinic',
                    'welcome_message' => 'Welcome to La Colina',
                    'main_service' => 'Specialized Clinical Care'
                ]
            ]
        ];
        
        return $tenantTranslations[$tenantName][$currentLocale] ?? [];
    }
    
    /**
     * ⭐ MÉTODO CLAVE: Traduce usando TranslatorInterface de Symfony
     * 
     * Este método DELEGA al TranslatorInterface pero con locale dinámico multi-tenant
     */
    public function trans(string $id, array $parameters = [], string $domain = 'messages'): string
    {
        // AQUÍ USA TranslatorInterface de Symfony
        return $this->translator->trans(
            $id,                        // Clave: 'auth.login'
            $parameters,                // Parámetros: ['%name%' => 'Juan']
            $domain,                    // Dominio: 'messages'
            $this->getCurrentLocale()   // ← Locale dinámico multi-tenant ('es', 'en')
        );
    }
    
    /**
     * Establece idioma manualmente
     */
    public function setUserLocale(string $locale): bool
    {
        if (!in_array($locale, $this->supportedLocales)) {
            return false;
        }
        
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->hasSession()) {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
            return true;
        }
        
        return false;
    }
}
```

**Flujo de traducción:**
```
Controller → LocalizationService::trans()
                ↓
            getCurrentLocale() → Multi-nivel (sesión/tenant/navegador)
                ↓
            TranslatorInterface::trans('auth.login', [], 'messages', 'es')
                ↓
            Busca en: translations/messages.es.yaml
                ↓
            Retorna: "Iniciar Sesión"
```

---

#### 3. **LocaleListener** (Event Listener Customizado)

```php
class LocaleListener implements EventSubscriberInterface
{
    private LocalizationService $localizationService;
    
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        // Solo request principal
        if (!$event->isMainRequest()) {
            return;
        }
        
        // Para API → stateless
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            $locale = $request->headers->get('Accept-Language', 'es');
            $request->setLocale($locale);
            return;
        }
        
        // Para web → usar servicio completo
        $locale = $this->localizationService->getCurrentLocale();
        $request->setLocale($locale);
        
        // Persistir en sesión
        if ($request->hasSession()) {
            $session = $request->getSession();
            if (!$session->has('_locale')) {
                $session->set('_locale', $locale);
            }
        }
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
```

### Uso en Templates

```twig
{# melisa_tenant/templates/login/form.html.twig #}

<h3>{{ 'auth.login'|trans }}</h3>
<label>{{ 'auth.username'|trans }}</label>
<input name="username" />
<label>{{ 'auth.password'|trans }}</label>
<input name="password" />
<button>{{ 'auth.login'|trans }}</button>

{# Traducciones específicas del tenant #}
<div class="tenant-info">
    <p>{{ 'auth.system_description'|trans }}</p>
    <p>{{ 'auth.selected_company'|trans }}: {{ tenant_name }}</p>
</div>
```

### Archivos de Traducción (Más Extensos)

**translations/messages.es.yaml** (10,602 bytes)
```yaml
# =============================================================================
# TRADUCCIONES ESPAÑOL - MELISA TENANT
# Sistema Multi-Tenant de Gestión Médica
# =============================================================================

nav:
  dashboard: 'Tablero'
  patients: 'Pacientes'
  appointments: 'Citas'
  medical_records: 'Historiales Médicos'
  reports: 'Reportes'
  settings: 'Configuración'

auth:
  login: 'Iniciar Sesión'
  username: 'Usuario'
  password: 'Contraseña'
  remember_me: 'Recordarme'
  system_description: 'Sistema Multi-Tenant de Gestión Médica'
  selected_company: 'Empresa Seleccionada'
  tenant_not_found: 'No se pudo determinar la empresa'
  user_not_found: 'Usuario no encontrado en %tenant%'

dashboard:
  title: 'Tablero de Control'
  welcome: 'Bienvenido'
  today: 'Hoy'
  this_week: 'Esta Semana'

# ... +200 líneas más de traducciones específicas
```

---

## 🔄 Flujo de Detección de Idioma

### melisa_base (Simple)
```
Request → RedirectToPreferredLocaleSubscriber
    ↓
    ¿Es homepage?
    ↓ SÍ
    Leer Accept-Language del navegador
    ↓
    Comparar con locales soportados
    ↓
    Redirigir a /{{locale}}/
```

### melisa_tenant (Multi-nivel con TranslatorInterface)
```
1. Request llega al servidor
    ↓
2. LocaleListener (Priority 20) se ejecuta
    ↓
3. ¿Es API? → SÍ → Accept-Language header (stateless)
    ↓ NO
4. LocalizationService::getCurrentLocale()
    ↓
    NIVEL 1: ¿Hay locale en sesión del usuario? → Usar sesión
    ↓ NO
    NIVEL 2: ¿Tenant tiene locale configurado? → Usar tenant
    ↓ NO
    NIVEL 3: ¿Header Accept-Language válido? → Usar navegador
    ↓ NO
    NIVEL 4: Usar locale por defecto ('es')
    ↓
5. $request->setLocale($locale) → Symfony conoce el idioma
    ↓
6. Persistir en sesión
    ↓
7. Controller ejecuta
    ↓
8. Template usa {{ 'auth.login'|trans }}
    ↓
9. Twig Extension → TranslatorInterface::trans('auth.login', [], 'messages', 'es')
    ↓
10. TranslatorInterface busca en translations/messages.es.yaml
    ↓
11. Encuentra: auth.login: 'Iniciar Sesión'
    ↓
12. Retorna traducción al template
```

**Componentes involucrados:**
- ✅ **LocaleListener** → Detecta y establece locale antes del controller
- ✅ **LocalizationService** → Lógica multi-nivel de detección
- ✅ **TranslatorInterface** → Motor de traducciones de Symfony
- ✅ **Request::setLocale()** → Establece locale en request
- ✅ **Session** → Persiste preferencia del usuario
- ✅ **YAML Files** → Almacenan traducciones

---

## 📊 Comparativa de Características

| Característica | melisa_base | melisa_tenant |
|----------------|-------------|---------------|
| **Detección automática** | ✅ Solo navegador | ✅ Multi-nivel (sesión/tenant/navegador) |
| **Persistencia** | ❌ No | ✅ Sesión del usuario |
| **Cambio manual** | ❌ No | ✅ LocaleController |
| **Multi-tenant aware** | ❌ No | ✅ Locale por tenant |
| **API support** | ❌ No | ✅ Stateless con headers |
| **Traducciones específicas** | ❌ No | ✅ Por tenant |
| **Cantidad de traducciones** | ~20 claves | ~200+ claves |
| **Servicio centralizado** | ❌ No | ✅ LocalizationService |
| **Extensiones Twig** | ❌ No | ✅ LocalizationExtension |
| **TranslatorInterface** | ✅ Directo | ✅ Envuelto en LocalizationService |
| **Formato ICU** | ✅ Sí | ❌ No (YAML estándar) |
| **Redirección automática** | ✅ Homepage | ❌ No (persistencia) |

---

## 💡 Casos de Uso

### Caso 1: Usuario visita homepage por primera vez

**melisa_base:**
```
1. Usuario accede a melisaupgrade.prod/
2. RedirectToPreferredLocaleSubscriber detecta Accept-Language: es-CL
3. Redirige a melisaupgrade.prod/es/
4. Usuario ve todo en español
5. Si cierra y vuelve, repite el proceso
```

**melisa_tenant:**
```
1. Usuario accede a melisahospital.melisaupgrade.prod/login
2. LocaleListener ejecuta LocalizationService
3. No hay sesión → No hay locale en tenant → Lee Accept-Language: es-CL
4. Establece locale='es' y guarda en sesión
5. Usuario ve todo en español
6. Si cierra y vuelve, usa el de la sesión (persiste)
```

### Caso 2: Usuario cambia idioma manualmente

**melisa_base:**
```
No hay funcionalidad para cambio manual
Usuario debe cambiar idioma del navegador y volver a homepage
```

**melisa_tenant:**
```
1. Usuario clickea en selector de idioma
2. LocaleController::switch('en')
3. LocalizationService::setUserLocale('en')
4. Guarda en sesión: _locale = 'en'
5. Toda la aplicación cambia a inglés
6. Persiste en próximas visitas
```

### Caso 3: Tenant tiene idioma por defecto

**melisa_base:**
```
No aplica - no hay concepto de tenant
```

**melisa_tenant:**
```
1. Tenant "melisahospital" configurado con locale='en' en BD
2. Usuario nuevo accede (sin sesión)
3. LocalizationService detecta tenant → usa 'en'
4. Todo el hospital trabaja en inglés por defecto
5. Usuario puede cambiar a 'es' si quiere (se guarda en su sesión)
```

### Caso 4: API Request

**melisa_base:**
```
No hay soporte específico para API
Usa configuración global
```

**melisa_tenant:**
```
1. API Request: GET /api/patients
   Header: Accept-Language: en-US
2. LocaleListener detecta /api/ → modo stateless
3. Lee header directamente → usa 'en'
4. NO guarda en sesión (stateless)
5. Próximo request puede usar diferente idioma
```

---

## 🎯 Ventajas de melisa_tenant

### ✅ Persistencia de Preferencias
- Usuario selecciona idioma → se mantiene en todas las sesiones
- No necesita cambiar idioma en cada visita

### ✅ Multi-tenant Aware
- Hospital puede tener idioma por defecto distinto a clínica
- Traducciones específicas por tipo de establecimiento

### ✅ API Support
- Stateless para APIs REST
- Header Accept-Language directo
- No contamina sesión web

### ✅ Fallback Robusto
- 4 niveles de detección
- Siempre tiene un idioma válido
- Nunca falla

### ✅ Servicio Centralizado
- Un solo punto de acceso: LocalizationService
- Fácil de testear
- Reutilizable en toda la aplicación

### ✅ Extensible
- Agregar nuevo idioma: 1 archivo + 1 línea en array
- Traducciones específicas por tenant: agregar a método
- Fácil mantener

---

## 🔧 Cómo Implementar en melisa_tenant

### 1. Usar LocalizationService en Controladores

```php
class MiController extends AbstractTenantAwareController
{
    public function __construct(
        private LocalizationService $localizationService
    ) {}
    
    public function index(Request $request): Response
    {
        // Obtener idioma actual
        $locale = $this->localizationService->getCurrentLocale();
        
        // ⭐ Traducir directamente (usa TranslatorInterface internamente)
        $message = $this->localizationService->trans('welcome.message');
        
        // Traducir con parámetros
        $greeting = $this->localizationService->trans(
            'auth.user_not_found',
            ['%tenant%' => $this->getTenantName()]
        );
        
        // Obtener traducciones específicas del tenant
        $tenantTranslations = $this->localizationService->getTenantSpecificTranslations();
        
        return $this->render('template.html.twig', [
            'locale' => $locale,
            'message' => $message,
            'greeting' => $greeting,
            'tenant_translations' => $tenantTranslations
        ]);
    }
}
```

### 2. Usar AbstractController con TranslatorInterface directo

Si no usas LocalizationService, puedes usar TranslatorInterface directo:

```php
class OtroController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator  // ← Inyección directa de Symfony
    ) {}
    
    public function action(): Response
    {
        // Traducción directa con TranslatorInterface
        $message = $this->translator->trans(
            'auth.login',           // Clave
            [],                     // Parámetros
            'messages',             // Dominio
            'es'                    // Locale (hardcodeado)
        );
        
        // O usar helper de AbstractController
        $message2 = $this->trans('auth.login');  // Usa locale del request
        
        return new Response($message);
    }
}
```

**Diferencia clave:**
- **TranslatorInterface directo** → Necesitas especificar locale manualmente
- **LocalizationService** → Detecta locale automáticamente (multi-tenant aware)

### 3. Cambiar Idioma Manualmente

```php
// LocaleController
#[Route('/locale/switch/{locale}', name: 'locale_switch')]
public function switch(string $locale): Response
{
    // LocalizationService valida y guarda en sesión
    if ($this->localizationService->setUserLocale($locale)) {
        return $this->redirectToRoute('dashboard');
    }
    
    return new Response('Invalid locale', 400);
}
```

### 3. Usar en Templates

```twig
{# ========================================
   OPCIÓN 1: Trans filter (más común)
   Usa TranslatorInterface internamente
   ======================================== #}
   
{# Traducción simple #}
{{ 'auth.login'|trans }}

{# Traducción con parámetros #}
{{ 'auth.user_not_found'|trans({'%tenant%': tenant_name}) }}

{# Traducción con dominio específico #}
{{ 'constraints.email'|trans({}, 'validators') }}

{# ========================================
   OPCIÓN 2: Trans function
   ======================================== #}
   
<h1>{{ trans('dashboard.title') }}</h1>
<p>{{ trans('auth.welcome', {'%name%': user.name}) }}</p>

{# ========================================
   OPCIÓN 3: Pluralización
   TranslatorInterface soporta pluralización automática
   ======================================== #}

{# translations/messages.es.yaml:
   patient:
     count: '{0} No hay pacientes|{1} 1 paciente|]1,Inf[ %count% pacientes'
#}

{{ 'patient.count'|trans({'%count%': 0}) }}   {# → "No hay pacientes" #}
{{ 'patient.count'|trans({'%count%': 1}) }}   {# → "1 paciente" #}
{{ 'patient.count'|trans({'%count%': 15}) }}  {# → "15 pacientes" #}

{# ========================================
   OPCIÓN 4: Obtener idioma actual
   ======================================== #}
   
{{ app.request.locale }}  {# → 'es' o 'en' #}

{# ========================================
   OPCIÓN 5: Selector de idioma
   ======================================== #}
   
<select onchange="window.location.href='/locale/change/' + this.value">
    <option value="es" {% if app.request.locale == 'es' %}selected{% endif %}>
        🇪🇸 Español
    </option>
    <option value="en" {% if app.request.locale == 'en' %}selected{% endif %}>
        🇺🇸 English
    </option>
</select>

{# ========================================
   OPCIÓN 6: AJAX cambio de idioma
   ======================================== #}
   
<button onclick="changeLocale('en')">Switch to English</button>

<script>
function changeLocale(locale) {
    fetch('/locale/change/' + locale, {method: 'POST'})
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();  // Recargar para aplicar traducciones
            }
        });
}
</script>
```

### 4. Archivo de Traducciones YAML

**translations/messages.es.yaml**
```yaml
# =============================================================================
# ESTRUCTURA JERÁRQUICA RECOMENDADA
# TranslatorInterface lee estos archivos automáticamente
# =============================================================================

nav:
  dashboard: 'Tablero'
  patients: 'Pacientes'
  appointments: 'Citas'

auth:
  login: 'Iniciar Sesión'
  username: 'Usuario'
  password: 'Contraseña'
  user_not_found: 'Usuario no encontrado en %tenant%'  # ← Parámetro
  
dashboard:
  title: 'Tablero de Control'
  welcome: 'Bienvenido, %name%'                       # ← Parámetro
  
patient:
  count: '{0} No hay pacientes|{1} 1 paciente|]1,Inf[ %count% pacientes'  # ← Pluralización
  
establishments:
  hospital: 'Hospital'
  clinic: 'Clínica'
  medical_center: 'Centro Médico'
```

**translations/messages.en.yaml**
```yaml
nav:
  dashboard: 'Dashboard'
  patients: 'Patients'
  appointments: 'Appointments'

auth:
  login: 'Sign In'
  username: 'Username'
  password: 'Password'
  user_not_found: 'User not found in %tenant%'
  
dashboard:
  title: 'Control Panel'
  welcome: 'Welcome, %name%'
  
patient:
  count: '{0} No patients|{1} 1 patient|]1,Inf[ %count% patients'
  
establishments:
  hospital: 'Hospital'
  clinic: 'Clinic'
  medical_center: 'Medical Center'
```

### 5. Configuración Symfony

**config/packages/translation.yaml**
```yaml
framework:
    default_locale: '%env(DEFAULT_LOCALE)%'
    translator:
        default_path: '%kernel.project_dir%/translations'
        fallbacks:
            - '%env(DEFAULT_LOCALE)%'
        # Cache de traducciones
        cache_dir: '%kernel.cache_dir%/translations'
```

**.env**
```bash
DEFAULT_LOCALE=es
```

---

## ⚙️ Cómo Funciona TranslatorInterface Internamente

### Arquitectura de Symfony Translator

```
┌─────────────────────────────────────────────────────────────┐
│                    TranslatorInterface                      │
│  (Interface Symfony\Contracts\Translation)                  │
└─────────────────────┬───────────────────────────────────────┘
                      │ implements
                      ↓
┌─────────────────────────────────────────────────────────────┐
│                       Translator.php                        │
│  (Class Symfony\Component\Translation\Translator)           │
│                                                             │
│  + MessageCatalogue  → Caché de traducciones en memoria    │
│  + Loaders           → Lee YAML/PHP/JSON/XLIFF             │
│  + Formatters        → ICU MessageFormat, sprintf          │
│  + Fallback Locales  → Si no encuentra 'es', usa 'en'      │
└─────────────────────┬───────────────────────────────────────┘
                      │ usa
                      ↓
┌─────────────────────────────────────────────────────────────┐
│                 MessageCatalogue.php                        │
│  Almacena todas las traducciones cargadas en memoria       │
│                                                             │
│  ['messages']['es']['auth.login'] = 'Iniciar Sesión'       │
│  ['messages']['en']['auth.login'] = 'Sign In'              │
│  ['validators']['es']['email.invalid'] = 'Email inválido'  │
└─────────────────────┬───────────────────────────────────────┘
                      │ carga desde
                      ↓
┌─────────────────────────────────────────────────────────────┐
│              translations/*.{locale}.yaml                   │
│                                                             │
│  messages.es.yaml    messages.en.yaml                       │
│  validators.es.yaml  validators.en.yaml                     │
└─────────────────────────────────────────────────────────────┘
```

### Proceso Interno de trans()

```php
// Cuando llamas a:
$translator->trans('auth.login', [], 'messages', 'es');

// Internamente Translator.php hace:

1. Verificar si MessageCatalogue para 'es' está cargado
   ↓
2. Si NO → Cargar translations/messages.es.yaml
   ↓
3. Buscar clave 'auth.login' en dominio 'messages'
   ↓
4. Si encuentra → Aplicar parámetros (si hay)
   ↓
5. Si NO encuentra → Intentar con fallback locale
   ↓
6. Si tampoco → Retornar la clave original 'auth.login'
   ↓
7. Retornar traducción final
```

### Ejemplo Real Paso a Paso

```php
// Template Twig ejecuta:
{{ 'auth.user_not_found'|trans({'%tenant%': 'Hospital'}) }}

// Twig Extension llama a:
$translator->trans('auth.user_not_found', ['%tenant%' => 'Hospital'], 'messages', 'es');

// Translator.php internamente:

// PASO 1: Obtener catálogo para locale 'es'
$catalogue = $this->getCatalogue('es');

// PASO 2: Si no existe, cargar desde filesystem
if (!$this->catalogues['es']) {
    $this->loadCatalogue('es');
    // Lee: translations/messages.es.yaml
    // Parsea YAML → Array PHP
    // Almacena en $this->catalogues['es']
}

// PASO 3: Buscar mensaje en catálogo
$id = 'auth.user_not_found';
$domain = 'messages';

if ($catalogue->has($id, $domain)) {
    $message = $catalogue->get($id, $domain);
    // $message = "Usuario no encontrado en %tenant%"
} else {
    $message = $id;  // Fallback a la clave
}

// PASO 4: Reemplazar parámetros
$parameters = ['%tenant%' => 'Hospital'];
foreach ($parameters as $key => $value) {
    $message = str_replace($key, $value, $message);
}
// $message = "Usuario no encontrado en Hospital"

// PASO 5: Retornar
return $message;
```

### Cache de Traducciones

```php
// Primera vez que se usa 'es':
$translator->trans('auth.login', [], 'messages', 'es');
// → Lee translations/messages.es.yaml
// → Parsea TODO el archivo
// → Almacena en memoria (MessageCatalogue)
// → Guarda en caché filesystem (var/cache/dev/translations/)

// Segunda vez (mismo request):
$translator->trans('auth.password', [], 'messages', 'es');
// → NO lee archivo (ya está en memoria)
// → Busca directamente en MessageCatalogue
// → SÚPER RÁPIDO

// Próximo request:
// → Lee desde var/cache/dev/translations/catalogue.es.php
// → NO parsea YAML (usa caché compilada)
```

### Fallback Locales

```yaml
# config/packages/translation.yaml
framework:
    translator:
        fallbacks:
            - es    # Si no encuentra en 'en', busca en 'es'
```

```php
// Si ejecutas:
$translator->trans('new.key', [], 'messages', 'en');

// Y 'new.key' NO existe en messages.en.yaml
// Pero SÍ existe en messages.es.yaml

// Translator.php hace:
// 1. Buscar en messages.en.yaml → NO EXISTE
// 2. Buscar en messages.es.yaml (fallback) → EXISTE
// 3. Retornar traducción en español

// Resultado: "Traducción en español aunque pediste inglés"
```

### Pluralización con TranslatorInterface

```yaml
# translations/messages.es.yaml
patient:
  count: '{0} No hay pacientes|{1} 1 paciente|]1,Inf[ %count% pacientes'
```

```php
// Template:
{{ 'patient.count'|trans({'%count%': 5}) }}

// TranslatorInterface usa MessageSelector internamente:
$message = '{0} No hay pacientes|{1} 1 paciente|]1,Inf[ %count% pacientes';
$number = 5;

// MessageSelector parsea:
// {0}        → Si count == 0
// {1}        → Si count == 1  
// ]1,Inf[    → Si count > 1

// Como 5 > 1 → Usa tercera parte: "%count% pacientes"
// Reemplaza %count% con 5 → "5 pacientes"
```

---

**Conclusión:** melisa_tenant tiene un sistema de localización **mucho más robusto y completo** que melisa_base, diseñado específicamente para multi-tenancy con persistencia, traducciones específicas por tenant, soporte API, y múltiples niveles de detección automática. 

### 🎯 Componentes Clave en melisa_tenant:

1. **TranslatorInterface** (Symfony Core)
   - Motor de traducciones
   - Caché automático de archivos YAML
   - Fallback locales
   - Pluralización
   - Reemplazo de parámetros

2. **LocalizationService** (Customizado Multi-Tenant)
   - Envuelve TranslatorInterface
   - Detecta locale dinámicamente (sesión/tenant/navegador)
   - Traducciones específicas por tenant
   - API stateless support

3. **LocaleListener** (Priority 20)
   - Ejecuta antes de controllers
   - Establece locale en request
   - Persiste en sesión

4. **LocaleController**
   - Permite cambio manual de idioma
   - Validación de locales soportados
   - Persistencia en sesión

**El flujo completo es:**
```
Request → LocaleListener → LocalizationService::getCurrentLocale() 
  → Controller → Template → {{ 'key'|trans }} 
  → TranslatorInterface::trans() 
  → translations/messages.{locale}.yaml 
  → Usuario ve texto traducido
```

🌍
