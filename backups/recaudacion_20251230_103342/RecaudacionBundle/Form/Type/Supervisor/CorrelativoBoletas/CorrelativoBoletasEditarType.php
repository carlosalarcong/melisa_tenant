<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Supervisor\CorrelativoBoletas;

use Rebsol\HermesBundle\Entity\EstadoPila;
use Rebsol\HermesBundle\Entity\SubEmpresa;
use Rebsol\HermesBundle\Entity\Talonario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;

class CorrelativoBoletasEditarType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['idEstadoPilaOriginal'];
        $options['numeroTerminoOriginal'];
        $options['numeroInicioOriginal'];
        $options['idSubEmpresaOriginal'];

        $idEstadoPila = $options['idEstadoPilaOriginal'];
        $idSubEmpresa = $options['idSubEmpresaOriginal'];

        $builder
            ->add('idEstadoPila', EntityType::class,
                array(
                    'class' => EstadoPila::class,
                    'choice_label' => 'nombre',
                    'placeholder' => 'Seleccionar Estado',
                    'em' => $options['database_default']


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

            //Campo oculto que nos permitirá validar sólo los campos del formulario pero sin modificar la fecha
            ->add('numeroTerminoOriginal', HiddenType::class, array('mapped' => false, 'data' => $options['numeroTerminoOriginal']))

            //Campo oculto que nos permitirá validar sólo los campos del formulario pero sin modificar la fecha
            ->add('numeroInicioOriginal', HiddenType::class, array('mapped' => false, 'data' => $options['numeroInicioOriginal']))

            //Campo oculto que nos permitirá validar sólo los campos del formulario pero sin modificar la fecha
            ->add('idEstadoPilaOriginal', HiddenType::class, array('mapped' => false, 'data' => $idEstadoPila))

            //Campo oculto que nos permitirá validar sólo los campos del formulario pero sin modificar la fecha
            ->add('idSubEmpresaOriginal', HiddenType::class, array('mapped' => false, 'data' => $idSubEmpresa))
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
                            'min' => 0,
                            'minMessage' => 'Valor no puede ser menor a 0'
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
                    )
                    )
                )
            )
            ->add('numeroActual', NumberType::class,
                array(
                    'label' => 'Número Actual',
                    'invalid_message' => 'Debe ingresar sólo números',
                    'constraints' => array(new constraints\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'Valor no puede ser menor a 0'
                        )
                    )
                    )
                )
            );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Talonario::class,
            'isNew' => true,
            'estado_activado' => null,
            'oEmpresa' => null,
            'sMantenedores' => null,
            'iIdEntidad' => false,
            'oEntidad' => false,
            'idEstadoPilaOriginal' => false,
            'numeroTerminoOriginal' => false,
            'numeroInicioOriginal' => false,
            'idSubEmpresaOriginal' => false,
            'database_default' => null

        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_CorrelativoBoletasEditarType';
    }
}