# 🔍 Scripts de Análisis - RecaudacionBundle

Scripts automatizados para identificar código obsoleto y métricas del bundle.

---

## 📊 Script 1: Análisis de Uso de Rutas

```bash
#!/bin/bash
# analisis_rutas_recaudacion.sh
# Identifica rutas no utilizadas basándose en logs de acceso

echo "=== ANÁLISIS DE RUTAS RECAUDACIONBUNDLE ==="
echo "Fecha: $(date)"
echo ""

# Configuración
LOG_DIR="/var/log/apache2"
OUTPUT_DIR="/tmp/recaudacion_analysis"
MESES_ANALISIS=6
UMBRAL_USO_MINIMO=10

mkdir -p "$OUTPUT_DIR"

# 1. Extraer todas las rutas de Recaudación de los logs
echo "[1/5] Extrayendo rutas de logs (últimos $MESES_ANALISIS meses)..."
find "$LOG_DIR" -name "access.log*" -mtime -$((MESES_ANALISIS * 30)) \
    -exec zgrep -h "GET\|POST\|PUT\|DELETE" {} \; | \
    grep -E "/Recaudacion|/Caja|/recaudacion|/caja" | \
    awk '{print $7}' | \
    cut -d'?' -f1 | \
    sort | uniq -c | sort -rn > "$OUTPUT_DIR/rutas_usadas_raw.txt"

echo "   ✓ Encontradas $(wc -l < $OUTPUT_DIR/rutas_usadas_raw.txt) rutas únicas"

# 2. Limpiar y formatear resultados
echo "[2/5] Procesando datos..."
awk '{print $1 "," $2}' "$OUTPUT_DIR/rutas_usadas_raw.txt" | \
    sed 's/^//' > "$OUTPUT_DIR/rutas_usadas.csv"

# Agregar header
sed -i '1i uso_total,ruta' "$OUTPUT_DIR/rutas_usadas.csv"

# 3. Identificar rutas poco usadas
echo "[3/5] Identificando rutas con uso < $UMBRAL_USO_MINIMO..."
awk -F',' -v umbral="$UMBRAL_USO_MINIMO" \
    'NR>1 && $1 < umbral {print $0}' \
    "$OUTPUT_DIR/rutas_usadas.csv" > "$OUTPUT_DIR/rutas_poco_usadas.csv"

echo "   ⚠️  Rutas con bajo uso: $(wc -l < $OUTPUT_DIR/rutas_poco_usadas.csv)"

# 4. Extraer rutas definidas en YAML
echo "[4/5] Extrayendo rutas definidas en configuración..."
cd /var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle 2>/dev/null || {
    echo "   ❌ No se pudo acceder al bundle. Ajusta la ruta."
    exit 1
}

find Resources/config/routing -name "*.yml" -exec grep -h "^\s\+path:" {} \; | \
    awk '{print $2}' | \
    sed 's/[{}]//g' | \
    sort | uniq > "$OUTPUT_DIR/rutas_definidas.txt"

echo "   ✓ Rutas definidas: $(wc -l < $OUTPUT_DIR/rutas_definidas.txt)"

# 5. Comparar rutas definidas vs usadas
echo "[5/5] Comparando rutas definidas vs usadas..."

# Rutas definidas pero nunca usadas
comm -23 \
    <(sort "$OUTPUT_DIR/rutas_definidas.txt") \
    <(awk -F',' 'NR>1 {print $2}' "$OUTPUT_DIR/rutas_usadas.csv" | sort) \
    > "$OUTPUT_DIR/rutas_no_usadas.txt"

echo "   ❌ Rutas NUNCA usadas: $(wc -l < $OUTPUT_DIR/rutas_no_usadas.txt)"

# 6. Generar reporte
cat > "$OUTPUT_DIR/reporte_rutas.txt" << EOF
===============================================
  REPORTE DE ANÁLISIS DE RUTAS - RecaudacionBundle
===============================================
Fecha: $(date)
Período analizado: Últimos $MESES_ANALISIS meses
Umbral de uso mínimo: $UMBRAL_USO_MINIMO accesos

--- RESUMEN ---
Rutas definidas en configuración: $(wc -l < $OUTPUT_DIR/rutas_definidas.txt)
Rutas con tráfico registrado: $(wc -l < $OUTPUT_DIR/rutas_usadas.csv)
Rutas con uso < $UMBRAL_USO_MINIMO: $(wc -l < $OUTPUT_DIR/rutas_poco_usadas.csv)
Rutas NUNCA usadas: $(wc -l < $OUTPUT_DIR/rutas_no_usadas.txt)

--- RUTAS NUNCA USADAS (candidatas a eliminar) ---
$(cat $OUTPUT_DIR/rutas_no_usadas.txt)

--- TOP 10 RUTAS MÁS USADAS ---
$(head -10 $OUTPUT_DIR/rutas_usadas_raw.txt)

--- TOP 10 RUTAS MENOS USADAS ---
$(tail -10 $OUTPUT_DIR/rutas_usadas_raw.txt)

===============================================
Archivos generados en: $OUTPUT_DIR
- rutas_usadas.csv (todas las rutas con conteo)
- rutas_poco_usadas.csv (rutas con bajo uso)
- rutas_no_usadas.txt (rutas nunca usadas)
- rutas_definidas.txt (rutas en configuración)
===============================================
EOF

cat "$OUTPUT_DIR/reporte_rutas.txt"

echo ""
echo "✓ Análisis completado. Archivos en: $OUTPUT_DIR"
```

