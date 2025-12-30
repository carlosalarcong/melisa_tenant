<?php

namespace App\Form\Supervisor\MantenedorFolios;

use Doctrine\ORM\EntityRepository;
use Rebsol\HermesBundle\Entity\RelEmpresaTipoDocumento;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MantenedorFoliosType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('talonario', EntityType::class,
            array(
                'class' => RelEmpresaTipoDocumento::class,
                'choice_label' => 'nombre',
                'required' => false,
                'mapped' => false,
                'em' => $options['database_default'],
                'query_builder' =>
                    function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('tp')
                            ->where('tp.idEstado = ?1')
                            ->andWhere('tp.idEmpresa = ?2')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oEmpresa']->getId());
                    },
            )
        );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'oEmpresa' => null,
                'estado_activado' => null,
                'database_default' => null
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'talonario';
    }
}

