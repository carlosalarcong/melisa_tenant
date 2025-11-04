<?php

namespace App\Service;

use App\Repository\MemberRepository;

class AuthenticationService
{
    public function __construct(
        private MemberRepository $memberRepository
    ) {}

    public function authenticateUser(string $username, string $password): ?array
    {
        $user = $this->memberRepository->findActiveUserByUsername($username);
        
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
