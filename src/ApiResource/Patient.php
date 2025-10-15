<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\DynamicPatientStateProvider;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * API Resource para Pacientes - Sin entidad Doctrine
 * 
 * Este recurso demuestra cómo API Platform puede trabajar 
 * sin entidades de base de datos usando State Providers
 */
#[ApiResource(
    shortName: 'Patient',
    description: 'Recurso de Pacientes para sistema multi-tenant Melisa',
    
    // Operaciones disponibles
    operations: [
        new Get(
            uriTemplate: '/patients/{id}',
            description: 'Obtiene un paciente específico por ID'
        ),
        new GetCollection(
            uriTemplate: '/patients',
            description: 'Lista todos los pacientes del tenant actual'
        )
    ],
    
    // State Provider personalizado
    provider: DynamicPatientStateProvider::class,
    
    // Configuración de API
    normalizationContext: [
        'groups' => ['patient:read']
    ],
    
    // Paginación
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 50,
    
    // Filtros y búsqueda
    extraProperties: [
        'searchable_fields' => ['name', 'cedula', 'email'],
        'tenant_aware' => true
    ]
)]
class Patient
{
    public function __construct(
        #[Groups(['patient:read'])]
        public ?string $id = null,
        #[Groups(['patient:read'])]
        public ?string $name = null,
        #[Groups(['patient:read'])]
        public ?string $cedula = null,
        #[Groups(['patient:read'])]
        public ?string $email = null,
        #[Groups(['patient:read'])]
        public ?string $phone = null,
        #[Groups(['patient:read'])]
        public ?string $address = null,
        #[Groups(['patient:read'])]
        public ?string $gender = null,
        #[Groups(['patient:read'])]
        public ?\DateTimeInterface $birthDate = null,
        #[Groups(['patient:read'])]
        public ?string $bloodType = null,
        #[Groups(['patient:read'])]
        public ?array $allergies = [],
        #[Groups(['patient:read'])]
        public ?array $medications = [],
        #[Groups(['patient:read'])]
        public ?string $emergencyContact = null,
        #[Groups(['patient:read'])]
        public ?string $emergencyPhone = null,
        #[Groups(['patient:read'])]
        public ?string $tenant = null,
        #[Groups(['patient:read'])]
        public ?\DateTimeInterface $createdAt = null,
        #[Groups(['patient:read'])]
        public ?\DateTimeInterface $updatedAt = null
    ) {}

    /**
     * Convierte array de datos en instancia Patient
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            cedula: $data['cedula'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            gender: $data['gender'] ?? null,
            birthDate: isset($data['birth_date']) ? new \DateTime($data['birth_date']) : null,
            bloodType: $data['blood_type'] ?? null,
            allergies: $data['allergies'] ?? [],
            medications: $data['medications'] ?? [],
            emergencyContact: $data['emergency_contact'] ?? null,
            emergencyPhone: $data['emergency_phone'] ?? null,
            tenant: $data['tenant'] ?? null,
            createdAt: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null
        );
    }

    /**
     * Convierte instancia Patient en array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cedula' => $this->cedula,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'gender' => $this->gender,
            'birth_date' => $this->birthDate?->format('Y-m-d'),
            'blood_type' => $this->bloodType,
            'allergies' => $this->allergies,
            'medications' => $this->medications,
            'emergency_contact' => $this->emergencyContact,
            'emergency_phone' => $this->emergencyPhone,
            'tenant' => $this->tenant,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Obtiene información médica resumida
     */
    public function getMedicalSummary(): array
    {
        return [
            'blood_type' => $this->bloodType,
            'allergies_count' => count($this->allergies),
            'medications_count' => count($this->medications),
            'has_emergency_contact' => !empty($this->emergencyContact)
        ];
    }
}