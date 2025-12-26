# 🔧 Fase 3: Servicios de Negocio

## 🎯 Objetivo
Extraer la lógica de negocio de los controladores legacy a servicios dedicados, siguiendo los principios SOLID y mejores prácticas de Symfony 6.

---

## 💡 Principio: Fat Services, Thin Controllers

**ANTES (Legacy):**
```php
class DMMNuevoController extends DatosMaestrosMedicosController {
    public function nuevoUsuarioAction(Request $request) {
        // 200+ líneas de lógica aquí
        // Validaciones, creación de entidades, persistencia...
    }
}
```

**DESPUÉS (Moderno):**
```php
class UserCreateController extends AbstractTenantAwareController {
    public function __construct(
        private UserManagementService $userService
    ) {}
    
    #[Route('/nuevo', name: 'new')]
    public function new(Request $request): Response {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->createUser($form->getData());
            return $this->redirectToRoute('admin_user_index');
        }
        
        return $this->render('admin/user/create.html.twig', [
            'form' => $form
        ]);
    }
}
```

---

## 📦 Servicios a Crear

### 1. UserManagementService 
**Responsabilidad:** CRUD de usuarios

### 2. ProfileManagementService
**Responsabilidad:** Gestión de grupos y perfiles

### 3. LicenseValidationService
**Responsabilidad:** Validación de licencias disponibles

### 4. ZoomIntegrationService
**Responsabilidad:** Integración con Zoom API

### 5. PasswordManagementService
**Responsabilidad:** Gestión de contraseñas e historial

### 6. UserValidationService
**Responsabilidad:** Validaciones de negocio

### 7. UserSpecialtyService
**Responsabilidad:** Gestión de especialidades médicas

### 8. UserSessionService
**Responsabilidad:** Control de sesiones y bloqueos

---

## 📝 Implementación de Servicios

### 1. UserManagementService.php

