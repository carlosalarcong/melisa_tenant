<?php

namespace App\Repository;

use App\Entity\Member;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Repository para Member usando TenantEntityManager
 * 
 * IMPORTANTE: No extiende ServiceEntityRepository porque necesitamos
 * usar TenantEntityManager en lugar de ManagerRegistry
 */
class MemberRepository extends EntityRepository
{
    private TenantEntityManager $entityManager;

    public function __construct(TenantEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $entityManager->getClassMetadata(Member::class));
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
