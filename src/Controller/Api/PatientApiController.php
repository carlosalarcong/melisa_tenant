<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TenantContext;

#[Route('/api/patients', name: 'api_patients_')]
class PatientApiController extends AbstractController
{
    public function __construct(
        private TenantContext $tenantContext
    ) {}

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getPatient(int $id): JsonResponse
    {
        // Simular datos de la base de datos del tenant actual
        $tenant = $this->tenantContext->getCurrentTenant();
        
        if (!$tenant) {
            return new JsonResponse(['error' => 'Tenant no encontrado'], 404);
        }

        // En un caso real, aquí harías una consulta a la BD específica del tenant
        // $patientRepository = $this->getPatientRepository($tenant['database_name']);
        // $patient = $patientRepository->find($id);
        
        // Por ahora, simulamos datos
        $patientsData = [
            12345 => [
                'id' => 12345,
                'name' => 'Juan Pérez González',
                'age' => 45,
                'status' => 'Activo',
                'rut' => '12.345.678-9',
                'phone' => '+56 9 8765 4321',
                'address' => 'Av. Las Condes 123, Santiago',
                'bloodType' => 'O+',
                'allergies' => ['Penicilina', 'Mariscos'],
                'lastVisit' => '2024-10-10',
                'nextAppointment' => '2024-10-20 14:30',
                'doctor' => 'Dr. María González',
                'tenant' => $tenant['name']
            ],
            67890 => [
                'id' => 67890,
                'name' => 'Ana María Rodríguez',
                'age' => 32,
                'status' => 'En tratamiento',
                'rut' => '18.765.432-1',
                'phone' => '+56 9 1234 5678',
                'address' => 'Calle Falsa 456, Valparaíso',
                'bloodType' => 'A-',
                'allergies' => [],
                'lastVisit' => '2024-10-08',
                'nextAppointment' => '2024-10-15 10:00',
                'doctor' => 'Dr. Carlos Méndez',
                'tenant' => $tenant['name']
            ]
        ];

        if (!isset($patientsData[$id])) {
            return new JsonResponse(['error' => 'Paciente no encontrado'], 404);
        }

        return new JsonResponse([
            'success' => true,
            'patient' => $patientsData[$id],
            'tenant_info' => [
                'name' => $tenant['name'],
                'database' => $tenant['database_name']
            ]
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function searchPatients(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $tenant = $this->tenantContext->getCurrentTenant();
        
        if (!$tenant) {
            return new JsonResponse(['error' => 'Tenant no encontrado'], 404);
        }

        // Simular búsqueda
        $allPatients = [
            ['id' => 12345, 'name' => 'Juan Pérez González', 'rut' => '12.345.678-9'],
            ['id' => 67890, 'name' => 'Ana María Rodríguez', 'rut' => '18.765.432-1'],
            ['id' => 11111, 'name' => 'Carlos Méndez Silva', 'rut' => '11.111.111-1'],
            ['id' => 22222, 'name' => 'María González López', 'rut' => '22.222.222-2'],
        ];

        $filtered = array_filter($allPatients, function($patient) use ($query) {
            return empty($query) || 
                   stripos($patient['name'], $query) !== false || 
                   stripos($patient['rut'], $query) !== false;
        });

        return new JsonResponse([
            'success' => true,
            'patients' => array_values($filtered),
            'total' => count($filtered),
            'query' => $query,
            'tenant' => $tenant['name']
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function updatePatient(int $id, Request $request): JsonResponse
    {
        $tenant = $this->tenantContext->getCurrentTenant();
        
        if (!$tenant) {
            return new JsonResponse(['error' => 'Tenant no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        // Aquí normalmente actualizarías la BD
        // $patientRepository->update($id, $data);
        
        return new JsonResponse([
            'success' => true,
            'message' => 'Paciente actualizado correctamente',
            'patient_id' => $id,
            'updated_fields' => $data,
            'tenant' => $tenant['name']
        ]);
    }
}