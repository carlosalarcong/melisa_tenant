<?php

namespace App\Entity\Tenant;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class Member implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = true;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $passwordChangedAt = null;

    /**
     * User type: 0 = Clinical Professional, 1 = Administrative
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $userType = null;

    /**
     * Failed login attempts counter
     */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $loginAttempts = 0;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'id', nullable: true)]
    private ?Person $person = null;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: true)]
    private ?State $state = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: true)]
    private ?Role $role = null;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(name: 'position_id', referencedColumnName: 'id', nullable: true)]
    private ?Position $position = null;

    #[ORM\ManyToOne(targetEntity: ProfessionalType::class)]
    #[ORM\JoinColumn(name: 'professional_type_id', referencedColumnName: 'id', nullable: true)]
    private ?ProfessionalType $professionalType = null;

    /**
     * RCM - Registro Médico (Medical Registration Number)
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rcm = null;

    /**
     * Superintendent Registry Number
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $superintendentRegistry = null;

    /**
     * General observation/comment
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observation = null;

    /**
     * Web observation/comment
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $webObservation = null;

    /**
     * Overbooking quantity allowed
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => 0])]
    private ?int $overbookingQuantity = 0;

    /**
     * Is default emergency professional
     */
    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $isEmergencyProfessional = false;

    /**
     * Is default integration professional
     */
    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $isIntegrationProfessional = false;

    /**
     * @var Collection<int, MemberGroup>
     */
    #[ORM\ManyToMany(targetEntity: MemberGroup::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: 'member_group_membership')]
    private Collection $groups;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPasswordChangedAt(): ?\DateTimeImmutable
    {
        return $this->passwordChangedAt;
    }

    public function setPasswordChangedAt(?\DateTimeImmutable $passwordChangedAt): static
    {
        $this->passwordChangedAt = $passwordChangedAt;
        return $this;
    }

    public function getUserType(): ?int
    {
        return $this->userType;
    }

    public function setUserType(?int $userType): static
    {
        $this->userType = $userType;
        return $this;
    }

    public function getLoginAttempts(): int
    {
        return $this->loginAttempts;
    }

    public function setLoginAttempts(int $loginAttempts): static
    {
        $this->loginAttempts = $loginAttempts;
        return $this;
    }

    public function incrementLoginAttempts(): static
    {
        $this->loginAttempts++;
        return $this;
    }

    public function resetLoginAttempts(): static
    {
        $this->loginAttempts = 0;
        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;
        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getProfessionalType(): ?ProfessionalType
    {
        return $this->professionalType;
    }

    public function setProfessionalType(?ProfessionalType $professionalType): static
    {
        $this->professionalType = $professionalType;
        return $this;
    }

    public function getRcm(): ?string
    {
        return $this->rcm;
    }

    public function setRcm(?string $rcm): static
    {
        $this->rcm = $rcm;
        return $this;
    }

    public function getSuperintendentRegistry(): ?string
    {
        return $this->superintendentRegistry;
    }

    public function setSuperintendentRegistry(?string $superintendentRegistry): static
    {
        $this->superintendentRegistry = $superintendentRegistry;
        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;
        return $this;
    }

    public function getWebObservation(): ?string
    {
        return $this->webObservation;
    }

    public function setWebObservation(?string $webObservation): static
    {
        $this->webObservation = $webObservation;
        return $this;
    }

    public function getOverbookingQuantity(): ?int
    {
        return $this->overbookingQuantity;
    }

    public function setOverbookingQuantity(?int $overbookingQuantity): static
    {
        $this->overbookingQuantity = $overbookingQuantity;
        return $this;
    }

    public function isEmergencyProfessional(): ?bool
    {
        return $this->isEmergencyProfessional;
    }

    public function setIsEmergencyProfessional(?bool $isEmergencyProfessional): static
    {
        $this->isEmergencyProfessional = $isEmergencyProfessional;
        return $this;
    }

    public function isIntegrationProfessional(): ?bool
    {
        return $this->isIntegrationProfessional;
    }

    public function setIsIntegrationProfessional(?bool $isIntegrationProfessional): static
    {
        $this->isIntegrationProfessional = $isIntegrationProfessional;
        return $this;
    }

    /**
     * @return Collection<int, MemberGroup>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(MemberGroup $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(MemberGroup $group): static
    {
        $this->groups->removeElement($group);
        return $this;
    }
}
