<?php

declare(strict_types=1);

namespace App\Controller\AdminUser;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use App\Form\Type\AdminUser\GroupAssignmentType;
use App\Form\Type\AdminUser\ProfileAssignmentType;
use App\Form\Type\AdminUser\UserType;
use App\Repository\GenderRepository;
use App\Repository\MemberRepository;
use App\Service\AdminUser\ProfileManagementService;
use App\Service\AdminUser\UserManagementService;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
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
        private TenantEntityManager $em,
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

        // Preparar datos iniciales del formulario
        $formData = [
            'identification' => $member->getPerson()->getIdentification(),
            'name' => $member->getPerson()->getName(),
            'lastName' => $member->getPerson()->getLastName(),
            'email' => $member->getEmail(),
            'phones' => $member->getPerson()->getPhones(),
            'birthDateAt' => $member->getPerson()->getBirthDateAt(),
            'gender' => $member->getPerson()->getGender(),
            'username' => $member->getUsername(),
            'userType' => $member->getUserType(),
            'role' => $member->getRole(),
            'state' => $member->getState(),
        ];

        // Crear formulario
        $form = $this->createForm(UserType::class, $formData, [
            'is_edit' => true,
            'require_password' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleEdit($form->getData(), $member);
        }
        
        // Obtener perfiles y grupos del usuario
        $memberProfiles = $this->profileManagement->getActiveProfiles($member);
        $memberGroups = $this->profileManagement->getMemberGroups($member);

        // Formularios de perfiles y grupos
        $profileForm = $this->createForm(ProfileAssignmentType::class, null, [
            'organization' => $organization,
        ]);
        
        $groupForm = $this->createForm(GroupAssignmentType::class, null, [
            'organization' => $organization,
        ]);

        return $this->render('admin_user/edit.html.twig', [
            'form' => $form->createView(),
            'profileForm' => $profileForm->createView(),
            'groupForm' => $groupForm->createView(),
            'member' => $member,
            'memberProfiles' => $memberProfiles,
            'memberGroups' => $memberGroups,
            'organization' => $organization,
        ]);
    }

    /**
     * Procesa la edición del usuario
     */
    private function handleEdit(array $formData, Member $member): Response
    {
        try {
            // Preparar datos completos
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
                'userType' => $formData['userType'],
                'role_id' => $formData['role']?->getId(),
                'state_id' => $formData['state']?->getId(),
            ];

            // Agregar contraseña solo si se proporcionó
            if (!empty($formData['password'])) {
                $data['password'] = $formData['password'];
            }

            // Actualizar usuario
            $this->userManagement->updateUser($member, $data);

            $this->addFlash('success', 'Usuario actualizado exitosamente');
            return $this->redirectToRoute('admin_user_view', ['id' => $member->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al actualizar usuario: ' . $e->getMessage());
            return $this->redirectToRoute('admin_user_edit', ['id' => $member->getId()]);
        }
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

    /**
     * Obtiene el estado ACTIVE
     */
    private function getActiveState(): ?State
    {
        return $this->em->getRepository(State::class)->findOneBy(['name' => 'ACTIVE']);
    }
}
