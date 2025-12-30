<?php

namespace App\Form\Supervisor\ApoyoFacturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ApoyoFacturacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mes', TextType::class,
                array(
                    'mapped' => false,
                    'data' => date('m-Y')
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'apoyo_facturacion_type';
    }
}
