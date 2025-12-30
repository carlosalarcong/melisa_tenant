#!/bin/bash

DEST_DIR="src/Entity"

echo "=== ACTUALIZANDO NAMESPACES DE ENTIDADES ==="
echo ""

# Cambiar namespace principal
find "$DEST_DIR" -name "*.php" -exec sed -i 's|namespace Rebsol\\HermesBundle\\Entity;|namespace App\\Entity;|g' {} \;

# Actualizar imports de otras entidades de HermesBundle
find "$DEST_DIR" -name "*.php" -exec sed -i 's|use Rebsol\\HermesBundle\\Entity\\|use App\\Entity\\|g' {} \;

# Contar archivos procesados
TOTAL=$(find "$DEST_DIR" -name "*.php" | wc -l)

echo "✓ Namespaces actualizados en $TOTAL archivos"
echo ""

# Verificar que no queden referencias a Rebsol\HermesBundle\Entity
PENDIENTES=$(grep -r "Rebsol\\\\HermesBundle\\\\Entity" "$DEST_DIR" --include="*.php" | wc -l)
if [ "$PENDIENTES" -gt 0 ]; then
    echo "⚠️  Aún quedan $PENDIENTES referencias a Rebsol\\HermesBundle\\Entity"
    grep -rn "Rebsol\\\\HermesBundle\\\\Entity" "$DEST_DIR" --include="*.php" | head -10
else
    echo "✅ No quedan referencias a Rebsol\\HermesBundle\\Entity"
fi
