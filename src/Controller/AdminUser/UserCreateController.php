<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
use App\Entity\Tenant\Person;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use App\Repository\GenderRepository;
use App\Service\AdminUser\LicenseValidationService;
use App\Service\AdminUser\UserManagementService;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $em,
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

        // Procesar formulario POST
        if ($request->isMethod('POST')) {
            return $this->handleCreate($request, $organization);
        }

        // Obtener datos para el formulario
        $roles = $this->em->getRepository(Role::class)->findBy(['state' => $this->getActiveState()]);
        $genders = $this->genderRepository->findAll();
        $licenseInfo = $this->licenseService->getLicenseInfo($organization);

        return $this->render('admin_user/create.html.twig', [
            'roles' => $roles,
            'genders' => $genders,
            'licenseInfo' => $licenseInfo,
            'organization' => $organization,
        ]);
    }

    /**
     * Procesa la creación del usuario
     */
    private function handleCreate(Request $request, Organization $organization): Response
    {
        try {
            // Validar datos requeridos
            $errors = $this->validateCreateData($request);
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('admin_user_create');
            }

            // Preparar datos completos del usuario
            $data = [
                // Person data
                'identification' => $request->request->get('identification'),
                'name' => $request->request->get('name'),
                'lastName' => $request->request->get('lastName'),
                'email' => $request->request->get('email'),
                'phones' => $request->request->get('phones', ''),
                'birthDateAt' => $request->request->get('birthDateAt') 
                    ? new \DateTimeImmutable($request->request->get('birthDateAt'))
                    : null,
                'gender_id' => (int) $request->request->get('gender_id'),
                // Member data
                'username' => $request->request->get('username'),
                'password' => $request->request->get('password'),
                'role_id' => (int) $request->request->get('role_id'),
            ];

            // Determinar tipo de usuario
            $userTypeStr = $request->request->get('userType');
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
            return $this->redirectToRoute('admin_user_view', ['id' => $member->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al crear usuario: ' . $e->getMessage());
            return $this->redirectToRoute('admin_user_create');
        }
    }

    /**
     * Valida los datos del formulario
     */
    private function validateCreateData(Request $request): array
    {
        $errors = [];

        // Campos requeridos
        $required = [
            'identification' => 'RUT/Identificación',
            'name' => 'Nombre',
            'lastName' => 'Apellido',
            'email' => 'Email',
            'username' => 'Nombre de usuario',
            'password' => 'Contraseña',
            'userType' => 'Tipo de usuario',
            'role_id' => 'Rol',
            'gender_id' => 'Género',
        ];

        foreach ($required as $field => $label) {
            if (empty($request->request->get($field))) {
                $errors[] = "El campo {$label} es requerido";
            }
        }

        // Validar email
        $email = $request->request->get('email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        }

        // Validar contraseña
        $password = $request->request->get('password');
        $passwordConfirm = $request->request->get('password_confirm');
        
        if ($password && strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Las contraseñas no coinciden';
        }

        return $errors;
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
     * Obtiene el estado ACTIVE
     */
    private function getActiveState(): ?State
    {
        return $this->em->getRepository(State::class)->findOneBy(['name' => 'ACTIVE']);
    }
}