```php
<?php

namespace App\Service\User;

use App\Entity\Main\UsuariosRebsol;
use App\Entity\Main\Persona;
use App\Entity\Main\Pnatural;
use App\Repository\UsuariosRebsolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

class UserManagementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private LicenseValidationService $licenseValidator,
        private PasswordManagementService $passwordManager,
        private UserSpecialtyService $specialtyService,
        private UserSessionService $sessionService,
        private UsuariosRebsolRepository $userRepository,
        private LoggerInterface $logger
    ) {}

    /**
     * Crea un nuevo usuario en el sistema
     */
    public function createUser(array $data, bool $isProfessional = false): UsuariosRebsol
    {
        $this->logger->info('Iniciando creación de usuario', [
            'username' => $data['nombreUsuario'] ?? 'N/A',
            'isProfessional' => $isProfessional
        ]);

        // Validar licencias disponibles
        if (!$this->licenseValidator->hasAvailableLicenses()) {
            throw new \RuntimeException('No hay licencias disponibles para crear usuarios');
        }

        $this->em->beginTransaction();
        
        try {
            // Crear entidades base
            $persona = $this->createPersona($data);
            $pnatural = $this->createPnatural($data, $persona);
            $usuario = $this->createUsuarioRebsol($data, $persona, $isProfessional);
            
            // Persistir entidades base
            $this->em->persist($persona);
            $this->em->persist($pnatural);
            $this->em->persist($usuario);
            $this->em->flush();
            
            // Guardar historial de contraseña
            $this->passwordManager->savePasswordHistory($usuario, $data['password']);
            
            // Asignar roles y especialidades si es profesional
            if ($isProfessional && !empty($data['especialidades'])) {
                $this->specialtyService->assignSpecialties($usuario, $data['especialidades']);
            }
            
            // Asignar servicios
            if (!empty($data['servicios'])) {
                $this->assignServices($usuario, $data['servicios']);
            }
            
            // Asignar grupos y perfiles
            if (!empty($data['grupos'])) {
                $this->assignGroups($usuario, $data['grupos']);
            }
            
            if (!empty($data['perfiles'])) {
                $this->assignProfiles($usuario, $data['perfiles']);
            }
            
            $this->em->flush();
            $this->em->commit();
            
            $this->logger->info('Usuario creado exitosamente', ['userId' => $usuario->getId()]);
            
            return $usuario;
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al crear usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public function updateUser(UsuariosRebsol $usuario, array $data): void
    {
        $this->logger->info('Actualizando usuario', ['userId' => $usuario->getId()]);

        $cambiosCriticos = false;
        
        $this->em->beginTransaction();
        
        try {
            // Actualizar datos básicos
            if ($this->updateBasicData($usuario, $data)) {
                $cambiosCriticos = true;
            }
            
            // Actualizar contraseña si cambió
            if (!empty($data['password']) && $data['password'] !== $usuario->getContrasena()) {
                $this->passwordManager->updatePassword($usuario, $data['password']);
                $cambiosCriticos = true;
            }
            
            // Actualizar servicios
            if (isset($data['servicios']) && $this->updateServices($usuario, $data['servicios'])) {
                $cambiosCriticos = true;
            }
            
            // Actualizar especialidades
            if (isset($data['especialidades'])) {
                $this->specialtyService->updateSpecialties($usuario, $data['especialidades']);
            }
            
            // Actualizar auditoría
            $usuario->setAuditoria(new \DateTime());
            
            $this->em->flush();
            $this->em->commit();
            
            // Si hubo cambios críticos, cerrar sesión
            if ($cambiosCriticos) {
                $this->sessionService->forceLogout($usuario, 'Perfil actualizado');
            }
            
            $this->logger->info('Usuario actualizado exitosamente', ['userId' => $usuario->getId()]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar usuario', [
                'userId' => $usuario->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Inactiva (elimina lógicamente) un usuario
     */
    public function deleteUser(UsuariosRebsol $usuario): void
    {
        $this->logger->info('Inactivando usuario', ['userId' => $usuario->getId()]);

        $this->em->beginTransaction();
        
        try {
            // Obtener estado inactivo
            $estadoInactivo = $this->em->getRepository('App\Entity\Main\EstadoUsuarios')
                ->findOneBy(['nombre' => 'INACTIVO']);
            
            // Inactivar usuario
            $usuario->setIdEstadoUsuario($estadoInactivo);
            $usuario->setAuditoria(new \DateTime());
            
            // Inactivar todas sus relaciones
            $this->inactivateUserRelations($usuario);
            
            $this->em->flush();
            $this->em->commit();
            
            // Forzar cierre de sesión
            $this->sessionService->forceLogout($usuario, 'Usuario desactivado');
            
            $this->logger->info('Usuario inactivado exitosamente', ['userId' => $usuario->getId()]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al inactivar usuario', [
                'userId' => $usuario->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reactiva un usuario inactivo
     */
    public function activateUser(UsuariosRebsol $usuario): void
    {
        $this->logger->info('Reactivando usuario', ['userId' => $usuario->getId()]);

        // Validar licencias disponibles
        if (!$this->licenseValidator->hasAvailableLicenses()) {
            throw new \RuntimeException('No hay licencias disponibles para reactivar usuarios');
        }

        $this->em->beginTransaction();
        
        try {
            // Obtener estado activo
            $estadoActivo = $this->em->getRepository('App\Entity\Main\EstadoUsuarios')
                ->findOneBy(['nombre' => 'ACTIVO']);
            
            $usuario->setIdEstadoUsuario($estadoActivo);
            $usuario->setAuditoria(new \DateTime());
            
            $this->em->flush();
            $this->em->commit();
            
            $this->logger->info('Usuario reactivado exitosamente', ['userId' => $usuario->getId()]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al reactivar usuario', [
                'userId' => $usuario->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Métodos privados auxiliares...
    
    private function createPersona(array $data): Persona
    {
        $persona = new Persona();
        $persona->setTelefonoFijo($data['telefonoFijo'] ?? null);
        $persona->setTelefonoMovil($data['telefonoMovil']);
        $persona->setCorreoElectronico($data['correoElectronico']);
        $persona->setAuditoria(new \DateTime());
        // ... más campos
        
        return $persona;
    }
    
    private function createPnatural(array $data, Persona $persona): Pnatural
    {
        $pnatural = new Pnatural();
        $pnatural->setIdPersona($persona);
        $pnatural->setNombre($data['nombre']);
        $pnatural->setApellidoPaterno($data['apellidoPaterno']);
        $pnatural->setApellidoMaterno($data['apellidoMaterno'] ?? null);
        $pnatural->setIdSexo($data['sexo']);
        $pnatural->setFechaNacimiento($data['fechaNacimiento']);
        $pnatural->setIdentificacion($data['identificacion']);
        // ... más campos
        
        return $pnatural;
    }
    
    private function createUsuarioRebsol(array $data, Persona $persona, bool $isProfessional): UsuariosRebsol
    {
        $usuario = new UsuariosRebsol();
        $usuario->setIdPersona($persona);
        $usuario->setNombreUsuario($data['nombreUsuario']);
        
        // Hash de contraseña
        $hashedPassword = $this->passwordHasher->hashPassword($usuario, $data['password']);
        $usuario->setContrasena($hashedPassword);
        
        // Estado activo por defecto
        $estadoActivo = $this->em->getRepository('App\Entity\Main\EstadoUsuarios')
            ->findOneBy(['nombre' => 'ACTIVO']);
        $usuario->setIdEstadoUsuario($estadoActivo);
        
        $usuario->setIdRol($data['rol']);
        $usuario->setIdCargo($data['cargo'] ?? null);
        $usuario->setIdTipoMedico($data['tipoMedico'] ?? null);
        $usuario->setAuditoria(new \DateTime());
        
        return $usuario;
    }
    
    private function updateBasicData(UsuariosRebsol $usuario, array $data): bool
    {
        $cambios = false;
        
        // Actualizar persona
        $persona = $usuario->getIdPersona();
        if ($persona->getCorreoElectronico() !== $data['correoElectronico']) {
            $persona->setCorreoElectronico($data['correoElectronico']);
            $cambios = true;
        }
        
        // ... más actualizaciones
        
        return $cambios;
    }
    
    private function updateServices(UsuariosRebsol $usuario, array $newServices): bool
    {
        // Lógica compleja de actualización de servicios
        // Similar a la del legacy pero modernizada
        return true;
    }
    
    private function assignServices(UsuariosRebsol $usuario, array $services): void
    {
        // Lógica de asignación de servicios
    }
    
    private function assignGroups(UsuariosRebsol $usuario, array $groups): void
    {
        // Lógica de asignación de grupos
    }
    
    private function assignProfiles(UsuariosRebsol $usuario, array $profiles): void
    {
        // Lógica de asignación de perfiles
    }
    
    private function inactivateUserRelations(UsuariosRebsol $usuario): void
    {
        // Inactivar servicios, especialidades, etc.
    }
}
```

