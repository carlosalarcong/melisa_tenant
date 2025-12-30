<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Recaudacion\Pago;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;

/**
 * @author jgutierrez
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 */
class BusquedaAvanzadaDirectorioPacienteType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rut', TextType::class, array(
            'required' => false
        ));

        $builder->add('opcionBusqueda', ChoiceType::class, array(
            'choices' => array_flip(array('2' => 'Que contenga', '1' => 'Que inicie', '0' => 'Exacta')),
            'required' => true,
            'multiple' => false,
            'data' => '0',
            'expanded' => true,

        ));

        $builder->add('nombres', TextType::class, array(
            'required' => false,
            'constraints' =>
                new constraints\Length(
                    array(
                        'min' => '3',
                        'max' => '10',
                        'minMessage' => 'Este campo debe tener {{ limit }} caracteres o más',
                        'maxMessage' => 'Este campo debe tener {{ limit }} caracteres máximo'
                    ))
        ));
        $builder->add('apPaterno', TextType::class, array(
            'required' => false,
            'constraints' =>
                array(
                    new constraints\Length(
                        array(
                            'min' => '3',
                            'max' => '10'
                        )
                    )
                )
        ));
        $builder->add('apMaterno', TextType::class, array(
            'required' => false,
            'constraints' =>
                array(
                    new constraints\Length(
                        array(
                            'min' => '3',
                            'max' => '10'
                        )
                    )
                )
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'oEmpresa' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'busquedaAvanzadaPaciente';
    }
}
