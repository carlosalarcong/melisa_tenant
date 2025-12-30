<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Api\Caja\Recaudacion\Pago;

use Rebsol\HermesBundle\Entity\MotivoDiferencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as validaform;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 * Participantes:
 *
 */
class DiferenciaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $factory = $builder->getFormFactory();
        $builder->add('tipoDiferencia', EntityType::class, array(
                'label' => 'Tipo Diferencia',
                'class' => TipoDiferencia::class,
                'property' => 'nombre',
                'required' => true,
                'mapped' => false,
                'empty_value' => 'Seleccionar Tipos de Diferencia',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('t')
                        ->where('t.idEmpresa = ?1')
                        ->andWhere('t.idEstado = ?2')
                        ->orderBy('t.nombre', 'ASC')
                        ->setParameter(1, $options['iEmpresa'])
                        ->setParameter(2, $options['estado_activado']);
                },
                'constraints' =>
                    array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Tipo de Dferencia')),
                    )
            )
        )
            ->add('motivoDiferencia', EntityType::class, array(
                    'label' => 'Motivo Diferencia',
                    'class' => MotivoDiferencia::class,
                    'property' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'empty_value' => 'Seleccionar Motivo Diferencia',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('m')
                            ->where('m.idEstado = ?2')
                            ->orderBy('m.nombre', 'ASC')
                            ->setParameter(2, $options['estado_activado']);
                    },
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Debe definir un Motivo de Diferencia')),
                        )
                )
            )
            ->add('agrupacion', NumberType::class, array(
                    'mapped' => false,
                    'required' => true,
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array
        (
            'iEmpresa' => null,
            'estado_activado' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_DiferenciaType';
    }

}
