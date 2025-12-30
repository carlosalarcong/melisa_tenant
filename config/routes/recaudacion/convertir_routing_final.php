<?php
function procesarArchivo($archivo) {
    $contenido = file_get_contents($archivo);
    $original = $contenido;
    
    // Patrón: RecaudacionBundle:_Default\Ruta\Clase:metodo
    $contenido = preg_replace_callback(
        '/RecaudacionBundle:(_Default|ApiPV)\\\\([^:]+):(\w+)/',
        function($matches) {
            $api = $matches[1]; // _Default o ApiPV
            $ruta = $matches[2]; // Recaudacion\Pago\Pagar
            $metodo = $matches[3];
            
            // Extraer última parte para nombre del controller
            $partes = explode('\\', $ruta);
            $ultimo = end($partes);
            
            return "App\\Controller\\Caja\\{$ruta}\\{$ultimo}Controller::{$metodo}";
        },
        $contenido
    );
    
    // Cambiar defaults: { _controller: X } por controller: X
    $contenido = preg_replace(
        '/defaults:\s*\{\s*_controller:\s*([^}]+)\s*\}/',
        'controller: $1',
        $contenido
    );
    
    if ($contenido !== $original) {
        file_put_contents($archivo, $contenido);
        return true;
    }
    return false;
}

$archivos = glob('Recaudacion/*.yml');
$archivos = array_merge($archivos, glob('Supervisor/**/*.yml'));

foreach ($archivos as $archivo) {
    if (procesarArchivo($archivo)) {
        echo "✓ Convertido: $archivo\n";
    }
}

echo "\n✅ Conversión completada\n";
