<?php

namespace App\Form\Supervisor\UbicacionCaja;

use Rebsol\HermesBundle\Entity\Estado;
use Rebsol\HermesBundle\Entity\Sucursal;
use Rebsol\HermesBundle\Entity\UbicacionCaja;
use Rebsol\HermesBundle\Form\EventListener\MantenedorMaestro\AddComprobarCambinacionFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UbicacionCajaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array('label' => 'Nombre'))
            ->add('descripcion', TextareaType::class,
                array(
                    'label' => 'Descripción',
                    'required' => false
                )
            )
            ->add('idSucursal', EntityType::class,
                array(
                    'label' => 'Sucursal',
                    'class' => Sucursal::class,
                    'choice_label' => 'nombreSucursal',
                    'required' => 'true',
                    'em' => $options['database_default'],
                    'placeholder' => 'Seleccionar Sucursal',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('e')
                            ->where('e.idEstado = ?1')
                            ->andWhere('e.idEmpresa = ?2')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oEmpresa']->getId());
                    }
                )
            );

        if (!$options['isNew']) {
            $builder
                ->add('idEstado', EntityType::class,
                    array(
                        'label' => 'Estado',
                        'class' => Estado::class,
                        'choice_label' => 'nombreEstado',
                        'required' => 'true',
                        'em' => $options['database_default'],
                        'placeholder' => 'Seleccionar Estado',
                        'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                            if ($options['isNew']) {
                                return $repository->createQueryBuilder('e')
                                    ->where('e.id = ?1')
                                    ->setParameter(1, $options['estado_activado']);
                            } else {
                                return $repository->createQueryBuilder('e');
                            }
                        }
                    )
                );
        }
        $factory = $builder->getFormFactory();
        $nombreSubscriber = new AddComprobarCambinacionFieldSubscriber($factory, 'UbicacionCaja', 'nombre', 'idSucursal', null, $options['sMantenedores'], $options['iIdEntidad']);
        $builder->addEventSubscriber($nombreSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UbicacionCaja::class,
            'isNew' => true,
            'estado_activado' => null,
            'oEmpresa' => null,
            'sMantenedores' => null,
            'iIdEntidad' => false,
            'oEntidad' => false,
            'database_default' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_ubicacioncajaType';
    }
}