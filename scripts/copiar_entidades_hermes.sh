#!/bin/bash

# Lista de entidades necesarias (sin duplicados)
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
    "Persona"
    "PersonaDomicilio"
    "Pnatural"
    "PrevisionPnatural"
    "RelUbicacionCajero"
    "ReservaAtencionLog"
    "Talonario"
    "TalonarioDetalle"
    "Tratamiento"
    "UbicacionCaja"
)

SOURCE_DIR="/var/www/html/melisa_prod/src/Rebsol/HermesBundle/Entity"
DEST_DIR="src/Entity"

mkdir -p "$DEST_DIR"

echo "=== COPIANDO ENTIDADES DE HERMESBUNDLE ==="
echo ""

for entidad in "${ENTIDADES[@]}"; do
    if [ -f "$SOURCE_DIR/$entidad.php" ]; then
        cp "$SOURCE_DIR/$entidad.php" "$DEST_DIR/"
        echo "✓ $entidad.php"
    else
        echo "✗ $entidad.php - NO ENCONTRADO"
    fi
done

echo ""
echo "=== TOTAL: $(ls -1 $DEST_DIR/*.php 2>/dev/null | wc -l) entidades copiadas ==="
