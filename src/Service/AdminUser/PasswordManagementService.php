<?php

declare(strict_types=1);

namespace App\Service\AdminUser;

use App\Entity\Tenant\Member;
use App\Entity\Tenant\PasswordHistory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Servicio para gestión de contraseñas e historial
 * 
 * Funcionalidades:
 * - Guardar historial de contraseñas
 * - Validar que contraseña no se repita
 * - Verificar expiración de contraseñas
 * - Actualizar contraseñas con validaciones
 */
class PasswordManagementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private int $passwordHistoryCount = 5,
        private int $passwordExpirationDays = 90
    ) {}

    /**
     * Guardar contraseña en el historial
     * 
     * @param Member $member
     * @param string $hashedPassword Contraseña ya hasheada
     */
    public function savePasswordHistory(Member $member, string $hashedPassword): void
    {
        try {
            $passwordHistory = new PasswordHistory();
            $passwordHistory->setMember($member);
            $passwordHistory->setPasswordHash($hashedPassword);
            $passwordHistory->setCreatedAt(new \DateTime());
            
            $this->em->persist($passwordHistory);
            $this->em->flush();
            
            // Limpiar registros antiguos (mantener solo N últimos)
            $this->cleanOldPasswords($member);
            
        } catch (\Exception $e) {
            $this->logger->error('Error al guardar historial de contraseña', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validar que la nueva contraseña no esté en el historial
     * 
     * @param Member $member
     * @param string $plainPassword Contraseña en texto plano
     * @return bool True si la contraseña es válida (no está en historial)
     */
    public function isPasswordValid(Member $member, string $plainPassword): bool
    {
        try {
            $repo = $this->em->getRepository(PasswordHistory::class);
            
            // Obtener últimas N contraseñas del historial
            $oldPasswords = $repo->createQueryBuilder('ph')
                ->where('ph.member = :member')
                ->setParameter('member', $member)
                ->orderBy('ph.createdAt', 'DESC')
                ->setMaxResults($this->passwordHistoryCount)
                ->getQuery()
                ->getResult();
            
            // Verificar contra cada contraseña del historial
            foreach ($oldPasswords as $passwordHistory) {
                if (password_verify($plainPassword, $passwordHistory->getPasswordHash())) {
                    return false; // Contraseña ya utilizada
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('Error al validar historial de contraseña', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
            return true; // En caso de error, permitir el cambio
        }
    }

    /**
     * Verificar si la contraseña del usuario ha expirado
     * 
     * @param Member $member
     * @return bool True si la contraseña ha expirado
     */
    public function isPasswordExpired(Member $member): bool
    {
        $lastChange = $member->getPasswordChangedAt();
        
        if (!$lastChange) {
            return false; // Si no hay fecha, no expirar
        }
        
        $now = new \DateTimeImmutable();
        $diff = $now->diff($lastChange);
        
        return $diff->days >= $this->passwordExpirationDays;
    }

    /**
     * Obtener días restantes antes de que expire la contraseña
     * 
     * @param Member $member
     * @return int Días restantes (negativo si ya expiró)
     */
    public function getDaysUntilExpiration(Member $member): int
    {
        $lastChange = $member->getPasswordChangedAt();
        
        if (!$lastChange) {
            return $this->passwordExpirationDays;
        }
        
        $now = new \DateTimeImmutable();
        $diff = $now->diff($lastChange);
        
        return $this->passwordExpirationDays - $diff->days;
    }

    /**
     * Limpiar contraseñas antiguas del historial
     * Mantener solo las últimas N contraseñas
     * 
     * @param Member $member
     */
    private function cleanOldPasswords(Member $member): void
    {
        try {
            $repo = $this->em->getRepository(PasswordHistory::class);
            
            // Obtener todos los registros ordenados por fecha
            $allPasswords = $repo->createQueryBuilder('ph')
                ->where('ph.member = :member')
                ->setParameter('member', $member)
                ->orderBy('ph.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
            
            // Si hay más de N, eliminar los más antiguos
            if (count($allPasswords) > $this->passwordHistoryCount) {
                $toDelete = array_slice($allPasswords, $this->passwordHistoryCount);
                
                foreach ($toDelete as $oldPassword) {
                    $this->em->remove($oldPassword);
                }
                
                $this->em->flush();
            }
            
        } catch (\Exception $e) {
            $this->logger->error('Error al limpiar historial de contraseñas', [
                'memberId' => $member->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
