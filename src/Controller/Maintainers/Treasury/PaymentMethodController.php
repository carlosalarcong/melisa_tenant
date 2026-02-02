<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\PaymentMethod;
use App\Form\Maintainers\Treasury\PaymentMethodType;
use App\Repository\Tenant\PaymentMethodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/maintainers/treasury/payment-method')]
class PaymentMethodController extends AbstractMantenedorController
{
    public function __construct(
        private readonly PaymentMethodRepository $repository
    ) {
    }

    #[Route('/', name: 'app_maintainers_treasury_payment_method_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->indexAction($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_payment_method_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->createAction($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_payment_method_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->editAction($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_payment_method_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->deleteAction($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_treasury_payment_method_export', methods: ['GET'])]
    public function export(): Response
    {
        return $this->exportAction();
    }

    protected function getEntityClass(): string
    {
        return PaymentMethod::class;
    }

    protected function getFormType(): string
    {
        return PaymentMethodType::class;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/payment_method';
    }

    protected function getRoutePrefix(): string
    {
        return 'app_maintainers_treasury_payment_method';
    }

    protected function getPageTitle(): string
    {
        return 'Métodos de Pago';
    }

    protected function getData(Request $request): array
    {
        $qb = $this->repository->createQueryBuilder('pm')
            ->leftJoin('pm.parent', 'parent')
            ->addSelect('parent')
            ->leftJoin('pm.paymentMethodType', 'pmt')
            ->addSelect('pmt');

        $search = $request->query->get('search', '');
        if (!empty($search)) {
            $qb->andWhere('pm.name LIKE :search OR pm.code LIKE :search OR parent.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('pm.name', 'ASC');

        return [
            'query' => $qb->getQuery(),
            'searchTerm' => $search
        ];
    }

    protected function getExportColumns(): array
    {
        return [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'parent.name' => 'Método Padre',
            'paymentMethodType.name' => 'Tipo',
            'issuesReceipt' => 'Emite Recibo',
            'isGuarantee' => 'Es Garantía',
            'isProfessionalPayment' => 'Pago Profesional',
            'isWebPayment' => 'Pago Web',
            'documentTypeCode' => 'Código Doc.',
            'accountingCode' => 'Código Contable',
            'visibleInCashRegister' => 'Visible en Caja',
            'creditCardPayment' => 'Pago con Tarjeta',
            'isActive' => 'Activo'
        ];
    }

    protected function getExportFileName(): string
    {
        return 'metodos_pago_' . date('Y-m-d_His') . '.csv';
    }
}
