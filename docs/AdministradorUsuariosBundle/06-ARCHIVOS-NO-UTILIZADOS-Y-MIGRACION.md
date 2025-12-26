# AdministradorUsuariosBundle - Parte 5: Archivos No Utilizados y Migración a Symfony 6

## 🗑️ Archivos Posiblemente No Utilizados

### Archivos de Respaldo (Carpeta Recycle/)

```
Resources/views/_Default/DatosMaestrosMedicos/MedicosVigentes/Recycle/
├── MedicoCreate_3.html.OLD.twig
├── MedicoCreate_3.html.twig
├── MedicoCreate_3_SUB.html.twig
└── Dinamico_SucursalUnidadServicio.html.twig
```

**Recomendación:** ✅ **ELIMINAR** - Son versiones antiguas, hay versiones actuales en uso.

---

### Archivos .OLD

```
Form/Edit/Edit_3.html.OLD.twig
Form/Crear/MedicoCreate_3.html.OLD.twig
```

**Recomendación:** ✅ **ELIMINAR** - Respaldos antiguos de archivos editados.

---

### Controladores de Respaldo

```
Controller/_Default/DatosMaestrosMedicos/DMMNuevoController.php.bckup
```

**Recomendación:** ✅ **ELIMINAR** - Backup del controlador activo.

---

### Vistas de Prueba

```
Resources/views/_Default/indexTest.html.twig
Resources/views/_Default/MedicosVigentes/index.vigentesTest.html.twig
```

**Recomendación:** ⚠️ **REVISAR** - Si no están referenciadas en ninguna ruta, eliminar.

---

### Controlador Base Vacío

```php
// Controller/DefaultController.php
class DefaultController extends \Rebsol\HermesBundle\Controller\DefaultController {
}
```

**Recomendación:** ⚠️ **MANTENER TEMPORALMENTE** - Aunque está vacío, puede ser requerido por la estructura de bundles de Symfony 2. Revisar si se usa en routing o DI.

---

### Repository Base

```php
// Repository/DefaultRepository.php
```

**Recomendación:** ✅ **MANTENER** - Extendido por otros repositorios, probablemente tiene lógica base.

---

### Archivos de Traducción No Utilizados

```
Resources/translations/messages.fr.xlf
```

**Recomendación:** ❓ **EVALUAR** - Si la aplicación no soporta francés, eliminar.

---

### Vistas Duplicadas

Hay **dos copias** de muchas vistas:
- `_Default/MedicosVigentes/` (usuarios)
- `_Default/DatosMaestrosMedicos/MedicosVigentes/` (profesionales)

**Análisis:**
```
_Default/MedicosVigentes/Form/Add/UserAdd_*.html.twig
_Default/DatosMaestrosMedicos/MedicosVigentes/Form/Add/UserAdd_*.html.twig

_Default/MedicosVigentes/Form/Ver/ver_*.html.twig
_Default/DatosMaestrosMedicos/MedicosVigentes/Form/Ver/ver_*.html.twig

etc.
```

**Recomendación:** 🔄 **CONSOLIDAR EN MIGRACIÓN** - Evaluar si realmente se necesitan dos versiones o se pueden unificar con parámetros.

---

### Vistas Layout Específicas

```
Resources/views/_Default/layoutMantenedorInfo.html.twig
Resources/views/_Default/layoutformulariosajax.html.twig
Resources/views/_Default/sublayout.html.twig
```

**Recomendación:** ⚠️ **REVISAR USO** - Si se usan, migrar a estructura moderna de Twig. Si no, eliminar.

---

## 📋 Resumen de Archivos a Considerar

### ✅ Definitivamente Eliminar

1. `Resources/views/.../Recycle/` - Toda la carpeta
2. Archivos `.OLD.twig`
3. `DMMNuevoController.php.bckup`
4. `indexTest.html.twig` (si no está en routing)
5. `index.vigentesTest.html.twig` (si no está en routing)

### ⚠️ Revisar Antes de Decidir

1. `Controller/DefaultController.php` (vacío pero puede ser necesario)
2. Vistas duplicadas entre MedicosVigentes y DatosMaestrosMedicos
3. `messages.fr.xlf` (si no hay internacionalización francés)
4. Layouts específicos del bundle

