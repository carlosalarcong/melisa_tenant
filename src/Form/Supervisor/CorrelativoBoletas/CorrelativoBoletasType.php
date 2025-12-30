<?php

namespace App\Form\Supervisor\CorrelativoBoletas;

use Rebsol\HermesBundle\Entity\EstadoPila;
use Rebsol\HermesBundle\Entity\RelEmpresaTipoDocumento;
use Rebsol\HermesBundle\Entity\SubEmpresa;
use Rebsol\HermesBundle\Entity\Sucursal;
use Rebsol\HermesBundle\Entity\Talonario;
use Rebsol\HermesBundle\Entity\UbicacionCaja;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;

class CorrelativoBoletasType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idSucursal', EntityType::class,
                array(
                    'class' => Sucursal::class,
                    'choice_label' => 'nombreSucursal',
                    'placeholder' => 'Seleccionar Sucursal',
                    'em' => $options['database_default'],
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('e')
                            ->where('e.idEstado = ?1')
                            ->andWhere('e.idEmpresa = ?2')
                            ->andWhere('e.id = ?3')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oEmpresa']->getId())
                            ->setParameter(3, $options['oSucursal']->getId());
                    },
                    'constraints' => array(new constraints\NotBlank(array('message' => 'Debe seleccionar una Sucursal')))

                )
            )
            ->add('idSubEmpresa', EntityType::class,
                array(
                    'class' => SubEmpresa::class,
                    'choice_label' => 'nombre',
                    'placeholder' => 'Seleccionar Sub Empresa',
                    'em' => $options['database_default'],
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('e')
                            ->where('e.idEstado = ?1')
                            ->andWhere('e.idEmpresa = ?2')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oEmpresa']->getId());

                    },
                    'constraints' => array(new constraints\NotBlank(array('message' => 'Debe seleccionar una Sub Empresa')))

                )
            )
            ->add('idRelEmpresaTipoDocumento', EntityType::class,
                array(
                    'class' => RelEmpresaTipoDocumento::class,
                    'choice_label' => 'nombre',
                    'placeholder' => 'Seleccionar Documento',
                    'em' => $options['database_default'],
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('e')
                            ->where('e.idEstado = ?1')
                            ->andWhere('e.idEmpresa = ?2')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oEmpresa']->getId());
                    },
                    'constraints' => array(new constraints\NotBlank(array('message' => 'Debe seleccionar un Documento')))

                )
            )
            ->add('numeroPila', TextType::class,
                array('required' => false,
                    'attr' => ['readonly' => true]))
            ->add('idEstadoPila', EntityType::class,
                array(
                    'class' => EstadoPila::class,
                    'choice_label' => 'nombre',
                    'placeholder' => 'Seleccionar Estado',
                    'em' => $options['database_default'],
                    'constraints' => array(new constraints\NotBlank(array('message' => 'Debe seleccionar un Estado de Pila')))

                )
            )
            ->add('fechaEntrega', DateType::class,
                array(
                    'label' => 'Fecha Vigencia',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr' => array('class' => 'date-picker'),
                    'data' => new \DateTime(date("Y-m-d"))
                )
            )
            ->add('numeroInicio', NumberType::class,
                array(
                    'label' => 'Número Inicio',
                    'invalid_message' => 'Debe ingresar sólo números',
                    'constraints' => array(new constraints\Range(
                        array(
                            'min' => 1,
                            'minMessage' => 'Valor no puede ser menor a 1'
                        )
                    ),
                        new constraints\NotBlank(array('message' => 'Debe seleccionar un número de inicio'))
                    )
                )
            )
            ->add('numeroTermino', NumberType::class,
                array(
                    'label' => 'Número Término',
                    'invalid_message' => 'Debe ingresar sólo números',
                    'constraints' => array(new constraints\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'Valor no puede ser menor a 0'
                        )
                    ),
                        new constraints\NotBlank(array('message' => 'Debe seleccionar un número de término'))
                    )
                )
            )
            ->add('numeroActual', NumberType::class,
                array(
                    'label' => 'Número Actual',
                    'invalid_message' => 'Debe ingresar sólo números',
                    'attr' => ['readonly' => true],
                    'constraints' => array(new constraints\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'Valor no puede ser menor a 0'
                        )
                    )
                    )
                )
            );
        if ($options['folio'] === '0') {
            $builder->add('idUbicacionCaja', EntityType::class,
                array(
                    'class' => UbicacionCaja::class,
                    'choice_label' => 'nombre',
                    'placeholder' => 'Seleccionar Ubicación',
                    'em' => $options['database_default'],
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('e')
                            ->where('e.idEstado = ?1')
                            ->andWhere('e.idSucursal = ?2')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oSucursal']->getId());
                    },
                    'constraints' => array(new constraints\NotBlank(array('message' => 'Debe seleccionar una Ubicación')))
                )
            );
        }


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Talonario::class,
            'isNew' => true,
            'estado_activado' => null,
            'oSucursal' => null,
            'oEmpresa' => null,
            'sMantenedores' => null,
            'iIdEntidad' => false,
            'oEntidad' => false,
            'folio' => 0,
            'database_default' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_CorrelativoBoletasType';
    }
}
