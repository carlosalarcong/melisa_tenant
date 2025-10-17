<?php

namespace App\Controller\Mantenedores\Melisawiclinic;

use App\Controller\Mantenedores\Basico\Region\Default\RegionController as DefaultRegionController;
use App\Entity\Region;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mantenedores/basico/region', name: 'mantenedores_region_')]
class RegionController extends DefaultRegionController
{
    protected function getMantenedorConfig(): array
    {
        $config = parent::getMantenedorConfig();
        
        // Personalización específica para Melisawiclinic (Clínica Tecnológica)
        $config['title'] = 'Gestión de Regiones - Wi Clinic Technology';
        $config['description'] = 'Administración avanzada de regiones con integración IoT y telemetría';
        $config['icon'] = 'fas fa-satellite-dish'; // Icono más tecnológico
        
        // Agregar campos específicos de tecnología
        $config['form_fields'][] = [
            'name' => 'cobertura_5g',
            'label' => 'Cobertura 5G',
            'type' => 'checkbox',
            'required' => false,
            'default' => false,
            'help' => 'Indica si la región tiene cobertura 5G para dispositivos IoT'
        ];
        
        $config['form_fields'][] = [
            'name' => 'zona_horaria',
            'label' => 'Zona Horaria',
            'type' => 'select',
            'required' => false,
            'options' => [
                'America/Santiago' => 'Santiago (UTC-3)',
                'America/New_York' => 'Nueva York (UTC-5)',
                'Europe/Madrid' => 'Madrid (UTC+1)',
                'Asia/Tokyo' => 'Tokio (UTC+9)'
            ],
            'help' => 'Zona horaria para sincronización de datos IoT'
        ];
        
        $config['form_fields'][] = [
            'name' => 'centro_datos',
            'label' => 'Centro de Datos',
            'type' => 'text',
            'required' => false,
            'maxlength' => 100,
            'placeholder' => 'URL o código del centro de datos regional'
        ];
        
        // Agregar columnas específicas
        $config['columns'][] = [
            'key' => 'cobertura_5g',
            'label' => '5G',
            'sortable' => true,
            'searchable' => false,
            'width' => '8%',
            'type' => 'boolean'
        ];
        
        $config['columns'][] = [
            'key' => 'zona_horaria',
            'label' => 'Zona Horaria',
            'sortable' => true,
            'searchable' => true,
            'width' => '12%'
        ];
        
        return $config;
    }

    protected function createEntity(): object
    {
        return new Region();
    }

    protected function getTemplateName(): string
    {
        return 'mantenedores/region/index.html.twig';
    }

    protected function mapRequestToEntity(Request $request, object $entity): void
    {
        parent::mapRequestToEntity($request, $entity);
        
        /** @var Region $entity */
        // Lógica específica para Wi Clinic (tecnológica)
        $cobertura5g = $request->request->get('cobertura_5g', false);
        $zonaHoraria = $request->request->get('zona_horaria', 'America/Santiago');
        $centroDatos = $request->request->get('centro_datos', '');
        
        // Agregar metadatos tecnológicos a la descripción o usar campos adicionales
        $metadatos = [
            '5G' => $cobertura5g ? 'Sí' : 'No',
            'TZ' => $zonaHoraria,
            'DC' => $centroDatos
        ];
        
        $descripcionTecnologica = $entity->getDescripcion() . ' | Tech: ' . json_encode($metadatos);
        $entity->setDescripcion($descripcionTecnologica);
    }

    protected function entityToArray(object $entity): array
    {
        $data = parent::entityToArray($entity);
        
        /** @var Region $entity */
        // Agregar datos específicos de la clínica tecnológica
        $data['cobertura_5g'] = $this->tieneCobertura5G($entity);
        $data['zona_horaria'] = $this->getZonaHoraria($entity);
        $data['centro_datos'] = $this->getCentroDatos($entity);
        $data['dispositivos_iot'] = $this->getDispositivosIoT($entity);
        $data['latencia_promedio'] = $this->getLatenciaPromedio($entity);
        
        return $data;
    }

