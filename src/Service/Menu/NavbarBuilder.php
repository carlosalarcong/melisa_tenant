<?php

namespace App\Service\Menu;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Servicio NavbarBuilder: Construye el menú de navegación filtrado por permisos.
 * 
 * Este servicio:
 * 1. Define la estructura completa del menú
 * 2. Obtiene el perfil de permisos del tenant
 * 3. Filtra los items según los roles del usuario
 * 4. Retorna solo los items visibles
 */
class NavbarBuilder
{
    public function __construct(
        private PermissionStrategyFactory $strategyFactory,
        private RequestStack $requestStack
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
     * 
     * @return MenuItem[]
     */
    private function getFullMenuStructure(): array
    {
        return [
            new MenuItem(
                name: 'dashboard',
                label: 'Dashboard',
                route: 'app_dashboard_default',
                icon: 'bx bx-home-circle',
                module: 'dashboard'
            ),
            new MenuItem(
                name: 'patients',
                label: 'Pacientes',
                route: 'app_patients',
                icon: 'bx bx-user',
                module: 'patients'
            ),
            new MenuItem(
                name: 'appointments',
                label: 'Citas',
                route: 'app_appointments',
                icon: 'bx bx-calendar',
                module: 'appointments'
            ),
            new MenuItem(
                name: 'mantenedores',
                label: 'Mantenedores',
                icon: 'bx bx-cog',
                children: [
                    // Básicos
                    new MenuItem(
                        name: 'maintenance_genders',
                        label: 'Géneros',
                        route: 'app_maintenance_gender_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_genders'
                    ),
                    new MenuItem(
                        name: 'maintenance_marital_status',
                        label: 'Estado Civil',
                        route: 'app_maintenance_marital_status_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_marital_status'
                    ),
                    new MenuItem(
                        name: 'maintenance_ethnic_groups',
                        label: 'Grupos Étnicos',
                        route: 'app_maintenance_ethnic_group_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_ethnic_groups'
                    ),
                    // Geográficos
                    new MenuItem(
                        name: 'maintenance_countries',
                        label: 'Países',
                        route: 'app_maintenance_country_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_countries'
                    ),
                    new MenuItem(
                        name: 'maintenance_regions',
                        label: 'Regiones',
                        route: 'app_maintenance_region_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_regions'
                    ),
                    new MenuItem(
                        name: 'maintenance_provinces',
                        label: 'Provincias',
                        route: 'app_maintenance_province_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_provinces'
                    ),
                    new MenuItem(
                        name: 'maintenance_municipalities',
                        label: 'Municipios',
                        route: 'app_maintenance_municipality_index',
                        icon: 'bx bx-circle',
                        module: 'maintenance_municipalities'
                    ),
                ]
            ),
            new MenuItem(
                name: 'reports',
                label: 'Reportes',
                route: 'app_reports',
                icon: 'bx bx-bar-chart-alt-2',
                module: 'reports'
            ),
            new MenuItem(
                name: 'settings',
                label: 'Configuración',
                route: 'app_settings',
                icon: 'bx bx-wrench',
                module: 'settings'
            ),
        ];
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
