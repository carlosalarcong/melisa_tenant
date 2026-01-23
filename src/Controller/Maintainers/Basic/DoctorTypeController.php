<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\DoctorType;
use App\Form\Maintainers\DoctorTypeType;
use App\Repository\Tenant\DoctorTypeRepository;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Doctor Type Controller
 * 
 * Gestiona el mantenedor de Tipos de Médico
 */
#[Route('/maintainers/basic/doctor-type')]
class DoctorTypeController extends AbstractMantenedorController
{
    public function __construct(
        private DoctorTypeRepository $repository,
        TenantEntityManager $tenantEntityManager
    ) {
        parent::__construct($tenantEntityManager);
    }

    #[Route('', name: 'app_maintainers_doctor_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_doctor_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_doctor_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_doctor_type_delete', methods: ['POST'])]
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
        return 'maintainers/basic/doctor_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return DoctorTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new DoctorType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_doctor_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo de Médico',
            'edit' => 'Editar Tipo de Médico',
            default => 'Tipos de Médico'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
