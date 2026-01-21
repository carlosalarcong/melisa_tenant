<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\OriginType;
use App\Form\Maintainers\OriginTypeType;
use App\Repository\Tenant\OriginTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Origin Type Controller
 * 
 * Gestiona el mantenedor de Tipos de Origen
 */
#[Route('/maintainers/basic/origin-type')]
class OriginTypeController extends AbstractMantenedorController
{
    public function __construct(
        private OriginTypeRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_origin_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_origin_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_origin_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_origin_type_delete', methods: ['POST'])]
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
        return 'maintainers/basic/origin_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return OriginTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new OriginType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_origin_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo de Origen',
            'edit' => 'Editar Tipo de Origen',
            default => 'Tipos de Origen'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
