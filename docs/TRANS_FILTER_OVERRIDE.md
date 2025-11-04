# 🔄 Sobrescritura del Filtro |trans de Symfony

## 📋 Resumen

El filtro `|trans` de Symfony ha sido **sobrescrito** para que sea **tenant-aware** automáticamente.

## ✅ ¿Qué cambió?

### ❌ ANTES (requería |ttrans)

```twig
{# Filtro personalizado #}
{{ 'auth.login'|ttrans }}

{# O especificar dominio manualmente #}
{{ 'auth.login'|trans({}, 'melisahospital') }}
```

### ✅ AHORA (funciona automáticamente con |trans)

```twig
{# El filtro |trans estándar detecta el tenant automáticamente #}
{{ 'auth.login'|trans }}

{# También funciona con parámetros #}
{{ 'auth.user_not_found'|trans({'%tenant%': tenant.name}) }}
```

## 🎯 Beneficios

1. ✅ **Compatibilidad**: No hay que cambiar templates existentes que usan `|trans`
2. ✅ **Simplicidad**: Los desarrolladores usan el filtro estándar de Symfony
3. ✅ **Automático**: La detección del tenant es transparente
4. ✅ **Fallback**: Si no encuentra la traducción en el tenant, busca en 'default' y luego en 'messages'

## 🔧 Implementación Técnica

### LocalizationExtension.php

```php
public function getFilters(): array
{
    return [
        // SOBRESCRIBE el filtro trans estándar de Symfony
        new TwigFilter('trans', [$this, 'translateTenant']),
        // Mantener ttrans como alias
        new TwigFilter('ttrans', [$this, 'translateTenant']),
    ];
}

public function translateTenant(string $id, array $parameters = []): string
{
    // Usa LocalizationService que detecta el tenant automáticamente
    return $this->localizationService->trans($id, $parameters);
}
```

### LocalizationService.php

```php
public function trans(string $id, array $parameters = [], string $domain = 'messages'): string
{
    $tenantDomain = $this->getTenantDomain(); // melisahospital, melisalacolina, default
    
    // NIVEL 1: Buscar en dominio del tenant
    if ($tenantDomain !== 'default' && $tenantDomain !== 'messages') {
        $tenantTranslation = $this->translator->trans($id, $parameters, $tenantDomain, $locale);
        if ($tenantTranslation !== $id) {
            return $tenantTranslation; // ✅ Encontrada
        }
    }
    
    // NIVEL 2: Buscar en dominio 'default'
    if ($tenantDomain !== 'default') {
        $defaultTranslation = $this->translator->trans($id, $parameters, 'default', $locale);
        if ($defaultTranslation !== $id) {
            return $defaultTranslation; // ✅ Encontrada
        }
    }
    
    // NIVEL 3: Fallback a dominio 'messages'
    return $this->translator->trans($id, $parameters, 'messages', $locale);
}
```

## 🧪 Ejemplos de Uso

### Ejemplo 1: Traducción Simple

```twig
{# En melisahospital.melisaupgrade.prod #}
{{ 'auth.login'|trans }}
{# Output: "Ingreso al Sistema Hospitalario" #}

{# En melisalacolina.melisaupgrade.prod #}
{{ 'auth.login'|trans }}
{# Output: "Acceso a La Colina" #}
```

### Ejemplo 2: Traducción con Parámetros

```twig
{{ 'auth.user_not_found'|trans({'%tenant%': tenant.name}) }}
{# Output: "Usuario no encontrado o inactivo en Hospital Central" #}
```

### Ejemplo 3: Traducción No Encontrada (Fallback)

```twig
{# Si 'some.new.key' NO está en melisahospital.es.yaml #}
{# Pero SÍ está en default.es.yaml #}
{{ 'some.new.key'|trans }}
{# Output: Traducción de default.es.yaml #}

{# Si tampoco está en default.es.yaml #}
{# Busca en messages.es.yaml #}
```

## 📁 Archivos de Traducción

```
translations/
├── melisahospital/
│   ├── melisahospital.es.yaml    (Prioridad 1)
│   └── melisahospital.en.yaml
├── melisalacolina/
│   ├── melisalacolina.es.yaml    (Prioridad 1)
│   └── melisalacolina.en.yaml
├── default/
│   ├── default.es.yaml           (Prioridad 2 - Fallback)
│   └── default.en.yaml
└── messages.es.yaml              (Prioridad 3 - Fallback global)
```

## ⚠️ Importante

1. **Claves Comentadas**: Las secciones `auth.*` y `nav.*` en `messages.es.yaml` están **COMENTADAS** para evitar conflictos
2. **Alias ttrans**: El filtro `|ttrans` sigue disponible como alias, pero ya no es necesario
3. **Compatibilidad**: Ambos filtros (`|trans` y `|ttrans`) funcionan exactamente igual

## 🔍 Verificación

### Verificar Filtros Registrados

```bash
php bin/console debug:twig --filter=trans
```

**Output esperado:**
```
Filters
-------
 * tenant_trans(parameters = [])
 * trans(parameters = [])
 * ttrans(parameters = [])
```

### Verificar Traducciones por Dominio

```bash
php bin/console debug:translation es melisahospital
php bin/console debug:translation es melisalacolina
php bin/console debug:translation es default
```

## 📝 Migración de Templates

### ✅ No se requiere acción

Si tus templates ya usaban `|trans`, **seguirán funcionando** pero ahora con detección automática de tenant.

### ⚙️ Opcional: Simplificar código

Si tienes código que especifica el dominio manualmente:

```twig
{# ANTES #}
{{ 'auth.login'|trans({}, 'melisahospital') }}

{# DESPUÉS (más simple) #}
{{ 'auth.login'|trans }}
```

## 🎓 Casos de Uso Avanzados

### Forzar un Dominio Específico

Si necesitas forzar un dominio específico (caso muy raro):

```twig
{# Usar el TranslatorInterface directamente desde un servicio #}
{# O crear un nuevo filtro personalizado #}
```

### Traducciones en Controladores PHP

```php
// En un Controller
$translation = $this->localizationService->trans('auth.login');
// Detecta automáticamente el tenant
```

## 🐛 Debugging

Si las traducciones no funcionan:

1. **Limpiar caché**: `php bin/console cache:clear`
2. **Verificar tenant**: Visita `/debug/translation` 
3. **Validar sintaxis**: `php bin/console lint:twig`
4. **Verificar servicios**: `php bin/console lint:container`

## 📚 Referencias

- [Documentación Principal](./TRANSLATIONS_BY_TENANT.md)
- [Comparación de Enfoques](./LOCALIZATION_SYSTEM_COMPARISON.md)
- [Flujo del Sistema](./SYSTEM_FLOW_DETAILED.md)

---

**Creado**: 2024-11-04  
**Última actualización**: 2024-11-04  
**Autor**: Sistema Melisa Tenant
