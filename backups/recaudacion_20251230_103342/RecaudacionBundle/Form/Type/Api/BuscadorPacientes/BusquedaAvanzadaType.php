<?php /** @noinspection PhpUndefinedClassInspection */

namespace Rebsol\CajaBundle\Form\Type\Api\BuscadorPacientes;

use Doctrine\ORM\EntityRepository;
use Rebsol\HermesBundle\Api\Caja\Api1\Controller;
use Rebsol\HermesBundle\Entity\Especie;
use Rebsol\HermesBundle\Entity\Raza;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as constraints;


class BusquedaAvanzadaType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', TextType::class, array(
            'required' => false,))
            ->add('apellidoPaterno', TextType::class, array(
                'required' => false,))
            ->add('apellidoMaterno', TextType::class, array(
                'required' => false,))
            ->add('apellidoMaterno', TextType::class, array(
                'required' => false,))
            ->add('especie', EntityType::class, array(
                'class' => Especie::class,
                'property' => 'nombre',
                'required' => false,
                'mapped' => false,
                'empty_value' => 'Seleccionar Especie',
                'constraints' => array(new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->where('s.idEstado = ?2')
                        ->setParameter(2, $options['estado_activado'])
                        ->orderBy('s.nombre', 'ASC');
                },
            ))
            ->add('raza', EntityType::class, array(
                'class' => Raza::class,
                'property' => 'nombre',
                'required' => false,
                'mapped' => false,
                'empty_value' => 'Seleccionar Raza',
                'constraints' => array(new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->where('s.idEstado = ?2')
                        ->setParameter(2, $options['estado_activado'])
                        ->orderBy('s.nombre', 'ASC');
                },
            ))
            ->add('opcionBusqueda', ChoiceType::class, array(
                'choices' => array_flip(array('2' => 'Que contenga', '1' => 'Que inicie', '0' => 'Exacta')),
                'required' => false,
                'multiple' => false,
                'data' => '0',
                'expanded' => true,

            ))
            ->add('opcionBusqueda2', ChoiceType::class, array(
                'choices' => array_flip(array('2' => 'Que contenga', '1' => 'Que inicie', '0' => 'Exacta')),
                'required' => false,
                'multiple' => false,
                'data' => '0',
                'expanded' => true,

            ))
            ->add('color', EntityType::class, array(
                'class' => Color::class,
                'property' => 'nombre',
                'required' => false,
                'mapped' => false,
                'empty_value' => 'Seleccionar Color   ',
                'constraints' => array(new constraints\NotBlank(array('message' => 'Este valor no debe estar en blanco')),),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->where('s.idEmpresa = ?1')
                        ->andWhere('s.idEstado = ?2')
                        ->setParameter(1, $options['oEmpresa']->getId())
                        ->setParameter(2, $options['estado_activado'])
                        ->orderBy('s.nombre', 'ASC');
                },
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'oEmpresa' => null,
            'estado_activado' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'busquedaAvanzadaPaciente';
    }
}
