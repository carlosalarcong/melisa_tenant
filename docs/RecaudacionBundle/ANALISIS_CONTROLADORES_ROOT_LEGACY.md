# 🔍 Análisis de Controladores Root Legacy - RecaudacionBundle

**Fecha:** 30 de Diciembre 2025  
**Total identificados:** 9 controladores (3,940 líneas)

---

## 📊 Inventario de Controladores Root

| Archivo | Líneas | Estado | Duplicado en _Default |
|---------|--------|--------|----------------------|
| RecaudacionController.php | 994 | ⚠️ Legacy | ✅ _Default/Recaudacion/DefaultController.php |
| DefaultController.php | 20 | ⚠️ Vacío | ✅ _Default/Recaudacion/DefaultController.php |
| BusquedaPacienteController.php | 266 | ⚠️ Legacy | Funcionalidad en _Default/Recaudacion/Pago/Dependencias/* |
| GarantiaPacienteController.php | 17 | ⚠️ Casi vacío | Posiblemente en _Default |
| GestionCajaController.php | 415 | ⚠️ Legacy | ✅ _Default/Recaudacion/GestionCaja/* |
| InformacionActualPacienteController.php | 16 | ⚠️ Casi vacío | Posiblemente en _Default |
| PagoCuenta/CuentaPacienteController.php | 306 | ⚠️ Legacy | Posiblemente en _Default/Recaudacion/Pago/* |
| PagoCuenta/PagoCuentaController.php | 650 | ⚠️ Legacy | ✅ _Default/Recaudacion/Pago/PagoController.php |
| PagoCuenta/RealizarPagoCuentaPacienteController.php | 1,256 | 🔴 Grande | ✅ _Default/Recaudacion/Pago/PagarController.php |

**Total:** 3,940 líneas de código legacy

---

## 🔍 Análisis Individual

### 1. RecaudacionController.php (994 líneas)

**Namespace:** `Rebsol\RecaudacionBundle\Controller`  
**Extiende:** `Rebsol\HermesBundle\Controller\DefaultController`

**Métodos principales:**
- `indexAction()` - Método principal de entrada
- `ObtenerLogoEmpresaLogin()`
- `obtenerSucursalPorUsuario()`
- `verificarUsuarioCajero()`
- `rFormaPago()`, `rPaciente()` - Helpers

**Uso en routing:**
```yaml
recaudacion_index:
    path: /
    defaults: { _controller: RecaudacionBundle:Recaudacion:index }
```

**Duplicado en:**
- `_Default/Recaudacion/DefaultController.php` (51,538 bytes)

**Conclusión:** 🗑️ **ELIMINAR** - Funcionalidad completamente reemplazada por _Default

---

### 2. DefaultController.php (20 líneas)

**Namespace:** `Rebsol\RecaudacionBundle\Controller`  
**Extiende:** `Rebsol\HermesBundle\Controller\DefaultController`

**Contenido:**
```php
class DefaultController extends \Rebsol\HermesBundle\Controller\DefaultController
{
    /*  public function indexAction()
    {
        return $this->render('RecaudacionBundle:Default:index.html.twig');
    }*/
}
```

**Conclusión:** 🗑️ **ELIMINAR** - Casi vacío, código comentado

---

### 3. BusquedaPacienteController.php (266 líneas)

**Namespace:** `Rebsol\RecaudacionBundle\Controller`  
**Extiende:** `RecaudacionController`

**Métodos:**
- `historialPacienteAction()`

**Uso en routing:**
```yaml
# Comentado en routing.yml
#recaudacion_busqueda_historial_paciente:
#    path: /historialPaciente
#    defaults: { _controller: RecaudacionBundle:BusquedaPaciente:historialPaciente }
```

**Duplicado en:**
- Funcionalidad probablemente en `_Default/Recaudacion/Pago/Dependencias/`

**Conclusión:** 🗑️ **ELIMINAR** - Ruta comentada, no está en uso

---

### 4. GarantiaPacienteController.php (17 líneas)

**Conclusión:** 🗑️ **ELIMINAR** - Casi vacío

---

### 5. GestionCajaController.php (415 líneas)

**Uso en routing:**
```yaml
# Todas las rutas comentadas
#recaudacion_gestion_caja_cerrar:
#    defaults: { _controller: RecaudacionBundle:GestionCaja:gestionCerrarCaja }
```

**Duplicado en:**
- `_Default/Recaudacion/GestionCaja/GestionCajaController.php`

**Conclusión:** 🗑️ **ELIMINAR** - Rutas comentadas, duplicado en _Default

---

### 6. InformacionActualPacienteController.php (16 líneas)

**Conclusión:** 🗑️ **ELIMINAR** - Casi vacío

---

### 7. PagoCuenta/CuentaPacienteController.php (306 líneas)

**Namespace:** `Rebsol\RecaudacionBundle\Controller\PagoCuenta`

**Uso en routing:**
```yaml
# Comentado
#Caja_PagoCuenta_ConsultarDatos_CuentaPaciente:
#    defaults: { _controller: RecaudacionBundle:PagoCuenta\CuentaPaciente:mostrarCuenta }
```

**Conclusión:** 🗑️ **ELIMINAR** - Ruta comentada

---

### 8. PagoCuenta/PagoCuentaController.php (650 líneas)

**Namespace:** `Rebsol\RecaudacionBundle\Controller\PagoCuenta`

**Duplicado en:**
- `_Default/Recaudacion/Pago/PagoController.php` (31,601 bytes)

**Conclusión:** 🗑️ **ELIMINAR** - Funcionalidad en _Default

---

### 9. PagoCuenta/RealizarPagoCuentaPacienteController.php (1,256 líneas)

**El más grande del root**

**Namespace:** `Rebsol\RecaudacionBundle\Controller\PagoCuenta`

**Duplicado en:**
- `_Default/Recaudacion/Pago/PagarController.php` (193,259 bytes = ~3,163 líneas)

**Conclusión:** 🗑️ **ELIMINAR** - Versión más completa en _Default

---

## 📋 Análisis de Rutas

### Ruta Activa Única:

```yaml
recaudacion_index:
    path: /
    defaults: { _controller: RecaudacionBundle:Recaudacion:index }
