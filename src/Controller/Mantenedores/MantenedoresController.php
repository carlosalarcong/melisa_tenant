<?php

namespace App\Controller\Mantenedores;

use App\Controller\AbstractTenantController;
use App\Service\TenantContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MantenedoresController extends AbstractTenantController
{
    public function __construct(TenantContext $tenantContext)
    {
        parent::__construct($tenantContext);
    }

    /**
     * Página principal de mantenedores
     */
    public function index(): Response
    {
        $tenant = $this->getCurrentTenant();
        
        // Debug temporal
        if (!$tenant) {
            $tenant = [
                'name' => 'Melisa Hospital',
                'subdomain' => 'melisahospital'
            ];
        }
        
        return $this->render('mantenedores/index.html.twig', [
            'tenant' => $tenant,
            'subdomain' => $tenant['subdomain'] ?? null,
            'page_title' => 'Mantenedores - ' . ($tenant['name'] ?? 'Sistema'),
            'menuItems' => $this->getMenuItems(),
            'basicItems' => $this->getBasicItems()
        ]);
    }

    /**
     * Página de mantenedores básicos
     */
    public function basico(): Response
    {
        $tenant = $this->getCurrentTenant();
        
        return $this->render('mantenedores/basico.html.twig', [
            'tenant' => $tenant,
            'subdomain' => $tenant['subdomain'] ?? null,
            'page_title' => 'Mantenedores Básicos - ' . ($tenant['name'] ?? 'Sistema'),
            'basicItems' => $this->getBasicItems()
        ]);
    }

    /**
     * Obtener elementos del menú principal de mantenedores
     */
    private function getMenuItems(): array
    {
        return [
            'Básico' => [
                [
                    'name' => 'Países',
                    'icon' => 'fas fa-globe',
                    'url' => '/mantenedores/basico/pais/content'
                ],
                [
                    'name' => 'Regiones',
                    'icon' => 'fas fa-map-marked-alt',
                    'url' => '/mantenedores/basico/region/content'
                ],
                [
                    'name' => 'Religiones',
                    'icon' => 'fas fa-pray',
                    'url' => '/mantenedores/basico/religion/content'
                ],
                [
                    'name' => 'Sexo',
                    'icon' => 'fas fa-venus-mars',
                    'url' => '/mantenedores/basico/sexo/content'
                ]
            ],
            'Médicos' => [
                [
                    'name' => 'Especialidades',
                    'icon' => 'fas fa-stethoscope',
                    'url' => '/mantenedores/medicos/especialidades'
                ],
                [
                    'name' => 'Diagnósticos',
                    'icon' => 'fas fa-notes-medical',
                    'url' => '/mantenedores/medicos/diagnosticos'
                ]
            ],
            'Sistema' => [
                [
                    'name' => 'Usuarios',
                    'icon' => 'fas fa-users',
                    'url' => '/mantenedores/sistema/usuarios'
                ],
                [
                    'name' => 'Roles',
                    'icon' => 'fas fa-user-tag',
                    'url' => '/mantenedores/sistema/roles'
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
                'name' => 'Países',
                'description' => 'Gestión de países y nacionalidades',
                'icon' => 'fas fa-globe',
                'url' => '/mantenedores/basico/pais/content'
            ],
            [
                'name' => 'Regiones',
                'description' => 'Gestión de regiones por país',
                'icon' => 'fas fa-map-marked-alt',
                'url' => '/mantenedores/basico/region/content'
            ],
            [
                'name' => 'Religiones',
                'description' => 'Gestión de religiones y creencias',
                'icon' => 'fas fa-pray',
                'url' => '/mantenedores/basico/religion/content'
            ],
            [
                'name' => 'Sexo',
                'description' => 'Gestión de tipos de sexo/género',
                'icon' => 'fas fa-venus-mars',
                'url' => '/mantenedores/basico/sexo/content'
            ]
        ];
    }
}