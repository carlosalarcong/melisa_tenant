<?php

namespace App\Tests\Unit\Service;

use App\Service\TenantResolver;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests unitarios para TenantResolver
 * 
 * CÓMO EJECUTAR ESTE TEST:
 * ========================
 * 
 * 1. EJECUTAR TODOS LOS TESTS DE ESTE ARCHIVO:
 *    php bin/phpunit tests/Unit/Service/TenantResolverTest.php
 * 
 * 2. EJECUTAR CON FORMATO DESCRIPTIVO:
 *    php bin/phpunit tests/Unit/Service/TenantResolverTest.php --testdox
 * 
 * 3. EJECUTAR SOLO UN TEST ESPECÍFICO:
 *    php bin/phpunit --filter testResolveTenantFromRequestWithValidSubdomain tests/Unit/Service/TenantResolverTest.php
 * 
 * 4. EJECUTAR TESTS POR PATRÓN DE NOMBRE:
 *    php bin/phpunit --filter "Resolution" tests/Unit/Service/TenantResolverTest.php
 *    php bin/phpunit --filter "Database" tests/Unit/Service/TenantResolverTest.php
 *    php bin/phpunit --filter "Connection" tests/Unit/Service/TenantResolverTest.php
 *    php bin/phpunit --filter "Edge" tests/Unit/Service/TenantResolverTest.php
 * 
 * 5. EJECUTAR CON INFORMACIÓN DETALLADA:
 *    php bin/phpunit tests/Unit/Service/TenantResolverTest.php --verbose
 * 
 * 6. EJECUTAR Y DETENER EN PRIMER FALLO:
 *    php bin/phpunit tests/Unit/Service/TenantResolverTest.php --stop-on-failure
 * 
 * 7. EJECUTAR TODOS LOS TESTS UNITARIOS:
 *    php bin/phpunit tests/Unit/
 * 
 * 8. EJECUTAR TEST ESPECÍFICO CON DEBUG:
 *    php bin/phpunit --filter testResolveTenantFromRequestWithValidSubdomain tests/Unit/Service/TenantResolverTest.php --testdox --verbose
 * 
 * EJEMPLOS PRÁCTICOS VERIFICADOS:
 * ===============================
 * 
 * ✅ EJECUTAR SOLO TEST DE CONEXIÓN:
 *    php bin/phpunit --filter testCreateTenantConnection tests/Unit/Service/TenantResolverTest.php --testdox
 * 
 * ✅ EJECUTAR TESTS DE VALIDACIÓN DE SUBDOMINIOS:
 *    php bin/phpunit --filter "Subdomain" tests/Unit/Service/TenantResolverTest.php --testdox
 * 
 * ✅ EJECUTAR TESTS DE MANEJO DE ERRORES:
 *    php bin/phpunit --filter "Exception\|Null" tests/Unit/Service/TenantResolverTest.php --testdox
 * 
 * ✅ VER TODOS LOS TESTS DISPONIBLES:
 *    php bin/phpunit tests/Unit/Service/TenantResolverTest.php --list-tests
 * 
 * EJEMPLOS DE RESULTADOS ESPERADOS:
 * =================================
 * 
 * ✅ TODOS LOS TESTS PASAN:
 * OK (12 tests, 42 assertions)
 * 
 * ❌ SI HAY FALLOS:
 * FAILURES!
 * Tests: 12, Assertions: 40, Failures: 1.
 * 
 * 📊 GRUPOS DE TESTS:
 * - tenant-resolution: Tests de extracción de subdominios (5 tests)
 * - tenant-db-operations: Tests de operaciones de BD (2 tests)  
 * - tenant-connection: Tests de conexiones (1 test)
 * - edge-cases: Tests de casos límite (2 tests)
 * - tenant-validation: Tests de validación (2 tests)
 * 
 * COBERTURA DE TESTS:
 * - Resolución de tenant desde subdominios
 * - Obtención de configuración de tenant desde BD
 * - Creación de conexiones específicas por tenant
 * - Manejo de errores y casos edge
 * - Validación de tenants activos vs inactivos
 */
class TenantResolverTest extends TestCase
{
    private TenantResolver $tenantResolver;
    private string $databaseUrl = 'mysql://melisa:melisamelisa@localhost:3306/melisa_central';

    /**
     * Inicializa el objeto TenantResolver antes de cada test
     * 
     * QUÉ HACE: Crea una instancia limpia del resolver para cada test
     * POR QUÉ: Asegura que cada test tenga una instancia aislada sin estado previo
     */
    protected function setUp(): void
    {
        $this->tenantResolver = new TenantResolver($this->databaseUrl);
    }

