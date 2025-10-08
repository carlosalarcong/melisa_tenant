<?php

namespace App\Entity;

use App\Repository\TenantMemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TenantMemberRepository::class)]
class TenantMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'member')]
    private ?Tenant $tenant = null;

    #[ORM\ManyToOne(inversedBy: 'tenantMembers')]
    private ?Member $member = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(nullable: true)]
    private ?array $permissions = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $last_access_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expires_at = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $invited_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invitation_token = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $accepted_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getLastAccessAt(): ?\DateTimeImmutable
    {
        return $this->last_access_at;
    }

    public function setLastAccessAt(?\DateTimeImmutable $last_access_at): static
    {
        $this->last_access_at = $last_access_at;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expires_at;
    }

    public function setExpiresAt(?\DateTimeImmutable $expires_at): static
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    public function getInvitedBy(): ?string
    {
        return $this->invited_by;
    }

    public function setInvitedBy(?string $invited_by): static
    {
        $this->invited_by = $invited_by;

        return $this;
    }

    public function getInvitationToken(): ?string
    {
        return $this->invitation_token;
    }

    public function setInvitationToken(?string $invitation_token): static
    {
        $this->invitation_token = $invitation_token;

        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->accepted_at;
    }

    public function setAcceptedAt(?\DateTimeImmutable $accepted_at): static
    {
        $this->accepted_at = $accepted_at;

        return $this;
    }
}
