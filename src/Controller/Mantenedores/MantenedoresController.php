<?php

namespace App\Controller\Mantenedores;

use App\Controller\AbstractTenantController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MantenedoresController extends AbstractTenantController
{
    /**
     * Página principal de mantenedores
     */
    public function index(): Response
    {
        $tenant = $this->getCurrentTenant();
        
        return $this->renderWithTenant('mantenedores/index.html.twig', [
            'page_title' => 'Mantenedores - ' . ($tenant?->getName() ?? 'Sistema'),
            'menu_items' => $this->getMenuItems()
        ]);
    }

    /**
     * Página de mantenedores básicos
     */
    public function basico(): Response
    {
        $tenant = $this->getCurrentTenant();
        
        return $this->renderWithTenant('mantenedores/basico/index.html.twig', [
            'page_title' => 'Mantenedores Básicos - ' . ($tenant?->getName() ?? 'Sistema'),
            'basic_items' => $this->getBasicItems()
        ]);
    }

    /**
     * Obtener elementos del menú principal de mantenedores
     */
    private function getMenuItems(): array
    {
        return [
            [
                'title' => 'Mantenedores Básicos',
                'description' => 'Gestión de datos básicos del sistema',
                'icon' => 'fas fa-database',
                'url' => '/mantenedores/basico',
                'color' => 'primary',
                'items' => [
                    'Países', 'Regiones', 'Religiones', 'Sexo'
                ]
            ],
            [
                'title' => 'Mantenedores Médicos',
                'description' => 'Gestión de datos médicos específicos',
                'icon' => 'fas fa-user-md',
                'url' => '/mantenedores/medicos',
                'color' => 'success',
                'items' => [
                    'Especialidades', 'Diagnósticos', 'Medicamentos'
                ]
            ],
            [
                'title' => 'Mantenedores de Sistema',
                'description' => 'Configuración y parámetros del sistema',
                'icon' => 'fas fa-cogs',
                'url' => '/mantenedores/sistema',
                'color' => 'info',
                'items' => [
                    'Usuarios', 'Roles', 'Permisos', 'Configuración'
                ]
            ]
        ];
    }

    /**
     * Obtener elementos básicos específicos
     */
    private function getBasicItems(): array
    {
        return [
            [
                'title' => 'Países',
                'description' => 'Gestión de países y nacionalidades',
                'icon' => 'fas fa-globe',
                'url' => '/mantenedores/basico/pais',
                'color' => 'primary',
                'count' => 0 // TODO: obtener count real
            ],
            [
                'title' => 'Regiones',
                'description' => 'Gestión de regiones por país',
                'icon' => 'fas fa-map-marked-alt',
                'url' => '/mantenedores/basico/region',
                'color' => 'success',
                'count' => 0
            ],
            [
                'title' => 'Religiones',
                'description' => 'Gestión de religiones y creencias',
                'icon' => 'fas fa-pray',
                'url' => '/mantenedores/basico/religion',
                'color' => 'warning',
                'count' => 0
            ],
            [
                'title' => 'Sexo',
                'description' => 'Gestión de tipos de sexo/género',
                'icon' => 'fas fa-venus-mars',
                'url' => '/mantenedores/basico/sexo',
                'color' => 'info',
                'count' => 0
            ]
        ];
    }
}