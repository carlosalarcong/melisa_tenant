<?php

function convertirRuta($archivo) {
    $contenido = file_get_contents($archivo);
    
    // Convertir sintaxis de Symfony 2 a 6
    // De: { _controller: RecaudacionBundle:_Default/Supervisor/Supervisor:index }
    // A: controller: App\Controller\Caja\Supervisor\SupervisorController::index
    
    $contenido = preg_replace_callback(
        '/\{\s*_controller:\s*RecaudacionBundle:_Default\/([^:]+):([^}\s]+)\s*\}/',
        function($matches) {
            $ruta = str_replace('/', '\\', $matches[1]);
            $metodo = $matches[2];
            $controller = basename($ruta);
            
            return "controller: App\\Controller\\Caja\\{$ruta}\\{$controller}Controller::{$metodo}";
        },
        $contenido
    );
    
    // De: defaults: { _controller: ... }
    // A: controller: ...
    $contenido = preg_replace(
        '/defaults:\s*\{\s*_controller:/',
        'controller:',
        $contenido
    );
    
    // Limpiar llaves sobrantes
    $contenido = preg_replace('/controller: ([^}]+)\s*\}/', 'controller: $1', $contenido);
    
    return $contenido;
}

$directorios = ['Recaudacion', 'Supervisor'];

foreach ($directorios as $dir) {
    $archivos = glob($dir . '/*.yml');
    foreach ($archivos as $archivo) {
        echo "Convirtiendo: $archivo\n";
        $nuevo = convertirRuta($archivo);
        file_put_contents($archivo, $nuevo);
    }
}

echo "\n✓ Conversión completada\n";
