<?php

namespace App\Controller\Mantenedores\Melisalacolina;

use App\Controller\Mantenedores\Basico\Sexo\Default\SexoController as DefaultSexoController;
use App\Entity\Sexo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mantenedores/basico/sexo', name: 'mantenedores_sexo_')]
class SexoController extends DefaultSexoController
{
    protected function getMantenedorConfig(): array
    {
        $config = parent::getMantenedorConfig();
        
        // Personalización específica para Melisalacolina
        $config['title'] = 'Gestión de Sexos - Clínica La Colina';
        $config['description'] = 'Administración especializada de tipos de sexo para clínica ambulatoria';
        $config['icon'] = 'fas fa-leaf'; // Icono más acorde con clínica naturista
        
        // Agregar campos específicos de la clínica
        $config['form_fields'][] = [
            'name' => 'preferencia_tratamiento',
            'label' => 'Preferencia de Tratamiento',
            'type' => 'select',
            'required' => false,
            'options' => [
                'general' => 'Tratamiento General',
                'especializado' => 'Tratamiento Especializado',
                'natural' => 'Medicina Natural'
            ],
            'help' => 'Tipo de tratamiento preferido según el sexo'
        ];
        
        // Agregar columna específica
        $config['columns'][] = [
            'key' => 'preferencia_tratamiento',
            'label' => 'Pref. Tratamiento',
            'sortable' => true,
            'searchable' => true,
            'width' => '15%'
        ];
        
        return $config;
    }

    protected function createEntity(): object
    {
        return new Sexo();
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/sexo/index.html.twig';
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        parent::mapRequestToEntity($request, $entity);
        
        /** @var Sexo $entity */
        // Lógica específica para Melisalacolina
        $preferencia = $request->request->get('preferencia_tratamiento', 'general');
        
        // Podríamos agregar campos específicos o lógica personalizada aquí
        // Por ejemplo, validaciones específicas para la clínica
        if ($entity->getCodigo() === 'F' && $preferencia === 'natural') {
            // Lógica específica para mujeres con preferencia de medicina natural
            $entity->setDescripcion($entity->getDescripcion() . ' - Medicina Natural');
        }
    }

    protected function entityToArray(object $entity): array
    {
        $data = parent::entityToArray($entity);
        
        /** @var Sexo $entity */
        // Agregar datos específicos de la clínica
        $data['preferencia_tratamiento'] = $this->getPreferenciaTratamiento($entity);
        $data['clinica_especialidad'] = $this->getEspecialidadClinica($entity);
        
        return $data;
    }

    /**
     * Endpoint específico para obtener estadísticas de sexo por tratamiento
     */
    #[Route('/estadisticas-tratamiento', name: 'estadisticas_tratamiento', methods: ['GET'])]
    public function estadisticasTratamiento(): JsonResponse
    {
        try {
            $repository = $this->getRepository();
            
            // Consultas específicas para la clínica
            $stats = [
                'total_pacientes_masculinos' => $repository->count(['codigo' => 'M', 'activo' => true]),
                'total_pacientes_femeninos' => $repository->count(['codigo' => 'F', 'activo' => true]),
                'preferencias_tratamiento' => $this->getPreferenciasEstadisticas(),
                'distribución_por_mes' => $this->getDistribucionMensual()
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para validar códigos específicos de la clínica
     */
    #[Route('/validar-codigo-clinica', name: 'validar_codigo_clinica', methods: ['POST'])]
    public function validarCodigoClinica(Request $request): JsonResponse
    {
        $codigo = $request->request->get('codigo');
        $entidadId = $request->request->get('entity_id');

        // Validaciones específicas para Melisalacolina
        $validaciones = [
            'codigo_valido' => in_array($codigo, ['M', 'F']),
            'disponible' => !$this->getRepository()->existsByCodigo($codigo, $entidadId),
            'clinica_compatible' => $this->esCodigoCompatibleClinica($codigo)
        ];

        $esValido = $validaciones['codigo_valido'] && 
                   $validaciones['disponible'] && 
                   $validaciones['clinica_compatible'];

        return new JsonResponse([
            'success' => true,
            'valido' => $esValido,
            'validaciones' => $validaciones,
            'mensaje' => $esValido ? 'Código válido' : 'Código no válido para esta clínica'
        ]);
    }

    private function getPreferenciaTratamiento(Sexo $sexo): string
    {
        // Lógica para determinar preferencia de tratamiento
        // Podría basarse en configuración específica de la clínica
        return match($sexo->getCodigo()) {
            'F' => 'natural',
            'M' => 'general',
            default => 'general'
        };
    }

    private function getEspecialidadClinica(Sexo $sexo): string
    {
        // Especialidades específicas de La Colina según el sexo
        return match($sexo->getCodigo()) {
            'F' => 'Ginecología Natural, Medicina Integrativa',
            'M' => 'Medicina General, Urología Natural',
            default => 'Medicina General'
        };
    }

    private function getPreferenciasEstadisticas(): array
    {
        // Simulación de estadísticas - en producción vendría de BD
        return [
            'natural' => 65,
            'especializado' => 25,
            'general' => 10
        ];
    }

    private function getDistribucionMensual(): array
    {
        // Simulación de distribución mensual
        return [
            'enero' => ['M' => 15, 'F' => 25],
            'febrero' => ['M' => 18, 'F' => 30],
            'marzo' => ['M' => 20, 'F' => 35],
            // ... más meses
        ];
    }

    private function esCodigoCompatibleClinica(string $codigo): bool
    {
        // Validaciones específicas para Melisalacolina
        // Por ejemplo, solo permitir ciertos códigos
        return in_array($codigo, ['M', 'F']); // La clínica solo maneja binario
    }
}