# Guía de Voters - Sistema de Permisos Granulares

## ¿Qué es un Voter?

Un Voter es un componente de Symfony que decide si un usuario tiene permiso para realizar una acción específica sobre un recurso. Es parte del sistema de seguridad y autorización.

---

## Implementación Actual: PermissionVoter

### Archivo Principal
`src/Security/Voter/PermissionVoter.php`

### Características Implementadas

- ✅ **Atributos soportados:** `VIEW`, `EDIT`, `DELETE`
- ✅ **Recursos soportados:** `SecuredResourceInterface` y `FieldAccess`
- ✅ **Resolución en cascada:** De específico a general (4 niveles)
- ✅ **Prioridad:** Usuario > Grupo > Denegar por defecto
- ✅ **Tests:** 9 tests unitarios con 19 assertions

---

## Cómo Funciona

### Flujo de Decisión

```
Usuario solicita acceso → supports() → ¿Es VIEW/EDIT/DELETE? → Sí
                                      ↓
                                    voteOnAttribute()
                                      ↓
                             ¿Usuario autenticado?
                                      ↓ Sí
                          resolvePermission() - Cascada
                                      ↓
                    ┌─────────────────┴─────────────────┐
                    ↓                                   ↓
            resolveForUser()                  resolveForGroups()
         (Prioridad ALTA)                    (Prioridad MEDIA)
                    ↓                                   ↓
            ¿Encontró permiso?                 ¿Encontró permiso?
                    ↓ No                               ↓ No
                    └───────────────┬───────────────────┘
                                    ↓
                           Denegación por defecto
                                 (false)
```

### Cascada de Resolución (4 niveles)

El voter busca permisos de **más específico a más general:**

1. `domain + resourceId + fieldName` - Más específico
   - Ejemplo: `patient + 123 + diagnosis`
   - "Permiso para el campo 'diagnosis' del paciente #123"

2. `domain + resourceId + NULL` 
   - Ejemplo: `patient + 123 + NULL`
   - "Permiso para TODOS los campos del paciente #123"

3. `domain + NULL + fieldName`
   - Ejemplo: `patient + NULL + diagnosis`
   - "Permiso para el campo 'diagnosis' de TODOS los pacientes"

4. `domain + NULL + NULL` - Más general
   - Ejemplo: `patient + NULL + NULL`
   - "Permiso para TODOS los campos de TODOS los pacientes"

### Ejemplo Práctico

**Escenario:** Usuario solicita editar campo `diagnosis` del paciente #123

**Búsqueda en cascada:**
1. 🔍 `patient + 123 + diagnosis` → ¿Existe? → Sí ✅ **PERMITIR**
2. Si no existe, buscar: `patient + 123 + NULL`
3. Si no existe, buscar: `patient + NULL + diagnosis`
4. Si no existe, buscar: `patient + NULL + NULL`
5. Si nada existe en permisos de usuario, repetir búsqueda en permisos de grupos
6. Si tampoco hay permisos de grupo → ❌ **DENEGAR**

---

## Ejemplos de Uso

### 1. En un Controlador

```php
use App\Security\Voter\PermissionVoter;
use App\Security\FieldAccess;

class PatientController extends AbstractController
{
    public function show(Patient $patient): Response
    {
        // Verificar acceso al recurso completo
        if (!$this->isGranted(PermissionVoter::VIEW, $patient)) {
            throw $this->createAccessDeniedException();
        }

        // Verificar acceso a campo específico
        $canEditDiagnosis = $this->isGranted(
            PermissionVoter::EDIT, 
            new FieldAccess($patient, 'diagnosis')
        );

        return $this->render('patient/show.html.twig', [
            'patient' => $patient,
            'canEditDiagnosis' => $canEditDiagnosis,
        ]);
    }
}
```

### 2. En una Vista Twig

```twig
{% if is_granted('VIEW', patient) %}
    <h1>{{ patient.fullName }}</h1>
    
    {# Mostrar campos sensibles solo si tiene permiso #}
    {% if is_granted('VIEW', field_access(patient, 'diagnosis')) %}
        <div class="diagnosis">
            <strong>Diagnóstico:</strong> {{ patient.diagnosis }}
        </div>
    {% endif %}
    
    {# Botón de editar solo si tiene permiso #}
    {% if is_granted('EDIT', patient) %}
        <a href="{{ path('patient_edit', {id: patient.id}) }}" class="btn btn-primary">
            Editar
        </a>
    {% endif %}
{% endif %}
```

