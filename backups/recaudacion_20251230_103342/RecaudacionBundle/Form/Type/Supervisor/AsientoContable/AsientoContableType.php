<?php
namespace Rebsol\RecaudacionBundle\Form\Type\Supervisor\AsientoContable;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as constraints;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Rebsol\HermesBundle\Entity\Sucursal;
use Rebsol\HermesBundle\Entity\UsuariosRebsol;

class AsientoContableType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('fechaIni', DateType::class,
                array(
                    'label'  => 'Fecha Inicio',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'mapped' => false,
                    'attr'   => array('class'=>'date-picker'),
                    'data'   => new \DateTime(date("Y-m-d"))

                )
            )

            ->add('fechaFin', DateType::class,
                array(
                    'label'  => 'Fecha Fin',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'mapped' => false,
                    'attr'   => array('class'=>'date-picker'),
                    'data'   => new \DateTime(date("Y-m-d"))

                )
            )

            ->add('idSucursal', EntityType::class,
                array(
                    'label'         => 'Sucursal',
                    'class'         => Sucursal::class,
                    'choice_label'  => 'nombreSucursal',
                    'em'            => $options['database_default'],
                    'placeholder'   => 'Seleccionar Sucursal',
                    'query_builder' =>
                        function (\Doctrine\ORM\EntityRepository $repository) use($options) {
                            return $repository->createQueryBuilder('s')
                                ->where('s.idEstado = :idEstado')
                                ->andWhere('s.idEmpresa = :idEmpresa')
                                ->setParameter('idEstado', $options['idEstado'])
                                ->setParameter('idEmpresa', $options['idEmpresa']);
                        },
                    'constraints' =>
                        array(new constraints\NotBlank(array('message' => 'Debe seleccionar una sucursal')))
                )
            )
            ->add('idUsuario', EntityType::class,
                array(
                    'label'         => 'Cajero',
                    'class'         => UsuariosRebsol::class,
                    'choice_label'  => 'nombreUsuario',
                    'placeholder'   => 'Seleccionar Cajero',
                    'em'            => $options['database_default'],
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use($options)
                    {
                        return $repository->createQueryBuilder('e')
                            ->where('e.idEstadoUsuario IS NULL');
                    },
                    'constraints' =>
                        array(new constraints\NotBlank(array('message' => 'Debe seleccionar una cajero')))
                )
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'isNew'           => true,
            'estado_activado' => null,
            'idEstado'        => null,
            'idEmpresa'       => null,
            'oEmpresa'        => null,
            'idSucursal'      => null,
            'idUbicacionCaja' => null,
            'database_default'=> null,
            'oEntidad'        => false
        ));
    }

    public function getBlockPrefix() {
        return 'asiento_contable_type';
    }
}
