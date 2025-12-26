<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Group;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Profile;
use App\Entity\Tenant\MemberProfile;
use App\Entity\Tenant\MemberGroup;
use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use App\Repository\MemberProfileRepository;
use App\Repository\MemberGroupRepository;
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
        private ProfileRepository $profileRepository,
        private GroupRepository $groupRepository,
        private MemberProfileRepository $memberProfileRepository,
        private MemberGroupRepository $memberGroupRepository,
        private UserSessionService $sessionService,
        private LoggerInterface $logger
    ) {}

    /**
     * Actualizar perfiles de un usuario
     * 
     * @param Member $member
     * @param array $profileData Formato: ['profileId' => true|false] (true=ACTIVE, false=INACTIVE/EXCLUSION)
     */
    public function updateMemberProfiles(Member $member, array $profileData): void
    {
        $this->em->beginTransaction();
        
        try {
            // Limpiar perfiles actuales
            $this->memberProfileRepository->removeAllByMember($member);
            
            // Crear nuevas relaciones
            foreach ($profileData as $profileId => $isActive) {
                $profile = $this->em->getReference(Profile::class, $profileId);
                
                $memberProfile = new MemberProfile();
                $memberProfile->setMember($member);
                $memberProfile->setProfile($profile);
                $memberProfile->setIsActive($isActive);
                
                $this->em->persist($memberProfile);
            }
            
            $this->em->flush();
            $this->em->commit();
            
            // Forzar logout porque cambiaron permisos
            $this->sessionService->forceLogout($member);
            
            $this->logger->info('Perfiles de usuario actualizados', [
                'memberId' => $member->getId(),
                'profileCount' => count($profileData)
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar perfiles', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener IDs de perfiles activos de un usuario
     * Considera herencia de grupos y exclusiones explícitas
     * 
     * @param Member $member
     * @return array Array de IDs de perfiles activos
     */
    public function getActiveProfiles(Member $member): array
    {
        return $this->profileRepository->getActiveMemberProfiles($member);
    }

    /**
     * Actualizar grupos de un usuario
     * 
     * @param Member $member
     * @param array $groupIds Array de IDs de grupos
     */
    public function updateMemberGroups(Member $member, array $groupIds): void
    {
        $this->em->beginTransaction();
        
        try {
            // Limpiar grupos actuales
            $this->memberGroupRepository->removeAllByMember($member);
            
            // Crear nuevas relaciones
            foreach ($groupIds as $groupId) {
                $group = $this->em->getReference(Group::class, $groupId);
                
                $memberGroup = new MemberGroup();
                $memberGroup->setMember($member);
                $memberGroup->setGroup($group);
                
                $this->em->persist($memberGroup);
            }
            
            $this->em->flush();
            $this->em->commit();
            
            // Forzar logout porque cambiaron permisos
            $this->sessionService->forceLogout($member);
            
            $this->logger->info('Grupos de usuario actualizados', [
                'memberId' => $member->getId(),
                'groupCount' => count($groupIds)
            ]);
            
        } catch (\Exception $e) {
            $this->em->rollback();
            $this->logger->error('Error al actualizar grupos', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener grupos activos de un usuario
     * 
     * @param Member $member
     * @return array Array de entidades Group
     */
    public function getMemberGroups(Member $member): array
    {
        return $this->groupRepository->getActiveMemberGroups($member);
    }
}
