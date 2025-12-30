<?php
namespace App\Entity\Tenant;

use App\Repository\GenderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Person>
     */
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'gender')]
    private Collection $people;

    public function __construct()
    {
        $this->people = new ArrayCollection();
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

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->setGender($this);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getGender() === $this) {
                $person->setGender(null);
            }
        }

        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getNombreSexo(): ?string
    {
        return $this->name;
    }

    public function getIdEstado()
    {
        return $this->isActive ? (object)['id' => 1] : (object)['id' => 2];
    }
}
