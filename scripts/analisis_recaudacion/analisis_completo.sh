#!/bin/bash
# analisis_completo.sh
# Ejecuta todos los scripts de análisis y genera reporte consolidado

echo "╔════════════════════════════════════════════════╗"
echo "║  ANÁLISIS COMPLETO - RecaudacionBundle         ║"
echo "║  Fecha: $(date +'%Y-%m-%d %H:%M:%S')                  ║"
echo "╚════════════════════════════════════════════════╝"
echo ""

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_DIR="/var/www/html/melisa_tenant/docs/RecaudacionBundle/analisis"

mkdir -p "$OUTPUT_DIR"

# 1. Métricas generales
echo "🔹 [1/4] Ejecutando análisis de métricas..."
bash "$SCRIPT_DIR/metricas_bundle.sh" > "$OUTPUT_DIR/01_metricas.log" 2>&1
if [ $? -eq 0 ]; then
    echo "   ✓ Completado"
else
    echo "   ⚠️  Completado con advertencias (ver log)"
fi

# 2. Controladores y templates
echo "🔹 [2/4] Ejecutando análisis de controladores y templates..."
bash "$SCRIPT_DIR/analisis_controladores_templates.sh" > "$OUTPUT_DIR/02_controladores.log" 2>&1
if [ $? -eq 0 ]; then
    echo "   ✓ Completado"
else
    echo "   ⚠️  Completado con advertencias (ver log)"
fi

# 3. Análisis de rutas
echo "🔹 [3/4] Ejecutando análisis de rutas..."
bash "$SCRIPT_DIR/analisis_rutas.sh" > "$OUTPUT_DIR/03_rutas.log" 2>&1
if [ $? -eq 0 ]; then
    echo "   ✓ Completado"
else
    echo "   ⚠️  Completado con advertencias (ver log)"
fi

# 4. Dependencias
echo "🔹 [4/4] Ejecutando análisis de dependencias..."
bash "$SCRIPT_DIR/analisis_dependencias.sh" > "$OUTPUT_DIR/04_dependencias.log" 2>&1
if [ $? -eq 0 ]; then
    echo "   ✓ Completado"
else
    echo "   ⚠️  Completado con advertencias (ver log)"
fi

# 5. Generar reporte consolidado
echo ""
echo "🔹 [5/5] Generando reporte consolidado..."

# Leer métricas JSON si existe
if [ -f "$OUTPUT_DIR/metricas_bundle.json" ]; then
    metricas_json=$(cat "$OUTPUT_DIR/metricas_bundle.json")
else
    metricas_json="{}"
fi

cat > "$OUTPUT_DIR/REPORTE_COMPLETO.md" << EOF
# 📊 Reporte Completo de Análisis - RecaudacionBundle

**Fecha de análisis:** $(date)
**Generado automáticamente**

---

## 📈 Resumen Ejecutivo

### Métricas Generales