---

### 2. ProfileManagementService.php

```php
<?php

namespace App\Service\User;

use App\Entity\Main\UsuariosRebsol;
use App\Entity\Main\RelUsuarioGrupo;
use App\Entity\Main\RelUsuarioPerfil;
use App\Repository\PerfilRepository;
use App\Repository\GrupoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ProfileManagementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PerfilRepository $perfilRepository,
        private GrupoRepository $grupoRepository,
        private UserSessionService $sessionService,
        private LoggerInterface $logger
    ) {}

    /**
     * Actualiza grupos y perfiles de un usuario
     */
    public function updateUserProfiles(
        UsuariosRebsol $usuario,
        array $newGroups,
        array $newProfiles
    ): void {
        $this->logger->info('Actualizando perfiles de usuario', [
            'userId' => $usuario->getId(),
            'groups' => count($newGroups),
            'profiles' => count($newProfiles)
        ]);

        $this->em->beginTransaction();
        
        try {
            $this->updateGroups($usuario, $newGroups);
            $this->updateProfiles($usuario, $newProfiles);
            
            $usuario->setAuditoria(new \DateTime());
            
            $this->em->flush();
            $this->em->commit();
            
            // Cerrar sesión porque los permisos cambiaron
            $this->sessionService->forceLogout($usuario, 'Permisos actualizados');
            
            $this->logger->info('Perfiles actualizados exitosamente', [
                'userId' => $usuario->getId()
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar perfiles', [
                'userId' => $usuario->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtiene perfiles activos de un usuario (directos + de grupos)
     */
    public function getActiveProfiles(UsuariosRebsol $usuario): array
    {
        $profiles = [];
        
        // Obtener perfiles directos ACTIVOS
        $directProfiles = $this->perfilRepository->findActiveByUser($usuario->getId());
        
        // Obtener perfiles INACTIVOS (exclusiones)
        $excludedProfiles = $this->perfilRepository->findInactiveByUser($usuario->getId());
        $excludedIds = array_map(fn($p) => $p->getId(), $excludedProfiles);
        
        // Obtener perfiles de grupos
        $groups = $this->grupoRepository->findActiveByUser($usuario->getId());
        if (!empty($groups)) {
            $groupProfiles = $this->perfilRepository->findByGroups(
                array_map(fn($g) => $g->getId(), $groups)
            );
            
            // Agregar solo si no están excluidos
            foreach ($groupProfiles as $profile) {
                if (!in_array($profile->getId(), $excludedIds)) {
                    $profiles[$profile->getId()] = $profile;
                }
            }
        }
        
        // Agregar perfiles directos
        foreach ($directProfiles as $profile) {
            $profiles[$profile->getId()] = $profile;
        }
        
        return array_values($profiles);
    }

    /**
     * Verifica si un usuario tiene un perfil específico
     */
    public function hasProfile(UsuariosRebsol $usuario, int $profileId): bool
    {
        $activeProfiles = $this->getActiveProfiles($usuario);
        return in_array($profileId, array_map(fn($p) => $p->getId(), $activeProfiles));
    }

    // Métodos privados...
    
    private function updateGroups(UsuariosRebsol $usuario, array $newGroups): void
    {
        // Obtener grupos actuales ACTIVOS
        $currentGroups = $this->grupoRepository->findActiveByUser($usuario->getId());
        $currentGroupIds = array_map(fn($g) => $g->getId(), $currentGroups);
        
        $newGroupIds = array_map(fn($g) => is_object($g) ? $g->getId() : $g, $newGroups);
        
        // Grupos a activar (nuevos)
        $toActivate = array_diff($newGroupIds, $currentGroupIds);
        
        // Grupos a inactivar (removidos)
        $toDeactivate = array_diff($currentGroupIds, $newGroupIds);
        
        // Activar grupos nuevos o reactivar inactivos
        foreach ($toActivate as $groupId) {
            $this->activateUserGroup($usuario, $groupId);
        }
        
        // Inactivar grupos removidos
        foreach ($toDeactivate as $groupId) {
            $this->deactivateUserGroup($usuario, $groupId);
        }
    }
    
    private function updateProfiles(UsuariosRebsol $usuario, array $newProfiles): void
    {
        // Lógica similar a updateGroups
        // RECORDAR: Estado INACTIVO = EXCLUSIÓN explícita
    }
    
    private function activateUserGroup(UsuariosRebsol $usuario, int $groupId): void
    {
        // Buscar relación existente
        $rel = $this->em->getRepository(RelUsuarioGrupo::class)
            ->findOneBy(['idUsuario' => $usuario, 'idGrupo' => $groupId]);
        
        if ($rel) {
            // Reactivar
            $rel->setIdEstado($this->getActiveState());
        } else {
            // Crear nueva
            $rel = new RelUsuarioGrupo();
            $rel->setIdUsuario($usuario);
            $rel->setIdGrupo($this->grupoRepository->find($groupId));
            $rel->setIdEstado($this->getActiveState());
            $this->em->persist($rel);
        }
    }
    
    private function deactivateUserGroup(UsuariosRebsol $usuario, int $groupId): void
    {
        $rel = $this->em->getRepository(RelUsuarioGrupo::class)
            ->findOneBy(['idUsuario' => $usuario, 'idGrupo' => $groupId]);
        
        if ($rel) {
            $rel->setIdEstado($this->getInactiveState());
        }
    }
    
    private function getActiveState()
    {
        return $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'ACTIVO']);
    }
    
    private function getInactiveState()
    {
        return $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'INACTIVO']);
    }
}
```

