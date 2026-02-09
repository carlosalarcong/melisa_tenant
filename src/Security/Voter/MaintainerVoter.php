<?php

namespace App\Security\Voter;

use App\Entity\Tenant\Member;
use App\Repository\Tenant\MaintainerRolePermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter para gestionar permisos de Mantenedores (Master Data)
 * 
 * CAMBIO ARQUITECTÓNICO (Sprint 1.5):
 * La matriz de permisos ahora se almacena en base de datos (tabla maintainer_role_permission)
 * en lugar de estar hardcoded. Esto permite administrar permisos dinámicamente mediante un
 * mantenedor CRUD sin modificar código.
 * 
 * Ventajas del nuevo approach:
 * ✅ Permisos administrables desde UI
 * ✅ Cambios en tiempo real sin despliegue
 * ✅ Auditoría de cambios en permisos
 * ✅ Granularidad por categoría y mantenedor
 * ✅ Cache automático para mantener performance
 * 
 * Performance:
 * - Request 1: 1 query a DB + cache
 * - Request 2-N: 0 queries (lee de cache Redis + in-memory)
 * 
 * Uso en Controller:
 * ```php
 * $this->denyAccessUnlessGranted(MaintainerVoter::CREATE, Gender::class);
 * $this->denyAccessUnlessGranted(MaintainerVoter::UPDATE, $entity);
 * ```
 * 
 * Uso en Twig:
 * ```twig
 * {% if is_granted(constant('App\\Security\\Voter\\MaintainerVoter::CREATE'), entity_class) %}
 *     <button>Crear</button>
 * {% endif %}
 * ```
 * 
 * @author Melisa Development Team
 * @since Sprint 1.5 - Database-driven permissions (Feb 2026)
 */
class MaintainerVoter extends Voter
{
    // ===== CONSTANTES DE PERMISOS =====
    
    /** Permiso para crear nuevos registros */
    public const CREATE = 'MAINTAINER_CREATE';
    
    /** Permiso para ver/listar registros (lectura) */
    public const READ = 'MAINTAINER_READ';
    
    /** Permiso para editar registros existentes */
    public const UPDATE = 'MAINTAINER_UPDATE';
    
    /** Permiso para eliminar registros */
    public const DELETE = 'MAINTAINER_DELETE';
    
    /** Permiso para exportar datos a CSV */
    public const EXPORT = 'MAINTAINER_EXPORT';

    /**
     * Repositorio para consultar permisos desde base de datos
     */
    public function __construct(
        private readonly MaintainerRolePermissionRepository $permissionRepository
    ) {
    }

    /**
     * Determina si este voter puede votar sobre el atributo y subject dados.
     * 
     * @param string $attribute El permiso solicitado (CREATE, READ, UPDATE, DELETE, EXPORT)
     * @param mixed $subject La entidad o clase sobre la que se solicita el permiso
     * @return bool True si el voter puede votar, false en caso contrario
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Soportar los 5 atributos de mantenedores
        $supportedAttributes = [
            self::CREATE,
            self::READ,
            self::UPDATE,
            self::DELETE,
            self::EXPORT,
        ];
        
        if (!in_array($attribute, $supportedAttributes)) {
            return false;
        }
        
        // El subject puede ser:
        // 1. Una entidad objeto (para UPDATE, DELETE)
        // 2. Un string con el nombre de la clase (para CREATE, READ)
        // 3. Null (para operaciones generales)
        return true;
    }

    /**
     * Vota sobre el permiso solicitado.
     * 
     * Esta es la lógica central del voter. Implementa una estrategia
     * de permisos por niveles con fallback.
     * 
     * @param string $attribute El permiso solicitado
     * @param mixed $subject La entidad o clase
     * @param TokenInterface $token El token de autenticación
     * @return bool True si se concede el permiso, false si se deniega
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // El usuario debe estar autenticado y ser instancia de Member
        if (!$user instanceof Member) {
            return false;
        }

        // FASE 1: Role-based simple
        // Para el MVP, solo verificamos roles (rápido y sin queries a DB)
        if ($this->canAccessByRole($user, $attribute, $subject)) {
            return true;
        }

        // FASE 3 (Futuro): Permission-based granular
        // if ($this->hasSpecificPermissionOverride($user, $subject, $attribute)) {
        //     return $this->permissionVoter->vote(...);
        // }

        // Por defecto, denegar acceso (secure by default)
        return false;
    }

    /**
     * Verifica si el usuario tiene acceso basado en sus roles consultando la base de datos.
     * 
     * NUEVO APPROACH (Sprint 1.5):
     * La matriz de permisos se consulta desde la tabla maintainer_role_permission.
     * Se usa cache Redis + in-memory para mantener performance óptima.
     * 
     * Lógica:
     * 1. Iterar sobre todos los roles del usuario
     * 2. Para cada rol, consultar permisos desde DB (con cache)
     * 3. Si algún rol concede el permiso, retornar true
     * 4. Si ningún rol concede, retornar false (deny by default)
     * 
     * @param Member $user El usuario autenticado
     * @param string $attribute El permiso solicitado
     * @param mixed $subject La entidad o clase
     * @return bool True si el rol permite el acceso
     */
    private function canAccessByRole(Member $user, string $attribute, mixed $subject): bool
    {
        $roles = $user->getRoles();
        $category = $this->getMaintenedorCategory($subject);
        $maintainer = $this->getMaintainerName($subject);

        // Iterar sobre todos los roles del usuario
        foreach ($roles as $role) {
            // Consultar permisos desde DB (con cache)
            if ($this->permissionRepository->hasPermission($role, $attribute, $category, $maintainer)) {
                return true;
            }
        }

        // Por defecto, denegar (secure by default)
        return false;
    }
    
    /**
     * Obtiene la categoría de un mantenedor a partir de su clase.
     * 
     * Esta función será útil en la Fase 2 cuando implementemos
     * permisos granulares por categoría.
     * 
     * Mapeo de namespaces a categorías:
     * - App\Entity\Tenant\Basic\* → 'basic'
     * - App\Entity\Tenant\Clinical\* → 'clinical'
     * - App\Entity\Tenant\Commercial\* → 'commercial'
     * - App\Entity\Tenant\Hospital\* → 'hospital'
     * - App\Entity\Tenant\Human\* → 'human'
     * 
     * @param mixed $subject La entidad o clase
     * @return string|null La categoría del mantenedor o null si no se puede determinar
     */
    private function getMaintenedorCategory(mixed $subject): ?string
    {
        // Obtener el nombre de la clase
        $className = is_object($subject) ? get_class($subject) : $subject;
        
        if (!is_string($className)) {
            return null;
        }
        
        // Extraer categoría del namespace
        // Ejemplo: App\Entity\Tenant\Basic\Gender → 'basic'
        if (preg_match('/App\\\\Entity\\\\Tenant\\\\(\w+)\\\\/', $className, $matches)) {
            return strtolower($matches[1]);
        }
        
        return null;
    }

    /**
     * Obtiene el nombre del mantenedor a partir de la clase.
     * 
     * @param mixed $subject La entidad o clase
     * @return string|null El nombre simple de la clase (ej: 'Gender', 'Disease')
     */
    private function getMaintainerName(mixed $subject): ?string
    {
        $className = is_object($subject) ? get_class($subject) : $subject;
        
        if (!is_string($className)) {
            return null;
        }
        
        // Extraer nombre simple de la clase
        // Ejemplo: App\Entity\Tenant\Gender → 'Gender'
        $parts = explode('\\', $className);
        return end($parts) ?: null;
    }
}
