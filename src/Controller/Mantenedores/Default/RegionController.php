<?php

namespace App\Controller\Mantenedores\Default;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Region;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mantenedores/basico/region', name: 'mantenedores_region_')]
class RegionController extends AbstractMantenedorController
{
    protected function getEntityClass(): string
    {
        return Region::class;
    }

    protected function createEntity(): object
    {
        return new Region();
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/region/index.html.twig';
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        /** @var Region $entity */
        $entity->setNombre($request->request->get('nombre', ''));
        $entity->setDescripcion($request->request->get('descripcion'));
        $entity->setCodigo($request->request->get('codigo', ''));
        $entity->setPais($request->request->get('pais'));
        $entity->setActivo((bool) $request->request->get('activo', true));
        
        // Actualizar timestamp en edición
        if ($entity->getId()) {
            $entity->setUpdatedAt();
        }
    }

    protected function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Regiones',
            'description' => 'Administración de regiones geográficas del sistema',
            'entity_name' => 'Región',
            'entity_name_plural' => 'Regiones',
            'icon' => 'fas fa-map-marked-alt',
            'columns' => [
                [
                    'key' => 'codigo',
                    'label' => 'Código',
                    'sortable' => true,
                    'searchable' => true,
                    'width' => '10%'
                ],
                [
                    'key' => 'nombre',
                    'label' => 'Nombre',
                    'sortable' => true,
                    'searchable' => true,
                    'width' => '30%'
                ],
                [
                    'key' => 'pais',
                    'label' => 'País',
                    'sortable' => true,
                    'searchable' => true,
                    'width' => '20%'
                ],
                [
                    'key' => 'descripcion',
                    'label' => 'Descripción',
                    'sortable' => true,
                    'searchable' => true,
                    'width' => '30%'
                ],
                [
                    'key' => 'activo',
                    'label' => 'Estado',
                    'sortable' => true,
                    'searchable' => false,
                    'width' => '10%',
                    'type' => 'boolean'
                ]
            ],
            'form_fields' => [
                [
                    'name' => 'codigo',
                    'label' => 'Código',
                    'type' => 'text',
                    'required' => true,
                    'maxlength' => 10,
                    'placeholder' => 'Ej: RM, V, VIII',
                    'help' => 'Código único de la región'
                ],
                [
                    'name' => 'nombre',
                    'label' => 'Nombre',
                    'type' => 'text',
                    'required' => true,
                    'maxlength' => 100,
                    'placeholder' => 'Ej: Región Metropolitana, Valparaíso'
                ],
                [
                    'name' => 'pais',
                    'label' => 'País',
                    'type' => 'text',
                    'required' => false,
                    'maxlength' => 100,
                    'placeholder' => 'Ej: Chile, Argentina, Colombia'
                ],
                [
                    'name' => 'descripcion',
                    'label' => 'Descripción',
                    'type' => 'textarea',
                    'required' => false,
                    'maxlength' => 255,
                    'placeholder' => 'Descripción opcional de la región'
                ],
                [
                    'name' => 'activo',
                    'label' => 'Activo',
                    'type' => 'checkbox',
                    'required' => false,
                    'default' => true
                ]
            ],
            'search_placeholder' => 'Buscar por código, nombre, país o descripción...',
            'routes' => [
                'index' => 'mantenedores_region_index',
                'list' => 'mantenedores_region_list',
                'show' => 'mantenedores_region_show',
                'create' => 'mantenedores_region_create',
                'update' => 'mantenedores_region_update',
                'delete' => 'mantenedores_region_delete'
            ]
        ];
    }

    protected function entityToArray(object $entity): array
    {
        /** @var Region $entity */
        return [
            'id' => $entity->getId(),
            'codigo' => $entity->getCodigo(),
            'nombre' => $entity->getNombre(),
            'pais' => $entity->getPais(),
            'descripcion' => $entity->getDescripcion(),
            'activo' => $entity->isActivo(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    protected function applySearchFilter($queryBuilder, string $search): void
    {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('e.codigo', ':search'),
                $queryBuilder->expr()->like('e.nombre', ':search'),
                $queryBuilder->expr()->like('e.pais', ':search'),
                $queryBuilder->expr()->like('e.descripcion', ':search')
            )
        )->setParameter('search', "%{$search}%");
    }

    /**
     * Endpoint adicional para obtener regiones por país
     */
    #[Route('/por-pais/{pais}', name: 'por_pais', methods: ['GET'])]
    public function regionesPorPais(string $pais): JsonResponse
    {
        try {
            $regiones = $this->getRepository()->findByPais($pais);
            
            $data = array_map([$this, 'entityToArray'], $regiones);

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'pais' => $pais,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al obtener regiones: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return parent::index();
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        return parent::list($request);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        return parent::show($id);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return parent::create($request);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return parent::update($request, $id);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return parent::delete($request, $id);
    }
}