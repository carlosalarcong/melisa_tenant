#!/bin/bash

echo "=== ANALIZANDO DEPENDENCIAS EN ENTIDADES LEGACY ==="
echo ""

# Buscar todas las referencias a entidades que NO estén en Legacy ni en Tenant
echo "Buscando referencias a otras entidades..."
echo ""

# Extraer todas las clases referenciadas en use statements
grep -rh "use App\\\\Entity\\\\Legacy\\\\" src/Entity/Legacy/*.php | \
    sed 's/use App\\Entity\\Legacy\\//; s/;//' | \
    sort -u > /tmp/legacy_refs.txt

# Buscar referencias a entidades que aún usen Rebsol\HermesBundle
echo "⚠️  Referencias a HermesBundle que aún existen:"
grep -rn "Rebsol\\\\HermesBundle\\\\Entity" src/Entity/Legacy/*.php | \
    sed 's|.*Rebsol\\HermesBundle\\Entity\\||; s|[^a-zA-Z0-9_].*||' | \
    sort -u | \
    while read entidad; do
        if [ ! -f "src/Entity/Legacy/$entidad.php" ]; then
            echo "   ✗ $entidad - NO ESTÁ EN LEGACY"
        fi
    done

echo ""
echo "=== ANALIZANDO @ManyToOne, @OneToMany, @ManyToMany ==="
echo ""

# Buscar relaciones en anotaciones Doctrine
for file in src/Entity/Legacy/*.php; do
    entidad=$(basename "$file" .php)
    
    # Buscar targetEntity en anotaciones
    referencias=$(grep -o 'targetEntity="[^"]*"' "$file" 2>/dev/null | \
        sed 's/targetEntity="//; s/"//g' | \
        sed 's/.*\\//' | \
        sort -u)
    
    if [ ! -z "$referencias" ]; then
        echo "📋 $entidad tiene relaciones con:"
        for ref in $referencias; do
            if [ ! -f "src/Entity/Legacy/$ref.php" ] && [ ! -f "src/Entity/Tenant/$ref.php" ]; then
                echo "   ⚠️  $ref - FALTA"
            else
                echo "   ✓ $ref - OK"
            fi
        done
    fi
done

echo ""
echo "=== ENTIDADES REFERENCIADAS EN PHPDOC ==="
echo ""

# Buscar en PHPDoc referencias a entidades
grep -rh "@var.*\\\\App\\\\Entity\\\\Legacy\\\\" src/Entity/Legacy/*.php | \
    sed 's|.*\\App\\Entity\\Legacy\\||; s|[^a-zA-Z0-9_].*||' | \
    sort -u | \
    while read entidad; do
        if [ ! -f "src/Entity/Legacy/$entidad.php" ]; then
            echo "⚠️  $entidad referenciado en PHPDoc pero NO ESTÁ EN LEGACY"
        fi
    done