### ✅ Mantener y Migrar

1. Todos los controladores en `_Default/DatosMaestrosMedicos/`
2. Repositorios `UsuariosRebsolRepository` y `PerfilRepository`
3. FormType `DMMType`, `addpType`, `addgType`, `FotoPnaturalType`
4. Vistas activas de formularios
5. Macros en `UI/Macros/`
6. Configuraciones YAML

---

## 🚀 Plan de Migración a Symfony 6

### Fase 1: Preparación y Análisis ✅ (Completado con esta documentación)

- [x] Documentar arquitectura actual
- [x] Identificar dependencias
- [x] Listar archivos no utilizados
- [x] Mapear entidades y relaciones
- [x] Documentar reglas de negocio

---

### Fase 2: Estructura Base

#### 2.1. Reestructurar Bundle como Módulo

**Symfony 6 no usa bundles de la misma forma.**

**Opción A: Convertir a estructura moderna**
```
src/
├── Controller/
│   └── Admin/
│       └── User/
│           ├── UserController.php
│           ├── UserCreateController.php
│           ├── UserEditController.php
│           ├── UserViewController.php
│           ├── UserDeleteController.php
│           └── UserGroupController.php
├── Entity/
│   └── (ya existen en HermesBundle)
├── Repository/
│   ├── UserRepository.php
│   └── ProfileRepository.php
├── Form/
│   └── Type/
│       ├── UserType.php
│       ├── ProfileAssignmentType.php
│       └── UserPhotoType.php
├── Service/
│   ├── UserManagementService.php
│   ├── ProfileManagementService.php
│   ├── LicenseValidationService.php
│   └── ZoomIntegrationService.php
└── Validator/
    └── Constraints/
        ├── UniqueUsername.php
        └── ValidRut.php
```

**Opción B: Mantener como Bundle (más fácil migración incremental)**
```
src/AdministradorUsuarios/
├── Controller/
├── Service/
├── Form/
└── Resources/
```

---

### Fase 3: Migrar Controladores

#### Cambios Necesarios en Controladores:

**ANTES (Symfony 2):**
```php
namespace Rebsol\AdministradorUsuariosBundle\Controller\_Default\DatosMaestrosMedicos;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DMMNuevoController extends DatosMaestrosMedicosController {
    
    public function nuevoUsuarioAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DMMType::class, $entity, $options);
        // ...
    }
}
```

**DESPUÉS (Symfony 6):**
```php
namespace App\Controller\Admin\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UserManagementService;

#[Route('/admin/usuarios')]
class UserCreateController extends AbstractController {
    
    public function __construct(
        private EntityManagerInterface $em,
        private UserManagementService $userService
    ) {}
    
    #[Route('/nuevo', name: 'admin_user_new')]
    public function new(Request $request): Response {
        $form = $this->createForm(UserType::class, $entity, $options);
        // ...
    }
}
```

**Cambios clave:**
1. ✅ Inyección de dependencias en constructor
2. ✅ Atributos PHP 8 para routing
3. ✅ `AbstractController` en lugar de `Controller`
4. ✅ Type hints estrictos
5. ✅ Lógica pesada movida a servicios

---

### Fase 4: Migrar Formularios

**ANTES (Symfony 2):**
```php
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DMMType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('nombreUsuario', TextType::class, array(
                'label' => 'Usuario',
                'required' => true,
                'attr' => [
                    'readonly' => true
                ]
            ));
    }
}
```

**DESPUÉS (Symfony 6):**
```php
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('nombreUsuario', TextType::class, [
                'label' => 'Usuario',
                'required' => true,
                'attr' => [
                    'readonly' => true
                ]
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isNew' => false,
            'empresa' => null,
        ]);
        
        $resolver->setAllowedTypes('isNew', 'bool');
        $resolver->setAllowedTypes('empresa', ['null', Empresa::class]);
    }
}
```

**Cambios clave:**
1. ✅ Arrays cortos `[]` en lugar de `array()`
2. ✅ Type hints `void` y de retorno
3. ✅ `configureOptions()` más robusto
4. ✅ Validación de tipos de opciones

---

