<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\Occupation;
use App\Form\Maintainers\OccupationType;
use App\Repository\Tenant\OccupationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Occupation Controller
 * 
 * Gestiona el mantenedor de Ocupaciones
 */
#[Route('/maintainers/basic/occupation')]
class OccupationController extends AbstractMantenedorController
{
    public function __construct(
        private OccupationRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_occupation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_occupation_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_occupation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_occupation_delete', methods: ['POST'])]
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
        return ['name', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/occupation/index.html.twig';
    }

    protected function getFormType(): string
    {
        return OccupationType::class;
    }

    protected function createNewEntity(): object
    {
        return new Occupation();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_occupation_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Ocupación',
            'edit' => 'Editar Ocupación',
            default => 'Ocupaciones'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
