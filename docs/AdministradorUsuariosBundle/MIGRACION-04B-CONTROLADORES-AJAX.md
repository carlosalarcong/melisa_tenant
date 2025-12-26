# 🎮 Fase 4B: Controladores Complementarios y AJAX

## 🎯 Objetivo
Migrar los controladores complementarios y endpoints AJAX del módulo.

---

## 📋 Controladores a Migrar

### Complementarios
1. **DMMAddController** → **UserGroupController**
2. **DMMUnlockController** → **UserUnlockController**
3. **DMMExportarExcelController** → **UserExportController**
4. **Zoom Integration** → **UserZoomController**

### AJAX
1. **GrupoPerfilController** → **GroupProfileController**
2. **UnidadporSucursalController** → **UnitBranchController**
3. **ServicioporUnidadController** → **ServiceUnitController**
4. **ValrutController** → **ValidateRutController**
5. **ValusernameController** → **ValidateUsernameController**
6. **VigenciaController** → **ValidateVigenciaController**

---

## 1️⃣ UserGroupController.php (Gestión Grupos/Perfiles)

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Form\Type\User\ProfileAssignmentType;
use App\Service\User\ProfileManagementService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios/{id}/perfiles', name: 'admin_user_profile_')]
class UserGroupController extends AbstractTenantAwareController
{
    public function __construct(
        private ProfileManagementService $profileService
    ) {}