### Fase 5: Crear Servicios para Lógica de Negocio

**Extraer lógica pesada de controladores a servicios:**

```php
// src/Service/UserManagementService.php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagementService {
    
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private LicenseValidationService $licenseValidator
    ) {}
    
    public function createUser(array $data): UsuariosRebsol {
        // Validar licencias disponibles
        if (!$this->licenseValidator->hasAvailableLicenses()) {
            throw new \Exception('No hay licencias disponibles');
        }
        
        // Crear entidades
        $persona = $this->createPersona($data);
        $pnatural = $this->createPnatural($data, $persona);
        $usuario = $this->createUsuarioRebsol($data, $persona);
        
        // Guardar historial contraseña
        $this->savePasswordHistory($usuario, $data['password']);
        
        // Asignar roles y servicios
        $this->assignRoles($usuario, $data);
        $this->assignServices($usuario, $data);
        
        $this->em->flush();
        
        return $usuario;
    }
    
    public function updateUser(UsuariosRebsol $usuario, array $data): void {
        // Lógica de actualización
        $cambiosPassword = $this->updateBasicData($usuario, $data);
        $cambiosServicios = $this->updateServices($usuario, $data);
        
        // Si cambió algo crítico, cerrar sesión
        if ($cambiosPassword || $cambiosServicios) {
            $this->forceLogout($usuario);
        }
        
        $this->em->flush();
    }
    
    public function deleteUser(UsuariosRebsol $usuario): void {
        // Inactivación lógica
        $usuario->setIdEstadoUsuario($this->getInactiveState());
        // ... inactivar relaciones
        
        $this->forceLogout($usuario, 'Usuario desactivado');
        $this->em->flush();
    }
    
    private function forceLogout(UsuariosRebsol $usuario, string $message = null): void {
        // Implementar cierre de sesión
        // (anteriormente botarUsuarioRebsol())
    }
}
```

---

### Fase 6: Migrar Repositorios

**ANTES (Symfony 2):**
```php
class UsuariosRebsolRepository extends DefaultRepository {
    
    public function DatosMaestrosMedicos() {
        $em = $this->getManager();
        $query = $em->createQuery("...");
        return $query->getResult();
    }
}
```

**DESPUÉS (Symfony 6):**
```php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository {
    
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, UsuariosRebsol::class);
    }
    
    public function findAllUsersWithDetails(): array {
        return $this->createQueryBuilder('u')
            ->select('u', 'p', 'pn')
            ->innerJoin('u.idPersona', 'p')
            ->innerJoin('p.pnatural', 'pn')
            ->where('u.idEstadoUsuario = :estado')
            ->setParameter('estado', 1)
            ->orderBy('pn.apellidoPaterno', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    public function findUserSpecialties(UsuariosRebsol $user): array {
        return $this->createQueryBuilder('u')
            ->select('e.nombreEspecialidadMedica')
            ->innerJoin('u.relEspecialidadProfesional', 'rep')
            ->innerJoin('rep.idEspecialidadMedica', 'e')
            ->where('u.id = :userId')
            ->andWhere('rep.idEstado = :estado')
            ->setParameter('userId', $user->getId())
            ->setParameter('estado', 1)
            ->getQuery()
            ->getResult();
    }
}
```

---

### Fase 7: Actualizar Seguridad

**ANTES (security.yml Symfony 2):**
```yaml
security:
    encoders:
        Rebsol\HermesBundle\Entity\UsuariosRebsol:
            algorithm: bcrypt
```

**DESPUÉS (config/packages/security.yaml Symfony 6):**
```yaml
security:
    password_hashers:
        App\Entity\UsuariosRebsol:
            algorithm: auto
            
    providers:
        app_user_provider:
            entity:
                class: App\Entity\UsuariosRebsol
                property: nombreUsuario
    
    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            custom_authenticator: App\Security\LoginAuthenticator
            logout:
                path: app_logout
```

**Actualizar código que usa encoder:**
```php
// ANTES
$factory = $this->get('security.encoder_factory');
$encoder = $factory->getEncoder($usuario);
$password = $encoder->encodePassword($plainPassword, $usuario->getSalt());

// DESPUÉS
$hashedPassword = $this->passwordHasher->hashPassword(
    $usuario,
    $plainPassword
);
```

