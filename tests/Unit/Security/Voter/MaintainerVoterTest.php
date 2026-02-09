<?php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Tenant\Gender;
use App\Entity\Tenant\Member;
use App\Repository\Tenant\MaintainerRolePermissionRepository;
use App\Security\Voter\MaintainerVoter;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test Suite para MaintainerVoter
 * 
 * ACTUALIZADO (Sprint 1.5): Tests adaptados para el nuevo sistema basado en DB.
 * Ahora mockeamos el repositorio MaintainerRolePermissionRepository.
 * 
 * Verifica que el sistema de permisos de mantenedores funciona correctamente
 * en todos los escenarios posibles:
 * - ROLE_ADMIN → Acceso completo (wildcard *)
 * - ROLE_MAINTAINER_MANAGER → CRUD completo + Export
 * - ROLE_MAINTAINER_USER → Solo READ
 * - Usuario sin rol específico → Sin acceso
 * - Usuario no autenticado → Sin acceso
 * 
 * @author Melisa Development Team
 * @since Sprint 1.5 - Database-driven permissions (Feb 2026)
 */
class MaintainerVoterTest extends TestCase
{
    // ===== PROPIEDADES DE LA CLASE =====
    
    /** @var MaintainerVoter - El voter REAL que vamos a probar */
    private MaintainerVoter $voter;
    
    /** @var Member&MockObject - Mock del usuario autenticado */
    private Member&MockObject $user;
    
    /** @var TokenInterface&MockObject - Mock del token de seguridad */
    private TokenInterface&MockObject $token;
    
    /** @var MaintainerRolePermissionRepository&MockObject - Mock del repositorio */
    private MaintainerRolePermissionRepository&MockObject $repository;

    /**
     * setUp() se ejecuta ANTES de cada test
     * 
     * Preparamos el voter, mocks y configuramos respuestas del repositorio.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear mock del repositorio
        $this->repository = $this->createMock(MaintainerRolePermissionRepository::class);
        
        // Crear el voter real con el repositorio mockeado
        $this->voter = new MaintainerVoter($this->repository);
        
        // Crear mocks del usuario y token
        $this->user = $this->createMock(Member::class);
        $this->token = $this->createMock(TokenInterface::class);
        
        // Configurar el token para que devuelva nuestro usuario
        $this->token->expects($this->any())
            ->method('getUser')
            ->willReturn($this->user);
    }
    
    /**
     * Configura el mock del repositorio para simular permisos de ROLE_ADMIN
     * ROLE_ADMIN tiene wildcard (*) = acceso completo
     */
    private function mockAdminPermissions(): void
    {
        $this->repository->expects($this->any())
            ->method('hasPermission')
            ->willReturn(true); // ROLE_ADMIN siempre retorna true (wildcard)
    }
    
    /**
     * Configura el mock del repositorio para simular permisos de ROLE_MAINTAINER_MANAGER
     * Tiene todos los permisos: CREATE, READ, UPDATE, DELETE, EXPORT
     */
    private function mockManagerPermissions(): void
    {
        $this->repository->expects($this->any())
            ->method('hasPermission')
            ->willReturnCallback(function($role, $permission) {
                return in_array($permission, [
                    MaintainerVoter::CREATE,
                    MaintainerVoter::READ,
                    MaintainerVoter::UPDATE,
                    MaintainerVoter::DELETE,
                    MaintainerVoter::EXPORT,
                ]);
            });
    }
    
    /**
     * Configura el mock del repositorio para simular permisos de ROLE_MAINTAINER_USER
     * Solo tiene READ
     */
    private function mockUserPermissions(): void
    {
        $this->repository->expects($this->any())
            ->method('hasPermission')
            ->willReturnCallback(function($role, $permission) {
                return $permission === MaintainerVoter::READ;
            });
    }
    
    /**
     * Configura el mock del repositorio para simular usuario sin permisos
     */
    private function mockNoPermissions(): void
    {
        $this->repository->expects($this->any())
            ->method('hasPermission')
            ->willReturn(false);
    }

    // ===== TESTS: ROLE_ADMIN (God mode) =====

