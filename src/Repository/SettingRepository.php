<?php

namespace App\Repository;

use App\Entity\Setting;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Doctrine\ORM\EntityRepository;

class SettingRepository extends EntityRepository
{
    private TenantEntityManager $entityManager;

    public function __construct(TenantEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $entityManager->getClassMetadata(Setting::class));
    }

    public function getAllSetting(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM setting s
            ORDER BY s.id ASC
            ';

        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();

    }


}
