# 📊 Reporte Completo de Análisis - RecaudacionBundle

**Fecha de análisis:** Tue Dec 30 10:26:26 -03 2025
**Generado automáticamente**

---

## 📈 Resumen Ejecutivo

### Métricas Generales

```json
{
  "fecha_analisis": "2025-12-30",
  "bundle_path": "/var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle",
  "lineas_codigo": {
    "php": 30599,
    "yaml": 0,
    "twig": 66612,
    "total": 97211
  },
  "archivos": {
    "controladores": 73,
    "servicios": 28,
    "repositorios": 7,
    "formularios": 35,
    "entidades": 0,
    "templates": 187,
    "routing_files": 32
  },
  "rutas_definidas": 258,
  "servicios_registrados": 2,
  "cobertura_docblocks": 60
}
```

---

## 🎯 Controladores

### Total de Controladores
- **Existentes:** 73
- **Con rutas:** 0
- **Sin rutas:** 73

### Controladores sin rutas (candidatos a eliminar)
```
./Api/Caja/Recaudacion/RecaudacionController.php
./Api/Unab/PagoCuenta/CuentaPacienteController.php
./Api/Unab/PagoCuenta/PagoCuentaController.php
./ApiPV/Recaudacion/RecaudacionController.php
./ApiPV/Supervisor/ConsolidadoCajaPorProfesional/ConsolidadoCajaPorProfesionalController.php
./ApiPV/Supervisor/ConsolidadoCajaPorProfesional/ConsolidadoCajaPorProfesionalInformeController.php
./BusquedaPacienteController.php
./DefaultController.php
./GarantiaPacienteController.php
./GestionCajaController.php
./InformacionActualPacienteController.php
./PagoCuenta/CuentaPacienteController.php
./PagoCuenta/PagoCuentaController.php
./PagoCuenta/RealizarPagoCuentaPacienteController.php
./RecaudacionController.php
./_Default/Recaudacion/BuscaProvinciaporComunaController.php
./_Default/Recaudacion/DefaultController.php
./_Default/Recaudacion/Diferencia/DiferenciaController.php
./_Default/Recaudacion/GestionCaja/GestionCajaController.php
./_Default/Recaudacion/Imed/ImedController.php
```

---

## 🎨 Templates

### Total de Templates
- **Existentes:** 187
- **Referenciados:** 100
- **Huérfanos:** 31

### Templates huérfanos (candidatos a eliminar)
```
Resources/views/FormasDePago/IndexImed.html.twig
Resources/views/FormasDePago/FormaDePago_Efectivo.html.twig
Resources/views/FormasDePago/DynamicControl.html.twig
Resources/views/FormasDePago/OtrosMedios_CartaConvenioImed.html.twig
Resources/views/FormasDePago/FormaDePago_TarjetaDebito.html.twig
Resources/views/FormasDePago/ImedMensajeError.html.twig
Resources/views/FormasDePago/FormaDePago_TarjetaCredito.html.twig
Resources/views/FormasDePago/OtrosMedios_CartaConvenioLasik.html.twig
Resources/views/FormasDePago/FormaDePago_BonoElectronico_IMED.html.twig
Resources/views/FormasDePago/FormaDePago_Gratuidad.html.twig
Resources/views/ApiPV/Recaudacion/GestionCaja/Informes/InformeMedioDePagoPDF.html.twig
Resources/views/ApiPV/Recaudacion/GestionCaja/Informes/InformeMedioDePago.html.twig
Resources/views/Supervisor/ConsolidadoCaja/InformeCajaEditar.html.twig
Resources/views/Supervisor/ConsolidadoCaja/InformeCajaPDF.html.twig
Resources/views/Supervisor/ConsolidadoCaja/InformeCajaVer.html.twig
Resources/views/Supervisor/ConsolidadoCaja/InformeCajaEditarVacio.html.twig
Resources/views/Supervisor/ConsolidadoCaja/InformeCajaEditar.html_1.twig
Resources/views/Recaudacion/GestionCaja/Informes/InformeCajaPDF.html.twig
Resources/views/Recaudacion/GestionCaja/Informes/InformeCajaPDFWeb.html.twig
Resources/views/Recaudacion/GestionCaja/Informes/InformeCajaWeb.html.twig
```

---

## 🚦 Rutas

### Total de Rutas
- **Definidas en YAML:** 172
- **Nombres únicos:** 321

### Muestra de rutas definidas (primeras 20)
```
/
/ActualizaPacienteSinRut
/ActualizarBono/idPago/
/ActualizarDatos
/AnulacionPagoAgenda
/AnulacionPagoAgendaApi1
/Anular_diferencia
/Aplicar_diferencia
/BuscaMascotasPorDueno
/BuscaOtrosMedios
/BusquedaAvanzada
/BusquedaExtranjero
/BusquedaRut
/CMPVP
/CajaAnulaPagoEsImed
/CajaGetNuevoFormDinamico
/CajaImedIndex
/CajaImedValidaPrevisionEsImed
/CajaPostPagoAbrirPDF
/CajaPostPagoEliminarPDF
```

⚠️ **NOTA:** Para análisis de uso real, se requiere acceso a logs de producción.

---

## 🔗 Dependencias

### Archivos que dependen de RecaudacionBundle

- **PHP (use statements):** 2 archivos
- **YAML (configuración):** 2 archivos  
- **TWIG (templates):** 4 archivos

---

## ✅ Recomendaciones

### Limpieza de Código

1. **Eliminar controladores sin rutas:** 73 archivos
2. **Eliminar templates huérfanos:** 31 archivos
3. **Revisar rutas definidas vs uso real** (requiere logs de producción)

### Estimación de Reducción de Código

Basado en los archivos identificados:
- **Controladores a eliminar:** ~73 archivos
- **Templates a eliminar:** ~31 archivos
- **Reducción estimada:** 10-25% del código total

---

## 📂 Archivos Generados

Todos los archivos de análisis se encuentran en:
`/var/www/html/melisa_tenant/docs/RecaudacionBundle/analisis`

- `metricas_bundle.json` - Métricas en formato JSON
- `controladores_existentes.txt` - Lista de todos los controladores
- `controladores_sin_rutas.txt` - Controladores sin rutas
- `templates_existentes.txt` - Lista de todos los templates
- `templates_huerfanos.txt` - Templates sin referencias
- `rutas_definidas.txt` - Todas las rutas del bundle
- `dependencias_*.txt` - Archivos que dependen del bundle

---

## 🚀 Próximos Pasos

1. **Revisar con equipo de negocio** los archivos candidatos a eliminar
2. **Analizar logs de producción** para identificar rutas no utilizadas
3. **Crear backup completo** antes de eliminar código
4. **Iniciar migración a Symfony 6** siguiendo el plan establecido

---

_Reporte generado automáticamente por scripts de análisis_
_Ver logs individuales en `/var/www/html/melisa_tenant/docs/RecaudacionBundle/analisis/*.log`_
