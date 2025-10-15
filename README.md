# 🏥 Melisa Tenant - Sistema Multi-Tenant de Gestión Médica

![Symfony](https://img.shields.io/badge/Symfony-6.4-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![API Platform](https://img.shields.io/badge/API%20Platform-4.2-success)
![Stimulus](https://img.shields.io/badge/Stimulus-3.2-yellow)

---

## 📋 Descripción

**Melisa Tenant** es la aplicación principal del sistema multi-tenant de gestión médica Melisa. Proporciona dashboards personalizados, APIs REST modernas y funcionalidades específicas para diferentes tipos de centros médicos (hospitales, clínicas, centros de atención primaria).

### ✨ Características Destacadas
- 🏗️ **Arquitectura Multi-Tenant** con resolución por subdominios
- 🚀 **API REST completa** con API Platform 4.2
- ⚡ **Frontend interactivo** con Stimulus JavaScript
- 🎨 **Dashboards personalizados** por tipo de centro médico
- 🔐 **Autenticación y autorización** por tenant
- 📱 **Interfaz responsive** con Bootstrap 5

---

## 🏗️ Arquitectura del Sistema

### 🌐 Multi-Tenant por Subdominios
El sistema utiliza una arquitectura multi-tenant basada en subdominios, donde cada tenant tiene su propia experiencia personalizada:

| Tenant | URL | Tipo | Descripción |
|--------|-----|------|-------------|
| **🏥 Hospital Central** | `melisahospital.melisaupgrade.prod` | Hospital | Dashboard especializado para hospitales |
| **🌿 Clínica La Colina** | `melisalacolina.melisaupgrade.prod` | Clínica | Dashboard optimizado para clínicas |
| **💙 Melisa Clinic** | `melisawiclinic.melisaupgrade.prod` | Default | Dashboard por defecto |

### 🗄️ Estructura de Base de Datos Multi-Tenant
- **`melisa_central`**: Gestión de tenants y usuarios centralizados
- **`melisalacolina`**: Base de datos específica de la clínica
- **`melisahospital`**: Base de datos específica del hospital  
- **`melisawiclinic`**: Base de datos del tenant por defecto

---

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