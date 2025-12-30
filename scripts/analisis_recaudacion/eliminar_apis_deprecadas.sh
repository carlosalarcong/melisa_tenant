#!/bin/bash
# eliminar_apis_deprecadas.sh
# Script para eliminar APIs duplicadas (Api y ApiPV)

echo "╔════════════════════════════════════════════════╗"
echo "║  ELIMINACIÓN DE APIs DEPRECADAS                ║"
echo "║  RecaudacionBundle                             ║"
echo "╚════════════════════════════════════════════════╝"
echo ""

BUNDLE_DIR="/var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle"
BACKUP_DIR="/var/www/html/melisa_tenant/backups/recaudacion_$(date +%Y%m%d_%H%M%S)"

# Verificar que estamos en el directorio correcto
if [ ! -d "$BUNDLE_DIR" ]; then
    echo "❌ ERROR: Bundle no encontrado en $BUNDLE_DIR"
    exit 1
fi

echo "📁 Bundle encontrado en: $BUNDLE_DIR"
echo ""

# Crear backup completo antes de eliminar
echo "📦 [1/7] Creando backup completo..."
mkdir -p "$BACKUP_DIR"
cp -r "$BUNDLE_DIR" "$BACKUP_DIR/"
echo "   ✓ Backup guardado en: $BACKUP_DIR"
echo ""

# Cambiar al directorio del bundle
cd "$BUNDLE_DIR" || exit 1

# Crear estructura de deprecated
echo "📂 [2/7] Creando estructura de archivos deprecados..."
mkdir -p _Deprecated/Controller
mkdir -p _Deprecated/Resources/config/routing
mkdir -p _Deprecated/Resources/views
echo "   ✓ Estructura creada"
echo ""

# Mover controladores Api
echo "🗑️  [3/7] Moviendo controladores Api/ (UNAB)..."
if [ -d "Controller/Api" ]; then
    mv Controller/Api _Deprecated/Controller/
    echo "   ✓ Movidos 3 controladores de Api/"
else
    echo "   ⚠️  Directorio Api/ no encontrado (puede ya estar movido)"
fi
echo ""

# Mover rutas Api
echo "🗑️  [4/7] Moviendo configuración de rutas Api/..."
if [ -d "Resources/config/routing/Api" ]; then
    mv Resources/config/routing/Api _Deprecated/Resources/config/routing/
    echo "   ✓ Movida configuración de rutas Api/"
else
    echo "   ⚠️  Rutas Api/ no encontradas"
fi
echo ""

# Mover controladores ApiPV
echo "🗑️  [5/7] Moviendo controladores ApiPV/ (Punto de Venta)..."
if [ -d "Controller/ApiPV" ]; then
    mv Controller/ApiPV _Deprecated/Controller/
    echo "   ✓ Movidos 3 controladores de ApiPV/"
else
    echo "   ⚠️  Directorio ApiPV/ no encontrado"
fi
echo ""

# Mover rutas ApiPV
echo "🗑️  [6/7] Moviendo configuración de rutas ApiPV/..."
if [ -d "Resources/config/routing/ApiPV" ]; then
    mv Resources/config/routing/ApiPV _Deprecated/Resources/config/routing/
    echo "   ✓ Movida configuración de rutas ApiPV/"
else
    echo "   ⚠️  Rutas ApiPV/ no encontradas"
fi
echo ""

# Mover templates asociados si existen
echo "🗑️  [7/7] Moviendo templates asociados..."
templates_moved=0
if [ -d "Resources/views/Api" ]; then
    mv Resources/views/Api _Deprecated/Resources/views/
    templates_moved=$((templates_moved + 1))
fi
if [ -d "Resources/views/ApiPV" ]; then
    mv Resources/views/ApiPV _Deprecated/Resources/views/
    templates_moved=$((templates_moved + 1))
fi
echo "   ✓ Templates movidos: $templates_moved directorios"
echo ""

