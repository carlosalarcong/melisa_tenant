<?php

namespace App\Controller\Mantenedores\Default;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Sexo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mantenedores/basico/sexo', name: 'mantenedores_sexo_')]
class SexoController extends AbstractMantenedorController
{
    protected function getEntityClass(): string
    {
        return Sexo::class;
    }

    protected function createEntity(): object
    {
        return new Sexo();
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/sexo/index.html.twig';
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        /** @var Sexo $entity */
        $entity->setNombre($request->request->get('nombre', ''));
        $entity->setDescripcion($request->request->get('descripcion'));
        $entity->setCodigo($request->request->get('codigo', ''));
        $entity->setActivo((bool) $request->request->get('activo', true));
        
        // Actualizar timestamp en edición
        if ($entity->getId()) {
            $entity->setUpdatedAt();
        }
    }

    protected function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Sexos',
            'description' => 'Administración de tipos de sexo para el sistema',
            'entity_name' => 'Sexo',
            'entity_name_plural' => 'Sexos',
            'icon' => 'fas fa-venus-mars',
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
                    'width' => '40%'
                ],
                [
                    'key' => 'descripcion',
                    'label' => 'Descripción',
                    'sortable' => true,
                    'searchable' => true,
                    'width' => '40%'
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
                    'maxlength' => 1,
                    'placeholder' => 'M o F',
                    'help' => 'M para Masculino, F para Femenino'
                ],
                [
                    'name' => 'nombre',
                    'label' => 'Nombre',
                    'type' => 'text',
                    'required' => true,
                    'maxlength' => 100,
                    'placeholder' => 'Ej: Masculino, Femenino'
                ],
                [
                    'name' => 'descripcion',
                    'label' => 'Descripción',
                    'type' => 'textarea',
                    'required' => false,
                    'maxlength' => 255,
                    'placeholder' => 'Descripción opcional del tipo de sexo'
                ],
                [
                    'name' => 'activo',
                    'label' => 'Activo',
                    'type' => 'checkbox',
                    'required' => false,
                    'default' => true
                ]
            ],
            'search_placeholder' => 'Buscar por código, nombre o descripción...',
            'routes' => [
                'index' => 'mantenedores_sexo_index',
                'list' => 'mantenedores_sexo_list',
                'show' => 'mantenedores_sexo_show',
                'create' => 'mantenedores_sexo_create',
                'update' => 'mantenedores_sexo_update',
                'delete' => 'mantenedores_sexo_delete'
            ]
        ];
    }

    protected function entityToArray(object $entity): array
    {
        /** @var Sexo $entity */
        return [
            'id' => $entity->getId(),
            'codigo' => $entity->getCodigo(),
            'nombre' => $entity->getNombre(),
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
                $queryBuilder->expr()->like('e.descripcion', ':search')
            )
        )->setParameter('search', "%{$search}%");
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