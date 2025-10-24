<?php

namespace App\Controller\Mantenedores\Basico;

use App\Controller\Mantenedores\AbstractMantenedorController;
use App\Entity\Region;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mantenedores/basico/region')]
class RegionController extends AbstractMantenedorController
{
    #[Route('', name: 'mantenedores_region_index', methods: ['GET'])]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return parent::index();
    }

    #[Route('/list', name: 'mantenedores_region_list', methods: ['GET'])]
    public function list(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::list($request);
    }

    #[Route('/{id}', name: 'mantenedores_region_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::show($id);
    }

    #[Route('', name: 'mantenedores_region_create', methods: ['POST'])]
    public function create(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::create($request);
    }

    #[Route('/{id}', name: 'mantenedores_region_update', methods: ['PUT', 'PATCH'], requirements: ['id' => '\d+'])]
    public function update(\Symfony\Component\HttpFoundation\Request $request, int $id): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::update($request, $id);
    }

    #[Route('/{id}', name: 'mantenedores_region_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(\Symfony\Component\HttpFoundation\Request $request, int $id): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return parent::delete($request, $id);
    }

    /**
     * Obtiene regiones filtradas por país
     */
    #[Route('/por-pais/{pais}', name: 'mantenedores_region_por_pais', methods: ['GET'])]
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
}