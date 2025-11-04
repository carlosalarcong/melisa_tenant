<?php

namespace App\Controller\Mantenedores;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador principal de Mantenedores
 * ✨ Ahora hereda de AbstractTenantAwareController - sin necesidad de constructor
 */
class MantenedoresController extends AbstractTenantAwareController
{
    /**
     * Página principal de mantenedores
     */
    #[Route('/mantenedores', name: 'mantenedores_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // ✨ Tenant disponible automáticamente
        $tenant = $this->getTenant();
        
        // Obtener contenido para precargar desde la ruta
        $preloadContent = $request->attributes->get('preload_content');
        
        // Mapeo de contenido a URLs
        $contentUrlMapping = [
            'pais' => '/mantenedores/basico/pais/content',
            'regiones' => '/mantenedores/basico/region/content',
            'religiones' => '/mantenedores/basico/religion/content',
            'sexo' => '/mantenedores/basico/sexo/content'
        ];
        
        $preloadUrl = null;
        if ($preloadContent && isset($contentUrlMapping[$preloadContent])) {
            $preloadUrl = $contentUrlMapping[$preloadContent];
        }
        
        return $this->render('mantenedores/index.html.twig', [
            'tenant' => $tenant,
            'subdomain' => $this->getTenantSubdomain(),
            'page_title' => 'Mantenedores - ' . $this->getTenantName(),
            'menuItems' => $this->getMenuItems(),
            'basicItems' => $this->getBasicItems(),
            'preload_content_url' => $preloadUrl,
            'preload_content_name' => $preloadContent
        ]);
    }

    /**
     * Página de mantenedores básicos
     */
    #[Route('/mantenedores/basico', name: 'mantenedores_basico_index', methods: ['GET'])]
    public function basico(): Response
    {
        // ✨ Tenant disponible automáticamente
        $tenant = $this->getTenant();
        
        return $this->render('mantenedores/basico.html.twig', [
            'tenant' => $tenant,
            'subdomain' => $this->getTenantSubdomain(),
            'page_title' => 'Mantenedores Básicos - ' . $this->getTenantName(),
            'basicItems' => $this->getBasicItems()
        ]);
    }

    /**
     * Ruta SPA para regiones con contenido precargado
     */
    #[Route('/mantenedores/basico/regiones', name: 'mantenedores_regiones_spa', methods: ['GET'])]
    public function regionesSpa(Request $request): Response
    {
        $request->attributes->set('preload_content', 'regiones');
        return $this->index($request);
    }

    /**
     * Ruta SPA para religiones con contenido precargado
     */
    #[Route('/mantenedores/basico/religiones', name: 'mantenedores_religiones_spa', methods: ['GET'])]
    public function religionesSpa(Request $request): Response
    {
        $request->attributes->set('preload_content', 'religiones');
        return $this->index($request);
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