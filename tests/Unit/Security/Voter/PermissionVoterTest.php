<?php

namespace App\Tests\Unit\Security\Voter;

// Importamos todas las clases que vamos a usar
use App\Entity\Tenant\GroupPermission;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\MemberGroup;
use App\Entity\Tenant\Permission;
use App\Repository\Tenant\GroupPermissionRepository;
use App\Repository\Tenant\PermissionRepository;
use App\Security\FieldAccess;
use App\Security\SecuredResourceInterface;
use App\Security\Voter\PermissionVoter;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Test Suite para PermissionVoter
 * 
 * Este archivo contiene 9 tests que verifican que el sistema de permisos
 * funciona correctamente en diferentes escenarios.
 */
class PermissionVoterTest extends TestCase
{
    // ===== PROPIEDADES DE LA CLASE =====
    // Estos objetos estarán disponibles en todos los tests
    
    /** @var PermissionRepository - Mock del repositorio de permisos de usuario */
    private PermissionRepository $permissionRepository;
    
    /** @var GroupPermissionRepository - Mock del repositorio de permisos de grupo */
    private GroupPermissionRepository $groupPermissionRepository;
    
    /** @var PermissionVoter - El voter REAL que vamos a probar */
    private PermissionVoter $voter;
    
    /** @var Member&MockObject - Mock del usuario autenticado */
    private Member&MockObject $user;
    
    /** @var TokenInterface&MockObject - Mock del token de seguridad */
    private TokenInterface&MockObject $token;

    /**
     * setUp() se ejecuta ANTES de cada test
     * 
     * Aquí preparamos todos los objetos que necesitaremos en los tests.
     * Es como un "escenario base" que se resetea antes de cada test.
     */
    protected function setUp(): void
    {
        // 1. Crear MOCK del repositorio de permisos (NO consulta BD real)
        $this->permissionRepository = $this->createMock(PermissionRepository::class);
        
        // 2. Crear MOCK del repositorio de permisos de grupo
        $this->groupPermissionRepository = $this->createMock(GroupPermissionRepository::class);
        
        // 3. Crear el VOTER REAL (esto SÍ es real, no es mock)
        // Le pasamos los mocks de repositorios para que no consulte BD
        $this->voter = new PermissionVoter($this->permissionRepository, $this->groupPermissionRepository);

        // 4. Crear MOCK de Member (usuario)
        // disableOriginalConstructor() = no ejecutar __construct() de Member
        $this->user = $this->getMockBuilder(Member::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        // 5. Crear MOCK del token de autenticación
        $this->token = $this->getMockBuilder(TokenInterface::class)
            ->getMock();
            
        // 6. Configurar que cuando llamen a getUser(), devuelva nuestro mock de user
        $this->token->method('getUser')->willReturn($this->user);
    }

    /**
     * TEST 1: Verifica que el voter SOPORTA los atributos correctos
     * 
     * El voter debe aceptar VIEW, EDIT, DELETE pero rechazar otros atributos inválidos.
     */
    public function testSupportsViewEditDeleteAttributes(): void
    {
        // ARRANGE: Crear un recurso fake
        $resource = $this->createMock(SecuredResourceInterface::class);

        // ACT: Usar Reflection para acceder al método protected 'supports()'
        // (Reflection nos permite llamar métodos privados/protected en tests)
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('supports');

        // ASSERT: Verificar que soporta VIEW, EDIT, DELETE
        $this->assertTrue($method->invoke($this->voter, PermissionVoter::VIEW, $resource));
        $this->assertTrue($method->invoke($this->voter, PermissionVoter::EDIT, $resource));
        $this->assertTrue($method->invoke($this->voter, PermissionVoter::DELETE, $resource));
        
        // ASSERT: NO debe soportar atributos inválidos
        $this->assertFalse($method->invoke($this->voter, 'INVALID_ATTRIBUTE', $resource));
    }

    /**
     * TEST 2: Verifica que el voter SOPORTA recursos que implementan SecuredResourceInterface
     * 
     * Solo debería funcionar con recursos que implementan la interfaz correcta.
     */
    public function testSupportsSecuredResourceInterface(): void
    {
        // ARRANGE: Crear mock de un recurso con la interfaz correcta
        $resource = $this->createMock(SecuredResourceInterface::class);
        
        // ACT: Obtener el método supports()
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('supports');

        // ASSERT: Debe soportar este tipo de recurso
        $this->assertTrue($method->invoke($this->voter, PermissionVoter::VIEW, $resource));
    }

    /**
     * TEST 3: Verifica que el voter SOPORTA FieldAccess (permisos a nivel de campo)
     * 
     * FieldAccess es un wrapper que permite verificar permisos de campos específicos.
     */
    public function testSupportsFieldAccess(): void
    {
        // ARRANGE: Crear un recurso y envolverlo en FieldAccess
        $resource = $this->createMock(SecuredResourceInterface::class);
        $fieldAccess = new FieldAccess($resource, 'diagnosis'); // Campo 'diagnosis'
        
        // ACT: Obtener método supports()
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('supports');

        // ASSERT: Debe soportar FieldAccess también
        $this->assertTrue($method->invoke($this->voter, PermissionVoter::VIEW, $fieldAccess));
    }

    /**
     * TEST 4: Verifica que se DENIEGA acceso cuando el usuario NO está autenticado
     * 
     * Si no hay usuario logueado (null), siempre debe denegar el acceso.
     * Este es un caso de seguridad crítico.
     */
    public function testDeniesAccessWhenUserNotAuthenticated(): void
    {
        // ARRANGE: Crear un token SIN usuario (simula usuario no logueado)
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null); // ← Usuario NO autenticado
        
        // ARRANGE: Crear un recurso cualquiera
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(1);

        // ACT: Intentar verificar permiso con usuario no autenticado
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('voteOnAttribute');
        $result = $method->invoke($this->voter, PermissionVoter::VIEW, $resource, $token);

        // ASSERT: DEBE denegar acceso (false)
        $this->assertFalse($result);
    }

