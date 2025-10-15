# 🎯 API Platform - Checklist Rápido

## ✅ **Setup mínimo para que funcione API Platform**

### 1. **Clonar y configurar**
```bash
git clone [TFS_URL] melisa_tenant
cd melisa_tenant
composer install
cp .env .env.local
```

### 2. **Base de datos**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 3. **Servidor local**
```bash
# Agregar a /etc/hosts:
127.0.0.1 melisahospital.localhost
127.0.0.1 melisalacolina.localhost  
127.0.0.1 melisawiclinic.localhost

# Ejecutar servidor:
php -S 0.0.0.0:8081 -t public/
```

### 4. **Probar API Platform**
- 📖 **Docs:** http://melisahospital.localhost:8081/api/docs
- 🧪 **Test:** `curl -H "X-Tenant-Context: melisahospital" "http://melisahospital.localhost:8081/api/patients"`

### 5. **Probar Stimulus**
```bash
# Compilar assets (NO usar npm)
php bin/console asset-map:compile
```
- 🎮 **Demo:** http://melisahospital.localhost:8081/dashboard/patients-api

---

## 🚨 **Problemas más comunes:**

| Problema | Solución |
|----------|----------|
| `No route found /api` | `composer require api-platform/core` |
| `Database connection` | Configurar `DATABASE_URL` en `.env.local` |
| `500 error` | `php bin/console cache:clear` |
| `Stimulus no carga` | `php bin/console asset-map:compile` |

---

## 📞 **Comandos de emergencia**
```bash
# Reset completo
php bin/console cache:clear
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create  
php bin/console doctrine:migrations:migrate
php bin/console asset-map:compile
```