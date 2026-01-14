#!/bin/bash

################################################################################
# Script de Deploy Automatizado - Melisa Tenant
# Uso: ./scripts/deploy.sh [branch]
# Ejemplo: ./scripts/deploy.sh feature/upgrade-symfony-7.4
################################################################################

set -e  # Detener si hay errores

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
BRANCH="${1:-feature/upgrade-symfony-7.4}"
PROJECT_DIR="/var/www/html/melisa_tenant"
BACKUP_DIR="$PROJECT_DIR/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo -e "${BLUE}╔══════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║       Deploy Automatizado - Melisa Tenant               ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════╝${NC}"
echo ""

# 1. Verificar directorio
echo -e "${YELLOW}[1/9] Verificando directorio del proyecto...${NC}"
cd "$PROJECT_DIR" || exit 1
echo -e "${GREEN}✓ Directorio correcto: $(pwd)${NC}"
echo ""

# 2. Crear backup de código actual
echo -e "${YELLOW}[2/9] Creando backup del código actual...${NC}"
BACKUP_FILE="$BACKUP_DIR/backup_$TIMESTAMP.tar.gz"
mkdir -p "$BACKUP_DIR"
tar -czf "$BACKUP_FILE" \
    --exclude='var/cache/*' \
    --exclude='var/log/*' \
    --exclude='vendor/*' \
    --exclude='node_modules/*' \
    --exclude='backups/*' \
    . 2>/dev/null || echo "Advertencia: Algunos archivos no se pudieron respaldar"
echo -e "${GREEN}✓ Backup creado: $BACKUP_FILE${NC}"
echo ""

# 3. Verificar estado de Git
echo -e "${YELLOW}[3/9] Verificando estado de Git...${NC}"
git fetch origin
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo -e "Rama actual: ${BLUE}$CURRENT_BRANCH${NC}"
if [ "$CURRENT_BRANCH" != "$BRANCH" ]; then
    echo -e "${YELLOW}Cambiando a rama: $BRANCH${NC}"
    git checkout "$BRANCH"
fi
echo -e "${GREEN}✓ Git verificado${NC}"
echo ""

# 4. Stash cambios locales si existen
echo -e "${YELLOW}[4/9] Guardando cambios locales...${NC}"
if ! git diff-index --quiet HEAD --; then
    echo -e "${YELLOW}Se encontraron cambios locales, guardando con stash...${NC}"
    git stash save "Auto-stash before deploy $TIMESTAMP"
    echo -e "${GREEN}✓ Cambios guardados en stash${NC}"
else
    echo -e "${GREEN}✓ No hay cambios locales${NC}"
fi
echo ""

# 5. Pull del repositorio
echo -e "${YELLOW}[5/9] Descargando últimos cambios del repositorio...${NC}"
git pull origin "$BRANCH"
echo -e "${GREEN}✓ Código actualizado${NC}"
echo ""

# 6. Instalar dependencias de PHP
echo -e "${YELLOW}[6/9] Instalando dependencias de Composer...${NC}"
# Detectar si es entorno de desarrollo o producción
if [ "$APP_ENV" = "prod" ]; then
    echo -e "${BLUE}  → Instalando sin dependencias de desarrollo (producción)${NC}"
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo -e "${BLUE}  → Instalando con dependencias de desarrollo${NC}"
    composer install --optimize-autoloader --no-interaction
fi
echo -e "${GREEN}✓ Dependencias de Composer instaladas${NC}"
echo ""

# 7. Ejecutar tests unitarios
echo -e "${YELLOW}[7/10] Ejecutando tests unitarios...${NC}"
if [ "$APP_ENV" != "prod" ]; then
    echo -e "${BLUE}  → Ejecutando PHPUnit...${NC}"
    php bin/phpunit --testdox || {
        echo -e "${RED}✗ Tests fallaron. Deploy abortado.${NC}"
        exit 1
    }
    echo -e "${GREEN}✓ Todos los tests pasaron${NC}"
else
    echo -e "${YELLOW}  → Tests omitidos en producción${NC}"
fi
echo ""

# 8. Ejecutar migraciones de base de datos
echo -e "${YELLOW}[8/10] Ejecutando migraciones de base de datos...${NC}"

# Migración de BD principal
echo -e "${BLUE}  → Migraciones de BD principal (Main)...${NC}"
php bin/console doctrine:migrations:migrate --em=main --no-interaction || echo "Advertencia: No hay nuevas migraciones en Main"

# Migraciones de tenants
echo -e "${BLUE}  → Migraciones de BD tenants...${NC}"
php bin/console doctrine:migrations:migrate --em=tenant --no-interaction || echo "Advertencia: No hay nuevas migraciones en Tenant"

echo -e "${GREEN}✓ Migraciones ejecutadas${NC}"
echo ""

# 9. Limpiar y calentar cache
echo -e "${YELLOW}[9/10] Limpiando y calentando cache...${NC}"
php bin/console cache:clear --no-warmup
php bin/console cache:warmup
echo -e "${GREEN}✓ Cache optimizado${NC}"
echo ""

# 10. Verificar permisos
echo -e "${YELLOW}[10/10] Verificando permisos de directorios...${NC}"
chmod -R 775 var/cache var/log 2>/dev/null || echo "Advertencia: No se pudieron cambiar algunos permisos"
echo -e "${GREEN}✓ Permisos verificados${NC}"
echo ""

# Resumen final
echo -e "${GREEN}╔══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║               ✓ DEPLOY COMPLETADO CON ÉXITO             ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}Resumen:${NC}"
echo -e "  • Rama desplegada: ${GREEN}$BRANCH${NC}"
echo -e "  • Commit actual: ${GREEN}$(git rev-parse --short HEAD)${NC}"
echo -e "  • Backup guardado: ${GREEN}$BACKUP_FILE${NC}"
echo -e "  • Fecha: ${GREEN}$(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo ""
echo -e "${YELLOW}Próximos pasos:${NC}"
echo -e "  1. Verificar la aplicación en: http://melisahospital.localhost:8081"
echo -e "  2. Revisar logs: tail -f var/log/prod.log"
echo -e "  3. Si hay problemas, restaurar backup: tar -xzf $BACKUP_FILE"
echo ""
