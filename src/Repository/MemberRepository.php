<?php

namespace App\Repository;

use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository para Member usando TenantEntityManager
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    /**
     * Busca un usuario activo por username
     */
    public function findActiveUserByUsername(string $username): ?array
    {
        $result = $this->createQueryBuilder('m')
            ->select('m.id', 'm.username', 'm.password', 'm.firstName', 'm.lastName', 'm.email', 'm.isActive')
            ->andWhere('m.username = :username')
            ->andWhere('m.isActive = :active')
            ->setParameter('username', $username)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        
        if (!$result) {
            return null;
        }
        
        // Mapear nombres de propiedades a nombres de columnas para compatibilidad
        return [
            'id' => $result['id'],
            'username' => $result['username'],
            'password' => $result['password'],
            'first_name' => $result['firstName'],
            'last_name' => $result['lastName'],
            'email' => $result['email'],
            'is_active' => $result['isActive']
        ];
    }

    /**
     * Obtiene la sucursal del usuario (mock temporal)
     * TODO: Implementar lógica real cuando se defina relación Member->Branch
     */
    public function obtenerSucursalUsuario(int $userId): ?object
    {
        // Mock temporal: retorna un objeto con ID 1 y método getId()
        // Cuando se implemente la relación Member->Branch, 
        // este método consultará la sucursal real del usuario
        return new class {
            public function getId() { return 1; }
            public function getNombre() { return 'Sucursal Principal'; }
        };
    }

    //    /**
    //     * @return Member[] Returns an array of Member objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Member
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
