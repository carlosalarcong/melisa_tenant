<?php

namespace App\Controller;

use App\Entity\Tenant\Person;
use App\Security\FieldAccess;
use App\Security\Voter\PermissionVoter;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador de prueba para el sistema de permisos.
 * 
 * Permite probar manualmente el PermissionVoter con datos reales.
 */
#[Route('/person/test')]
class PersonTestController extends AbstractController
{
    public function __construct(
        private readonly TenantEntityManager $entityManager
    ) {
    }

    /**
     * Lista todas las personas (sin verificar permisos)
     * 
     * @Route("", name="person_test_list", methods=["GET"])
     */
    #[Route('', name: 'person_test_list', methods: ['GET'])]
    public function list(): Response
    {
        $persons = $this->entityManager->getRepository(Person::class)->findAll();

        return $this->render('person_test/list.html.twig', [
            'persons' => $persons,
        ]);
    }

    /**
     * Muestra una persona CON verificación de permisos.
     * 
     * Verifica que el usuario tenga permiso VIEW sobre la persona.
     * Si no tiene permiso, lanza AccessDeniedException (403).
     * 
     * @Route("/{id}", name="person_test_show", methods=["GET"])
     */
    #[Route('/{id}', name: 'person_test_show', methods: ['GET'])]
    #[IsGranted(PermissionVoter::VIEW, subject: 'person')]
    public function show(Person $person): Response
    {
        // Si llegamos aquí, el usuario SÍ tiene permiso VIEW

        // Verificar permisos de campos específicos
        $canEditEmail = $this->isGranted(
            PermissionVoter::EDIT,
            new FieldAccess($person, 'email')
        );

        $canEditName = $this->isGranted(
            PermissionVoter::EDIT,
            new FieldAccess($person, 'name')
        );

        $canDelete = $this->isGranted(PermissionVoter::DELETE, $person);

        return $this->render('person_test/show.html.twig', [
            'person' => $person,
            'canEditEmail' => $canEditEmail,
            'canEditName' => $canEditName,
            'canDelete' => $canDelete,
        ]);
    }

    /**
     * Edita una persona CON verificación de permisos.
     * 
     * Verifica que el usuario tenga permiso EDIT sobre la persona.
     * 
     * @Route("/{id}/edit", name="person_test_edit", methods=["GET"])
     */
    #[Route('/{id}/edit', name: 'person_test_edit', methods: ['GET'])]
    #[IsGranted(PermissionVoter::EDIT, subject: 'person')]
    public function edit(Person $person): Response
    {
        return $this->render('person_test/edit.html.twig', [
            'person' => $person,
        ]);
    }

    /**
     * Elimina una persona CON verificación de permisos.
     * 
     * Verifica que el usuario tenga permiso DELETE sobre la persona.
     * 
     * @Route("/{id}/delete", name="person_test_delete", methods=["POST"])
     */
    #[Route('/{id}/delete', name: 'person_test_delete', methods: ['POST'])]
    #[IsGranted(PermissionVoter::DELETE, subject: 'person')]
    public function delete(Person $person): Response
    {
        $this->entityManager->remove($person);
        $this->entityManager->flush();

        $this->addFlash('success', 'Persona eliminada correctamente');

        return $this->redirectToRoute('person_test_list');
    }

    /**
     * Prueba de permisos en código (sin atributo IsGranted)
     * 
     * Demuestra verificación manual de permisos.
     * 
     * @Route("/{id}/manual-check", name="person_test_manual_check", methods=["GET"])
     */
    #[Route('/{id}/manual-check', name: 'person_test_manual_check', methods: ['GET'])]
    public function manualCheck(Person $person): Response
    {
        // Verificación manual con isGranted()
        if (!$this->isGranted(PermissionVoter::VIEW, $person)) {
            throw $this->createAccessDeniedException('No tienes permiso para ver esta persona');
        }

        $permissions = [
            'view' => $this->isGranted(PermissionVoter::VIEW, $person),
            'edit' => $this->isGranted(PermissionVoter::EDIT, $person),
            'delete' => $this->isGranted(PermissionVoter::DELETE, $person),
            'edit_email' => $this->isGranted(
                PermissionVoter::EDIT,
                new FieldAccess($person, 'email')
            ),
            'edit_name' => $this->isGranted(
                PermissionVoter::EDIT,
                new FieldAccess($person, 'name')
            ),
            'view_mobilePhone' => $this->isGranted(
                PermissionVoter::VIEW,
                new FieldAccess($person, 'mobilePhone')
            ),
        ];

        return $this->render('person_test/manual_check.html.twig', [
            'person' => $person,
            'permissions' => $permissions,
        ]);
    }
}
