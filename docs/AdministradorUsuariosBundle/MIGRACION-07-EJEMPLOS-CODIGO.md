# 💡 Fase 7: Ejemplos de Código Completos

## 🎯 Objetivo
Proporcionar ejemplos completos de código migrado del módulo de Administración de Usuarios.

---

## 📦 Ejemplo Completo: Servicio UserManagementService

```php
<?php
// src/Service/User/UserManagementService.php

namespace App\Service\User;

use App\Entity\Main\UsuariosRebsol;
use App\Entity\Main\Persona;
use App\Entity\Main\Pnatural;
use App\Entity\Main\RelUsuarioServicio;
use App\Entity\Main\RelUsuarioGrupo;
use App\Entity\Main\RelUsuarioPerfil;
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
     * Crea un nuevo usuario completo
     */
    public function createUser(array $data, bool $isProfessional = false): UsuariosRebsol
    {
        $this->logger->info('Iniciando creación de usuario', [
            'username' => $data['nombreUsuario'] ?? 'N/A',
            'isProfessional' => $isProfessional
        ]);

        // Validar licencias
        if (!$this->licenseValidator->hasAvailableLicenses()) {
            throw new \RuntimeException('No hay licencias disponibles');
        }

        $this->em->beginTransaction();
        
        try {
            // 1. Crear Persona
            $persona = new Persona();
            $persona->setTelefonoFijo($data['telefonoFijo'] ?? null);
            $persona->setTelefonoMovil($data['telefonoMovil']);
            $persona->setCorreoElectronico($data['correoElectronico']);
            $persona->setCorreoElectronico2($data['correoElectronico2'] ?? null);
            $persona->setIdEmpresa($data['empresa']);
            $persona->setAuditoria(new \DateTime());
            
            $this->em->persist($persona);
            
            // 2. Crear Pnatural
            $pnatural = new Pnatural();
            $pnatural->setIdPersona($persona);
            $pnatural->setNombre($data['nombre']);
            $pnatural->setApellidoPaterno($data['apellidoPaterno']);
            $pnatural->setApellidoMaterno($data['apellidoMaterno'] ?? null);
            $pnatural->setIdSexo($data['sexo']);
            $pnatural->setFechaNacimiento($data['fechaNacimiento']);
            $pnatural->setIdentificacion($data['identificacion']);
            $pnatural->setIdTipoIdentificacion($data['tipoIdentificacion'] ?? null);
            
            $this->em->persist($pnatural);
            
            // 3. Crear UsuariosRebsol
            $usuario = new UsuariosRebsol();
            $usuario->setIdPersona($persona);
            $usuario->setNombreUsuario($data['nombreUsuario']);
            
            // Hash contraseña
            $hashedPassword = $this->passwordHasher->hashPassword(
                $usuario,
                $data['password']
            );
            $usuario->setContrasena($hashedPassword);
            
            // Estado activo por defecto
            $estadoActivo = $this->em->getRepository('App\Entity\Main\EstadoUsuarios')
                ->findOneBy(['nombre' => 'ACTIVO']);
            $usuario->setIdEstadoUsuario($estadoActivo);
            
            $usuario->setIdRol($data['rol']);
            $usuario->setIdCargo($data['cargo'] ?? null);
            $usuario->setIdTipoMedico($data['tipoMedico'] ?? null);
            $usuario->setAuditoria(new \DateTime());
            
            $this->em->persist($usuario);
            $this->em->flush();
            
            // 4. Guardar historial de contraseña
            $this->passwordManager->savePasswordHistory($usuario, $data['password']);
            
            // 5. Asignar especialidades (solo profesionales)
            if ($isProfessional && !empty($data['especialidades'])) {
                $this->specialtyService->assignSpecialties(
                    $usuario,
                    $data['especialidades']
                );
            }
            
            // 6. Asignar servicio principal
            if (!empty($data['servicio'])) {
                $this->assignService($usuario, $data['servicio'], true);
            }
            
            // 7. Asignar grupos
            if (!empty($data['grupos'])) {
                $this->assignGroups($usuario, $data['grupos']);
            }
            
            // 8. Asignar perfiles
            if (!empty($data['perfiles'])) {
                $this->assignProfiles($usuario, $data['perfiles']);
            }
            
            $this->em->flush();
            $this->em->commit();
            
            $this->logger->info('Usuario creado exitosamente', [
                'userId' => $usuario->getId()
            ]);
            
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
     * Asigna un servicio al usuario
     */
    private function assignService(UsuariosRebsol $usuario, $servicio, bool $isActive = false): void
    {
        $estadoActivo = $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'ACTIVO']);
        $estadoInactivo = $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'INACTIVO']);
        
        // Si es activo, inactivar otros servicios
        if ($isActive) {
            $existingServices = $this->em->getRepository(RelUsuarioServicio::class)
                ->findBy(['idUsuario' => $usuario]);
            
            foreach ($existingServices as $rel) {
                $rel->setIdEstado($estadoInactivo);
            }
        }
        
        $rel = new RelUsuarioServicio();
        $rel->setIdUsuario($usuario);
        $rel->setIdServicio($servicio);
        $rel->setIdEstado($isActive ? $estadoActivo : $estadoInactivo);
        
        $this->em->persist($rel);
    }

    /**
     * Asigna grupos al usuario
     */
    private function assignGroups(UsuariosRebsol $usuario, array $grupos): void
    {
        $estadoActivo = $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'ACTIVO']);
        
        foreach ($grupos as $grupo) {
            $rel = new RelUsuarioGrupo();
            $rel->setIdUsuario($usuario);
            $rel->setIdGrupo($grupo);
            $rel->setIdEstado($estadoActivo);
            
            $this->em->persist($rel);
        }
    }

    /**
     * Asigna perfiles al usuario
     */
    private function assignProfiles(UsuariosRebsol $usuario, array $perfiles): void
    {
        $estadoActivo = $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'ACTIVO']);
        
        foreach ($perfiles as $perfil) {
            $rel = new RelUsuarioPerfil();
            $rel->setIdUsuario($usuario);
            $rel->setIdPerfil($perfil);
            $rel->setIdEstado($estadoActivo);
            
            $this->em->persist($rel);
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
            // Actualizar datos de persona
            $persona = $usuario->getIdPersona();
            if (isset($data['telefonoMovil'])) {
                $persona->setTelefonoMovil($data['telefonoMovil']);
            }
            if (isset($data['telefonoFijo'])) {
                $persona->setTelefonoFijo($data['telefonoFijo']);
            }
            if (isset($data['correoElectronico']) && 
                $persona->getCorreoElectronico() !== $data['correoElectronico']) {
                $persona->setCorreoElectronico($data['correoElectronico']);
                $cambiosCriticos = true;
            }
            if (isset($data['correoElectronico2'])) {
                $persona->setCorreoElectronico2($data['correoElectronico2']);
            }
            $persona->setAuditoria(new \DateTime());
            
            // Actualizar datos de Pnatural
            $pnatural = $persona->getPnatural();
            if (isset($data['nombre'])) {
                $pnatural->setNombre($data['nombre']);
            }
            if (isset($data['apellidoPaterno'])) {
                $pnatural->setApellidoPaterno($data['apellidoPaterno']);
            }
            if (isset($data['apellidoMaterno'])) {
                $pnatural->setApellidoMaterno($data['apellidoMaterno']);
            }
            if (isset($data['fechaNacimiento'])) {
                $pnatural->setFechaNacimiento($data['fechaNacimiento']);
            }
            
            // Actualizar contraseña si cambió
            if (!empty($data['password'])) {
                $this->passwordManager->updatePassword($usuario, $data['password']);
                $cambiosCriticos = true;
            }
            
            // Actualizar rol (esto es crítico)
            if (isset($data['rol']) && $usuario->getIdRol() !== $data['rol']) {
                $usuario->setIdRol($data['rol']);
                $cambiosCriticos = true;
            }
            
            // Actualizar cargo y tipo médico
            if (isset($data['cargo'])) {
                $usuario->setIdCargo($data['cargo']);
            }
            if (isset($data['tipoMedico'])) {
                $usuario->setIdTipoMedico($data['tipoMedico']);
            }
            
            // Actualizar especialidades
            if (isset($data['especialidades'])) {
                $this->specialtyService->updateSpecialties(
                    $usuario,
                    $data['especialidades']
                );
            }
            
            // Actualizar auditoría
            $usuario->setAuditoria(new \DateTime());
            
            $this->em->flush();
            $this->em->commit();
            
            // Si hubo cambios críticos, cerrar sesión
            if ($cambiosCriticos) {
                $this->sessionService->forceLogout($usuario, 'Perfil actualizado');
            }
            
            $this->logger->info('Usuario actualizado exitosamente', [
                'userId' => $usuario->getId()
            ]);
            
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
     * Inactiva un usuario (eliminación lógica)
     */
    public function deleteUser(UsuariosRebsol $usuario): void
    {
        $this->logger->info('Inactivando usuario', ['userId' => $usuario->getId()]);

        $this->em->beginTransaction();
        
        try {
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
            
            $this->logger->info('Usuario inactivado exitosamente', [
                'userId' => $usuario->getId()
            ]);
            
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
     * Inactiva todas las relaciones de un usuario
     */
    private function inactivateUserRelations(UsuariosRebsol $usuario): void
    {
        $estadoInactivo = $this->em->getRepository('App\Entity\Main\Estado')
            ->findOneBy(['nombre' => 'INACTIVO']);
        
        // Servicios
        $servicios = $this->em->getRepository(RelUsuarioServicio::class)
            ->findBy(['idUsuario' => $usuario]);
        foreach ($servicios as $rel) {
            $rel->setIdEstado($estadoInactivo);
        }
        
        // Grupos
        $grupos = $this->em->getRepository(RelUsuarioGrupo::class)
            ->findBy(['idUsuario' => $usuario]);
        foreach ($grupos as $rel) {
            $rel->setIdEstado($estadoInactivo);
        }
        
        // Perfiles
        $perfiles = $this->em->getRepository(RelUsuarioPerfil::class)
            ->findBy(['idUsuario' => $usuario]);
        foreach ($perfiles as $rel) {
            $rel->setIdEstado($estadoInactivo);
        }
        
        // Especialidades
        if ($usuario->getIdRol()->getProfClinico() == 1) {
            $this->specialtyService->inactivateAllSpecialties($usuario);
        }
    }

    /**
     * Reactiva un usuario
     */
    public function activateUser(UsuariosRebsol $usuario): void
    {
        // Validar licencias
        if (!$this->licenseValidator->hasAvailableLicenses()) {
            throw new \RuntimeException('No hay licencias disponibles');
        }

        $estadoActivo = $this->em->getRepository('App\Entity\Main\EstadoUsuarios')
            ->findOneBy(['nombre' => 'ACTIVO']);
        
        $usuario->setIdEstadoUsuario($estadoActivo);
        $usuario->setAuditoria(new \DateTime());
        
        $this->em->flush();
        
        $this->logger->info('Usuario reactivado', ['userId' => $usuario->getId()]);
    }
}
```

---

## ⏱️ Tiempo Estimado

Este archivo es de referencia, no requiere implementación adicional.

---

## ➡️ Siguiente Paso

[08 - Checklist y Conclusiones](./MIGRACION-08-CHECKLIST-CONCLUSIONES.md)

---

**Fase:** 7 de 10  
**Prioridad:** 🟡 Media - Referencia  
**Riesgo:** 🟢 Bajo
