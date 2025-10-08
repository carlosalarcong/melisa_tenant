<?php

namespace App\Entity;

use App\Repository\TenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
class Tenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $subdomain = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domain = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $database_name = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $contact_email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $contact_phone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contact_name = null;

    #[ORM\Column(length: 50)]
    private ?string $plan = null;

    #[ORM\Column(nullable: true)]
    private ?int $max_users = null;

    #[ORM\Column(nullable: true)]
    private ?array $features = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $trial_ends_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $subscription_ends_at = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $logo_url = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $timezone = null;

    /**
     * @var Collection<int, TenantMember>
     */
    #[ORM\OneToMany(targetEntity: TenantMember::class, mappedBy: 'tenant')]
    private Collection $member;

    public function __construct()
    {
        $this->member = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function setSubdomain(?string $subdomain): static
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDatabaseName(): ?string
    {
        return $this->database_name;
    }

    public function setDatabaseName(?string $database_name): static
    {
        $this->database_name = $database_name;

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

    public function getContactEmail(): ?string
    {
        return $this->contact_email;
    }

    public function setContactEmail(?string $contact_email): static
    {
        $this->contact_email = $contact_email;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contact_phone;
    }

    public function setContactPhone(?string $contact_phone): static
    {
        $this->contact_phone = $contact_phone;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contact_name;
    }

    public function setContactName(?string $contact_name): static
    {
        $this->contact_name = $contact_name;

        return $this;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getMaxUsers(): ?int
    {
        return $this->max_users;
    }

    public function setMaxUsers(?int $max_users): static
    {
        $this->max_users = $max_users;

        return $this;
    }

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(?array $features): static
    {
        $this->features = $features;

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

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTrialEndsAt(): ?\DateTimeImmutable
    {
        return $this->trial_ends_at;
    }

    public function setTrialEndsAt(?\DateTimeImmutable $trial_ends_at): static
    {
        $this->trial_ends_at = $trial_ends_at;

        return $this;
    }

    public function getSubscriptionEndsAt(): ?\DateTimeImmutable
    {
        return $this->subscription_ends_at;
    }

    public function setSubscriptionEndsAt(?\DateTimeImmutable $subscription_ends_at): static
    {
        $this->subscription_ends_at = $subscription_ends_at;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(?string $logo_url): static
    {
        $this->logo_url = $logo_url;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return Collection<int, TenantMember>
     */
    public function getMember(): Collection
    {
        return $this->member;
    }

    public function addMember(TenantMember $member): static
    {
        if (!$this->member->contains($member)) {
            $this->member->add($member);
            $member->setTenant($this);
        }

        return $this;
    }

    public function removeMember(TenantMember $member): static
    {
        if ($this->member->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getTenant() === $this) {
                $member->setTenant(null);
            }
        }

        return $this;
    }
}
