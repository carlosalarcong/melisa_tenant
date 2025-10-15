# 🚀 Setup del Proyecto para el Equipo - Melisa Tenant

## 📋 **Pasos para configurar el proyecto desde TFS**

### 1️⃣ **Clonar el repositorio**
```bash
# Clonar desde TFS
git clone [URL_DEL_TFS] melisa_tenant
cd melisa_tenant
```

### 2️⃣ **Instalar dependencias de PHP**
```bash
# Instalar dependencias con Composer
composer install

# Si no tienen composer instalado:
# curl -sS https://getcomposer.org/installer | php
# sudo mv composer.phar /usr/local/bin/composer
```

### 3️⃣ **Configurar el entorno**
```bash
# Copiar archivo de configuración
cp .env .env.local

# Editar .env.local con configuración local
nano .env.local
```

**Configuración mínima en `.env.local`:**
```env
# Base de datos local
DATABASE_URL="postgresql://user:password@localhost:5432/melisa_db"
# O SQLite para desarrollo rápido:
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# Entorno de desarrollo
APP_ENV=dev
APP_DEBUG=1

# Secret (generar uno nuevo)
APP_SECRET=tu_secret_aqui
```

### 4️⃣ **Configurar la base de datos**
```bash
# Crear la base de datos
php bin/console doctrine:database:create

# Ejecutar las migraciones
php bin/console doctrine:migrations:migrate

# Opcional: Cargar datos de prueba
php bin/console doctrine:fixtures:load
```

### 5️⃣ **Configurar el servidor web**

#### **Opción A: Servidor PHP integrado (desarrollo rápido)**
```bash
# Puerto principal (melisahospital)
php -S 0.0.0.0:8081 -t public/
```

#### **Opción B: Docker Compose (recomendado)**
```bash
# Si tienen Docker
docker-compose up -d
```

#### **Opción C: Apache/Nginx virtual hosts**
```bash
# Configurar virtual hosts para:
# - melisahospital.localhost:8081
# - melisalacolina.localhost:8081  
# - melisawiclinic.localhost:8081
```

### 6️⃣ **Configurar hosts locales**
```bash
# Editar /etc/hosts (Linux/Mac) o C:\Windows\System32\drivers\etc\hosts (Windows)
sudo nano /etc/hosts

# Agregar estas líneas:
127.0.0.1 melisahospital.localhost
127.0.0.1 melisalacolina.localhost
127.0.0.1 melisawiclinic.localhost
```

### 7️⃣ **Compilar assets frontend (Stimulus)**
```bash
# Este proyecto usa Asset Mapper de Symfony (NO necesita npm/yarn)
php bin/console asset-map:compile

# Verificar que los assets están mapeados
php bin/console debug:asset-map
```

**⚠️ IMPORTANTE:** Este proyecto **NO usa npm/webpack**, usa **Asset Mapper** de Symfony 6.4+

---

## 🔧 **Verificar que API Platform funciona**

### 📊 **1. Verificar la instalación**
```bash
# Limpiar caché
php bin/console cache:clear

# Verificar rutas de API Platform
php bin/console debug:router | grep api
```

**Deberían ver rutas como:**
```
api_entrypoint           GET      /api/{index}.{_format}
api_doc                  GET      /api/docs.{_format}
api_jsonld_context       GET      /api/contexts/{shortName}.{_format}
_api_/patients_get_collection GET /api/patients
_api_/patients_get_item   GET      /api/patients/{id}
```

### 📊 **2. Probar los endpoints**
```bash
# Probar endpoint de pacientes con diferentes tenants
curl -H "X-Tenant-Context: melisahospital" \
     "http://melisahospital.localhost:8081/api/patients"

curl -H "X-Tenant-Context: melisalacolina" \
     "http://melisalacolina.localhost:8081/api/patients"

curl -H "X-Tenant-Context: melisawiclinic" \
     "http://melisawiclinic.localhost:8081/api/patients"
```

### 📊 **3. Verificar la documentación interactiva**
Visitar: `http://melisahospital.localhost:8081/api/docs`

### 📊 **4. Probar la integración Stimulus**
Visitar: `http://melisahospital.localhost:8081/dashboard/patients-api`

---

## 🐛 **Solución de problemas comunes**

### ❌ **Error: "No route found for GET /api"**
```bash
# Verificar que API Platform está instalado
composer show api-platform/core

# Reinstalar si es necesario
composer require api-platform/core
```

### ❌ **Error: Database connection**
```bash
# Verificar conexión a BD
php bin/console doctrine:schema:validate

# Recrear BD si es necesario
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### ❌ **Error: 500 en endpoints API**
```bash
# Ver logs detallados
tail -f var/log/dev.log

# Verificar permisos
sudo chown -R www-data:www-data var/
sudo chmod -R 775 var/
```

### ❌ **Error: Stimulus controllers no cargan**
```bash
# Verificar Asset Mapper
php bin/console debug:asset-map

# Recompilar assets
php bin/console asset-map:compile
```

### ❌ **Error: Multi-tenant no funciona**
```bash
# Verificar que el middleware está activo
php bin/console debug:container | grep tenant

# Verificar headers en requests
curl -v -H "X-Tenant-Context: melisahospital" \
     "http://localhost:8081/api/patients"
```

---

## 🎯 **URLs importantes para testing**

| Funcionalidad | URL | Headers necesarios |
|---------------|-----|-------------------|
| **API Docs** | http://melisahospital.localhost:8081/api/docs | - |
| **Pacientes API** | http://melisahospital.localhost:8081/api/patients | `X-Tenant-Context: melisahospital` |
| **Demo Stimulus** | http://melisahospital.localhost:8081/dashboard/patients-api | Login requerido |
| **Login** | http://melisahospital.localhost:8081/login | - |

---

## 👥 **Datos de prueba**

### 🏥 **Usuarios de prueba**
```php
// Crear usuario admin temporal
php bin/console security:hash-password
# Usar el hash generado en la BD
```

### 📋 **Pacientes de prueba**
Los State Providers generan automáticamente:
- **Hospital**: HSP001, HSP002, HSP003...
- **La Colina**: LC001, LC002, LC003...
- **Wi Clinic**: WC001, WC002, WC003...

---

## 📚 **Documentación adicional**

- **API Platform:** `docs/STIMULUS_INTEGRATION.md`
- **Multi-tenant:** `src/Service/TenantResolver.php`
- **State Providers:** `src/State/PatientStateProvider.php`
- **Controllers:** `assets/controllers/apiplatform/`

---

## 🚀 **Comandos útiles para desarrollo**

```bash
# Desarrollo diario
php bin/console cache:clear                    # Limpiar caché
php bin/console debug:router                   # Ver rutas
php bin/console debug:container | grep api    # Debug API Platform
php bin/console doctrine:schema:update --dump-sql  # Ver cambios BD

# Testing API
curl -H "X-Tenant-Context: melisahospital" "http://localhost:8081/api/patients"
curl -H "X-Tenant-Context: melisalacolina" "http://localhost:8081/api/patients"
curl -H "X-Tenant-Context: melisawiclinic" "http://localhost:8081/api/patients"

# Servidor local
php -S 0.0.0.0:8081 -t public/
```

---

*Guía actualizada: Octubre 15, 2025*
*Proyecto: Melisa Tenant Multi-Platform*