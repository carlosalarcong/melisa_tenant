<?php

namespace App\Form\Recaudacion\Pago;

use Rebsol\HermesBundle\Entity\MotivoDiferencia;
use Rebsol\HermesBundle\Entity\TipoSentidoDiferencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    /**
     * [buildForm description]
     * @param FormBuilderInterface $builder [description]
     * @param array $options [description]
     * @return null                        [description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('tipoDiferencia', EntityType::class, array(
                    'label' => 'Tipo Diferencia',
                    'class' => TipoSentidoDiferencia::class,
                    'choice_label' => 'nombre',
                    'required' => true,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Tipo',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('t')
                            ->where('t.idEstado = ?2')
                            ->orderBy('t.nombre', 'ASC')
                            ->setParameter(2, $options['estado_activado']);
                    },
                    'constraints' => array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Tipo de Dferencia')),
                    )
                )
            )
            ->add('motivoDiferencia', EntityType::class, array(
                    'label' => 'Motivo Diferencia',
                    'class' => MotivoDiferencia::class,
                    'choice_label' => 'nombre',
                    'required' => true,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Motivo',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('m')
                            ->where('m.idEstado = ?2')
                            ->andWhere('m.idEmpresa = ?1')
                            ->orderBy('m.nombre', 'ASC')
                            ->setParameter(1, $options['iEmpresa'])
                            ->setParameter(2, $options['estado_activado']);
                    },
                    'constraints' => array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Motivo de Diferencia')),
                    )
                )
            )
            ->add('motivoDiferenciaSaldo', EntityType::class, array(
                    'label' => 'Motivo Diferencia',
                    'class' => MotivoDiferencia::class,
                    'choice_label' => 'nombre',
                    'required' => true,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'placeholder' => 'Seleccionar Motivo',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('m')
                            ->where('m.idEstado = ?2')
                            ->andWhere('m.idEmpresa = ?1')
                            ->orderBy('m.nombre', 'ASC')
                            ->setParameter(1, $options['iEmpresa'])
                            ->setParameter(2, $options['estado_activado']);
                    },
                    'constraints' => array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Motivo de Diferencia')),
                    )
                )
            )
            ->add('montoGrupal', NumberType::class, array(
                'label' => 'Ingrese Monto Aplicado al Monto Total',
                'mapped' => false,
                'required' => false,
                'trim' => true,
                'data' => 0
            ))
            ->add('montoSaldo', NumberType::class, array(
                'label' => 'Ingrese Monto Aplicado al Monto Total',
                'mapped' => false,
                'required' => false,
                'trim' => true,
                'data' => 0
            ))
            ->add('porcentajeGrupal', NumberType::class, array(
                'label' => 'Ingrese Porcentaje Aplicado al Monto Total',
                'mapped' => false,
                'required' => false,
                'trim' => true,
                'data' => 0
            ));

        for ($i = 1; $i <= $options['count']; $i++) {
            $builder
                ->add('monto_' . $i, NumberType::class, array(
                    'label' => 'Ingrese Monto',
                    'mapped' => false,
                    'required' => false,
                    'trim' => true,
                    'data' => 0
                ))
                ->add('porcentaje_' . $i, NumberType::class, array(
                    'label' => 'Ingrese Porcentaje',
                    'mapped' => false,
                    'required' => false,
                    'trim' => true,
                    'data' => 0
                ));
        }
    }

    /**
     * [configureOptions description]
     * @param OptionsResolver $resolver [description]
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array
        (
            'iEmpresa' => null,
            'estado_activado' => null,
            'count' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_DiferenciaType';
    }

}
