<?php

namespace Rebsol\CajaBundle\Form\Type\Api\BuscadorPacientes;

use Rebsol\HermesBundle\Api\Caja\Api1\Controller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;


class BusquedaPorRutType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rut', TextType::class, array(
            'required' => false,
            'constraints' =>
                array(
                    new constraints\Length(
                        array(
                            'min' => 8,
                            'max' => 13,
                            'minMessage' => 'Your first name must be at least {{ limit }} characters length',
                            'maxMessage' => 'Your first name cannot be longer than than {{ limit }} characters length'
                        )
                    )
                )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {/*
        $resolver->setDefaults(array(
           'oEmpresa'        => null
        ));*/
    }

    public function getBlockPrefix()
    {
        return 'buscarDueno';
    }
}