**Uso:**
```bash
chmod +x analisis_rutas_recaudacion.sh
./analisis_rutas_recaudacion.sh
```

---

## 🎯 Script 2: Análisis de Controladores y Templates

```bash
#!/bin/bash
# analisis_controladores_templates.sh
# Identifica controladores sin rutas y templates huérfanos

echo "=== ANÁLISIS CONTROLADORES Y TEMPLATES ==="
echo "Fecha: $(date)"
echo ""

BUNDLE_DIR="/var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle"
OUTPUT_DIR="/tmp/recaudacion_analysis"

mkdir -p "$OUTPUT_DIR"

# 1. Analizar controladores
echo "[1/4] Analizando controladores..."
cd "$BUNDLE_DIR/Controller" || exit 1

# Encontrar todos los controladores
find . -name "*.php" > "$OUTPUT_DIR/controladores_existentes.txt"
echo "   ✓ Controladores encontrados: $(wc -l < $OUTPUT_DIR/controladores_existentes.txt)"

# Controladores con rutas (anotaciones o atributos)
grep -r -l "Route\|@Route\|#\[Route\]" . > "$OUTPUT_DIR/controladores_con_rutas.txt"
echo "   ✓ Controladores con rutas: $(wc -l < $OUTPUT_DIR/controladores_con_rutas.txt)"

# Controladores sin rutas (potencialmente obsoletos)
comm -23 \
    <(sort "$OUTPUT_DIR/controladores_existentes.txt") \
    <(sort "$OUTPUT_DIR/controladores_con_rutas.txt") \
    > "$OUTPUT_DIR/controladores_sin_rutas.txt"

echo "   ⚠️  Controladores SIN rutas: $(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt)"

# 2. Analizar templates
echo "[2/4] Analizando templates..."
cd "$BUNDLE_DIR/Resources/views" 2>/dev/null || {
    echo "   ⚠️  Directorio views no encontrado"
    touch "$OUTPUT_DIR/templates_existentes.txt"
}

if [ -d "$BUNDLE_DIR/Resources/views" ]; then
    find . -name "*.twig" > "$OUTPUT_DIR/templates_existentes.txt"
    echo "   ✓ Templates encontrados: $(wc -l < $OUTPUT_DIR/templates_existentes.txt)"
else
    echo "0" > "$OUTPUT_DIR/templates_existentes.txt"
fi

# Templates referenciados en controladores
echo "[3/4] Buscando referencias a templates en controladores..."
cd "$BUNDLE_DIR/Controller"
grep -roh "render\|renderView" . | \
    grep -o "'[^']*\.twig'" | \
    sed "s/'//g" | \
    sort | uniq > "$OUTPUT_DIR/templates_referenciados.txt"

echo "   ✓ Templates referenciados: $(wc -l < $OUTPUT_DIR/templates_referenciados.txt)"

# Templates huérfanos (sin referencias)
# Nota: Este análisis es básico y puede tener falsos positivos
echo "[4/4] Identificando templates huérfanos..."

# Para cada template, buscar si está referenciado
> "$OUTPUT_DIR/templates_huerfanos.txt"
while IFS= read -r template; do
    template_name=$(basename "$template")
    
    # Buscar en controladores
    if ! grep -rq "$template_name" "$BUNDLE_DIR/Controller/"; then
        # Buscar en otros templates (includes/extends)
        if ! grep -rq "$template_name" "$BUNDLE_DIR/Resources/views/" 2>/dev/null; then
            echo "$template" >> "$OUTPUT_DIR/templates_huerfanos.txt"
        fi
    fi
done < "$OUTPUT_DIR/templates_existentes.txt"

if [ -f "$OUTPUT_DIR/templates_huerfanos.txt" ]; then
    echo "   ⚠️  Templates huérfanos: $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt)"
else
    echo "0" > "$OUTPUT_DIR/templates_huerfanos.txt"
fi

# 3. Generar reporte
cat > "$OUTPUT_DIR/reporte_controladores_templates.txt" << EOF
===============================================
  ANÁLISIS CONTROLADORES Y TEMPLATES
  RecaudacionBundle
===============================================
Fecha: $(date)

--- CONTROLADORES ---
Total de controladores: $(wc -l < $OUTPUT_DIR/controladores_existentes.txt)
Controladores con rutas: $(wc -l < $OUTPUT_DIR/controladores_con_rutas.txt)
Controladores SIN rutas: $(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt)

Controladores sin rutas (candidatos a eliminar):
$(cat $OUTPUT_DIR/controladores_sin_rutas.txt)

--- TEMPLATES ---
Total de templates: $(wc -l < $OUTPUT_DIR/templates_existentes.txt)
Templates referenciados: $(wc -l < $OUTPUT_DIR/templates_referenciados.txt)
Templates huérfanos: $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt)

Templates huérfanos (candidatos a eliminar):
$(cat $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo "Ninguno")

===============================================
EOF

cat "$OUTPUT_DIR/reporte_controladores_templates.txt"

echo ""
echo "✓ Análisis completado. Archivos en: $OUTPUT_DIR"
```

