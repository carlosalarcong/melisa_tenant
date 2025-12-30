<?php

namespace Rebsol\CajaBundle\Form\Type\Api\BuscadorPacientes;

use Rebsol\HermesBundle\Api\Caja\Api1\Controller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BusquedaMascotaType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombreMascota', TextType::class, array(
            'required' => false
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
        return 'buscarMascota';
    }
}

