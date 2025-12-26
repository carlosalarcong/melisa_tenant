<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
use App\Entity\Tenant\Person;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use App\Form\Type\AdminUser\UserType;
use App\Repository\GenderRepository;
use App\Service\AdminUser\LicenseValidationService;
use App\Service\AdminUser\UserManagementService;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador para creación de usuarios
 */
#[Route('/admin/users')]
class UserCreateController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $em,
        private UserManagementService $userManagement,
        private LicenseValidationService $licenseService,
        private GenderRepository $genderRepository
    ) {}

    /**
     * Formulario de creación de usuario
     */
    #[Route('/create', name: 'admin_user_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $organization = $this->getOrganization();
        
        if (!$organization) {
            $this->addFlash('error', 'No se pudo cargar la organización');
            return $this->redirectToRoute('admin_user_index');
        }

        // Verificar licencias disponibles
        if (!$this->licenseService->hasAvailableLicenses($organization)) {
            $this->addFlash('error', 'No hay licencias disponibles. Contacte con el administrador.');
            return $this->redirectToRoute('admin_user_index');
        }

        // Crear formulario
        $form = $this->createForm(UserType::class, null, [
            'is_edit' => false,
            'require_password' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleCreate($form->getData(), $organization, $request);
        }

        // Obtener información de licencias para mostrar
        $licenseInfo = $this->licenseService->getLicenseInfo($organization);

        // Si es petición AJAX, devolver solo el formulario
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin_user/user_form.html.twig', [
                'form' => $form->createView(),
                'is_edit' => false,
                'licenseInfo' => $licenseInfo,
                'organization' => $organization,
            ]);
        }

        // Devolver vista completa con layout
        return $this->render('admin_user/create.html.twig', [
            'form' => $form->createView(),
            'licenseInfo' => $licenseInfo,
            'organization' => $organization,
        ]);
    }

    /**
     * Procesa la creación del usuario
     */
    private function handleCreate(array $formData, Organization $organization, Request $request): Response
    {
        try {
            // Preparar datos completos del usuario
            $data = [
                // Person data
                'identification' => $formData['identification'],
                'name' => $formData['name'],
                'lastName' => $formData['lastName'],
                'email' => $formData['email'],
                'phones' => $formData['phones'] ?? '',
                'birthDateAt' => $formData['birthDateAt'] ?? null,
                'gender_id' => $formData['gender']?->getId(),
                // Member data
                'username' => $formData['username'],
                'password' => $formData['password'],
                'role_id' => $formData['role']?->getId(),
            ];

            // Determinar tipo de usuario
            $userTypeStr = $formData['userType'];
            $userType = $userTypeStr === 'PROFESSIONAL' 
                ? \App\Enum\UserTypeEnum::PROFESSIONAL 
                : \App\Enum\UserTypeEnum::ADMINISTRATIVE;

            // Crear usuario
            $member = $this->userManagement->createUser(
                $data,
                $organization,
                $userType
            );

            $this->addFlash('success', 'Usuario creado exitosamente');
            
            // Si es petición AJAX, devolver JSON
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'userId' => $member->getId(),
                    'redirect' => $this->generateUrl('admin_user_index')
                ]);
            }
            
            return $this->redirectToRoute('admin_user_view', ['id' => $member->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al crear usuario: ' . $e->getMessage());
            
            // Si es petición AJAX, devolver JSON con error
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error al crear usuario: ' . $e->getMessage()
                ], 400);
            }
            
            return $this->redirectToRoute('admin_user_create');
        }
    }

    /**
     * Obtiene la organización actual del tenant
     */
    private function getOrganization(): ?Organization
    {
        // Buscar la primera organización activa
        return $this->em->getRepository(Organization::class)->findOneBy(['state' => $this->getActiveState()]);
    }

    /**
     * Obtiene el estado ACTIVE
     */
    private function getActiveState(): ?State
    {
        return $this->em->getRepository(State::class)->findOneBy(['name' => 'ACTIVE']);
    }
}
