# 🔧 Mejora de Malas Prácticas - Symfony 3 → Symfony 6

## 🎯 Objetivo
Identificar y corregir las malas prácticas del código legacy durante la migración.

---

## 🚨 Malas Prácticas Identificadas en el Código Legacy

### 1. ❌ Controladores Gigantes (God Controllers)

**PROBLEMA en Symfony 3:**
```php
// DatosMaestrosMedicosController.php - 1000+ líneas

class DatosMaestrosMedicosController extends AgendaController
{
    // 200+ líneas de lógica en renderViewDMM()
    protected function renderViewDMM(array $arr)
    {
        // Lógica de negocio mezclada con presentación
        // Consultas directas a BD
        // Validaciones complejas
        // Manipulación de datos
        // Renderizado de vistas
        // Todo en UN solo método
    }
    
    // Método con 300+ líneas
    public function nuevoUsuarioAction(Request $request)
    {
        // Validación manual
        // Creación de múltiples entidades
        // Persistencia directa
        // Lógica de negocio
        // Envío de emails
        // Todo mezclado
    }
}
```

**SOLUCIÓN Symfony 6:**
```php
// Controlador delgado - 50 líneas
class UserCreateController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService,
        private LicenseValidationService $licenseService
    ) {}

    #[Route('/nuevo', name: 'new')]
    public function new(Request $request): Response
    {
        // Solo lógica de HTTP
        if (!$this->licenseService->hasAvailableLicenses()) {
            $this->addFlash('error', 'No hay licencias disponibles');
            return $this->redirectToRoute('admin_user_index');
        }

        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Delegar a servicio
                $user = $this->userService->createUser($form->getData());
                $this->addFlash('success', 'Usuario creado');
                return $this->redirectToRoute('admin_user_index');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('admin/user/create.html.twig', ['form' => $form]);
    }
}

// Servicio con la lógica - bien organizado
class UserManagementService
{
    // Lógica de negocio aquí
    public function createUser(array $data): UsuariosRebsol
    {
        // Transacción
        // Validaciones
        // Creación de entidades
        // Persistencia
        return $user;
    }
}
```

**Beneficios:**
✅ Controlador con una sola responsabilidad (SRP)
✅ Lógica de negocio reutilizable
✅ Fácil de testear
✅ Fácil de mantener

---

### 2. ❌ Acceso Directo a EntityManager en Controladores

**PROBLEMA:**
```php
class DMMNuevoController extends DatosMaestrosMedicosController
{
    public function nuevoUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // Queries directas en controlador
        $sexos = $em->getRepository('HermesBundle:Sexo')
            ->findBy(['idEmpresa' => $empresa, 'idEstado' => 1]);
        
        $roles = $em->getRepository('HermesBundle:Rol')
            ->createQueryBuilder('r')
            ->where('r.idEmpresa = :empresa')
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getResult();
        
        // Persistencia directa
        $em->persist($persona);
        $em->persist($pnatural);
        $em->persist($usuario);
        $em->flush();
        
        // Lógica compleja mezclada
    }
}
```

**SOLUCIÓN:**
```php
// Controlador limpio
class UserCreateController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService
    ) {}

    #[Route('/nuevo', name: 'new')]
    public function new(Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userService->createUser($form->getData());
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/create.html.twig', ['form' => $form]);
    }
}

// Servicio encapsula toda la lógica
class UserManagementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private SexoRepository $sexoRepository,
        private RolRepository $rolRepository
    ) {}

    public function createUser(array $data): UsuariosRebsol
    {
        $this->em->beginTransaction();
        try {
            $persona = $this->createPersona($data);
            $pnatural = $this->createPnatural($data, $persona);
            $usuario = $this->createUsuario($data, $persona);
            
            $this->em->persist($persona);
            $this->em->persist($pnatural);
            $this->em->persist($usuario);
            $this->em->flush();
            $this->em->commit();
            
            return $usuario;
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
```

---

### 3. ❌ Herencia de Controladores Innecesaria

**PROBLEMA:**
```php
// Todos heredan del mismo controlador base gigante
class DMMNuevoController extends DatosMaestrosMedicosController
class DMMEditController extends DatosMaestrosMedicosController
class DMMVerController extends DatosMaestrosMedicosController

// Y este hereda de otro
class DatosMaestrosMedicosController extends AgendaController

// Y este de otro
class AgendaController extends HermesController

// 4+ niveles de herencia!
```