    /**
     * Mostrar modal de asignación de grupos y perfiles
     */
    #[Route('/asignar', name: 'assign', methods: ['GET', 'POST'])]
    public function assign(
        Request $request,
        #[MapEntity] UsuariosRebsol $user
    ): Response {
        $tenant = $this->getTenant();
        
        // Obtener grupos y perfiles actuales
        $currentGroups = $this->profileService->getUserGroups($user);
        $currentProfiles = $this->profileService->getActiveProfiles($user);
        
        $form = $this->createForm(ProfileAssignmentType::class, [
            'groups' => $currentGroups,
            'profiles' => $currentProfiles
        ], [
            'empresa' => $tenant
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Validar que tenga al menos 1 grupo O 1 perfil
            if (empty($data['groups']) && empty($data['profiles'])) {
                $this->addFlash('error', 'Debe asignar al menos un grupo o perfil');
                return $this->redirectToRoute('admin_user_profile_assign', ['id' => $user->getId()]);
            }

            try {
                $this->profileService->updateUserProfiles(
                    $user,
                    $data['groups'] ?? [],
                    $data['profiles'] ?? []
                );

                $this->addFlash('success', 'Grupos y perfiles actualizados. El usuario debe iniciar sesión nuevamente.');

                return $this->redirectToRoute('admin_user_view', ['id' => $user->getId()]);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Error: ' . $e->getMessage());
            }
        }

        return $this->render('admin/user/assign_profiles.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}
```

---

## 2️⃣ UserUnlockController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Service\User\UserSessionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserUnlockController extends AbstractTenantAwareController
{
    public function __construct(
        private UserSessionService $sessionService
    ) {}

    /**
     * Desbloquear usuario
     */
    #[Route('/{id}/desbloquear', name: 'unlock', methods: ['POST'])]
    public function unlock(
        Request $request,
        #[MapEntity] UsuariosRebsol $user
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('unlock-user-' . $user->getId(), $request->request->get('_token'))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Token CSRF inválido'
            ], 400);
        }

        try {
            $this->sessionService->unlockUser($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Usuario desbloqueado exitosamente'
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

## 3️⃣ UserExportController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Repository\UsuariosRebsolRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios', name: 'admin_user_')]
class UserExportController extends AbstractTenantAwareController
{
    public function __construct(
        private UsuariosRebsolRepository $userRepository
    ) {}

    /**
     * Exportar listado a Excel
     */
    #[Route('/exportar', name: 'export', methods: ['GET'])]
    public function export(): StreamedResponse
    {
        $tenant = $this->getTenant();
        $users = $this->userRepository->findAllUsersWithDetails($tenant);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Usuario');
        $sheet->setCellValue('C1', 'Nombre Completo');
        $sheet->setCellValue('D1', 'RUT');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Rol');
        $sheet->setCellValue('G1', 'Estado');
        $sheet->setCellValue('H1', 'Último Login');

        // Data
        $row = 2;
        foreach ($users as $user) {
            $pnatural = $user->getIdPersona()->getPnatural();
            
            $sheet->setCellValue('A' . $row, $user->getId());
            $sheet->setCellValue('B' . $row, $user->getNombreUsuario());
            $sheet->setCellValue('C' . $row, $pnatural->getNombreCompleto());
            $sheet->setCellValue('D' . $row, $pnatural->getIdentificacion());
            $sheet->setCellValue('E' . $row, $user->getIdPersona()->getCorreoElectronico());
            $sheet->setCellValue('F' . $row, $user->getIdRol()->getNombre());
            $sheet->setCellValue('G' . $row, $user->getIdEstadoUsuario()->getNombre());
            $sheet->setCellValue('H' . $row, $user->getUltimoLogin()?->format('Y-m-d H:i'));
            
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = sprintf('usuarios_%s_%s.xlsx', $tenant->getSubdomain(), date('Ymd_His'));
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
```

---

## 4️⃣ UserZoomController.php

```php
<?php

namespace App\Controller\Admin\User;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Main\UsuariosRebsol;
use App\Service\User\ZoomIntegrationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin/usuarios/{id}/zoom', name: 'admin_user_zoom_')]
class UserZoomController extends AbstractTenantAwareController
{
    public function __construct(
        private ZoomIntegrationService $zoomService
    ) {}

    /**
     * Vincular usuario con Zoom
     */
    #[Route('/vincular', name: 'link', methods: ['POST'])]
    public function link(
        #[MapEntity] UsuariosRebsol $user
    ): JsonResponse {
        try {
            $result = $this->zoomService->linkUser($user);

            return new JsonResponse([
                'success' => true,
                'message' => $result['message'],
                'status' => $result['status']
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar estado de Zoom
     */
    #[Route('/verificar', name: 'verify', methods: ['GET'])]
    public function verify(
        #[MapEntity] UsuariosRebsol $user
    ): JsonResponse {
        try {
            $status = $this->zoomService->checkUserStatus($user);

            return new JsonResponse([
                'success' => true,
                'status' => $status
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

## 🔌 Controladores AJAX

### Ajax/GroupProfileController.php

```php
<?php

namespace App\Controller\Admin\User\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Repository\PerfilRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios/ajax', name: 'admin_user_ajax_')]
class GroupProfileController extends AbstractTenantAwareController
{
    public function __construct(
        private PerfilRepository $perfilRepository
    ) {}

    /**
     * Obtener perfiles por grupo
     */
    #[Route('/grupo/{groupId}/perfiles', name: 'group_profiles', methods: ['GET'])]
    public function getProfilesByGroup(int $groupId): JsonResponse
    {
        $profiles = $this->perfilRepository->findByGroup($groupId);

        return new JsonResponse([
            'success' => true,
            'profiles' => array_map(fn($p) => [
                'id' => $p->getId(),
                'nombre' => $p->getNombre()
            ], $profiles)
        ]);
    }
}
```

---

### Ajax/UnitBranchController.php

```php
<?php

namespace App\Controller\Admin\User\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Repository\UnidadRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios/ajax', name: 'admin_user_ajax_')]
class UnitBranchController extends AbstractTenantAwareController
{
    public function __construct(
        private UnidadRepository $unidadRepository
    ) {}

    /**
     * Obtener unidades por sucursal
     */
    #[Route('/sucursal/{branchId}/unidades', name: 'branch_units', methods: ['GET'])]
    public function getUnitsByBranch(int $branchId): JsonResponse
    {
        $units = $this->unidadRepository->findByBranch($branchId);

        return new JsonResponse([
            'success' => true,
            'units' => array_map(fn($u) => [
                'id' => $u->getId(),
                'nombre' => $u->getNombre()
            ], $units)
        ]);
    }
}
```

---

### Ajax/ServiceUnitController.php

```php
<?php

namespace App\Controller\Admin\User\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Repository\ServicioRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios/ajax', name: 'admin_user_ajax_')]
class ServiceUnitController extends AbstractTenantAwareController
{
    public function __construct(
        private ServicioRepository $servicioRepository
    ) {}

    /**
     * Obtener servicios por unidad
     */
    #[Route('/unidad/{unitId}/servicios', name: 'unit_services', methods: ['GET'])]
    public function getServicesByUnit(int $unitId): JsonResponse
    {
        $services = $this->servicioRepository->findByUnit($unitId);

        return new JsonResponse([
            'success' => true,
            'services' => array_map(fn($s) => [
                'id' => $s->getId(),
                'nombre' => $s->getNombre()
            ], $services)
        ]);
    }
}
```

---

### Ajax/ValidateRutController.php

```php
<?php

namespace App\Controller\Admin\User\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Repository\PnaturalRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios/ajax', name: 'admin_user_ajax_')]
class ValidateRutController extends AbstractTenantAwareController
{
    public function __construct(
        private PnaturalRepository $pnaturalRepository
    ) {}

    /**
     * Validar RUT único
     */
    #[Route('/validar-rut', name: 'validate_rut', methods: ['POST'])]
    public function validateRut(Request $request): JsonResponse
    {
        $rut = $request->request->get('rut');
        $excludeId = $request->request->get('exclude_id');

        $exists = $this->pnaturalRepository->rutExists($rut, $excludeId);

        return new JsonResponse([
            'success' => true,
            'available' => !$exists,
            'message' => $exists ? 'RUT ya registrado' : 'RUT disponible'
        ]);
    }
}
```

---

### Ajax/ValidateUsernameController.php

```php
<?php

namespace App\Controller\Admin\User\Ajax;

use App\Controller\AbstractTenantAwareController;
use App\Repository\UsuariosRebsolRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios/ajax', name: 'admin_user_ajax_')]
class ValidateUsernameController extends AbstractTenantAwareController
{
    public function __construct(
        private UsuariosRebsolRepository $userRepository
    ) {}

    /**
     * Validar username único
     */
    #[Route('/validar-username', name: 'validate_username', methods: ['POST'])]
    public function validateUsername(Request $request): JsonResponse
    {
        $username = $request->request->get('username');
        $excludeId = $request->request->get('exclude_id');

        $exists = $this->userRepository->usernameExists($username, $excludeId);

        return new JsonResponse([
            'success' => true,
            'available' => !$exists,
            'message' => $exists ? 'Usuario ya existe' : 'Usuario disponible'
        ]);
    }
}
```

---

## ⏱️ Tiempo Estimado

- **UserGroupController:** 1 día
- **Unlock, Export, Zoom:** 1 día
- **Controladores AJAX:** 1 día
- **Testing:** 1 día
- **Total:** **4 días**

---

## ➡️ Siguiente Paso

[05 - Formularios y Repositorios](./MIGRACION-05-FORMULARIOS-REPOSITORIOS.md)

---

**Fase:** 4B de 10  
**Prioridad:** 🟡 Media  
**Riesgo:** 🟢 Bajo
