<?php

namespace Rebsol\RecaudacionBundle\Form\Type\ApiPV\ConsolidadoCajaPorProfesional;

use Rebsol\HermesBundle\Entity\Sucursal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;


class ConsolidadoCajaPorProfesionalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', DateType::class,
                array(
                    'label' => 'Fecha Vigencia',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'mapped' => false,
                    'attr' => array('class' => 'date-picker')
                )
            )
            ->add('Sucursal', EntityType::class,
                array(
                    'class' => Sucursal::class,
                    'choice_label' => 'nombreSucursal',
                    'em' => $options['database_default'],
                    'placeholder' => 'Seleccionar Sucursal',
                    'query_builder' =>
                        function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                            return $repository->createQueryBuilder('e')
                                ->where('e.idEstado = :idEstado')
                                ->andWhere('e.idEmpresa = :idEmpresa')
                                ->andWhere('e.id = :id')
                                ->setParameter('idEstado', $options['idEstado'])
                                ->setParameter('idEmpresa', $options['idEmpresa'])
                                ->setParameter('id', $options['idSucursal']);
                        },
                    'constraints' =>
                        array(new constraints\NotBlank(array('message' => 'Debe seleccionar una sucursal')))
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'isNew' => true,
            'estado_activado' => null,
            'idEstado' => null,
            'idEmpresa' => null,
            'idSucursal' => null,
            'database_default' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'consolidado_caja_por_profesional_type';
    }
}
