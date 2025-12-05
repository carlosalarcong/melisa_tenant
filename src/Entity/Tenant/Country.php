<?php

namespace App\Entity\Tenant;

//use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: '`country`')]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $nationalityName = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $countryCodeHl7 = null;

    #[ORM\Column(options: ["default" => true])]
    private ?bool $isActive = true;

    /**
     * @var Collection<int, Region>
     */
    #[ORM\OneToMany(targetEntity: Region::class, mappedBy: 'country')]
    private Collection $regions;

    /**
     * @var Collection<int, Person>
     */
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'nacionality')]
    private Collection $people;

    /**
     * @var Collection<int, PersonAddress>
     */
    #[ORM\OneToMany(targetEntity: PersonAddress::class, mappedBy: 'country')]
    private Collection $personAddresses;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->personAddresses = new ArrayCollection();
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

    public function getNationalityName(): ?string
    {
        return $this->nationalityName;
    }

    public function setNationalityName(string $nationalityName): static
    {
        $this->nationalityName = $nationalityName;

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

    public function getCountryCodeHl7(): ?string
    {
        return $this->countryCodeHl7;
    }

    public function setCountryCodeHl7(?string $countryCodeHl7): static
    {
        $this->countryCodeHl7 = $countryCodeHl7;

        return $this;
    }

    /**
     * @return Collection<int, Region>
     */
    public function getRegions(): Collection
    {
        return $this->regions;
    }

    public function addRegion(Region $region): static
    {
        if (!$this->regions->contains($region)) {
            $this->regions->add($region);
            $region->setCountry($this);
        }

        return $this;
    }

    public function removeRegion(Region $region): static
    {
        if ($this->regions->removeElement($region)) {
            // set the owning side to null (unless already changed)
            if ($region->getCountry() === $this) {
                $region->setCountry(null);
            }
        }

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
            $person->setNacionality($this);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getNacionality() === $this) {
                $person->setNacionality(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PersonAddress>
     */
    public function getPersonAddresses(): Collection
    {
        return $this->personAddresses;
    }

    public function addPersonAddress(PersonAddress $personAddress): static
    {
        if (!$this->personAddresses->contains($personAddress)) {
            $this->personAddresses->add($personAddress);
            $personAddress->setCountry($this);
        }

        return $this;
    }

    public function removePersonAddress(PersonAddress $personAddress): static
    {
        if ($this->personAddresses->removeElement($personAddress)) {
            // set the owning side to null (unless already changed)
            if ($personAddress->getCountry() === $this) {
                $personAddress->setCountry(null);
            }
        }

        return $this;
    }

}
