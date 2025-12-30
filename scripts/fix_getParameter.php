<?php

$file = '/var/www/html/melisa_tenant/src/Controller/Caja/RecaudacionController.php';
$content = file_get_contents($file);

// Reemplazar todas las ocurrencias de $this->container->getParameter por $this->getParameter
$content = str_replace('$this->container->getParameter', '$this->getParameter', $content);

// También reemplazar RebsolHermesBundle: por App\Entity\Legacy\
$content = str_replace('RebsolHermesBundle:', 'App\\Entity\\Legacy\\', $content);

file_put_contents($file, $content);

echo "✅ Archivo actualizado correctamente\n";
echo "- Reemplazado: \$this->container->getParameter → \$this->getParameter\n";
echo "- Reemplazado: RebsolHermesBundle: → App\\Entity\\Legacy\\\n";
