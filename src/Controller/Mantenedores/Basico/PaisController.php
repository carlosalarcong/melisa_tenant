<?php

namespace App\Controller\Mantenedores\Basico;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Pais;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class PaisController extends AbstractMantenedorController
{
    protected function getEntityClass(): string
    {
        return Pais::class;
    }

    protected function createEntity(): object
    {
        return new Pais();
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        /** @var Pais $entity */
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['nombrePais'])) {
            $entity->setNombrePais($data['nombrePais']);
        }
        
        if (isset($data['nombreGentilicio'])) {
            $entity->setNombreGentilicio($data['nombreGentilicio']);
        }
        
        if (isset($data['activo'])) {
            $entity->setActivo((bool) $data['activo']);
        }
    }

    protected function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Países',
            'entity_name' => 'País',
            'entity_name_plural' => 'Países',
            'fields' => [
                'nombrePais' => [
                    'label' => 'Nombre del País',
                    'type' => 'text',
                    'required' => true,
                    'searchable' => true
                ],
                'nombreGentilicio' => [
                    'label' => 'Gentilicio',
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
            'search_fields' => ['nombrePais', 'nombreGentilicio'],
            'default_sort' => ['nombrePais' => 'ASC']
        ];
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/basico/pais/index.html.twig';
    }

    protected function applySearchFilter($queryBuilder, string $search): void
    {
        $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('e.nombrePais', ':search'),
                    $queryBuilder->expr()->like('e.nombreGentilicio', ':search')
                )
            )
            ->setParameter('search', '%' . $search . '%');
    }
}