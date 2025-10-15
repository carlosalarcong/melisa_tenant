<?php

namespace App\State\Melisawiclinic;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Patient;
use App\Service\TenantContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * State Provider específico para Melisa Wi Clinic
 * 
 * Maneja los datos específicos del centro médico tecnológico
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
        $tenant = $request?->headers->get('X-Tenant-Context') ?? 'melisawiclinic';
        
        // Datos específicos de Wi Clinic
        $patientsData = $this->getTechClinicPatients($tenant);
        
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
     * Datos específicos de Melisa Wi Clinic (Centro Tecnológico)
     */
    private function getTechClinicPatients(string $tenant): array
    {
        return [
            [
                'id' => 'WIC001',
                'name' => 'Alejandro Tech - Paciente Telemedicina',
                'cedula' => '11223344-5',
                'email' => 'alejandro.tech@wiclinic.cl',
                'phone' => '+56911223344',
                'address' => 'Av. Tecnología 1000, Providencia',
                'gender' => 'M',
                'birth_date' => '1990-04-15',
                'blood_type' => 'A+',
                'allergies' => [],
                'medications' => ['Multivitamínico'],
                'emergency_contact' => 'Carolina Tech',
                'emergency_phone' => '+56922334455',
                'tenant' => $tenant,
                'created_at' => '2023-08-01 09:00:00',
                'updated_at' => '2024-01-22 10:30:00'
            ],
            [
                'id' => 'WIC002',
                'name' => 'Valentina Digital - Paciente Wearables',
                'cedula' => '22334455-6',
                'email' => 'valentina.digital@wiclinic.cl',
                'phone' => '+56922334455',
                'address' => 'Calle Innovation 2000, Providencia',
                'gender' => 'F',
                'birth_date' => '1987-08-22',
                'blood_type' => 'O+',
                'allergies' => ['Níquel (smartwatch)'],
                'medications' => ['Omega 3 1000mg'],
                'emergency_contact' => 'Sebastián Digital',
                'emergency_phone' => '+56933445566',
                'tenant' => $tenant,
                'created_at' => '2023-08-15 14:20:00',
                'updated_at' => '2024-01-25 16:45:00'
            ],
            [
                'id' => 'WIC003',
                'name' => 'Mateo IoT - Paciente Sensores',
                'cedula' => '33445566-7',
                'email' => 'mateo.iot@wiclinic.cl',
                'phone' => '+56933445566',
                'address' => 'Plaza Smart 3000, Providencia',
                'gender' => 'M',
                'birth_date' => '1985-12-10',
                'blood_type' => 'B+',
                'allergies' => ['Adhesivos médicos'],
                'medications' => ['Metformina 850mg', 'Sensor glucosa continuo'],
                'emergency_contact' => 'Isabella IoT',
                'emergency_phone' => '+56944556677',
                'tenant' => $tenant,
                'created_at' => '2023-09-01 11:15:00',
                'updated_at' => '2024-01-28 13:20:00'
            ],
            [
                'id' => 'WIC004',
                'name' => 'Sofía AI - Paciente IA Diagnóstica',
                'cedula' => '44556677-8',
                'email' => 'sofia.ai@wiclinic.cl',
                'phone' => '+56944556677',
                'address' => 'Av. Machine Learning 4000, Providencia',
                'gender' => 'F',
                'birth_date' => '1993-06-05',
                'blood_type' => 'AB+',
                'allergies' => [],
                'medications' => ['Complejo B'],
                'emergency_contact' => 'Diego AI',
                'emergency_phone' => '+56955667788',
                'tenant' => $tenant,
                'created_at' => '2023-09-20 16:30:00',
                'updated_at' => '2024-01-30 12:10:00'
            ],
            [
                'id' => 'WIC005',
                'name' => 'Leonardo VR - Paciente Realidad Virtual',
                'cedula' => '55667788-9',
                'email' => 'leonardo.vr@wiclinic.cl',
                'phone' => '+56955667788',
                'address' => 'Calle Metaverso 5000, Providencia',
                'gender' => 'M',
                'birth_date' => '1991-01-30',
                'blood_type' => 'O-',
                'allergies' => ['Mareo por movimiento VR'],
                'medications' => ['Dimenhidrinato 50mg PRN'],
                'emergency_contact' => 'Camila VR',
                'emergency_phone' => '+56966778899',
                'tenant' => $tenant,
                'created_at' => '2023-10-05 08:45:00',
                'updated_at' => '2024-02-01 14:25:00'
            ],
            [
                'id' => 'WIC006',
                'name' => 'Emma Blockchain - Paciente Historial Distribuido',
                'cedula' => '66778899-0',
                'email' => 'emma.blockchain@wiclinic.cl',
                'phone' => '+56966778899',
                'address' => 'Plaza Crypto 6000, Providencia',
                'gender' => 'F',
                'birth_date' => '1989-03-18',
                'blood_type' => 'A-',
                'allergies' => [],
                'medications' => ['Magnesio 400mg', 'Hash verificado blockchain'],
                'emergency_contact' => 'Tomás Blockchain',
                'emergency_phone' => '+56977889900',
                'tenant' => $tenant,
                'created_at' => '2023-10-20 13:50:00',
                'updated_at' => '2024-02-05 11:40:00'
            ]
        ];
    }
}