**Uso:**
```bash
chmod +x analisis_controladores_templates.sh
./analisis_controladores_templates.sh
```

---

## 📈 Script 3: Métricas del Bundle

```bash
#!/bin/bash
# metricas_bundle.sh
# Genera métricas generales del RecaudacionBundle

echo "=== MÉTRICAS DEL BUNDLE ==="
echo "Fecha: $(date)"
echo ""

BUNDLE_DIR="/var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle"
OUTPUT_DIR="/tmp/recaudacion_analysis"

mkdir -p "$OUTPUT_DIR"

cd "$BUNDLE_DIR" || exit 1

# Métricas de código
echo "📊 MÉTRICAS DE CÓDIGO"
echo "===================="

# Líneas de código PHP
php_lines=$(find . -name "*.php" -exec wc -l {} + | tail -1 | awk '{print $1}')
echo "Líneas de código PHP: $php_lines"

# Líneas de configuración YAML
yaml_lines=$(find . -name "*.yml" -o -name "*.yaml" -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "Líneas de configuración YAML: $yaml_lines"

# Líneas de templates Twig
twig_lines=$(find . -name "*.twig" -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "Líneas de templates Twig: $twig_lines"

echo ""
echo "📁 ESTRUCTURA"
echo "===================="

# Contar archivos por tipo
echo "Controladores: $(find Controller -name "*.php" 2>/dev/null | wc -l)"
echo "Servicios: $(find Services -name "*.php" 2>/dev/null | wc -l)"
echo "Repositorios: $(find Repository -name "*.php" 2>/dev/null | wc -l)"
echo "Formularios: $(find Form -name "*.php" 2>/dev/null | wc -l)"
echo "Entidades: $(find Entity -name "*.php" 2>/dev/null | wc -l)"
echo "Templates: $(find Resources/views -name "*.twig" 2>/dev/null | wc -l)"
echo "Archivos de rutas: $(find Resources/config/routing -name "*.yml" 2>/dev/null | wc -l)"

echo ""
echo "🏗️ ARQUITECTURA"
echo "===================="

# Contar rutas YAML
total_rutas=$(find Resources/config/routing -name "*.yml" -exec grep -h "^\s*path:" {} \; 2>/dev/null | wc -l)
echo "Rutas definidas (YAML): $total_rutas"

# Contar servicios registrados
total_servicios=$(grep -c "class:" Resources/config/services.yml 2>/dev/null || echo "0")
echo "Servicios registrados: $total_servicios"

# APIs identificadas
echo "APIs/Versiones:"
ls -1d Controller/*/ 2>/dev/null | sed 's|Controller/||; s|/$||' | sed 's/^/  - /'

echo ""
echo "📊 COMPLEJIDAD"
echo "===================="

# Complejidad ciclomática (requiere phploc)
if command -v phploc &> /dev/null; then
    phploc --quiet . | grep -E "Cyclomatic Complexity|Lines of Code"
else
    echo "  (instalar phploc para métricas de complejidad)"
fi

# Clases más grandes
echo ""
echo "Top 5 archivos PHP más grandes:"
find . -name "*.php" -exec wc -l {} + | sort -rn | head -5 | \
    awk '{printf "  %s líneas - %s\n", $1, $2}'

echo ""
echo "🔍 ANÁLISIS DE DEPENDENCIAS"
echo "===================="

# Usar composer para ver dependencias del bundle
if [ -f "../../composer.json" ]; then
    echo "Dependencias externas:"
    grep -A 20 '"require"' ../../composer.json | \
        grep -v "^{" | grep -v "^}" | head -10
fi

echo ""
echo "📝 DOCUMENTACIÓN"
echo "===================="

# Archivos README/docs
readme_count=$(find . -iname "readme*" -o -iname "*.md" | wc -l)
echo "Archivos de documentación: $readme_count"

# Cobertura de docblocks
files_with_docblocks=$(grep -rl "\/\*\*" --include="*.php" . | wc -l)
total_php_files=$(find . -name "*.php" | wc -l)
coverage=$((files_with_docblocks * 100 / total_php_files))
echo "Archivos con docblocks: $files_with_docblocks/$total_php_files ($coverage%)"

# Guardar métricas en archivo
cat > "$OUTPUT_DIR/metricas_bundle.json" << EOF
{
  "fecha_analisis": "$(date -I)",
  "lineas_codigo": {
    "php": $php_lines,
    "yaml": $yaml_lines,
    "twig": $twig_lines,
    "total": $((php_lines + yaml_lines + twig_lines))
  },
  "archivos": {
    "controladores": $(find Controller -name "*.php" 2>/dev/null | wc -l),
    "servicios": $(find Services -name "*.php" 2>/dev/null | wc -l),
    "repositorios": $(find Repository -name "*.php" 2>/dev/null | wc -l),
    "formularios": $(find Form -name "*.php" 2>/dev/null | wc -l),
    "entidades": $(find Entity -name "*.php" 2>/dev/null | wc -l),
    "templates": $(find Resources/views -name "*.twig" 2>/dev/null | wc -l)
  },
  "rutas_definidas": $total_rutas,
  "servicios_registrados": $total_servicios,
  "cobertura_docblocks": $coverage
}
EOF

echo ""
echo "✓ Métricas guardadas en: $OUTPUT_DIR/metricas_bundle.json"
```

