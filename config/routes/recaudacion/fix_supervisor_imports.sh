#!/bin/bash
cd Supervisor

# Copiar archivos faltantes
mkdir -p UbicacionCaja UbicacionCajero CorrelativoBoletas AutorizacionDescuentos ConsolidadoCaja MantenedorFolios AsientoContable ReporteProduccion ApoyoFacturacion

cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/UbicacionCaja/*.yml UbicacionCaja/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/UbicacionCajero/*.yml UbicacionCajero/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/CorrelativoBoletas/*.yml CorrelativoBoletas/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/AutorizacionDescuentos/*.yml AutorizacionDescuentos/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/ConsolidadoCaja/*.yml ConsolidadoCaja/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/MantenedorFolios/*.yml MantenedorFolios/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/AsientoContable/*.yml AsientoContable/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/ReporteProduccion/*.yml ReporteProduccion/ 2>/dev/null
cp ../../backups/recaudacion_20251230_103342/RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/ApoyoFacturacion/*.yml ApoyoFacturacion/ 2>/dev/null

echo "✓ Archivos copiados"

# Actualizar references en supervisor.yml
sed -i 's|@RecaudacionBundle/Resources/config/routing/_Default/Servet/Supervisor/||g' supervisor.yml

echo "✓ Referencias actualizadas en supervisor.yml"
