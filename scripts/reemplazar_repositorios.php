<?php
/**
 * Script para reemplazar referencias de repositorios de Symfony 2/3 a Symfony 6
 * Convierte 'RebsolHermesBundle:Entity' a 'App\Entity\Legacy\Entity'
 */

$directories = [
    __DIR__ . '/../src/Controller/Caja',
    __DIR__ . '/../src/Controller/Legacy'
];

$patterns = [
    // Para getRepository con comillas simples
    "/'RebsolHermesBundle:([^']+)'/",
    // Para getRepository con comillas dobles
    '/"RebsolHermesBundle:([^"]+)"/',
    // Para DQL queries sin comillas
    '/\bRebsolHermesBundle:(\w+)\b/'
];

$replacements = [
    "'App\\\\Entity\\\\Legacy\\\\$1'",
    '"App\\\\Entity\\\\Legacy\\\\$1"',
    'App\\\\Entity\\\\Legacy\\\\$1'
];

function processDirectory($dir, $patterns, $replacements) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $filesProcessed = 0;
    $totalReplacements = 0;

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            $content = file_get_contents($filePath);
            $originalContent = $content;
            
            foreach ($patterns as $index => $pattern) {
                $content = preg_replace($pattern, $replacements[$index], $content);
            }
            
            if ($content !== $originalContent) {
                file_put_contents($filePath, $content);
                $filesProcessed++;
                $replacementCount = substr_count($originalContent, 'RebsolHermesBundle:') - substr_count($content, 'RebsolHermesBundle:');
                $totalReplacements += $replacementCount;
                echo "✓ {$file->getFilename()} ({$replacementCount} reemplazos)\n";
            }
        }
    }

    return [$filesProcessed, $totalReplacements];
}

echo "Iniciando reemplazo de repositorios...\n\n";

$totalFiles = 0;
$totalReplacements = 0;

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "Procesando: $dir\n";
        list($files, $replacements) = processDirectory($dir, $patterns, $replacements);
        $totalFiles += $files;
        $totalReplacements += $replacements;
        echo "\n";
    }
}

echo "\n========================================\n";
echo "Resumen:\n";
echo "- Archivos procesados: $totalFiles\n";
echo "- Total de reemplazos: $totalReplacements\n";
echo "========================================\n";
