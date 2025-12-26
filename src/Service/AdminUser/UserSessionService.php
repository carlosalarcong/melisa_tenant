<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Member;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Servicio para gestión de sesiones de usuarios
 * 
 * Funcionalidades:
 * - Forzar logout de usuario (al cambiar contraseña/permisos)
 * - Desbloquear usuario
 * - Gestionar intentos de login
 */
class UserSessionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokenStorage,
        private LoggerInterface $logger,
        private int $maxFailedAttempts = 5
    ) {}

    /**
     * Forzar logout del usuario
     * Se debe llamar cuando cambia contraseña o permisos
     * 
     * @param Member $member
     */
    public function forceLogout(Member $member): void
    {
        try {
            // Invalidar token actual si el usuario está logueado
            $currentUser = $this->tokenStorage->getToken()?->getUser();
            
            if ($currentUser instanceof Member && 
                $currentUser->getId() === $member->getId()) {
                $this->tokenStorage->setToken(null);
            }
            
            // TODO: Implementar invalidación de sesiones activas en BD
            // Esto requiere tracking de sesiones (tabla user_sessions)
            
            $this->logger->info('Usuario forzado a logout', [
                'userId' => $member->getId(),
                'username' => $member->getUserIdentifier()
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Error al forzar logout', [
                'userId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Desbloquear usuario después de intentos fallidos
     * 
     * @param Member $member
     */
    public function unlockUser(Member $member): void
    {
        try {
            $member->resetLoginAttempts();
            $member->setUpdatedAt(new \DateTimeImmutable());
            
            $this->em->flush();
            
            $this->logger->info('Usuario desbloqueado', [
                'userId' => $member->getId(),
                'username' => $member->getUserIdentifier()
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Error al desbloquear usuario', [
                'userId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Registrar intento de login fallido
     * 
     * @param Member $member
     * @return bool True si el usuario fue bloqueado
     */
    public function registerFailedAttempt(Member $member): bool
    {
        try {
            $member->incrementLoginAttempts();
            $attempts = $member->getLoginAttempts();
            
            $isBlocked = $attempts >= $this->maxFailedAttempts;
            
            if ($isBlocked) {
                $this->logger->warning('Usuario bloqueado por intentos fallidos', [
                    'userId' => $member->getId(),
                    'username' => $member->getUserIdentifier(),
                    'attempts' => $attempts
                ]);
            }
            
            $this->em->flush();
            
            return $isBlocked;
            
        } catch (\Exception $e) {
            $this->logger->error('Error al registrar intento fallido', [
                'userId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Resetear intentos de login después de login exitoso
     * 
     * @param Member $member
     */
    public function resetFailedAttempts(Member $member): void
    {
        try {
            if ($member->getLoginAttempts() > 0) {
                $member->resetLoginAttempts();
                $this->em->flush();
            }
        } catch (\Exception $e) {
            $this->logger->error('Error al resetear intentos', [
                'userId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar si el usuario está bloqueado
     * 
     * @param Member $member
     * @return bool
     */
    public function isUserLocked(Member $member): bool
    {
        return $member->getLoginAttempts() >= $this->maxFailedAttempts;
    }
}
