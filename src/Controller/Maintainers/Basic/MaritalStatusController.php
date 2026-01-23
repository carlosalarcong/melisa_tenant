<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\MaritalStatus;
use App\Form\Maintainers\MaritalStatusType;
use App\Repository\Tenant\MaritalStatusRepository;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Marital Status Controller
 * 
 * Gestiona el mantenedor de Estados Conyugales
 */
#[Route('/maintainers/basic/marital-status')]
class MaritalStatusController extends AbstractMantenedorController
{
    public function __construct(
        private MaritalStatusRepository $repository,
        TenantEntityManager $tenantEntityManager
    ) {
        parent::__construct($tenantEntityManager);
    }

    #[Route('', name: 'app_maintainers_marital_status_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_marital_status_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_marital_status_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_marital_status_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->repository->createQueryBuilder('ms')
            ->orderBy('ms.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return ['name', 'maritalStatusCodeHl7', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/marital_status/index.html.twig';
    }

    protected function getFormType(): string
    {
        return MaritalStatusType::class;
    }

    protected function createNewEntity(): object
    {
        return new MaritalStatus();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_marital_status_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Estado Conyugal',
            'edit' => 'Editar Estado Conyugal',
            default => 'Estados Conyugales'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