**SOLUCIÓN:**
```php
// Composición sobre herencia
class UserCreateController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService,
        private LicenseValidationService $licenseService
    ) {}
}

class UserEditController extends AbstractTenantAwareController
{
    public function __construct(
        private UserManagementService $userService
    ) {}
}

// Solo un nivel de herencia
// Funcionalidad compartida mediante servicios
```

---

### 4. ❌ Variables Crípticas y Mal Nombradas

**PROBLEMA:**
```php
protected function renderViewDMM(array $arr)
{
    $from = $arr['from'];  // ¿Qué es from?
    $new = $arr['new'];    // ¿new es boolean? ¿número?
    
    if ($new == 0) {
        // Editar
    } elseif ($new == 1) {
        // Crear
    } elseif ($new == 2) {
        // Ver
    } elseif ($new == 3) {
        // Listar
    }
    
    // ¿from es de?, ¿desde?, ¿tipo?
    if ($from == 0) {
        // Profesional
    } elseif ($from == 1) {
        // Usuario
    }
}
```

**SOLUCIÓN:**
```php
// Usar Enums de PHP 8.1
enum UserTypeEnum: int
{
    case PROFESSIONAL = 0;
    case ADMINISTRATIVE = 1;
    
    public function label(): string
    {
        return match($this) {
            self::PROFESSIONAL => 'Profesional',
            self::ADMINISTRATIVE => 'Usuario Administrativo'
        };
    }
}

enum OperationTypeEnum: int
{
    case EDIT = 0;
    case CREATE = 1;
    case VIEW = 2;
    case LIST = 3;
}

// Uso claro
if ($userType === UserTypeEnum::PROFESSIONAL) {
    // Lógica para profesional
}

if ($operation === OperationTypeEnum::CREATE) {
    // Lógica de creación
}
```

---

### 5. ❌ Números Mágicos y Strings Hardcodeados

**PROBLEMA:**
```php
// ¿Qué significa 1, 0, 2?
$usuario->setIdEstadoUsuario(1);  // Activo
$servicio->setEstado(0);          // Inactivo
$especialidad->setEstado(2);      // Bloqueado

// Strings duplicados por todas partes
$estado = $em->getRepository('App:Estado')->findOneBy(['nombre' => 'ACTIVO']);
$estado = $em->getRepository('App:Estado')->findOneBy(['nombre' => 'ACTIVO']);
$estado = $em->getRepository('App:Estado')->findOneBy(['nombre' => 'ACTIVO']);
```

**SOLUCIÓN:**
```php
// Enum para estados
enum StateEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
    case BLOCKED = 2;
}

// Constantes de clase
class Estado
{
    public const ACTIVE = 'ACTIVO';
    public const INACTIVE = 'INACTIVO';
    public const BLOCKED = 'BLOQUEADO';
}

// Servicio para estados (caché)
class StateService
{
    private array $stateCache = [];
    
    public function getActiveState(): Estado
    {
        return $this->stateCache['active'] ??= 
            $this->estadoRepository->findOneBy(['nombre' => Estado::ACTIVE]);
    }
    
    public function getInactiveState(): Estado
    {
        return $this->stateCache['inactive'] ??= 
            $this->estadoRepository->findOneBy(['nombre' => Estado::INACTIVE]);
    }
}

// Uso
$usuario->setIdEstadoUsuario($this->stateService->getActiveState());
```

---

### 6. ❌ Falta de Manejo de Transacciones

**PROBLEMA:**
```php
public function nuevoUsuarioAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();
    
    // Sin transacción - Si falla algo a mitad, datos inconsistentes
    $em->persist($persona);
    $em->flush();  // Ya guardó persona
    
    $em->persist($pnatural);
    $em->flush();  // Ya guardó pnatural
    
    // ERROR aquí - pero persona y pnatural ya están en BD
    $em->persist($usuario);
    $em->flush();
}
```

**SOLUCIÓN:**
```php
public function createUser(array $data): UsuariosRebsol
{
    $this->em->beginTransaction();
    
    try {
        $persona = $this->createPersona($data);
        $pnatural = $this->createPnatural($data, $persona);
        $usuario = $this->createUsuario($data, $persona);
        
        $this->em->persist($persona);
        $this->em->persist($pnatural);
        $this->em->persist($usuario);
        
        // Flush una sola vez
        $this->em->flush();
        $this->em->commit();
        
        return $usuario;
        
    } catch (\Exception $e) {
        $this->em->rollback();
        $this->logger->error('Error creating user', ['error' => $e->getMessage()]);
        throw $e;
    }
}
```

