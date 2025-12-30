#!/bin/bash
# analisis_rutas.sh
# Identifica rutas definidas en el bundle

echo "=== ANÁLISIS DE RUTAS RECAUDACIONBUNDLE ==="
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

echo "[1/2] Extrayendo rutas definidas en configuración..."
cd "$BUNDLE_DIR"

# Extraer rutas de archivos YAML
if [ -d "Resources/config/routing" ]; then
    find Resources/config/routing -name "*.yml" -exec grep -h "^\s\+path:" {} \; 2>/dev/null | \
        awk '{print $2}' | \
        sed 's/[{}]//g' | \
        sort | uniq > "$OUTPUT_DIR/rutas_definidas.txt"
    
    echo "   ✓ Rutas definidas en YAML: $(wc -l < $OUTPUT_DIR/rutas_definidas.txt)"
else
    echo "   ⚠️  Directorio routing no encontrado"
    touch "$OUTPUT_DIR/rutas_definidas.txt"
fi

# Extraer nombres de rutas (route names)
echo "[2/2] Extrayendo nombres de rutas..."
if [ -d "Resources/config/routing" ]; then
    find Resources/config/routing -name "*.yml" -exec grep -B1 "path:" {} \; 2>/dev/null | \
        grep -v "path:" | grep -v "^--$" | \
        sed 's/://g' | sed 's/^\s*//' | \
        sort | uniq > "$OUTPUT_DIR/nombres_rutas.txt"
    
    echo "   ✓ Nombres de rutas: $(wc -l < $OUTPUT_DIR/nombres_rutas.txt)"
else
    touch "$OUTPUT_DIR/nombres_rutas.txt"
fi

# Analizar prefijos de rutas
echo ""
echo "📊 ANÁLISIS DE RUTAS"
echo "===================="

if [ -s "$OUTPUT_DIR/rutas_definidas.txt" ]; then
    echo ""
    echo "Rutas por prefijo:"
    cat "$OUTPUT_DIR/rutas_definidas.txt" | \
        awk -F/ '{if ($2 != "") print $2}' | \
        sort | uniq -c | sort -rn | \
        awk '{printf "  %3d rutas - /%s\n", $1, $2}'
    
    echo ""
    echo "Métodos HTTP detectados:"
    if [ -d "Resources/config/routing" ]; then
        grep -rh "methods:" Resources/config/routing/*.yml 2>/dev/null | \
            sed 's/methods://g; s/\[//g; s/\]//g; s/,/ /g' | \
            tr ' ' '\n' | sed 's/^\s*//; s/\s*$//' | \
            grep -v '^$' | sort | uniq -c | sort -rn | \
            awk '{printf "  %s: %d rutas\n", $2, $1}'
    fi
    
    echo ""
    echo "Top 10 rutas más profundas (más segmentos):"
    awk '{print NF-1, $0}' FS=/ "$OUTPUT_DIR/rutas_definidas.txt" | \
        sort -rn | head -10 | \
        awk '{$1=""; print "  " $0}'
fi

# Generar reporte
cat > "$OUTPUT_DIR/reporte_rutas.txt" << EOF
===============================================
  REPORTE DE ANÁLISIS DE RUTAS
  RecaudacionBundle
===============================================
Fecha: $(date)

--- RESUMEN ---
Rutas definidas en configuración: $(wc -l < $OUTPUT_DIR/rutas_definidas.txt)
Nombres de rutas únicos: $(wc -l < $OUTPUT_DIR/nombres_rutas.txt)

--- RUTAS DEFINIDAS ---
$(cat $OUTPUT_DIR/rutas_definidas.txt)

--- NOMBRES DE RUTAS ---
$(cat $OUTPUT_DIR/nombres_rutas.txt)

===============================================
NOTA: Para análisis de uso real, se requiere acceso a logs de Apache/Nginx
      Ejecutar este script en el servidor de producción para mejores resultados.
===============================================
EOF

echo ""
cat "$OUTPUT_DIR/reporte_rutas.txt"

echo ""
echo "✓ Análisis completado. Archivos en: $OUTPUT_DIR"
echo ""
echo "⚠️  NOTA IMPORTANTE:"
echo "   Para análisis de uso real (rutas no utilizadas), necesitas:"
echo "   1. Acceso a logs de Apache/Nginx del servidor de producción"
echo "   2. Ejecutar el script analisis_rutas_completo.sh en producción"
