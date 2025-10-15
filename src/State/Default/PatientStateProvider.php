<?php

namespace App\State\Default;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Patient;
use App\Service\TenantContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * State Provider por defecto para Hospital Melisa
 * 
 * Maneja los datos estándar del hospital principal
 */
class PatientStateProvider implements ProviderInterface
{
    public function __construct(
        private TenantContext $tenantContext,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        
        // Obtener tenant del header o usar default
        $tenant = $request?->headers->get('X-Tenant-Context') ?? 'melisahospital';
        
        // Datos específicos del Hospital Melisa
        $patientsData = $this->getHospitalPatients($tenant);
        
        // Si es una operación de item individual (GET /api/patients/{id})
        if (isset($uriVariables['id'])) {
            $patientId = $uriVariables['id'];
            
            foreach ($patientsData as $data) {
                if ($data['id'] === $patientId) {
                    return Patient::fromArray($data);
                }
            }
            
            return null; // Paciente no encontrado
        }
        
        // Si es una colección (GET /api/patients)
        $patients = [];
        foreach ($patientsData as $data) {
            $patients[] = Patient::fromArray($data);
        }
        
        return $patients;
    }

    /**
     * Datos específicos del Hospital Melisa (tenant por defecto)
     */
    private function getHospitalPatients(string $tenant): array
    {
        return [
            [
                'id' => 'HSP001',
                'name' => 'Dr. María González - Paciente Hospitalario',
                'cedula' => '12345678-9',
                'email' => 'maria.gonzalez@melisahospital.cl',
                'phone' => '+56912345678',
                'address' => 'Av. Hospital 123, Santiago',
                'gender' => 'F',
                'birth_date' => '1985-03-15',
                'blood_type' => 'O+',
                'allergies' => ['Penicilina', 'Mariscos'],
                'medications' => ['Metformina 500mg', 'Losartán 50mg', 'Aspirina 100mg'],
                'emergency_contact' => 'Juan González',
                'emergency_phone' => '+56987654321',
                'tenant' => $tenant,
                'created_at' => '2023-01-15 10:30:00',
                'updated_at' => '2024-01-10 14:20:00'
            ],
            [
                'id' => 'HSP002',
                'name' => 'Carlos Rodríguez - Paciente UCI',
                'cedula' => '23456789-0',
                'email' => 'carlos.rodriguez@melisahospital.cl',
                'phone' => '+56923456789',
                'address' => 'Calle Hospital 456, Santiago',
                'gender' => 'M',
                'birth_date' => '1978-11-22',
                'blood_type' => 'A-',
                'allergies' => ['Morfina'],
                'medications' => ['Atorvastatina 20mg', 'Enalapril 10mg'],
                'emergency_contact' => 'Ana Rodríguez',
                'emergency_phone' => '+56976543210',
                'tenant' => $tenant,
                'created_at' => '2023-02-20 09:15:00',
                'updated_at' => '2024-01-05 16:45:00'
            ],
            [
                'id' => 'HSP003',
                'name' => 'Ana Silva - Paciente Cirugía',
                'cedula' => '34567890-1',
                'email' => 'ana.silva@melisahospital.cl',
                'phone' => '+56934567890',
                'address' => 'Plaza Hospital 789, Santiago',
                'gender' => 'F',
                'birth_date' => '1992-07-08',
                'blood_type' => 'B+',
                'allergies' => ['Ibuprofeno', 'Latex'],
                'medications' => ['Tramadol 50mg'],
                'emergency_contact' => 'Luis Silva',
                'emergency_phone' => '+56965432109',
                'tenant' => $tenant,
                'created_at' => '2023-03-10 11:00:00',
                'updated_at' => '2024-01-08 12:30:00'
            ],
            [
                'id' => 'HSP004',
                'name' => 'Pedro Martínez - Paciente Oncología',
                'cedula' => '45678901-2',
                'email' => 'pedro.martinez@melisahospital.cl',
                'phone' => '+56945678901',
                'address' => 'Av. Especialidades 321, Santiago',
                'gender' => 'M',
                'birth_date' => '1965-12-03',
                'blood_type' => 'AB+',
                'allergies' => ['Contraste yodado'],
                'medications' => ['Cisplatino', 'Ondansetrón 8mg'],
                'emergency_contact' => 'Carmen Martínez',
                'emergency_phone' => '+56954321098',
                'tenant' => $tenant,
                'created_at' => '2023-04-25 14:45:00',
                'updated_at' => '2024-01-12 09:30:00'
            ]
        ];
    }
}