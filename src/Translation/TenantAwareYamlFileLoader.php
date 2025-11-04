<?php

namespace App\Translation;

use App\Service\TenantContext;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Loader customizado que carga traducciones desde carpetas específicas del tenant
 * 
 * NO SE USA DIRECTAMENTE - Symfony 6.4 carga automáticamente desde paths configurados
 * Este archivo queda como referencia para futuras versiones
 */
class TenantAwareYamlFileLoader extends YamlFileLoader
{
    // Clase de referencia - No se usa en Symfony 6.4
    // Las traducciones se cargan automáticamente desde los paths configurados
}
