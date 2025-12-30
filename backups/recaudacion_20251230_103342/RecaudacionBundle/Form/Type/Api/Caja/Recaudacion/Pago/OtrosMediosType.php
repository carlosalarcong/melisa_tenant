<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Api\Caja\Recaudacion\Pago;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/* use Symfony\Component\Form\FormInterface; */
//sirve para generar Evento
//sirve para generar Evento
//sirve para llamar la Constraine

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 * Participantes:
 */
class OtrosMediosType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $factory = $builder->getFormFactory();
        if ($options['clone']) {
            $ListadoMediosPago = array();
            $ListadoMediosPago[] = $options['idFrom'];
        } else {
            $ListadoMediosPago = $options['idFrom'];
        }
        foreach ($ListadoMediosPago as $idForm) {
            $builder
                ->add('medioPago_' . $idForm, NumberType::class, array(
                        'mapped' => false,
                        'required' => true,
                    )
                )
                ->add('dinamico_' . $idForm, NumberType::class, array(
                        'mapped' => false,
                        'required' => true,
                        'data' => 1,
                    )
                )
                ->add('monto_' . $idForm, NumberType::class, array(
                        'mapped' => false,
                        'required' => true,
                    )
                )
                ->add('folio_' . $idForm, NumberType::class, array(
                        'mapped' => false,
                        'required' => true,
                    )
                );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array
        (
            'idFrom' => null,
            'clone' => false,
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_OtrosMediosType';
    }

}