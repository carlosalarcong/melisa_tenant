<?php

namespace App\Controller\Mantenedores\Basico;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Sexo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mantenedores/basico/sexo')]
class SexoController extends AbstractMantenedorController
{
    #[Route('', name: 'mantenedores_sexo_spa', methods: ['GET'])]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return parent::index();
    }

    #[Route('/list', name: 'mantenedores_sexo_list', methods: ['GET'])]
    public function list(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::list($request);
    }

    #[Route('/{id}', name: 'mantenedores_sexo_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::show($id);
    }

    #[Route('', name: 'mantenedores_sexo_create', methods: ['POST'])]
    public function create(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::create($request);
    }

    #[Route('/{id}', name: 'mantenedores_sexo_update', methods: ['PUT', 'PATCH'], requirements: ['id' => '\d+'])]
    public function update(\Symfony\Component\HttpFoundation\Request $request, int $id): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::update($request, $id);
    }

    #[Route('/{id}', name: 'mantenedores_sexo_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(\Symfony\Component\HttpFoundation\Request $request, int $id): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::delete($request, $id);
    }

    protected function getEntityClass(): string
    {
        return Sexo::class;
    }

    protected function createEntity(): object
    {
        return new Sexo();
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        /** @var Sexo $entity */
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['nombre'])) {
            $entity->setNombre($data['nombre']);
        }
        
        if (isset($data['codigo'])) {
            $entity->setCodigo($data['codigo']);
        }
        
        if (isset($data['activo'])) {
            $entity->setActivo((bool) $data['activo']);
        }
    }

    protected function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Sexos',
            'entity_name' => 'Sexo',
            'entity_name_plural' => 'Sexos',
            'fields' => [
                'nombre' => [
                    'label' => 'Nombre',
                    'type' => 'text',
                    'required' => true,
                    'searchable' => true
                ],
                'codigo' => [
                    'label' => 'Código',
                    'type' => 'text',
                    'required' => true,
                    'searchable' => true
                ],
                'activo' => [
                    'label' => 'Activo',
                    'type' => 'boolean',
                    'required' => false,
                    'searchable' => false
                ]
            ],
            'search_fields' => ['nombre', 'codigo'],
            'default_sort' => ['nombre' => 'ASC']
        ];
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/basico/sexo/index.html.twig';
    }

    protected function applySearchFilter($queryBuilder, string $search): void
    {
        $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('e.nombre', ':search'),
                    $queryBuilder->expr()->like('e.codigo', ':search')
                )
            )
            ->setParameter('search', '%' . $search . '%');
    }
}