#!/bin/bash
# metricas_bundle.sh
# Genera métricas generales del RecaudacionBundle

echo "=== MÉTRICAS DEL BUNDLE ==="
echo "Fecha: $(date)"
echo ""

BUNDLE_DIR="/var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle"
OUTPUT_DIR="/var/www/html/melisa_tenant/docs/RecaudacionBundle/analisis"

mkdir -p "$OUTPUT_DIR"

# Verificar que el bundle existe
if [ ! -d "$BUNDLE_DIR" ]; then
    echo "❌ ERROR: No se encuentra el bundle en $BUNDLE_DIR"
    echo "   Verifica la ruta o ajusta la variable BUNDLE_DIR"
    exit 1
fi

cd "$BUNDLE_DIR" || exit 1

# Métricas de código
echo "📊 MÉTRICAS DE CÓDIGO"
echo "===================="

# Líneas de código PHP
php_lines=$(find . -name "*.php" -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "Líneas de código PHP: ${php_lines:-0}"

# Líneas de configuración YAML
yaml_lines=$(find . -name "*.yml" -o -name "*.yaml" -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "Líneas de configuración YAML: ${yaml_lines:-0}"

# Líneas de templates Twig
twig_lines=$(find . -name "*.twig" -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "Líneas de templates Twig: ${twig_lines:-0}"

echo ""
echo "📁 ESTRUCTURA"
echo "===================="

# Contar archivos por tipo
controllers=$(find Controller -name "*.php" 2>/dev/null | wc -l)
services=$(find Services -name "*.php" 2>/dev/null | wc -l)
repositories=$(find Repository -name "*.php" 2>/dev/null | wc -l)
forms=$(find Form -name "*.php" 2>/dev/null | wc -l)
entities=$(find Entity -name "*.php" 2>/dev/null | wc -l)
templates=$(find Resources/views -name "*.twig" 2>/dev/null | wc -l)
routing_files=$(find Resources/config/routing -name "*.yml" 2>/dev/null | wc -l)

echo "Controladores: $controllers"
echo "Servicios: $services"
echo "Repositorios: $repositories"
echo "Formularios: $forms"
echo "Entidades: $entities"
echo "Templates: $templates"
echo "Archivos de rutas: $routing_files"

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
if [ -d "Controller" ]; then
    ls -1d Controller/*/ 2>/dev/null | sed 's|Controller/||; s|/$||' | sed 's/^/  - /' || echo "  (sin subdirectorios)"
fi

echo ""
echo "📊 COMPLEJIDAD"
echo "===================="

# Top 5 archivos PHP más grandes
echo "Top 5 archivos PHP más grandes:"
find . -name "*.php" -exec wc -l {} + 2>/dev/null | sort -rn | head -6 | tail -5 | \
    awk '{printf "  %s líneas - %s\n", $1, $2}'

echo ""
echo "📝 DOCUMENTACIÓN"
echo "===================="

# Archivos README/docs
readme_count=$(find . -iname "readme*" -o -iname "*.md" 2>/dev/null | wc -l)
echo "Archivos de documentación: $readme_count"

# Cobertura de docblocks
files_with_docblocks=$(grep -rl "\/\*\*" --include="*.php" . 2>/dev/null | wc -l)
total_php_files=$(find . -name "*.php" 2>/dev/null | wc -l)
if [ "$total_php_files" -gt 0 ]; then
    coverage=$((files_with_docblocks * 100 / total_php_files))
else
    coverage=0
fi
echo "Archivos con docblocks: $files_with_docblocks/$total_php_files ($coverage%)"

# Guardar métricas en archivo JSON
cat > "$OUTPUT_DIR/metricas_bundle.json" << EOF
{
  "fecha_analisis": "$(date -I)",
  "bundle_path": "$BUNDLE_DIR",
  "lineas_codigo": {
    "php": ${php_lines:-0},
    "yaml": ${yaml_lines:-0},
    "twig": ${twig_lines:-0},
    "total": $((${php_lines:-0} + ${yaml_lines:-0} + ${twig_lines:-0}))
  },
  "archivos": {
    "controladores": $controllers,
    "servicios": $services,
    "repositorios": $repositories,
    "formularios": $forms,
    "entidades": $entities,
    "templates": $templates,
    "routing_files": $routing_files
  },
  "rutas_definidas": $total_rutas,
  "servicios_registrados": $total_servicios,
  "cobertura_docblocks": $coverage
}
EOF

echo ""
echo "✓ Métricas guardadas en: $OUTPUT_DIR/metricas_bundle.json"
echo ""
echo "📄 Para ver el JSON:"
echo "   cat $OUTPUT_DIR/metricas_bundle.json | jq"
