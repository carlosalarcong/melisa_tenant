#!/bin/bash
# Script para actualizar namespaces de RecaudacionBundle a estructura Symfony 6

echo "=== ACTUALIZANDO NAMESPACES A SYMFONY 6 ==="
echo ""

# 1. Actualizar namespaces de Controladores
echo "1. Actualizando Controladores..."
find src/Controller/Caja -name "*.php" -type f -exec sed -i 's|namespace Rebsol\\RecaudacionBundle\\Controller\\_Default|namespace App\\Controller\\Caja|g' {} \;
find src/Controller/Caja -name "*.php" -type f -exec sed -i 's|use Rebsol\\RecaudacionBundle\\Controller\\|use App\\Controller\\Caja\\|g' {} \;
echo "   ✓ Controladores actualizados"

# 2. Actualizar namespaces de Forms
echo "2. Actualizando Forms..."
find src/Form/Recaudacion -name "*.php" -type f -exec sed -i 's|namespace Rebsol\\RecaudacionBundle\\Form\\Type\\Recaudacion|namespace App\\Form\\Recaudacion|g' {} \;
find src/Form/Supervisor -name "*.php" -type f -exec sed -i 's|namespace Rebsol\\RecaudacionBundle\\Form\\Type\\Supervisor|namespace App\\Form\\Supervisor|g' {} \;
echo "   ✓ Forms actualizados"

# 3. Actualizar namespaces de Services
echo "3. Actualizando Services..."
find src/Service/Recaudacion -name "*.php" -type f -exec sed -i 's|namespace Rebsol\\RecaudacionBundle\\Services|namespace App\\Service\\Recaudacion|g' {} \;
echo "   ✓ Services actualizados"

# 4. Actualizar namespaces de Repositories
echo "4. Actualizando Repositories..."
find src/Repository -name "*RecaudacionBundle*.php" -o -name "*Recaudacion*.php" -o -name "*CuentaPaciente*.php" 2>/dev/null | xargs -r sed -i 's|namespace Rebsol\\RecaudacionBundle\\Repository|namespace App\\Repository|g'
echo "   ✓ Repositories actualizados"

# 5. Actualizar referencias en imports
echo "5. Actualizando imports en todos los archivos..."
find src/Controller/Caja src/Form src/Service/Recaudacion -name "*.php" -type f -exec sed -i 's|use Rebsol\\RecaudacionBundle\\Form\\Type|use App\\Form|g' {} \;
find src/Controller/Caja src/Form src/Service/Recaudacion -name "*.php" -type f -exec sed -i 's|use Rebsol\\RecaudacionBundle\\Services|use App\\Service\\Recaudacion|g' {} \;
find src/Controller/Caja src/Form src/Service/Recaudacion -name "*.php" -type f -exec sed -i 's|use Rebsol\\RecaudacionBundle\\Repository|use App\\Repository|g' {} \;
echo "   ✓ Imports actualizados"

echo ""
echo "✅ Namespaces actualizados a App\\"
echo ""
echo "Verificando un archivo de ejemplo..."
head -15 src/Controller/Caja/Recaudacion/DefaultController.php | grep -E "namespace|use App"
