<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Organization;
use App\Repository\MemberRepository;
use App\Service\AdminUser\UserManagementService;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador para eliminación (inactivación) de usuarios
 */
#[Route('/admin/users')]
class UserDeleteController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $em,
        private MemberRepository $memberRepository,
        private UserManagementService $userManagement
    ) {}

    /**
     * Eliminar (inactivar) usuario
     */
    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        // Verificar CSRF token
        if (!$this->isCsrfTokenValid('delete_user_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF inválido');
            return $this->redirectToRoute('admin_user_index');
        }

        $organization = $this->getOrganization();
        
        if (!$organization) {
            $this->addFlash('error', 'No se pudo cargar la organización');
            return $this->redirectToRoute('admin_user_index');
        }

        // Obtener miembro
        $member = $this->memberRepository->find($id);
        
        if (!$member) {
            $this->addFlash('error', 'Usuario no encontrado');
            return $this->redirectToRoute('admin_user_index');
        }

        // Verificar que pertenece a la organización
        if ($member->getPerson()->getOrganization()->getId() !== $organization->getId()) {
            $this->addFlash('error', 'No tiene permisos para eliminar este usuario');
            return $this->redirectToRoute('admin_user_index');
        }

        try {
            // Eliminar (inactivar) usuario
            $this->userManagement->deleteUser($member);
            
            $this->addFlash('success', 'Usuario eliminado exitosamente');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * Obtiene la organización actual del tenant
     */
    private function getOrganization(): ?Organization
    {
        $tenant = $this->getTenant();
        return $this->em->getRepository(Organization::class)->find($tenant['id'] ?? 1);
    }
}
