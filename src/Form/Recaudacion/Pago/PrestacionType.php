<?php

namespace App\Form\Recaudacion\Pago;

use App\Entity\Tenant\ReferralSource;
use App\Entity\Tenant\PersonAddress;
use App\Entity\Tenant\Person;
use App\Entity\Tenant\HealthInsurance;
use App\Entity\Tenant\InsurancePlan;
use App\Entity\Tenant\TreatmentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
class PrestacionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('prevision', EntityType::class, array(
                'label' => 'Prevision',
                'class' => HealthInsurance::class,
                'choice_label' => 'nombre',
                'required' => true,
                'mapped' => false,
                'placeholder' => 'Seleccionar Financiador',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('p')
                        ->where('p.isActive = :active')
                        ->orderBy('p.name', 'ASC')
                        ->setParameter('active', true);
                },
                'constraints' =>
                    array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Financiador')),
                    )
            )
        )
            ->add('convenio', EntityType::class, array(
                    'label' => 'Convenio',
                    'class' => HealthInsurance::class,
                    'choice_label' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Convenio',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('p')
                            ->where('p.isActive = :active')
                            ->orderBy('p.name', 'ASC')
                            ->setParameter('active', true);
                    },
                    'constraints' =>
                        array(
                            new validaform\NotBlank(array('message' => 'Debe definir un Prevision')),
                        )
                )
            )
            ->add('plan', EntityType::class, array(
                    'class' => InsurancePlan::class,
                    'choice_label' => 'nombre',
                    'required' => true,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Plan',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->where('s.isActive = :active')
                            ->orderBy('s.name', 'ASC')
                            ->setParameter('active', true);
                    }
                )
            )
            ->add('origenSelect', EntityType::class, array(
                    'class' => ReferralSource::class,
                    'choice_label' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Origen',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->where('s.branchId = :branchId')
                            ->andWhere('s.isActive = :active')
                            ->orderBy('s.name', 'ASC')
                            ->setParameter('branchId', $options['sucursal'])
                            ->setParameter('active', true);
                    }
                )
            )
            ->add('derivadoSelect', EntityType::class, array(
                    'class' => Person::class,
                    'choice_label' => 'nombrePnatural',
                    'required' => false,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Profesional',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('p')
                            ->orderBy('p.name', 'ASC');
                    }
                )
            )
            ->add('tipoTratamiento', EntityType::class, array(
                    'class' => TreatmentType::class,
                    'choice_label' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'placeholder' => 'Seleccionar Tipo Tratamiento',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->where('s.isActive = :active')
                            ->orderBy('s.name', 'ASC')
                            ->setParameter('active', true);
                    }
                )
            )
            ->add('derivadoCheck', CheckboxType::class, array('mapped' => false))
            ->add('derivadoExterno', TextType::class, array('mapped' => false, 'required' => false))
            ->add('derivadoExternoRut', TextType::class, array('mapped' => false, 'required' => false))
            ->add('nombreTratamiento', TextType::class, array('mapped' => false, 'required' => true))
            ->add('archivo', FileType::class,
                array(
                    'label' => 'pdf',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => array(
                        new validaform\NotNull(array('message' => 'Este valor no debe estar en blanco')),
                        new validaform\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                        new validaform\File(
                            array(
                                'maxSize' => '4M',
                                'mimeTypes' => array(
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-powerpoint',
                                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                    'application/x-pdf',
                                    'application/pdf',
                                ),
                                'mimeTypesMessage' => 'El archivo no pertenece al formato permitido',
                            )
                        )
                    )
                )
            )

            //////////////////////////////////////////////////////////////////////////////////
            // PAQUETES, PRESTACIONES, INSUMOS //
            //////////////////////////////////////////////////////////////////////////////////


            ->add('cantidadPrestaciones', TextType::class, array(
                    'label' => 'numero',
                    'mapped' => false,
                    'required' => false
                )
            )
            ->add('cantidadPaquetes', TextType::class, array(
                    'label' => 'numero',
                    'mapped' => false,
                    'required' => false
                )
            )
            ->add('cantidadInsumos', TextType::class, array(
                    'label' => 'numero',
                    'mapped' => false,
                    'required' => false
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array
        (
            'data_class' => PersonAddress::class,
            'validaform' => true,
            'iEmpresa' => null,
            'estado_activado' => null,
            'sucursal' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_PrestacionType';
    }

}