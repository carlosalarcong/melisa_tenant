<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\EducationLevel;
use App\Form\Maintainers\EducationLevelType;
use App\Repository\Tenant\EducationLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Education Level Controller
 * 
 * Gestiona el mantenedor de Niveles de Instrucción
 */
#[Route('/maintainers/basic/education-level')]
class EducationLevelController extends AbstractMantenedorController
{
    public function __construct(
        private EducationLevelRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_education_level_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_education_level_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_education_level_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_education_level_delete', methods: ['POST'])]
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
        return 'maintainers/basic/education_level/index.html.twig';
    }

    protected function getFormType(): string
    {
        return EducationLevelType::class;
    }

    protected function createNewEntity(): object
    {
        return new EducationLevel();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_education_level_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Nivel de Instrucción',
            'edit' => 'Editar Nivel de Instrucción',
            default => 'Niveles de Instrucción'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