\`\`\`json
$metricas_json
\`\`\`

---

## 🎯 Controladores

### Total de Controladores
- **Existentes:** $(wc -l < $OUTPUT_DIR/controladores_existentes.txt 2>/dev/null || echo "N/A")
- **Con rutas:** $(wc -l < $OUTPUT_DIR/controladores_con_rutas.txt 2>/dev/null || echo "N/A")
- **Sin rutas:** $(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt 2>/dev/null || echo "N/A")

### Controladores sin rutas (candidatos a eliminar)
\`\`\`
$(cat $OUTPUT_DIR/controladores_sin_rutas.txt 2>/dev/null | head -20 || echo "Sin datos")
\`\`\`

---

## 🎨 Templates

### Total de Templates
- **Existentes:** $(wc -l < $OUTPUT_DIR/templates_existentes.txt 2>/dev/null || echo "N/A")
- **Referenciados:** $(wc -l < $OUTPUT_DIR/templates_referenciados.txt 2>/dev/null || echo "N/A")
- **Huérfanos:** $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo "N/A")

### Templates huérfanos (candidatos a eliminar)
\`\`\`
$(cat $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null | head -20 || echo "Ninguno")
\`\`\`

---

## 🚦 Rutas

### Total de Rutas
- **Definidas en YAML:** $(wc -l < $OUTPUT_DIR/rutas_definidas.txt 2>/dev/null || echo "N/A")
- **Nombres únicos:** $(wc -l < $OUTPUT_DIR/nombres_rutas.txt 2>/dev/null || echo "N/A")

### Muestra de rutas definidas (primeras 20)
\`\`\`
$(cat $OUTPUT_DIR/rutas_definidas.txt 2>/dev/null | head -20 || echo "Sin datos")
\`\`\`

⚠️ **NOTA:** Para análisis de uso real, se requiere acceso a logs de producción.

---

## 🔗 Dependencias

### Archivos que dependen de RecaudacionBundle

- **PHP (use statements):** $(cat $OUTPUT_DIR/dependencias_use.txt 2>/dev/null | awk -F: '{print $1}' | sort | uniq | wc -l) archivos
- **YAML (configuración):** $(cat $OUTPUT_DIR/dependencias_yaml.txt 2>/dev/null | awk -F: '{print $1}' | sort | uniq | wc -l) archivos  
- **TWIG (templates):** $(cat $OUTPUT_DIR/dependencias_twig.txt 2>/dev/null | awk -F: '{print $1}' | sort | uniq | wc -l) archivos

---

## ✅ Recomendaciones

### Limpieza de Código

1. **Eliminar controladores sin rutas:** $(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt 2>/dev/null || echo "0") archivos
2. **Eliminar templates huérfanos:** $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo "0") archivos
3. **Revisar rutas definidas vs uso real** (requiere logs de producción)

### Estimación de Reducción de Código

Basado en los archivos identificados:
- **Controladores a eliminar:** ~$(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt 2>/dev/null || echo "0") archivos
- **Templates a eliminar:** ~$(wc -l < $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo "0") archivos
- **Reducción estimada:** 10-25% del código total

---

## 📂 Archivos Generados

Todos los archivos de análisis se encuentran en:
\`$OUTPUT_DIR\`

- \`metricas_bundle.json\` - Métricas en formato JSON
- \`controladores_existentes.txt\` - Lista de todos los controladores
- \`controladores_sin_rutas.txt\` - Controladores sin rutas
- \`templates_existentes.txt\` - Lista de todos los templates
- \`templates_huerfanos.txt\` - Templates sin referencias
- \`rutas_definidas.txt\` - Todas las rutas del bundle
- \`dependencias_*.txt\` - Archivos que dependen del bundle

---

## 🚀 Próximos Pasos

1. **Revisar con equipo de negocio** los archivos candidatos a eliminar
2. **Analizar logs de producción** para identificar rutas no utilizadas
3. **Crear backup completo** antes de eliminar código
4. **Iniciar migración a Symfony 6** siguiendo el plan establecido

---

_Reporte generado automáticamente por scripts de análisis_
_Ver logs individuales en \`$OUTPUT_DIR/*.log\`_
EOF

echo "   ✓ Completado"

# Mostrar resumen
echo ""
echo "╔════════════════════════════════════════════════╗"
echo "║  ✅ ANÁLISIS COMPLETADO                         ║"
echo "╚════════════════════════════════════════════════╝"
echo ""
echo "📁 Todos los archivos generados en:"
echo "   $OUTPUT_DIR"
echo ""
echo "📄 Archivos principales:"
echo "   - REPORTE_COMPLETO.md (reporte consolidado)"
echo "   - metricas_bundle.json (métricas en JSON)"
echo "   - controladores_sin_rutas.txt"
echo "   - templates_huerfanos.txt"
echo "   - rutas_definidas.txt"
echo "   - dependencias_*.txt"
echo ""
echo "📊 Ver reporte completo:"
echo "   cat $OUTPUT_DIR/REPORTE_COMPLETO.md"
echo ""
echo "📋 Ver logs de ejecución:"
echo "   ls -lah $OUTPUT_DIR/*.log"