**Uso:**
```bash
chmod +x metricas_bundle.sh
./metricas_bundle.sh
```

---

## 🔗 Script 4: Análisis de Dependencias Cruzadas

```bash
#!/bin/bash
# analisis_dependencias.sh
# Encuentra qué otros bundles/archivos dependen de RecaudacionBundle

echo "=== ANÁLISIS DE DEPENDENCIAS CRUZADAS ==="
echo "Fecha: $(date)"
echo ""

SRC_DIR="/var/www/html/melisa_prod/src"
OUTPUT_DIR="/tmp/recaudacion_analysis"

mkdir -p "$OUTPUT_DIR"

echo "[1/3] Buscando dependencias en otros bundles..."

# Buscar use statements
grep -r "use Rebsol\\\\RecaudacionBundle" \
    --include="*.php" \
    --exclude-dir=RecaudacionBundle \
    "$SRC_DIR" > "$OUTPUT_DIR/dependencias_use.txt"

echo "   ✓ Archivos con 'use' de RecaudacionBundle: $(wc -l < $OUTPUT_DIR/dependencias_use.txt)"

# Buscar referencias en YAML
echo "[2/3] Buscando referencias en configuración..."
grep -r "RecaudacionBundle\|@Recaudacion" \
    --include="*.yml" \
    --include="*.yaml" \
    --exclude-dir=RecaudacionBundle \
    "$SRC_DIR/.." > "$OUTPUT_DIR/dependencias_yaml.txt"

echo "   ✓ Archivos YAML con referencias: $(wc -l < $OUTPUT_DIR/dependencias_yaml.txt)"

# Buscar en templates Twig
echo "[3/3] Buscando referencias en templates..."
grep -r "RecaudacionBundle\|@Recaudacion" \
    --include="*.twig" \
    --exclude-dir=RecaudacionBundle \
    "$SRC_DIR/.." > "$OUTPUT_DIR/dependencias_twig.txt"

echo "   ✓ Templates con referencias: $(wc -l < $OUTPUT_DIR/dependencias_twig.txt)"

# Generar reporte
cat > "$OUTPUT_DIR/reporte_dependencias.txt" << EOF
===============================================
  ANÁLISIS DE DEPENDENCIAS CRUZADAS
  RecaudacionBundle
===============================================
Fecha: $(date)

--- ARCHIVOS QUE DEPENDEN DE RecaudacionBundle ---

PHP (use statements):
$(cat $OUTPUT_DIR/dependencias_use.txt | awk -F: '{print $1}' | sort | uniq)

YAML (configuración):
$(cat $OUTPUT_DIR/dependencias_yaml.txt | awk -F: '{print $1}' | sort | uniq)

TWIG (templates):
$(cat $OUTPUT_DIR/dependencias_twig.txt | awk -F: '{print $1}' | sort | uniq)

--- RESUMEN ---
Archivos PHP dependientes: $(cat $OUTPUT_DIR/dependencias_use.txt | awk -F: '{print $1}' | sort | uniq | wc -l)
Archivos YAML dependientes: $(cat $OUTPUT_DIR/dependencias_yaml.txt | awk -F: '{print $1}' | sort | uniq | wc -l)
Archivos TWIG dependientes: $(cat $OUTPUT_DIR/dependencias_twig.txt | awk -F: '{print $1}' | sort | uniq | wc -l)

⚠️  IMPORTANTE: Antes de eliminar código del RecaudacionBundle,
    asegúrate de actualizar estos archivos dependientes.

===============================================
EOF

cat "$OUTPUT_DIR/reporte_dependencias.txt"

echo ""
echo "✓ Análisis completado. Archivos en: $OUTPUT_DIR"
```

