<?php

namespace App\Controller\Maintainers\Structure;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\SubCompany;
use App\Form\Maintainers\SubCompanyType;
use App\Repository\Tenant\SubCompanyRepository;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/structure/sub-company')]
class SubCompanyController extends AbstractMantenedorController
{
    public function __construct(
        private SubCompanyRepository $subCompanyRepository,
        TenantEntityManager $tenantEntityManager
    ) {
        parent::__construct($tenantEntityManager);
    }

    #[Route('', name: 'app_maintainers_sub_company_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_sub_company_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_sub_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_sub_company_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->subCompanyRepository->createQueryBuilder('sc')
            ->orderBy('sc.name', 'ASC');
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'taxId', 'description', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/structure/sub_company/index.html.twig';
    }

    protected function getFormType(): string
    {
        return SubCompanyType::class;
    }

    protected function createNewEntity(): object
    {
        return new SubCompany();
    }

    protected function getEntityName(): string
    {
        return 'Sub-Empresa';
    }

    protected function getItemsPerPage(): int
    {
        return 15;
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_sub_company_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'index' => 'Sub-Empresas',
            'create' => 'Crear Sub-Empresa',
            'edit' => 'Editar Sub-Empresa',
            default => 'Sub-Empresas'
        };
    }
}
