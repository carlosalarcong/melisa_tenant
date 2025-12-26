<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Grupo;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Perfil;
use App\Enum\ProfileStateEnum;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Servicio para gestión de perfiles y grupos de usuarios
 * 
 * REGLAS CRÍTICAS:
 * - Estado INACTIVE en perfil = EXCLUSIÓN EXPLÍCITA (no hereda de grupos)
 * - Los grupos asignan perfiles, pero exclusiones anulan herencia
 * - Perfiles definen acceso a módulos del sistema
 */
class ProfileManagementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserSessionService $sessionService,
        private LoggerInterface $logger
    ) {}

    /**
     * Actualizar perfiles de un usuario
     * 
     * @param Member $usuario
     * @param array $profileData Formato: ['profileId' => 'ACTIVE|INACTIVE']
     */
    public function updateUserProfiles(Member $usuario, array $profileData): void
    {
        $this->em->beginTransaction();
        
        try {
            // Limpiar perfiles actuales
            $conn = $this->em->getConnection();
            $conn->delete('perfil_usuario', ['id_usuario_rebsol' => $usuario->getId()]);
            
            // Insertar nuevos perfiles
            foreach ($profileData as $profileId => $state) {
                $stateValue = $state === 'ACTIVE' ? ProfileStateEnum::ACTIVE->value : ProfileStateEnum::INACTIVE->value;
                
                $conn->insert('perfil_usuario', [
                    'id_perfil' => $profileId,
                    'id_usuario_rebsol' => $usuario->getId(),
                    'estado' => $stateValue
                ]);
            }
            
            $this->em->flush();
            $this->em->commit();
            
            // Forzar logout porque cambiaron permisos
            $this->sessionService->forceLogout($usuario);
            
            $this->logger->info('Perfiles de usuario actualizados', [
                'userId' => $usuario->getId(),
                'profileCount' => count($profileData)
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar perfiles', [
                'userId' => $usuario->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener perfiles activos de un usuario
     * Considera herencia de grupos y exclusiones explícitas
     * 
     * @param Member $usuario
     * @return array Array de IDs de perfiles activos
     */
    public function getActiveProfiles(Member $usuario): array
    {
        $conn = $this->em->getConnection();
        
        // Query que considera exclusiones explícitas
        $sql = "
            SELECT DISTINCT p.id_perfil
            FROM perfil p
            WHERE p.id_perfil IN (
                -- Perfiles desde grupos
                SELECT gp.id_perfil
                FROM grupo g
                INNER JOIN grupo_usuario gu ON g.id_grupo = gu.id_grupo
                INNER JOIN grupo_perfil gp ON g.id_grupo = gp.id_grupo
                WHERE gu.id_usuario_rebsol = :userId
                AND g.estado = 1
                AND gp.estado = 1
                
                UNION
                
                -- Perfiles directos ACTIVOS
                SELECT pu.id_perfil
                FROM perfil_usuario pu
                WHERE pu.id_usuario_rebsol = :userId
                AND pu.estado = 1
            )
            -- EXCLUIR perfiles con estado INACTIVE (exclusión explícita)
            AND p.id_perfil NOT IN (
                SELECT pu.id_perfil
                FROM perfil_usuario pu
                WHERE pu.id_usuario_rebsol = :userId
                AND pu.estado = 0  -- INACTIVE = EXCLUSIÓN
            )
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['userId' => $usuario->getId()]);
        
        return array_column($result->fetchAllAssociative(), 'id_perfil');
    }

    /**
     * Actualizar grupos de un usuario
     * 
     * @param Member $usuario
     * @param array $groupIds IDs de grupos a asignar
     */
    public function updateUserGroups(Member $usuario, array $groupIds): void
    {
        $this->em->beginTransaction();
        
        try {
            // Limpiar grupos actuales
            $conn = $this->em->getConnection();
            $conn->delete('grupo_usuario', ['id_usuario_rebsol' => $usuario->getId()]);
            
            // Insertar nuevos grupos
            foreach ($groupIds as $groupId) {
                $conn->insert('grupo_usuario', [
                    'id_grupo' => $groupId,
                    'id_usuario_rebsol' => $usuario->getId()
                ]);
            }
            
            $this->em->flush();
            $this->em->commit();
            
            // Forzar logout porque cambiaron permisos
            $this->sessionService->forceLogout($usuario);
            
            $this->logger->info('Grupos de usuario actualizados', [
                'userId' => $usuario->getId(),
                'groupCount' => count($groupIds)
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar grupos', [
                'userId' => $usuario->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener grupos de un usuario
     * 
     * @param Member $usuario
     * @return Grupo[]
     */
    public function getUserGroups(Member $usuario): array
    {
        $conn = $this->em->getConnection();
        
        $sql = "
            SELECT g.*
            FROM grupo g
            INNER JOIN grupo_usuario gu ON g.id_grupo = gu.id_grupo
            WHERE gu.id_usuario_rebsol = :userId
            AND g.estado = 1
            ORDER BY g.nombre
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['userId' => $usuario->getId()]);
        
        return $result->fetchAllAssociative();
    }
}
