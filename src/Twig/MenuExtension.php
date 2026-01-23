<?php

namespace App\Twig;

use App\Service\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Extensión de Twig que hace el menú disponible globalmente
 * 
 * Esto permite que todas las vistas accedan a 'menu_items' sin
 * necesidad de pasarlo explícitamente desde cada controlador.
 */
class MenuExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private MenuBuilder $menuBuilder
    ) {}

    /**
     * Define variables globales disponibles en todas las plantillas Twig
     */
    public function getGlobals(): array
    {
        $menu = $this->menuBuilder->buildMenu();
        $enrichedMenu = $this->menuBuilder->enrichMenu($menu);

        return [
            'menu_items' => $enrichedMenu,
        ];
    }
}
