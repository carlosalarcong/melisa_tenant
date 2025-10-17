<?php

namespace App\Controller\Mantenedores\Basico;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Region;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        /** @var Region $entity */
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['codigoRegion'])) {
            $entity->setCodigoRegion((int) $data['codigoRegion']);
        }
        
        if (isset($data['nombreRegion'])) {
            $entity->setNombreRegion($data['nombreRegion']);
        }
        
        if (isset($data['addressStateHl7'])) {
            $entity->setAddressStateHl7($data['addressStateHl7']);
        }
        
        if (isset($data['activo'])) {
            $entity->setActivo((bool) $data['activo']);
        }
    }

    protected function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Regiones',
            'entity_name' => 'Región',
            'entity_name_plural' => 'Regiones',
            'fields' => [
                'codigoRegion' => [
                    'label' => 'Código',
                    'type' => 'number',
                    'required' => false,
                    'searchable' => true
                ],
                'nombreRegion' => [
                    'label' => 'Nombre de la Región',
                    'type' => 'text',
                    'required' => true,
                    'searchable' => true
                ],
                'addressStateHl7' => [
                    'label' => 'Código HL7',
                    'type' => 'text',
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
            'search_fields' => ['nombreRegion', 'codigoRegion', 'addressStateHl7'],
            'default_sort' => ['nombreRegion' => 'ASC']
        ];
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/basico/region/index.html.twig';
    }

    protected function applySearchFilter($queryBuilder, string $search): void
    {
        $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('e.nombreRegion', ':search'),
                    $queryBuilder->expr()->like('e.codigoRegion', ':search'),
                    $queryBuilder->expr()->like('e.addressStateHl7', ':search')
                )
            )
            ->setParameter('search', '%' . $search . '%');
    }

    /**
     * Obtiene regiones filtradas por país
     */
    public function regionesPorPais(Request $request, string $pais): JsonResponse
    {
        $repository = $this->entityManager->getRepository($this->getEntityClass());
        
        $queryBuilder = $repository->createQueryBuilder('e')
            ->leftJoin('e.pais', 'p')
            ->where('p.id = :paisId OR p.nombrePais LIKE :paisNombre')
            ->setParameter('paisId', $pais)
            ->setParameter('paisNombre', '%' . $pais . '%')
            ->orderBy('e.nombreRegion', 'ASC');
        
        $regiones = $queryBuilder->getQuery()->getResult();
        
        $data = [];
        foreach ($regiones as $region) {
            $data[] = $this->entityToArray($region);
        }
        
        return new JsonResponse([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }
}