    /**
     * TEST 5: Verifica que se OTORGA acceso cuando el usuario TIENE permiso específico
     * 
     * Escenario: Usuario tiene permiso directo para ver el paciente #123
     * Resultado esperado: Debe devolver TRUE (conceder acceso)
     * 
     * Este test demuestra el Patrón AAA (Arrange-Act-Assert)
     */
    public function testGrantsAccessWithUserSpecificPermission(): void
    {
        // ============ ARRANGE (Preparar) ============
        // Configuramos todo el escenario de prueba
        
        // 1. Crear un recurso (paciente) fake con ID 123
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient'); // Dominio: patient
        $resource->method('getPermissionId')->willReturn(123); // ID del recurso: 123

        // 2. Crear un permiso fake que dice "SÍ puede ver el paciente 123"
        $permission = $this->createMock(Permission::class);
        $permission->method('getResourceId')->willReturn(123); // Para el recurso 123
        $permission->method('getFieldName')->willReturn(null); // Todos los campos
        $permission->method('canView')->willReturn(true); // ← CLAVE: SÍ puede ver

        // 3. Configurar el repositorio mock para que devuelva ese permiso
        // expects($this->once()) = verificar que se llama exactamente 1 vez
        // with($this->user, 'patient') = verificar que se llama con estos parámetros
        // willReturn([$permission]) = devolver este array de permisos
        $this->permissionRepository
            ->expects($this->once()) // Se debe llamar 1 vez
            ->method('findAllByMember') // Al método findAllByMember()
            ->with($this->user, 'patient') // Con estos parámetros
            ->willReturn([$permission]); // Devolver este permiso

        // 4. Usuario sin grupos (para que no use permisos de grupo)
        $this->user->method('getGroups')->willReturn(new ArrayCollection());

        // ============ ACT (Actuar) ============
        // Ejecutamos el método que queremos probar
        
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('voteOnAttribute');
        
        // Invocar: voter->voteOnAttribute(VIEW, $resource, $token)
        $result = $method->invoke($this->voter, PermissionVoter::VIEW, $resource, $this->token);

        // ============ ASSERT (Verificar) ============
        // Comprobamos que el resultado es el esperado
        
        // Debe devolver TRUE porque el usuario SÍ tiene permiso
        $this->assertTrue($result);
    }

