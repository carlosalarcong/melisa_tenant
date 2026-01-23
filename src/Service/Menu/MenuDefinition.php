<?php

namespace App\Service\Menu;

/**
 * MenuDefinition: Única fuente de verdad para la estructura del menú
 * 
 * Define toda la estructura del menú de navegación del sistema.
 * Usado por MenuBuilder (sidebar) y PermissionAwareMenuBuilder (filtrado por permisos).
 */
class MenuDefinition
{
    use MenuIconsTrait;

    /**
     * Retorna la estructura completa del menú del sistema
     * 
     * @return array
     */
    public function getMenuStructure(): array
    {
        return [
            [
                'name' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => $this->getIconForItem('dashboard'),
                'route' => 'app_dashboard_default',
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'pacientes',
                'label' => 'Pacientes',
                'icon' => $this->getIconForItem('pacientes'),
                'route' => null,
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'citas',
                'label' => 'Citas',
                'icon' => $this->getIconForItem('citas'),
                'route' => null,
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'mantenedores',
                'label' => 'Mantenedores',
                'icon' => $this->getIconForItem('mantenedores'),
                'module' => null,
                'children' => [
                    [
                        'name' => 'maintenance_basic',
                        'label' => 'Mantenimiento Básico',
                        'icon' => $this->getIconForItem('maintenance_basic'),
                        'module' => null,
                        'children' => [
                            [
                                'name' => 'gender',
                                'label' => 'Sexo',
                                'icon' => $this->getIconForItem('gender'),
                                'route' => 'app_maintainers_gender_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'marital_status',
                                'label' => 'Estado Civil',
                                'icon' => $this->getIconForItem('marital_status'),
                                'route' => 'app_maintainers_marital_status_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'occupation',
                                'label' => 'Ocupación',
                                'icon' => $this->getIconForItem('occupation'),
                                'route' => 'app_maintainers_occupation_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'religion',
                                'label' => 'Religión',
                                'icon' => $this->getIconForItem('religion'),
                                'route' => 'app_maintainers_religion_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'location',
                                'label' => 'Localización',
                                'icon' => $this->getIconForItem('location'),
                                'route' => 'app_maintainers_location_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'doctor_type',
                                'label' => 'Tipo de Doctor',
                                'icon' => $this->getIconForItem('doctor_type'),
                                'route' => 'app_maintainers_doctor_type_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'education_level',
                                'label' => 'Nivel de Educación',
                                'icon' => $this->getIconForItem('education_level'),
                                'route' => 'app_maintainers_education_level_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'ethnic_group',
                                'label' => 'Grupo Étnico',
                                'icon' => $this->getIconForItem('ethnic_group'),
                                'route' => 'app_maintainers_ethnic_group_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'job_position',
                                'label' => 'Cargo',
                                'icon' => $this->getIconForItem('job_position'),
                                'route' => 'app_maintainers_job_position_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'medical_box',
                                'label' => 'Caja Médica',
                                'icon' => $this->getIconForItem('medical_box'),
                                'route' => 'app_maintainers_medical_box_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'origin',
                                'label' => 'Origen',
                                'icon' => $this->getIconForItem('origin'),
                                'route' => 'app_maintainers_origin_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ],
                            [
                                'name' => 'origin_type',
                                'label' => 'Tipo de Origen',
                                'icon' => $this->getIconForItem('origin_type'),
                                'route' => 'app_maintainers_origin_type_index',
                                'module' => 'maintenance_basic',
                                'children' => []
                            ]
                        ]
                    ],
                    [
                        'name' => 'maintenance_clinical',
                        'label' => 'Clínico',
                        'icon' => $this->getIconForItem('maintenance_clinical'),
                        'module' => null,
                        'children' => []
                    ],
                    [
                        'name' => 'maintenance_geographic',
                        'label' => 'Geográficos',
                        'icon' => $this->getIconForItem('maintenance_geographic'),
                        'module' => null,
                        'children' => []
                    ]
                ]
            ],
            [
                'name' => 'reportes',
                'label' => 'Reportes',
                'icon' => $this->getIconForItem('reportes'),
                'route' => null,
                'module' => null,
                'children' => []
            ],
            [
                'name' => 'configuracion',
                'label' => 'Configuración',
                'icon' => $this->getIconForItem('configuracion'),
                'route' => null,
                'module' => null,
                'children' => []
            ]
        ];
    }
}
