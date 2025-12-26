<?php

declare(strict_types=1);

namespace App\Form\Type\AdminUser;

use App\Entity\Tenant\Profile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para asignar perfiles a un usuario
 */
class ProfileAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $organization = $options['organization'];

        $builder
            ->add('profiles', EntityType::class, [
                'label' => 'Perfiles',
                'class' => Profile::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes
                'required' => false,
                'query_builder' => function ($repository) use ($organization) {
                    return $repository->createQueryBuilder('p')
                        ->innerJoin('p.state', 's')
                        ->where('p.organization = :organization')
                        ->andWhere('s.name = :active')
                        ->setParameter('organization', $organization)
                        ->setParameter('active', 'ACTIVE')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_attr' => function (Profile $profile) {
                    return [
                        'data-profile-id' => $profile->getId(),
                        'data-profile-description' => $profile->getDescription() ?? '',
                    ];
                },
                'attr' => [
                    'class' => 'profile-checkboxes',
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
