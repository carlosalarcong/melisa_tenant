<?php

namespace App\Form\Recaudacion\Pago;

use Rebsol\HermesBundle\Form\Type\DefaultType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as validaform;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 28/05/2015
 *
 */
class AdjuntoType extends DefaultType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
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
            ->add('idPaciente', TextType::class, array('mapped' => false, 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'rebsol_hermesbundle_AdjuntoType';
    }

}
