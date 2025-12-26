# 📝 Fase 5: Formularios y Repositorios

## 🎯 Objetivo
Migrar los formularios (FormTypes) y repositorios del módulo a Symfony 6.

---

## 📋 Parte 1: Formularios

### Formularios a Migrar
1. **DMMType** → **UserType** / **ProfessionalType**
2. **addpType** → **ProfileAssignmentType**
3. **addgType** → **GroupAssignmentType**
4. **FotoPnaturalType** → **UserPhotoType**

---

## 1️⃣ UserType.php (Formulario Principal)

```php
<?php

namespace App\Form\Type\User;

use App\Entity\Main\UsuariosRebsol;
use App\Entity\Main\Rol;
use App\Entity\Main\Cargo;
use App\Entity\Main\TipoMedico;
use App\Entity\Main\Sucursal;
use App\Entity\Main\Unidad;
use App\Entity\Main\Servicio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'];
        $isProfessional = $options['is_professional'];
        $empresa = $options['empresa'];

        // Sección: Datos Personales
        $builder
            // Nombre completo
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 60
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El nombre es requerido']),
                    new Assert\Length(['max' => 60])
                ]
            ])
            
            ->add('apellidoPaterno', TextType::class, [
                'label' => 'Apellido Paterno',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 60
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Apellido paterno es requerido']),
                    new Assert\Length(['max' => 60])
                ]
            ])
            
            ->add('apellidoMaterno', TextType::class, [
                'label' => 'Apellido Materno',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 60
                ],
                'constraints' => [
                    new Assert\Length(['max' => 60])
                ]
            ])
            
            // RUT o identificación
            ->add('identificacion', TextType::class, [
                'label' => 'RUT',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '12345678-9',
                    'data-validate' => 'rut'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'RUT es requerido'])
                ]
            ])
            
            // Sexo
            ->add('sexo', EntityType::class, [
                'label' => 'Sexo',
                'class' => 'App\Entity\Main\Sexo',
                'choice_label' => 'nombre',
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'query_builder' => function($repository) use ($empresa) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.idEmpresa = :empresa')
                        ->andWhere('s.idEstado = 1')
                        ->setParameter('empresa', $empresa)
                        ->orderBy('s.nombre', 'ASC');
                }
            ])
            
            // Fecha de nacimiento
            ->add('fechaNacimiento', DateType::class, [
                'label' => 'Fecha de Nacimiento',
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Fecha de nacimiento requerida']),
                    new Assert\LessThan([
                        'value' => 'today',
                        'message' => 'La fecha debe ser anterior a hoy'
                    ])
                ]
            ])
            
            // Contacto
            ->add('telefonoMovil', TextType::class, [
                'label' => 'Teléfono Móvil',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+56 9 1234 5678'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Teléfono móvil es requerido']),
                    new Assert\Length(['min' => 8, 'max' => 15])
                ]
            ])
            
            ->add('telefonoFijo', TextType::class, [
                'label' => 'Teléfono Fijo',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\Length(['min' => 8, 'max' => 15])
                ]
            ])
            
            ->add('correoElectronico', EmailType::class, [
                'label' => 'Correo Electrónico',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'usuario@ejemplo.com'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Email es requerido']),
                    new Assert\Email(['message' => 'Email inválido'])
                ]
            ])
            
            ->add('correoElectronico2', EmailType::class, [
                'label' => 'Correo Electrónico Secundario',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\Email(['message' => 'Email secundario inválido'])
                ]
            ]);

        // Sección: Datos de Acceso
        $builder
            ->add('nombreUsuario', TextType::class, [
                'label' => 'Nombre de Usuario',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => !$isNew,
                    'data-validate' => 'username'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Usuario es requerido']),
                    new Assert\Length(['min' => 4, 'max' => 60])
                ]
            ]);

        // Contraseña solo en creación o si se va a cambiar
        if ($isNew) {
            $builder
                ->add('password', PasswordType::class, [
                    'label' => 'Contraseña',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password'
                    ],
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Contraseña es requerida']),
                        new Assert\Length(['min' => 8, 'message' => 'Mínimo 8 caracteres'])
                    ]
                ])
                ->add('passwordConfirm', PasswordType::class, [
                    'label' => 'Confirmar Contraseña',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password'
                    ]
                ]);
        }

        // Sección: Datos Profesionales
        $builder
            ->add('rol', EntityType::class, [
                'label' => 'Rol',
                'class' => Rol::class,
                'choice_label' => 'nombre',
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'query_builder' => function($repository) use ($empresa, $isProfessional) {
                    $qb = $repository->createQueryBuilder('r')
                        ->where('r.idEmpresa = :empresa')
                        ->andWhere('r.idEstado = 1')
                        ->setParameter('empresa', $empresa);
                    
                    if ($isProfessional) {
                        $qb->andWhere('r.profClinico = 1');
                    }
                    
                    return $qb->orderBy('r.nombre', 'ASC');
                }
            ])
            
            ->add('cargo', EntityType::class, [
                'label' => 'Cargo',
                'class' => Cargo::class,
                'choice_label' => 'nombre',
                'required' => false,
                'attr' => ['class' => 'form-select'],
                'query_builder' => function($repository) use ($empresa) {
                    return $repository->createQueryBuilder('c')
                        ->where('c.idEmpresa = :empresa')
                        ->andWhere('c.idEstado = 1')
                        ->setParameter('empresa', $empresa)
                        ->orderBy('c.nombre', 'ASC');
                }
            ])
            
            ->add('tipoMedico', EntityType::class, [
                'label' => 'Tipo Médico',
                'class' => TipoMedico::class,
                'choice_label' => 'nombre',
                'required' => false,
                'attr' => ['class' => 'form-select']
            ]);

        // Especialidades (solo profesionales, solo edición)
        if ($isProfessional && !$isNew) {
            $builder->add('especialidades', EntityType::class, [
                'label' => 'Especialidades',
                'class' => 'App\Entity\Main\EspecialidadMedica',
                'choice_label' => 'nombre',
                'multiple' => true,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-select',
                    'size' => 5
                ],
                'query_builder' => function($repository) use ($empresa) {
                    return $repository->createQueryBuilder('e')
                        ->where('e.idEmpresa = :empresa')
                        ->andWhere('e.idEstado = 1')
                        ->setParameter('empresa', $empresa)
                        ->orderBy('e.nombre', 'ASC');
                }
            ]);
        }

        // Sección: Ubicación y Servicios
        $builder
            ->add('sucursal', EntityType::class, [
                'label' => 'Sucursal',
                'class' => Sucursal::class,
                'choice_label' => 'nombre',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                    'data-action' => 'change->unit-branch#loadUnits'
                ],
                'query_builder' => function($repository) use ($empresa) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.idEmpresa = :empresa')
                        ->andWhere('s.idEstado = 1')
                        ->setParameter('empresa', $empresa)
                        ->orderBy('s.nombre', 'ASC');
                }
            ])
            
            ->add('unidad', EntityType::class, [
                'label' => 'Unidad',
                'class' => Unidad::class,
                'choice_label' => 'nombre',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                    'data-action' => 'change->service-unit#loadServices'
                ]
            ])
            
            ->add('servicio', EntityType::class, [
                'label' => 'Servicio',
                'class' => Servicio::class,
                'choice_label' => 'nombre',
                'required' => true,
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'is_new' => false,
            'is_professional' => false,
            'empresa' => null,
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
        $resolver->setAllowedTypes('is_professional', 'bool');
        $resolver->setRequired('empresa');
    }
}
```

