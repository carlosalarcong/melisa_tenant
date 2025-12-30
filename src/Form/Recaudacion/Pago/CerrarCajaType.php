<?php

namespace App\Form\Recaudacion\Pago;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 * Participantes:
 */
class CerrarCajaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('superavit', NumberType::class, array(
            'mapped' => false,
            'required' => false,
        ))
            ->add('deficit', NumberType::class, array(
                'mapped' => false,
                'required' => false,
            ))
            ->add('deposito_8_2', NumberType::class, array(
                'mapped' => false,
                'required' => false,
            ));

        $idFrom = $options['idFrom'];

        foreach ($idFrom as $value) {
            $builder->add('deposito_' . $value, NumberType::class, array(
                    'mapped' => false,
                    'required' => false,
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'idFrom' => null,
            'estado_activado' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_CerrarCajaType';
    }

}