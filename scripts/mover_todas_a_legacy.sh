#!/bin/bash

echo "=== MOVIENDO TODAS LAS ENTIDADES LEGACY ==="
echo ""

# Mover todas las entidades .php que están directamente en src/Entity/ (no en subdirectorios)
for file in src/Entity/*.php; do
    if [ -f "$file" ]; then
        filename=$(basename "$file")
        mv "$file" src/Entity/Legacy/
        echo "✓ $filename → Legacy/"
    fi
done

echo ""
echo "=== ACTUALIZANDO NAMESPACES EN LEGACY ==="
echo ""

# Actualizar namespace en todas las entidades de Legacy
find src/Entity/Legacy -name "*.php" -exec sed -i 's|^namespace App\\Entity;|namespace App\\Entity\\Legacy;|g' {} \;
echo "✓ Namespaces actualizados"

# Actualizar imports entre entidades Legacy (referencias internas)
find src/Entity/Legacy -name "*.php" -exec sed -i 's|use App\\Entity\\|use App\\Entity\\Legacy\\|g' {} \;
echo "✓ Imports internos actualizados"

# Actualizar PHPDoc en entidades Legacy
find src/Entity/Legacy -name "*.php" -exec sed -i '
s|\\App\\Entity\\|\\App\\Entity\\Legacy\\|g
s|@var App\\Entity\\|@var App\\Entity\\Legacy\\|g
s|@param App\\Entity\\|@param App\\Entity\\Legacy\\|g
s|@return App\\Entity\\|@return App\\Entity\\Legacy\\|g
' {} \;
echo "✓ PHPDoc en Legacy actualizado"

echo ""
echo "Total entidades en Legacy: $(ls -1 src/Entity/Legacy/*.php | wc -l)"
