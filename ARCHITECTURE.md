# Arquitectura Multi-Tenant Melisa

## 🏗️ Arquitectura de 2 Proyectos (CORRECTA)

```
┌─────────────────────────────────────────────────────────────────┐
│                     INFRAESTRUCTURA MYSQL                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────┐  │
│  │ melisa_central   │  │ melisalacolina   │  │ melisahospital│ │
│  ├──────────────────┤  ├──────────────────┤  ├──────────────┤  │
│  │ tenant (table)   │  │ member (users)   │  │ member (users)│ │
│  │  - id            │  │ patient          │  │ patient       │  │
│  │  - subdomain     │  │ appointment      │  │ appointment   │  │
│  │  - database_name │  │ invoice          │  │ invoice       │  │
│  └──────────────────┘  └──────────────────┘  └──────────────┘  │
│          ↑                      ↑                     ↑          │
└──────────|──────────────────────|─────────────────────|──────────┘
           │                      │                     │
           │ lee registro         │ switch             │ switch
           │                      │ conexión           │ conexión
           │                      │                     │
┌──────────┴──────────┐  ┌────────┴─────────────────────┴─────────┐
│ melisa_central/     │  │ melisa_tenant/                          │
│ (Proyecto Admin)    │  │ (Proyecto Multi-Tenant)                 │
├─────────────────────┤  ├─────────────────────────────────────────┤
│ - CRUD de tenants   │  │ - Login por subdomain                   │
│ - Solo admin users  │  │ - Dashboard dinámico                    │
│ - Gestión registro  │  │ - Módulos de negocio                    │
│                     │  │                                         │
│ Entity:             │  │ Entity:                                 │
│  └─ Tenant.php      │  │  └─ Member.php (en cada tenant DB)      │
│                     │  │                                         │
│ NO tiene Member ❌  │  │ Service:                                │
│                     │  │  ├─ TenantResolver (lee melisa_central) │
│                     │  │  └─ TenantContext (mantiene tenant)     │
└─────────────────────┘  └─────────────────────────────────────────┘
```

**Separación de Responsabilidades:**
- **melisa_central**: "¿Qué tenants existen?" → Responde: melisalacolina, melisahospital
- **melisa_tenant**: "Dame los usuarios de melisalacolina" → Switch a BD melisalacolina → lee tabla member

---

## 📁 Estructura de Proyectos

```
/var/www/html/
│
├── melisa_central/                    # PROYECTO MAIN (Solo Registro)
│   ├── src/
│   │   ├── Entity/
│   │   │   └── Tenant.php            # ← SOLO registro de clientes
│   │   ├── Controller/
│   │   │   └── TenantController.php  # CRUD de tenants (admin)
│   │   └── Repository/
│   │       └── TenantRepository.php
│   ├── migrations/                    # Solo para tabla tenant
│   └── config/
│       └── packages/doctrine.yaml    # 1 EM apuntando a melisa_central
│
└── melisa_tenant/                     # PROYECTO TENANT (App Multi-Tenant)
    ├── src/
    │   ├── Entity/
    │   │   └── Member.php            # ← Usuarios EN CADA tenant DB
    │   ├── Controller/
    │   │   ├── LoginController.php   # Login (lee member de tenant DB)
    │   │   └── DashboardController.php
    │   ├── Service/
    │   │   ├── TenantResolver.php    # Lee melisa_central.tenant
    │   │   └── TenantContext.php     # Mantiene tenant actual
    │   └── EventSubscriber/
    │       └── TenantSubscriber.php  # Cambia conexión a tenant DB
    ├── migrations/                    # Para member, patient, etc (TODOS los tenants)
    └── config/
        └── packages/doctrine.yaml    # 1 EM (dinámico: melisalacolina, melisahospital, etc)
```

**Flujo de Datos:**
1. `melisa_central`: Lee tabla `tenant` → sabe que existe `melisalacolina`
2. `melisa_tenant`: Cambia conexión a `melisalacolina` → lee tabla `member` con usuarios

**NO hay tabla `member` en melisa_central** ✅

---

## 🔄 Flujo de Autenticación (Login)

### Paso a Paso Detallado:

