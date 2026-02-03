<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\ConsultationType;
use App\Form\Maintainers\Commercial\ConsultationTypeType;
use App\Repository\Tenant\ConsultationTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/consultation-type')]
class ConsultationTypeController extends AbstractMantenedorController
{
    public function __construct(
        private ConsultationTypeRepository $consultationTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->consultationTypeRepository->createQueryBuilder('ct')
            ->orderBy('ct.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
        'id' => 'Id',
        'name' => 'Nombre',
        'code' => 'Código',
        'description' => 'Descripción',
        'isActive' => 'Estado'
    ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/consultation_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return ConsultationTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new ConsultationType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_consultation_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo de Consulta',
            'edit' => 'Editar Tipo de Consulta',
            default => 'Tipos de Consulta'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_consultation_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_consultation_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_consultation_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConsultationType $consultationType): Response
    {
        return $this->handleEdit($request, $consultationType->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_consultation_type_delete', methods: ['DELETE'])]
    public function delete(Request $request, ConsultationType $consultationType): Response
    {
        return $this->handleDelete($request, $consultationType->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_consultation_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
