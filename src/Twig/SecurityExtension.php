<?php

namespace App\Twig;

use App\Security\FieldAccess;
use App\Security\SecuredResourceInterface;
use App\Security\Voter\PermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extensión Twig para funcionalidades de seguridad y permisos granulares.
 * 
 * Proporciona funciones para verificar permisos a nivel de campo desde plantillas Twig,
 * permitiendo control granular de qué campos se muestran o editan según los permisos del usuario.
 * 
 * @see PermissionVoter Para la lógica de resolución de permisos
 * @see FieldAccess Para el value object que representa acceso a campos
 */
class SecurityExtension extends AbstractExtension
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            // Función para crear FieldAccess (uso con is_granted)
            new TwigFunction('field_access', [$this, 'createFieldAccess']),
            
            // Helpers directos para verificar permisos de campos
            new TwigFunction('can_view_field', [$this, 'canViewField']),
            new TwigFunction('can_edit_field', [$this, 'canEditField']),
            new TwigFunction('can_delete_field', [$this, 'canDeleteField']),
        ];
    }

    /**
     * Crea un objeto FieldAccess para usar con is_granted().
     * 
     * Uso en Twig:
     * ```twig
     * {% if is_granted('VIEW', field_access(person, 'email')) %}
     *     Email: {{ person.email }}
     * {% endif %}
     * 
     * {% if is_granted('EDIT', field_access(person, 'salary')) %}
     *     <input name="salary" value="{{ person.salary }}">
     * {% endif %}
     * ```
     * 
     * @param SecuredResourceInterface $resource El recurso protegido
     * @param string $fieldName El nombre del campo a verificar
     * @return FieldAccess Objeto para usar con is_granted()
     */
    public function createFieldAccess(SecuredResourceInterface $resource, string $fieldName): FieldAccess
    {
        return new FieldAccess($resource, $fieldName);
    }

    /**
     * Verifica si el usuario actual puede VER un campo específico.
     * 
     * Helper directo equivalente a: is_granted('VIEW', new FieldAccess(...))
     * 
     * Uso en Twig:
     * ```twig
     * {% if can_view_field(person, 'salary') %}
     *     <p>Salario: {{ person.salary }}</p>
     * {% endif %}
     * 
     * {% if can_view_field(patient, 'diagnosis') %}
     *     <div class="diagnosis">{{ patient.diagnosis }}</div>
     * {% endif %}
     * ```
     * 
     * @param SecuredResourceInterface $resource El recurso protegido
     * @param string $fieldName El nombre del campo
     * @return bool true si tiene permiso VIEW, false en caso contrario
     */
    public function canViewField(SecuredResourceInterface $resource, string $fieldName): bool
    {
        return $this->security->isGranted(
            PermissionVoter::VIEW,
            new FieldAccess($resource, $fieldName)
        );
    }

    /**
     * Verifica si el usuario actual puede EDITAR un campo específico.
     * 
     * Helper directo equivalente a: is_granted('EDIT', new FieldAccess(...))
     * 
     * Uso en Twig:
     * ```twig
     * {% if can_edit_field(person, 'email') %}
     *     <input name="email" value="{{ person.email }}">
     * {% else %}
     *     <p>Email: {{ person.email }} (solo lectura)</p>
     * {% endif %}
     * 
     * <div class="field {{ can_edit_field(person, 'name') ? 'editable' : 'readonly' }}">
     *     {{ person.name }}
     * </div>
     * ```
     * 
     * @param SecuredResourceInterface $resource El recurso protegido
     * @param string $fieldName El nombre del campo
     * @return bool true si tiene permiso EDIT, false en caso contrario
     */
    public function canEditField(SecuredResourceInterface $resource, string $fieldName): bool
    {
        return $this->security->isGranted(
            PermissionVoter::EDIT,
            new FieldAccess($resource, $fieldName)
        );
    }

    /**
     * Verifica si el usuario actual puede ELIMINAR un campo específico.
     * 
     * Helper directo equivalente a: is_granted('DELETE', new FieldAccess(...))
     * 
     * Uso en Twig:
     * ```twig
     * {% if can_delete_field(person, 'medicalHistory') %}
     *     <button type="button" onclick="deleteField('medicalHistory')">
     *         Eliminar historial
     *     </button>
     * {% endif %}
     * ```
     * 
     * @param SecuredResourceInterface $resource El recurso protegido
     * @param string $fieldName El nombre del campo
     * @return bool true si tiene permiso DELETE, false en caso contrario
     */
    public function canDeleteField(SecuredResourceInterface $resource, string $fieldName): bool
    {
        return $this->security->isGranted(
            PermissionVoter::DELETE,
            new FieldAccess($resource, $fieldName)
        );
    }
}
