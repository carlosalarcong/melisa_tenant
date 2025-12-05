<?php
namespace App\Entity\Tenant;

use App\Repository\GenderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenderRepository::class)]
class Gender
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isPerson = null;

    #[ORM\Column(length: 25)]
    private ?string $icon = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $genderCodeHl7 = null;

    #[ORM\Column(options: ["default" => true])]
    private ?bool $isActive = true;

    public function __construct()
    {
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

    public function isPerson(): ?bool
    {
        return $this->isPerson;
    }

    public function setIsPerson(bool $isPerson): static
    {
        $this->isPerson = $isPerson;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getGenderCodeHl7(): ?string
    {
        return $this->genderCodeHl7;
    }

    public function setGenderCodeHl7(?string $genderCodeHl7): static
    {
        $this->genderCodeHl7 = $genderCodeHl7;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

}