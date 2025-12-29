<?php

declare(strict_types=1);

namespace App\Form\Type\AdminUser;

use App\Entity\Tenant\Gender;
use App\Entity\Tenant\IdentificationType;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Role;
use App\Entity\Tenant\State;
use App\Enum\UserTypeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulario para crear/editar usuarios
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];
        $requirePassword = $options['require_password'];

        // === DATOS DE PERSONA ===
        
        $builder
            ->add('documentType', EntityType::class, [
                'label' => 'Tipo de Documento',
                'class' => IdentificationType::class,
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => 'Seleccionar Documento',
                'attr' => [
                    'class' => 'form-select',
                ],
                'mapped' => false, // No está en Member, se maneja en controller
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El tipo de documento es requerido']),
                ],
            ])
            ->add('identification', TextType::class, [
                'label' => 'RUT/Identificación',
                'required' => true,
                'mapped' => false, // No está en Member, está en Person
                'attr' => [
                    'placeholder' => 'Ej: 12345678-9',
                    'class' => 'form-control',
                    'maxlength' => 20,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La identificación es requerida']),
                    new Assert\Length(['max' => 20]),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
                'mapped' => false, // Está en Person
                'attr' => [
                    'placeholder' => 'Ej: Juan',
                    'class' => 'form-control',
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El nombre es requerido']),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Apellido Paterno',
                'required' => true,
                'mapped' => false, // Está en Person
                'attr' => [
                    'placeholder' => 'Ej: Pérez',
                    'class' => 'form-control',
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El apellido paterno es requerido']),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('secondLastName', TextType::class, [
                'label' => 'Apellido Materno',
                'required' => false,
                'mapped' => false, // Está en Person como middleName
                'attr' => [
                    'placeholder' => 'Ej: Gómez',
                    'class' => 'form-control',
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Principal',
                'required' => true,
                'mapped' => false, // Está en Person
                'attr' => [
                    'placeholder' => 'correo@ejemplo.com',
                    'class' => 'form-control',
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El email es requerido']),
                    new Assert\Email(['message' => 'El email no es válido']),
                    new Assert\Length(['max' => 180]),
                ],
            ])
            ->add('secondEmail', EmailType::class, [
                'label' => 'Email Secundario',
                'required' => false,
                'mapped' => false, // Está en Person como secondaryEmail
                'attr' => [
                    'placeholder' => 'correo2@ejemplo.com',
                    'class' => 'form-control',
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new Assert\Email(['message' => 'El email no es válido']),
                    new Assert\Length(['max' => 180]),
                ],
            ])
            ->add('mobilePhone', TextType::class, [
                'label' => 'Teléfono Móvil',
                'required' => true,
                'mapped' => false, // Está en Person
                'attr' => [
                    'placeholder' => 'Ej: +56912345678',
                    'class' => 'form-control',
                    'maxlength' => 20,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El teléfono móvil es requerido']),
                    new Assert\Length(['max' => 20]),
                ],
            ])
            ->add('landlinePhone', TextType::class, [
                'label' => 'Teléfono Fijo',
                'required' => false,
                'mapped' => false, // Está en Person como homePhone
                'attr' => [
                    'placeholder' => 'Ej: +56221234567',
                    'class' => 'form-control',
                    'maxlength' => 20,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 20]),
                ],
            ])
            ->add('birthDateAt', BirthdayType::class, [
                'label' => 'Fecha de Nacimiento',
                'required' => false,
                'mapped' => false, // Está en Person
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'input' => 'datetime_immutable',
            ])
            ->add('gender', EntityType::class, [
                'label' => 'Género',
                'class' => Gender::class,
                'choice_label' => 'name',
                'required' => true,
                'mapped' => false, // Está en Person
                'placeholder' => 'Seleccione género',
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El género es requerido']),
                ],
            ]);

        // === DATOS DE USUARIO ===

        $builder
            ->add('username', TextType::class, [
                'label' => 'Nombre de Usuario',
                'required' => true,
                'attr' => [
                    'placeholder' => $isEdit ? 'No se puede modificar' : 'Se genera automáticamente',
                    'class' => 'form-control',
                    'maxlength' => 180,
                    'autocomplete' => 'username',
                    'readonly' => true, // Readonly siempre (generado automáticamente)
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El username es requerido']),
                    new Assert\Length([
                        'min' => 4,
                        'max' => 180,
                        'minMessage' => 'El username debe tener al menos 4 caracteres',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9._-]+$/',
                        'message' => 'Solo se permiten letras, números, puntos, guiones y guiones bajos',
                    ]),
                ],
                'help' => $isEdit ? 'El username no puede ser modificado' : 'Se genera automáticamente: primera letra del nombre + apellido',
            ])
            ->add('userType', ChoiceType::class, [
                'label' => 'Tipo de Usuario',
                'required' => true,
                'choices' => [
                    'Profesional' => UserTypeEnum::PROFESSIONAL->value,
                    'Administrativo' => UserTypeEnum::ADMINISTRATIVE->value,
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El tipo de usuario es requerido']),
                ],
            ])
            ->add('role', EntityType::class, [
                'label' => 'Rol',
                'class' => Role::class,
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => 'Seleccione rol',
                'attr' => [
                    'class' => 'form-select',
                ],
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('r')
                        ->innerJoin('r.state', 's')
                        ->where('s.name = :active')
                        ->setParameter('active', 'ACTIVE')
                        ->orderBy('r.name', 'ASC');
                },
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El rol es requerido']),
                ],
            ]);

        // Estado solo en edición
        if ($isEdit) {
            $builder->add('state', EntityType::class, [
                'label' => 'Estado',
                'class' => State::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El estado es requerido']),
                ],
            ]);
        }

        // === CONTRASEÑA ===
        
        $passwordConstraints = [
            new Assert\Length([
                'min' => 8,
                'max' => 4096,
                'minMessage' => 'La contraseña debe tener al menos 8 caracteres',
            ]),
        ];

        if ($requirePassword) {
            $passwordConstraints[] = new Assert\NotBlank(['message' => 'La contraseña es requerida']);
        }

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => $requirePassword,
            'first_options' => [
                'label' => 'Contraseña',
                'attr' => [
                    'placeholder' => $isEdit ? 'Dejar vacío para no cambiar' : 'Mínimo 8 caracteres',
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                ],
                'constraints' => $passwordConstraints,
            ],
            'second_options' => [
                'label' => 'Confirmar Contraseña',
                'attr' => [
                    'placeholder' => 'Repetir contraseña',
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                ],
            ],
            'invalid_message' => 'Las contraseñas deben coincidir',
            'mapped' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // No mapear directamente a entidad
            'is_edit' => false,
            'require_password' => true,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
        $resolver->setAllowedTypes('require_password', 'bool');
    }
}