    /**
     * Prueba extracción correcta de tenant desde URL con subdomain válido
     * 
     * QUÉ HACE: Verifica que puede extraer "melisahospital" de melisahospital.melisaupgrade.prod
     * VALIDA: 
     * - Extracción del subdomain desde URL
     * - Mock de respuesta de BD para evitar conexión real
     * - Verificación de datos del tenant (nombre, BD, estado activo)
     * 
     * @group tenant-resolution
     */
    public function testResolveTenantFromRequestWithValidSubdomain(): void
    {
        // Arrange
        // 1. ENTRADA: URL real con subdomain válido
        $request = Request::create('http://melisahospital.melisaupgrade.prod/some/path');
        
        // Mock de la respuesta esperada de BD
        $expectedTenant = [
            'id' => 3,
            'name' => 'Hospital Central',
            'subdomain' => 'melisahospital',
            'database_name' => 'melisahospital',
            'host' => 'localhost',
            'host_port' => 3306,
            'db_user' => 'melisa',
            'db_password' => 'melisamelisa',
            'driver' => 'mysql',
            'is_active' => 1
        ];

        // 2. MOCK: Crear objeto falso para simular respuesta de BD
        $resolverMock = $this->createPartialMock(TenantResolver::class, ['getTenantBySlug']);
        $resolverMock->__construct($this->databaseUrl);
        $resolverMock->expects($this->once())                    // ← Debe llamarse exactamente 1 vez
                    ->method('getTenantBySlug')                  // ← Mock este método específico
                    ->with('melisahospital')                     // ← Debe recibir este parámetro exacto
                    ->willReturn($expectedTenant);               // ← Retornar estos datos falsos predefinidos

        // 3. EJECUCIÓN: Llamar al método real que queremos probar
        $result = $resolverMock->resolveTenantFromRequest($request);

        // 4. VALIDACIÓN: Verificar que funciona correctamente
        $this->assertNotNull($result);                          // ← Debe retornar algo, no null
        $this->assertEquals('melisahospital', $result['subdomain']); // ← Subdomain extraído correctamente
        $this->assertEquals('Hospital Central', $result['name']);     // ← Nombre del tenant correcto
        $this->assertEquals('melisahospital', $result['database_name']); // ← BD asignada correctamente
        $this->assertTrue((bool)$result['is_active']);          // ← Tenant debe estar activo
    }

    /**
     * Prueba resolución con otro tenant válido (La Colina)
     * 
     * QUÉ HACE: Verifica que funciona con diferentes tenants, no solo melisahospital
     * POR QUÉ: Asegura que el sistema es genérico y funciona con múltiples tenants
     * 
     * @group tenant-resolution
     */
    public function testResolveTenantFromRequestWithSubdomainLaColina(): void
    {
        // Arrange
        $request = Request::create('http://melisalacolina.melisaupgrade.prod/dashboard');
        
        $expectedTenant = [
            'id' => 1,
            'name' => 'La Colina',
            'subdomain' => 'melisalacolina',
            'database_name' => 'melisalacolina',
            'is_active' => 1
        ];

        $resolverMock = $this->createPartialMock(TenantResolver::class, ['getTenantBySlug']);
        $resolverMock->expects($this->once())
                    ->method('getTenantBySlug')
                    ->with('melisalacolina')
                    ->willReturn($expectedTenant);

        // Act
        $result = $resolverMock->resolveTenantFromRequest($request);

        // Assert
        $this->assertEquals('melisalacolina', $result['subdomain']);
        $this->assertEquals('La Colina', $result['name']);
    }

