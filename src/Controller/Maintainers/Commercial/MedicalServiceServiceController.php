<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\MedicalServiceService;
use App\Form\Maintainers\Commercial\MedicalServiceServiceType;
use App\Repository\Tenant\MedicalServiceServiceRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/medical-service-service')]
class MedicalServiceServiceController extends AbstractMantenedorController
{
    public function __construct(
        private MedicalServiceServiceRepository $medicalServiceServiceRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->medicalServiceServiceRepository->createQueryBuilder('mss')
            ->leftJoin('mss.medicalService', 'ms')
            ->leftJoin('mss.service', 's')
            ->addSelect('ms', 's')
            ->orderBy('mss.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
            'id' => 'ID',
            'medicalService.name' => 'Acción Clínica',
            'service.name' => 'Servicio',
            'isActive' => 'Activo'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/medical_service_service/index.html.twig';
    }

    protected function getFormType(): string
    {
        return MedicalServiceServiceType::class;
    }

    protected function createNewEntity(): object
    {
        return new MedicalServiceService();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_medical_service_service_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Asignar Servicio a Acción Clínica',
            'edit' => 'Editar Servicio de Acción Clínica',
            default => 'Servicios por Acción Clínica'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_medical_service_service_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_medical_service_service_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_medical_service_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MedicalServiceService $medicalServiceService): Response
    {
        return $this->handleEdit($request, $medicalServiceService->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_medical_service_service_delete', methods: ['DELETE'])]
    public function delete(Request $request, MedicalServiceService $medicalServiceService): Response
    {
        return $this->handleDelete($request, $medicalServiceService->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_medical_service_service_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