    /**
     * TEST 6: Verifica que se OTORGA acceso mediante permisos de GRUPO
     * 
     * Escenario: Usuario NO tiene permiso individual, PERO pertenece a un grupo
     *            que SÍ tiene permiso para ver todos los pacientes.
     * Resultado esperado: Debe devolver TRUE (hereda permiso del grupo)
     * 
     * Este test demuestra la cascada: Usuario → Grupo
     */
    public function testGrantsAccessWithGroupPermission(): void
    {
        // ARRANGE: Crear recurso (paciente #123)
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(123);

        // ARRANGE: Crear un grupo (ej: "DOCTORES")
        $group = $this->createMock(MemberGroup::class);
        
        // ARRANGE: Crear permiso del GRUPO que dice "SÍ puede ver TODOS los pacientes"
        $groupPermission = $this->createMock(GroupPermission::class);
        $groupPermission->method('getResourceId')->willReturn(null); // NULL = todos los recursos
        $groupPermission->method('getFieldName')->willReturn(null); // NULL = todos los campos
        $groupPermission->method('canView')->willReturn(true); // SÍ puede ver

        // ARRANGE: El usuario NO tiene permisos individuales
        $this->permissionRepository
            ->method('findAllByMember')
            ->willReturn([]); // Array vacío = sin permisos individuales

        // ARRANGE: El usuario SÍ pertenece a un grupo
        $this->user->method('getGroups')->willReturn(new ArrayCollection([$group]));

        // ARRANGE: Configurar que el repositorio devuelva el permiso del grupo
        $this->groupPermissionRepository
            ->expects($this->once()) // Se debe llamar porque no hay permisos de usuario
            ->method('findByGroups')
            ->with([$group], 'patient') // Buscar permisos del grupo para domain='patient'
            ->willReturn([$groupPermission]); // Devolver permiso del grupo

        // ACT: Ejecutar el voter
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('voteOnAttribute');
        $result = $method->invoke($this->voter, PermissionVoter::VIEW, $resource, $this->token);

        // ASSERT: Debe devolver TRUE (hereda permiso del grupo)
        $this->assertTrue($result);
    }

    /**
     * TEST 7: Verifica que se DENIEGA acceso por defecto (sin permisos)
     * 
     * Escenario: Usuario autenticado PERO sin permisos individuales ni de grupo
     * Resultado esperado: Debe devolver FALSE (denegar por defecto)
     * 
     * Principio de seguridad: "Denegar por defecto" (deny by default)
     * Si no hay permisos explícitos, se deniega el acceso.
     */
    public function testDeniesAccessByDefault(): void
    {
        // ARRANGE: Crear recurso
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(123);

        // ARRANGE: Usuario SIN permisos individuales
        $this->permissionRepository
            ->method('findAllByMember')
            ->willReturn([]); // Sin permisos de usuario

        // ARRANGE: Usuario SIN grupos (por lo tanto sin permisos de grupo)
        $this->user->method('getGroups')->willReturn(new ArrayCollection()); // Sin grupos

        // ACT: Ejecutar el voter
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('voteOnAttribute');
        $result = $method->invoke($this->voter, PermissionVoter::VIEW, $resource, $this->token);

        // ASSERT: Debe devolver FALSE (denegar por defecto)
        // Este es el comportamiento de seguridad más importante:
        // Si no hay permisos explícitos, SIEMPRE denegar
        $this->assertFalse($result);
    }

    /**
     * TEST 8: Verifica que los permisos de USUARIO tienen PRIORIDAD sobre los de GRUPO
     * 
     * Escenario: Usuario tiene permiso DENEGAR explícito para paciente #123
     *            Su grupo podría tener permiso PERMITIR, pero NO SE CONSULTA
     * Resultado esperado: Debe devolver FALSE (respeta permiso individual)
     * 
     * Orden de prioridad (de mayor a menor):
     * 1. Permiso individual de usuario  ← GANA
     * 2. Permiso de grupo
     * 3. Denegar por defecto
     */
    public function testUserPermissionOverridesGroupPermission(): void
    {
        // ARRANGE: Crear recurso
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(123);

        // ARRANGE: Usuario tiene permiso explícito de DENEGAR
        // (Por ejemplo: paciente VIP que solo su doctor puede ver)
        $userPermission = $this->createMock(Permission::class);
        $userPermission->method('getResourceId')->willReturn(123);
        $userPermission->method('getFieldName')->willReturn(null);
        $userPermission->method('canView')->willReturn(false); // ← Explícitamente DENEGAR

        // ARRANGE: Configurar repositorio para devolver permiso de DENEGAR
        $this->permissionRepository
            ->method('findAllByMember')
            ->willReturn([$userPermission]); // Usuario tiene permiso (aunque sea de denegar)

        // ARRANGE: Verificar que NO se consultan permisos de grupo
        // expects($this->never()) = NO debe llamarse nunca
        // Esto demuestra que los permisos de usuario tienen prioridad
        $this->groupPermissionRepository
            ->expects($this->never()) // ← CLAVE: Nunca debe llamarse
            ->method('findByGroups');

        // ACT: Ejecutar el voter
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('voteOnAttribute');
        $result = $method->invoke($this->voter, PermissionVoter::VIEW, $resource, $this->token);

        // ASSERT: Debe devolver FALSE (respeta el DENEGAR del usuario)
        // Aunque su grupo diga "PERMITIR", el permiso individual tiene prioridad
        $this->assertFalse($result);
    }