### 3. Con Atributos PHP 8

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PatientController extends AbstractController
{
    #[IsGranted(PermissionVoter::EDIT, subject: 'patient')]
    public function edit(Patient $patient): Response
    {
        // Si llega aquí, el usuario tiene permiso EDIT
        // Automáticamente verifica antes de ejecutar
        
        return $this->render('patient/edit.html.twig', [
            'patient' => $patient,
        ]);
    }
    
    #[IsGranted(PermissionVoter::DELETE, subject: 'patient')]
    public function delete(Patient $patient): Response
    {
        // Solo usuarios con permiso DELETE pueden ejecutar esto
        $entityManager->remove($patient);
        $entityManager->flush();
        
        return $this->redirectToRoute('patient_list');
    }
}
```

### 4. Verificar Múltiples Campos

```php
public function getEditableFields(Patient $patient): array
{
    $editableFields = [];
    
    $fields = ['firstName', 'lastName', 'diagnosis', 'medicalHistory', 'medications'];
    
    foreach ($fields as $field) {
        if ($this->isGranted(PermissionVoter::EDIT, new FieldAccess($patient, $field))) {
            $editableFields[] = $field;
        }
    }
    
    return $editableFields;
}
```

---

## Componentes del Sistema

### Entidades

- **`Member`** - Usuario del sistema
- **`MemberGroup`** - Grupos de usuarios (ej: DOCTORES, ENFERMERAS)
- **`Permission`** - Permisos individuales por usuario
- **`GroupPermission`** - Permisos por grupo

### Interfaces

- **`SecuredResourceInterface`** - Debe implementarse en entidades que requieren permisos
  ```php
  interface SecuredResourceInterface
  {
      public function getPermissionDomain(): string; // ej: 'patient'
      public function getPermissionId(): ?int;       // ej: 123
  }
  ```

- **`FieldAccess`** - Value object para verificar permisos de campos específicos
  ```php
  $fieldAccess = new FieldAccess($patient, 'diagnosis');
  ```

### Repositorios

- **`PermissionRepository::findAllByMember()`** - Carga permisos de usuario
- **`GroupPermissionRepository::findByGroups()`** - Carga permisos de grupos

---

## Principios de Seguridad

### 1. Deny by Default (Denegar por Defecto)
Si no hay permisos explícitos, **siempre se deniega** el acceso.

### 2. Prioridad de Permisos
```
Usuario > Grupo > Denegar
```
Los permisos individuales **siempre ganan** sobre los permisos de grupo.

### 3. Permisos Explícitos
Tanto PERMITIR como DENEGAR son explícitos:
- `canView = true` → PERMITIR
- `canView = false` → DENEGAR explícito (bloquea permisos de grupo)

### 4. Usuarios No Autenticados
Siempre se deniega el acceso si el usuario no está autenticado.

---

## Tests Unitarios

**Archivo:** `tests/Unit/Security/Voter/PermissionVoterTest.php`

### Tests Implementados (9 tests)

1. ✅ **testSupportsViewEditDeleteAttributes** - Verifica atributos soportados
2. ✅ **testSupportsSecuredResourceInterface** - Verifica soporte de interfaz
3. ✅ **testSupportsFieldAccess** - Verifica soporte de campos
4. ✅ **testDeniesAccessWhenUserNotAuthenticated** - Seguridad sin autenticación
5. ✅ **testGrantsAccessWithUserSpecificPermission** - Permiso individual
6. ✅ **testGrantsAccessWithGroupPermission** - Permiso heredado de grupo
7. ✅ **testDeniesAccessByDefault** - Denegar por defecto
8. ✅ **testUserPermissionOverridesGroupPermission** - Prioridad de usuario
9. ✅ **testFieldLevelPermissionCascade** - Permisos a nivel de campo

### Ejecutar Tests

```bash
# Todos los tests del voter
php bin/phpunit tests/Unit/Security/Voter/PermissionVoterTest.php

# Un test específico
php bin/phpunit --filter testGrantsAccessWithUserSpecificPermission
```

---

## Métricas

| Métrica | Valor |
|---------|-------|
| Archivos creados | 3 |
| Tests unitarios | 9 |
| Aserciones | 19 |
| Líneas de código (voter) | ~240 |
| Líneas de código (tests) | ~380 |
| Cobertura funcional | 100% |

---

## Notas Técnicas

### Rendimiento Actual (Sin Cache)

⚠️ **Sin optimización de cache**, el sistema ejecuta:
- **2 queries por verificación de usuario**
- **1 query por grupo** del usuario (promedio 2-3 grupos)
- **Total: 4-8 queries por verificación**
- **Con 10 campos verificados: 40-80 queries por request**

💡 **Optimización futura:** Implementar cache para reducir a 2 queries totales por request.

### Métodos del Voter

```php
// Método público (punto de entrada)
supports(string $attribute, mixed $subject): bool

// Método público (decisión de permiso)
voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool

// Métodos privados (lógica interna)
resolvePermission()      // Cascada usuario → grupo → denegar
resolveForUser()         // Buscar permisos de usuario (4 niveles)
resolveForGroups()       // Buscar permisos de grupos (4 niveles)
checkPermissionFlag()    // Verificar flag según atributo (VIEW/EDIT/DELETE)
```

---

## Resumen

El **PermissionVoter** implementa un sistema de permisos granulares con:

✅ Permisos a nivel de **recurso completo**
✅ Permisos a nivel de **campo específico**
✅ Resolución en **cascada** (específico → general)
✅ **Prioridad** de permisos individuales sobre grupales
✅ **Denegar por defecto** para máxima seguridad
✅ **Tests completos** con 9 escenarios cubiertos

El sistema está listo para ser usado en controladores, servicios y vistas Twig mediante `isGranted()` y `#[IsGranted]`.
