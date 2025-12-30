#!/bin/bash

# Segunda ronda de entidades faltantes
ENTIDADES=(
    $(cd /var/www/html/melisa_tenant && ./scripts/analizar_dependencias_legacy.sh 2>&1 | grep "FALTA" | sed 's/.*⚠️  //; s/ - FALTA//' | sort -u)
)

SOURCE_DIR="/var/www/html/melisa_prod/src/Rebsol/HermesBundle/Entity"
DEST_DIR="src/Entity/Legacy"

echo "=== SEGUNDA RONDA - COPIANDO DEPENDENCIAS EN CASCADA ==="
echo ""
echo "Total a copiar: ${#ENTIDADES[@]}"
echo ""

COPIADAS=0
YA_EXISTEN=0
NO_ENCONTRADAS=0

for entidad in "${ENTIDADES[@]}"; do
    if [ -f "$DEST_DIR/$entidad.php" ]; then
        YA_EXISTEN=$((YA_EXISTEN + 1))
    elif [ -f "$SOURCE_DIR/$entidad.php" ]; then
        cp "$SOURCE_DIR/$entidad.php" "$DEST_DIR/"
        echo "✓ $entidad.php"
        COPIADAS=$((COPIADAS + 1))
    else
        echo "✗ $entidad.php - NO ENCONTRADO"
        NO_ENCONTRADAS=$((NO_ENCONTRADAS + 1))
    fi
done

echo ""
echo "=== RESUMEN ==="
echo "✓ Copiadas: $COPIADAS"
echo "○ Ya existían: $YA_EXISTEN"
echo "✗ No encontradas: $NO_ENCONTRADAS"
echo ""
echo "Total en Legacy: $(ls -1 $DEST_DIR/*.php 2>/dev/null | wc -l)"
