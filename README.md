# Melisa Tenant - Sistema Multi-Tenant de Gestión Médica

![Symfony](https://img.shields.io/badge/Symfony-6.4-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

## 📋 Descripción

**Melisa Tenant** es la aplicación principal del sistema multi-tenant de gestión médica Melisa. Proporciona dashboards personalizados y funcionalidades específicas para diferentes tipos de centros médicos (hospitales, clínicas, centros de atención primaria).

## 🏗️ Arquitectura Multi-Tenant

El sistema utiliza una arquitectura multi-tenant basada en subdominios, donde cada tenant tiene su propia experiencia personalizada:

- **🏥 Hospital Central** (`melisahospital.melisaupgrade.prod`) - Dashboard especializado para hospitales
- **🌿 Clínica La Colina** (`melisalacolina.melisaupgrade.prod`) - Dashboard optimizado para clínicas
- **💙 Melisa Clinic** (`melisawiclinic.melisaupgrade.prod`) - Dashboard por defecto

## ⚡ Características Principales

### 🔐 Sistema de Autenticación
- Autenticación simplificada con username/password
- Sesiones persistentes por tenant
- Gestión de usuarios por base de datos específica

### 🎨 Dashboards Personalizados
- **Hospital:** Interfaz oscura con gestión de emergencias, quirófanos y UCI
- **Clínica:** Interfaz clara con enfoque en citas y medicina general
- **Default:** Interfaz estándar para centros médicos genéricos

### 🌐 Multi-Tenant Routing
- Resolución automática de tenant por subdominio
- Controllers específicos por tenant
- Templates personalizados por organización

### 📱 Interfaz Responsive
- Bootstrap 5 para diseño adaptativo
- Font Awesome para iconografía médica
- Navbar superior con perfil de usuario

## 🛠️ Tecnologías

- **Backend:** Symfony 6.4
- **Base de Datos:** MySQL 8.0
- **Frontend:** Bootstrap 5, Font Awesome 6
- **Servidor Web:** Apache 2.4 con VirtualHost wildcard
- **PHP:** 8.1+

## 📁 Estructura del Proyecto

```
melisa_tenant/
├── src/
│   ├── Controller/
│   │   ├── Dashboard/
│   │   │   ├── Default/           # Controllers para dashboard por defecto
│   │   │   ├── Melisahospital/    # Controllers para hospital
│   │   │   └── Melisalacolina/    # Controllers para clínica
│   │   ├── AbstractTenantController.php
│   │   └── LoginController.php
│   └── Service/
│       ├── TenantContext.php      # Gestión de contexto multi-tenant
│       └── TenantResolver.php     # Resolución de tenants
├── templates/
│   ├── dashboard/
│   │   ├── default/               # Templates dashboard por defecto
│   │   ├── melisahospital/        # Templates dashboard hospital
│   │   └── melisalacolina/        # Templates dashboard clínica
│   ├── login/
│   └── base.html.twig            # Template base con navbar
├── config/
│   └── packages/
└── public/
```

## 🚀 Instalación y Configuración

### Prerrequisitos
- PHP 8.1 o superior
- MySQL 8.0
- Composer
- Apache con mod_rewrite

### 1. Clonar el repositorio
```bash
git clone https://tfs.rayensalud.com:8080/tfs/RayenSalud/Melisa/_git/MelisaTenant melisa_tenant
cd melisa_tenant
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar base de datos
```bash
# Copiar archivo de configuración
cp .env .env.local

# Editar configuración de base de datos
# DATABASE_URL="mysql://melisa:melisamelisa@127.0.0.1:3306/melisa_central"
```

### 4. Configurar Apache VirtualHost
```apache
<VirtualHost *:8081>
    ServerName melisaupgrade.prod
    ServerAlias *.melisaupgrade.prod
    DocumentRoot /var/www/html/melisa_tenant/public
    
    <Directory /var/www/html/melisa_tenant/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Configurar hosts (desarrollo)
```bash
echo "127.0.0.1 melisawiclinic.melisaupgrade.prod" >> /etc/hosts
echo "127.0.0.1 melisalacolina.melisaupgrade.prod" >> /etc/hosts
echo "127.0.0.1 melisahospital.melisaupgrade.prod" >> /etc/hosts
```

## 🗄️ Base de Datos

### Estructura Multi-Tenant
- **melisa_central:** Gestión de tenants y usuarios centralizados
- **melisalacolina:** Base de datos específica de la clínica
- **melisahospital:** Base de datos específica del hospital  
- **melisawiclinic:** Base de datos del tenant por defecto

### Credenciales por Defecto
```
Usuario: melisa
Password: password
```

## 🎯 Uso del Sistema

### Acceso por Subdominios
- **Hospital:** https://melisahospital.melisaupgrade.prod:8081
- **Clínica:** https://melisalacolina.melisaupgrade.prod:8081
- **Default:** https://melisawiclinic.melisaupgrade.prod:8081

### Funcionalidades por Tenant

#### 🏥 Dashboard Hospital
- Centro de emergencias en tiempo real
- Gestión de quirófanos (8 salas)
- Monitoreo UCI/UTI (15 camas)
- Laboratorio 24 horas
- Farmacia hospitalaria

#### 🌿 Dashboard Clínica
- Gestión de citas médicas
- Control de pacientes ambulatorios
- Especialidades médicas
- Timeline de actividades
- Medicina preventiva

#### 💙 Dashboard Default
- Funcionalidades básicas de clínica
- Interfaz estándar personalizable
- Gestión general de pacientes

## 🔧 Desarrollo

### Agregar Nuevo Tenant
1. Crear controller en `src/Controller/Dashboard/{TenantName}/`
2. Crear templates en `templates/dashboard/{tenantname}/`
3. Registrar tenant en base de datos central
4. Configurar subdominio en Apache

### Estructura de Controller
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
        // Lógica específica del tenant
    }
}
```

## 🧪 Testing

### Usuarios de Prueba
```
# Admin
Usuario: admin / Password: password

# Doctor
Usuario: doctor1 / Password: password

# Enfermera
Usuario: enfermera1 / Password: password
```

## 📝 Comandos Útiles

```bash
# Limpiar cache
php bin/console cache:clear

# Ver rutas
php bin/console debug:router

# Verificar configuración
php bin/console debug:config

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate
```

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📞 Soporte

- **Repositorio:** https://tfs.rayensalud.com:8080/tfs/RayenSalud/Melisa/_git/MelisaTenant
- **Documentación:** Ver docs/ folder
- **Issues:** Reportar en TFS

## 📄 Licencia

Este proyecto es propietario de RayenSalud.

---

**Desarrollado con ❤️ por el equipo de RayenSalud**