---

### 7. ❌ Sin Logging y Sin Auditoría Adecuada

**PROBLEMA:**
```php
public function dellAction(Request $request, $id)
{
    $em = $this->getDoctrine()->getManager();
    $usuario = $em->getRepository('HermesBundle:UsuariosRebsol')->find($id);
    
    $usuario->setIdEstadoUsuario($estadoInactivo);
    $em->flush();
    
    // ¿Quién eliminó? ¿Cuándo? ¿Por qué?
    // Sin rastro
    
    return new JsonResponse(['success' => true]);
}
```

**SOLUCIÓN:**
```php
public function deleteUser(UsuariosRebsol $usuario): void
{
    // Log antes
    $this->logger->info('Inactivating user', [
        'userId' => $usuario->getId(),
        'username' => $usuario->getNombreUsuario(),
        'performedBy' => $this->security->getUser()->getId(),
        'timestamp' => new \DateTime()
    ]);

    $this->em->beginTransaction();
    
    try {
        $usuario->setIdEstadoUsuario($this->stateService->getInactiveState());
        $usuario->setAuditoria(new \DateTime());
        
        // Registrar en tabla de auditoría
        $auditLog = new UserAuditLog();
        $auditLog->setUsuario($usuario);
        $auditLog->setAction('DELETE');
        $auditLog->setPerformedBy($this->security->getUser());
        $auditLog->setTimestamp(new \DateTime());
        $this->em->persist($auditLog);
        
        $this->em->flush();
        $this->em->commit();
        
        // Log después
        $this->logger->info('User inactivated successfully', [
            'userId' => $usuario->getId()
        ]);
        
    } catch (\Exception $e) {
        $this->em->rollback();
        
        // Log error
        $this->logger->error('Failed to inactivate user', [
            'userId' => $usuario->getId(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw $e;
    }
}
```

---

### 8. ❌ Validaciones Dispersas y Duplicadas

**PROBLEMA:**
```php
// En controlador 1
if (strlen($data['password']) < 8) {
    throw new \Exception('Contraseña muy corta');
}

// En controlador 2
if (strlen($data['password']) < 8) {
    throw new \Exception('Password debe tener 8 caracteres mínimo');
}

// En controlador 3
if (mb_strlen($data['password']) < 8) {
    $this->addFlash('error', 'La contraseña debe tener al menos 8 caracteres');
}

// Validación RUT duplicada 10+ veces
```

**SOLUCIÓN:**
```php
// Validador custom reutilizable
#[Attribute]
class ValidChileanRut extends Constraint
{
    public string $message = 'El RUT "{{ value }}" no es válido';
}

class ValidChileanRutValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$this->isValidRut($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
    
    private function isValidRut(string $rut): bool
    {
        // Lógica de validación centralizada
    }
}

// Uso en entidad
class Pnatural
{
    #[Assert\NotBlank]
    #[ValidChileanRut]
    private string $identificacion;
}

// O en form
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('identificacion', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new ValidChileanRut()
            ]
        ]);
    }
}
```

---

### 9. ❌ Queries N+1 y Falta de Optimización

**PROBLEMA:**
```php
// DatosMaestrosMedicosController
public function IndexAction(Request $request)
{
    $usuarios = $em->getRepository('HermesBundle:UsuariosRebsol')
        ->findAll();
    
    // N+1 query problem
    foreach ($usuarios as $usuario) {
        $persona = $usuario->getIdPersona();  // Query
        $pnatural = $persona->getPnatural();   // Query
        $rol = $usuario->getIdRol();          // Query
        $especialidades = $usuario->getEspecialidades(); // Query
        $grupos = $usuario->getGrupos();      // Query
    }
    
    // 1 query inicial + N * 5 queries = DESASTRE
}
```

