<?php

namespace App\Controller\Maintainers\Structure;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\Department;
use App\Form\Maintainers\DepartmentType;
use App\Repository\Tenant\DepartmentRepository;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/structure/department')]
class DepartmentController extends AbstractMantenedorController
{
    public function __construct(
        private DepartmentRepository $departmentRepository,
        TenantEntityManager $tenantEntityManager
    ) {
        parent::__construct($tenantEntityManager);
    }

    #[Route('', name: 'app_maintainers_department_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_department_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_department_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_department_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->departmentRepository->createQueryBuilder('d')
            ->leftJoin('d.branch', 'b')
            ->addSelect('b')
            ->orderBy('d.name', 'ASC');
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'branch', 'description', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/structure/department/index.html.twig';
    }

    protected function getFormType(): string
    {
        return DepartmentType::class;
    }

    protected function createNewEntity(): object
    {
        return new Department();
    }

    protected function getEntityName(): string
    {
        return 'Unidad';
    }

    protected function getItemsPerPage(): int
    {
        return 15;
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_department_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'index' => 'Unidades',
            'create' => 'Crear Unidad',
            'edit' => 'Editar Unidad',
            default => 'Unidades'
        };
    }
}
