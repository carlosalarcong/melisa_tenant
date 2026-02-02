<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CashRegisterLocation;
use App\Form\Maintainers\Treasury\CashRegisterLocationType;
use App\Repository\Tenant\CashRegisterLocationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/maintainers/treasury/cash-register-location')]
class CashRegisterLocationController extends AbstractMantenedorController
{
    public function __construct(
        private readonly CashRegisterLocationRepository $repository
    ) {
    }

    #[Route('/', name: 'app_maintainers_treasury_cash_register_location_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->indexAction($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_cash_register_location_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->createAction($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_cash_register_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->editAction($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_cash_register_location_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->deleteAction($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_treasury_cash_register_location_export', methods: ['GET'])]
    public function export(): Response
    {
        return $this->exportAction();
    }

    protected function getEntityClass(): string
    {
        return CashRegisterLocation::class;
    }

    protected function getFormType(): string
    {
        return CashRegisterLocationType::class;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/cash_register_location';
    }

    protected function getRoutePrefix(): string
    {
        return 'app_maintainers_treasury_cash_register_location';
    }

    protected function getPageTitle(): string
    {
        return 'Ubicaciones de Caja';
    }

    protected function getData(Request $request): array
    {
        $qb = $this->repository->createQueryBuilder('crl')
            ->leftJoin('crl.branch', 'b')
            ->addSelect('b');

        $search = $request->query->get('search', '');
        if (!empty($search)) {
            $qb->andWhere('crl.name LIKE :search OR crl.description LIKE :search OR b.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('crl.name', 'ASC');

        return [
            'query' => $qb->getQuery(),
            'searchTerm' => $search
        ];
    }

    protected function getExportColumns(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'branch.name' => 'Sucursal',
            'isActive' => 'Activo'
        ];
    }

    protected function getExportFileName(): string
    {
        return 'ubicaciones_caja_' . date('Y-m-d_His') . '.csv';
    }
}
