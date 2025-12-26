# 🎮 Fase 4A: Migración de Controladores Principales

## 🎯 Objetivo
Migrar los controladores principales del módulo de Administración de Usuarios de Symfony 3 a Symfony 6 usando atributos PHP 8 y mejores prácticas.

---

## 📋 Controladores a Migrar

### Controladores Principales
1. **DatosMaestrosMedicosController** → **UserController**
2. **DMMNuevoController** → **UserCreateController**
3. **DMMEditController** → **UserEditController**
4. **DMMVerController** → **UserViewController**
5. **DMMAddController** → **UserGroupController**
6. **DMMDellController** → **UserDeleteController**
7. **DMMActController** → **UserActivateController**
8. **DMMUnlockController** → **UserUnlockController**
9. **DMMExportarExcelController** → **UserExportController**

---

## 🔄 Cambios Generales en Todos los Controladores

### ANTES (Symfony 3):
```php
namespace Rebsol\AdministradorUsuariosBundle\Controller\_Default\DatosMaestrosMedicos;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DMMNuevoController extends DatosMaestrosMedicosController
{
    public function nuevoUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // ...
    }
}
```

### DESPUÉS (Symfony 6):
```php
namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserCreateController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService
    ) {}
    
    #[Route('/nuevo', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // ...
    }
}
```

### Cambios Clave:
1. ✅ **Namespace moderno**: `App\Controller\Admin\User`
2. ✅ **Hereda de**: `AbstractTenantAwareController` (con tenant context automático)
3. ✅ **Atributos PHP 8**: `#[Route]` en lugar de anotaciones
4. ✅ **Type hints estrictos**: Parámetros y retorno tipados
5. ✅ **Inyección de dependencias**: Constructor con servicios
6. ✅ **Métodos sin sufijo "Action"**: `new()` en lugar de `nuevoAction()`

---

## 1️⃣ UserController.php (Base y Listados)

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Service\User\UserManagementService;
use App\Service\User\LicenseValidationService;
use App\Repository\UsuariosRebsolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService,
        private LicenseValidationService $licenseService,
        private UsuariosRebsolRepository $userRepository
    ) {}

    /**
     * Listado de usuarios administrativos
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $tenant = $this->getTenant();
        
        // Obtener usuarios con datos relacionados
        $users = $this->userRepository->findAllUsersWithDetails($tenant, false);
        
        // Información de licencias
        $licenseInfo = $this->licenseService->getLicenseInfo();
        
        // Verificar expiración de contraseñas
        $expirationWarnings = $this->userService->checkPasswordExpirations($users);
        
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'license_info' => $licenseInfo,
            'expiration_warnings' => $expirationWarnings,
            'is_professional' => false
        ]);
    }

    /**
     * Listado de profesionales médicos
     */
    #[Route('/profesionales', name: 'professionals', methods: ['GET'])]
    public function professionals(Request $request): Response
    {
        $tenant = $this->getTenant();
        
        // Obtener solo profesionales (rol con profClinico = 1)
        $professionals = $this->userRepository->findAllUsersWithDetails($tenant, true);
        
        $licenseInfo = $this->licenseService->getLicenseInfo();
        
        return $this->render('admin/user/professional_index.html.twig', [
            'users' => $professionals,
            'license_info' => $licenseInfo,
            'is_professional' => true
        ]);
    }

    /**
     * Dashboard de administración de usuarios
     */
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $tenant = $this->getTenant();
        
        $stats = [
            'total_users' => $this->userRepository->countTotalUsers($tenant),
            'active_users' => $this->userRepository->countActiveUsers($tenant),
            'blocked_users' => $this->userRepository->countBlockedUsers($tenant),
            'professionals' => $this->userRepository->countProfessionals($tenant),
            'license_info' => $this->licenseService->getLicenseInfo(),
        ];
        
        return $this->render('admin/user/dashboard.html.twig', [
            'stats' => $stats
        ]);
    }
}
```

---

## 2️⃣ UserCreateController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Form\Type\User\UserType;
use App\Form\Type\User\ProfessionalType;
use App\Service\User\UserManagementService;
use App\Service\User\LicenseValidationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserCreateController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService,
        private LicenseValidationService $licenseService
    ) {}

    /**
     * Crear nuevo usuario administrativo
     */
    #[Route('/nuevo', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Verificar licencias disponibles
        if (!$this->licenseService->hasAvailableLicenses()) {
            $this->addFlash('error', 'No hay licencias disponibles para crear usuarios');
            return $this->redirectToRoute('admin_user_index');
        }

        $tenant = $this->getTenant();
        
        $form = $this->createForm(UserType::class, null, [
            'is_new' => true,
            'is_professional' => false,
            'empresa' => $tenant
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $user = $this->userService->createUser($data, false);

                $this->addFlash('success', sprintf(
                    'Usuario "%s" creado exitosamente',
                    $user->getNombreUsuario()
                ));

                return $this->redirectToRoute('admin_user_index');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al crear usuario: ' . $e->getMessage());
            }
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form,
            'is_professional' => false,
            'license_info' => $this->licenseService->getLicenseInfo()
        ]);
    }

    /**
     * Crear nuevo profesional médico
     */
    #[Route('/profesionales/nuevo', name: 'new_professional', methods: ['GET', 'POST'])]
    public function newProfessional(Request $request): Response
    {
        if (!$this->licenseService->hasAvailableLicenses()) {
            $this->addFlash('error', 'No hay licencias disponibles');
            return $this->redirectToRoute('admin_user_professionals');
        }

        $tenant = $this->getTenant();
        
        $form = $this->createForm(ProfessionalType::class, null, [
            'is_new' => true,
            'empresa' => $tenant
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $user = $this->userService->createUser($data, true);

                $this->addFlash('success', sprintf(
                    'Profesional "%s" creado exitosamente',
                    $user->getNombreUsuario()
                ));

                return $this->redirectToRoute('admin_user_professionals');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al crear profesional: ' . $e->getMessage());
            }
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form,
            'is_professional' => true,
            'license_info' => $this->licenseService->getLicenseInfo()
        ]);
    }
}
```

