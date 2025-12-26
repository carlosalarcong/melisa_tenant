<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Organization;
use App\Repository\MemberRepository;
use App\Service\AdminUser\LicenseValidationService;
use App\Service\AdminUser\UserManagementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador para activación de usuarios
 */
#[Route('/admin/users')]
class UserActivateController extends AbstractTenantAwareController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MemberRepository $memberRepository,
        private UserManagementService $userManagement,
        private LicenseValidationService $licenseService
    ) {}

    /**
     * Activar usuario
     */
    #[Route('/{id}/activate', name: 'admin_user_activate', methods: ['POST'])]
    public function activate(int $id, Request $request): Response
    {
        // Verificar CSRF token
        if (!$this->isCsrfTokenValid('activate_user_' . $id, $request->request->get('_token'))) {
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
            $this->addFlash('error', 'No tiene permisos para activar este usuario');
            return $this->redirectToRoute('admin_user_index');
        }

        try {
            // Verificar licencias disponibles
            if (!$this->licenseService->hasAvailableLicenses($organization)) {
                $this->addFlash('error', 'No hay licencias disponibles para activar este usuario');
                return $this->redirectToRoute('admin_user_index');
            }

            // Activar usuario
            $this->userManagement->activateUser($member);
            
            $this->addFlash('success', 'Usuario activado exitosamente');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al activar usuario: ' . $e->getMessage());
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
