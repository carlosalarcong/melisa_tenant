<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Supervisor\MantenedorFolios;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;

class MantenedorFoliosEditarType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('numeroDocumento', NumberType::class,
            array(

                'required' => false,
                'invalid_message' => 'Debe ingresar sólo números',
                'constraints' =>
                    array(
                        new constraints\Range(
                            array(
                                'min' => 0,
                                'minMessage' => 'Valor no puede ser menor a 0'
                            ),

                            array('message' => 'Valor no debe estar en blanco')

                        )
                    ),

            )
        );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'estado_activado' => null
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'editar_numero';
    }
}
