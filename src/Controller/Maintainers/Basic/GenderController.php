<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\Gender;
use App\Form\Maintainers\GenderType;
use App\Repository\Tenant\GenderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gender Controller
 * 
 * Gestiona el mantenedor de Géneros/Sexos
 * Los géneros son datos compartidos por todos los tenants
 */
#[Route('/maintainers/basic/gender')]
class GenderController extends AbstractMantenedorController
{
    public function __construct(
        private GenderRepository $genderRepository,
        $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_gender_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return parent::index($request);
    }

    #[Route('/create', name: 'app_maintainers_gender_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return parent::create($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_gender_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return parent::edit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_gender_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return parent::delete($request, $id);
    }

    // ========================================================================
    // Implementación de métodos abstractos
    // ========================================================================

    protected function getData(Request $request): array
    {
        // Todos los géneros - sin filtrar por tenant (dato maestro compartido)
        return $this->genderRepository->findAll();
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'active'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/gender/index.html.twig';
    }

    protected function getFormType(): string
    {
        return GenderType::class;
    }

    protected function createNewEntity(): object
    {
        return new Gender();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_gender_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Create Gender',
            'edit' => 'Edit Gender',
            default => 'Gender Management'
        };
    }

    // ========================================================================
    // Hooks personalizados (opcional)
    // ========================================================================

    /**
     * Normalizar el código a mayúsculas antes de guardar
     */
    protected function beforeSave(object $entity, Request $request): void
    {
        if ($entity instanceof Gender && $entity->getCode()) {
            $entity->setCode(strtoupper($entity->getCode()));
        }
        
        $entity->setUpdatedAt(new \DateTime());
    }

    /**
     * Verificar que no haya registros asociados antes de eliminar
     */
    protected function canDelete(object $entity): bool
    {
        // TODO: Verificar si hay pacientes con este género
        // $count = $this->patientRepository->countByGender($entity);
        // return $count === 0;
        
        return true;
    }

    /**
     * Mensajes personalizados en español
     */
    protected function getSuccessMessage(string $action): string
    {
        return match($action) {
            'create' => 'Género creado exitosamente',
            'edit' => 'Género actualizado exitosamente',
            'delete' => 'Género eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Género no encontrado',
            'cannot_delete' => 'No se puede eliminar este género porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
