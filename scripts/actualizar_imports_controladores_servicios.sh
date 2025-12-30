#!/bin/bash

echo "=== ACTUALIZANDO IMPORTS EN CONTROLADORES Y SERVICIOS ==="
echo ""

# Actualizar imports de entidades en controladores
echo "Actualizando src/Controller/Caja/..."
find src/Controller/Caja -name "*.php" -exec sed -i 's|use Rebsol\\HermesBundle\\Entity\\|use App\\Entity\\|g' {} \;

# Actualizar imports de entidades en servicios
echo "Actualizando src/Service/Recaudacion/..."
find src/Service/Recaudacion -name "*.php" -exec sed -i 's|use Rebsol\\HermesBundle\\Entity\\|use App\\Entity\\|g' {} \;

# Actualizar también en PHPDoc de controladores y servicios
echo "Actualizando PHPDoc..."
find src/Controller/Caja src/Service/Recaudacion -name "*.php" -exec sed -i 's|\\Rebsol\\HermesBundle\\Entity\\|\\App\\Entity\\|g; s|@var Rebsol\\HermesBundle\\Entity\\|@var App\\Entity\\|g; s|@param Rebsol\\HermesBundle\\Entity\\|@param App\\Entity\\|g; s|@return Rebsol\\HermesBundle\\Entity\\|@return App\\Entity\\|g' {} \;

echo ""
echo "✓ Imports actualizados"

# Verificar
PENDIENTES=$(grep -r "use Rebsol\\\\HermesBundle\\\\Entity\\\\" src/Controller/Caja src/Service/Recaudacion --include="*.php" | wc -l)
echo ""
if [ "$PENDIENTES" -gt 0 ]; then
    echo "⚠️  Aún quedan $PENDIENTES imports de Rebsol\\HermesBundle\\Entity"
else
    echo "✅ Todos los imports de entidades actualizados"
fi
