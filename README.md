# 🏥 Melisa Tenant - Sistema Multi-Tenant de Gestión Médica

![Symfony](https://img.shields.io/badge/Symfony-6.4-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![API Platform](https://img.shields.io/badge/API%20Platform-4.2-success)
![Stimulus](https://img.shields.io/badge/Stimulus-3.2-yellow)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)

**Melisa Tenant** es un sistema multi-tenant de gestión médica desarrollado con Symfony 6.4, API Platform y Stimulus. Cada clínica/hospital tiene su propio dashboard personalizado y base de datos independiente.

---

## 🎯 Instalación Rápida

### 📋 Prerrequisitos
- **PHP 8.1 o superior**
- **MySQL 8.0**
- **Composer**
- **Apache** con mod_rewrite

### 🚀 Pasos de Instalación

#### 1. **Clonar el repositorio**
```bash
git clone [URL_TFS] melisa_tenant
cd melisa_tenant
```

#### 2. **Instalar dependencias**
```bash
composer install
```

#### 3. **Configurar entornos**
```bash
# Crear archivos de configuración por entorno
cp .env .env.dev.local
cp .env .env.dev.test
cp .env .env.local
```

**Configuración `.env` (base):**
```env
# Configuración base del proyecto
APP_SECRET=change_me_in_production
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```

**Configuración `.env.dev.local` (desarrollo):**
```env
DATABASE_URL="mysql://melisa:melisamelisa@127.0.0.1:3306/melisa_central"
APP_ENV=dev
APP_DEBUG=1
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1|.*\.localhost)(:[0-9]+)?$'
```

**Configuración `.env.dev.test` (testing):**
```env
DATABASE_URL="mysql://melisa_test:melisa_test@127.0.0.1:3306/melisa_central_test"
APP_ENV=test
APP_DEBUG=0
CORS_ALLOW_ORIGIN='*'
```

**Configuración `.env.local` (local override):**
```env
# Configuraciones locales específicas del desarrollador
# Este archivo se ignora en git para configuraciones personales
APP_ENV=dev
APP_DEBUG=1
```

#### 4. **Configurar base de datos multi-tenant**
```bash
# Crear base de datos central
php bin/console doctrine:database:create

# Ejecutar migraciones multi-tenant (comando personalizado)
php bin/console app:migrate-tenant
```

**Nota:** El comando `app:migrate-tenant` es personalizado y realiza:
- Migración de la base de datos central `melisa_central`
- Creación automática de bases de datos por tenant
- Migración de esquemas específicos para cada tenant
- Configuración de conexiones dinámicas

#### 5. **Configurar hosts del sistema (Windows)**

**Windows:**
1. Abrir **Bloc de notas como Administrador**
2. Abrir: `C:\Windows\System32\drivers\etc\hosts`
3. Cambiar filtro a **"Todos los archivos (*.*)"**

Agregar estas líneas:
```
127.0.0.1 melisahospital.localhost
127.0.0.1 melisalacolina.localhost
127.0.0.1 melisawiclinic.localhost
```

#### 6. **Compilar assets**
```bash
php bin/console asset-map:compile
```

#### 7. **Ejecutar servidor**
```bash
php -S 0.0.0.0:8081 -t public/
```

#### 8. **Verificar instalación**
- 🏥 **Hospital:** http://melisahospital.localhost:8081
- 🌿 **La Colina:** http://melisalacolina.localhost:8081
- 💻 **Wi Clinic:** http://melisawiclinic.localhost:8081
- 📖 **API Docs:** http://melisahospital.localhost:8081/api/docs

---

## 🔧 Comando Multi-Tenant Personalizado

### 📋 **app:migrate-tenant**

Este comando personalizado automatiza la configuración completa de la base de datos multi-tenant:

```bash
php bin/console app:migrate-tenant
```

**Funcionalidades del comando:**
1. **Migración Central**: Ejecuta migraciones en `melisa_central`
2. **Creación de Tenants**: Crea automáticamente bases de datos por tenant:
   - `melisahospital_db`
   - `melisalacolina_db` 
   - `melisawiclinic_db`
3. **Migraciones por Tenant**: Ejecuta migraciones específicas en cada tenant
4. **Configuración Dinámica**: Configura conexiones de base de datos dinámicas
5. **Datos de Prueba**: Opcionalmente carga fixtures por tenant

**Parámetros disponibles:**
```bash
# Migrar solo un tenant específico
php bin/console app:migrate-tenant --tenant=melisahospital

# Recrear todas las bases de datos (cuidado en producción)
php bin/console app:migrate-tenant --reset

# Cargar datos de prueba después de migrar
php bin/console app:migrate-tenant --with-fixtures

# Ver qué haría sin ejecutar (dry-run)
php bin/console app:migrate-tenant --dry-run
```

---

## 🏗️ Arquitectura del Sistema

### 🌐 Multi-Tenant por Subdominios

| Tenant | URL | Descripción |
|--------|-----|-------------|
| **🏥 Hospital** | `melisahospital.localhost:8081` | Dashboard para hospitales |
| **🌿 La Colina** | `melisalacolina.localhost:8081` | Dashboard para clínicas |
| **💻 Wi Clinic** | `melisawiclinic.localhost:8081` | Dashboard tecnológico |

### 🗄️ Bases de Datos
- **`melisa_central`** - Gestión de tenants y configuración
- **Por tenant** - Base de datos independiente por cada clínica

### ⚡ Sistema de Controllers Stimulus
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

## 🚀 API Platform - REST API

### 📊 Configuración API
```yaml
api_platform:
    title: 'Melisa Medical API - Sistema Multi-tenant'
    description: 'API REST para gestión médica hospitalaria y clínicas'
    version: 1.0.0
    
    # Soporte multi-tenancy
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
```

### 🔗 Endpoints Principales
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/docs` | Documentación interactiva |
| `GET` | `/api/patients` | Lista pacientes del tenant |
| `GET` | `/api/patients/{id}` | Detalle paciente |
| `POST` | `/api/patients` | Crear paciente |
| `PUT/PATCH` | `/api/patients/{id}` | Actualizar paciente |
| `DELETE` | `/api/patients/{id}` | Eliminar paciente |

### 📡 Headers Multi-Tenant
```http
Content-Type: application/json
X-Tenant-Context: melisahospital
X-Hospital-ID: hospital-001
Authorization: Bearer {token}
```

### 📝 Ejemplo de uso
```bash
curl -H "X-Tenant-Context: melisahospital" \
     -H "Content-Type: application/json" \
     "http://melisahospital.localhost:8081/api/patients"
```

---

## 🧪 Testing y Datos de Prueba

### 👤 Usuarios de Prueba
```bash
Usuario: admin
Password: password
```

### 📋 Datos de Prueba API
Los State Providers generan automáticamente:
- **Hospital**: HSP001, HSP002, HSP003...
- **La Colina**: LC001, LC002, LC003...
- **Wi Clinic**: WC001, WC002, WC003...

### 🧪 Verificar Multi-tenant
```bash
# Probar diferentes tenants
curl -H "X-Tenant-Context: melisahospital" "http://melisahospital.localhost:8081/api/patients"
curl -H "X-Tenant-Context: melisalacolina" "http://melisalacolina.localhost:8081/api/patients"
curl -H "X-Tenant-Context: melisawiclinic" "http://melisawiclinic.localhost:8081/api/patients"
```

---

## 🛠️ Comandos Útiles

```bash
# 🧹 Limpiar cache
php bin/console cache:clear

# 🛣️ Ver rutas
php bin/console debug:router

# ⚙️ Verificar configuración API Platform
php bin/console debug:config api_platform

# 🗄️ Ejecutar migraciones multi-tenant
php bin/console app:migrate-tenant

# 🔍 Debug multi-tenant
php bin/console debug:container | grep tenant

# 🎮 Verificar assets Stimulus
php bin/console debug:asset-map

# 🚀 Compilar assets (después de cambios JS/CSS)
php bin/console asset-map:compile
```

---

## 🐛 Solución de Problemas

### ❌ **Error: "No route found for GET /api"**
```bash
# Verificar que API Platform está instalado
composer show api-platform/core

# Reinstalar si es necesario
composer require api-platform/core

# Limpiar cache
php bin/console cache:clear
```

### ❌ **Error: Database connection**
```bash
# Verificar configuración en .env.dev.local
# DATABASE_URL="mysql://usuario:password@127.0.0.1:3306/melisa_central"

# Verificar conexión
php bin/console doctrine:schema:validate

# Ejecutar migraciones multi-tenant
php bin/console app:migrate-tenant
```

### ❌ **Error: Subdomain no resuelve**

**Windows:**
```cmd
# Verificar hosts
type C:\Windows\System32\drivers\etc\hosts | findstr localhost

# Limpiar DNS
ipconfig /flushdns
```

### ❌ **Error: Assets/Controllers Stimulus no cargan**
```bash
# Recompilar assets
php bin/console asset-map:compile

# Eliminar cache de assets
rm -rf public/assets/

# Verificar mapping
php bin/console debug:asset-map
```

---

### 📧 Contacto
- **Documentación**: Ver carpeta `docs/` para guías técnicas
- **API Testing**: Usar `/api/docs` para pruebas interactivas
- **Debug**: Usar `/_profiler` en desarrollo

---

**🩺 Desarrollado con ❤️ por el equipo de RayenSalud para revolucionar la gestión médica digital**

---

*Manual de instalación consolidado - Octubre 2025*
*Proyecto: Melisa Tenant Multi-Platform*