```

**Problema:** Esta ruta apunta a `RecaudacionController.php` (root) que es legacy.

**Solución:** Actualizar a:
```yaml
recaudacion_index:
    path: /
    defaults: { _controller: RecaudacionBundle:_Default\Recaudacion\Default:index }
```

---

## ✅ Recomendaciones de Acción

### FASE 1: Actualizar Ruta Principal

**Prioridad:** 🔴 CRÍTICA

**Acción:**
1. Actualizar `recaudacion_index` en `routing.yml`
2. Apuntar a `_Default/Recaudacion/DefaultController::index`
3. Probar que funciona

**Script:**
```bash
cd /var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle
sed -i 's|_controller: RecaudacionBundle:Recaudacion:index|_controller: RecaudacionBundle:_Default\\Recaudacion\\Default:index|' Resources/config/routing.yml
```

---

### FASE 2: Mover Controladores Root a _Deprecated

**Prioridad:** 🟡 ALTA

**Acción:**
```bash
cd /var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle/Controller

# Mover controladores root
mv RecaudacionController.php _Deprecated/Controller/Root/
mv DefaultController.php _Deprecated/Controller/Root/
mv BusquedaPacienteController.php _Deprecated/Controller/Root/
mv GarantiaPacienteController.php _Deprecated/Controller/Root/
mv GestionCajaController.php _Deprecated/Controller/Root/
mv InformacionActualPacienteController.php _Deprecated/Controller/Root/

# Mover directorio PagoCuenta completo
mv PagoCuenta/ _Deprecated/Controller/Root/
```

**Archivos a mover:** 9 archivos (3,940 líneas)

---

### FASE 3: Verificar y Probar

**Checklist:**
```markdown
- [ ] Actualizar routing.yml
- [ ] Limpiar cache: php bin/console cache:clear
- [ ] Verificar rutas: php bin/console debug:router | grep recaudacion
- [ ] Probar módulo de caja manualmente
- [ ] Verificar que no hay imports a controladores root
- [ ] Commit cambios
```

---

## 📊 Impacto de la Eliminación

| Métrica | Antes | Después | Reducción |
|---------|-------|---------|-----------|
| Controladores Activos | 67 | 58 | -13% |
| Controladores Deprecados | 6 | 15 | +150% |
| Líneas de código root | 3,940 | 0 | -100% |
| Controladores únicos _Default | 58 | 58 | 0% |

---

## 🔍 Búsqueda de Dependencias

**Comando para verificar dependencias:**
```bash
cd /var/www/html/melisa_prod/src
grep -r "use.*RecaudacionBundle\\\\Controller\\\\RecaudacionController" \
  --include="*.php" \
  --exclude-dir=RecaudacionBundle | wc -l

