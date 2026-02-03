<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\NutritionistBmiIndex;
use App\Form\Maintainers\Hospital\NutritionistBmiIndexType;
use App\Repository\Tenant\NutritionistBmiIndexRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/maintainers/hospital/nutritionist-bmi-index')]
class NutritionistBmiIndexController extends AbstractMantenedorController
{
    public function __construct(
        private NutritionistBmiIndexRepository $nutritionistBmiIndexRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService,
        TranslatorInterface $translator
    ) {
        parent::__construct($tenantEntityManager, $translator);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_nutritionist_bmi_index_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_nutritionist_bmi_index_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_nutritionist_bmi_index_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_nutritionist_bmi_index_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_nutritionist_bmi_index_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: $this->translateColumns(['name', 'is_active']),
            filename: 'indices_imc_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->nutritionistBmiIndexRepository->createQueryBuilder('nbi')
            ->orderBy('nbi.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
        'name' => $this->translator->trans('maintainers.columns.name', [], 'maintainers'),
        'isActive' => $this->translator->trans('maintainers.columns.is_active', [], 'maintainers')
    ];
    
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/hospital/nutritionist_bmi_index/index.html.twig';
    }

    protected function getFormType(): string
    {
        return NutritionistBmiIndexType::class;
    }

    protected function createNewEntity(): object
    {
        return new NutritionistBmiIndex();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_nutritionist_bmi_index_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Indice IMC',
            'edit' => 'Editar Indice IMC',
            default => 'Indices IMC'
        };
    }

    protected function beforeSave(object $entity, Request $request): void
    {
        $entity->setUpdatedAt(new \DateTime());
    }

    protected function canDelete(object $entity): bool
    {
        return true;
    }

    protected function getSuccessMessage(string $action): string
    {
        return match($action) {
            'create' => 'Índice IMC creado exitosamente',
            'edit' => 'Índice IMC actualizado exitosamente',
            'delete' => 'Índice IMC eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Índice IMC no encontrado',
            'cannot_delete' => 'No se puede eliminar este índice IMC porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
