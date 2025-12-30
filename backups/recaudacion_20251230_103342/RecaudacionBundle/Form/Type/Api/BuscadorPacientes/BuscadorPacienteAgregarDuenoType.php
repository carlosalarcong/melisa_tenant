<?php

namespace Rebsol\CajaBundle\Form\Type\Api\BuscadorPacientes;

use Rebsol\HermesBundle\Entity\Comuna;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;

/**
 * @author jgutierrez
 * @version 1.0.0
 * Fecha Creación: 24/10/2013
 */
class BuscadorPacienteAgregarDuenoType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', TextType::class, array(
            'required' => false,
            'mapped' => false
        ))
            ->add('rut', TextType::class, array(
                'label' => 'Rut Paciente',
                'required' => false,
                'mapped' => false
            ))
            ->add('nombre', TextType::class, array(
                'label' => 'Telefono Trabajo',
                'required' => true,
                'mapped' => false,
                'constraints' =>
                    array(
                        new constraints\Length(
                            array(
                                'min' => 3,
                                'max' => 20,
                                'minMessage' => 'Este debe tener {{ limit }} caracteres o más',
                                'maxMessage' => 'Este debe tener {{ limit }} caracteres o menos',
                            )),
                        new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                    )
            ))
            ->add('apPaterno', TextType::class, array(
                'label' => 'Apellido Paterno',
                'required' => true,
                'mapped' => false,
                'constraints' =>
                    array(
                        new constraints\Length(
                            array(
                                'min' => 3,
                                'max' => 20,
                                'minMessage' => 'Este debe tener {{ limit }} caracteres o más',
                                'maxMessage' => 'Este debe tener {{ limit }} caracteres o menos',
                            )),
                        new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                    )
            ))
            ->add('apMaterno', TextType::class, array(
                'label' => 'Apellido Materno',
                'required' => true,
                'mapped' => false,
                'constraints' =>
                    array(
                        new constraints\Length(
                            array(
                                'min' => 3,
                                'max' => 20,
                                'minMessage' => 'Este debe tener {{ limit }} caracteres o más',
                                'maxMessage' => 'Este debe tener {{ limit }} caracteres o menos',
                            )),
                        new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                    )
            ))
            ->add('comuna', EntityType::class, array(
                'label' => 'Comuna',
                'class' => Comuna::class,
                'choice_label' => 'nombreComuna',
                'required' => true,
                'mapped' => false,
                'placeholder' => 'Seleccionar Comuna',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('s')
                        ->Where('s.idEstado = ?2')
                        ->setParameter(2, $options['estado_activado']);
                }
            ))
            ->add('correo1', EmailType::class, array(
                'label' => 'Correo Principal',
                'required' => true,
                'mapped' => false,
                'constraints' =>
                    array(
                        new constraints\Email(array('message' => 'No es una dirección de correo electrónico válida.')),
                        new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                    )
            ))
            ->add('telefonoFijo', TextType::class, array(
                'label' => 'Telefono Fijo',
                'required' => false,
                'mapped' => false
            ))
            ->add('telefonoMovil', TextType::class, array(
                'label' => 'Telefono Movil',
                'required' => false,
                'mapped' => false,
                'constraints' => array(new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),)
            ))
            ->add('telefonoTrabajo', TextType::class, array(
                'label' => 'Telefono Trabajo',
                'required' => false,
                'mapped' => false
            ))
            ->add('direccion', TextType::class, array(
                'label' => 'Dirección',
                'required' => false,
                'mapped' => false
            ))
            ->add('resto', TextType::class, array(
                'label' => 'Dirección',
                'required' => false,
                'mapped' => false
            ))
            ->add('numero', TextType::class, array(
                'label' => 'Dirección',
                'required' => false,
                'mapped' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'oEmpresa' => null,
            'estado_activado' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'directorioPacienteDueno';
    }
}
