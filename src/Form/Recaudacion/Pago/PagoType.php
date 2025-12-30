<?php

namespace App\Form\Recaudacion\Pago;

use Rebsol\HermesBundle\Entity\Comuna;
use Rebsol\HermesBundle\Entity\PersonaDomicilio;
use Rebsol\HermesBundle\Entity\Sexo;
use Rebsol\HermesBundle\Entity\TipoIdentificacionExtranjero;
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
use Rebsol\HermesBundle\Entity\Pais;

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
                    'class' => TipoIdentificacionExtranjero::class,
                    'choice_label' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'placeholder' => 'Seleccionar Documento',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->Where('s.idEstado = ?2')
                            ->setParameter(2, $options['estado_activado']);
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
                    'class' => Sexo::class,
                    'choice_label' => 'nombreSexo',
                    'required' => true,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'placeholder' => 'Seleccionar Sexo',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        //debe definirse en las opciones del type "idEmpresa" 'iEmpresa' => null, para tener la variable libre, y recibir desde el controlador su valor, y previamente en el controlador, para lograr traer el valor de la empresa.
                        return $repository->createQueryBuilder('s')
                            ->where('s.idEmpresa = ?1')
                            ->andWhere('s.idEstado = ?2')
                            ->andWhere('s.esPersona != 0')
                            ->setParameter(1, $options['iEmpresa'])
                            ->setParameter(2, $options['estado_activado']);
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
                    , 'class' => Pais::class
                    , 'choice_label' => 'nombrePais'
                    , 'required' => true
                    , 'mapped' => false
                    , 'placeholder' => 'Seleccionar País'
                    , 'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use($options) {
                        return $repository->createQueryBuilder('s')
                            ->Where('s.idEstado = ?2')
                            ->setParameter(2, $options['estado_activado']);
                        }
                    , 'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                        )
                )
            )
            ->add('comuna', EntityType::class, array(
                    'label' => 'Comuna',
                    'class' => Comuna::class,
                    'choice_label' => 'nombreComuna',
                    'required' => true,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'placeholder' => 'Seleccionar Comuna',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->Where('s.idEstado = ?2')
                            ->orderBy('s.nombreComuna', 'ASC')
                            ->setParameter(2, $options['estado_activado']);
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
            'data_class' => PersonaDomicilio::class,
            'validaform' => true,
            'iEmpresa' => null,
            'estado_activado' => null,
            'database_default' => null,
            'habilitarPaisExtranjero' => 0
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_PagoType';
    }

}
