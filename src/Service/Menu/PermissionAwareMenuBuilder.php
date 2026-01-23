<?php

namespace App\Service\Menu;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Servicio PermissionAwareMenuBuilder: Construye el menú filtrado por permisos del usuario.
 * 
 * Este servicio:
 * 1. Obtiene la estructura del menú desde MenuDefinition
 * 2. Convierte arrays a objetos MenuItem
 * 3. Obtiene el perfil de permisos del tenant
 * 4. Filtra los items según los roles del usuario
 * 5. Retorna solo los items visibles según roles y estrategia de permisos
 */
class PermissionAwareMenuBuilder
{
    public function __construct(
        private PermissionStrategyFactory $strategyFactory,
        private RequestStack $requestStack,
        private MenuDefinition $menuDefinition
    ) {}

    /**
     * Construye el menú filtrado por permisos del usuario.
     *
     * @param array $userRoles Roles del usuario actual
     * @return array Array de MenuItems visibles
     */
    public function buildMenu(array $userRoles): array
    {
        // Obtener estrategia según perfil del tenant
        $strategy = $this->strategyFactory->createStrategy();
        
        // Definir estructura completa del menú
        $fullMenu = $this->getFullMenuStructure();
        
        // Filtrar items según permisos
        return $this->filterMenuItems($fullMenu, $userRoles, $strategy);
    }

    /**
     * Define la estructura completa del menú.
     * Obtiene la estructura desde MenuDefinition y la convierte a MenuItem objects.
     * 
     * @return MenuItem[]
     */
    private function getFullMenuStructure(): array
    {
        $menuStructure = $this->menuDefinition->getMenuStructure();
        return $this->convertArraysToMenuItems($menuStructure);
    }

    /**
     * Convierte arrays de menú a objetos MenuItem recursivamente.
     * 
     * @param array $items Array de items del menú
     * @return MenuItem[]
     */
    private function convertArraysToMenuItems(array $items): array
    {
        $menuItems = [];
        
        foreach ($items as $item) {
            $menuItem = new MenuItem(
                name: $item['name'],
                label: $item['label'],
                route: $item['route'] ?? null,
                icon: $item['icon'],
                module: $item['module'] ?? null
            );
            
            // Agregar children recursivamente si existen
            if (!empty($item['children'])) {
                $children = $this->convertArraysToMenuItems($item['children']);
                foreach ($children as $child) {
                    $menuItem->addChild($child);
                }
            }
            
            $menuItems[] = $menuItem;
        }
        
        return $menuItems;
    }

    /**
     * Filtra los items del menú según los permisos del usuario.
     *
     * @param MenuItem[] $items
     * @param array $userRoles
     * @param PermissionStrategyInterface $strategy
     * @return array Array de items filtrados (como arrays)
     */
    private function filterMenuItems(array $items, array $userRoles, PermissionStrategyInterface $strategy): array
    {
        $filteredItems = [];

        foreach ($items as $item) {
            // Si el item tiene un módulo, verificar permisos
            if ($item->getModule() && !$strategy->canAccess($item->getModule(), $userRoles)) {
                continue; // Skip este item
            }

            // Si tiene hijos, filtrarlos recursivamente
            if ($item->hasChildren()) {
                $filteredChildren = $this->filterMenuItems($item->getChildren(), $userRoles, $strategy);
                
                // Si no quedan hijos visibles, skip el item padre
                if (empty($filteredChildren)) {
                    continue;
                }
                
                // Crear nuevo item con hijos filtrados
                $newItem = new MenuItem(
                    name: $item->getName(),
                    label: $item->getLabel(),
                    route: $item->getRoute(),
                    icon: $item->getIcon(),
                    module: $item->getModule()
                );
                
                // Asignar hijos filtrados manualmente
                foreach ($filteredChildren as $childArray) {
                    // Los hijos ya vienen como arrays, los agregamos directamente
                }
                
                $filteredItems[] = [
                    'name' => $item->getName(),
                    'label' => $item->getLabel(),
                    'route' => $item->getRoute(),
                    'icon' => $item->getIcon(),
                    'module' => $item->getModule(),
                    'children' => $filteredChildren,
                ];
            } else {
                // Item sin hijos, agregarlo como array
                $filteredItems[] = $item->toArray();
            }
        }

        return $filteredItems;
    }

    /**
     * Obtiene los roles del usuario desde la sesión.
     * 
     * @return array
     */
    public function getUserRolesFromSession(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return ['ROLE_USER'];
        }

        $session = $request->getSession();
        $roles = $session->get('user_roles', []);
        
        // Si no hay roles, usar ROLE_USER por defecto
        return !empty($roles) ? $roles : ['ROLE_USER'];
    }
}
