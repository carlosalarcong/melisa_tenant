#!/bin/bash

echo "=== ACTUALIZANDO IMPORTS EN CONTROLADORES ==="
echo ""

# Lista de todas las entidades movidas a Legacy
ENTIDADES=(
    "AccionClinicaPaciente"
    "AccionClinicaPacienteLog"
    "ArticuloPacienteLog"
    "BonoDetalle"
    "BonoDetalleBonificacion"
    "Caja"
    "CuentaPaciente"
    "CuentaPacienteLog"
    "DerivadorExterno"
    "DetalleCaja"
    "DetalleCajaCheque"
    "DetalleDocumentoPago"
    "DetallePagoCuenta"
    "DetalleTalonario"
    "DetalleTratamiento"
    "Diferencia"
    "DocumentoPago"
    "InterfazImed"
    "Paciente"
    "PagoCuenta"
    "PagoCuentaDetalle"
    "PersonaDomicilio"
    "RelUbicacionCajero"
    "ReservaAtencionLog"
    "Talonario"
    "TalonarioDetalle"
    "Tratamiento"
    "UbicacionCaja"
)

# Actualizar controladores
for entidad in "${ENTIDADES[@]}"; do
    find src/Controller/Caja -name "*.php" -exec sed -i "s|use App\\\\Entity\\\\${entidad};|use App\\\\Entity\\\\Legacy\\\\${entidad};|g" {} \;
done
echo "✓ Controladores actualizados"

# Actualizar servicios
for entidad in "${ENTIDADES[@]}"; do
    find src/Service/Recaudacion -name "*.php" -exec sed -i "s|use App\\\\Entity\\\\${entidad};|use App\\\\Entity\\\\Legacy\\\\${entidad};|g" {} \;
done
echo "✓ Servicios actualizados"

# Actualizar repositorios
for entidad in "${ENTIDADES[@]}"; do
    find src/Repository -name "*.php" -exec sed -i "s|use App\\\\Entity\\\\${entidad};|use App\\\\Entity\\\\Legacy\\\\${entidad};|g" {} \;
done
echo "✓ Repositorios actualizados"

# Actualizar Forms
for entidad in "${ENTIDADES[@]}"; do
    find src/Form -name "*.php" -exec sed -i "s|use App\\\\Entity\\\\${entidad};|use App\\\\Entity\\\\Legacy\\\\${entidad};|g" {} \;
done
echo "✓ Forms actualizados"

echo ""
echo "=== ACTUALIZANDO PHPDOC ==="
echo ""

# Actualizar PHPDoc en todos los archivos
for entidad in "${ENTIDADES[@]}"; do
    find src/Controller/Caja src/Service/Recaudacion src/Repository src/Form -name "*.php" -exec sed -i "
    s|\\\\App\\\\Entity\\\\${entidad}|\\\\App\\\\Entity\\\\Legacy\\\\${entidad}|g
    s|@var App\\\\Entity\\\\${entidad}|@var App\\\\Entity\\\\Legacy\\\\${entidad}|g
    s|@param App\\\\Entity\\\\${entidad}|@param App\\\\Entity\\\\Legacy\\\\${entidad}|g
    s|@return App\\\\Entity\\\\${entidad}|@return App\\\\Entity\\\\Legacy\\\\${entidad}|g
    " {} \;
done
echo "✓ PHPDoc actualizado"

echo ""
echo "=== VERIFICACIÓN ==="
echo ""

# Verificar que no queden referencias sin Legacy (excepto Tenant)
PENDIENTES=$(grep -r "use App\\\\Entity\\\\" src/Controller/Caja src/Service/Recaudacion --include="*.php" | grep -v "Entity\\\\Legacy" | grep -v "Entity\\\\Tenant" | wc -l)

if [ "$PENDIENTES" -gt 0 ]; then
    echo "⚠️  Quedan $PENDIENTES referencias sin Legacy/Tenant:"
    grep -r "use App\\\\Entity\\\\" src/Controller/Caja src/Service/Recaudacion --include="*.php" | grep -v "Entity\\\\Legacy" | grep -v "Entity\\\\Tenant" | head -10
else
    echo "✅ Todas las referencias apuntan a Legacy o Tenant"
fi

echo ""
echo "📊 Resumen de actualizaciones:"
echo "   - Controladores: $(grep -r "App\\\\Entity\\\\Legacy" src/Controller/Caja --include="*.php" | wc -l) referencias"
echo "   - Servicios: $(grep -r "App\\\\Entity\\\\Legacy" src/Service/Recaudacion --include="*.php" | wc -l) referencias"
echo "   - Repositorios: $(grep -r "App\\\\Entity\\\\Legacy" src/Repository --include="*.php" | wc -l) referencias"