**SOLUCIÓN:**
```php
// Repository con eager loading
class UsuariosRebsolRepository extends ServiceEntityRepository
{
    public function findAllUsersWithDetails(Empresa $empresa): array
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'p', 'pn', 'r', 'eu', 'e', 'g')
            ->innerJoin('u.idPersona', 'p')
            ->innerJoin('p.pnatural', 'pn')
            ->innerJoin('u.idRol', 'r')
            ->innerJoin('u.idEstadoUsuario', 'eu')
            ->leftJoin('u.especialidades', 'e')
            ->leftJoin('u.grupos', 'g')
            ->where('p.idEmpresa = :empresa')
            ->setParameter('empresa', $empresa)
            ->orderBy('pn.apellidoPaterno', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

// Una sola query con todos los datos
$users = $this->userRepository->findAllUsersWithDetails($tenant);
```

---

### 10. ❌ Sin Tests

**PROBLEMA:**
```php
// Carpeta tests/ vacía o con 1-2 tests obsoletos
// Sin tests unitarios
// Sin tests de integración
// Sin tests funcionales
// Deployment sin validación automatizada
```

**SOLUCIÓN:**
```php
// Tests Unitarios
class UserManagementServiceTest extends TestCase
{
    private UserManagementService $service;
    
    protected function setUp(): void
    {
        $this->service = new UserManagementService(
            $this->createMock(EntityManagerInterface::class),
            // ... otros mocks
        );
    }
    
    public function testCreateUserWithValidData(): void
    {
        $data = ['nombreUsuario' => 'test', /* ... */];
        $user = $this->service->createUser($data);
        
        $this->assertInstanceOf(UsuariosRebsol::class, $user);
        $this->assertEquals('test', $user->getNombreUsuario());
    }
    
    public function testCreateUserWithoutLicenseThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No hay licencias disponibles');
        
        // Mock sin licencias
        $this->service->createUser([/* ... */]);
    }
}

// Tests Funcionales
class UserControllerTest extends WebTestCase
{
    public function testUserListPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/usuarios');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table#users-table');
    }
    
    public function testCreateUserWithValidData(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/usuarios/nuevo');
        
        $form = $crawler->selectButton('Guardar')->form([
            'user[nombreUsuario]' => 'testuser',
            'user[password]' => 'Test1234',
            // ...
        ]);
        
        $client->submit($form);
        $this->assertResponseRedirects('/admin/usuarios');
    }
}
```

---

### 11. ❌ Uso de Arrays en Lugar de DTOs

**PROBLEMA:**
```php
protected function renderViewDMM(array $arr)
{
    // Array gigante sin tipo
    // No sabes qué claves existen
    // Sin autocompletado
    // Errores en runtime
    
    $from = $arr['from'];
    $new = $arr['new'];
    $idUser = $arr['idUser'] ?? null;  // ¿Existe?
    $errorReturn = $arr['errorReturn'] ?? false;
    $mensajeError = $arr['mensajeError'] ?? '';
    // ...
}
```

**SOLUCIÓN:**
```php
// DTO (Data Transfer Object)
class UserFormData
{
    public function __construct(
        public readonly UserTypeEnum $userType,
        public readonly OperationTypeEnum $operation,
        public readonly ?int $userId = null,
        public readonly bool $hasError = false,
        public readonly ?string $errorMessage = null,
        public readonly ?FormInterface $form = null,
        public readonly ?Rol $rol = null,
        public readonly ?UsuariosRebsol $entity = null
    ) {}
}

// Uso con tipos estrictos
public function renderUserForm(UserFormData $data): Response
{
    // Autocompletado funciona
    // Tipos garantizados
    // Errores en compile time
    
    if ($data->userType === UserTypeEnum::PROFESSIONAL) {
        // ...
    }
    
    if ($data->operation === OperationTypeEnum::CREATE) {
        // ...
    }
}
```

---

### 12. ❌ Mezcla de Español e Inglés

**PROBLEMA:**
```php
class DMMNuevoController  // Español
{
    public function nuevoUsuarioAction()  // Español
    {
        $em = $this->getDoctrine()->getManager();  // Inglés
        $persona = new Persona();  // Español
        $usuario = new UsuariosRebsol();  // Español mezclado
        $password = $data['contrasena'];  // Mezclado
    }
}
```

