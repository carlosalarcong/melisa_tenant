<?php

namespace App\Form\Recaudacion\Pago;

use App\Entity\Tenant\Municipality;
use App\Entity\Tenant\PersonAddress;
use App\Entity\Tenant\Gender;
use App\Entity\Tenant\IdentificationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as validaform;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Tenant\Country;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 * Participantes:
 *
 */
class PagoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('habilitarPaisExtranjero', HiddenType::class, array(
                    'required' => false,
                    'mapped' => false,
                    "data" => $options['habilitarPaisExtranjero']
            ))
            ->add('rutdv', NumberType::class, array(
                'label' => 'Rut',
                'mapped' => false,
                'required' => true,
                'trim' => true
            ))
            ->add('documento', EntityType::class, array(
                    'label' => 'documento',
                    'class' => IdentificationType::class,
                    'choice_label' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Documento',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->Where('s.isActive = :active')
                            ->setParameter('active', true);
                    },
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Debe definir un Sexo')),
                        )
                )
            )
            ->add('numeroDocumento', TextType::class, array(
                    'label' => 'Número Documento',
                    'required' => false,
                    'mapped' => false
                )
            )
            ->add('rutPersona', NumberType::class, array(
                'label' => 'Rut',
                'mapped' => false,
                'required' => false,
                'trim' => true
            ))
            ->add('digitoVerifivador', TextType::class, array(
                    'required' => true,
                    'mapped' => false,
                    'constraints' =>
                        array(
                            new validaform\Type(
                                array(
                                    'type' => 'string'
                                )
                            ),
                            new validaform\Length(
                                array(
                                    'min' => '1',
                                    'max' => '1',
                                    'minMessage' => 'Este debe tener {{ limit }} caracteres o más')
                            ),
                        )
                )
            )
            ->add('telefonoFijo', TextType::class, array(
                    'label' => 'Teléfono Fijo',
                    'required' => false,
                    'mapped' => false,
                    'constraints' =>
                        new validaform\Length(
                            array(
                                'min' => '8',
                                'max' => '10',
                                'minMessage' => 'Este campo debe tener {{ limit }} caracteres o más',
                                'maxMessage' => 'Este campo debe tener {{ limit }} caracteres máximo')
                        ),
                )
            )
            ->add('telefonoMovil', TextType::class, array(
                    'label' => 'Teléfono Contacto',
                    'required' => false,
                    'mapped' => false,
                    'constraints' =>
                        new validaform\Length(
                            array(
                                'min' => '8',
                                'max' => '10',
                                'minMessage' => 'Este campo debe tener {{ limit }} caracteres o más',
                                'maxMessage' => 'Este campo debe tener {{ limit }} caracteres máximo')
                        ),
                )
            )
            ->add('telefonoTrabajo', TextType::class, array(
                    'label' => 'Teléfono Contacto',
                    'required' => false,
                    'mapped' => false
                )
            )
            ->add('correoElectronico', EmailType::class, array(
                    'label' => 'Correo Electronico',
                    'required' => false,
                    'mapped' => false,
                    'trim' => true,
                    'constraints' =>
                        array(
                            new validaform\Email(array('message' => '"{{ correoElectronico }}" no es una dirección de correo electrónico válida.')),
                            new validaform\NotBlank(array('message' => 'Este valor no debe estár en blanco')),
                        )
                )
            )
            //++++++++++++++++++++++//
            //FIN CAMPOS PERSONA
            //CAMPOS PNATURAL
            // // CONTROLLER INYECCION -> Pnatural
            //++++++++++++++++++++++//
            ->add('nombrePnatural', TextType::class, array(
                    'label' => 'Nombre',
                    'mapped' => false,
                    'required' => true,
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Este valor no debe estár en blanco')),
                            new validaform\Length(
                                array(
                                    'max' => '60'))
                        )
                )
            )
            ->add('apellidoPaterno', TextType::class, array(
                    'label' => 'Apellido Paterno',
                    'mapped' => false,
                    'required' => true,
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Este valor no debe estár en blanco')),
                            new validaform\Length(
                                array(
                                    'max' => '45'))
                        )
                )
            )
            ->add('apellidoMaterno', TextType::class, array(
                    'label' => 'Apellido Materno',
                    'mapped' => false,
                    'required' => true,
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Este valor no debe estár en blanco')),
                            new validaform\Length(
                                array(
                                    'max' => '45'))
                        )
                )
            )
            ->add('fechaNacimiento', DateType::class, array(
                    'label' => 'Fecha Nacimiento',
                    'mapped' => false,
                    'required' => true,
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd-MM-yyyy',
                    'constraints' =>
                        array(
                            new validaform\Date(),
                            new validaform\NotBlank(array('message' => 'Este valor no debe estár en blanco')),
                        )
                )
            )
            //++++++++++++++++++++++//
            // FIN CAMPOS PNATURAL
            // CAMPOS SEXO
            // // CONTROLLER INYECCION -> Pnatural
            //++++++++++++++++++++++//
            ->add('idSexo', EntityType::class, array(
                    'label' => 'Sexo',
                    'class' => Gender::class,
                    'choice_label' => 'nombreSexo',
                    'required' => true,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Sexo',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->where('s.isPerson = :isPerson')
                            ->andWhere('s.isActive = :isActive')
                            ->setParameter('isPerson', true)
                            ->setParameter('isActive', true);
                    },
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Debe definir un Sexo')),
                        )
                )
            )
            //++++++++++++++++++++++//
            // FIN CAMPOS SEXO
            ->add('pais', EntityType::class, array(
                    'label' => 'Pais'
                    , 'class' => Country::class
                    , 'choice_label' => 'nombrePais'
                    , 'required' => true
                    , 'mapped' => false
                    , 'placeholder' => 'Seleccionar País'
                    , 'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use($options) {
                        return $repository->createQueryBuilder('s')
                            ->Where('s.isActive = :active')
                            ->setParameter('active', true);
                        }
                    , 'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                        )
                )
            )
            ->add('comuna', EntityType::class, array(
                    'label' => 'Comuna',
                    'class' => Municipality::class,
                    'choice_label' => 'nombreComuna',
                    'required' => true,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Comuna',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->Where('s.isActive = :active')
                            ->orderBy('s.name', 'ASC')
                            ->setParameter('active', true);
                    },
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Debe definir una Comuna')),
                        )
                )
            )
            ->add('direccion', TextType::class, array(
                    'label' => 'Dirección',
                    'mapped' => false,
                    'required' => false
                )
            )
            ->add('numero', TextType::class, array(
                    'label' => 'numero',
                    'mapped' => false,
                    'required' => false
                )
            )
            ->add('resto', TextType::class, array(
                    'label' => 'resto',
                    'mapped' => false,
                    'required' => false
                )
            )
            ->add('idPais2', NumberType::class, array(
                'mapped' => false,
                'required' => false
            ))
            ->add('empresaSolicitante', HiddenType::class, array(
                'mapped' => false,
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array
        (
            'data_class' => PersonAddress::class,
            'validaform' => true,
            'iEmpresa' => null,
            'estado_activado' => null,
            'habilitarPaisExtranjero' => 0
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_PagoType';
    }

}
