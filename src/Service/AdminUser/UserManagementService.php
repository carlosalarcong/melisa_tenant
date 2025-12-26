<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Organization;
use App\Entity\Tenant\State;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Person;
use App\Entity\Tenant\Role;
use App\Enum\StateEnum;
use App\Enum\UserTypeEnum;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Servicio principal para gestión de usuarios
 * 
 * Responsabilidades:
 * - Crear usuarios (profesionales y administrativos)
 * - Editar información de usuarios
 * - Eliminar usuarios (inactivación lógica)
 * - Activar/reactivar usuarios
 * - Gestión de contraseñas
 */
class UserManagementService
{
    public function __construct(
        private TenantEntityManager $em,
        private UserPasswordHasherInterface $passwordHasher,
        private PasswordManagementService $passwordService,
        private LicenseValidationService $licenseService,
        private UserSessionService $sessionService,
        private LoggerInterface $logger,
        private Security $security
    ) {}

    /**
     * Crear un nuevo usuario en el sistema
     * 
     * @param array $data Datos del usuario
     * @param Organization $tenant Tenant al que pertenece
     * @param UserTypeEnum $userType Tipo de usuario
     * @return Member
     * @throws \RuntimeException Si no hay licencias disponibles
     */
    public function createUser(array $data, Organization $tenant, UserTypeEnum $userType): Member
    {
        // Validar licencias disponibles
        if (!$this->licenseService->hasAvailableLicenses($tenant)) {
            throw new \RuntimeException('No hay licencias disponibles para crear nuevos usuarios');
        }

        $this->em->beginTransaction();
        
        try {
            // 1. Crear Person (combina Persona + Pnatural de melisa_prod)
            $person = $this->createPerson($data, $tenant);
            $this->em->persist($person);
            
            // 2. Crear Member
            $member = $this->createMember($data, $person, $userType);
            $this->em->persist($member);
            
            // 3. Hash de contraseña y guardar en historial
            if (isset($data['password'])) {
                $hashedPassword = $this->passwordHasher->hashPassword($member, $data['password']);
                $member->setPassword($hashedPassword);
                $this->passwordService->savePasswordHistory($member, $hashedPassword);
            }
            
            // 4. Flush
            $this->em->flush();
            $this->em->commit();
            
            $this->logger->info('Usuario creado exitosamente', [
                'memberId' => $member->getId(),
                'username' => $member->getUserIdentifier(),
                'userType' => $userType->name,
                'createdBy' => $this->security->getUser()?->getUserIdentifier()
            ]);
            
            return $member;
            
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
     * Actualizar un usuario existente
     * 
     * @param Member $member Usuario a actualizar
     * @param array $data Nuevos datos
     * @return Member
     */
    public function updateUser(Member $member, array $data): Member
    {
        $this->em->beginTransaction();
        
        try {
            $passwordChanged = false;
            $permissionsChanged = false;
            
            // Actualizar Person (combina Persona + Pnatural)
            $person = $member->getPerson();
            if (isset($data['email'])) {
                $person->setEmail($data['email']);
            }
            if (isset($data['mobilePhone'])) {
                $person->setMobilePhone($data['mobilePhone']);
            }
            if (isset($data['name'])) {
                $person->setName($data['name']);
            }
            if (isset($data['lastName'])) {
                $person->setLastName($data['lastName']);
            }
            if (isset($data['middleName'])) {
                $person->setMiddleName($data['middleName']);
            }
            if (isset($data['birthDateAt'])) {
                if ($data['birthDateAt'] instanceof \DateTimeImmutable) {
                    $person->setBirthDateAt($data['birthDateAt']);
                } elseif ($data['birthDateAt'] instanceof \DateTime) {
                    $person->setBirthDateAt(\DateTimeImmutable::createFromMutable($data['birthDateAt']));
                } elseif (is_string($data['birthDateAt'])) {
                    $person->setBirthDateAt(new \DateTimeImmutable($data['birthDateAt']));
                }
            }
            
            // Actualizar Member
            if (isset($data['username'])) {
                $member->setUsername($data['username']);
            }
            
            // Cambio de contraseña
            if (isset($data['password']) && !empty($data['password'])) {
                $hashedPassword = $this->passwordHasher->hashPassword($member, $data['password']);
                $member->setPassword($hashedPassword);
                $member->setPasswordChangedAt(new \DateTimeImmutable());
                $this->passwordService->savePasswordHistory($member, $hashedPassword);
                $passwordChanged = true;
            }
            
            // Cambio de rol (implica cambio de permisos)
            if (isset($data['role']) && $data['role'] !== $member->getRole()) {
                $member->setRole($data['role']);
                $permissionsChanged = true;
            }
            
            $member->setUpdatedAt(new \DateTimeImmutable());
            
            $this->em->flush();
            $this->em->commit();
            
            // Forzar logout si cambió contraseña o permisos
            if ($passwordChanged || $permissionsChanged) {
                $this->sessionService->forceLogout($member);
            }
            
            $this->logger->info('Usuario actualizado', [
                'memberId' => $member->getId(),
                'passwordChanged' => $passwordChanged,
                'permissionsChanged' => $permissionsChanged,
                'updatedBy' => $this->security->getUser()?->getUserIdentifier()
            ]);
            
            return $member;
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar usuario', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar (inactivar) un usuario
     * Eliminación lógica, no física
     * 
     * @param Member $member Usuario a eliminar
     */
    public function deleteUser(Member $member): void
    {
        $this->em->beginTransaction();
        
        try {
            $inactiveState = $this->em->getRepository(State::class)
                ->findOneBy(['name' => 'INACTIVE']);
            
            if (!$inactiveState) {
                throw new \RuntimeException('Estado INACTIVE no encontrado en base de datos');
            }
            
            $member->setState($inactiveState);
            $member->setUpdatedAt(new \DateTimeImmutable());
            
            // Forzar logout
            $this->sessionService->forceLogout($member);
            
            $this->em->flush();
            $this->em->commit();
            
            $this->logger->info('Usuario inactivado', [
                'memberId' => $member->getId(),
                'username' => $member->getUserIdentifier(),
                'deletedBy' => $this->security->getUser()?->getUserIdentifier()
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al inactivar usuario', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Activar (reactivar) un usuario inactivo
     * 
     * @param Member $member Usuario a activar
     */
    public function activateUser(Member $member): void
    {
        $this->em->beginTransaction();
        
        try {
            // Verificar licencias disponibles
            $tenant = $member->getPerson()->getOrganization();
            if (!$tenant) {
                throw new \RuntimeException('Usuario no tiene organización asociada');
            }
            
            if (!$this->licenseService->hasAvailableLicenses($tenant)) {
                throw new \RuntimeException('No hay licencias disponibles para activar el usuario');
            }
            
            $activeState = $this->em->getRepository(State::class)
                ->findOneBy(['name' => 'ACTIVE']);
            
            if (!$activeState) {
                throw new \RuntimeException('Estado ACTIVE no encontrado en base de datos');
            }
            
            $member->setState($activeState);
            $member->setUpdatedAt(new \DateTimeImmutable());
            
            $this->em->flush();
            $this->em->commit();
            
            $this->logger->info('Usuario activado', [
                'memberId' => $member->getId(),
                'username' => $member->getUserIdentifier(),
                'activatedBy' => $this->security->getUser()?->getUserIdentifier()
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al activar usuario', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Crear entidad Person (combina Persona + Pnatural de melisa_prod)
     */
    private function createPerson(array $data, Organization $tenant): Person
    {
        $person = new Person();
        
        // Campos básicos de identificación
        $person->setIdentification($data['identification']); // RUT
        $person->setName($data['name']);
        $person->setLastName($data['lastName']);
        $person->setMiddleName($data['middleName'] ?? null);
        
        // Fecha de nacimiento
        if (isset($data['birthDateAt'])) {
            if ($data['birthDateAt'] instanceof \DateTimeImmutable) {
                $person->setBirthDateAt($data['birthDateAt']);
            } elseif ($data['birthDateAt'] instanceof \DateTime) {
                $person->setBirthDateAt(\DateTimeImmutable::createFromMutable($data['birthDateAt']));
            } elseif (is_string($data['birthDateAt'])) {
                $person->setBirthDateAt(new \DateTimeImmutable($data['birthDateAt']));
            }
        }
        
        // Contacto
        $person->setEmail($data['email'] ?? null);
        $person->setMobilePhone($data['mobilePhone'] ?? null);
        $person->setHomePhone($data['homePhone'] ?? null);
        $person->setWorkPhone($data['workPhone'] ?? null);
        
        // Relaciones opcionales
        if (isset($data['gender'])) {
            $person->setGender($data['gender']);
        }
        if (isset($data['identificationType'])) {
            $person->setIdentificationType($data['identificationType']);
        }
        
        // Relación con Organization
        $person->setOrganization($tenant);
        
        return $person;
    }

    /**
     * Crear entidad Member
     */
    private function createMember(array $data, Person $person, UserTypeEnum $userType): Member
    {
        $member = new Member();
        $member->setPerson($person);
        
        if (isset($data['username'])) {
            $member->setUsername($data['username']);
        }
        
        // Estado activo por defecto
        $activeState = $this->em->getRepository(State::class)
            ->findOneBy(['name' => 'ACTIVE']);
        if ($activeState) {
            $member->setState($activeState);
        }
        
        // Rol
        if (isset($data['role'])) {
            $member->setRole($data['role']);
        }
        
        // Tipo de usuario (0 = Profesional, 1 = Administrativo)
        $member->setUserType($userType->value);
        
        // Inicializar campos de seguridad
        $member->setLoginAttempts(0);
        $member->setPasswordChangedAt(new \DateTimeImmutable());
        
        return $member;
    }
}
