<?php

namespace Rebsol\RecaudacionBundle\Form\Type\Supervisor\UbicacionCajero;

use Doctrine\ORM\EntityRepository;
use Rebsol\HermesBundle\Entity\EstadoRelUbicacionCajero;
use Rebsol\HermesBundle\Entity\RelUbicacionCajero;
use Rebsol\HermesBundle\Entity\UbicacionCaja;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;

class UbicacionCajeroType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $usuarioOriginal = $options['idUsuarioOriginal'];

        $builder
            ->add('montoInicial', TextType::class, array(
                    'label' => 'Monto Inicial'
                )
            )
            ->add('idUbicacionCaja', EntityType::class, array(
                    'label' => 'Ubicación Caja',
                    'class' => UbicacionCaja::class,
                    'choice_label' => 'nombre',
                    'required' => 'true',
                    'placeholder' => 'Seleccionar Ubicación Caja',
                    'query_builder' => function (EntityRepository $repository) use ($options) {
                        return $repository->createQueryBuilder('e')
                            ->join('e.idSucursal', 's')
                            ->Where('e.idEstado = ?1')
                            ->andWhere('s.idEstado = ?1')
                            ->andWhere('s.idEmpresa = ?2')
                            ->setParameter(1, $options['estado_activado'])
                            ->setParameter(2, $options['oEmpresa']->getId());
                    }
                )
            )
            ->add('idUsuario', ChoiceType::class,
                array(
                    'choices' => $this->getUsuariosAdministrativos($options['arrUsuariosAdministrativos']),
                    'required' => true,
                    'label' => 'Cajero',
                    'placeholder' => 'Seleccionar Cajero',
                    'mapped' => false,
                    'constraints' => array(
                        new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),
                    )
                )
            )

            //Campo oculto que nos permitirá validar sólo los campos del formulario pero sin modificar la fecha
            ->add('idUsuarioOriginal', HiddenType::class, array('mapped' => false, 'data' => $usuarioOriginal));

        if (!$options['isNew']) {
            $builder
                ->add('idEstado', EntityType::class,
                    array(
                        'label' => 'Estado',
                        'class' => EstadoRelUbicacionCajero::class,
                        'choice_label' => 'nombre',
                        'required' => 'true',

                        'placeholder' => 'Seleccionar Estado',
                        'query_builder' => function (EntityRepository $repository) use ($options) {
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
    }

    private function getUsuariosAdministrativos($arrUsuariosAdministrativos)
    {

        $arrChoices = array();

        foreach ($arrUsuariosAdministrativos as $i) {
            $arrChoices[$i->getIdPersona()->getIdPnatural()->getNombrePorOrden(9)] = $i->getId();
        }

        return $arrChoices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
                'data_class' => RelUbicacionCajero::class,
                'isNew' => true,
                'estado_activado' => null,
                'oEmpresa' => null,
                'sMantenedores' => null,
                'iIdEntidad' => false,
                'oEntidad' => false,
                'arrUsuariosAdministrativos' => false,
                'idUsuarioOriginal' => false,
            )
        );
    }

    public function getBlockPrefix()
    {

        return 'rebsol_hermesbundle_ubicacioncajeroType';
    }
}
