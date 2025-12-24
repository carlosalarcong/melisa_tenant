<?php

namespace App\Security\Voter;

use App\Entity\Tenant\Member;
use App\Repository\Tenant\GroupPermissionRepository;
use App\Repository\Tenant\PermissionRepository;
use App\Security\FieldAccess;
use App\Security\SecuredResourceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter para gestionar permisos granulares a nivel de recurso y campo.
 * 
 * Implementa un sistema de permisos en cascada:
 * 1. Permisos específicos de usuario
 * 2. Permisos de grupos del usuario
 * 3. Denegación por defecto
 * 
 * OPTIMIZACIÓN: Cache in-memory de permisos por request.
 * Los permisos se cargan una sola vez del usuario y grupos, y se reutilizan
 * durante todo el request, reduciendo de 20-40 queries a solo 2 queries.
 */
class PermissionVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    /**
     * Cache in-memory de permisos de usuario indexados por usuario ID.
     * Formato: [userId => [domain => [Permission, ...]]]
     * 
     * @var array<int, array<string, array>>
     */
    private array $userPermissionsCache = [];

    /**
     * Cache in-memory de permisos de grupos indexados por usuario ID.
     * Formato: [userId => [domain => [GroupPermission, ...]]]
     * 
     * @var array<int, array<string, array>>
     */
    private array $groupPermissionsCache = [];

    public function __construct(
        private readonly PermissionRepository $permissionRepository,
        private readonly GroupPermissionRepository $groupPermissionRepository
    ) {
    }

    /**
     * Determina si este voter puede votar sobre el atributo y subject dados.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Soporta los atributos VIEW, EDIT, DELETE
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // Soporta recursos que implementen SecuredResourceInterface o FieldAccess
        return $subject instanceof SecuredResourceInterface || $subject instanceof FieldAccess;
    }

    /**
     * Vota sobre el permiso solicitado.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // El usuario debe estar autenticado y ser instancia de Member
        if (!$user instanceof Member) {
            return false;
        }

        // Extraer dominio, resourceId y fieldName del subject
        if ($subject instanceof FieldAccess) {
            $domain = $subject->resource->getPermissionDomain();
            $resourceId = $subject->resource->getPermissionId();
            $fieldName = $subject->field;
        } elseif ($subject instanceof SecuredResourceInterface) {
            $domain = $subject->getPermissionDomain();
            $resourceId = $subject->getPermissionId();
            $fieldName = null;
        } else {
            return false;
        }

        // Resolver permisos en cascada
        return $this->resolvePermission($user, $domain, $resourceId, $fieldName, $attribute);
    }

    /**
     * Resuelve el permiso en cascada: usuario → grupos → denegación por defecto.
     */
    private function resolvePermission(
        Member $user,
        string $domain,
        ?int $resourceId,
        ?string $fieldName,
        string $attribute
    ): bool {
        // 1. Intentar resolver por permisos de usuario (prioridad más alta)
        $userPermission = $this->resolveForUser($user, $domain, $resourceId, $fieldName, $attribute);
        if ($userPermission !== null) {
            return $userPermission;
        }

        // 2. Intentar resolver por permisos de grupos
        $groupPermission = $this->resolveForGroups($user, $domain, $resourceId, $fieldName, $attribute);
        if ($groupPermission !== null) {
            return $groupPermission;
        }

        // 3. Denegación por defecto
        return false;
    }

    /**
     * Resuelve permisos específicos del usuario en cascada (específico → general).
     * 
     * Cascada de búsqueda:
     * 1. domain + resourceId + fieldName (más específico)
     * 2. domain + resourceId (todos los campos del recurso)
     * 3. domain + fieldName (campo específico en todos los recursos)
     * 4. domain (todos los recursos y campos del dominio)
     * 
     * OPTIMIZACIÓN: Usa cache in-memory para evitar múltiples queries.
     */
    private function resolveForUser(
        Member $user,
        string $domain,
        ?int $resourceId,
        ?string $fieldName,
        string $attribute
    ): ?bool {
        // Cargar permisos del usuario (con cache in-memory)
        $permissions = $this->loadUserPermissions($user, $domain);

        // 1. Buscar permiso específico: domain + resourceId + fieldName
        if ($resourceId !== null && $fieldName !== null) {
            foreach ($permissions as $permission) {
                if ($permission->getResourceId() === $resourceId && $permission->getFieldName() === $fieldName) {
                    return $this->checkPermissionFlag($permission, $attribute);
                }
            }
        }

        // 2. Buscar permiso para el recurso completo: domain + resourceId + NULL fieldName
        if ($resourceId !== null) {
            foreach ($permissions as $permission) {
                if ($permission->getResourceId() === $resourceId && $permission->getFieldName() === null) {
                    return $this->checkPermissionFlag($permission, $attribute);
                }
            }
        }

        // 3. Buscar permiso para el campo en todos los recursos: domain + NULL resourceId + fieldName
        if ($fieldName !== null) {
            foreach ($permissions as $permission) {
                if ($permission->getResourceId() === null && $permission->getFieldName() === $fieldName) {
                    return $this->checkPermissionFlag($permission, $attribute);
                }
            }
        }

        // 4. Buscar permiso general para el dominio: domain + NULL resourceId + NULL fieldName
        foreach ($permissions as $permission) {
            if ($permission->getResourceId() === null && $permission->getFieldName() === null) {
                return $this->checkPermissionFlag($permission, $attribute);
            }
        }

        // No se encontró ningún permiso de usuario aplicable
        return null;
    }

    /**
     * Resuelve permisos de grupos del usuario en cascada (específico → general).
     * 
     * Cascada de búsqueda (igual que usuario):
     * 1. domain + resourceId + fieldName
     * 2. domain + resourceId
     * 3. domain + fieldName
     * 4. domain
     * 
     * OPTIMIZACIÓN: Usa cache in-memory para evitar múltiples queries.
     */
    private function resolveForGroups(
        Member $user,
        string $domain,
        ?int $resourceId,
        ?string $fieldName,
        string $attribute
    ): ?bool {
        // Obtener los grupos del usuario
        $groups = $user->getGroups()->toArray();
        if (empty($groups)) {
            return null;
        }

        // Cargar permisos de grupos (con cache in-memory)
        $groupPermissions = $this->loadGroupPermissions($user, $groups, $domain);

        // 1. Buscar permiso específico: domain + resourceId + fieldName
        if ($resourceId !== null && $fieldName !== null) {
            foreach ($groupPermissions as $permission) {
                if ($permission->getResourceId() === $resourceId && $permission->getFieldName() === $fieldName) {
                    return $this->checkGroupPermissionFlag($permission, $attribute);
                }
            }
        }

        // 2. Buscar permiso para el recurso completo: domain + resourceId + NULL fieldName
        if ($resourceId !== null) {
            foreach ($groupPermissions as $permission) {
                if ($permission->getResourceId() === $resourceId && $permission->getFieldName() === null) {
                    return $this->checkGroupPermissionFlag($permission, $attribute);
                }
            }
        }

        // 3. Buscar permiso para el campo en todos los recursos: domain + NULL resourceId + fieldName
        if ($fieldName !== null) {
            foreach ($groupPermissions as $permission) {
                if ($permission->getResourceId() === null && $permission->getFieldName() === $fieldName) {
                    return $this->checkGroupPermissionFlag($permission, $attribute);
                }
            }
        }

        // 4. Buscar permiso general para el dominio: domain + NULL resourceId + NULL fieldName
        foreach ($groupPermissions as $permission) {
            if ($permission->getResourceId() === null && $permission->getFieldName() === null) {
                return $this->checkGroupPermissionFlag($permission, $attribute);
            }
        }

        // No se encontró ningún permiso de grupo aplicable
        return null;
    }

    /**
     * Verifica el flag de permiso según el atributo solicitado.
     */
    private function checkPermissionFlag(object $permission, string $attribute): bool
    {
        return match ($attribute) {
            self::VIEW => $permission->canView(),
            self::EDIT => $permission->canEdit(),
            self::DELETE => $permission->canDelete(),
            default => false,
        };
    }

    /**
     * Verifica el flag de permiso de grupo según el atributo solicitado.
     */
    private function checkGroupPermissionFlag(object $permission, string $attribute): bool
    {
        return match ($attribute) {
            self::VIEW => $permission->canView(),
            self::EDIT => $permission->canEdit(),
            self::DELETE => $permission->canDelete(),
            default => false,
        };
    }

    /**
     * Carga permisos del usuario con cache in-memory.
     * 
     * Si los permisos ya fueron cargados en este request, los devuelve del cache.
     * Caso contrario, los carga de la base de datos y los cachea.
     * 
     * OPTIMIZACIÓN: Reduce queries de N verificaciones a 1 query por dominio.
     * 
     * @param Member $user El usuario
     * @param string $domain El dominio de permisos (ej: 'persona', 'patient')
     * @return array Array de objetos Permission
     */
    private function loadUserPermissions(Member $user, string $domain): array
    {
        $userId = $user->getId();

        // Verificar si ya está en cache
        if (isset($this->userPermissionsCache[$userId][$domain])) {
            return $this->userPermissionsCache[$userId][$domain];
        }

        // Cargar de la base de datos
        $permissions = $this->permissionRepository->findAllByMember($user, $domain);

        // Guardar en cache
        if (!isset($this->userPermissionsCache[$userId])) {
            $this->userPermissionsCache[$userId] = [];
        }
        $this->userPermissionsCache[$userId][$domain] = $permissions;

        return $permissions;
    }

    /**
     * Carga permisos de grupos del usuario con cache in-memory.
     * 
     * Si los permisos ya fueron cargados en este request, los devuelve del cache.
     * Caso contrario, los carga de la base de datos y los cachea.
     * 
     * OPTIMIZACIÓN: Reduce queries de N verificaciones a 1 query por dominio.
     * 
     * @param Member $user El usuario
     * @param array $groups Los grupos del usuario
     * @param string $domain El dominio de permisos (ej: 'persona', 'patient')
     * @return array Array de objetos GroupPermission
     */
    private function loadGroupPermissions(Member $user, array $groups, string $domain): array
    {
        $userId = $user->getId();

        // Verificar si ya está en cache
        if (isset($this->groupPermissionsCache[$userId][$domain])) {
            return $this->groupPermissionsCache[$userId][$domain];
        }

        // Cargar de la base de datos
        $groupPermissions = $this->groupPermissionRepository->findByGroups($groups, $domain);

        // Guardar en cache
        if (!isset($this->groupPermissionsCache[$userId])) {
            $this->groupPermissionsCache[$userId] = [];
        }
        $this->groupPermissionsCache[$userId][$domain] = $groupPermissions;

        return $groupPermissions;
    }
}
