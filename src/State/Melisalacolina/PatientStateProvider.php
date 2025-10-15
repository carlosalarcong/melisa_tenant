<?php

namespace App\State\Melisalacolina;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Patient;
use App\Service\TenantContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * State Provider específico para Clínica La Colina
 * 
 * Maneja los datos específicos de la clínica especializada
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
        
        // Obtener tenant del header
        $tenant = $request?->headers->get('X-Tenant-Context') ?? 'melisalacolina';
        
        // Datos específicos de La Colina
        $patientsData = $this->getClinicPatients($tenant);
        
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
     * Datos específicos de Clínica La Colina
     */
    private function getClinicPatients(string $tenant): array
    {
        return [
            [
                'id' => 'COL001',
                'name' => 'Esperanza Morales - Paciente Cardiología',
                'cedula' => '87654321-0',
                'email' => 'esperanza.morales@lacolina.cl',
                'phone' => '+56987654321',
                'address' => 'Av. La Colina 100, Las Condes',
                'gender' => 'F',
                'birth_date' => '1970-05-20',
                'blood_type' => 'O-',
                'allergies' => ['Betabloqueadores'],
                'medications' => ['Atorvastatina 40mg', 'Metoprolol 50mg'],
                'emergency_contact' => 'Roberto Morales',
                'emergency_phone' => '+56976543210',
                'tenant' => $tenant,
                'created_at' => '2023-03-01 08:45:00',
                'updated_at' => '2024-01-15 10:20:00'
            ],
            [
                'id' => 'COL002',
                'name' => 'Roberto Fernández - Paciente Neurología',
                'cedula' => '76543210-9',
                'email' => 'roberto.fernandez@lacolina.cl',
                'phone' => '+56976543210',
                'address' => 'Calle Colina 200, Las Condes',
                'gender' => 'M',
                'birth_date' => '1982-09-14',
                'blood_type' => 'A+',
                'allergies' => ['Fenitoína'],
                'medications' => ['Levetiracetam 500mg', 'Ácido Valproico 250mg'],
                'emergency_contact' => 'Patricia Fernández',
                'emergency_phone' => '+56965432109',
                'tenant' => $tenant,
                'created_at' => '2023-04-10 14:30:00',
                'updated_at' => '2024-01-08 16:15:00'
            ],
            [
                'id' => 'COL003',
                'name' => 'Cristina López - Paciente Ginecología',
                'cedula' => '65432109-8',
                'email' => 'cristina.lopez@lacolina.cl',
                'phone' => '+56965432109',
                'address' => 'Plaza Colina 300, Las Condes',
                'gender' => 'F',
                'birth_date' => '1988-11-28',
                'blood_type' => 'B-',
                'allergies' => ['Sulfonamidas'],
                'medications' => ['Ácido Fólico 5mg', 'Hierro 65mg'],
                'emergency_contact' => 'Manuel López',
                'emergency_phone' => '+56954321098',
                'tenant' => $tenant,
                'created_at' => '2023-05-15 11:20:00',
                'updated_at' => '2024-01-12 09:45:00'
            ],
            [
                'id' => 'COL004',
                'name' => 'Diana Ruiz - Paciente Dermatología',
                'cedula' => '54321098-7',
                'email' => 'diana.ruiz@lacolina.cl',
                'phone' => '+56954321098',
                'address' => 'Av. Especialistas 400, Las Condes',
                'gender' => 'F',
                'birth_date' => '1995-02-18',
                'blood_type' => 'AB-',
                'allergies' => ['Retinoides'],
                'medications' => ['Isotretinoína 20mg'],
                'emergency_contact' => 'Carlos Ruiz',
                'emergency_phone' => '+56943210987',
                'tenant' => $tenant,
                'created_at' => '2023-06-20 15:10:00',
                'updated_at' => '2024-01-18 14:30:00'
            ],
            [
                'id' => 'COL005',
                'name' => 'Fernando Castillo - Paciente Traumatología',
                'cedula' => '43210987-6',
                'email' => 'fernando.castillo@lacolina.cl',
                'phone' => '+56943210987',
                'address' => 'Calle Deportistas 500, Las Condes',
                'gender' => 'M',
                'birth_date' => '1975-07-12',
                'blood_type' => 'O+',
                'allergies' => ['AINES'],
                'medications' => ['Paracetamol 500mg', 'Tramadol 50mg'],
                'emergency_contact' => 'María Castillo',
                'emergency_phone' => '+56932109876',
                'tenant' => $tenant,
                'created_at' => '2023-07-05 12:30:00',
                'updated_at' => '2024-01-20 11:15:00'
            ]
        ];
    }
}