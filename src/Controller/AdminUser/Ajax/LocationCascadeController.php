<?php

declare(strict_types=1);

namespace App\Controller\AdminUser\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Branch;
use App\Entity\Tenant\Department;
use App\Entity\Tenant\MedicalService;
use App\Entity\Tenant\State;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador AJAX para cascade de ubicaciones: Sucursal → Unidad → Servicio
 */
#[Route('/admin/users/ajax')]
class LocationCascadeController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $em
    ) {}

    /**
     * Obtener unidades (departments) por sucursal (branch)
     */
    #[Route('/branch/{branchId}/departments', name: 'admin_user_ajax_branch_departments', methods: ['GET'])]
    public function getDepartmentsByBranch(int $branchId): JsonResponse
    {
        try {
            $branch = $this->em->getRepository(Branch::class)->find($branchId);
            
            if (!$branch) {
                return $this->json([
                    'success' => false,
                    'message' => 'Sucursal no encontrada',
                    'departments' => []
                ], 404);
            }

            $activeState = $this->getActiveState();
            
            $departments = $this->em->getRepository(Department::class)->findBy([
                'branch' => $branch,
                'state' => $activeState
            ], ['name' => 'ASC']);

            $data = array_map(function(Department $department) {
                return [
                    'id' => $department->getId(),
                    'name' => $department->getName(),
                    'code' => $department->getCode(),
                ];
            }, $departments);

            return $this->json([
                'success' => true,
                'departments' => $data
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al obtener unidades: ' . $e->getMessage(),
                'departments' => []
            ], 500);
        }
    }

    /**
     * Obtener servicios médicos por unidad (department)
     */
    #[Route('/department/{departmentId}/services', name: 'admin_user_ajax_department_services', methods: ['GET'])]
    public function getServicesByDepartment(int $departmentId): JsonResponse
    {
        try {
            $department = $this->em->getRepository(Department::class)->find($departmentId);
            
            if (!$department) {
                return $this->json([
                    'success' => false,
                    'message' => 'Unidad no encontrada',
                    'services' => []
                ], 404);
            }

            $activeState = $this->getActiveState();
            
            $services = $this->em->getRepository(MedicalService::class)->findBy([
                'department' => $department,
                'state' => $activeState
            ], ['name' => 'ASC']);

            $data = array_map(function(MedicalService $service) {
                return [
                    'id' => $service->getId(),
                    'name' => $service->getName(),
                    'code' => $service->getCode(),
                ];
            }, $services);

            return $this->json([
                'success' => true,
                'services' => $data
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al obtener servicios: ' . $e->getMessage(),
                'services' => []
            ], 500);
        }
    }

    /**
     * Obtiene el estado ACTIVE
     */
    private function getActiveState(): ?State
    {
        return $this->em->getRepository(State::class)->findOneBy(['name' => 'ACTIVE']);
    }
}
