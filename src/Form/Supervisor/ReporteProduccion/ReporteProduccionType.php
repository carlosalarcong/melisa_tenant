<?php


namespace App\Form\Supervisor\ReporteProduccion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ReporteProduccionType extends AbstractType {

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
            );
    }
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class'      => null
        ));
    }

    public function getBlockPrefix() {
        return 'reporte_produccion_type';
    }
}