    /**
     * TEST 9: Verifica permisos a nivel de CAMPO (field-level permissions)
     * 
     * Escenario: Usuario tiene permiso para EDITAR el campo 'diagnosis' del paciente #123
     *            (pero podría NO tener permiso para otros campos)
     * Resultado esperado: Debe devolver TRUE (puede editar ese campo específico)
     * 
     * Permisos granulares en cascada (de más específico a más general):
     * 1. domain='patient' + resourceId=123 + fieldName='diagnosis'  ← Este caso
     * 2. domain='patient' + resourceId=123 + fieldName=NULL
     * 3. domain='patient' + resourceId=NULL + fieldName='diagnosis'
     * 4. domain='patient' + resourceId=NULL + fieldName=NULL
     */
    public function testFieldLevelPermissionCascade(): void
    {
        // ARRANGE: Crear recurso (paciente #123)
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(123);

        // ARRANGE: Envolver el recurso en FieldAccess para verificar campo específico
        // FieldAccess = "Quiero verificar el campo 'diagnosis' de este paciente"
        $fieldAccess = new FieldAccess($resource, 'diagnosis');

        // ARRANGE: Permiso MUY ESPECÍFICO para el campo 'diagnosis' del paciente #123
        $permission = $this->createMock(Permission::class);
        $permission->method('getResourceId')->willReturn(123); // Paciente #123
        $permission->method('getFieldName')->willReturn('diagnosis'); // Campo 'diagnosis'
        $permission->method('canEdit')->willReturn(true); // SÍ puede editar

        // ARRANGE: Configurar repositorio para devolver este permiso
        $this->permissionRepository
            ->method('findAllByMember')
            ->willReturn([$permission]);

        // ARRANGE: Sin grupos
        $this->user->method('getGroups')->willReturn(new ArrayCollection());

        // ACT: Ejecutar el voter con EDIT en un campo específico
        $reflection = new \ReflectionClass($this->voter);
        $method = $reflection->getMethod('voteOnAttribute');
        $result = $method->invoke($this->voter, PermissionVoter::EDIT, $fieldAccess, $this->token);

        // ASSERT: Debe devolver TRUE (puede editar el campo 'diagnosis')
        // Este es el nivel más granular de permisos:
        // - Recurso específico (paciente #123)
        // - Campo específico (diagnosis)
        // - Acción específica (EDIT)
        $this->assertTrue($result);
    }
}

/**
 * ============================================
 * RESUMEN DEL TEST SUITE
 * ============================================
 * 
 * Este archivo contiene 9 tests que cubren:
 * 
 * Soporte de atributos (VIEW, EDIT, DELETE)
 * Soporte de tipos de recursos (SecuredResourceInterface, FieldAccess)
 * Seguridad: denegar usuarios no autenticados
 * Permisos individuales de usuario
 * Permisos heredados de grupo
 * Denegación por defecto (sin permisos)
 * Prioridad: usuario > grupo
 * Permisos a nivel de campo (field-level)
 * 
 * Principios demostrados:
 * - Mock objects para evitar base de datos
 * - Patrón AAA (Arrange-Act-Assert)
 * - Reflection para acceder a métodos protected
 * - Expects para verificar llamadas a métodos
 * - Asserts para verificar resultados
 * 
 * Ejecutar: php bin/phpunit tests/Unit/Security/Voter/PermissionVoterTest.php
 */