---

## 2️⃣ ProfileAssignmentType.php

```php
<?php

namespace App\Form\Type\User;

use App\Entity\Main\Grupo;
use App\Entity\Main\Perfil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $empresa = $options['empresa'];

        $builder
            ->add('groups', EntityType::class, [
                'label' => 'Grupos',
                'class' => Grupo::class,
                'choice_label' => 'nombre',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'size' => 8,
                    'data-action' => 'change->profile-assignment#updateProfiles'
                ],
                'query_builder' => function($repository) use ($empresa) {
                    return $repository->createQueryBuilder('g')
                        ->where('g.idEmpresa = :empresa')
                        ->andWhere('g.idEstado = 1')
                        ->setParameter('empresa', $empresa)
                        ->orderBy('g.nombre', 'ASC');
                }
            ])
            
            ->add('profiles', EntityType::class, [
                'label' => 'Perfiles',
                'class' => Perfil::class,
                'choice_label' => 'nombre',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'size' => 8
                ],
                'query_builder' => function($repository) use ($empresa) {
                    return $repository->createQueryBuilder('p')
                        ->where('p.idEmpresa = :empresa')
                        ->andWhere('p.idEstado = 1')
                        ->setParameter('empresa', $empresa)
                        ->orderBy('p.nombre', 'ASC');
                },
                'help' => 'Tip: Seleccionar un perfil INACTIVO lo excluye aunque venga del grupo'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null
        ]);
        
        $resolver->setRequired('empresa');
    }
}
```

---

## 📦 Parte 2: Repositorios

### Repositorios a Actualizar
1. **UsuariosRebsolRepository**
2. **PerfilRepository**
3. **GrupoRepository**

---

## 1️⃣ UsuariosRebsolRepository.php (Actualizado)