**Uso:**
```bash
chmod +x analisis_dependencias.sh
./analisis_dependencias.sh
```

---

## 🚀 Script 5: Master Script - Ejecutar Todos

```bash
#!/bin/bash
# analisis_completo_recaudacion.sh
# Ejecuta todos los scripts de análisis

echo "╔════════════════════════════════════════════════╗"
echo "║  ANÁLISIS COMPLETO - RecaudacionBundle         ║"
echo "║  Fecha: $(date +'%Y-%m-%d %H:%M:%S')                  ║"
echo "╚════════════════════════════════════════════════╝"
echo ""

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_DIR="/tmp/recaudacion_analysis"

mkdir -p "$OUTPUT_DIR"

# 1. Métricas generales
echo "🔹 [1/5] Ejecutando análisis de métricas..."
bash "$SCRIPT_DIR/metricas_bundle.sh" > "$OUTPUT_DIR/01_metricas.log" 2>&1
echo "   ✓ Completado"

# 2. Análisis de rutas
echo "🔹 [2/5] Ejecutando análisis de rutas (puede tardar)..."
bash "$SCRIPT_DIR/analisis_rutas_recaudacion.sh" > "$OUTPUT_DIR/02_rutas.log" 2>&1
echo "   ✓ Completado"

# 3. Controladores y templates
echo "🔹 [3/5] Ejecutando análisis de controladores y templates..."
bash "$SCRIPT_DIR/analisis_controladores_templates.sh" > "$OUTPUT_DIR/03_controladores.log" 2>&1
echo "   ✓ Completado"

# 4. Dependencias
echo "🔹 [4/5] Ejecutando análisis de dependencias..."
bash "$SCRIPT_DIR/analisis_dependencias.sh" > "$OUTPUT_DIR/04_dependencias.log" 2>&1
echo "   ✓ Completado"

# 5. Generar reporte consolidado
echo "🔹 [5/5] Generando reporte consolidado..."

cat > "$OUTPUT_DIR/REPORTE_COMPLETO.md" << 'EOF'
# 📊 Reporte Completo de Análisis - RecaudacionBundle

**Fecha de análisis:** $(date)
**Generado automáticamente**

---

## 📈 Resumen Ejecutivo

```
$(cat $OUTPUT_DIR/metricas_bundle.json)
```

---

## 🚦 Rutas

### Rutas sin uso (candidatas a eliminar)
```
$(cat $OUTPUT_DIR/rutas_no_usadas.txt | head -20)
```

### Rutas con bajo uso (< 10 accesos)
```
$(cat $OUTPUT_DIR/rutas_poco_usadas.csv | head -20)
```

---

## 🎯 Controladores

### Controladores sin rutas
```
$(cat $OUTPUT_DIR/controladores_sin_rutas.txt)
```

---

## 🎨 Templates

### Templates huérfanos
```
$(cat $OUTPUT_DIR/templates_huerfanos.txt)
```

---

## 🔗 Dependencias

Archivos externos que dependen de RecaudacionBundle: 
$(cat $OUTPUT_DIR/dependencias_*.txt | wc -l) referencias encontradas

---

## ✅ Recomendaciones

1. **Eliminar rutas no usadas:** $(wc -l < $OUTPUT_DIR/rutas_no_usadas.txt) rutas
2. **Revisar rutas con bajo uso:** $(wc -l < $OUTPUT_DIR/rutas_poco_usadas.csv) rutas
3. **Eliminar controladores sin rutas:** $(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt) archivos
4. **Eliminar templates huérfanos:** $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt) archivos

**Líneas de código a eliminar (estimado):** 10-30% del total

---

_Reporte generado con scripts de análisis automático_
EOF

eval "cat <<< \"$(cat $OUTPUT_DIR/REPORTE_COMPLETO.md)\"" > "$OUTPUT_DIR/REPORTE_COMPLETO_FINAL.md"

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
echo "   - REPORTE_COMPLETO_FINAL.md (reporte consolidado)"
echo "   - metricas_bundle.json (métricas en JSON)"
echo "   - rutas_no_usadas.txt (rutas a eliminar)"
echo "   - controladores_sin_rutas.txt (controladores a eliminar)"
echo "   - templates_huerfanos.txt (templates a eliminar)"
echo ""
echo "📊 Para ver el reporte completo:"
echo "   cat $OUTPUT_DIR/REPORTE_COMPLETO_FINAL.md"
echo ""
```

**Uso:**
```bash
chmod +x analisis_completo_recaudacion.sh
./analisis_completo_recaudacion.sh
```

---

## 📝 Notas de Uso

### Requisitos Previos

```bash
# Instalar herramientas opcionales
composer global require phploc/phploc
composer global require sebastian/phpcpd
```

### Permisos

```bash
# Dar permisos de ejecución a todos los scripts
chmod +x analisis_*.sh metricas_*.sh
```

### Programación Automática

```bash
# Agregar a crontab para análisis mensual
0 0 1 * * /ruta/scripts/analisis_completo_recaudacion.sh
```

---

## 🔧 Personalización

### Ajustar Umbrales

Editar variables en los scripts:

```bash
# En analisis_rutas_recaudacion.sh
MESES_ANALISIS=6          # Cambiar período de análisis
UMBRAL_USO_MINIMO=10      # Cambiar umbral de uso mínimo

# En analisis_controladores_templates.sh
LOG_DIR="/var/log/apache2"  # Cambiar ubicación de logs
```

---

**Última actualización:** 30 de Diciembre 2025
