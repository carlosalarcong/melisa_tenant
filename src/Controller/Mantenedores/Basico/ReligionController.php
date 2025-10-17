<?php

namespace App\Controller\Mantenedores\Basico;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Religion;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class ReligionController extends AbstractMantenedorController
{
    protected function getEntityClass(): string
    {
        return Religion::class;
    }

    protected function createEntity(): object
    {
        return new Religion();
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        /** @var Religion $entity */
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['nombre'])) {
            $entity->setNombre($data['nombre']);
        }
        
        if (isset($data['codigo'])) {
            $entity->setCodigo($data['codigo']);
        }
        
        if (isset($data['descripcion'])) {
            $entity->setDescripcion($data['descripcion']);
        }
        
        if (isset($data['activo'])) {
            $entity->setActivo((bool) $data['activo']);
        }
    }

    protected function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Religiones',
            'entity_name' => 'Religión',
            'entity_name_plural' => 'Religiones',
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
                'descripcion' => [
                    'label' => 'Descripción',
                    'type' => 'textarea',
                    'required' => false,
                    'searchable' => true
                ],
                'activo' => [
                    'label' => 'Activo',
                    'type' => 'boolean',
                    'required' => false,
                    'searchable' => false
                ]
            ],
            'search_fields' => ['nombre', 'codigo', 'descripcion'],
            'default_sort' => ['nombre' => 'ASC']
        ];
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/basico/religion/index.html.twig';
    }

    protected function applySearchFilter($queryBuilder, string $search): void
    {
        $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('e.nombre', ':search'),
                    $queryBuilder->expr()->like('e.codigo', ':search'),
                    $queryBuilder->expr()->like('e.descripcion', ':search')
                )
            )
            ->setParameter('search', '%' . $search . '%');
    }
}