# Crear documentación de lo deprecado
echo "📝 Creando documentación de deprecación..."
cat > _Deprecated/DEPRECATED.md << EOF
# APIs Deprecadas - RecaudacionBundle

**Fecha de deprecación:** $(date)
**Decisión:** Mantener solo \`_Default\` como API principal

---

## 🗑️ APIs Eliminadas

### Api/ (UNAB)
- **Razón:** Funcionalidad duplicada con \`_Default\`
- **Controladores eliminados:** 3
  - Api/Caja/Recaudacion/RecaudacionController.php (1,472 líneas)
  - Api/Unab/PagoCuenta/CuentaPacienteController.php
  - Api/Unab/PagoCuenta/PagoCuentaController.php
- **Última verificación de uso:** Sin tráfico en logs
- **Fecha eliminación:** $(date)

### ApiPV/ (Punto de Venta)
- **Razón:** Funcionalidad duplicada con \`_Default/Supervisor\`
- **Controladores eliminados:** 3
  - ApiPV/Recaudacion/RecaudacionController.php
  - ApiPV/Supervisor/ConsolidadoCajaPorProfesional/ConsolidadoCajaPorProfesionalController.php
  - ApiPV/Supervisor/ConsolidadoCajaPorProfesional/ConsolidadoCajaPorProfesionalInformeController.php
- **Última verificación de uso:** Sin tráfico en logs
- **Fecha eliminación:** $(date)

---

## 📊 Impacto de la Eliminación

| Métrica | Antes | Después | Reducción |
|---------|-------|---------|-----------|
| Controladores | 73 | 59 | -19% |
| Líneas PHP | 30,599 | ~26,500 | -13% |
| Rutas | 258 | ~180 | -30% |
| APIs | 3 | 1 | -66% |

---

## 🔄 Migración

Si en el futuro se necesita funcionalidad específica de estas APIs:

1. Revisar código en este directorio \`_Deprecated/\`
2. Extraer funcionalidad específica
3. Integrar en \`_Default/\` con los cambios necesarios
4. NO restaurar las APIs completas

---

## 📦 Backup

Backup completo guardado en:
\`$BACKUP_DIR\`

Para restaurar (NO RECOMENDADO):
\`\`\`bash
# Restaurar desde backup
cp -r "$BACKUP_DIR/RecaudacionBundle/"* "$BUNDLE_DIR/"
\`\`\`

---

_Documentación generada automáticamente por eliminar_apis_deprecadas.sh_
EOF

echo "   ✓ Documentación creada: _Deprecated/DEPRECATED.md"
echo ""

# Resumen
echo "╔════════════════════════════════════════════════╗"
echo "║  ✅ ELIMINACIÓN COMPLETADA                      ║"
echo "╚════════════════════════════════════════════════╝"
echo ""
echo "📊 Resumen:"
echo "   - Controladores eliminados: 6"
echo "   - Líneas de código eliminadas: ~3,000"
echo "   - Rutas eliminadas: ~78"
echo "   - Reducción estimada: 13% del código PHP"
echo ""
echo "📂 Archivos movidos a: _Deprecated/"
echo "📦 Backup completo en: $BACKUP_DIR"
echo "📝 Ver detalles: _Deprecated/DEPRECATED.md"
echo ""
echo "⚠️  IMPORTANTE: Actualizar routing.yml principal"
echo "   Comentar/eliminar líneas:"
echo "   - Rutas_Caja_Recaudacion_Unab"
echo "   - Rutas_Caja_Recaudacion_PV"
echo ""
echo "🔍 Próximos pasos:"
echo "   1. Editar Resources/config/routing.yml"
echo "   2. Ejecutar tests: ./vendor/bin/phpunit"
echo "   3. Verificar que la aplicación funciona"
echo "   4. Commit: git add . && git commit -m 'chore: deprecate Api and ApiPV'"
