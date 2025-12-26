<?php

declare(strict_types=1);

namespace App\Form\Type\AdminUser;

use App\Entity\Tenant\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para asignar grupos a un usuario
 */
class GroupAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $organization = $options['organization'];

        $builder
            ->add('groups', EntityType::class, [
                'label' => 'Grupos',
                'class' => Group::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes
                'required' => false,
                'query_builder' => function ($repository) use ($organization) {
                    return $repository->createQueryBuilder('g')
                        ->innerJoin('g.state', 's')
                        ->where('g.organization = :organization')
                        ->andWhere('s.name = :active')
                        ->setParameter('organization', $organization)
                        ->setParameter('active', 'ACTIVE')
                        ->orderBy('g.name', 'ASC');
                },
                'choice_attr' => function (Group $group) {
                    return [
                        'data-group-id' => $group->getId(),
                        'data-group-description' => $group->getDescription() ?? '',
                    ];
                },
                'attr' => [
                    'class' => 'group-checkboxes',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'organization' => null,
        ]);

        $resolver->setRequired('organization');
    }
}