```
1. Usuario visita:
   http://melisalacolina.melisaupgrade.prod/login
          ↓
2. [TenantSubscriber] extrae subdomain
   subdomain = "melisalacolina"
          ↓
3. [TenantResolver] consulta melisa_central
   Query: SELECT * FROM tenant WHERE subdomain = 'melisalacolina'
   Respuesta: {
     id: 2,
     name: "Melisa La Colina",
     database_name: "melisalacolina",
     subdomain: "melisalacolina"
   }
          ↓
4. [Doctrine] cambia conexión
   De: ninguna
   A: melisalacolina (BD del tenant)
          ↓
5. Usuario ingresa credenciales:
   username: "doctor.gomez"
   password: "******"
          ↓
6. [LoginController] consulta EN melisalacolina
   Query: SELECT * FROM member 
          WHERE username = 'doctor.gomez'
   Resultado: Hash del password
          ↓
7. Verificación de password
   password_verify($input, $hash)
          ↓
8. Login exitoso
   Session guardada con tenant + member
          ↓
9. Redirect a /dashboard
```

### Punto Clave:
- **melisa_central** solo dice: "melisalacolina existe y su BD es `melisalacolina`"
- **melisalacolina** (tenant DB) tiene la tabla `member` con usuarios y passwords
- **Aislamiento total:** Los usuarios de melisalacolina NO pueden ver usuarios de melisahospital

---

## 💾 Bases de Datos

### melisa_central (Main DB) - SOLO REGISTRO
```sql
-- ÚNICA TABLA: Registro de clientes
tenant
  ├── id
  ├── name
  ├── subdomain          # melisalacolina, melisahospital
  ├── database_name      # Nombre de la BD del tenant
  ├── domain
  ├── is_active
  └── ...
```
**IMPORTANTE:** 
- ❌ NO tiene tabla `member` (usuarios están en cada tenant DB)
- ❌ NO tiene tabla `tenant_member` (no existe relación aquí)
- ✅ SOLO registra QUÉ tenants existen y DÓNDE está su BD

### melisalacolina (Tenant DB #1)
```sql
-- Datos del cliente La Colina
member              # ← Usuarios con username/password de La Colina
  ├── id
  ├── username
  ├── password
  ├── email
  └── ...

patient             # Pacientes de La Colina
appointment         # Citas de La Colina
invoice             # Facturas de La Colina
```

### melisahospital (Tenant DB #2)
```sql
-- Datos del cliente Hospital (AISLADOS)
member              # ← Usuarios con username/password del Hospital
  ├── id
  ├── username
  ├── password
  └── ...

patient             # Pacientes del Hospital
appointment         # Citas del Hospital
invoice             # Facturas del Hospital
```

**Cada tenant tiene su propia tabla `member` completamente aislada.**

---

## 🎯 Estrategia con el Bundle

El bundle está diseñado para **1 proyecto con 2 Entity Managers**.  
Tu arquitectura ya tiene **2 proyectos**, que es **mejor**.

### ❌ NO Hacer:
- NO fusionar proyectos en uno solo
- NO crear `src/Entity/Main/` en melisa_tenant
- NO crear Entity Manager "main" en melisa_tenant

### ✅ SÍ Hacer:
- ✅ Adoptar `TenantEntityManager` del bundle
- ✅ Usar `SwitchDbEvent` para cambiar conexión
- ✅ Usar comandos del bundle para migraciones tenant
- ✅ Mantener proyectos separados

---

## 📋 Ventajas de Tu Arquitectura vs Bundle

| Aspecto | Tu Arquitectura (2 Proyectos) | Bundle (1 Proyecto) |
|---------|-------------------------------|---------------------|
| **Separación** | Total | Lógica (mismo proyecto) |
| **Escalabilidad** | Independiente por proyecto | Limitada |
| **Deploy** | Separado (central vs tenant) | Junto |
| **Seguridad** | Main puede estar privado | Todo expuesto |
| **Complejidad** | Media | Alta (2 EMs) |
| **Mantenibilidad** | Alta | Media |
| **Testing** | Fácil (proyectos aislados) | Complejo (2 EMs) |

---

## 🚀 Próximos Pasos

1. **Mantener arquitectura de 2 proyectos**
2. **En melisa_tenant:**
   - Adoptar `TenantEntityManager`
   - Usar `SwitchDbEvent`
   - Usar comandos del bundle

3. **En melisa_central:**
   - Sin cambios (ya está correcto)
   - Opcionalmente: exponer API REST

---

## 📞 Decisión Final

**Tu arquitectura es correcta.**  
No necesitas el 100% del bundle, solo:
- TenantEntityManager
- SwitchDbEvent
- Comandos de migración

El resto de features (Main EM, TenantConfigProvider) ya los tienes implementados mejor en proyectos separados.
