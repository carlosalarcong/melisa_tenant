<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\PathologyArticle;
use App\Form\Maintainers\Commercial\PathologyArticleType;
use App\Repository\Tenant\PathologyArticleRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/pathology-article')]
class PathologyArticleController extends AbstractMantenedorController
{
    public function __construct(
        private PathologyArticleRepository $pathologyArticleRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->pathologyArticleRepository->createQueryBuilder('pa')
            ->leftJoin('pa.gesPathology', 'gp')
            ->addSelect('gp')
            ->orderBy('pa.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return ['id', 'gesPathology.name', 'articleName', 'articleCode', 'quantity', 'unitCost', 'isMandatory', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/pathology_article/index.html.twig';
    }

    protected function getFormType(): string
    {
        return PathologyArticleType::class;
    }

    protected function createNewEntity(): object
    {
        return new PathologyArticle();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_pathology_article_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Artículo por Patología',
            'edit' => 'Editar Artículo por Patología',
            default => 'Artículos por Patología GES'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_pathology_article_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_pathology_article_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_pathology_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PathologyArticle $pathologyArticle): Response
    {
        return $this->handleEdit($request, $pathologyArticle->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_pathology_article_delete', methods: ['DELETE'])]
    public function delete(Request $request, PathologyArticle $pathologyArticle): Response
    {
        return $this->handleDelete($request, $pathologyArticle->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_pathology_article_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
