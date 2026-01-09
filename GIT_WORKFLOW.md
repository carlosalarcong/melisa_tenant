# 🔀 Git Workflow - Melisa Tenant

## 📋 Estrategia: Git Flow Profesional

Este proyecto utiliza **Git Flow** para mantener un flujo de trabajo ordenado y profesional.

---

## 🌳 Estructura de Branches

```text
master (producción)
  └── develop (integración)
       ├── feature/nombre-feature
       ├── feature/otra-feature
       └── bugfix/nombre-bug
```

### **Branches principales:**

#### 🟢 `master`

- **Propósito:** Código en producción
- **Protección:** Solo merge desde `develop` con Pull Request
- **Deploy:** Automático o manual a producción
- **Regla:** NUNCA commit directo

#### 🟡 `develop`

- **Propósito:** Integración y testing
- **Protección:** Solo merge desde features/bugfixes con Pull Request
- **Deploy:** Automático a staging
- **Regla:** Testing completo antes de merge a master

---

## 🚀 Flujo de Trabajo

### **1. Nueva Feature**

```bash
# Desde develop actualizado
git checkout develop
git pull origin develop

# Crear feature branch
git checkout -b feature/nombre-descriptivo

# Trabajar en la feature
git add .
git commit -m "feat: descripción del cambio"

# Push a GitHub
git push -u origin feature/nombre-descriptivo

# Crear Pull Request a develop
```

### **2. Testing en Develop**

```bash
# Después de merge a develop
git checkout develop
git pull origin develop

# Deploy automático a staging
# Ejecutar tests
php bin/phpunit
php bin/console cache:clear --env=prod

# Pruebas manuales en staging
```

### **3. Deploy a Producción**

```bash
# Solo si develop está estable
git checkout master
git pull origin master

# Crear Pull Request desde develop a master
# Revisar cambios
# Aprobar y mergear

# Deploy a producción
```

---

## 📝 Convenciones de Commits

Seguimos **Conventional Commits** para mantener un historial limpio:

### **Tipos de commits:**

- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `refactor:` Refactorización de código
- `docs:` Cambios en documentación
- `test:` Agregar o modificar tests
- `chore:` Tareas de mantenimiento
- `perf:` Mejoras de performance
- `style:` Cambios de formato (sin cambio de lógica)

### **Ejemplos:**

```bash
git commit -m "feat: agregar módulo de recaudación"
git commit -m "fix: corregir error en login multi-tenant"
git commit -m "refactor: traducir entidades al inglés"
git commit -m "docs: actualizar plan de migración"
git commit -m "test: agregar tests para TenantResolver"
```

---

## 🔒 Protección de Branches

### **Configuración recomendada en GitHub:**

#### master

- ✅ Require pull request reviews (1 aprobación mínimo)
- ✅ Require status checks to pass (CI/CD)
- ✅ No force push
- ✅ No delete branch

#### develop

- ✅ Require pull request reviews (opcional)
- ✅ Require status checks to pass
- ✅ No force push

---

## 🎯 Estado Actual del Proyecto

### **Branches Activos:**

| Branch                                  | Propósito          | Estado                    |
|-----------------------------------------|--------------------|---------------------------|
| `master`                                | Producción         | ✅ Estable (Symfony 6.4)  |
| `develop`                               | Integración        | ✅ Symfony 7.4.3 LTS      |
| `feature/upgrade-symfony-7.4`           | Migración SF7      | ✅ Mergeado a develop     |
| `feature/recaudacion`                   | Módulo Recaudación | 🟡 En desarrollo          |
| `feature/administrador-usuarios-bundle` | Admin Usuarios     | 🟡 En desarrollo          |

### **Próximos Pasos:**

1. 🔄 **Testing en Develop** - Verificar Symfony 7.4 en staging
2. ⏳ **Merge a Master** - Después de testing exhaustivo en develop
3. ⏳ **Deploy a Producción** - Con plan de rollback preparado

---

## 🛠️ Comandos Útiles

### **Ver branches:**

```bash
git branch -a                    # Todos los branches
git branch -vv                   # Con info de tracking
```

### **Limpiar branches:**

```bash
git branch -d feature/nombre     # Eliminar local
git push origin --delete feature/nombre  # Eliminar remoto
git fetch --prune                # Limpiar referencias
```

### **Actualizar desde remoto:**

```bash
git fetch origin                 # Traer cambios
git pull origin develop          # Actualizar develop
git rebase origin/develop        # Rebase sobre develop
```

### **Ver historial:**

```bash
git log --oneline --graph --all  # Gráfico de commits
git log --author="nombre"        # Commits por autor
git log --since="2 weeks ago"    # Últimas 2 semanas
```

---

## 📊 Flujo de Release

Cuando `develop` está listo para producción:

```bash
# 1. Crear release branch
git checkout develop
git checkout -b release/v1.0.0

# 2. Actualizar versiones y changelog
# Editar package.json, composer.json, etc.

# 3. Commit de release
git commit -m "chore: prepare release v1.0.0"

# 4. Merge a master
git checkout master
git merge release/v1.0.0 --no-ff
git tag -a v1.0.0 -m "Release v1.0.0"

# 5. Merge a develop
git checkout develop
git merge release/v1.0.0 --no-ff

# 6. Push
git push origin master --tags
git push origin develop

# 7. Eliminar release branch
git branch -d release/v1.0.0
```

---

## 🚨 Hotfixes (Bugs en Producción)

Para correcciones urgentes en producción:

```bash
# 1. Crear hotfix desde master
git checkout master
git checkout -b hotfix/descripcion-bug

# 2. Corregir bug
git commit -m "fix: corregir bug crítico en producción"

# 3. Merge a master
git checkout master
git merge hotfix/descripcion-bug --no-ff
git tag -a v1.0.1 -m "Hotfix v1.0.1"

# 4. Merge a develop
git checkout develop
git merge hotfix/descripcion-bug --no-ff

# 5. Push
git push origin master --tags
git push origin develop

# 6. Deploy inmediato a producción
```

---

## ✅ Checklist Pre-Merge a Master

Antes de mergear `develop` a `master`:

- [ ] Todos los tests pasan (unit, integration, e2e)
- [ ] Sin errores en logs de staging
- [ ] Performance verificada
- [ ] Migraciones de BD probadas
- [ ] Documentación actualizada
- [ ] Changelog generado
- [ ] Pull Request revisado y aprobado
- [ ] Plan de rollback preparado
- [ ] Stakeholders notificados

---

## 📞 Contacto

**Responsable Git Flow:** [Nombre]  
**Preguntas:** [Email o Slack]

---

**Última actualización:** 9 de enero de 2026
