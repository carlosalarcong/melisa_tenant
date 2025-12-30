#!/bin/bash
# analisis_controladores_templates.sh
# Identifica controladores sin rutas y templates huérfanos

echo "=== ANÁLISIS CONTROLADORES Y TEMPLATES ==="
echo "Fecha: $(date)"
echo ""

BUNDLE_DIR="/var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle"
OUTPUT_DIR="/var/www/html/melisa_tenant/docs/RecaudacionBundle/analisis"

mkdir -p "$OUTPUT_DIR"

# Verificar que el bundle existe
if [ ! -d "$BUNDLE_DIR" ]; then
    echo "❌ ERROR: No se encuentra el bundle en $BUNDLE_DIR"
    exit 1
fi

# 1. Analizar controladores
echo "[1/4] Analizando controladores..."
cd "$BUNDLE_DIR/Controller" || exit 1

# Encontrar todos los controladores
find . -name "*.php" > "$OUTPUT_DIR/controladores_existentes.txt"
echo "   ✓ Controladores encontrados: $(wc -l < $OUTPUT_DIR/controladores_existentes.txt)"

# Controladores con rutas (anotaciones o atributos)
grep -r -l "Route\|@Route\|#\[Route\]" . 2>/dev/null > "$OUTPUT_DIR/controladores_con_rutas.txt" || touch "$OUTPUT_DIR/controladores_con_rutas.txt"
echo "   ✓ Controladores con rutas: $(wc -l < $OUTPUT_DIR/controladores_con_rutas.txt)"

# Controladores sin rutas (potencialmente obsoletos)
comm -23 \
    <(sort "$OUTPUT_DIR/controladores_existentes.txt") \
    <(sort "$OUTPUT_DIR/controladores_con_rutas.txt") \
    > "$OUTPUT_DIR/controladores_sin_rutas.txt"

echo "   ⚠️  Controladores SIN rutas: $(wc -l < $OUTPUT_DIR/controladores_sin_rutas.txt)"

# 2. Analizar templates
echo "[2/4] Analizando templates..."
cd "$BUNDLE_DIR"

if [ -d "Resources/views" ]; then
    find Resources/views -name "*.twig" 2>/dev/null > "$OUTPUT_DIR/templates_existentes.txt"
    echo "   ✓ Templates encontrados: $(wc -l < $OUTPUT_DIR/templates_existentes.txt)"
else
    echo "   ⚠️  Directorio views no encontrado"
    touch "$OUTPUT_DIR/templates_existentes.txt"
fi

# Templates referenciados en controladores
echo "[3/4] Buscando referencias a templates en controladores..."
cd "$BUNDLE_DIR/Controller"
grep -roh "render.*['\"].*\.twig['\"]" . 2>/dev/null | \
    grep -o "['\"][^'\"]*\.twig['\"]" | \
    sed "s/['\"]//g" | \
    sort | uniq > "$OUTPUT_DIR/templates_referenciados.txt"

echo "   ✓ Templates referenciados: $(wc -l < $OUTPUT_DIR/templates_referenciados.txt)"

# Templates huérfanos (sin referencias)
echo "[4/4] Identificando templates huérfanos..."

> "$OUTPUT_DIR/templates_huerfanos.txt"

if [ -f "$OUTPUT_DIR/templates_existentes.txt" ] && [ -s "$OUTPUT_DIR/templates_existentes.txt" ]; then
    while IFS= read -r template; do
        template_name=$(basename "$template")
        
        # Buscar en controladores
        if ! grep -rq "$template_name" "$BUNDLE_DIR/Controller/" 2>/dev/null; then
            # Buscar en otros templates (includes/extends)
            if [ -d "$BUNDLE_DIR/Resources/views" ]; then
                if ! grep -rq "$template_name" "$BUNDLE_DIR/Resources/views/" 2>/dev/null; then
                    echo "$template" >> "$OUTPUT_DIR/templates_huerfanos.txt"
                fi
            else
                echo "$template" >> "$OUTPUT_DIR/templates_huerfanos.txt"
            fi
        fi
    done < "$OUTPUT_DIR/templates_existentes.txt"
    
    echo "   ⚠️  Templates huérfanos: $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo 0)"
else
    echo "   ⚠️  Templates huérfanos: 0"
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
Templates huérfanos: $(wc -l < $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo 0)

Templates huérfanos (candidatos a eliminar):
$(cat $OUTPUT_DIR/templates_huerfanos.txt 2>/dev/null || echo "Ninguno")

===============================================
EOF

cat "$OUTPUT_DIR/reporte_controladores_templates.txt"

echo ""
echo "✓ Análisis completado. Archivos en: $OUTPUT_DIR"
