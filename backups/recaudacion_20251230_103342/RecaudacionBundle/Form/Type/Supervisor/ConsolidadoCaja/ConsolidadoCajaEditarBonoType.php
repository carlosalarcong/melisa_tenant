<?php

namespace Rebsol\CajaBundle\Form\Type\Supervisor\ConsolidadoCaja;

use Rebsol\HermesBundle\Entity\TarjetaCredito;
use Rebsol\HermesBundle\Entity\TipoGratuidad;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;


class ConsolidadoCajaEditarBonoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroDocumento', NumberType::class,
                array(
                    'invalid_message' => 'Debe ingresar sólo números',
                    'mapped' => false,
                    'data' => $options['n°Bono'],
                    'constraints' =>
                        array(
                            new constraints\NotBlank
                            (
                                array('message' => 'El Número del Bono no debe estar en blanco')
                            ),

                            new constraints\Range
                            (
                                array(
                                    'min' => '0',
                                    'minMessage' => 'El Número del Bono no debe ser menor a 0'
                                )
                            )
                        )

                )
            )
            ->add('TarjetaCredito', EntityType::class,
                array(
                    'class' => TarjetaCredito::class,
                    'property' => 'nombre',
                    'empty_value' => 'Seleccionar Tarjeta',
                    'query_builder' =>
                        function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                            return $repository->createQueryBuilder('e')
                                ->where('e.idEstado = :idEstado')
                                ->setParameter('idEstado', $options['idEstado']);

                        },
                    'constraints' =>
                        array(new constraints\NotBlank(array('message' => 'Debe seleccionar una Tarjeta de Crédito')))
                )
            )
            ->add('numeroVoucher', TextType::class,
                array(
                    'invalid_message' => 'Debe ingresar sólo números',
                    'mapped' => false,
                    'data' => $options['n°Voucher'],
                    'constraints' =>
                        array(
                            new constraints\NotBlank
                            (
                                array('message' => 'El Número Voucher no debe estar en blanco')
                            ),

                            new constraints\Range
                            (
                                array(
                                    'min' => '0',
                                    'minMessage' => 'El Número Voucher no debe ser menor a 0'
                                )
                            )
                        )

                )
            )
            ->add('Gratuidad', EntityType::class,
                array(
                    'class' => TipoGratuidad::class,
                    'property' => 'nombre',
                    'empty_value' => 'Seleccionar Gratuidad',
                    'query_builder' =>
                        function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                            return $repository->createQueryBuilder('e')
                                ->where('e.idEstado = :idEstado')
                                ->setParameter('idEstado', $options['idEstado']);

                        },
                    'constraints' =>
                        array(new constraints\NotBlank(array('message' => 'Debe seleccionar una Gratuidad')))
                )
            );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class'      => 'Rebsol\HermesBundle\Entity\PagoCuenta',
            'data_class' => null,
            'isNew' => true,
            'estado_activado' => null,
            'n°Bono' => null,
            'n°Voucher' => null,
            'idEstado' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'consolidado_caja_type';
    }
}
