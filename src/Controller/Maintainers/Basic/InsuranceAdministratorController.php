<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\InsuranceAdministrator;
use App\Form\Maintainers\InsuranceAdministratorType;
use App\Repository\Tenant\InsuranceAdministratorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Insurance Administrator Controller
 * 
 * Gestiona el mantenedor de Administradores de Seguro
 */
#[Route('/maintainers/basic/insurance-administrator')]
class InsuranceAdministratorController extends AbstractMantenedorController
{
    public function __construct(
        private InsuranceAdministratorRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_insurance_administrator_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_insurance_administrator_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_insurance_administrator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_insurance_administrator_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    protected function getData(Request $request): array
    {
        return $this->repository->findAll();
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/insurance_administrator/index.html.twig';
    }

    protected function getFormType(): string
    {
        return InsuranceAdministratorType::class;
    }

    protected function createNewEntity(): object
    {
        return new InsuranceAdministrator();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_insurance_administrator_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Administrador de Seguro',
            'edit' => 'Editar Administrador de Seguro',
            default => 'Administradores de Seguro'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
