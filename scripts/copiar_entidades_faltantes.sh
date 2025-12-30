#!/bin/bash

# Lista de entidades faltantes
ENTIDADES_FALTANTES=(
    "AccionClinica"
    "AdministradorSeguro"
    "Articulo"
    "Banco"
    "Bodega"
    "Comuna"
    "CondicionPago"
    "DetalleNivelInstruccion"
    "Empresa"
    "EmpresaSolicitante"
    "Estado"
    "EstadoAccionClinica"
    "EstadoConyugal"
    "EstadoCuenta"
    "EstadoDetalleTalonario"
    "EstadoDiferencia"
    "EstadoPago"
    "EstadoPila"
    "EstadoReapertura"
    "EstadoRelUbicacionCajero"
    "EstadoReproductivo"
    "EstadoTalonarioDetalle"
    "EstadoTratamiento"
    "ExamenPacienteFcDetalle"
    "Facturacion"
    "FormaPago"
    "HorarioConsulta"
    "MotivoDiferencia"
    "MotivoGratuidad"
    "NivelFonasa"
    "Ocupacion"
    "Origen"
    "PabAgenda"
    "PagoWeb"
    "PaqueteArticulo"
    "PaquetePrestacion"
    "PrPlan"
    "Prevision"
    "PuebloOriginario"
    "Raza"
    "RchIndicacionPlanificacion"
    "RchReceta"
    "RecienNacido"
    "RelEmpresaTipoDocumento"
    "RelUsuarioServicio"
    "ReservaAtencion"
    "ReservaAtencionTipoLog"
    "Rol"
    "Servicio"
    "SubEmpresa"
    "Sucursal"
    "TarjetaCredito"
    "TipoAtencionFc"
    "TipoCargaArticuloPaciente"
    "TipoIdentificacionExtranjero"
    "TipoPnatural"
    "TipoTratamiento"
    "UsuariosRebsol"
)

SOURCE_DIR="/var/www/html/melisa_prod/src/Rebsol/HermesBundle/Entity"
DEST_DIR="src/Entity/Legacy"

echo "=== COPIANDO ENTIDADES FALTANTES ==="
echo ""
echo "Total a copiar: ${#ENTIDADES_FALTANTES[@]}"
echo ""

COPIADAS=0
NO_ENCONTRADAS=0

for entidad in "${ENTIDADES_FALTANTES[@]}"; do
    if [ -f "$SOURCE_DIR/$entidad.php" ]; then
        cp "$SOURCE_DIR/$entidad.php" "$DEST_DIR/"
        echo "✓ $entidad.php"
        COPIADAS=$((COPIADAS + 1))
    else
        echo "✗ $entidad.php - NO ENCONTRADO en melisa_prod"
        NO_ENCONTRADAS=$((NO_ENCONTRADAS + 1))
    fi
done

echo ""
echo "=== RESUMEN ==="
echo "✓ Copiadas: $COPIADAS"
echo "✗ No encontradas: $NO_ENCONTRADAS"
echo ""
echo "Total en Legacy: $(ls -1 $DEST_DIR/*.php 2>/dev/null | wc -l)"