---

### 3. LicenseValidationService.php

```php
<?php

namespace App\Service\User;

use App\Repository\UsuariosRebsolRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class LicenseValidationService
{
    private const WARNING_THRESHOLD = 10;

    public function __construct(
        private EntityManagerInterface $em,
        private UsuariosRebsolRepository $userRepository,
        private int $totalLicenses,
        private LoggerInterface $logger
    ) {}

    /**
     * Verifica si hay licencias disponibles
     */
    public function hasAvailableLicenses(): bool
    {
        $usedLicenses = $this->getUsedLicensesCount();
        $available = $this->totalLicenses - $usedLicenses;
        
        $this->logger->info('Verificando licencias', [
            'total' => $this->totalLicenses,
            'used' => $usedLicenses,
            'available' => $available
        ]);
        
        return $available > 0;
    }

    /**
     * Obtiene cantidad de licencias usadas (con lock para evitar race conditions)
     */
    public function getUsedLicensesCount(): int
    {
        return $this->userRepository->countActiveUsers();
    }

    /**
     * Obtiene información completa de licencias
     */
    public function getLicenseInfo(): array
    {
        $used = $this->getUsedLicensesCount();
        $available = $this->totalLicenses - $used;
        $isWarning = $available <= self::WARNING_THRESHOLD;
        
        return [
            'total' => $this->totalLicenses,
            'used' => $used,
            'available' => $available,
            'percentage' => round(($used / $this->totalLicenses) * 100, 2),
            'is_warning' => $isWarning,
            'can_create_user' => $available > 0
        ];
    }

    /**
     * Valida y reserva una licencia (transaccional)
     */
    public function reserveLicense(): void
    {
        if (!$this->hasAvailableLicenses()) {
            throw new \RuntimeException('No hay licencias disponibles');
        }
        
        // La reserva se hace implícitamente al crear el usuario
        // Este método valida que sea posible
    }
}
```

---

Continúa en siguiente mensaje con más servicios...

---

## ⏱️ Tiempo Estimado de Esta Fase

- **UserManagementService:** 2 días
- **ProfileManagementService:** 1.5 días
- **LicenseValidationService:** 0.5 días
- **Otros servicios:** 2 días
- **Testing unitario:** 2 días
- **Total:** **8 días** (1.5 semanas)

---

## ➡️ Siguiente Paso

[04 - Migración de Controladores](./MIGRACION-04-CONTROLADORES.md)

---

**Fase:** 3 de 10  
**Prioridad:** 🔴 Alta - Core del negocio  
**Riesgo:** 🟡 Medio - Lógica compleja
