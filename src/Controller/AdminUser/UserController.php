<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use App\Repository\MemberRepository;
use App\Service\AdminUser\LicenseValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador principal para administración de usuarios
 * Maneja el listado y búsqueda de usuarios
 */
#[Route('/admin/users')]
class UserController extends AbstractTenantAwareController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MemberRepository $memberRepository,
        private LicenseValidationService $licenseService
    ) {}

    /**
     * Lista de usuarios con filtros
     */
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Obtener organización del tenant
        $organization = $this->getOrganization();
        
        if (!$organization) {
            $this->addFlash('error', 'No se pudo cargar la organización');
            return $this->redirectToRoute('dashboard');
        }

        // Obtener filtros de búsqueda
        $filters = [
            'search' => $request->query->get('search', ''),
            'state' => $request->query->get('state', ''),
            'role' => $request->query->get('role', ''),
            'userType' => $request->query->get('userType', ''),
        ];

        // Obtener usuarios filtrados
        $members = $this->memberRepository->findByFilters($organization, $filters);

        // Obtener información de licencias
        $licenseInfo = $this->licenseService->getLicenseInfo($organization);

        // Obtener estados y roles para los filtros
        $states = $this->em->getRepository(State::class)->findAll();
        $roles = $this->em->getRepository(Role::class)->findBy(['state' => $this->getActiveState()]);

        return $this->render('admin_user/index.html.twig', [
            'members' => $members,
            'filters' => $filters,
            'states' => $states,
            'roles' => $roles,
            'licenseInfo' => $licenseInfo,
            'organization' => $organization,
        ]);
    }

    /**
     * Obtiene la organización actual del tenant
     */
    private function getOrganization(): ?Organization
    {
        $tenant = $this->getTenant();
        
        // Buscar organización por el tenant
        // Asumiendo que el tenant tiene un ID de organización
        return $this->em->getRepository(Organization::class)->find($tenant['id'] ?? 1);
    }

    /**
     * Obtiene el estado ACTIVE
     */
    private function getActiveState(): ?State
    {
        return $this->em->getRepository(State::class)->findOneBy(['name' => 'ACTIVE']);
    }
}