```php
<?php

namespace App\Repository;

use App\Entity\Main\UsuariosRebsol;
use App\Entity\Main\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UsuariosRebsolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuariosRebsol::class);
    }

    /**
     * Encuentra todos los usuarios con datos relacionados
     */
    public function findAllUsersWithDetails(Empresa $empresa, bool $onlyProfessionals = false): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u', 'p', 'pn', 'r', 'eu')
            ->innerJoin('u.idPersona', 'p')
            ->innerJoin('p.pnatural', 'pn')
            ->innerJoin('u.idRol', 'r')
            ->innerJoin('u.idEstadoUsuario', 'eu')
            ->where('p.idEmpresa = :empresa')
            ->setParameter('empresa', $empresa);

        if ($onlyProfessionals) {
            $qb->andWhere('r.profClinico = 1');
        }

        return $qb
            ->orderBy('pn.apellidoPaterno', 'ASC')
            ->addOrderBy('pn.apellidoMaterno', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Cuenta usuarios activos (para licencias)
     */
    public function countActiveUsers(Empresa $empresa): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->innerJoin('u.idPersona', 'p')
            ->innerJoin('u.idEstadoUsuario', 'eu')
            ->where('p.idEmpresa = :empresa')
            ->andWhere('eu.nombre = :activo')
            ->setParameter('empresa', $empresa)
            ->setParameter('activo', 'ACTIVO')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Cuenta total de usuarios
     */
    public function countTotalUsers(Empresa $empresa): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->innerJoin('u.idPersona', 'p')
            ->where('p.idEmpresa = :empresa')
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Cuenta usuarios bloqueados
     */
    public function countBlockedUsers(Empresa $empresa): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->innerJoin('u.idPersona', 'p')
            ->innerJoin('u.idEstadoUsuario', 'eu')
            ->where('p.idEmpresa = :empresa')
            ->andWhere('eu.nombre = :bloqueado')
            ->setParameter('empresa', $empresa)
            ->setParameter('bloqueado', 'BLOQUEADO')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Cuenta profesionales
     */
    public function countProfessionals(Empresa $empresa): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->innerJoin('u.idPersona', 'p')
            ->innerJoin('u.idRol', 'r')
            ->where('p.idEmpresa = :empresa')
            ->andWhere('r.profClinico = 1')
            ->setParameter('empresa', $empresa)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Obtiene servicios de un usuario
     */
    public function getUserServices(int $userId): array
    {
        return $this->createQueryBuilder('u')
            ->select('s.nombre', 'rus.estado', 'suc.nombre as sucursal', 'un.nombre as unidad')
            ->innerJoin('u.relUsuarioServicio', 'rus')
            ->innerJoin('rus.idServicio', 's')
            ->innerJoin('s.idUnidad', 'un')
            ->innerJoin('un.idSucursal', 'suc')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtiene últimos logins
     */
    public function getLastLogins(int $userId, int $limit = 10): array
    {
        return $this->_em->createQueryBuilder()
            ->select('ull.fechaLogin', 'ull.ip', 'ull.navegador')
            ->from('App\Entity\Main\UsuarioLoginLog', 'ull')
            ->where('ull.idUsuario = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('ull.fechaLogin', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Verifica si un username existe
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.nombreUsuario = :username')
            ->setParameter('username', $username);

        if ($excludeId) {
            $qb->andWhere('u.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Busca usuario por username
     */
    public function findByUsername(string $username): ?UsuariosRebsol
    {
        return $this->createQueryBuilder('u')
            ->where('u.nombreUsuario = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
```

---

## 2️⃣ PerfilRepository.php (Actualizado)

```php
<?php

namespace App\Repository;

use App\Entity\Main\Perfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PerfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Perfil::class);
    }

    /**
     * Encuentra perfiles activos de un usuario (directos)
     */
    public function findActiveByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.relUsuarioPerfil', 'rup')
            ->where('rup.idUsuario = :userId')
            ->andWhere('rup.idEstado = 1')
            ->andWhere('p.idEstado = 1')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra perfiles inactivos (exclusiones)
     */
    public function findInactiveByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.relUsuarioPerfil', 'rup')
            ->where('rup.idUsuario = :userId')
            ->andWhere('rup.idEstado = 0')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra perfiles de un grupo
     */
    public function findByGroup(int $groupId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.relGrupoPerfil', 'rgp')
            ->where('rgp.idGrupo = :groupId')
            ->andWhere('rgp.idEstado = 1')
            ->andWhere('p.idEstado = 1')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra perfiles de múltiples grupos
     */
    public function findByGroups(array $groupIds): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.relGrupoPerfil', 'rgp')
            ->where('rgp.idGrupo IN (:groupIds)')
            ->andWhere('rgp.idEstado = 1')
            ->andWhere('p.idEstado = 1')
            ->setParameter('groupIds', $groupIds)
            ->getQuery()
            ->getResult();
    }
}
```

---

## ⏱️ Tiempo Estimado

- **UserType:** 1.5 días
- **ProfileAssignmentType:** 0.5 días
- **Repositorios:** 1 día
- **Testing:** 1 día
- **Total:** **4 días**

---

## ➡️ Siguiente Paso

[06 - Seguridad, Routing y Vistas](./MIGRACION-06-SEGURIDAD-VISTAS.md)

---

**Fase:** 5 de 10  
**Prioridad:** 🔴 Alta  
**Riesgo:** 🟡 Medio