---

### Fase 8: Migrar Vistas Twig

**Cambios menores pero importantes:**

**ANTES:**
```twig
{{ form_widget(form.nombreUsuario, {'attr': {'class': 'form-control'}}) }}

{% if app.user.id == usuario.id %}
```

**DESPUÉS (sin cambios mayores en sintaxis):**
```twig
{{ form_widget(form.nombreUsuario, {'attr': {'class': 'form-control'}}) }}

{% if app.user.id == usuario.id %}
```

**Pero considerar:**
1. ✅ Usar Webpack Encore para assets
2. ✅ Modernizar JavaScript (ES6+)
3. ✅ Migrar de jQuery a vanilla JS o Vue/React si es posible
4. ✅ Usar Stimulus para interactividad

---

### Fase 9: Migrar Routing

**ANTES (routing.yml):**
```yaml
AdministradorUsuarios:
    path:  /
    defaults: { _controller: AdministradorUsuariosBundle:_Default/DatosMaestrosMedicos/DatosMaestrosMedicos:usuarioIndex}

AdministradorUsuario_New:
    path:  /DatosMaestrosMedicos/NuevoUsuario
    defaults: { _controller: AdministradorUsuariosBundle:_Default/DatosMaestrosMedicos/DMMNuevo:nuevoUsuario}
```

**DESPUÉS (Atributos PHP 8):**
```php
#[Route('/admin/usuarios', name: 'admin_users_')]
class UserController extends AbstractController {
    
    #[Route('/', name: 'index')]
    public function index(): Response {
        // ...
    }
    
    #[Route('/nuevo', name: 'new')]
    public function new(): Response {
        // ...
    }
    
    #[Route('/{id}/editar', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(int $id): Response {
        // ...
    }
}
```

---

### Fase 10: Validaciones Modernas

**ANTES (constraints en FormType):**
```php
'constraints' => array(
    new validaform\NotBlank(array('message' => 'Campo requerido')),
    new validaform\Length(array('max' => 60))
)
```

**DESPUÉS (Atributos en Entity):**
```php
use Symfony\Component\Validator\Constraints as Assert;

class Persona {
    
    #[Assert\NotBlank(message: 'Campo requerido')]
    #[Assert\Length(max: 60)]
    private string $nombre;
    
    #[Assert\Email(message: 'Email inválido')]
    #[Assert\NotBlank]
    private string $correoElectronico;
}
```

---

## 🎯 Prioridades de Migración

### Alto (Crítico para funcionamiento)

1. ✅ **Controladores principales** (Nuevo, Editar, Ver, Listar)
2. ✅ **UsuariosRebsolRepository** (queries complejas)
3. ✅ **DMMType** (formulario principal)
4. ✅ **Lógica de seguridad** (contraseñas, bloqueos)
5. ✅ **Gestión de licencias**

### Medio (Importante)

6. ⚠️ **Grupos y Perfiles** (DMMAddController, addpType)
7. ⚠️ **Integración Zoom** (vincularZoom)
8. ⚠️ **Macros JavaScript** (validaciones cliente)
9. ⚠️ **Exportar a Excel**
10. ⚠️ **Subir foto de perfil**

### Bajo (Puede postponerse)

11. ⏳ **Controladores de dependencias AJAX** (pueden usarse como están)
12. ⏳ **Vistas de prueba/test**
13. ⏳ **Desbloqueo de usuarios** (funcionalidad admin)

---

## 📝 Checklist de Migración

### Pre-Migración
- [ ] Backup completo de base de datos
- [ ] Documentación de configuración actual
- [ ] Lista de dependencias externas
- [ ] Plan de rollback

### Migración de Código
- [ ] Crear estructura moderna de directorios
- [ ] Migrar entidades (si no están en HermesBundle)
- [ ] Crear servicios de negocio
- [ ] Migrar repositorios
- [ ] Migrar controladores uno por uno
- [ ] Migrar formularios
- [ ] Actualizar vistas Twig
- [ ] Migrar validaciones a atributos
- [ ] Actualizar configuración de seguridad
- [ ] Migrar routing a atributos

