<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Api\Caja\Recaudacion\Pago;

use Rebsol\HermesBundle\Entity\Origen;
use Rebsol\HermesBundle\Entity\PersonaDomicilio;
use Rebsol\HermesBundle\Entity\Pnatural;
use Rebsol\HermesBundle\Entity\Prevision;
use Rebsol\HermesBundle\Entity\PrPlan;
use Rebsol\HermesBundle\Entity\TipoTratamiento;
use Rebsol\HermesBundle\Form\Type\DefaultType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as validaform;

/* use Symfony\Component\Form\FormInterface; */
//sirve para generar Evento
//sirve para generar Evento
//sirve para llamar la Constraine

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 05/11/2013
 * Participantes:
 *
 */
class PrestacionType extends DefaultType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();

        $builder->add('prevision', EntityType::class, array(
                'label' => 'Prevision',
                'class' => Prevision::class,
                'property' => 'nombrePrevision',
                'required' => true,
                'mapped' => false,
                'em' => $options['database_default'],
                'empty_value' => 'Seleccionar Financiador',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('p')
                        ->join('p.idTipoPrevision', 'tp')
                        ->where('p.idEmpresa = ?1')
                        ->andWhere('p.idEstado = ?2')
                        ->andwhere('tp.idEmpresa = ?1')
                        ->andWhere('tp.idEstado = ?2')
                        ->andWhere('tp.id = p.idTipoPrevision')
                        ->andWhere('tp.esConvenio = 0')
                        ->orderBy('p.nombrePrevision', 'ASC')
                        ->setParameter(1, $options['iEmpresa'])
                        ->setParameter(2, $options['estado_activado']);
                },
                'constraints' =>
                    array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Financiador')),
                    )
            )
        )->add('convenio', EntityType::class, array(
                'label' => 'Convenio',
                'class' => Prevision::class,
                'property' => 'nombrePrevision',
                'required' => false,
                'mapped' => false,
                'em' => $options['database_default'],
                'empty_value' => 'Seleccionar Convenio',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('p')
                        ->join('p.idTipoPrevision', 'tp')
                        ->where('p.idEmpresa = ?1')
                        ->andWhere('p.idEstado = ?2')
                        ->andwhere('tp.idEmpresa = ?1')
                        ->andWhere('tp.idEstado = ?2')
                        ->andWhere('tp.id = p.idTipoPrevision')
                        ->andWhere('tp.esConvenio = 1')
                        ->orderBy('p.nombrePrevision', 'ASC')
                        ->setParameter(1, $options['iEmpresa'])
                        ->setParameter(2, $options['estado_activado']);
                },
                'constraints' =>
                    array(
                        new validaform\NotBlank(array('message' => 'Debe definir un Prevision')),
                    )
            )
        )
            ->add('plan', EntityType::class, array(
                    'class' => PrPlan::class,
                    'property' => 'nombre',
                    'required' => true,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'empty_value' => 'Seleccionar Plan',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s');
                        // ->Where('s.id IS NULL');
                    }
                )
            )
            ->add('origenSelect', EntityType::class, array(
                    'class' => Origen::class,
                    'property' => 'nombre',
                    'required' => true,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'empty_value' => 'Seleccionar Origen',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->where('s.idSucursal = ?1')
                            ->setParameter(1, $options['sucursal']);
                    }
                )
            )
            ->add('derivadoSelect', EntityType::class, array(
                'class' => Pnatural::class,
                'property' => 'nombrePnatural',
                'required' => true,
                'mapped' => false,
                'em' => $options['database_default'],
                'empty_value' => 'Seleccionar Profesional',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {

                    return $repository->createQueryBuilder('pnat')
                        ->addSelect('pnat')
                        ->leftJoin('pnat.idPersona', 'per')
                        ->leftJoin('RebsolHermesBundle:UsuariosRebsol', 'usr', 'with', '(usr.idPersona = per.id)')
                        ->leftJoin('RebsolHermesBundle:RelUsuarioServicio', 'russ', 'with', '(russ.idUsuario = usr.id)')
                        ->leftJoin('russ.idServicio', 'serv')
                        ->leftJoin('serv.idUnidad', 'uni')
                        ->leftJoin('uni.idSucursal', 'suc')
                        ->leftJoin('RebsolHermesBundle:RolProfesional', 'rp', 'with', '(rp.idUsuario = usr.id)')
                        ->leftJoin('rp.idRol', 'rol')
                        ->where('per.idEmpresa = ?2')
                        ->andWhere('usr.esSala != ?1')
                        ->andWhere('russ.idEstado = ?3')
                        ->andWhere('usr.idEstadoUsuario = ?3')
                        ->andWhere('rol.profClinico = ?4')
                        ->andWhere('pnat.numeroHermanoGemelo = 0')
                        ->setParameter(1, 1)
                        ->setParameter(2, $options['iEmpresa'])
                        ->setParameter(3, $this->obtenerParametroYML('estado_activo'))
                        ->setParameter(4, $this->obtenerParametroYML('rol_medico'))
                        ->orderBy('pnat.nombrePnatural', 'ASC');
                }
            ))->add('archivo', FileType::class, array(
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
            ->add('derivadoExternoRut', TextType::class, array('mapped' => false, 'required' => false))
            ->add('tipoTratamiento', EntityType::class, array(
                    'class' => TipoTratamiento::class,
                    'property' => 'nombre',
                    'required' => false,
                    'mapped' => false,
                    'em' => $options['database_default'],
                    'empty_value' => 'Seleccionar Tipo Tratamiento',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('s')
                            ->where('s.idEstado = ?1')
                            ->andwhere('s.idEmpresa = ?2')
                            ->setParameter(1, 1)
                            ->setParameter(2, $options['iEmpresa']);
                    }
                )
            )
            ->add('derivadoCheck', CheckboxType::class, array('mapped' => false))
            ->add('derivadoExterno', TextType::class, array('mapped' => false, 'required' => true))
            ->add('nombreTratamiento', TextType::class, array('mapped' => false, 'required' => true))

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
            'data_class' => PersonaDomicilio::class,
            'validaform' => true,
            'iEmpresa' => null,
            'estado_activado' => null,
            'sucursal' => null,
            'database_default' => null
            //Dejo variable de iEmpresa para la consulta de sexo, y restringir los sexos según empresa
        ));
    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_PrestacionType';
    }

}
