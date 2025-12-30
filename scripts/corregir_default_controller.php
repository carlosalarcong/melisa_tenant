<?php
/**
 * Script para corregir getRepository() sin argumentos en DefaultController.php
 */

$file = __DIR__ . '/../src/Controller/Legacy/DefaultController.php';
$content = file_get_contents($file);

// Mapa de reemplazos basado en el código original
$replacements = [
    // botarUsuarioRebsol
    "\$oUsuarioRebsol = \$em->getRepository()->find(\$oUsuarioRebsol);" 
        => "\$oUsuarioRebsol = \$em->getRepository('App\\Entity\\Legacy\\UsuariosRebsol')->find(\$oUsuarioRebsol);",
    "\$oUsuarioExcluido = \$em->getRepository()->findOneBy(array('idUsuario'=>\$oUsuarioRebsol->getId()));" 
        => "\$oUsuarioExcluido = \$em->getRepository('App\\Entity\\Legacy\\UsuarioExcluido')->findOneBy(array('idUsuario'=>\$oUsuarioRebsol->getId()));",
    "\$oEstadoActivo = \$em->getRepository()->find(\$this->getParameter('estado_activo'));" 
        => "\$oEstadoActivo = \$em->getRepository('App\\Entity\\Legacy\\Estado')->find(\$this->getParameter('estado_activo'));",
    
    // obtenerApiModulo
    "\$oModulo = \$em->getRepository()->find(\$idModulo);" 
        => "\$oModulo = \$em->getRepository('App\\Entity\\Legacy\\Modulo')->find(\$idModulo);",
    "\$oApisModulo = \$em->getRepository()->obtenerApiModuloEmpresa(\$oModulo, \$oEmpresa, \$this->getParameter('estado_activo'));" 
        => "\$oApisModulo = \$em->getRepository('App\\Entity\\Legacy\\Perfil')->obtenerApiModuloEmpresa(\$oModulo, \$oEmpresa, \$this->getParameter('estado_activo'));",
    
    // crearMeetingZoom
    "\$oProfesionalNatural = \$em->getRepository()->findOneBy(['idPersona' => \$oReservaAtencion->getIdUsuarioProfesional()->getIdPersona()->getId()]);" 
        => "\$oProfesionalNatural = \$em->getRepository('App\\Entity\\Legacy\\Pnatural')->findOneBy(['idPersona' => \$oReservaAtencion->getIdUsuarioProfesional()->getIdPersona()->getId()]);",
    
    // método estado() - Estados
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPila.activo'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPila')->find(\$this->getParameter('EstadoPila.activo'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPila.inactivo'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPila')->find(\$this->getParameter('EstadoPila.inactivo'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoReapertura.cerrada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoReapertura')->find(\$this->getParameter('EstadoReapertura.cerrada'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoReapertura.abierta'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoReapertura')->find(\$this->getParameter('EstadoReapertura.abierta'));",
    "return \$em->getRepository()->find(\$this->getParameter('Estado.activo'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\Estado')->find(\$this->getParameter('Estado.activo'));",
    "return \$em->getRepository()->find(\$this->getParameter('Estado.inactivo'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\Estado')->find(\$this->getParameter('Estado.inactivo'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPago.pagadoNormal'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPago')->find(\$this->getParameter('EstadoPago.pagadoNormal'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPago.anulado'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPago')->find(\$this->getParameter('EstadoPago.anulado'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPago.garantia'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPago')->find(\$this->getParameter('EstadoPago.garantia'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPago.garantiaRegularizada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPago')->find(\$this->getParameter('EstadoPago.garantiaRegularizada'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoPago.pendientePago'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoPago')->find(\$this->getParameter('EstadoPago.pendientePago'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.cerradaPagada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.cerradaPagada'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.anulado'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.anulado'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.cerradaPendientePago'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.cerradaPendientePago'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.abiertaPendientePago'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.abiertaPendientePago'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.cerradaPagadaTotal'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.cerradaPagadaTotal'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.abiertaPagadaTotal'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.abiertaPagadaTotal'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoCuenta.cerradaRevisionInterna'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoCuenta')->find(\$this->getParameter('EstadoCuenta.cerradaRevisionInterna'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDetalleTalonario.emitidas'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDetalleTalonario')->find(\$this->getParameter('EstadoDetalleTalonario.emitidas'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDetalleTalonario.anulada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDetalleTalonario')->find(\$this->getParameter('EstadoDetalleTalonario.anulada'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoAccionClinica.solicitado'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoAccionClinica')->find(\$this->getParameter('EstadoAccionClinica.solicitado'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoTratamiento.Finalizado'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoTratamiento')->find(\$this->getParameter('EstadoTratamiento.Finalizado'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoTratamiento.EnProceso'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoTratamiento')->find(\$this->getParameter('EstadoTratamiento.EnProceso'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoTratamiento.Anulado'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoTratamiento')->find(\$this->getParameter('EstadoTratamiento.Anulado'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDiferencia.cajeroPideAutorizacion'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDiferencia')->find(\$this->getParameter('EstadoDiferencia.cajeroPideAutorizacion'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDiferencia.autorizada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDiferencia')->find(\$this->getParameter('EstadoDiferencia.autorizada'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDiferencia')->find(\$this->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDiferencia.cajeroCancelaSolicitud'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDiferencia')->find(\$this->getParameter('EstadoDiferencia.cajeroCancelaSolicitud'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoDiferencia.rechazada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoDiferencia')->find(\$this->getParameter('EstadoDiferencia.rechazada'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoGarantia.porEmitir'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoGarantia')->find(\$this->getParameter('EstadoGarantia.porEmitir'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoGarantia.emitida'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoGarantia')->find(\$this->getParameter('EstadoGarantia.emitida'));",
    "return \$em->getRepository()->find(\$this->getParameter('EstadoGarantia.anulada'));" 
        => "return \$em->getRepository('App\\Entity\\Legacy\\EstadoGarantia')->find(\$this->getParameter('EstadoGarantia.anulada'));",
];

$count = 0;
foreach ($replacements as $search => $replace) {
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        $count++;
    }
}

file_put_contents($file, $content);

echo "✓ Corregidos $count getRepository() sin argumentos en DefaultController.php\n";
