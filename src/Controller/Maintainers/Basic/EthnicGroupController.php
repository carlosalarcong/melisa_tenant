<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\EthnicGroup;
use App\Form\Maintainers\EthnicGroupType;
use App\Repository\Tenant\EthnicGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Ethnic Group Controller
 * 
 * Gestiona el mantenedor de Pueblos Originarios
 */
#[Route('/maintainers/basic/ethnic-group')]
class EthnicGroupController extends AbstractMantenedorController
{
    public function __construct(
        private EthnicGroupRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_ethnic_group_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_ethnic_group_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_ethnic_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_ethnic_group_delete', methods: ['POST'])]
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
        return ['name', 'code', 'active'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/ethnic_group/index.html.twig';
    }

    protected function getFormType(): string
    {
        return EthnicGroupType::class;
    }

    protected function createNewEntity(): object
    {
        return new EthnicGroup();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_ethnic_group_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Pueblo Originario',
            'edit' => 'Editar Pueblo Originario',
            default => 'Pueblos Originarios'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
