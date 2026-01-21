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
                route: null, // TODO: Implementar ruta app_patients
                icon: 'bx bx-user',
                module: 'patients'
            ),
            new MenuItem(
                name: 'appointments',
                label: 'Citas',
                route: null, // TODO: Implementar ruta app_appointments
                icon: 'bx bx-calendar',
                module: 'appointments'
            ),
            new MenuItem(
                name: 'mantenedores',
                label: 'Mantenedores',
                icon: 'bx bx-cog',
                children: [
                    // Básico - Con 3 niveles de profundidad
                    new MenuItem(
                        name: 'maintenance_basic',
                        label: 'Básico',
                        icon: 'bx bx-folder',
                        children: [
                            new MenuItem(
                                name: 'maintenance_gender',
                                label: 'Sexo',
                                route: 'app_maintainers_gender_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_gender'
                            ),
                            new MenuItem(
                                name: 'maintenance_religion',
                                label: 'Religión',
                                route: 'app_maintainers_religion_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_religion'
                            ),
                            new MenuItem(
                                name: 'maintenance_marital_status',
                                label: 'Estado Conyugal',
                                route: 'app_maintainers_marital_status_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_marital_status'
                            ),
                            new MenuItem(
                                name: 'maintenance_ethnic_group',
                                label: 'Pueblo Originario',
                                route: 'app_maintainers_ethnic_group_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_ethnic_group'
                            ),
                            new MenuItem(
                                name: 'maintenance_occupation',
                                label: 'Ocupación',
                                route: 'app_maintainers_occupation_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_occupation'
                            ),
                            new MenuItem(
                                name: 'maintenance_education_level',
                                label: 'Nivel Instrucción',
                                route: 'app_maintainers_education_level_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_education_level'
                            ),
                            new MenuItem(
                                name: 'maintenance_education_detail',
                                label: 'Detalle Nivel Instrucción',
                                route: 'app_maintainers_education_level_detail_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_education_detail'
                            ),
                            new MenuItem(
                                name: 'maintenance_insurance_admin',
                                label: 'Administrador Seguro',
                                route: 'app_maintainers_insurance_administrator_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_insurance_admin'
                            ),
                            new MenuItem(
                                name: 'maintenance_position',
                                label: 'Cargo',
                                route: 'app_maintainers_job_position_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_position'
                            ),
                            new MenuItem(
                                name: 'maintenance_doctor_type',
                                label: 'Tipo Médico',
                                route: 'app_maintainers_doctor_type_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_doctor_type'
                            ),
                            new MenuItem(
                                name: 'maintenance_box',
                                label: 'Box',
                                route: 'app_maintainers_medical_box_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_box'
                            ),
                            new MenuItem(
                                name: 'maintenance_location',
                                label: 'Ubicación',
                                route: 'app_maintainers_location_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_location'
                            ),
                            new MenuItem(
                                name: 'maintenance_multimedia_category',
                                label: 'Categoría Multimedia',
                                route: null, // TODO: Implementar ruta
                                icon: 'bx bx-circle',
                                module: 'maintenance_multimedia_category'
                            ),
                            new MenuItem(
                                name: 'maintenance_origin_type',
                                label: 'Tipo Origen',
                                route: 'app_maintainers_origin_type_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_origin_type'
                            ),
                            new MenuItem(
                                name: 'maintenance_origin',
                                label: 'Origen',
                                route: 'app_maintainers_origin_index',
                                icon: 'bx bx-circle',
                                module: 'maintenance_origin'
                            ),
                        ]
                    ),
                    // Clínico - Estructura preparada para 3 niveles
                    new MenuItem(
                        name: 'maintenance_clinical',
                        label: 'Clínico',
                        icon: 'bx bx-folder',
                        children: [
                            new MenuItem(
                                name: 'maintenance_care_item',
                                label: 'Ítem atención',
                                route: null,
                                icon: 'bx bx-chevron-right',
                                module: 'maintenance_care_item'
                            ),
                            new MenuItem(
                                name: 'maintenance_background',
                                label: 'Antecedentes',
                                route: null,
                                icon: 'bx bx-chevron-right',
                                module: 'maintenance_background',
                                children: [
                                    new MenuItem(
                                        name: 'maintenance_background_type',
                                        label: 'Tipo Antecedente',
                                        route: null,
                                        icon: 'bx bx-circle',
                                        module: 'maintenance_background_type'
                                    ),
                                    new MenuItem(
                                        name: 'maintenance_background_item',
                                        label: 'Antecedente',
                                        route: null,
                                        icon: 'bx bx-circle',
                                        module: 'maintenance_background_item'
                                    ),
                                ]
                            ),
                            // Más items clínicos...
                        ]
                    ),
                    // Geográficos
                    new MenuItem(
                        name: 'maintenance_geographic',
                        label: 'Geográficos',
                        icon: 'bx bx-folder',
                        children: [
                            new MenuItem(
                                name: 'maintenance_country',
                                label: 'Países',
                                route: null, // TODO: Implementar ruta
                                icon: 'bx bx-circle',
                                module: 'maintenance_country'
                            ),
                            new MenuItem(
                                name: 'maintenance_region',
                                label: 'Regiones',
                                route: null, // TODO: Implementar ruta
                                icon: 'bx bx-circle',
                                module: 'maintenance_region'
                            ),
                            new MenuItem(
                                name: 'maintenance_province',
                                label: 'Provincias',
                                route: null, // TODO: Implementar ruta
                                icon: 'bx bx-circle',
                                module: 'maintenance_province'
                            ),
                            new MenuItem(
                                name: 'maintenance_municipality',
                                label: 'Municipios',
                                route: null, // TODO: Implementar ruta
                                icon: 'bx bx-circle',
                                module: 'maintenance_municipality'
                            ),
                        ]
                    ),
                ]
            ),
            new MenuItem(
                name: 'reports',
                label: 'Reportes',
                route: null, // TODO: Implementar ruta
                icon: 'bx bx-bar-chart-alt-2',
                module: 'reports'
            ),
            new MenuItem(
                name: 'settings',
                label: 'Configuración',
                route: null, // TODO: Implementar ruta
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
