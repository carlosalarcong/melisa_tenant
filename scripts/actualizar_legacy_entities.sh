#!/bin/bash

echo "=== ACTUALIZANDO NAMESPACES EN ENTIDADES LEGACY ==="
echo ""

# Actualizar namespace en las entidades movidas
for file in src/Entity/Legacy/Persona.php src/Entity/Legacy/Pnatural.php src/Entity/Legacy/PrevisionPnatural.php; do
    sed -i 's|^namespace App\\Entity;|namespace App\\Entity\\Legacy;|g' "$file"
    echo "✓ $(basename $file) - namespace actualizado"
done

echo ""
echo "=== ACTUALIZANDO IMPORTS EN CONTROLADORES Y SERVICIOS ==="
echo ""

# Actualizar imports en controladores
find src/Controller/Caja -name "*.php" -exec sed -i '
s|use App\\Entity\\Persona;|use App\\Entity\\Legacy\\Persona;|g
s|use App\\Entity\\Pnatural;|use App\\Entity\\Legacy\\Pnatural;|g
s|use App\\Entity\\PrevisionPnatural;|use App\\Entity\\Legacy\\PrevisionPnatural;|g
' {} \;
echo "✓ Controladores actualizados"

# Actualizar imports en servicios
find src/Service/Recaudacion -name "*.php" -exec sed -i '
s|use App\\Entity\\Persona;|use App\\Entity\\Legacy\\Persona;|g
s|use App\\Entity\\Pnatural;|use App\\Entity\\Legacy\\Pnatural;|g
s|use App\\Entity\\PrevisionPnatural;|use App\\Entity\\Legacy\\PrevisionPnatural;|g
' {} \;
echo "✓ Servicios actualizados"

# Actualizar imports en otras entidades que referencien estas
find src/Entity -name "*.php" -not -path "*/Legacy/*" -exec sed -i '
s|use App\\Entity\\Persona;|use App\\Entity\\Legacy\\Persona;|g
s|use App\\Entity\\Pnatural;|use App\\Entity\\Legacy\\Pnatural;|g
s|use App\\Entity\\PrevisionPnatural;|use App\\Entity\\Legacy\\PrevisionPnatural;|g
' {} \;
echo "✓ Otras entidades actualizadas"

# Actualizar PHPDoc
find src/Controller/Caja src/Service/Recaudacion src/Entity -name "*.php" -exec sed -i '
s|\\App\\Entity\\Persona|\\App\\Entity\\Legacy\\Persona|g
s|\\App\\Entity\\Pnatural|\\App\\Entity\\Legacy\\Pnatural|g
s|\\App\\Entity\\PrevisionPnatural|\\App\\Entity\\Legacy\\PrevisionPnatural|g
s|@var App\\Entity\\Persona|@var App\\Entity\\Legacy\\Persona|g
s|@var App\\Entity\\Pnatural|@var App\\Entity\\Legacy\\Pnatural|g
s|@var App\\Entity\\PrevisionPnatural|@var App\\Entity\\Legacy\\PrevisionPnatural|g
s|@param App\\Entity\\Persona|@param App\\Entity\\Legacy\\Persona|g
s|@param App\\Entity\\Pnatural|@param App\\Entity\\Legacy\\Pnatural|g
s|@param App\\Entity\\PrevisionPnatural|@param App\\Entity\\Legacy\\PrevisionPnatural|g
s|@return App\\Entity\\Persona|@return App\\Entity\\Legacy\\Persona|g
s|@return App\\Entity\\Pnatural|@return App\\Entity\\Legacy\\Pnatural|g
s|@return App\\Entity\\PrevisionPnatural|@return App\\Entity\\Legacy\\PrevisionPnatural|g
' {} \;
echo "✓ PHPDoc actualizado"

echo ""
echo "=== VERIFICACIÓN ==="
PENDIENTES=$(grep -r "use App\\\\Entity\\\\Persona;" src/Controller/Caja src/Service/Recaudacion src/Entity --include="*.php" | grep -v Legacy | wc -l)
if [ "$PENDIENTES" -gt 0 ]; then
    echo "⚠️  Aún quedan $PENDIENTES referencias sin Legacy"
    grep -r "use App\\\\Entity\\\\Persona;" src/Controller/Caja src/Service/Recaudacion src/Entity --include="*.php" | grep -v Legacy | head -5
else
    echo "✅ Todas las referencias actualizadas correctamente"
fi

echo ""
echo "📊 Resumen:"
echo "   - $(find src/Controller/Caja -name "*.php" -exec grep -l "App\\\\Entity\\\\Legacy" {} \; | wc -l) controladores actualizados"
echo "   - $(find src/Service/Recaudacion -name "*.php" -exec grep -l "App\\\\Entity\\\\Legacy" {} \; | wc -l) servicios actualizados"
echo "   - $(find src/Entity -name "*.php" -not -path "*/Legacy/*" -exec grep -l "App\\\\Entity\\\\Legacy" {} \; | wc -l) otras entidades actualizadas"
