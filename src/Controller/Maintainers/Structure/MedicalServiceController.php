<?php

namespace App\Controller\Maintainers\Structure;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\MedicalService;
use App\Form\Maintainers\MedicalServiceType;
use App\Repository\Tenant\MedicalServiceRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/structure/medical-service')]
class MedicalServiceController extends AbstractMantenedorController
{
    public function __construct(
        private MedicalServiceRepository $medicalServiceRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_medical_service_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_medical_service_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_medical_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_medical_service_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_medical_service_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'code', 'department.name', 'hl7ServiceType', 'isActive'],
            headers: ['Nombre', 'Código', 'Departamento', 'Tipo HL7', 'Activo'],
            filename: 'servicios_medicos_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->medicalServiceRepository->createQueryBuilder('ms')
            ->leftJoin('ms.department', 'd')
            ->addSelect('d')
            ->orderBy('ms.name', 'ASC');
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'department', 'hl7ServiceType', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/structure/medical_service/index.html.twig';
    }

    protected function getFormType(): string
    {
        return MedicalServiceType::class;
    }

    protected function createNewEntity(): object
    {
        return new MedicalService();
    }

    protected function getEntityName(): string
    {
        return 'Servicio';
    }

    protected function getItemsPerPage(): int
    {
        return 15;
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_medical_service_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'index' => 'Servicios Médicos',
            'create' => 'Crear Servicio',
            'edit' => 'Editar Servicio',
            default => 'Servicios Médicos'
        };
    }
}