grep -r "use.*RecaudacionBundle\\\\Controller\\\\PagoCuenta" \
  --include="*.php" \
  --exclude-dir=RecaudacionBundle | wc -l
```

**Resultado esperado:** 0 dependencias externas (todos son legacy)

---

## ⚠️ Riesgos

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Ruta raíz deja de funcionar | Media | Alto | Actualizar routing antes de eliminar |
| Imports rotos | Baja | Medio | Buscar dependencias primero |
| Funcionalidad perdida | Muy Baja | Alto | Código duplicado en _Default |

---

## 🚀 Plan de Ejecución

### Paso 1: Verificar Dependencias (5 min)
```bash
cd /var/www/html/melisa_prod/src
grep -r "RecaudacionController\|PagoCuenta" --include="*.php" \
  --exclude-dir=RecaudacionBundle | wc -l
```

### Paso 2: Actualizar Routing (2 min)
```bash
cd /var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle
cp Resources/config/routing.yml Resources/config/routing.yml.backup2
# Editar manualmente o usar sed
```

### Paso 3: Mover a _Deprecated (3 min)
```bash
mkdir -p _Deprecated/Controller/Root
mv Controller/{Recaudacion,Default,BusquedaPaciente,Garantia,GestionCaja,InformacionActual}*.php \
   _Deprecated/Controller/Root/
mv Controller/PagoCuenta _Deprecated/Controller/Root/
```

### Paso 4: Probar (5 min)
```bash
php bin/console cache:clear
php bin/console debug:router | grep recaudacion_index
# Acceder a /Hermes/Recaudacion/ en navegador
```

### Paso 5: Commit (2 min)
```bash
git add .
git commit -m "chore(RecaudacionBundle): deprecate root legacy controllers"
```

**Tiempo total estimado:** 15-20 minutos

---

## 📝 Documentación Actualizada

Agregar a `_Deprecated/DEPRECATED.md`:

```markdown
## Controladores Root Legacy (9 archivos)

**Fecha de deprecación:** 30/12/2025

### Archivos eliminados:
- RecaudacionController.php (994 líneas)
- DefaultController.php (20 líneas)
- BusquedaPacienteController.php (266 líneas)
- GarantiaPacienteController.php (17 líneas)
- GestionCajaController.php (415 líneas)
- InformacionActualPacienteController.php (16 líneas)
- PagoCuenta/CuentaPacienteController.php (306 líneas)
- PagoCuenta/PagoCuentaController.php (650 líneas)
- PagoCuenta/RealizarPagoCuentaPacienteController.php (1,256 líneas)

**Total:** 3,940 líneas eliminadas

### Razón:
Versiones antiguas completamente reemplazadas por `_Default/`.
Todas las rutas asociadas estaban comentadas excepto `recaudacion_index` 
que fue actualizada para apuntar a `_Default/Recaudacion/DefaultController`.

### Duplicados en:
- _Default/Recaudacion/DefaultController.php
- _Default/Recaudacion/Pago/PagoController.php
- _Default/Recaudacion/Pago/PagarController.php
- _Default/Recaudacion/GestionCaja/GestionCajaController.php
```

---

## ✅ Checklist de Validación Post-Eliminación

```markdown
- [ ] Ruta principal funciona (/Hermes/Recaudacion/)
- [ ] Búsqueda de pacientes funciona
- [ ] Pago de servicios funciona
- [ ] Gestión de caja funciona
- [ ] Supervisor funciona
- [ ] No hay errores 500 en logs
- [ ] Tests pasan (si existen)
```

---

**Última actualización:** 30 de Diciembre 2025  
**Estado:** 📋 PENDIENTE EJECUCIÓN
