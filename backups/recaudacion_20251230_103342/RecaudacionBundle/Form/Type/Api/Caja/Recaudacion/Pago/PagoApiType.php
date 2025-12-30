<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Api\Caja\Recaudacion\Pago;

use Rebsol\HermesBundle\Entity\Comuna;
use Rebsol\HermesBundle\Entity\PersonaDomicilio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as validaform;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 19/06/2014
 * Participantes:
 *
 */
class PagoApiType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $factory = $builder->getFormFactory();


        $builder
            ->add('rutdv', NumberType::class, array(
                'label' => 'Rut',
                'mapped' => false,
                'required' => true,
                'trim' => true
            ))
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
            ->add('comuna', EntityType::class, array(
                    'label' => 'Comuna',
                    'class' => Comuna::class,
                    'property' => 'nombreComuna',
                    'required' => true,
                    'mapped' => false,
                    'empty_value' => 'Seleccionar Comuna',
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
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array
        (
            'data_class' => PersonaDomicilio::class,
            'validaform' => true,
            'iEmpresa' => null,
            'estado_activado' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_PagoType';
    }

}
