#!/bin/bash
# analisis_dependencias.sh
# Encuentra qué otros bundles/archivos dependen de RecaudacionBundle

echo "=== ANÁLISIS DE DEPENDENCIAS CRUZADAS ==="
echo "Fecha: $(date)"
echo ""

SRC_DIR="/var/www/html/melisa_prod/src"
OUTPUT_DIR="/var/www/html/melisa_tenant/docs/RecaudacionBundle/analisis"

mkdir -p "$OUTPUT_DIR"

# Verificar que el directorio existe
if [ ! -d "$SRC_DIR" ]; then
    echo "❌ ERROR: No se encuentra el directorio src en $SRC_DIR"
    exit 1
fi

echo "[1/3] Buscando dependencias en otros bundles (PHP)..."

# Buscar use statements
grep -r "use Rebsol\\\\RecaudacionBundle" \
    --include="*.php" \
    --exclude-dir=RecaudacionBundle \
    "$SRC_DIR" 2>/dev/null > "$OUTPUT_DIR/dependencias_use.txt" || touch "$OUTPUT_DIR/dependencias_use.txt"

echo "   ✓ Referencias encontradas: $(wc -l < $OUTPUT_DIR/dependencias_use.txt)"

# Buscar referencias en YAML
echo "[2/3] Buscando referencias en configuración (YAML)..."
grep -r "RecaudacionBundle\|@Recaudacion" \
    --include="*.yml" \
    --include="*.yaml" \
    --exclude-dir=RecaudacionBundle \
    "$SRC_DIR/.." 2>/dev/null > "$OUTPUT_DIR/dependencias_yaml.txt" || touch "$OUTPUT_DIR/dependencias_yaml.txt"

echo "   ✓ Referencias encontradas: $(wc -l < $OUTPUT_DIR/dependencias_yaml.txt)"

# Buscar en templates Twig
echo "[3/3] Buscando referencias en templates (Twig)..."
grep -r "RecaudacionBundle\|@Recaudacion" \
    --include="*.twig" \
    --exclude-dir=RecaudacionBundle \
    "$SRC_DIR/.." 2>/dev/null > "$OUTPUT_DIR/dependencias_twig.txt" || touch "$OUTPUT_DIR/dependencias_twig.txt"

echo "   ✓ Referencias encontradas: $(wc -l < $OUTPUT_DIR/dependencias_twig.txt)"

# Generar reporte
cat > "$OUTPUT_DIR/reporte_dependencias.txt" << EOF
===============================================
  ANÁLISIS DE DEPENDENCIAS CRUZADAS
  RecaudacionBundle
===============================================
Fecha: $(date)

--- ARCHIVOS QUE DEPENDEN DE RecaudacionBundle ---

PHP (use statements): $(wc -l < $OUTPUT_DIR/dependencias_use.txt) referencias
$(cat $OUTPUT_DIR/dependencias_use.txt | awk -F: '{print $1}' | sort | uniq | sed 's/^/  /')

YAML (configuración): $(wc -l < $OUTPUT_DIR/dependencias_yaml.txt) referencias
$(cat $OUTPUT_DIR/dependencias_yaml.txt | awk -F: '{print $1}' | sort | uniq | sed 's/^/  /')

TWIG (templates): $(wc -l < $OUTPUT_DIR/dependencias_twig.txt) referencias
$(cat $OUTPUT_DIR/dependencias_twig.txt | awk -F: '{print $1}' | sort | uniq | sed 's/^/  /')

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
