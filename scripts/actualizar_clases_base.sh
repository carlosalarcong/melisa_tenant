#!/bin/bash
echo "=== ACTUALIZANDO NAMESPACES DE CLASES BASE ==="
echo ""

# 1. Actualizar namespace principal
echo "1. Actualizando namespaces..."
sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller;|namespace App\\Controller\\Caja;|g' src/Controller/Caja/RecaudacionController.php
sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller;|namespace App\\Controller\\Caja;|g' src/Controller/Caja/BusquedaPacienteController.php
sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller;|namespace App\\Controller\\Caja;|g' src/Controller/Caja/DefaultController.php
sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller;|namespace App\\Controller\\Caja;|g' src/Controller/Caja/GarantiaPacienteController.php
sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller;|namespace App\\Controller\\Caja;|g' src/Controller/Caja/GestionCajaController.php
sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller;|namespace App\\Controller\\Caja;|g' src/Controller/Caja/InformacionActualPacienteController.php
echo "   ✓ Namespaces principales actualizados"

# 2. Actualizar imports de Forms
echo "2. Actualizando imports de Forms..."
sed -i 's|use Rebsol\\RecaudacionBundle\\Form\\Type|use App\\Form|g' src/Controller/Caja/*.php
echo "   ✓ Forms actualizados"

# 3. Actualizar extends de HermesBundle (temporal - marcar para refactorizar)
echo "3. Marcando herencia de HermesBundle (pendiente migración)..."
sed -i 's|use Rebsol\\HermesBundle\\Controller\\DefaultController;|use Rebsol\\HermesBundle\\Controller\\DefaultController; // TODO: Migrar HermesBundle|g' src/Controller/Caja/RecaudacionController.php
echo "   ⚠️  HermesBundle pendiente de migrar"

echo ""
echo "✅ Actualización completada"
echo ""
echo "Verificando RecaudacionController..."
head -20 src/Controller/Caja/RecaudacionController.php | grep -E "namespace|class"
