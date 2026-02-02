<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\PaymentMethodType;
use App\Form\Maintainers\Treasury\PaymentMethodTypeType;
use App\Repository\Tenant\PaymentMethodTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/maintainers/treasury/payment-method-type')]
class PaymentMethodTypeController extends AbstractMantenedorController
{
    public function __construct(
        private readonly PaymentMethodTypeRepository $repository
    ) {
    }

    #[Route('/', name: 'app_maintainers_treasury_payment_method_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->indexAction($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_payment_method_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->createAction($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_payment_method_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->editAction($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_payment_method_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->deleteAction($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_treasury_payment_method_type_export', methods: ['GET'])]
    public function export(): Response
    {
        return $this->exportAction();
    }

    protected function getEntityClass(): string
    {
        return PaymentMethodType::class;
    }

    protected function getFormType(): string
    {
        return PaymentMethodTypeType::class;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/payment_method_type';
    }

    protected function getRoutePrefix(): string
    {
        return 'app_maintainers_treasury_payment_method_type';
    }

    protected function getPageTitle(): string
    {
        return 'Tipos de Método de Pago';
    }

    protected function getData(Request $request): array
    {
        $qb = $this->repository->createQueryBuilder('pmt');

        $search = $request->query->get('search', '');
        if (!empty($search)) {
            $qb->andWhere('pmt.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('pmt.name', 'ASC');

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
            'isActive' => 'Activo'
        ];
    }

    protected function getExportFileName(): string
    {
        return 'tipos_metodo_pago_' . date('Y-m-d_His') . '.csv';
    }
}
