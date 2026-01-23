<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\MedicalBox;
use App\Form\Maintainers\MedicalBoxType;
use App\Repository\Tenant\MedicalBoxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Medical Box Controller
 * 
 * Gestiona el mantenedor de Boxes
 */
#[Route('/maintainers/basic/medical-box')]
class MedicalBoxController extends AbstractMantenedorController
{
    public function __construct(
        private MedicalBoxRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_medical_box_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_medical_box_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_medical_box_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_medical_box_delete', methods: ['POST'])]
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
        return ['name', 'number', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/medical_box/index.html.twig';
    }

    protected function getFormType(): string
    {
        return MedicalBoxType::class;
    }

    protected function createNewEntity(): object
    {
        return new MedicalBox();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_medical_box_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Box',
            'edit' => 'Editar Box',
            default => 'Boxes Médicos'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
