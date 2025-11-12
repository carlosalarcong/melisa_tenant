<?php

namespace App\Repository;

use App\Entity\Member;
use App\Entity\ResetPasswordRequest;
use Doctrine\ORM\EntityRepository;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

/**
 * @extends EntityRepository<ResetPasswordRequest>
 */
class ResetPasswordRequestRepository extends EntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function __construct(TenantEntityManager $em)
    {
        parent::__construct($em, $em->getClassMetadata(ResetPasswordRequest::class));
    }

    /**
     * @param Member $user
     */
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        return new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);
    }
}