    /**
     * ROLE_ADMIN debe tener acceso a CREATE
     */
    public function testAdminCanCreateMaintainer(): void
    {
        // Arrange: Usuario con ROLE_ADMIN
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        // Act: Solicitar permiso CREATE
        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::CREATE]);

        // Assert: El voto debe ser ACCESS_GRANTED
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * ROLE_ADMIN debe tener acceso a READ
     */
    public function testAdminCanReadMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::READ]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * ROLE_ADMIN debe tener acceso a UPDATE
     */
    public function testAdminCanUpdateMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::UPDATE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * ROLE_ADMIN debe tener acceso a DELETE
     */
    public function testAdminCanDeleteMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * ROLE_ADMIN debe tener acceso a EXPORT
     */
    public function testAdminCanExportMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::EXPORT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    // ===== TESTS: ROLE_MAINTAINER_MANAGER (CRUD completo) =====

    /**
     * 
     * ROLE_MAINTAINER_MANAGER debe tener acceso a CREATE
     */
    public function testManagerCanCreateMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_MANAGER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::CREATE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_MANAGER debe tener acceso a READ
     */
    public function testManagerCanReadMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_MANAGER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::READ]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_MANAGER debe tener acceso a UPDATE
     */
    public function testManagerCanUpdateMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_MANAGER']);

        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::UPDATE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_MANAGER debe tener acceso a DELETE
     */
    public function testManagerCanDeleteMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_MANAGER']);

        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_MANAGER debe tener acceso a EXPORT
     */
    public function testManagerCanExportMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_MANAGER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::EXPORT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    // ===== TESTS: ROLE_MAINTAINER_USER (Solo lectura) =====

    /**
     * 
     * ROLE_MAINTAINER_USER NO debe tener acceso a CREATE
     */
    public function testUserCannotCreateMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_USER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::CREATE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_USER SÍ debe tener acceso a READ
     */
    public function testUserCanReadMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_USER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::READ]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_USER NO debe tener acceso a UPDATE
     */
    public function testUserCannotUpdateMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_USER']);

        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::UPDATE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_USER NO debe tener acceso a DELETE
     */
    public function testUserCannotDeleteMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_USER']);

        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * 
     * ROLE_MAINTAINER_USER NO debe tener acceso a EXPORT
     */
    public function testUserCannotExportMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_USER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::EXPORT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    // ===== TESTS: Usuario sin rol específico =====

    /**
     * 
     * Usuario sin rol de mantenedores NO debe tener acceso a CREATE
     */
    public function testRegular_userCannotCreateMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER']); // Solo ROLE_USER básico

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::CREATE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * 
     * Usuario sin rol de mantenedores NO debe tener acceso a READ
     */
    public function testRegular_userCannotReadMaintainer(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER']);

        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::READ]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    // ===== TESTS: Usuario no autenticado =====

    /**
     * 
     * Usuario no autenticado NO debe tener ningún acceso
     */
    public function testUnauthenticated_userHasNo_access(): void
    {
        // Token que devuelve null como usuario (no autenticado)
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn(null);

        $result = $this->voter->vote($token, Gender::class, [MaintainerVoter::READ]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * 
     * Usuario que no es instancia de Member NO debe tener acceso
     */
    public function testUserNotInstanceOfMemberHasNoAccess(): void
    {
        // Token que devuelve un mock que no es Member
        $token = $this->createMock(TokenInterface::class);
        $notMemberUser = $this->createMock(\Symfony\Component\Security\Core\User\UserInterface::class);
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($notMemberUser);

        $result = $this->voter->vote($token, Gender::class, [MaintainerVoter::READ]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    // ===== TESTS: Atributos no soportados =====

    /**
     * 
     * El voter debe abstenerse para atributos no soportados
     */
    public function testVoterAbstainsFor_unsupported_attribute(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_ADMIN']);

        // Atributo inventado que no existe
        $result = $this->voter->vote($this->token, Gender::class, ['INVALID_PERMISSION']);

        // El voter debe ABSTENERSE (no votar ni conceder ni denegar)
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    // ===== TESTS: Jerarquía de roles =====

    /**
     * 
     * ROLE_ADMIN tiene prioridad sobre otros roles
     */
    public function testAdminRoleTakesPrecedence(): void
    {
        // Usuario con múltiples roles, incluyendo ADMIN
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_USER', 'ROLE_MAINTAINER_USER', 'ROLE_ADMIN']);

        // ROLE_ADMIN debe ganar, permitiendo DELETE
        $gender = new Gender();
        $result = $this->voter->vote($this->token, $gender, [MaintainerVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * 
     * Verificar que todos los permisos funcionan para ROLE_ADMIN
     */
    public function testAdminHasAll_permissions(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_ADMIN']);

        $allPermissions = [
            MaintainerVoter::CREATE,
            MaintainerVoter::READ,
            MaintainerVoter::UPDATE,
            MaintainerVoter::DELETE,
            MaintainerVoter::EXPORT,
        ];

        foreach ($allPermissions as $permission) {
            $result = $this->voter->vote($this->token, Gender::class, [$permission]);
            $this->assertEquals(
                VoterInterface::ACCESS_GRANTED, 
                $result, 
                "ROLE_ADMIN debe tener acceso a $permission"
            );
        }
    }

    /**
     * 
     * Verificar matriz completa de permisos para ROLE_MAINTAINER_MANAGER
     */
    public function testManagerHasCrud_and_export_permissions(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_MAINTAINER_MANAGER']);

        $allowedPermissions = [
            MaintainerVoter::CREATE,
            MaintainerVoter::READ,
            MaintainerVoter::UPDATE,
            MaintainerVoter::DELETE,
            MaintainerVoter::EXPORT,
        ];

        foreach ($allowedPermissions as $permission) {
            $result = $this->voter->vote($this->token, Gender::class, [$permission]);
            $this->assertEquals(
                VoterInterface::ACCESS_GRANTED, 
                $result, 
                "ROLE_MAINTAINER_MANAGER debe tener acceso a $permission"
            );
        }
    }

    /**
     * 
     * Verificar que ROLE_MAINTAINER_USER solo tiene READ
     */
    public function testUser_onlyHasRead_permission(): void
    {
        $this->user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_MAINTAINER_USER']);

        // Debe tener READ
        $result = $this->voter->vote($this->token, Gender::class, [MaintainerVoter::READ]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);

        // NO debe tener CREATE, UPDATE, DELETE, EXPORT
        $deniedPermissions = [
            MaintainerVoter::CREATE,
            MaintainerVoter::UPDATE,
            MaintainerVoter::DELETE,
            MaintainerVoter::EXPORT,
        ];

        foreach ($deniedPermissions as $permission) {
            $result = $this->voter->vote($this->token, Gender::class, [$permission]);
            $this->assertEquals(
                VoterInterface::ACCESS_DENIED, 
                $result, 
                "ROLE_MAINTAINER_USER NO debe tener acceso a $permission"
            );
        }
    }
}
