<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Organization;
use App\Repository\MemberRepository;
use App\Service\AdminUser\PasswordManagementService;
use App\Service\AdminUser\ProfileManagementService;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador para visualización de detalles de usuario
 */
#[Route('/admin/users')]
class UserViewController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $em,
        private MemberRepository $memberRepository,
        private ProfileManagementService $profileManagement,
        private PasswordManagementService $passwordManagement
    ) {}

    /**
     * Ver detalles de usuario
     */
    #[Route('/{id}', name: 'admin_user_view', methods: ['GET'])]
    public function view(int $id): Response
    {
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
            $this->addFlash('error', 'No tiene permisos para ver este usuario');
            return $this->redirectToRoute('admin_user_index');
        }

        // Obtener perfiles y grupos del usuario
        $memberProfiles = $this->profileManagement->getActiveProfiles($member);
        $memberGroups = $this->profileManagement->getMemberGroups($member);

        // Obtener información de contraseña
        $passwordExpired = $this->passwordManagement->isPasswordExpired($member);
        $daysUntilExpiration = $this->passwordManagement->getDaysUntilExpiration($member);

        return $this->render('admin_user/view.html.twig', [
            'member' => $member,
            'memberProfiles' => $memberProfiles,
            'memberGroups' => $memberGroups,
            'passwordExpired' => $passwordExpired,
            'daysUntilExpiration' => $daysUntilExpiration,
            'organization' => $organization,
        ]);
    }

    /**
     * Obtiene la organización actual del tenant
     */
    private function getOrganization(): ?Organization
    {
        $tenant = $this->getTenant();
        // Buscar la primera organización activa
        return $this->em->getRepository(Organization::class)->findOneBy(['state' => $this->getActiveState()]);
    }
}
