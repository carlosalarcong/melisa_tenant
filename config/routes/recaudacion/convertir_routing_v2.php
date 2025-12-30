<?php
function convertirController($match) {
    $ruta = $match[1]; // _Default/Recaudacion/Pago/Pago
    
    // Remover _Default/
    $ruta = preg_replace('/^_Default\//', '', $ruta);
    
    // Convertir / a \
    $ruta = str_replace('/', '\\', $ruta);
    
    // Extraer la última parte para el nombre del controller
    $partes = explode('\\', $ruta);
    $ultimo = end($partes);
    
    return "controller: App\\Controller\\Caja\\{$ruta}\\{$ultimo}Controller";
}

function procesarArchivo($archivo) {
    $contenido = file_get_contents($archivo);
    $original = $contenido;
    
    // Patrón para capturar: RecaudacionBundle:_Default/Ruta/A/Accion:metodo
    $contenido = preg_replace_callback(
        '/RecaudacionBundle:(_Default\/[^:]+):(\w+)/',
        function($matches) {
            $controller = convertirController($matches);
            return $controller . '::' . $matches[2];
        },
        $contenido
    );
    
    // También manejar ApiPV si existe
    $contenido = preg_replace_callback(
        '/RecaudacionBundle:(ApiPV\/[^:]+):(\w+)/',
        function($matches) {
            $ruta = str_replace('/', '\\', $matches[1]);
            $partes = explode('\\', $ruta);
            $ultimo = end($partes);
            return "controller: App\\Controller\\Caja\\{$ruta}\\{$ultimo}Controller::" . $matches[2];
        },
        $contenido
    );
    
    if ($contenido !== $original) {
        file_put_contents($archivo, $contenido);
        return true;
    }
    return false;
}

$archivos = [
    'Recaudacion/recaudacion.yml',
    'Recaudacion/servicio.yml',
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        if (procesarArchivo($archivo)) {
            echo "✓ Convertido: $archivo\n";
        } else {
            echo "- Sin cambios: $archivo\n";
        }
    }
}