---

## 3️⃣ UserEditController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Form\Type\User\UserType;
use App\Form\Type\User\ProfessionalType;
use App\Service\User\UserManagementService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserEditController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService
    ) {}

    /**
     * Editar usuario
     */
    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] UsuariosRebsol $user
    ): Response {
        $tenant = $this->getTenant();
        
        // Verificar que el usuario pertenece al tenant
        if ($user->getIdPersona()->getIdEmpresa()->getId() !== $tenant->getId()) {
            throw $this->createAccessDeniedException();
        }

        $isProfessional = $user->getIdRol()->getProfClinico() == 1;
        
        $formClass = $isProfessional ? ProfessionalType::class : UserType::class;
        
        $form = $this->createForm($formClass, $user, [
            'is_new' => false,
            'is_professional' => $isProfessional,
            'empresa' => $tenant
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $this->userService->updateUser($user, $data);

                $this->addFlash('success', 'Usuario actualizado exitosamente');

                return $this->redirectToRoute('admin_user_view', ['id' => $user->getId()]);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al actualizar: ' . $e->getMessage());
            }
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
            'is_professional' => $isProfessional
        ]);
    }

    /**
     * Subir foto de perfil
     */
    #[Route('/{id}/foto', name: 'upload_photo', methods: ['POST'])]
    public function uploadPhoto(
        Request $request,
        #[MapEntity] UsuariosRebsol $user
    ): Response {
        try {
            $file = $request->files->get('photo');
            
            if ($file) {
                $this->userService->updateProfilePhoto($user, $file);
                $this->addFlash('success', 'Foto actualizada exitosamente');
            }

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al subir foto: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
    }
}
```

---

## 4️⃣ UserViewController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Service\User\ProfileManagementService;
use App\Service\User\UserSpecialtyService;
use App\Repository\UsuariosRebsolRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserViewController extends AbstractTenantAwareController
{
    public function __construct(
        private ProfileManagementService $profileService,
        private UserSpecialtyService $specialtyService,
        private UsuariosRebsolRepository $userRepository
    ) {}

    /**
     * Ver detalles de usuario
     */
    #[Route('/{id}', name: 'view', methods: ['GET'])]
    public function view(
        #[MapEntity] UsuariosRebsol $user
    ): Response {
        $tenant = $this->getTenant();
        
        // Verificar pertenencia al tenant
        if ($user->getIdPersona()->getIdEmpresa()->getId() !== $tenant->getId()) {
            throw $this->createAccessDeniedException();
        }

        $isProfessional = $user->getIdRol()->getProfClinico() == 1;
        
        // Obtener datos relacionados
        $userDetails = [
            'user' => $user,
            'is_professional' => $isProfessional,
            'groups' => $this->profileService->getUserGroups($user),
            'profiles' => $this->profileService->getActiveProfiles($user),
            'services' => $this->userRepository->getUserServices($user->getId()),
            'login_history' => $this->userRepository->getLastLogins($user->getId(), 10),
        ];
        
        // Si es profesional, obtener especialidades
        if ($isProfessional) {
            $userDetails['specialties'] = $this->specialtyService->getUserSpecialties($user);
            $userDetails['blocked_specialties'] = $this->specialtyService->getBlockedSpecialties($user);
        }

        return $this->render('admin/user/view.html.twig', $userDetails);
    }
}
```

---

## 5️⃣ UserDeleteController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Service\User\UserManagementService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserDeleteController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService
    ) {}

    /**
     * Inactivar usuario (eliminación lógica)
     */
    #[Route('/{id}/eliminar', name: 'delete', methods: ['POST', 'DELETE'])]
    public function delete(
        Request $request,
        #[MapEntity] UsuariosRebsol $user
    ): JsonResponse {
        // Verificar token CSRF
        if (!$this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Token CSRF inválido'
            ], 400);
        }

        // No permitir eliminar el usuario actual
        if ($this->getUser()->getId() === $user->getId()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 400);
        }

        try {
            $this->userService->deleteUser($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Usuario inactivado exitosamente'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 6️⃣ UserActivateController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Service\User\UserManagementService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserActivateController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService
    ) {}

    /**
     * Reactivar usuario inactivo
     */
    #[Route('/{id}/activar', name: 'activate', methods: ['POST'])]
    public function activate(
        Request $request,
        #[MapEntity] UsuariosRebsol $user
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('activate-user-' . $user->getId(), $request->request->get('_token'))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Token CSRF inválido'
            ], 400);
        }

        try {
            $this->userService->activateUser($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Usuario reactivado exitosamente'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

---

## ⏱️ Tiempo Estimado

- **UserController:** 1 día
- **UserCreateController:** 1 día
- **UserEditController:** 1 día
- **UserViewController:** 0.5 días
- **UserDeleteController:** 0.5 días
- **UserActivateController:** 0.5 días
- **Testing:** 1 día
- **Total:** **6 días**

---

## ➡️ Siguiente Paso

[04B - Controladores Complementarios y AJAX](./MIGRACION-04B-CONTROLADORES-AJAX.md)

---

**Fase:** 4A de 10  
**Prioridad:** 🔴 Alta  
**Riesgo:** 🟡 Medio
