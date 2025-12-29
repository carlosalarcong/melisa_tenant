<?php

declare(strict_types=1);

namespace App\Controller\AdminUser\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Organization;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador AJAX para datos dinámicos de formularios de usuarios
 */
#[Route('/admin/users/ajax')]
class UserAjaxController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $em
    ) {}

    // NOTA: Los métodos getProfiles() y getGroups() fueron eliminados
    // porque las entidades Profile y Group ya no existen en el proyecto

    /**
     * Obtener roles disponibles
     */
    #[Route('/roles', name: 'admin_user_ajax_roles', methods: ['GET'])]
    public function getRoles(Request $request): JsonResponse
    {
        $activeState = $this->getActiveState();
        
        $roles = $this->em->getRepository(Role::class)->findBy([
            'state' => $activeState
        ]);

        $data = array_map(function(Role $role) {
            return [
                'id' => $role->getId(),
                'name' => $role->getName(),
                'description' => $role->getDescription(),
                'isClinicalProfessional' => $role->getIsClinicalProfessional(),
            ];
        }, $roles);

        return $this->json($data);
    }

    /**
     * Verificar disponibilidad de username
     */
    #[Route('/check-username', name: 'admin_user_ajax_check_username', methods: ['GET'])]
    public function checkUsername(Request $request): JsonResponse
    {
        $username = $request->query->get('username');
        $excludeMemberId = $request->query->get('excludeMemberId');

        if (empty($username)) {
            return $this->json(['available' => false, 'message' => 'Username requerido']);
        }

        $qb = $this->em->getRepository(\App\Entity\Tenant\Member::class)
            ->createQueryBuilder('m')
            ->where('m.username = :username')
            ->setParameter('username', $username);

        // Excluir el usuario actual al editar
        if ($excludeMemberId) {
            $qb->andWhere('m.id != :excludeId')
               ->setParameter('excludeId', $excludeMemberId);
        }

        $exists = $qb->getQuery()->getOneOrNullResult() !== null;

        return $this->json([
            'available' => !$exists,
            'message' => $exists ? 'Username ya está en uso' : 'Username disponible'
        ]);
    }

    /**
     * Verificar disponibilidad de email
     */
    #[Route('/check-email', name: 'admin_user_ajax_check_email', methods: ['GET'])]
    public function checkEmail(Request $request): JsonResponse
    {
        $email = $request->query->get('email');
        $excludeMemberId = $request->query->get('excludeMemberId');

        if (empty($email)) {
            return $this->json(['available' => false, 'message' => 'Email requerido']);
        }

        $qb = $this->em->getRepository(\App\Entity\Tenant\Member::class)
            ->createQueryBuilder('m')
            ->where('m.email = :email')
            ->setParameter('email', $email);

        // Excluir el usuario actual al editar
        if ($excludeMemberId) {
            $qb->andWhere('m.id != :excludeId')
               ->setParameter('excludeId', $excludeMemberId);
        }

        $exists = $qb->getQuery()->getOneOrNullResult() !== null;

        return $this->json([
            'available' => !$exists,
            'message' => $exists ? 'Email ya está en uso' : 'Email disponible'
        ]);
    }

    /**
     * Verificar disponibilidad de identificación
     */
    #[Route('/check-identification', name: 'admin_user_ajax_check_identification', methods: ['GET'])]
    public function checkIdentification(Request $request): JsonResponse
    {
        $identification = $request->query->get('identification');
        $excludePersonId = $request->query->get('excludePersonId');

        if (empty($identification)) {
            return $this->json(['available' => false, 'message' => 'Identificación requerida']);
        }

        $organization = $this->getOrganization();
        
        if (!$organization) {
            return $this->json(['error' => 'Organización no encontrada'], 404);
        }

        $qb = $this->em->getRepository(\App\Entity\Tenant\Person::class)
            ->createQueryBuilder('p')
            ->where('p.identification = :identification')
            ->andWhere('p.organization = :organization')
            ->setParameter('identification', $identification)
            ->setParameter('organization', $organization);

        // Excluir la persona actual al editar
        if ($excludePersonId) {
            $qb->andWhere('p.id != :excludeId')
               ->setParameter('excludeId', $excludePersonId);
        }

        $exists = $qb->getQuery()->getOneOrNullResult() !== null;

        return $this->json([
            'available' => !$exists,
            'message' => $exists ? 'Identificación ya está en uso' : 'Identificación disponible'
        ]);
    }

    /**
     * Obtiene la organización actual del tenant
     */
    private function getOrganization(): ?Organization
    {
        $tenant = $this->getTenant();
        return $this->em->getRepository(Organization::class)->find($tenant['id'] ?? 1);
    }

    /**
     * Identifica si un rol es profesional clínico
     * Retorna true si el rol requiere datos institucionales (cargo, sucursal, etc.)
     */
    #[Route('/identify-role/{roleId}', name: 'admin_user_ajax_identify_role', methods: ['GET'])]
    public function identifyRole(int $roleId): JsonResponse
    {
        try {
            $role = $this->em->getRepository(Role::class)->find($roleId);
            
            if (!$role) {
                return $this->json([
                    'success' => false,
                    'isClinicalProfessional' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'isClinicalProfessional' => $role->getIsClinicalProfessional(),
                'roleName' => $role->getName(),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'isClinicalProfessional' => false,
                'message' => 'Error al identificar rol: ' . $e->getMessage()
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
