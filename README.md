# 🏥 Melisa Tenant - Sistema Multi-Tenant de Gestión Médica

![Symfony](https://img.shields.io/badge/Symfony-6.4-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![API Platform](https://img.shields.io/badge/API%20Platform-4.2-success)
![Stimulus](https://img.shields.io/badge/Stimulus-3.2-yellow)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)

**Melisa Tenant** es un sistema multi-tenant de gestión médica con API Platform y frontend interactivo Stimulus. Cada clínica/hospital tiene su propio dashboard personalizado y base de datos independiente.

---

## 🏗️ Arquitectura del Sistema

### 🌐 **Multi-Tenant por Subdominios**

| Tenant | URL | Descripción |
|--------|-----|-------------|
| **🏥 Hospital** | `melisahospital.localhost:8081` | Dashboard para hospitales |
| **🌿 La Colina** | `melisalacolina.localhost:8081` | Dashboard para clínicas |
| **💻 Wi Clinic** | `melisawiclinic.localhost:8081` | Dashboard tecnológico |

### 🗄️ **Bases de Datos**
- **`melisa_central`** - Gestión de tenants y configuración
- **Por tenant** - Base de datos independiente por cada clínica

### 🎮 **Controllers Stimulus (Nueva Arquitectura)**
```
assets/controllers/
├── dynamic_loader.js              # Sistema de fallback automático
├── internal/                      # Controllers internos (formularios, UI)
│   ├── default/
│   ├── melisahospital/
│   ├── melisalacolina/
│   └── melisawiclinic/
└── apiplatform/                   # Controllers API Platform
    ├── default/
    ├── melisahospital/
    ├── melisalacolina/
    └── melisawiclinic/
```

**Sistema de Fallback:**
1. Busca controller específico del tenant: `internal/melisalacolina/patient_controller.js`
2. Si no existe, usa default: `internal/default/patient_controller.js`

---

## 🚀 Instalación

### 📋 **Prerrequisitos**
- **PHP 8.1+**
- **MySQL 8.0**
- **Composer**
- **Apache** con mod_rewrite

### 🔧 **Pasos de Instalación**

#### 1. **Clonar repositorio**
```bash
git clone [TFS_URL] melisa_tenant
cd melisa_tenant
```

#### 2. **Instalar dependencias**
```bash
composer install
```

#### 3. **Configurar entorno**
```bash
cp .env .env.local
```

Editar `.env.local`:
```env
DATABASE_URL="mysql://melisa:melisamelisa@127.0.0.1:3306/melisa_central"
APP_ENV=dev
APP_DEBUG=1
```

#### 4. **Configurar base de datos**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### 5. **Compilar assets**
```bash
php bin/console asset-map:compile
```

---

## 🌐 Configuración de Hosts

### 🐧 **Linux/Mac**
```bash
sudo nano /etc/hosts
```
Agregar:
```
127.0.0.1 melisahospital.localhost
127.0.0.1 melisalacolina.localhost
127.0.0.1 melisawiclinic.localhost
```

### 🪟 **Windows**

#### **Método 1: Editor de texto (Recomendado)**
1. Abrir **Bloc de notas como Administrador**
2. Ir a: `Archivo` → `Abrir`
3. Navegar a: `C:\Windows\System32\drivers\etc\`
4. Cambiar filtro de archivos a **"Todos los archivos (*.*)"**
5. Abrir archivo `hosts`
6. Agregar al final:
```
127.0.0.1 melisahospital.localhost
127.0.0.1 melisalacolina.localhost
127.0.0.1 melisawiclinic.localhost
```
7. Guardar archivo

#### **Método 2: PowerShell (Avanzado)**
```powershell
# Ejecutar PowerShell como Administrador
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1 melisahospital.localhost"
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1 melisalacolina.localhost"
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1 melisawiclinic.localhost"
```

#### **Verificar configuración Windows:**
```cmd
ping melisahospital.localhost
# Debe responder desde 127.0.0.1
```

---

## 🖥️ Ejecutar el Sistema

### **Servidor de desarrollo:**
```bash
php -S 0.0.0.0:8081 -t public/
```

### **URLs de acceso:**
- 🏥 **Hospital:** http://melisahospital.localhost:8081
- 🌿 **La Colina:** http://melisalacolina.localhost:8081  
- 💻 **Wi Clinic:** http://melisawiclinic.localhost:8081
- 📖 **API Docs:** http://melisahospital.localhost:8081/api/docs
- 📚 **Examples:** http://melisahospital.localhost:8081/examples

---

## 🚀 API Platform

### **Endpoints Principales**
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/docs` | Documentación interactiva |
| `GET` | `/api/patients` | Lista pacientes del tenant |
| `GET` | `/api/patients/{id}` | Detalle paciente |
| `POST` | `/api/patients` | Crear paciente |

### **Headers Multi-Tenant**
```http
Content-Type: application/json
X-Tenant-Context: melisahospital
```

### **Ejemplo de uso:**
```bash
curl -H "X-Tenant-Context: melisahospital" \
     "http://melisahospital.localhost:8081/api/patients"
```

---

## ⚡ Controllers Stimulus

### **Uso en Templates**

#### **Controllers Internos (formularios):**
```html
<div data-controller="internal--patient"
     data-internal--patient-validate-on-change-value="true">
    <input data-internal--patient-target="name">
    <button data-action="click->internal--patient#save">Guardar</button>
</div>
```

#### **Controllers API Platform:**
```html
<div data-controller="apiplatform--api-patient"
     data-apiplatform--api-patient-tenant-value="melisahospital">
    <div data-apiplatform--api-patient-target="patientList"></div>
    <button data-action="click->apiplatform--api-patient#loadPatients">Cargar</button>
</div>
```

### **Características por Tenant:**

- **🏥 Hospital:** Controllers base con funcionalidad médica estándar
- **� La Colina:** Especialidades médicas, seguros, estilos verdes
- **💻 Wi Clinic:** IoT, telemetría, blockchain, IA, estilos tecnológicos

---

## 📚 Ejemplos Interactivos

Visita `/examples` para ver demos funcionando:

- **API Platform Demo:** Integración completa con State Providers
- **Internal Controllers Demo:** Formularios con validación en tiempo real
- **Multi-tenant Testing:** Prueba fallbacks por subdomain

---

## 🛠️ Comandos del Sistema

### 📊 **Comandos Multi-Tenant**

#### 🚀 **Migración Automática Multi-Tenant**
```bash
php bin/console app:migrate-tenant [opciones]
```

**Descripción**: Comando personalizado que aplica migraciones automáticamente a todas las bases de datos de tenants registrados en el sistema.

**Opciones:**
- `--dry-run` - Simula la ejecución sin aplicar cambios reales
- `--force` - Ejecuta las migraciones en todas las bases de datos
- `--generate-only` - Solo genera nuevas migraciones sin aplicarlas

**Funcionalidades:**
- ✅ **Detección automática** de tenants activos desde `melisa_central`
- ✅ **Generación automática** de migraciones basadas en entidades
- ✅ **Aplicación simultánea** a múltiples bases de datos
- ✅ **Sistema dinámico** que lee archivos de migración automáticamente
- ✅ **Manejo de errores** esperados (tablas existentes, claves duplicadas)
- ✅ **Registro de versiones** en `doctrine_migration_versions`

**Ejemplos:**

```bash
# Simulación (recomendado antes de ejecutar)
php bin/console app:migrate-tenant --dry-run

# Ejecución real en todas las bases de datos
php bin/console app:migrate-tenant --force

# Solo generar migraciones nuevas
php bin/console app:migrate-tenant --generate-only
```

**Salida del comando:**
```
🚀 Migración Automática Multi-Tenant
====================================

📊 Resumen de Migración Automática
----------------------------------
 Modo de ejecución        🔄 EJECUCIÓN REAL  
 Total tenants activos    3                  
 Directorio migraciones   ./migrations/      
 Entidades detectadas     6                  

📋 Tenants que serán procesados:
   • Clínica La Colina (melisalacolina) → BD: melisalacolina
   • Clínica Wiclinic (melisawiclinic) → BD: melisawiclinic
   • Hospital Central (melisahospital) → BD: melisahospital

🚀 Aplicando Migraciones a Todos los Tenants
--------------------------------------------
 📋 Procesando [1/3]: Clínica La Colina (melisalacolina)
     ✅ Tabla member creada exitosamente
 📋 Procesando [2/3]: Clínica Wiclinic (melisawiclinic)
     ✅ Tabla member creada exitosamente
 📋 Procesando [3/3]: Hospital Central (melisahospital)
     ✅ Tabla member creada exitosamente

📈 Resultados Finales
---------------------
  ✅ Exitosos           3     
  ❌ Fallidos           0     
  📊 Total procesados   3     
  🎯 Tasa de éxito      100%  

🎉 Todas las migraciones fueron aplicadas exitosamente a todos los tenants!
```

**Bases de datos soportadas:**
- `melisalacolina` - Base de datos Clínica La Colina
- `melisawiclinic` - Base de datos Wi Clinic
- `melisahospital` - Base de datos Hospital Central

### 🔧 **Comandos Symfony Estándar**

#### **Base de Datos:**
```bash
# Crear base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Generar migración
php bin/console doctrine:migrations:diff

# Ver estado de migraciones
php bin/console doctrine:migrations:status

# Crear entidad
php bin/console make:entity
```

#### **Cache y Desarrollo:**
```bash
# Limpiar cache
php bin/console cache:clear

# Limpiar cache específico
php bin/console cache:clear --env=prod

# Ver rutas disponibles
php bin/console debug:router

# Debug configuración
php bin/console debug:config
```

#### **Assets y Frontend:**
```bash
# Compilar assets
php bin/console asset-map:compile

# Ver asset mapping
php bin/console debug:asset-map

# Limpiar assets compilados
rm -rf public/assets/
```

#### **API Platform:**
```bash
# Debug configuración API Platform
php bin/console debug:config api_platform

# Ver recursos API
php bin/console api:debug

# Generar documentación OpenAPI
php bin/console api:openapi:export
```

### 🐛 **Comandos de Debug**

#### **Multi-Tenant Debug:**
```bash
# Ver configuración tenant actual
php bin/console debug:container | grep tenant

# Debug tenant context
php bin/console debug:container tenant.context

# Ver servicios de tenant
php bin/console debug:container tenant.resolver
```

#### **Verificación de Sistema:**
```bash
# Verificar configuración de base de datos
php bin/console doctrine:schema:validate

# Ver información del entorno
php bin/console about

# Debug configuración de seguridad
php bin/console debug:config security
```

### 📊 **Comandos de Monitoreo**

#### **Estado del Sistema:**
```bash
# Ver estado de las migraciones por tenant
mysql -u root -p123456 -e "
SELECT 
    'melisalacolina' as tenant,
    COUNT(*) as migraciones_ejecutadas
FROM melisalacolina.doctrine_migration_versions
UNION ALL
SELECT 
    'melisawiclinic' as tenant,
    COUNT(*) as migraciones_ejecutadas  
FROM melisawiclinic.doctrine_migration_versions
UNION ALL
SELECT 
    'melisahospital' as tenant,
    COUNT(*) as migraciones_ejecutadas
FROM melisahospital.doctrine_migration_versions;"
```

#### **Verificación de Tablas:**
```bash
# Verificar tabla member en todos los tenants
mysql -u root -p123456 -e "
SELECT 'melisalacolina' as tenant, COUNT(*) as member_table_exists
FROM information_schema.tables 
WHERE table_schema='melisalacolina' AND table_name='member'
UNION ALL
SELECT 'melisawiclinic' as tenant, COUNT(*) as member_table_exists
FROM information_schema.tables 
WHERE table_schema='melisawiclinic' AND table_name='member'
UNION ALL
SELECT 'melisahospital' as tenant, COUNT(*) as member_table_exists
FROM information_schema.tables 
WHERE table_schema='melisahospital' AND table_name='member';"
```

### 🚀 **Comandos de Deployment**

#### **Preparación para Producción:**
```bash
# Optimizar autoloader
composer dump-autoload --optimize --classmap-authoritative

# Compilar assets para producción
php bin/console asset-map:compile --env=prod

# Limpiar cache de producción
php bin/console cache:clear --env=prod

# Optimizar cache
php bin/console cache:warmup --env=prod
```

#### **Backup y Restore:**
```bash
# Backup de todas las bases de datos de tenants
mysqldump -u root -p123456 --databases melisalacolina melisawiclinic melisahospital melisa_central > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore de backup
mysql -u root -p123456 < backup_20251017_120000.sql
```

---

## 🛠️ Comandos Útiles

```bash
# Limpiar cache
php bin/console cache:clear

# Ver rutas
php bin/console debug:router

# Compilar assets (después de cambios JS/CSS)
php bin/console asset-map:compile

# Verificar controllers Stimulus
php bin/console debug:asset-map
```

---

## � Estructura del Proyecto

```
melisa_tenant/
├── 📂 src/
│   ├── Controller/          # Controllers PHP por tenant
│   ├── Entity/              # Entities con API Platform
│   ├── Service/             # TenantResolver, TenantContext
│   └── State/               # State Providers multi-tenant
├── 📂 assets/
│   ├── controllers/         # Controllers Stimulus con fallback
│   ├── app.js              # Dynamic Controller Loader
│   └── styles/             # CSS
├── 📂 templates/
│   ├── dashboard/          # Templates por tenant
│   └── examples/           # Demos interactivos
├── 📂 config/
│   ├── packages/           # Configuración bundles
│   └── routes.yaml         # Rutas principales
└── 📂 docs/                # Documentación técnica
```

---

## 🧪 Testing

### **Datos de Prueba:**
- **Usuario:** admin / **Password:** password

### **Verificar Multi-tenant:**
1. Acceder a diferentes subdominios
2. Verificar que cada uno muestra su dashboard específico
3. Probar API con diferentes headers `X-Tenant-Context`

---

## 🐛 Troubleshooting

### **Controller no encontrado:**
```
🎮 [Dynamic Loader] ❌ Controller no encontrado: internal--patient
```
**Solución:** Verificar que existe `internal/[subdomain]/patient_controller.js` o `internal/default/patient_controller.js`

### **Subdomain no resuelve:**
**Linux/Mac:**
```bash
# Verificar hosts
cat /etc/hosts | grep localhost
```

**Windows:**
```cmd
# Verificar hosts
type C:\Windows\System32\drivers\etc\hosts | findstr localhost
```

### **Assets no cargan:**
```bash
# Recompilar assets
php bin/console asset-map:compile
rm -rf public/assets/  # Eliminar cache
```

---

## 🤝 Contribución

1. Fork del proyecto
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar funcionalidad'`)
4. Push (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

### **Estándares:**
- **PSR-12** para PHP
- **Symfony Best Practices**
- **Comentarios en español**
- **Controllers Stimulus** siguiendo nueva estructura

---

## � Soporte

### **Documentación:**
- `/api/docs` - API interactiva
- `/examples` - Demos en vivo  
- `docs/` - Documentación técnica completa

### **Debug útil:**
```bash
# Ver configuración tenant
php bin/console debug:container | grep tenant

# Verificar asset mapping
php bin/console debug:asset-map

# Ver información de controllers Stimulus
console.log(DynamicControllerLoader.getDebugInfo())
```

---

🩺 **Desarrollado por RayenSalud para revolucionar la gestión médica digital**

## 🚀 API Platform - REST API Moderna

### � Configuración API (`api_platform.yaml`)
```yaml
api_platform:
    title: 'Melisa Medical API - Sistema Multi-tenant'
    description: 'API REST para gestión médica hospitalaria y clínicas'
    version: 1.0.0
    
    # Multi-tenancy support
    defaults:
        stateless: true
        cache_headers:
            vary: ['Content-Type', 'Authorization', 'Origin', 'X-Tenant-Context']
    
    # Documentación automática
    swagger:
        versions: [3]
        api_keys:
            tenant:
                name: X-Tenant-Context
                type: header
    
    # Formatos médicos soportados
    formats:
        jsonld: ['application/ld+json']  # JSON-LD para datos estructurados
        json: ['application/json']       # JSON estándar
        html: ['text/html']             # Documentación web
        xml: ['application/xml']        # XML para interoperabilidad
        csv: ['text/csv']               # CSV para exportaciones
    
    # Paginación optimizada
    collection:
        pagination:
            enabled: true
            items_per_page: 20
            maximum_items_per_page: 100
```

### 🔗 Endpoints Disponibles
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api` | Documentación interactiva API |
| `GET` | `/api/patients` | Lista de pacientes (paginada) |
| `GET` | `/api/patients/{id}` | Detalle de paciente específico |
| `POST` | `/api/patients` | Crear nuevo paciente |
| `PUT/PATCH` | `/api/patients/{id}` | Actualizar paciente |
| `DELETE` | `/api/patients/{id}` | Eliminar paciente |

### � Headers Multi-Tenant
```http
X-Tenant-Context: melisahospital
X-Hospital-ID: hospital-001
Content-Type: application/json
Authorization: Bearer {token}
```

---

## 🌐 CORS - Cross-Origin Resource Sharing

### ⚙️ Configuración CORS (`nelmio_cors.yaml`)
```yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization', 'X-Tenant-Context', 'X-Hospital-ID']
        expose_headers: ['Link', 'X-Total-Count', 'X-Tenant-Name']
        max_age: 3600
    
    # Configuración específica para API
    paths:
        '^/api':
            allow_origin: ['*']
            allow_headers: ['Content-Type', 'Authorization', 'X-Tenant-Context', 'X-Hospital-ID']
            allow_methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
```

### 🎯 Casos de Uso CORS
- **App móvil del hospital** → API backend
- **Dashboard web** → API de pacientes  
- **Sistema de emergencias** → API de historias clínicas
- **Aplicación de farmacia** → API de medicamentos

---

## 🆔 UUID - Identificadores Únicos Seguros

### ⚙️ Configuración UUID (`uid.yaml`)
```yaml
framework:
    uid:
        default_uuid_version: 7      # UUIDs ordenables por tiempo
        time_based_uuid_version: 7   # Para auditoría médica
```

### 🔐 Ventajas en Sistemas Médicos
- **Privacidad**: IDs no adivinables para historias clínicas
- **Seguridad**: Imposible enumerar pacientes secuencialmente
- **Distribución**: Sin conflictos entre diferentes hospitales
- **Auditoría**: UUIDs v7 ordenables por tiempo de creación

**Ejemplo:**
```php
// ❌ ID tradicional (inseguro)
$patient->id = 12345;  // Fácil de adivinar el siguiente

// ✅ UUID v7 (seguro)
$patient->id = "01H9Z8K7D2QS7A3B1C4F5G6H8J";  // Imposible de adivinar
```

---

## ⚡ Stimulus - Frontend Interactivo

### 🎮 Controladores JavaScript
El sistema incluye controladores Stimulus para interactividad sin recargar página:

```javascript
// patient_controller.js
export default class extends Controller {
    static targets = ["info", "name", "status", "age", "phone", "address"]
    static values = { patientId: Number, apiUrl: String }
    
    async showInfo() {
        const response = await fetch(`${this.apiUrlValue}/${this.patientIdValue}`)
        const data = await response.json()
        this.displayPatientData(data.patient)
    }
}
```

### 📋 Funcionalidades Implementadas
- **Búsqueda en tiempo real** de pacientes
- **Carga asíncrona** de datos médicos
- **Formularios interactivos** sin refrescar página
- **Indicadores de carga** y manejo de errores

---

## 🛠️ Stack Tecnológico

### Backend
- **Framework**: Symfony 6.4
- **API**: API Platform 4.2
- **Base de Datos**: MySQL 8.0
- **PHP**: 8.1+
- **Autenticación**: Symfony Security

### Frontend
- **CSS Framework**: Bootstrap 5.3
- **JavaScript**: Stimulus 3.2
- **Icons**: Font Awesome 6
- **Build Tool**: Symfony AssetMapper

### DevOps
- **Servidor Web**: Apache 2.4 con VirtualHost wildcard
- **CORS**: Nelmio CORS Bundle
- **UUID**: Symfony UID Component

---

## 📁 Estructura del Proyecto

```
melisa_tenant/
├── 🎯 API & Controllers
│   ├── src/Controller/
│   │   ├── Api/
│   │   │   └── PatientApiController.php    # API REST de pacientes
│   │   ├── Dashboard/
│   │   │   ├── Default/                    # Controllers dashboard por defecto
│   │   │   ├── Melisahospital/            # Controllers para hospital
│   │   │   └── Melisalacolina/            # Controllers para clínica
│   │   ├── AbstractTenantController.php    # Base para controllers multi-tenant
│   │   └── LoginController.php            # Autenticación
│   │
├── 🏗️ Services & Logic
│   ├── src/Service/
│   │   ├── TenantContext.php              # Gestión de contexto multi-tenant
│   │   ├── TenantResolver.php             # Resolución de tenants por subdomain
│   │   └── DynamicControllerResolver.php  # Routing dinámico por tenant
│   │
├── 📄 Templates & Views
│   ├── templates/
│   │   ├── dashboard/
│   │   │   ├── default/                   # Templates dashboard por defecto
│   │   │   ├── melisahospital/           # Templates dashboard hospital
│   │   │   └── melisalacolina/           # Templates dashboard clínica
│   │   ├── login/form.html.twig          # Login multi-tenant
│   │   └── base.html.twig                # Template base con navbar
│   │
├── ⚡ Frontend Assets
│   ├── assets/
│   │   ├── controllers/
│   │   │   ├── patient_controller.js      # Stimulus para pacientes
│   │   │   └── hello_controller.js        # Controlador base
│   │   ├── app.js                        # JavaScript principal
│   │   └── styles/app.css                # Estilos CSS
│   │
├── ⚙️ Configuration
│   ├── config/
│   │   ├── packages/
│   │   │   ├── api_platform.yaml         # Config API Platform
│   │   │   ├── nelmio_cors.yaml          # Config CORS
│   │   │   ├── uid.yaml                  # Config UUIDs
│   │   │   ├── doctrine.yaml             # Config base de datos
│   │   │   └── security.yaml             # Config autenticación
│   │   ├── routes/
│   │   │   └── api_platform.yaml         # Rutas API (/api)
│   │   └── routes.yaml                   # Rutas principales
│   │
└── 🚀 Public & Entry
    └── public/
        └── index.php                     # Entry point
```

---

## 🚀 Instalación y Configuración

### 📋 Prerrequisitos
- PHP 8.1 o superior
- MySQL 8.0
- Composer
- Apache con mod_rewrite
- Node.js (para assets)

### 1. 📥 Clonar el repositorio
```bash
git clone https://tfs.rayensalud.com:8080/tfs/RayenSalud/Melisa/_git/MelisaTenant melisa_tenant
cd melisa_tenant
```

### 2. 📦 Instalar dependencias
```bash
composer install
```

### 3. 🗄️ Configurar base de datos
```bash
# Copiar archivo de configuración
cp .env .env.local

# Editar configuración de base de datos en .env.local
# DATABASE_URL="mysql://melisa:melisamelisa@127.0.0.1:3306/melisa_central"
# CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1|.*\.melisaupgrade\.prod)(:[0-9]+)?$'
```

### 4. 🌐 Configurar Apache VirtualHost
```apache
<VirtualHost *:8081>
    ServerName melisaupgrade.prod
    ServerAlias *.melisaupgrade.prod
    DocumentRoot /var/www/html/melisa_tenant/public
    
    <Directory /var/www/html/melisa_tenant/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Headers para API Platform
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Tenant-Context"
</VirtualHost>
```

### 5. 🖥️ Configurar hosts (desarrollo)
```bash
echo "127.0.0.1 melisawiclinic.melisaupgrade.prod" >> /etc/hosts
echo "127.0.0.1 melisalacolina.melisaupgrade.prod" >> /etc/hosts
echo "127.0.0.1 melisahospital.melisaupgrade.prod" >> /etc/hosts
```

### 6. 🗄️ Configurar base de datos
```bash
# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Crear datos de prueba (opcional)
php bin/console doctrine:fixtures:load
```

---

## 🎯 Uso del Sistema

### 🌐 Acceso por Subdominios
| Tipo | URL | Puerto |
|------|-----|---------|
| **🏥 Hospital** | https://melisahospital.melisaupgrade.prod:8081 | 8081 |
| **🌿 Clínica** | https://melisalacolina.melisaupgrade.prod:8081 | 8081 |
| **💙 Default** | https://melisawiclinic.melisaupgrade.prod:8081 | 8081 |
| **📡 API Docs** | https://melisahospital.melisaupgrade.prod:8081/api | 8081 |

### 🏥 Funcionalidades por Tenant

#### 🏥 Dashboard Hospital
- **🚨 Centro de emergencias** en tiempo real
- **🏩 Gestión de quirófanos** (8 salas)
- **💓 Monitoreo UCI/UTI** (15 camas)
- **🧪 Laboratorio** 24 horas
- **💊 Farmacia** hospitalaria
- **📋 API REST** para pacientes

#### 🌿 Dashboard Clínica
- **📅 Gestión de citas** médicas
- **👥 Control de pacientes** ambulatorios
- **👨‍⚕️ Especialidades** médicas
- **📈 Timeline** de actividades
- **🩺 Medicina** preventiva

#### 💙 Dashboard Default
- **⚕️ Funcionalidades básicas** de clínica
- **🎨 Interfaz estándar** personalizable
- **👤 Gestión general** de pacientes

---

## 🧪 Testing y Desarrollo

### � Usuarios de Prueba
```bash
# Admin
Usuario: admin / Password: password

# Doctor
Usuario: doctor1 / Password: password

# Enfermera
Usuario: enfermera1 / Password: password
```

### 🧪 Datos de Prueba API
```json
// GET /api/patients/12345
{
  "id": 12345,
  "name": "Juan Pérez González",
  "age": 45,
  "status": "Activo",
  "bloodType": "O+",
  "allergies": ["Penicilina", "Mariscos"]
}
```

### 📝 Comandos Útiles

```bash
# 🧹 Limpiar cache
php bin/console cache:clear

# 🛣️ Ver rutas (incluye API)
php bin/console debug:router

# ⚙️ Verificar configuración
php bin/console debug:config api_platform

# 🗄️ Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# 🔍 Debug multi-tenant
php bin/console debug:container tenant

# 🚀 Servidor de desarrollo
php -S localhost:8000 -t public/
```

---

## �🔧 Desarrollo Avanzado

### 🏗️ Agregar Nuevo Tenant
1. **Controller**: Crear en `src/Controller/Dashboard/{TenantName}/`
2. **Templates**: Crear en `templates/dashboard/{tenantname}/`
3. **Base de datos**: Registrar tenant en `melisa_central`
4. **Apache**: Configurar subdominio

### 📋 Estructura de Controller
```php
<?php
namespace App\Controller\Dashboard\{TenantName};

use App\Controller\AbstractTenantController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractTenantController
{
    #[Route('/dashboard', name: 'app_dashboard_{tenantname}')]
    public function index(): Response
    {
        $tenant = $this->getTenantContext();
        return $this->render('dashboard/{tenantname}/index.html.twig', [
            'tenant' => $tenant
        ]);
    }
}
```

### 🚀 Crear Nueva API Resource
```php
<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Delete()
    ]
)]
class Patient
{
    // Implementación de la entidad
}
```

---

## 🤝 Contribución

1. **Fork** el proyecto
2. **Crear rama feature** (`git checkout -b feature/nueva-funcionalidad`)
3. **Commit cambios** (`git commit -am 'Agregar nueva funcionalidad'`)
4. **Push** a la rama (`git push origin feature/nueva-funcionalidad`)
5. **Crear Pull Request**

### 📋 Estándares de Código
- **PSR-12** para PHP
- **Symfony Best Practices**
- **API Platform Guidelines**
- **Comentarios en español**

---

## 📞 Soporte y Documentación

### 📚 Documentación Adicional
- **API Interactive Docs**: `/api` (Swagger UI)
- **Symfony Profiler**: `/_profiler` (desarrollo)
- **API Platform Admin**: Configuración avanzada

### 🐛 Troubleshooting
```bash
# Verificar configuración CORS
curl -H "Origin: http://localhost:3000" -I http://localhost:8000/api

# Debug tenant resolution
php bin/console debug:container --parameter tenant.current

# Verificar rutas API
php bin/console debug:router --show-controllers api_
```

---

## 📄 Licencia

Este proyecto es propietario de **RayenSalud**.

---

**🩺 Desarrollado con ❤️ por el equipo de RayenSalud para revolucionar la gestión médica digital**