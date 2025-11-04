<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class AuthenticationService
{
    public function authenticateUser(Connection $tenantConnection, string $username, string $password): ?array
    {
        $userQuery = '
            SELECT id, username, password, first_name, last_name, email, is_active
            FROM member 
            WHERE username = ? AND is_active = true
        ';
        
        $userResult = $tenantConnection->executeQuery($userQuery, [$username]);
        $user = $userResult->fetchAssociative();
        
        if (!$user) {
            return null;
        }

        // Verificar contraseña
        if (!password_verify($password, $user['password'])) {
            return null;
        }

        // Remover password del array antes de retornar
        unset($user['password']);
        
        return $user;
    }
}