    /**
     * Endpoint específico para obtener métricas IoT por región
     */
    #[Route('/metricas-iot', name: 'metricas_iot', methods: ['GET'])]
    public function metricasIoT(): JsonResponse
    {
        try {
            $repository = $this->getRepository();
            $regiones = $repository->findActive();
            
            $metricas = [];
            foreach ($regiones as $region) {
                $metricas[] = [
                    'region_id' => $region->getId(),
                    'region_nombre' => $region->getNombre(),
                    'dispositivos_activos' => $this->getDispositivosActivos($region),
                    'datos_transmitidos_mb' => $this->getDatosTransmitidos($region),
                    'latencia_ms' => $this->getLatenciaPromedio($region),
                    'uptime_porcentaje' => $this->getUptime($region),
                    'alertas_activas' => $this->getAlertasActivas($region)
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $metricas,
                'timestamp' => new \DateTime(),
                'total_regiones' => count($metricas)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al obtener métricas IoT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para configurar dispositivos IoT en una región
     */
    #[Route('/{id}/configurar-iot', name: 'configurar_iot', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function configurarIoT(Request $request, int $id): JsonResponse
    {
        try {
            $region = $this->getRepository()->find($id);
            
            if (!$region) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Región no encontrada'
                ], 404);
            }

            $configuracion = [
                'sensor_tipos' => $request->request->all('sensor_tipos'),
                'frecuencia_datos' => $request->request->get('frecuencia_datos', 60), // segundos
                'protocolo' => $request->request->get('protocolo', 'MQTT'),
                'encriptacion' => $request->request->get('encriptacion', 'TLS1.3'),
                'backup_centro' => $request->request->get('backup_centro', '')
            ];

            // Aquí iría la lógica real de configuración IoT
            $resultado = $this->aplicarConfiguracionIoT($region, $configuracion);

            return new JsonResponse([
                'success' => true,
                'message' => 'Configuración IoT aplicada exitosamente',
                'configuracion' => $configuracion,
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al configurar IoT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard en tiempo real de todas las regiones
     */
    #[Route('/dashboard-tiempo-real', name: 'dashboard_tiempo_real', methods: ['GET'])]
    public function dashboardTiempoReal(): JsonResponse
    {
        try {
            $resumen = [
                'total_regiones' => $this->getRepository()->count(['activo' => true]),
                'regiones_5g' => $this->contarRegiones5G(),
                'dispositivos_total' => $this->contarDispositivosTotal(),
                'datos_hoy_gb' => $this->getDatosTransmitidosHoy(),
                'alertas_criticas' => $this->getAlertasCriticas(),
                'uptime_promedio' => $this->getUptimePromedio(),
                'regiones_por_zona' => $this->getRegionesAgrupadasPorZona()
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $resumen,
                'timestamp' => new \DateTime(),
                'next_update' => (new \DateTime())->add(new \DateInterval('PT30S')) // 30 segundos
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error en dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos privados para funcionalidades específicas de IoT

    private function tieneCobertura5G(Region $region): bool
    {
        // Lógica para determinar cobertura 5G basada en metadatos
        $descripcion = $region->getDescripcion();
        return strpos($descripcion, '"5G":"Sí"') !== false;
    }

    private function getZonaHoraria(Region $region): string
    {
        // Extraer zona horaria de metadatos
        $descripcion = $region->getDescripcion();
        if (preg_match('/"TZ":"([^"]+)"/', $descripcion, $matches)) {
            return $matches[1];
        }
        return 'America/Santiago';
    }

    private function getCentroDatos(Region $region): string
    {
        // Extraer centro de datos de metadatos
        $descripcion = $region->getDescripcion();
        if (preg_match('/"DC":"([^"]+)"/', $descripcion, $matches)) {
            return $matches[1];
        }
        return 'N/A';
    }

    private function getDispositivosIoT(Region $region): int
    {
        // Simulación - en producción vendría de base de datos IoT
        return rand(50, 500);
    }

    private function getLatenciaPromedio(Region $region): float
    {
        // Simulación de latencia en ms
        return $this->tieneCobertura5G($region) ? rand(1, 10) : rand(20, 100);
    }

    private function getDispositivosActivos(Region $region): int
    {
        return rand(45, 480); // Simulación
    }

    private function getDatosTransmitidos(Region $region): float
    {
        return round(rand(100, 5000) / 100, 2); // MB
    }

    private function getUptime(Region $region): float
    {
        return round(rand(95, 100) + rand(0, 99) / 100, 2); // Porcentaje
    }

    private function getAlertasActivas(Region $region): int
    {
        return rand(0, 5);
    }

    private function aplicarConfiguracionIoT(Region $region, array $configuracion): array
    {
        // Simulación de aplicación de configuración
        return [
            'configurado' => true,
            'timestamp' => new \DateTime(),
            'dispositivos_afectados' => rand(10, 100),
            'tiempo_aplicacion' => rand(5, 30) . ' segundos'
        ];
    }

    private function contarRegiones5G(): int
    {
        return rand(3, 8); // Simulación
    }

    private function contarDispositivosTotal(): int
    {
        return rand(1000, 5000); // Simulación
    }

    private function getDatosTransmitidosHoy(): float
    {
        return round(rand(500, 2000) / 100, 2); // GB
    }

    private function getAlertasCriticas(): int
    {
        return rand(0, 10);
    }

    private function getUptimePromedio(): float
    {
        return round(rand(98, 100) + rand(0, 99) / 100, 2);
    }

    private function getRegionesAgrupadasPorZona(): array
    {
        return [
            'America/Santiago' => rand(2, 5),
            'America/New_York' => rand(1, 3),
            'Europe/Madrid' => rand(1, 2),
            'Asia/Tokyo' => rand(0, 2)
        ];
    }
}