<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use App\Repository\GenderRepository;
use App\Repository\MemberRepository;
use App\Service\AdminUser\ProfileManagementService;
use App\Service\AdminUser\UserManagementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlador para edición de usuarios
 */
#[Route('/admin/users')]
class UserEditController extends AbstractTenantAwareController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MemberRepository $memberRepository,
        private UserManagementService $userManagement,
        private ProfileManagementService $profileManagement,
        private GenderRepository $genderRepository
    ) {}

    /**
     * Formulario de edición de usuario
     */
    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
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
            $this->addFlash('error', 'No tiene permisos para editar este usuario');
            return $this->redirectToRoute('admin_user_index');
        }

        // Procesar formulario POST
        if ($request->isMethod('POST')) {
            return $this->handleEdit($request, $member);
        }

        // Obtener datos para el formulario
        $roles = $this->em->getRepository(Role::class)->findBy(['state' => $this->getActiveState()]);
        $genders = $this->genderRepository->findAll();
        $states = $this->em->getRepository(State::class)->findAll();
        
        // Obtener perfiles y grupos del usuario
        $memberProfiles = $this->profileManagement->getActiveProfiles($member);
        $memberGroups = $this->profileManagement->getMemberGroups($member);

        return $this->render('admin_user/edit.html.twig', [
            'member' => $member,
            'roles' => $roles,
            'genders' => $genders,
            'states' => $states,
            'memberProfiles' => $memberProfiles,
            'memberGroups' => $memberGroups,
            'organization' => $organization,
        ]);
    }

    /**
     * Procesa la edición del usuario
     */
    private function handleEdit(Request $request, Member $member): Response
    {
        try {
            // Validar datos requeridos
            $errors = $this->validateEditData($request);
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('admin_user_edit', ['id' => $member->getId()]);
            }

            $person = $member->getPerson();

            // Preparar datos completos
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
                'userType' => $request->request->get('userType'),
                'role_id' => (int) $request->request->get('role_id'),
                'state_id' => (int) $request->request->get('state_id'),
            ];

            // Agregar contraseña solo si se proporcionó
            $password = $request->request->get('password');
            if (!empty($password)) {
                $data['password'] = $password;
            }

            // Actualizar usuario
            $this->userManagement->updateUser($member, $data);

            // Actualizar perfiles y grupos si se proporcionaron
            $profileIds = $request->request->all('profile_ids') ?? [];
            $groupIds = $request->request->all('group_ids') ?? [];
            
            if (!empty($profileIds)) {
                $this->profileManagement->updateMemberProfiles($member, $profileIds);
            }
            
            if (!empty($groupIds)) {
                $this->profileManagement->updateMemberGroups($member, $groupIds);
            }

            $this->addFlash('success', 'Usuario actualizado exitosamente');
            return $this->redirectToRoute('admin_user_view', ['id' => $member->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al actualizar usuario: ' . $e->getMessage());
            return $this->redirectToRoute('admin_user_edit', ['id' => $member->getId()]);
        }
    }

    /**
     * Valida los datos del formulario
     */
    private function validateEditData(Request $request): array
    {
        $errors = [];

        // Campos requeridos
        $required = [
            'identification' => 'RUT/Identificación',
            'name' => 'Nombre',
            'lastName' => 'Apellido',
            'email' => 'Email',
            'username' => 'Nombre de usuario',
            'userType' => 'Tipo de usuario',
            'role_id' => 'Rol',
            'gender_id' => 'Género',
            'state_id' => 'Estado',
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

        // Validar contraseña si se proporcionó
        $password = $request->request->get('password');
        if ($password) {
            if (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres';
            }

            $passwordConfirm = $request->request->get('password_confirm');
            if ($password !== $passwordConfirm) {
                $errors[] = 'Las contraseñas no coinciden';
            }
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
