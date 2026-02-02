<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\DocumentType;
use App\Form\Maintainers\Treasury\DocumentTypeType;
use App\Repository\Tenant\DocumentTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/maintainers/treasury/document-type')]
class DocumentTypeController extends AbstractMantenedorController
{
    public function __construct(
        private readonly DocumentTypeRepository $repository
    ) {
    }

    #[Route('/', name: 'app_maintainers_treasury_document_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->indexAction($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_document_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->createAction($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_document_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->editAction($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_document_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->deleteAction($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_treasury_document_type_export', methods: ['GET'])]
    public function export(): Response
    {
        return $this->exportAction();
    }

    protected function getEntityClass(): string
    {
        return DocumentType::class;
    }

    protected function getFormType(): string
    {
        return DocumentTypeType::class;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/document_type';
    }

    protected function getRoutePrefix(): string
    {
        return 'app_maintainers_treasury_document_type';
    }

    protected function getPageTitle(): string
    {
        return 'Tipos de Documento';
    }

    protected function getData(Request $request): array
    {
        $qb = $this->repository->createQueryBuilder('dt');

        $search = $request->query->get('search', '');
        if (!empty($search)) {
            $qb->andWhere('dt.name LIKE :search OR dt.siiCode LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('dt.name', 'ASC');

        return [
            'query' => $qb->getQuery(),
            'searchTerm' => $search
        ];
    }

    protected function getExportColumns(): array
    {
        return [
            'id' => 'ID',
            'siiCode' => 'Código SII',
            'name' => 'Nombre',
            'isDte' => '¿Es DTE?',
            'isLogistics' => '¿Es de Logística?',
            'isActive' => 'Activo'
        ];
    }

    protected function getExportFileName(): string
    {
        return 'tipos_documento_' . date('Y-m-d_His') . '.csv';
    }
}