### Testing
- [ ] Tests unitarios para servicios
- [ ] Tests funcionales para controladores
- [ ] Tests de integración para flujos completos
- [ ] Validación manual de cada formulario
- [ ] Pruebas de seguridad (contraseñas, bloqueos)
- [ ] Pruebas de permisos (grupos, perfiles)

### Post-Migración
- [ ] Eliminar código obsoleto
- [ ] Documentación actualizada
- [ ] Capacitación a equipo
- [ ] Monitoreo de errores en producción

---

## ⚠️ Puntos Críticos a No Olvidar

### 1. Sistema de Contraseñas
- Mantener compatibilidad con contraseñas existentes
- Verificar que el algoritmo de hash sea compatible
- Migrar gradualmente a `auto` hasher

### 2. Gestión de Sesiones
- El método `botarUsuarioRebsol()` debe ser reimplementado
- Considerar usar Security Events de Symfony 6
- Implementar listener para cerrar sesiones remotamente

### 3. Licencias
- Validación debe ser atómica (evitar race conditions)
- Considerar usar locks de base de datos
- Cachear conteo de licencias para performance

### 4. Perfiles y Grupos
- Lógica compleja de inclusión/exclusión
- **Estado INACTIVO = EXCLUSIÓN explícita**
- Mantener esta lógica exactamente igual

### 5. Servicios Activos
- Solo UN servicio puede estar activo
- Al cambiar servicio activo → cerrar sesión
- Validar en cada request el servicio actual

### 6. Especialidades Bloqueadas
- Especialidades con fecha NO se pueden desasignar
- Solo se pueden BLOQUEAR (estado = 2)
- Mostrar claramente en interfaz

### 7. Integración Zoom
- Manejar estados asincrónicos
- Validar que API responda
- Timeout razonable en requests
- Manejo de errores robusto

---

## 📚 Recursos Adicionales Recomendados

### Documentación Oficial
- [Symfony 6 Upgrade Guide](https://symfony.com/doc/current/setup/upgrade_major.html)
- [Doctrine ORM 2.x Upgrade](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/changelog/migration_2_7.html)
- [PHP 8 Migration Guide](https://www.php.net/manual/en/migration80.php)

### Librerías a Considerar
- **Symfony UX:** Para modernizar interfaz
- **API Platform:** Si se necesita API REST
- **EasyAdmin:** Para reemplazar parte del CRUD manual
- **Webpack Encore:** Para assets modernos

### Testing
- **PHPUnit 9+:** Tests unitarios
- **Symfony Panther:** Tests browser
- **Faker:** Datos de prueba

---

## 🎓 Conclusiones

### Complejidad del Bundle
**Alta** - Este bundle maneja lógica de negocio crítica:
- Seguridad y autenticación
- Permisos complejos (grupos → perfiles → módulos)
- Validaciones de negocio específicas
- Integraciones externas (Zoom)
- Gestión de licencias

### Tiempo Estimado de Migración
- **Setup inicial:** 1-2 semanas
- **Migración core:** 4-6 semanas
- **Testing exhaustivo:** 2-3 semanas
- **Ajustes y refinamiento:** 2-4 semanas
- **Total:** **2-4 meses** (con 1 desarrollador)

### Riesgos Principales
1. ⚠️ **Pérdida de sesiones activas** durante deploy
2. ⚠️ **Incompatibilidad de contraseñas** existentes
3. ⚠️ **Lógica de permisos** compleja puede tener bugs
4. ⚠️ **Race conditions** en asignación de licencias
5. ⚠️ **Integración Zoom** puede fallar

### Recomendación Final
1. **Migrar en fases** - No todo a la vez
2. **Tests exhaustivos** - Cada funcionalidad
3. **Mantener ambas versiones** temporalmente
4. **Feature flags** para ir activando funcionalidad
5. **Rollback plan** robusto

---

## 📞 Contacto para Dudas

Para cualquier duda durante la migración, referirse a esta documentación o al equipo de arquitectura.

**Documentación creada:** Diciembre 2025  
**Versión:** 1.0  
**Estado:** Completado ✅

---

**FIN DE LA DOCUMENTACIÓN** - AdministradorUsuariosBundle
