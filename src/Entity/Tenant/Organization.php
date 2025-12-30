<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organization - Tenant/Client information
 * 
 * Represents a healthcare organization/hospital that uses the system.
 * This is the main tenant entity that isolates data between different clients.
 * Following HL7 standard naming convention.
 */
#[ORM\Entity]
#[ORM\Table(name: 'organization')]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tax_id', type: 'integer')]
    private int $taxId;

    #[ORM\Column(name: 'tax_id_verification_digit', type: 'string', length: 1)]
    private string $taxIdVerificationDigit;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'address', type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: 'website', type: 'string', length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(name: 'landline_phone', type: 'string', length: 45, nullable: true)]
    private ?string $landlinePhone = null;

    #[ORM\Column(name: 'mobile_phone', type: 'string', length: 45, nullable: true)]
    private ?string $mobilePhone = null;

    #[ORM\Column(name: 'logo_path', type: 'string', length: 255, nullable: true)]
    private ?string $logoPath = null;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'contact_person', type: 'string', length: 255, nullable: true)]
    private ?string $contactPerson = null;

    #[ORM\Column(name: 'license_quantity', type: 'integer')]
    private int $licenseQuantity;

    // #[ORM\Column(name: 'code', type: 'string', length: 50, nullable: true)]
    // private ?string $code = null;

    // #[ORM\Column(name: 'aws_integration', type: 'boolean', options: ['default' => true])]
    // private bool $awsIntegration = true;

    // #[ORM\Column(name: 'teleconsultation', type: 'boolean', options: ['default' => false])]
    // private bool $teleconsultation = false;

    // TODO: Crear entidades relacionadas antes de descomentar
    // #[ORM\ManyToOne(targetEntity: HospitalType::class)]
    // #[ORM\JoinColumn(name: 'ID_TIPO_HOSPITAL', referencedColumnName: 'ID')]
    // private ?HospitalType $hospitalType = null;

    // #[ORM\ManyToOne(targetEntity: HealthService::class)]
    // #[ORM\JoinColumn(name: 'ID_SERVICIO_SALUD', referencedColumnName: 'ID')]
    // private ?HealthService $healthService = null;

    // #[ORM\ManyToOne(targetEntity: FonasaLevel::class)]
    // #[ORM\JoinColumn(name: 'ID_NIVEL_FONASA', referencedColumnName: 'ID')]
    // private ?FonasaLevel $fonasaLevel = null;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id')]
    private ?State $state = null;

    // #[ORM\ManyToOne(targetEntity: Tariff::class)]
    // #[ORM\JoinColumn(name: 'ID_ARANCEL', referencedColumnName: 'ID')]
    // private ?Tariff $tariff = null;

    // #[ORM\ManyToOne(targetEntity: Comuna::class)]
    // #[ORM\JoinColumn(name: 'ID_COMUNA', referencedColumnName: 'ID')]
    // private ?Comuna $commune = null;

    // #[ORM\ManyToOne(targetEntity: ForeignIdType::class)]
    // #[ORM\JoinColumn(name: 'ID_TIPO_IDENTIFICACION_DEFAULT', referencedColumnName: 'ID')]
    // private ?ForeignIdType $defaultForeignIdType = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxId(): int
    {
        return $this->taxId;
    }

    public function setTaxId(int $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }

    public function getTaxIdVerificationDigit(): string
    {
        return $this->taxIdVerificationDigit;
    }

    public function setTaxIdVerificationDigit(string $taxIdVerificationDigit): self
    {
        $this->taxIdVerificationDigit = $taxIdVerificationDigit;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getLandlinePhone(): ?string
    {
        return $this->landlinePhone;
    }

    public function setLandlinePhone(?string $landlinePhone): self
    {
        $this->landlinePhone = $landlinePhone;
        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;
        return $this;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(?string $logoPath): self
    {
        $this->logoPath = $logoPath;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function setContactPerson(?string $contactPerson): self
    {
        $this->contactPerson = $contactPerson;
        return $this;
    }

    public function getLicenseQuantity(): int
    {
        return $this->licenseQuantity;
    }

    public function setLicenseQuantity(int $licenseQuantity): self
    {
        $this->licenseQuantity = $licenseQuantity;
        return $this;
    }

    // public function getCode(): ?string
    // {
    //     return $this->code;
    // }

    // public function setCode(?string $code): self
    // {
    //     $this->code = $code;
    //     return $this;
    // }

    // public function getAwsIntegration(): bool
    // {
    //     return $this->awsIntegration;
    // }

    // public function setAwsIntegration(bool $awsIntegration): self
    // {
    //     $this->awsIntegration = $awsIntegration;
    //     return $this;
    // }

    // public function getTeleconsultation(): bool
    // {
    //     return $this->teleconsultation;
    // }

    // public function setTeleconsultation(bool $teleconsultation): self
    // {
    //     $this->teleconsultation = $teleconsultation;
    //     return $this;
    // }

    // TODO: Descomentar cuando se creen las entidades
    // public function getHospitalType(): ?HospitalType
    // {
    //     return $this->hospitalType;
    // }

    // public function setHospitalType(?HospitalType $hospitalType): self
    // {
    //     $this->hospitalType = $hospitalType;
    //     return $this;
    // }

    // public function getHealthService(): ?HealthService
    // {
    //     return $this->healthService;
    // }

    // public function setHealthService(?HealthService $healthService): self
    // {
    //     $this->healthService = $healthService;
    //     return $this;
    // }

    // public function getFonasaLevel(): ?FonasaLevel
    // {
    //     return $this->fonasaLevel;
    // }

    // public function setFonasaLevel(?FonasaLevel $fonasaLevel): self
    // {
    //     $this->fonasaLevel = $fonasaLevel;
    //     return $this;
    // }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;
        return $this;
    }

    // public function getTariff(): ?Tariff
    // {
    //     return $this->tariff;
    // }

    // public function setTariff(?Tariff $tariff): self
    // {
    //     $this->tariff = $tariff;
    //     return $this;
    // }

    // public function getCommune(): ?Comuna
    // {
    //     return $this->commune;
    // }

    // public function setCommune(?Comuna $commune): self
    // {
    //     $this->commune = $commune;
    //     return $this;
    // }

    // public function getDefaultForeignIdType(): ?ForeignIdType
    // {
    //     return $this->defaultForeignIdType;
    // }

    // public function setDefaultForeignIdType(?ForeignIdType $defaultForeignIdType): self
    // {
    //     $this->defaultForeignIdType = $defaultForeignIdType;
    //     return $this;
    // }

    /**
     * Get full tax ID with verification digit (e.g., "12345678-9")
     */
    public function getFullTaxId(): string
    {
        return $this->taxId . '-' . $this->taxIdVerificationDigit;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->name ?? $this->getFullTaxId();
    }
}
