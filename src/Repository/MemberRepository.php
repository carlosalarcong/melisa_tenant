<?php

namespace App\Repository;

use App\Entity\Tenant\Member;
use App\Entity\Tenant\Organization;
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
     * Busca miembros por filtros
     * 
     * @param Organization $organization
     * @param array $filters ['search' => string, 'state' => int, 'role' => int, 'userType' => string]
     * @return Member[]
     */
    public function findByFilters(Organization $organization, array $filters = []): array
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.person', 'p')
            ->innerJoin('p.organization', 'o')
            ->leftJoin('m.state', 's')
            ->leftJoin('m.role', 'r')
            ->where('o.id = :organizationId')
            ->setParameter('organizationId', $organization->getId())
            ->orderBy('p.lastName', 'ASC')
            ->addOrderBy('p.name', 'ASC');

        // Filtro de búsqueda (nombre, apellido, email, username)
        if (!empty($filters['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('p.name', ':search'),
                    $qb->expr()->like('p.lastName', ':search'),
                    $qb->expr()->like('m.email', ':search'),
                    $qb->expr()->like('m.username', ':search')
                )
            )
            ->setParameter('search', '%' . $filters['search'] . '%');
        }

        // Filtro por estado
        if (!empty($filters['state'])) {
            $qb->andWhere('s.id = :stateId')
               ->setParameter('stateId', $filters['state']);
        }

        // Filtro por rol
        if (!empty($filters['role'])) {
            $qb->andWhere('r.id = :roleId')
               ->setParameter('roleId', $filters['role']);
        }

        // Filtro por tipo de usuario
        if (!empty($filters['userType'])) {
            $qb->andWhere('m.userType = :userType')
               ->setParameter('userType', $filters['userType']);
        }

        return $qb->getQuery()->getResult();
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