**SOLUCIÓN:**
```php
// Opción 1: Todo en inglés (recomendado internacional)
class UserCreateController
{
    public function new()
    {
        $em = $this->em;
        $person = new Person();
        $user = new User();
        $password = $data['password'];
    }
}

// Opción 2: Todo en español (si solo es para mercado local)
class ControladorCrearUsuario
{
    public function nuevo()
    {
        $ge = $this->gestorEntidades;
        $persona = new Persona();
        $usuario = new Usuario();
        $contrasena = $data['contrasena'];
    }
}

// NUNCA mezclar los dos
```

---

## 🎯 Mejores Prácticas a Implementar

### 1. ✅ SOLID Principles

#### Single Responsibility Principle (SRP)
```php
// ❌ MAL - Una clase hace todo
class UserService
{
    public function createUser() { }
    public function validateLicense() { }
    public function hashPassword() { }
    public function sendEmail() { }
    public function auditLog() { }
}

// ✅ BIEN - Cada clase una responsabilidad
class UserManagementService { }
class LicenseValidationService { }
class PasswordHashingService { }
class EmailNotificationService { }
class AuditLogService { }
```

#### Dependency Inversion Principle (DIP)
```php
// ❌ MAL - Depende de implementación concreta
class UserService
{
    private MySQLUserRepository $repository;
}

// ✅ BIEN - Depende de abstracción
class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}
}
```

---

### 2. ✅ Inyección de Dependencias

```php
// ❌ MAL - Service Location
class UserController
{
    public function new()
    {
        $service = $this->get('user.management.service');
        $validator = $this->get('license.validator');
    }
}

// ✅ BIEN - Constructor Injection
class UserController
{
    public function __construct(
        private UserManagementService $userService,
        private LicenseValidationService $licenseValidator
    ) {}
}
```

---

### 3. ✅ Type Hints Estrictos

```php
// ❌ MAL - Sin tipos
public function createUser($data)
{
    return $user;
}

// ✅ BIEN - Tipos estrictos
public function createUser(array $data): UsuariosRebsol
{
    return $user;
}

// ✅ MEJOR - Con DTO
public function createUser(CreateUserDTO $data): UsuariosRebsol
{
    return $user;
}
```

---

### 4. ✅ Manejo de Errores Robusto

```php
// ❌ MAL - Excepciones genéricas
throw new \Exception('Error');

// ✅ BIEN - Excepciones específicas
class UserNotFoundException extends \RuntimeException {}
class LicenseUnavailableException extends \RuntimeException {}
class InvalidPasswordException extends \DomainException {}

throw new LicenseUnavailableException(
    'No hay licencias disponibles para crear usuarios'
);
```

---

### 5. ✅ Cacheo Estratégico

```php
// Para datos que no cambian frecuentemente
use Symfony\Contracts\Cache\CacheInterface;

class StateService
{
    public function __construct(
        private CacheInterface $cache,
        private EstadoRepository $repository
    ) {}
    
    public function getActiveState(): Estado
    {
        return $this->cache->get('state.active', function() {
            return $this->repository->findOneBy(['nombre' => 'ACTIVO']);
        });
    }
}
```

---

## 📊 Impacto de las Mejoras

| Aspecto | Legacy | Mejorado | Beneficio |
|---------|--------|----------|-----------|
| **Mantenibilidad** | 2/10 | 9/10 | +350% |
| **Testabilidad** | 1/10 | 9/10 | +800% |
| **Performance** | 5/10 | 8/10 | +60% |
| **Escalabilidad** | 3/10 | 9/10 | +200% |
| **Seguridad** | 6/10 | 9/10 | +50% |
| **Legibilidad** | 3/10 | 9/10 | +200% |

---

## 🎓 Recomendaciones Finales

### Durante la Migración

1. **No replicar malas prácticas** - Solo porque "así funcionaba antes"
2. **Refactorizar progresivamente** - No todo de golpe
3. **Tests antes de cambiar** - Asegurar funcionalidad
4. **Code review estricto** - Dos pares de ojos
5. **Documentar decisiones** - Por qué se cambió X

### Después de la Migración

1. **Mantener estándares** - No volver atrás
2. **Capacitar al equipo** - Nuevas prácticas
3. **Monitoreo continuo** - Detectar regresiones
4. **Mejora continua** - Siempre hay espacio

---

**Conclusión:** La migración no es solo cambiar de versión, es una oportunidad de **mejorar significativamente** la calidad del código.

---

**Documento creado:** Diciembre 2025  
**Versión:** 1.0  
**Estado:** ✅ Lista para aplicar