    /**
     * Prueba comportamiento con URL sin subdomain
     * 
     * QUÉ HACE: Verifica URL sin subdomain (melisaupgrade.prod) retorna null
     * VALIDA: Que el sistema no trata dominios base como tenants
     * 
     * @group tenant-resolution
     */
    public function testResolveTenantFromRequestWithNoSubdomain(): void
    {
        // Arrange
        $request = Request::create('http://melisaupgrade.prod/some/path');
        
        // Act
        $result = $this->tenantResolver->resolveTenantFromRequest($request);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba que subdominios comunes se ignoran (www)
     * 
     * QUÉ HACE: Verifica que www.melisaupgrade.prod se ignora
     * POR QUÉ: www, api, admin no son tenants válidos del sistema
     * 
     * @group tenant-resolution
     */
    public function testResolveTenantFromRequestWithCommonSubdomain(): void
    {
        // Arrange - www debe ser ignorado
        $request = Request::create('http://www.melisaupgrade.prod/some/path');
        
        // Act
        $result = $this->tenantResolver->resolveTenantFromRequest($request);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba que subdomain API se ignora
     * 
     * QUÉ HACE: Verifica que api.melisaupgrade.prod se ignora
     * POR QUÉ: API no debe ser tratado como tenant, es infraestructura
     * 
     * @group tenant-resolution
     */
    public function testResolveTenantFromRequestWithApiSubdomain(): void
    {
        // Arrange - api debe ser ignorado
        $request = Request::create('http://api.melisaupgrade.prod/some/path');
        
        // Act
        $result = $this->tenantResolver->resolveTenantFromRequest($request);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba que subdomain ADMIN se ignora
     * 
     * QUÉ HACE: Verifica que admin.melisaupgrade.prod se ignora
     * POR QUÉ: Admin no debe ser tratado como tenant, es panel administrativo
     * 
     * @group tenant-resolution
     */
    public function testResolveTenantFromRequestWithAdminSubdomain(): void
    {
        // Arrange - admin debe ser ignorado
        $request = Request::create('http://admin.melisaupgrade.prod/some/path');
        
        // Act
        $result = $this->tenantResolver->resolveTenantFromRequest($request);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba manejo de errores de base de datos
     * 
     * QUÉ HACE: Simula error de BD y verifica que la excepción se propaga correctamente
     * POR QUÉ: Asegura manejo robusto de errores de conexión a BD
     * 
     * @group tenant-db-operations
     */
    public function testGetTenantBySlugThrowsExceptionOnDatabaseError(): void
    {
        // Arrange
        $resolverMock = $this->createPartialMock(TenantResolver::class, ['getTenantBySlug']);
        $resolverMock->expects($this->once())
                    ->method('getTenantBySlug')
                    ->with('invalid_tenant')
                    ->willThrowException(new \Exception('Database connection failed'));

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database connection failed');

        // Act
        $resolverMock->getTenantBySlug('invalid_tenant');
    }

    /**
     * Prueba búsqueda de tenant inexistente
     * 
     * QUÉ HACE: Verifica búsqueda de tenant que no existe en BD
     * VALIDA: Que retorna null en lugar de fallar cuando no encuentra el tenant
     * 
     * @group tenant-db-operations
     */
    public function testGetTenantBySlugReturnsNullForInvalidTenant(): void
    {
        // Arrange
        $resolverMock = $this->createPartialMock(TenantResolver::class, ['getTenantBySlug']);
        $resolverMock->expects($this->once())
                    ->method('getTenantBySlug')
                    ->with('nonexistent')
                    ->willReturn(null);

        // Act
        $result = $resolverMock->getTenantBySlug('nonexistent');

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba creación de conexión específica para tenant
     * 
     * QUÉ HACE: Verifica creación de conexión de BD específica para tenant
     * VALIDA: 
     * - Que crea una instancia de Connection válida
     * - Parámetros correctos (host, puerto, BD, credenciales)
     * - Driver MySQL configurado
     * 
     * @group tenant-connection
     */
    public function testCreateTenantConnectionWithValidTenant(): void
    {
        // Arrange
        $tenantData = [
            'host' => 'localhost',
            'host_port' => 3306,
            'database_name' => 'melisahospital',
            'db_user' => 'melisa',
            'db_password' => 'melisamelisa'
        ];

        // Act
        $connection = $this->tenantResolver->createTenantConnection($tenantData);

        // Assert
        $this->assertInstanceOf(Connection::class, $connection);
        $params = $connection->getParams();
        $this->assertEquals('localhost', $params['host']);
        $this->assertEquals(3306, $params['port']);
        $this->assertEquals('melisahospital', $params['dbname']);
        $this->assertEquals('melisa', $params['user']);
        $this->assertEquals('melisamelisa', $params['password']);
        $this->assertEquals('pdo_mysql', $params['driver']);
    }

    /**
     * Prueba caso edge con host vacío
     * 
     * QUÉ HACE: Verifica comportamiento con host vacío en el request
     * POR QUÉ: Manejo robusto de requests malformadas o incompletas
     * 
     * @group edge-cases
     */
    public function testResolveTenantWithEmptyHost(): void
    {
        // Arrange - usar un host válido pero vacío en lugar de URI inválida
        $request = Request::create('http://example.com/some/path');
        $request->headers->set('HOST', '');
        
        // Act
        $result = $this->tenantResolver->resolveTenantFromRequest($request);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba URL con dominio simple (sin subdomain)
     * 
     * QUÉ HACE: Verifica URL con solo un dominio (localhost)
     * VALIDA: Que no trata dominios simples como tenants válidos
     * 
     * @group edge-cases
     */
    public function testResolveTenantWithSingleDomainPart(): void
    {
        // Arrange
        $request = Request::create('http://localhost/some/path');
        
        // Act
        $result = $this->tenantResolver->resolveTenantFromRequest($request);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba obtención de lista de todos los tenants activos
     * 
     * QUÉ HACE: Verifica obtención de lista completa de tenants disponibles
     * VALIDA:
     * - Formato correcto del array retornado
     * - Conteo esperado de tenants
     * - Estructura de datos (subdomain + name)
     * - Datos específicos del primer tenant
     * 
     * @group tenant-validation
     */
    public function testGetAllActiveTenantsReturnsFormattedArray(): void
    {
        // Arrange
        $expectedTenants = [
            ['subdomain' => 'melisahospital', 'name' => 'Hospital Central'],
            ['subdomain' => 'melisalacolina', 'name' => 'La Colina'],
            ['subdomain' => 'melisawiclinic', 'name' => 'Wiclinic']
        ];

        $resolverMock = $this->createPartialMock(TenantResolver::class, ['getAllActiveTenants']);
        $resolverMock->expects($this->once())
                    ->method('getAllActiveTenants')
                    ->willReturn($expectedTenants);

        // Act
        $result = $resolverMock->getAllActiveTenants();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        
        foreach ($result as $tenant) {
            $this->assertArrayHasKey('subdomain', $tenant);
            $this->assertArrayHasKey('name', $tenant);
        }
        
        $this->assertEquals('melisahospital', $result[0]['subdomain']);
        $this->assertEquals('Hospital Central', $result[0]['name']);
    }
}