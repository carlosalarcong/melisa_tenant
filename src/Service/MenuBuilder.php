<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Servicio que construye el menú de navegación del sidebar
 * 
 * Genera la estructura jerárquica del menú y determina qué items
 * deben estar expandidos basándose en la ruta actual.
 */
class MenuBuilder
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    /**
     * Construye el menú completo del sistema
     */
    public function buildMenu(): array
    {
        return [
            [
                'name' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'bx bx-home-circle',
                'route' => 'app_dashboard_default',
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'pacientes',
                'label' => 'Pacientes',
                'icon' => 'bx bx-group',
                'route' => null,
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'citas',
                'label' => 'Citas',
                'icon' => 'bx bx-calendar-check',
                'route' => null,
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'mantenedores',
                'label' => 'Mantenedores',
                'icon' => 'bx bx-data',
                'module' => null,
                'children' => [
                    [
                        'name' => 'maintenance_basic',
                        'label' => 'Mantenimiento Básico',
                        'icon' => 'bx bx-folder',
                        'module' => null,
                        'children' => [
                            [
                                'name' => 'gender',
                                'label' => 'Sexo',
                                'icon' => 'bx bx-male-female',
                                'route' => 'app_maintainers_gender_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'marital_status',
                                'label' => 'Estado Civil',
                                'icon' => 'bx bx-heart',
                                'route' => 'app_maintainers_marital_status_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'occupation',
                                'label' => 'Ocupación',
                                'icon' => 'bx bx-briefcase',
                                'route' => 'app_maintainers_occupation_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'religion',
                                'label' => 'Religión',
                                'icon' => 'bx bx-church',
                                'route' => 'app_maintainers_religion_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'location',
                                'label' => 'Localización',
                                'icon' => 'bx bx-map',
                                'route' => 'app_maintainers_location_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'doctor_type',
                                'label' => 'Tipo de Doctor',
                                'icon' => 'bx bx-user-plus',
                                'route' => 'app_maintainers_doctor_type_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'education_level',
                                'label' => 'Nivel de Educación',
                                'icon' => 'bx bx-book',
                                'route' => 'app_maintainers_education_level_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'ethnic_group',
                                'label' => 'Grupo Étnico',
                                'icon' => 'bx bx-group',
                                'route' => 'app_maintainers_ethnic_group_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'job_position',
                                'label' => 'Cargo',
                                'icon' => 'bx bx-id-card',
                                'route' => 'app_maintainers_job_position_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'medical_box',
                                'label' => 'Caja Médica',
                                'icon' => 'bx bx-clinic',
                                'route' => 'app_maintainers_medical_box_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'origin',
                                'label' => 'Origen',
                                'icon' => 'bx bx-map-pin',
                                'route' => 'app_maintainers_origin_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'origin_type',
                                'label' => 'Tipo de Origen',
                                'icon' => 'bx bx-compass',
                                'route' => 'app_maintainers_origin_type_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'reportes',
                'label' => 'Reportes',
                'icon' => 'bx bx-bar-chart-alt-2',
                'route' => null,
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'configuracion',
                'label' => 'Configuración',
                'icon' => 'bx bx-cog',
                'route' => null,
                'module' => null,
                'children' => []
            ]
        ];
    }

    /**
     * Determina si un item debe estar expandido basándose en la ruta actual
     */
    public function shouldExpand(array $item): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $currentPath = $request->getPathInfo();
        $currentRoute = $request->attributes->get('_route');

        // Expandir si la ruta actual coincide
        if (isset($item['route']) && $currentRoute === $item['route']) {
            return true;
        }

        // Expandir si algún hijo debe estar expandido
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($this->shouldExpand($child)) {
                    return true;
                }
            }
        }

        // Expandir mantenedores si estamos en cualquier ruta de maintenance
        if ($item['name'] === 'mantenedores' && str_contains($currentPath, '/maintainers')) {
            return true;
        }

        // Expandir subcategorías de maintenance
        if (isset($item['name']) && in_array($item['name'], ['maintenance_basic', 'maintenance_clinical', 'maintenance_geographic'])) {
            if (str_contains($currentPath, '/maintainers')) {
                // Verificar si algún hijo tiene la ruta activa
                foreach ($item['children'] ?? [] as $child) {
                    if (isset($child['route']) && $currentRoute === $child['route']) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Marca el menú con información de expansión y activación
     */
    public function enrichMenu(array $menu): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request?->attributes->get('_route');

        return array_map(function($item) use ($currentRoute) {
            $item['is_active'] = isset($item['route']) && $item['route'] === $currentRoute;
            $item['should_expand'] = $this->shouldExpand($item);
            
            if (!empty($item['children'])) {
                $item['children'] = $this->enrichMenu($item['children']);
            }
            
            return $item;
        }, $menu);
    }
}
